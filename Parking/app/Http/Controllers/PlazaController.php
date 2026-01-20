<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Plaza;
use App\Models\Planta;
use App\Models\Parking;
use Illuminate\Support\Facades\DB;
use App\Models\RegistroAcceso;

class PlazaController extends Controller
{
    public function index(Request $request)
    {
        // Obtener todos los parkings y plantas para los selectores
        $parkings = Parking::all();
        $plantas = Planta::all();
        
        // Consulta base
        $query = Plaza::with(['planta.parking']);
        
        // Aplicar filtros
        if ($request->filled('parking')) {
            $query->whereHas('planta', function($q) use ($request) {
                $q->where('idParkingPlanta', $request->parking);
            });
        }
        
        if ($request->filled('planta')) {
            $query->where('idPlantaPlaza', $request->planta);
        }
        
        if ($request->filled('estado')) {
            $query->where('estadoPlaza', $request->estado);
        }
        
        // Paginación
        $perPage = $request->input('per_page', 10);
        $plazas = $query->paginate($perPage)->withQueryString();

        return view('plazas.index', compact('plazas', 'parkings', 'plantas'));
    }

    public function show($id)
    {
        // Carga la plaza con las relaciones necesarias
        $plaza = Plaza::with(['planta'])->findOrFail($id);
        
        return view('plazas.show', compact('plaza'));
    }

    public function create()
    {
        // Obtener todos los parkings con sus plantas
        $parkings = Parking::with('plantas')->get();
    
        return view('plazas.create', compact('parkings'));
    }    

