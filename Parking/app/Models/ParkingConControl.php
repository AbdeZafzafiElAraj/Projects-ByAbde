<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ParkingConControl extends Model
{
    use HasFactory;

    protected $table = 'parkings_con_control';
    // La clave primaria será 'idParkingCC', que también actúa como clave foránea
    protected $primaryKey = 'idParkingCC';
    // No es autoincrementable porque se usa como clave foránea
    public $incrementing = false;
    // Para que coincida con el tipo de datos de idParking en la tabla Parking
    protected $keyType = 'unsignedBigInteger';

    public function parking()
    {
        return $this->belongsTo(Parking::class, 'idParkingCC');
    }

    public function asignarPlaza(string $matricula): void
    {
    if ($this->contadorPlazasLibres <= 0) {
        throw new \Exception('No hay plazas libres disponibles.');
    }

    $this->matricula = $matricula;
    $this->contadorPlazasLibres--;
    $this->save();
}

public function liberarPlaza(string $matricula): void
{
    $this->matricula = null;
    $this->contadorPlazasLibres++;
    $this->save();
}

}
