<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegistroAcceso extends Model
{
    use HasFactory;

    protected $table = 'registro_accesos';
    protected $primaryKey = 'idRegistroAcceso';

    protected $fillable = [
        'user_id',
        'idParkingAcceso',
        'idPlazaAsignadaAcceso',
        'matricula',
        'tipoVehiculo',
        'metodoPago',
        'horaEntrada',
        'horaSalida',
        'estado',
        'tarifaAplicada'
    ];

    protected $casts = [
        'horaEntrada' => 'datetime',
        'horaSalida' => 'datetime',
        'tarifaAplicada' => 'decimal:2'
    ];

    // Relación muchos a uno con Tarifas
    public function tarifa()
    {
        return $this->belongsTo(Tarifa::class, 'idParkingAcceso', 'idParkingTarifa')
                    ->where('tipoVehiculo', $this->tipoVehiculo);
    }

    // Relación muchos a uno con Plazas
    public function plaza()
    {
        return $this->belongsTo(Plaza::class, 'idPlazaAsignadaAcceso', 'idPlaza');
    }

    // Relación muchos a uno con Parkings
    public function parking()
    {
        return $this->belongsTo(Parking::class, 'idParkingAcceso', 'idParking');
    }

    /**
     * Obtiene el usuario que realizó el registro.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Método para calcular el costo total basado en la tarifa y la duración
     */
    public function calcularCostoTotal()
    {
        if (!$this->horaEntrada || !$this->horaSalida) {
            return null; // No se puede calcular sin ambos tiempos
        }

        // Calcula la diferencia en horas usando Carbon
        $horaEntrada = Carbon::parse($this->horaEntrada);
        $horaSalida = Carbon::parse($this->horaSalida);
        $horas = $horaEntrada->diffInMinutes($horaSalida) / 60; // Convierte minutos a horas

        // Obtiene el precio por hora de la tarifa asociada
        $precioPorHora = $this->tarifa ? $this->tarifa->precioHora : 0.0;

        // Calcula el costo total
        return round($horas * $precioPorHora, 2); // Redondeo a 2 decimales
    }

    /**
     * Registrar entrada en una plaza
     */
    public function registrarEntrada(string $matricula, int $plazaId): void
    {
        // Cargar la plaza correspondiente
        $plaza = Plaza::find($plazaId);

        if (!$plaza) {
            throw new \Exception('La plaza especificada no existe.');
        }

        // Verificar que la plaza esté libre
        if ($plaza->estadoPlaza === 'ocupada') {
            throw new \Exception('La plaza ya está ocupada.');
        }

        // Actualizar el estado de la plaza
        $plaza->estadoPlaza = 'ocupada';
        $plaza->save();

        // Crear el registro de acceso
        $this->matricula = $matricula;
        $this->horaEntrada = now();
        $this->idPlazaAsignadaAcceso = $plazaId;
        $this->idParkingAcceso = $plaza->planta->parking->idParking; // Relación con el parking
        $this->tipoParking = $plaza->planta->parking->tipoParking;
        $this->estadoPlaza = 'ocupada';
        $this->tipoVehiculo = $plaza->tipoVehiculo;
        $this->save();
    }

    /**
     * Registrar salida del vehículo
     */
    public function registrarSalida(): void
    {
        // Verificar que ya existe una hora de entrada
        if ($this->horaSalida) {
            throw new \Exception('La salida ya ha sido registrada para este acceso.');
        }

        // Actualizar la hora de salida
        $this->horaSalida = now();

        // Calcular el costo total utilizando la tarifa asociada
        $tarifa = Tarifa::where('idParkingTarifa', $this->idParkingAcceso)
            ->where('tipoVehiculo', $this->tipoVehiculo)
            ->first();

        if ($tarifa) {
            $this->idTarifaAcceso = $tarifa->idTarifa; // Asignar tarifa al registro
            $this->tarifaAplicada = $this->calcularCostoTotal();
        } else {
            $this->tarifaAplicada = 0.0; // Sin tarifa encontrada
        }

        // Guardar los cambios en el registro de acceso
        $this->save();

        // Liberar la plaza asociada
        $plaza = Plaza::find($this->idPlazaAsignadaAcceso);
        if ($plaza) {
            $plaza->estadoPlaza = 'lliure';
            $plaza->save();
        }
    }
}
