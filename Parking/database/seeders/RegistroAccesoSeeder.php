<?php

namespace Database\Seeders;

use App\Models\RegistroAcceso;
use App\Models\Parking;
use App\Models\Plaza;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class RegistroAccesoSeeder extends Seeder
{
    public function run()
    {
        // Reiniciar plazas manteniendo las coordenadas
        Plaza::query()->update([
            'estadoPlaza' => 'libre',
            'matricula' => null,
            'tipoVehiculo' => null
        ]);

        $parkings = Parking::all();
        $users = User::all();

        if ($parkings->isEmpty() || $users->isEmpty()) {
            return;
        }

        foreach ($parkings as $parking) {
            // Obtener las opciones de pago del parking
            $opcionesPagoParking = json_decode($parking->opcionesPagoParking, true);
            
            if (empty($opcionesPagoParking)) {
                continue; // Saltar si no hay opciones de pago definidas
            }

            // Obtener todas las plazas del parking
            $plazas = Plaza::with('planta')
                ->whereHas('planta', function($query) use ($parking) {
                    $query->where('idParkingPlanta', $parking->idParking);
                })
                ->whereNotNull('x1')
                ->whereNotNull('y1')
                ->get();

            if ($plazas->isEmpty()) {
                continue;
            }

            // Calcular número de registros basado en el tamaño del parking
            $numRegistros = min($plazas->count() * 3, 500); // Máximo 500 registros por parking
            
            for ($i = 0; $i < $numRegistros; $i++) {
                try {
                    $plaza = $plazas->random();
                    $user = $users->random();
                    
                    // Distribución de tipos de vehículos
                    $tiposVehiculo = [
                        'coche' => 70,
                        'moto' => 20,
                        'furgoneta' => 10
                    ];

                    $tipoVehiculo = $this->getRandomWeighted($tiposVehiculo);
                    $matricula = $this->generateMatricula();
                    
                    // Distribución de fechas
                    $horaEntrada = Carbon::now()
                        ->subMonths(rand(0, 2))
                        ->subDays(rand(0, 30))
                        ->subHours(rand(0, 23))
                        ->subMinutes(rand(0, 59));

                    $finalizado = rand(1, 100) <= 80;
                    $duracionHoras = $this->getDuracionEstancia();
                    $horaSalida = $finalizado ? $horaEntrada->copy()->addMinutes($duracionHoras * 60) : null;
                    
                    // Seleccionar un método de pago aleatorio de las opciones disponibles del parking
                    $metodoPago = $opcionesPagoParking[array_rand($opcionesPagoParking)];

                    // Cálculo de tarifa
                    $tarifaBase = 2.5;
                    $tarifaAplicada = $finalizado ? 
                        round($duracionHoras * $tarifaBase * $this->getTarifaMultiplicador($tipoVehiculo), 2) : 
                        null;

                    // Crear registro
                    $registro = RegistroAcceso::create([
                        'user_id' => $user->id,
                        'idParkingAcceso' => $parking->idParking,
                        'idPlazaAsignadaAcceso' => $plaza->idPlaza,
                        'matricula' => $matricula,
                        'tipoVehiculo' => $tipoVehiculo,
                        'metodoPago' => $metodoPago,
                        'horaEntrada' => $horaEntrada,
                        'horaSalida' => $horaSalida,
                        'estado' => $finalizado ? 'finalizado' : 'activo',
                        'tarifaAplicada' => $tarifaAplicada
                    ]);

                    // Actualizar plaza si el registro está activo
                    if (!$finalizado) {
                        $plaza->update([
                            'estadoPlaza' => 'ocupada',
                            'matricula' => $matricula,
                            'tipoVehiculo' => $tipoVehiculo
                        ]);
                        
                        $plazas = $plazas->where('idPlaza', '!=', $plaza->idPlaza);
                    }

                } catch (\Exception $e) {
                    continue;
                }
            }
        }
    }

    private function getRandomWeighted(array $weightedValues)
    {
        $array = [];
        foreach ($weightedValues as $value => $weight) {
            for ($i = 0; $i < $weight; $i++) {
                $array[] = $value;
            }
        }
        return $array[array_rand($array)];
    }

    private function getDuracionEstancia(): float
    {
        $probabilidad = rand(1, 100);
        if ($probabilidad <= 40) { // 40% corta duración
            return round(rand(1, 3) + rand(0, 59) / 60, 2);
        } elseif ($probabilidad <= 80) { // 40% media duración
            return round(rand(3, 8) + rand(0, 59) / 60, 2);
        } else { // 20% larga duración
            return round(rand(8, 24) + rand(0, 59) / 60, 2);
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

    private function generateMatricula(): string
    {
        $numeros = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
        $letras = substr(str_shuffle('BCDFGHJKLMNPQRSTVWXYZ'), 0, 3);
        return "{$numeros}{$letras}";
    }
}