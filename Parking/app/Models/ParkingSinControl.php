<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ParkingSinControl extends Model
{
    use HasFactory;

    protected $table = 'parkings_sin_control';
    // La clave primaria será 'idParkingCC', que también actúa como clave foránea
    protected $primaryKey = 'idParkingSC';
    // No es autoincrementable porque se usa como clave foránea
    public $incrementing = false;
    // Para que coincida con el tipo de datos de idParking en la tabla Parking
    protected $keyType = 'unsignedBigInteger';

    public function parking()
    {
        return $this->belongsTo(Parking::class, 'idParkingSC');
    }

    public function registrarEntrada(string $matricula): void
{
    $this->matricula = $matricula;
    $this->contadorPlazasLibres--;
    $this->save();
}

public function registrarSalida(string $matricula): void
{
    $this->matricula = null;
    $this->contadorPlazasLibres++;
    $this->save();
}

}
