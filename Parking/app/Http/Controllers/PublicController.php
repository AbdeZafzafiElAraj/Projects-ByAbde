<?php

namespace App\Http\Controllers;

use App\Models\Parking;
use App\Models\Plaza;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PublicController extends Controller
{
    public function welcome()
    {
        $parkings = Parking::with(['plantas.plazas'])
            ->select([
                'parkings.*',
                DB::raw('(SELECT COUNT(*) FROM plazas 
                         JOIN plantas ON plazas.idPlantaPlaza = plantas.idPlanta 
                         WHERE plantas.idParkingPlanta = parkings.idParking 
                         AND plazas.estadoPlaza = "libre") as plazasLibres'),
                DB::raw('(SELECT COUNT(*) FROM plazas 
                         JOIN plantas ON plazas.idPlantaPlaza = plantas.idPlanta 
                         WHERE plantas.idParkingPlanta = parkings.idParking 
                         AND plazas.estadoPlaza = "ocupada") as plazasOcupadas'),
                DB::raw('(SELECT COUNT(*) FROM plazas 
                         JOIN plantas ON plazas.idPlantaPlaza = plantas.idPlanta 
                         WHERE plantas.idParkingPlanta = parkings.idParking) as plazasTotales'),
                DB::raw('(SELECT MIN(precio) FROM tarifas 
                         WHERE tarifas.idParkingTarifa = parkings.idParking) as tarifaMinima')
            ])->get();

        // Calcular estadísticas generales
        $estadisticas = [
            'totalParkings' => $parkings->count(),
            'plazasDisponibles' => $parkings->sum('plazasLibres'),
            'plazasOcupadas' => $parkings->sum('plazasOcupadas'),
            'plazasTotales' => $parkings->sum('plazasTotales')
        ];

        return view('welcome', compact('parkings', 'estadisticas'));
    }

    public function mapaParking()
    {
        $parkings = Parking::with(['plantas.plazas', 'tarifas'])
            ->select([
                'parkings.*',
                DB::raw('(SELECT COUNT(*) FROM plazas 
                         JOIN plantas ON plazas.idPlantaPlaza = plantas.idPlanta 
                         WHERE plantas.idParkingPlanta = parkings.idParking 
                         AND plazas.estadoPlaza = "libre") as plazasLibres'),
                DB::raw('(SELECT COUNT(*) FROM plazas 
                         JOIN plantas ON plazas.idPlantaPlaza = plantas.idPlanta 
                         WHERE plantas.idParkingPlanta = parkings.idParking 
                         AND plazas.estadoPlaza = "ocupada") as plazasOcupadas'),
                DB::raw('(SELECT COUNT(*) FROM plazas 
                         JOIN plantas ON plazas.idPlantaPlaza = plantas.idPlanta 
                         WHERE plantas.idParkingPlanta = parkings.idParking) as plazasTotales'),
                DB::raw('(SELECT MIN(precio) FROM tarifas 
                         WHERE tarifas.idParkingTarifa = parkings.idParking) as tarifaMinima')
            ])
            ->whereNotNull('latitudParking')
            ->whereNotNull('longitudParking')
            ->get()
            ->map(function($parking) {
                return [
                    'id' => $parking->idParking,
                    'latitud' => (float)$parking->latitudParking,
                    'longitud' => (float)$parking->longitudParking,
                    'nombre' => $parking->nombreParking,
                    'direccion' => $parking->direccionParking,
                    'plazasLibres' => (int)$parking->plazasLibres,
                    'plazasOcupadas' => (int)$parking->plazasOcupadas,
                    'plazasTotales' => (int)$parking->plazasTotales,
                    'tarifa' => $parking->tarifaMinima ? number_format($parking->tarifaMinima, 2) . '€/h' : 'N/A',
                    'horario' => $parking->horaAperturaParking . ' - ' . $parking->horaCierreParking,
                    'plantas' => $parking->plantas->map(function($planta) {
                        return [
                            'nombre' => $planta->nombrePlanta,
                            'libres' => $planta->plazas->where('estadoPlaza', 'libre')->count(),
                            'ocupadas' => $planta->plazas->where('estadoPlaza', 'ocupada')->count()
                        ];
                    })
                ];
            });

        return response()->json($parkings);
    }

    public function disponibilidad()
    {
        // Obtener disponibilidad en tiempo real
        $parkings = Parking::with(['plantas' => function($query) {
            $query->withCount([
                'plazas as plazas_libres' => function($query) {
                    $query->where('estadoPlaza', 'libre');
                },
                'plazas as plazas_ocupadas' => function($query) {
                    $query->where('estadoPlaza', 'ocupada');
                }
            ]);
        }])->get();

        return response()->json([
            'parkings' => $parkings->map(function($parking) {
                return [
                    'id' => $parking->idParking,
                    'nombre' => $parking->nombreParking,
                    'direccion' => $parking->direccionParking,
                    'plantas' => $parking->plantas->map(function($planta) {
                        return [
                            'nombre' => $planta->nombrePlanta,
                            'disponibles' => $planta->plazas_libres,
                            'ocupadas' => $planta->plazas_ocupadas,
                            'total' => $planta->plazas_libres + $planta->plazas_ocupadas
                        ];
                    })
                ];
            })
        ]);
    }

    public function infoParking(Parking $parking)
    {
        // Cargar el parking con todas sus relaciones necesarias
        $parking->load(['plantas.plazas' => function($query) {
            $query->select('id', 'planta_id', 'estado', 'tipo');
        }]);

        // Calcular estadísticas del parking
        $estadisticas = [
            'plazasLibres' => $parking->plantas->sum(function($planta) {
                return $planta->plazas->where('estado', 'libre')->count();
            }),
            'plazasOcupadas' => $parking->plantas->sum(function($planta) {
                return $planta->plazas->where('estado', 'ocupada')->count();
            }),
            'plazasCerradas' => $parking->plantas->sum(function($planta) {
                return $planta->plazas->where('estado', 'cerrada')->count();
            }),
            'porPlanta' => $parking->plantas->map(function($planta) {
                return [
                    'nombre' => $planta->nombrePlanta,
                    'libres' => $planta->plazas->where('estado', 'libre')->count(),
                    'ocupadas' => $planta->plazas->where('estado', 'ocupada')->count(),
                    'cerradas' => $planta->plazas->where('estado', 'cerrada')->count()
                ];
            })
        ];

        return view('welcome', compact('parking', 'estadisticas'));
    }
} 