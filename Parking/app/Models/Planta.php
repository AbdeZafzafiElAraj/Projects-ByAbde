<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Planta extends Model
{
    use HasFactory;

    protected $table = 'plantas';
    protected $primaryKey = 'idPlanta';

    protected $fillable = [
        'nombrePlanta',
        'capacidadMaximaPlanta',
        'estadoPlanta',
        'latitudPlanta',
        'longitudPlanta',
        'idParkingPlanta',
    ];

    protected $appends = ['plazasDisponibles'];

    // Relación muchos a uno con Parkings
    public function parking()
    {
        return $this->belongsTo(Parking::class, 'idParkingPlanta', 'idParking');
    }

    // Relación uno a muchos con Plazas
    public function plazas()
    {
        return $this->hasMany(Plaza::class, 'idPlantaPlaza', 'idPlanta');
    }

    // Atributo calculado para plazas disponibles
    public function getPlazasDisponiblesAttribute()
    {
        return $this->plazas()
            ->where('estadoPlaza', 'libre')
            ->count();
    }

    // Atributo calculado para el total de plazas
    public function getCapacidadActualAttribute()
    {
        return $this->plazas()->count();
    }

    // Método para verificar si se pueden añadir más plazas
    public function puedeAnadirPlazas($cantidad = 1)
    {
        return ($this->capacidadActual + $cantidad) <= $this->capacidadMaximaPlanta;
    }

    protected static function booted()
    {
        // Cuando se actualiza una planta
        static::updating(function ($planta) {
            // Si el estado de la planta está cambiando
            if ($planta->isDirty('estadoPlanta')) {
                if ($planta->estadoPlanta === 'cerrada') {
                    // Cuando la planta se cierra, todas las plazas pasan a estado 'cerrada'
                    $planta->plazas()->update(['estadoPlaza' => 'cerrada']);
                } else if ($planta->estadoPlanta === 'abierta') {
                    // Cuando la planta se abre, todas las plazas pasan a estado 'libre'
                    $planta->plazas()->update(['estadoPlaza' => 'libre']);
                }
            }
        });
    }
}
