<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Planta;

class PlantaSeeder extends Seeder
{
    public function run()
    {
        $configuracionParkings = [
            // Parking Vendrell (200 plazas)
            1 => [
                ['nombre' => 'Planta 1A', 'capacidad' => 100],
                ['nombre' => 'Planta 2A', 'capacidad' => 100],
            ],
            // Parking Ametlla (150 plazas)
            2 => [
                ['nombre' => 'Planta 1B', 'capacidad' => 75],
                ['nombre' => 'Planta 2B', 'capacidad' => 75],
            ],
            // Parking Plaza Catalunya (300 plazas)
            3 => [
                ['nombre' => 'Sótano -2', 'capacidad' => 100],
                ['nombre' => 'Sótano -1', 'capacidad' => 100],
                ['nombre' => 'Planta Baja', 'capacidad' => 100],
            ],
            // Parking Port Aventura (400 plazas)
            4 => [
                ['nombre' => 'Zona A', 'capacidad' => 100],
                ['nombre' => 'Zona B', 'capacidad' => 100],
                ['nombre' => 'Zona C', 'capacidad' => 100],
                ['nombre' => 'Zona VIP', 'capacidad' => 100],
            ],
            // Parking Rambla Nova (180 plazas)
            5 => [
                ['nombre' => 'Planta -1', 'capacidad' => 60],
                ['nombre' => 'Planta 1', 'capacidad' => 60],
                ['nombre' => 'Planta 2', 'capacidad' => 60],
            ],
            // Parking Estación Sants (250 plazas)
            6 => [
                ['nombre' => 'Sótano -2', 'capacidad' => 85],
                ['nombre' => 'Sótano -1', 'capacidad' => 85],
                ['nombre' => 'Planta Exterior', 'capacidad' => 80],
            ],
            // Parking Mercadona Calafell (120 plazas)
            7 => [
                ['nombre' => 'Zona Clientes', 'capacidad' => 100],
                ['nombre' => 'Zona Empleados', 'capacidad' => 20],
            ],
        ];

        $plantas = [];
        $now = now();

        foreach ($configuracionParkings as $idParking => $plantasParking) {
            foreach ($plantasParking as $planta) {
                $plantas[] = [
                    'nombrePlanta' => $planta['nombre'],
                    'capacidadMaximaPlanta' => $planta['capacidad'],
                    'estadoPlanta' => 'abierta',
                    'idParkingPlanta' => $idParking,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        foreach ($plantas as $planta) {
            Planta::create($planta);
        }
    }
}