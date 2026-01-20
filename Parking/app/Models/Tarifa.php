<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tarifa extends Model
{
    use HasFactory;

    protected $table = 'tarifas';
    protected $primaryKey = 'idTarifa';

    protected $fillable = [
        'idParkingTarifa',
        'tipoVehiculo',
        'precio',
        'descripcion'
    ];

    protected $casts = [
        'precio' => 'decimal:2'
    ];
    // Relación muchos a uno con Parkings
    public function parking()
    {
        return $this->belongsTo(Parking::class, 'idParkingTarifa', 'idParking');
    }
    // Relación uno a muchos con RegistroAccesos
    public function registroAccesos()
    {
        // hasMany = Una tarifa puede estar en muchos registros de acceso
        return $this->hasMany(RegistroAcceso::class, 'idTarifaAcceso', 'idTarifa');
    }
}
