<?php

namespace App\Http\Controllers;

use App\Models\Parking;
use App\Models\Plaza;
use App\Models\RegistroAcceso;
use App\Models\Tarifa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class RegistroAccesoController extends Controller
{
    public function index()
    {
        try {
            $parkings = Parking::with(['plantas.plazas', 'tarifas'])->get();
            
            foreach ($parkings as $parking) {
                // Calcular plazas disponibles
                $plazasLibres = 0;
                $plazasTotales = 0;
                
                foreach ($parking->plantas as $planta) {
                    foreach ($planta->plazas as $plaza) {
                        $plazasTotales++;
                        if ($plaza->estadoPlaza === 'libre') {
                            $plazasLibres++;
                        }
                    }
                }
                
                // Calcular tarifa mínima
                $tarifaMinima = $parking->tarifas->min('precio') ?? 0;
                
                // Añadir propiedades calculadas
                $parking->plazasLibres = $plazasLibres;
                $parking->plazasTotales = $plazasTotales;
                $parking->tarifaMinima = $tarifaMinima;
            }

            return view('registro-accesos.index', compact('parkings'));
        } catch (\Exception $e) {
            \Log::error('Error en index de registro-accesos: ' . $e->getMessage());
            return back()->with('error', 'Error al cargar los parkings');
        }
    }

    public function create($parkingId)
    {
        $parking = Parking::with(['plantas.plazas', 'tarifas'])->findOrFail($parkingId);
        
        // Obtener plazas libres por planta
        $plazasPorPlanta = [];
        $plazasLibres = 0;
        
        foreach ($parking->plantas as $planta) {
            $plazasLibresPlanta = $planta->plazas->where('estadoPlaza', 'libre');
            if ($plazasLibresPlanta->count() > 0) {
                $plazasPorPlanta[$planta->idPlanta] = [
                    'nombrePlanta' => $planta->nombrePlanta,
                    'plazas' => $plazasLibresPlanta
                ];
                $plazasLibres += $plazasLibresPlanta->count();
            }
        }

        if ($plazasLibres === 0) {
            return redirect()->back()
                            ->with('error', 'No hay plazas disponibles en este parking');
        }

        // Verificar si opcionesPagoParking es un string JSON o un array
        $opcionesPago = is_string($parking->opcionesPagoParking) 
            ? json_decode($parking->opcionesPagoParking, true) 
            : $parking->opcionesPagoParking;

        if (!$opcionesPago) {
            $opcionesPago = ['efectivo', 'tarjeta']; // valores por defecto
        }

        $horaEntrada = now();

        // Obtener las tarifas del parking
        $tarifas = $parking->tarifas->mapWithKeys(function ($tarifa) {
            return [$tarifa->tipoVehiculo => $tarifa->precio];
        });

        return view('registro-accesos.create', compact(
            'parking',
            'plazasLibres',
            'plazasPorPlanta',
            'horaEntrada',
            'opcionesPago',
            'tarifas'
        ));
    }

    private function getMultiplicador($tipoVehiculo)
    {
        return [
            'moto' => 0.8,
            'coche' => 1.0,
            'furgoneta' => 1.5
        ][$tipoVehiculo] ?? 1.0;
    }

    public function store(Request $request)
    {
        try {
            $validationRules = [
                'idParkingAcceso' => 'required|exists:parkings,idParking',
                'tipoVehiculo' => 'required|in:coche,moto,furgoneta',
                'matricula' => 'required|regex:/^[0-9]{4}[A-Z]{3}$/',
                'metodoPago' => 'required|string',
                'horaEntrada' => 'required|date',
                'horaSalida' => 'nullable|date|after:horaEntrada',
            ];

            $parking = Parking::findOrFail($request->idParkingAcceso);

            // Añadir validación de plaza solo si es parking sin control
            if ($parking->tipoParking === 'sinControl') {
                $validationRules['idPlazaAsignadaAcceso'] = 'required|exists:plazas,idPlaza';
            }

            $request->validate($validationRules);

            // Buscar plaza según el tipo de parking
            if ($parking->tipoParking === 'conControl') {
                // Buscar una plaza libre automáticamente
                $plazaLibre = null;
                foreach ($parking->plantas as $planta) {
                    $plazaLibre = $planta->plazas->where('estadoPlaza', 'libre')->first();
                    if ($plazaLibre) break;
                }

                if (!$plazaLibre) {
                    return redirect()->back()
                                   ->with('error', 'No hay plazas disponibles en este momento')
                                   ->withInput();
                }
            } else {
                // Para parking sin control, usar la plaza seleccionada
                $plazaLibre = Plaza::where('idPlaza', $request->idPlazaAsignadaAcceso)
                                  ->where('estadoPlaza', 'libre')
                                  ->first();

                if (!$plazaLibre) {
                    return redirect()->back()
                                   ->with('error', 'La plaza seleccionada ya no está disponible')
                                   ->withInput();
                }
            }

            // Calcular tarifa según el tipo de vehículo
            $tarifa = $parking->tarifas->where('tipoVehiculo', $request->tipoVehiculo)->first();
            
            if (!$tarifa) {
                return redirect()->back()
                               ->with('error', 'No hay tarifa definida para este tipo de vehículo')
                               ->withInput();
            }

            // Calcular el total estimado
            $horaEntrada = Carbon::parse($request->horaEntrada);
            $horaSalida = Carbon::parse($request->horaSalida);
            $horasEstimadas = $horaEntrada->diffInHours($horaSalida);
            $totalEstimado = $tarifa->precio * $horasEstimadas;

            DB::beginTransaction();

            // Actualizar el estado de la plaza
            $plazaLibre->update([
                'estadoPlaza' => 'ocupada',
                'matricula' => $request->matricula,
                'tipoVehiculo' => $request->tipoVehiculo
            ]);

            $registro = new RegistroAcceso();
            $registro->user_id = auth()->id();
            $registro->idParkingAcceso = $request->idParkingAcceso;
            $registro->idPlazaAsignadaAcceso = $plazaLibre->idPlaza;
            $registro->matricula = $request->matricula;
            $registro->tipoVehiculo = $request->tipoVehiculo;
            $registro->metodoPago = $request->metodoPago;
            $registro->horaEntrada = $request->horaEntrada;
            $registro->horaSalida = $request->horaSalida;
            $registro->estado = 'activo';
            $registro->tarifaAplicada = $tarifa->precio;
            $registro->save();

            DB::commit();

            // Preparar datos para el ticket
            $datosTicket = [
                'parking' => $parking->nombreParking,
                'matricula' => $registro->matricula,
                'tipoVehiculo' => $registro->tipoVehiculo,
                'planta' => $plazaLibre->planta->nombrePlanta,
                'plaza' => $plazaLibre->numeroPlaza,
                'horaEntrada' => $registro->horaEntrada,
                'horaSalida' => $registro->horaSalida,
                'metodoPago' => $registro->metodoPago,
                'tarifa' => $registro->tarifaAplicada,
                'horasEstimadas' => $horasEstimadas,
                'totalEstimado' => $totalEstimado,
                'idRegistro' => $registro->idRegistroAcceso
            ];

            return view('registro-accesos.ticket', compact('datosTicket'))
                    ->with('success', 'Registro creado correctamente');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                            ->withErrors($e->validator)
                            ->withInput()
                            ->with('error', 'Por favor, corrija los errores en el formulario');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error al crear registro de acceso: ' . $e->getMessage());
            return redirect()->back()
                            ->with('error', 'Ha ocurrido un error al procesar su solicitud. Por favor, inténtelo de nuevo.')
                            ->withInput();
        }
    }

    private function getTarifaMultiplicador(string $tipoVehiculo): float
    {
        return match($tipoVehiculo) {
            'moto' => 0.8,
            'furgoneta' => 1.5,
            default => 1.0, // coche
        };
    }

    public function registrarSalida(Request $request)
    {
        $request->validate([
            'matricula' => 'required|string|max:10',
            'idParkingAcceso' => 'required|exists:parkings,idParking',
        ]);

        try {
            $registro = RegistroAcceso::where('matricula', $request->matricula)
                ->where('idParkingAcceso', $request->idParkingAcceso)
                ->where('horaSalida', null)
                ->firstOrFail();

            $registro->registrarSalida();

            return view('registro-accesos.recibo', compact('registro'));

        } catch (\Exception $e) {
            return back()->with('error', 'No se encontró un registro de entrada activo para esta matrícula.');
        }
    }

    public function show($parking_id)
    {
        try {
            // Obtener el parking específico
            $parking = \App\Models\Parking::findOrFail($parking_id);
            
            // Obtener los parámetros de ordenación con valores por defecto
            $currentOrderBy = request('orderBy', 'horaEntrada');
            $currentOrderDirection = request('orderDirection', 'desc');
            $currentMetodoPago = request('metodoPago', '');
            $perPage = request('per_page', 10);
            
            // Construir la consulta base
            $query = \App\Models\RegistroAcceso::where('idParkingAcceso', $parking_id)
                ->with(['plaza.planta', 'parking']);
            
            // Aplicar filtro por método de pago si se seleccionó uno
            if (!empty($currentMetodoPago)) {
                $query->where('metodoPago', $currentMetodoPago);
            }
            
            // Aplicar ordenación según el campo seleccionado
            switch ($currentOrderBy) {
                case 'fecha':
                    $query->orderBy('horaEntrada', $currentOrderDirection);
                    break;
                case 'tarifa':
                    $query->orderBy('tarifaAplicada', $currentOrderDirection);
                    break;
                case 'estado':
                    $query->orderBy('estado', $currentOrderDirection);
                    break;
                default:
                    $query->orderBy('horaEntrada', $currentOrderDirection);
            }
            
            // Obtener los registros paginados
            $registros = $query->paginate($perPage)->withQueryString();
            
            // Definir los métodos de pago disponibles
            $metodosPago = [
                '' => 'Todos los métodos',
                'efectivo' => 'Efectivo',
                'tarjeta' => 'Tarjeta',
                'movil' => 'Móvil'
            ];
            
            // Verificar si hay datos
            if ($registros->isEmpty() && request()->has('metodoPago')) {
                session()->flash('info', 'No se encontraron registros con el filtro seleccionado.');
            }
            
            return view('registro-accesos.show', compact(
                'parking',
                'registros',
                'metodosPago',
                'currentMetodoPago',
                'currentOrderBy',
                'currentOrderDirection'
            ));
            
        } catch (\Exception $e) {
            \Log::error('Error al mostrar registros del parking: ' . $e->getMessage());
            return back()->with('error', 'Error al cargar los registros del parking');
        }
    }

    public function ticket($registro_id)
    {
        try {
            $registro = \App\Models\RegistroAcceso::with(['parking', 'plaza.planta'])
                ->where('idRegistroAcceso', $registro_id)
                ->firstOrFail();
            
            // Debug log para ver la información de la planta
            \Log::info('Datos del registro:', [
                'plaza_info' => $registro->plaza->toArray(),
                'planta_info' => $registro->plaza->planta->toArray()
            ]);

            // Calcular las horas estimadas
            $horasEstimadas = \Carbon\Carbon::parse($registro->horaEntrada)
                ->diffInHours(\Carbon\Carbon::parse($registro->horaSalida));
            
            // Calcular el total estimado
            $totalEstimado = $registro->tarifaAplicada * $horasEstimadas;

            // Preparar datos para el ticket con el nombre correcto de la planta
            $datosTicket = [
                'parking' => $registro->parking->nombreParking,
                'matricula' => $registro->matricula,
                'tipoVehiculo' => $registro->tipoVehiculo,
                'planta' => $registro->plaza->planta->nombrePlanta ?? 'N/A',
                'plaza' => $registro->plaza->numeroPlaza,
                'horaEntrada' => $registro->horaEntrada,
                'horaSalida' => $registro->horaSalida,
                'metodoPago' => $registro->metodoPago,
                'tarifa' => number_format($registro->tarifaAplicada, 2),
                'horasEstimadas' => number_format($horasEstimadas, 1),
                'totalEstimado' => number_format($totalEstimado, 2),
                'idRegistro' => $registro->idRegistroAcceso
            ];

            // Debug log para ver los datos del ticket
            \Log::info('Datos del ticket:', $datosTicket);

            return view('registro-accesos.ticket', compact('datosTicket'));
            
        } catch (\Exception $e) {
            \Log::error('Error al generar ticket: ' . $e->getMessage());
            return back()->with('error', 'Error al generar el ticket');
        }
    }
}