    public function store(Request $request)
    {
        $planta = Planta::findOrFail($request->idPlantaPlaza);
        
        if (!$planta->puedeAnadirPlazas()) {
            return back()->withErrors(['error' => 'No se pueden añadir más plazas. Se ha alcanzado la capacidad máxima.']);
        }
        
        // Validar los datos del formulario
        $validatedData = $request->validate([
            'idPlantaPlaza' => 'required|exists:plantas,idPlanta',
            'x1' => 'required|numeric|min:0',
            'y1' => 'required|numeric|min:0',
            'x2' => 'required|numeric|min:0',
            'y2' => 'required|numeric|min:0',
            'x3' => 'required|numeric|min:0',
            'y3' => 'required|numeric|min:0',
            'x4' => 'required|numeric|min:0',
            'y4' => 'required|numeric|min:0',
        ]);

        // Validar que las coordenadas formen un rectángulo válido
        if (!$this->validarRectangulo($validatedData)) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['coordenadas' => 'Las coordenadas deben formar un rectángulo válido']);
        }

        // Obtener la planta seleccionada para saber a qué parking pertenece
        $planta = Planta::findOrFail($request->idPlantaPlaza);
        
        // Obtener el último número de plaza para este parking
        $ultimaPlaza = Plaza::whereHas('planta', function($query) use ($planta) {
            $query->where('idParkingPlanta', $planta->idParkingPlanta);
        })->max('numeroPlaza');

        // Asignar el siguiente número disponible
        $validatedData['numeroPlaza'] = ($ultimaPlaza ?? 0) + 1;
        
        // Añadir valores por defecto
        $validatedData['estadoPlaza'] = 'libre';
        $validatedData['tipoVehiculo'] = null;
        $validatedData['matricula'] = null;

        // Crear la plaza
        Plaza::create($validatedData);

        // Redirigir con mensaje de éxito
        return redirect()->route('plazas.index')->with('success', 'Plaza creada correctamente.');
    }

    // Añadir este método privado para validar el rectángulo
    private function validarRectangulo($data)
    {
        // Verificar que los puntos formen un rectángulo
        
        // Verificar que los puntos no sean todos iguales
        $puntos = [
            [$data['x1'], $data['y1']],
            [$data['x2'], $data['y2']],
            [$data['x3'], $data['y3']],
            [$data['x4'], $data['y4']]
        ];
        
        // Calcular las distancias entre los puntos
        $distancias = [];
        for ($i = 0; $i < 4; $i++) {
            $j = ($i + 1) % 4;
            $distancias[] = sqrt(
                pow($puntos[$i][0] - $puntos[$j][0], 2) +
                pow($puntos[$i][1] - $puntos[$j][1], 2)
            );
        }
        
        // En un rectángulo, los lados opuestos deben ser iguales
        $epsilon = 0.0001; // Margen de error para comparaciones de punto flotante
        return abs($distancias[0] - $distancias[2]) < $epsilon &&
               abs($distancias[1] - $distancias[3]) < $epsilon;
    }

    public function edit($id)
    {
        // Cargar la plaza junto con la planta y el parking asociado
        $plaza = Plaza::with(['planta.parking.plantas'])->findOrFail($id);
    
        // Obtener todos los parkings
        $parkings = Parking::all();
    
        // Obtener las plantas del parking asociado
        $plantas = $plaza->planta->parking->plantas ?? collect();
    
        return view('plazas.edit', compact('plaza', 'plantas', 'parkings'));
    }    

    public function update(Request $request, $id)
    {
        
        // Obtener la plaza
        $plaza = Plaza::findOrFail($id);

        try {
            // Validar los datos recibidos
            $validatedData = $request->validate([
                'tipoVehiculo' => 'nullable|in:coche,furgoneta,moto',
                'matricula' => [
                    'nullable',
                    'string',
                    function ($attribute, $value, $fail) {
                        if ($value !== null) {
                            // Eliminar espacios y convertir a mayúsculas
                            $value = strtoupper(preg_replace('/\s+/', '', $value));
                            
                            // Verificar que tenga exactamente 7 caracteres
                            if (strlen($value) !== 7) {
                                $fail('La matrícula debe tener 4 números seguidos de 3 letras.');
                                return;
                            }
                            
                            // Verificar que los primeros 4 caracteres son números
                            if (!preg_match('/^\d{4}/', substr($value, 0, 4))) {
                                $fail('Los primeros 4 caracteres deben ser números.');
                                return;
                            }
                            
                            // Verificar que los últimos 3 caracteres son letras
                            if (!preg_match('/^[A-Z]{3}$/', substr($value, 4))) {
                                $fail('Los últimos 3 caracteres deben ser letras.');
                                return;
                            }
                        }
                    }
                ],
                'estadoPlaza' => 'required|in:libre,ocupada,cerrada,mantenimiento',
                'idPlantaPlaza' => 'required|exists:plantas,idPlanta',
            ]);

            if ($validatedData['estadoPlaza'] === 'cerrada') {
                // Si la plaza está cerrada, borra la matrícula y el tipo de vehículo
                $validatedData['tipoVehiculo'] = null;
                $validatedData['matricula'] = null;
            } elseif ($validatedData['estadoPlaza'] === 'ocupada') {
                // Si la plaza está ocupada, validar que los campos sean obligatorios
                $request->validate([
                    'tipoVehiculo' => 'required|in:coche,furgoneta,moto',
                    'matricula' => 'required|string',
                ]);
            } else {
                // Si no está cerrada ni ocupada, está libre
                $validatedData['tipoVehiculo'] = null;
                $validatedData['matricula'] = null;
                $validatedData['estadoPlaza'] = 'libre'; // Redundante, pero asegura consistencia
            }

            // Actualizar las coordenadas de la plaza
            $plaza->update($request->only([
                'tipoVehiculo',
                'matricula',
                'estadoPlaza',
                'idPlantaPlaza',
                'x1',
                'y1',
                'x2',
                'y2',
                'x3',
                'y3',
                'x4',
                'y4',
            ]));
        
            // Guardar los cambios en la plaza
            $plaza->save();
        
            // Redirigir con mensaje de éxito
            return redirect()->route('plazas.index')->with('success', 'Plaza actualizada correctamente.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', $e->validator->errors()->first());
        }
    }    

    public function destroy($id)
    {
        $plaza = Plaza::findOrFail($id);
        $plaza->delete();
        return redirect()->route('plazas.index')->with('success', 'Plaza eliminada correctamente.');
    }

    public function aparcar(Request $request, Plaza $plaza)
    {
        try {
            if ($plaza->estadoPlaza !== 'libre') {
                return response()->json(['message' => 'La plaza no está disponible'], 400);
            }

            $request->validate([
                'matricula' => 'required|string|max:7',
                'tipoVehiculo' => 'required|in:coche,moto,furgoneta',
                'metodoPago' => 'required|string'
            ]);

            DB::beginTransaction();

            $plaza->update([
                'estadoPlaza' => 'ocupada',
                'matricula' => $request->matricula,
                'tipoVehiculo' => $request->tipoVehiculo
            ]);

            // Crear registro de acceso
            RegistroAcceso::create([
                'user_id' => auth()->id(),
                'idParkingAcceso' => $plaza->planta->idParkingPlanta,
                'idPlazaAsignadaAcceso' => $plaza->idPlaza,
                'matricula' => $request->matricula,
                'tipoVehiculo' => $request->tipoVehiculo,
                'metodoPago' => $request->metodoPago,
                'horaEntrada' => now(),
                'estado' => 'activo'
            ]);

            DB::commit();
            return response()->json(['message' => 'Vehículo aparcado correctamente']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function desaparcar(Plaza $plaza)
    {
        try {
            if ($plaza->estadoPlaza !== 'ocupada') {
                return response()->json(['message' => 'La plaza no está ocupada'], 400);
            }

            DB::beginTransaction();

            // Buscar el registro de acceso activo
            $registro = RegistroAcceso::where('idPlazaAsignadaAcceso', $plaza->idPlaza)
                ->where('estado', 'activo')
                ->first();

            if ($registro) {
                $registro->update([
                    'horaSalida' => now(),
                    'estado' => 'finalizado'
                ]);
            }

            // Actualizar estado de la plaza
            $plaza->update([
                'estadoPlaza' => 'libre',
                'matricula' => null,
                'tipoVehiculo' => null
            ]);

            DB::commit();
            return response()->json(['message' => 'Vehículo desaparcado correctamente']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function bloquear(Plaza $plaza)
    {
        try {
            if ($plaza->estadoPlaza !== 'libre') {
                return response()->json(['message' => 'Solo se pueden poner en mantenimiento las plazas libres'], 400);
            }

            // Añadir comillas al valor del estado
            $plaza->update([
                'estadoPlaza' => 'mantenimiento',
                'tipoVehiculo' => null,
                'matricula' => null
            ]);
            
            return response()->json(['message' => 'Plaza puesta en mantenimiento correctamente']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function desbloquear(Plaza $plaza)
    {
        try {
            if ($plaza->estadoPlaza !== 'mantenimiento') {
                return response()->json(['message' => 'La plaza no está en mantenimiento'], 400);
            }

            // Añadir comillas al valor del estado
            $plaza->update([
                'estadoPlaza' => 'libre',
                'tipoVehiculo' => null,
                'matricula' => null
            ]);
            
            return response()->json(['message' => 'Plaza habilitada correctamente']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function cerrar(Plaza $plaza)
    {
        try {
            if ($plaza->estadoPlaza !== 'libre') {
                return response()->json(['message' => 'Solo se pueden cerrar plazas libres'], 400);
            }

            $plaza->update([
                'estadoPlaza' => 'cerrada',
                'tipoVehiculo' => null,
                'matricula' => null
            ]);
            
            return response()->json(['message' => 'Plaza cerrada correctamente']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function abrir(Plaza $plaza)
    {
        try {
            if ($plaza->estadoPlaza !== 'cerrada') {
                return response()->json(['message' => 'La plaza no está cerrada'], 400);
            }

            $plaza->update([
                'estadoPlaza' => 'libre',
                'tipoVehiculo' => null,
                'matricula' => null
            ]);
            
            return response()->json(['message' => 'Plaza abierta correctamente']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function updateEstado(Request $request, Plaza $plaza)
    {
        $request->validate([
            'estado' => 'required|in:libre,ocupada,mantenimiento'
        ]);

        $plaza->update([
            'estadoPlaza' => $request->estado
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Estado actualizado correctamente'
        ]);
    }
}
