<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Plaza extends Model
{
    use HasFactory;

    const ESTADO_LIBRE = 'libre';
    const ESTADO_OCUPADA = 'ocupada';
    const ESTADO_MANTENIMIENTO = 'mantenimiento';

    protected $table = 'plazas';
    protected $primaryKey = 'idPlaza';

    protected $fillable = [
        'numeroPlaza',
        'estadoPlaza',
        'matricula',
        'tipoVehiculo',
        'idPlantaPlaza',
        'x1',
        'y1',
        'x2',
        'y2',
        'x3',
        'y3',
        'x4',
        'y4'
    ];


    protected $casts = [
        'estadoPlaza' => 'string',
        'tipoVehiculo' => 'string'
    ];

    // Eventos del modelo
    protected static function booted()
    {
        // Antes de crear una nueva plaza
        static::creating(function ($plaza) {
            $plaza->asignarNumeroPlaza();
        });
    }

    // Relación muchos a uno con Planta
    public function planta()
    {
        return $this->belongsTo(Planta::class, 'idPlantaPlaza', 'idPlanta');
    }

    // Relación uno a muchos con RegistroAccesos
    public function registroAccesos()
    {
        return $this->hasMany(RegistroAcceso::class, 'idPlazaAsignadaAcceso', 'idPlaza');
    }

    // Método para asignar número de plaza
    private function asignarNumeroPlaza()
    {
        if (!$this->numeroPlaza) {
            $planta = Planta::findOrFail($this->idPlantaPlaza);
            $ultimaPlaza = Plaza::whereHas('planta', function($query) use ($planta) {
                $query->where('idParkingPlanta', $planta->idParkingPlanta);
            })->max('numeroPlaza');

            $this->numeroPlaza = ($ultimaPlaza ?? 0) + 1;
        }
    }
}
