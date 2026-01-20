<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ParkingRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Asegúrate de que el usuario esté autorizado
    }

    public function rules()
    {
        return [
            'nombreParking' => 'required|string|max:255',
            'direccionParking' => 'required|string',
            'ciudadParking' => 'required|string',
            'capacidadParking' => 'required|integer|min:1',
            'plazasDisponibles' => 'nullable|integer|max:capacidadParking',
            'horaAperturaParking' => 'required|date_format:H:i',
            'horaCierreParking' => 'required|date_format:H:i|after:horaAperturaParking',
            'opcionesPagoParking' => 'required|in:tarjeta,efectivo',
            'tipoParking' => 'required|in:conControl,sinControl',
        ];
    }
}
