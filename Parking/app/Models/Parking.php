<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Parking extends Model
{
    use HasFactory;

    protected $table = 'parkings';
    protected $primaryKey = 'idParking';

    // Atributos asignables en masa
    protected $fillable = [
        'nombreParking',
        'direccionParking',
        'ciudadParking',
        'horaAperturaParking',
        'horaCierreParking',
        'opcionesPagoParking',
        'tipoParking',
        'latitudParking',
        'longitudParking',
        'imagenParking'
    ];

    // Atributos calculados
    protected $appends = [
        'capacidadTotal', 
        'plazasDisponibles', 
        'plazasOcupadas', 
        'plazasCerradas'
    ];

    // Conversion de opcionesPagoParking a array 
    protected $casts = [
        'opcionesPagoParking' => 'json'
    ];

    /**
     * Relaciones
     */

    // Relación uno a muchos con Plantas
    public function tarifas()
    {
        // Cambiamos la relación para usar idParkingTarifa
        return $this->hasMany(Tarifa::class, 'idParkingTarifa', 'idParking');
    }

    public function plantas()
    {
        return $this->hasMany(Planta::class, 'idParkingPlanta', 'idParking');
    }

    public function plazas()
    {
        return $this->hasManyThrough(
            Plaza::class,
            Planta::class,
            'idParkingPlanta', // Clave foránea en plantas
            'idPlantaPlaza',   // Clave foránea en plazas
            'idParking',       // Clave local en parkings
            'idPlanta'         // Clave local en plantas
        );
    }

    // Relación uno a muchos con RegistroAccesos
    public function registroAccesos()
    {
        // hasMany = Un parking tiene muchos registros de acceso
        return $this->hasMany(RegistroAcceso::class, 'idParkingAcceso', 'idParking');
    }

    // Relación uno a uno con ParkingSinControl
    public function parkingSinControl()
    {
        // hasOne = Un parking tiene un registro sin control
        return $this->hasOne(ParkingSinControl::class, 'idParkingSC', 'idParking')->where('tipoParking', 'sinControl');
    }

    // Relación uno a uno con ParkingConControl
    public function parkingConControl()
    {
        // hasOne = Un parking tiene un registro con control
        return $this->hasOne(ParkingConControl::class, 'idParkingCC', 'idParking')->where('tipoParking', 'conControl');
    }

    /**
     * Métodos personalizados
     */

    // Obtener tarifa por tipo de vehículo
    public function obtenerTarifa(string $tipoVehiculo): float
    {
        $tarifa = $this->tarifas()->where('tipoVehiculo', $tipoVehiculo)->first();
        return $tarifa ? $tarifa->precioHora : 0.0;
    }

    /**
     * Validaciones en eventos de Eloquent
     */
    protected static function booted()
    {
        // Acción al eliminar un parking
        static::deleting(function ($parking) {
            // Eliminar las plantas relacionadas
            $parking->plantas()->delete();
        });
    }

    // Calcular capacidad total sumando las capacidades máximas de las plantas
    public function getCapacidadTotalAttribute()
    {
        return $this->plantas->sum('capacidadMaximaPlanta');
    }

    // Calcular plazas disponibles
    public function getPlazasDisponiblesAttribute()
    {
        return $this->plantas->sum(function ($planta) {
            return $planta->plazas()
                ->where('estadoPlaza', 'libre')
                ->count();
        });
    }

    // Calcular plazas ocupadas
    public function getPlazasOcupadasAttribute()
    {
        return $this->plantas->sum(function ($planta) {
            return $planta->plazas()
                ->where('estadoPlaza', 'ocupada')
                ->count();
        });
    }

    // Calcular plazas cerradas
    public function getPlazasCerradasAttribute()
    {
        return $this->plantas->sum(function ($planta) {
            return $planta->plazas()
                ->where('estadoPlaza', 'cerrada')
                ->count();
        });
    }
}
