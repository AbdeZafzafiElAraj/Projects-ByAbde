<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Parking;

class WelcomeController extends Controller
{
    public function index()
    {
        // Recupera todos los parkings de la base de datos
        $parkings = Parking::all();

        // Envía los parkings a la vista
        return view('welcome', compact('parkings'));
    }
}
