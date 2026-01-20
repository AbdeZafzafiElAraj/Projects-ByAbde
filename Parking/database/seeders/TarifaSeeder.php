<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tarifa;
use App\Models\Parking;

class TarifaSeeder extends Seeder
{
    public function run()
    {
        // Definimos precios base por ciudad
        $preciosPorCiudad = [
            'Barcelona' => [
                'coche' => ['min' => 3.50, 'max' => 4.50],
                'moto' => ['min' => 2.00, 'max' => 3.00],
                'furgoneta' => ['min' => 4.50, 'max' => 5.50],
            ],
            'Tarragona' => [
                'coche' => ['min' => 2.80, 'max' => 3.80],
                'moto' => ['min' => 1.80, 'max' => 2.50],
                'furgoneta' => ['min' => 3.80, 'max' => 4.80],
            ],
            'El Vendrell' => [
                'coche' => ['min' => 2.20, 'max' => 3.00],
                'moto' => ['min' => 1.50, 'max' => 2.00],
                'furgoneta' => ['min' => 3.00, 'max' => 4.00],
            ],
            'Vila-seca' => [ // Port Aventura (precios turísticos)
                'coche' => ['min' => 3.00, 'max' => 4.00],
                'moto' => ['min' => 2.00, 'max' => 2.50],
                'furgoneta' => ['min' => 4.00, 'max' => 5.00],
            ],
            'Calafell' => [
                'coche' => ['min' => 2.00, 'max' => 2.80],
                'moto' => ['min' => 1.20, 'max' => 1.80],
                'furgoneta' => ['min' => 2.80, 'max' => 3.80],
            ],
            'Ametlla de Mar' => [
                'coche' => ['min' => 1.80, 'max' => 2.50],
                'moto' => ['min' => 1.00, 'max' => 1.50],
                'furgoneta' => ['min' => 2.50, 'max' => 3.50],
            ],
        ];

        // Factores especiales
        $factoresEspeciales = [
            'Parking Plaza Catalunya' => 1.3, // Centro ciudad
            'Parking Estación Sants' => 1.2, // Estación principal
            'Parking Port Aventura' => 1.4, // Zona turística
            'Parking Mercadona Calafell' => 0, // Gratuito
        ];

        // Crear tarifas para cada parking
        Parking::all()->each(function ($parking) use ($preciosPorCiudad, $factoresEspeciales) {
            // Si el parking es gratuito, no creamos tarifas
            if (in_array('gratuito', json_decode($parking->opcionesPagoParking))) {
                return;
            }

            // Obtenemos los precios base para la ciudad
            $preciosCiudad = $preciosPorCiudad[$parking->ciudadParking] ?? [
                'coche' => ['min' => 2.00, 'max' => 3.00],
                'moto' => ['min' => 1.20, 'max' => 1.80],
                'furgoneta' => ['min' => 3.00, 'max' => 4.00],
            ];

            // Factor especial por ubicación específica
            $factorEspecial = $factoresEspeciales[$parking->nombreParking] ?? 1.0;

            // Tipos de vehículos y sus descripciones
            $tiposVehiculo = [
                'coche' => 'Tarifa estándar para coches',
                'moto' => 'Tarifa reducida para motocicletas',
                'furgoneta' => 'Tarifa especial para furgonetas y vehículos grandes'
            ];

            foreach ($tiposVehiculo as $tipo => $descripcion) {
                $precioBase = $preciosCiudad[$tipo];
                
                // Calculamos el precio final con variación aleatoria y factores
                $precioFinal = round(
                    (rand($precioBase['min'] * 100, $precioBase['max'] * 100) / 100) * $factorEspecial,
                    2
                );

                // Creamos la tarifa
                Tarifa::create([
                    'idParkingTarifa' => $parking->idParking,
                    'tipoVehiculo' => $tipo,
                    'precio' => $precioFinal,
                    'descripcion' => $descripcion . ' en ' . $parking->nombreParking
                ]);
            }
        });
    }
}