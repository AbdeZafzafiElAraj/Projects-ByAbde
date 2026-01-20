<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Parking;

class ParkingSeeder extends Seeder
{
    public function run()
    {
        $parkings = [
            [
                'nombreParking' => 'Parking Vendrell',
                'direccionParking' => 'Calle de les Roquetes 79',
                'ciudadParking' => 'El Vendrell',
                'horaAperturaParking' => '08:00:00',
                'horaCierreParking' => '22:00:00',
                'opcionesPagoParking' => ['tarjeta', 'movil'],
                'imagenParking' => 'vendrell.jpg',
                'latitudParking' => 41.220375, 
                'longitudParking' => 1.538557,
                'tipoParking' => 'conControl',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombreParking' => 'Parking Ametlla',
                'direccionParking' => 'Avenida de Tarragona 11',
                'ciudadParking' => 'Ametlla de Mar',
                'horaAperturaParking' => '07:00:00',
                'horaCierreParking' => '23:00:00',
                'opcionesPagoParking' => ['efectivo'],
                'imagenParking' => 'ametlla.jpg',
                'latitudParking' => 40.885764, 
                'longitudParking' => 0.803519,
                'tipoParking' => 'sinControl',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombreParking' => 'Parking Plaza Catalunya',
                'direccionParking' => 'Plaza Catalunya 1-4',
                'ciudadParking' => 'Barcelona',
                'horaAperturaParking' => '00:00:00', // 24h
                'horaCierreParking' => '23:59:59',
                'opcionesPagoParking' => ['efectivo', 'tarjeta', 'movil'],
                'imagenParking' => 'plaza_catalunya.jpg',
                'latitudParking' => 41.386867,
                'longitudParking' => 2.170064,
                'tipoParking' => 'conControl',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombreParking' => 'Parking Port Aventura',
                'direccionParking' => 'Avinguda Alcalde Pere Molas',
                'ciudadParking' => 'Vila-seca',
                'horaAperturaParking' => '09:00:00',
                'horaCierreParking' => '21:00:00',
                'opcionesPagoParking' => ['tarjeta', 'movil'],
                'imagenParking' => 'port_aventura.jpg',
                'latitudParking' => 41.0898,
                'longitudParking' => 1.1499,
                'tipoParking' => 'conControl',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombreParking' => 'Parking Rambla Nova',
                'direccionParking' => 'Rambla Nova 123',
                'ciudadParking' => 'Tarragona',
                'horaAperturaParking' => '07:30:00',
                'horaCierreParking' => '23:30:00',
                'opcionesPagoParking' => ['efectivo', 'tarjeta'],
                'imagenParking' => 'rambla_nova.jpg',
                'latitudParking' => 41.1189,
                'longitudParking' => 1.2445,
                'tipoParking' => 'conControl',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombreParking' => 'Parking Estación Sants',
                'direccionParking' => 'Plaça dels Països Catalans',
                'ciudadParking' => 'Barcelona',
                'horaAperturaParking' => '00:00:00', // 24h
                'horaCierreParking' => '23:59:59',
                'opcionesPagoParking' => ['efectivo', 'tarjeta', 'movil'],
                'imagenParking' => 'sants.jpg',
                'latitudParking' => 41.3790,
                'longitudParking' => 2.1403,
                'tipoParking' => 'conControl',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombreParking' => 'Parking Mercadona Calafell',
                'direccionParking' => 'Carrer del Ferrocarril 54',
                'ciudadParking' => 'Calafell',
                'horaAperturaParking' => '09:00:00',
                'horaCierreParking' => '21:30:00',
                'opcionesPagoParking' => ['gratuito'],
                'imagenParking' => 'mercadona_calafell.jpg',
                'latitudParking' => 41.2022,
                'longitudParking' => 1.5657,
                'tipoParking' => 'sinControl',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];

        foreach ($parkings as $parking) {
            // Convertir el array de opciones de pago a JSON
            $parking['opcionesPagoParking'] = json_encode($parking['opcionesPagoParking']);
            
            // Crear el parking
            Parking::create($parking);
        }
    }
}