<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Planta;
use App\Models\Plaza;
use App\Models\RegistroAcceso;
use Carbon\Carbon;

class PlazaSeeder extends Seeder
{
    public function run()
    {
        // Obtener todas las plantas con sus parkings
        $plantas = Planta::with('parking')->get();
        $now = now();
        $tiposVehiculos = ['coche', 'moto', 'furgoneta'];
        $estadosPlaza = ['libre', 'ocupada', 'cerrada', 'mantenimiento'];

        foreach ($plantas as $planta) {
            // Verificar que la planta tenga un parking asignado
            if (!$planta->parking) {
                continue;
            }

            // Configuración del layout según el tipo de parking
            $plazaAncho = 40;
            $plazaAlto = 80;
            $espaciado = 10;
            $margenLateral = 20;
            $margenSuperior = 20;

            // Calcular el número óptimo de columnas basado en la capacidad
            $plazasPorFila = min(10, ceil(sqrt($planta->capacidadMaximaPlanta)));
            $numFilas = ceil($planta->capacidadMaximaPlanta / $plazasPorFila);

            for ($i = 0; $i < $planta->capacidadMaximaPlanta; $i++) {
                // Calcular posición en la cuadrícula
                $fila = floor($i / $plazasPorFila);
                $columna = $i % $plazasPorFila;

                // Calcular coordenadas base
                $x = $margenLateral + ($columna * ($plazaAncho + $espaciado));
                $y = $margenSuperior + ($fila * ($plazaAlto + $espaciado));

                // Generar estado aleatorio con distribución realista
                $random = rand(0, 10);
                $estadoPlaza = match(true) {
                    $random < 5 => 'libre',         // 50% probabilidad
                    $random < 8 => 'ocupada',       // 30% probabilidad
                    $random < 9 => 'cerrada',       // 10% probabilidad
                    default => 'mantenimiento'      // 10% probabilidad
                };

                // Asignar tipo de vehículo y matrícula solo si está ocupada
                $tipoVehiculo = null;
                $matricula = null;

                if ($estadoPlaza === 'ocupada') {
                    $tipoVehiculo = $tiposVehiculos[array_rand($tiposVehiculos)];
                    $matricula = $this->generateMatricula();
                }

                // Crear la plaza con coordenadas precisas
                $plaza = Plaza::create([
                    'numeroPlaza' => $i + 1,
                    'tipoVehiculo' => $tipoVehiculo,
                    'estadoPlaza' => $estadoPlaza,
                    'matricula' => $matricula,
                    'idPlantaPlaza' => $planta->idPlanta,
                    // Coordenadas de los cuatro vértices del rectángulo
                    'x1' => $x,                     // Esquina superior izquierda
                    'y1' => $y,
                    'x2' => $x + $plazaAncho,      // Esquina superior derecha
                    'y2' => $y,
                    'x3' => $x + $plazaAncho,      // Esquina inferior derecha
                    'y3' => $y + $plazaAlto,
                    'x4' => $x,                     // Esquina inferior izquierda
                    'y4' => $y + $plazaAlto,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);

                // Si la plaza está ocupada, crear un registro de acceso
                if ($estadoPlaza === 'ocupada') {
                    $horaEntrada = Carbon::now()->subHours(rand(1, 24));
                    
                    RegistroAcceso::create([
                        'user_id' => rand(1, 2), // Asumiendo que tenemos admin y operador
                        'idParkingAcceso' => $planta->idParkingPlanta,
                        'idPlazaAsignadaAcceso' => $plaza->idPlaza,
                        'matricula' => $matricula,
                        'tipoVehiculo' => $tipoVehiculo,
                        'metodoPago' => ['efectivo', 'tarjeta', 'app'][array_rand(['efectivo', 'tarjeta', 'app'])],
                        'horaEntrada' => $horaEntrada,
                        'horaSalida' => null,
                        'estado' => 'activo',
                        'created_at' => $horaEntrada,
                        'updated_at' => $now,
                    ]);
                }
            }
        }
    }

    private function generateMatricula(): string
    {
        $numeros = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
        $letras = chr(rand(65, 90)) . chr(rand(65, 90)) . chr(rand(65, 90));
        return "{$numeros}{$letras}";
    }
}
