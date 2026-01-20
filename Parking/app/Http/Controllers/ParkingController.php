<?php

namespace App\Http\Controllers;

use App\Models\Parking;
use App\Models\Planta;
use App\Models\Plaza;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class ParkingController extends Controller
{
    // Mostrar todos los parkings
    public function index()
    {
        $parkings = Parking::all(); // Obtener todos los parkings
        return view('parkings.index', compact('parkings')); // Pasar los parkings a la vista
    }

    public function show($id)
    {
        $parking = Parking::with(['plantas.plazas'])->findOrFail($id);
    
        // Calcular las plazas ocupadas, disponibles y cerradas
        $capacidadTotal = $parking->plantas->sum('capacidadPlanta');
        $plazasOcupadas = $parking->plantas->flatMap(fn($planta) => $planta->plazas->where('estadoPlaza', 'ocupada'))->count();
        $plazasCerradas = $parking->plantas->flatMap(fn($planta) => $planta->plazas->where('estadoPlaza', 'cerrada'))->count();
        //$plazasDisponibles = $capacidadTotal - ($plazasOcupadas + $plazasCerradas);
        $plazasDisponibles = $parking->plantas->flatMap(fn($planta) => $planta->plazas->where('estadoPlaza', 'libre'))->count();
    
        // Plantas y tarifas
        $plantas = $parking->plantas;
        $tarifas = $parking->tarifas;
    
        return view('parkings.show', compact(
            'parking',
            'plantas',
            'tarifas',
            'plazasOcupadas',
            'plazasDisponibles',
            'plazasCerradas'
        ));
    }
    


    // Mostrar el dashboard de los parkings
    public function indexDashboard()
    {
        $parkings = Parking::all(); // Obtener todos los parkings
        return view('dashboard', compact('parkings')); // Pasar los parkings a la vista
    }

    // Mostrar el formulario para crear un nuevo parking
    public function create()
    {
        return view('parkings.create'); // Mostrar vista de creación
    }

    // Guardar un nuevo parking en la base de datos
    public function store(Request $request)
    {
        // Validar los datos del formulario
        $request->validate([
            'nombreParking' => 'required|string',
            'direccionParking' => 'required|string',
            'ciudadParking' => 'required|string',
            'horaAperturaParking' => 'required',
            'horaCierreParking' => 'required',
            'opcionesPagoParking' => 'required|array|min:1',
            'opcionesPagoParking.*' => 'in:tarjeta,efectivo,movil,gratuito',
            'tipoParking' => 'required|in:conControl,sinControl',
            'latitudParking' => 'required|numeric',
            'longitudParking' => 'required|numeric',
            'imagenParking' => 'nullable|image|max:10240',
            'numPlantas' => 'required|integer|min:1',
            'plazasPorPlanta' => 'required|array',
            'plazasPorPlanta.*' => 'required|integer|min:1',
        ]);

        try {
            // Crear array de plazas por planta
            $plazasPorPlanta = $request->plazasPorPlanta;

            // Verificar que tenemos el número correcto de plazas por planta
            if (count($plazasPorPlanta) != $request->numPlantas) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['error' => 'El número de plantas no coincide con las plazas especificadas']);
            }

            // Guardar la imagen si existe
            $imagenPath = null;
            if ($request->hasFile('imagenParking')) {
                $imagenPath = $request->file('imagenParking')->store('images', 'public');
            }

            // Crear el parking
            $parking = Parking::create([
                'nombreParking' => $request->nombreParking,
                'direccionParking' => $request->direccionParking,
                'ciudadParking' => $request->ciudadParking,
                'horaAperturaParking' => $request->horaAperturaParking,
                'horaCierreParking' => $request->horaCierreParking,
                'opcionesPagoParking' => $request->opcionesPagoParking,
                'tipoParking' => $request->tipoParking,
                'latitudParking' => $request->latitudParking,
                'longitudParking' => $request->longitudParking,
                'imagenParking' => $imagenPath,
            ]);

            // Configuración del layout de plazas
            $plazaAncho = 40;
            $plazaAlto = 80;
            $espaciado = 2;
            $plazasPorFila = 10;

            // Crear las plantas con su número específico de plazas
            for ($i = 0; $i < $request->numPlantas; $i++) {
                $planta = Planta::create([
                    'nombrePlanta' => 'Planta ' . ($i + 1),
                    'capacidadMaximaPlanta' => $plazasPorPlanta[$i],
                    'estadoPlanta' => 'abierta',
                    'idParkingPlanta' => $parking->idParking
                ]);

                // Crear las plazas para esta planta
                $plazas = [];
                for ($j = 0; $j < $plazasPorPlanta[$i]; $j++) {
                    // Calcular la posición en la cuadrícula
                    $fila = floor($j / $plazasPorFila);
                    $columna = $j % $plazasPorFila;

                    // Calcular las coordenadas reales
                    $x = $columna * ($plazaAncho + $espaciado);
                    $y = $fila * ($plazaAlto + $espaciado);

                    $plazas[] = [
                        'numeroPlaza' => $j + 1,
                        'estadoPlaza' => 'libre',
                        'x1' => $x,
                        'y1' => $y,
                        'x2' => $x + $plazaAncho,
                        'y2' => $y,
                        'x3' => $x + $plazaAncho,
                        'y3' => $y + $plazaAlto,
                        'x4' => $x,
                        'y4' => $y + $plazaAlto,
                        'idPlantaPlaza' => $planta->idPlanta,
                        'created_at' => now(),
                        'updated_at' => now()
                    ];
                }

                // Insertar todas las plazas de una vez
                DB::table('plazas')->insert($plazas);
            }

            return redirect()->route('parkings.index')
                ->with('success', 'Parking creado con éxito');

        } catch (\Exception $e) {
            // Si algo falla, eliminar la imagen si se subió
            if ($imagenPath) {
                Storage::disk('public')->delete($imagenPath);
            }
            
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Error al crear el parking: ' . $e->getMessage()]);
        }
    }


    // Mostrar el formulario para editar un parking existente
    public function edit($id)
    {
        $parking = Parking::with(['plantas', 'tarifas'])->findOrFail($id);
        return view('parkings.edit', compact('parking'));
    }

    public function update(Request $request, $id)
    {
        $parking = Parking::findOrFail($id);

        try {
            // Validación de datos
            $validated = $request->validate([
                'nombreParking' => 'required|string|max:255',
                'direccionParking' => 'required|string|max:255',
                'ciudadParking' => 'required|string|max:255',
                'horaAperturaParking' => 'required',
                'horaCierreParking' => 'required',
                'opcionesPagoParking' => 'required|array|min:1',
                'opcionesPagoParking.*' => 'in:tarjeta,efectivo,movil,gratuito',
                'tipoParking' => 'required|in:conControl,sinControl',
                'latitudParking' => 'required|numeric',
                'longitudParking' => 'required|numeric',
                'imagenParking' => 'nullable|image|max:2048',
            ]);

            // Crear array con todos los datos validados
            $datosActualizar = $validated;
            
            // Convertir opcionesPagoParking a JSON antes de guardar
            $datosActualizar['opcionesPagoParking'] = json_encode($request->opcionesPagoParking);

            // Manejar la imagen si se ha subido una nueva
            if ($request->hasFile('imagenParking')) {
                if ($parking->imagenParking) {
                    Storage::disk('public')->delete($parking->imagenParking);
                }
                $datosActualizar['imagenParking'] = $request->file('imagenParking')->store('images', 'public');
            } else {
                unset($datosActualizar['imagenParking']);
            }

            // Actualizar el parking
            $parking->update($datosActualizar);

            return redirect()
                ->route('parkings.show', $parking->idParking)
                ->with('success', 'Parking actualizado correctamente');

        } catch (\Exception $e) {
            return redirect()
                ->route('parkings.edit', $parking->idParking)
                ->withInput()
                ->withErrors(['error' => 'Error al actualizar el parking: ' . $e->getMessage()]);
        }
    }



    // Eliminar un parking y sus datos asociados
    public function destroy(Parking $parking)
    {
        // Eliminar las plantas y plazas asociadas
        $parking->plantas()->each(function ($planta) {
            $planta->plazas()->delete(); // Eliminar las plazas de la planta
            $planta->delete(); // Eliminar la planta
        });

        // Eliminar la imagen asociada si existe
        if ($parking->imagenParking) {
            Storage::delete($parking->imagenParking);
        }

        // Eliminar el parking de la base de datos
        $parking->delete();

        // Redirigir a la lista de parkings con un mensaje de éxito
        return redirect()->route('parkings.index')->with('success', 'Parking y sus datos eliminados con éxito.');
    }
    public function getCoordenadas($id)
    {
        $parking = Parking::findOrFail($id);
        
        // Devolver las coordenadas como una respuesta JSON
        return response()->json([
            'latitud' => $parking->latitudParking,
            'longitud' => $parking->longitudParking
        ]);
    }
}
