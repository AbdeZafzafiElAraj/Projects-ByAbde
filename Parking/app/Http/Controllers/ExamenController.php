<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Parking;
use App\Models\Plaza;



class ExamenController extends Controller
{
    public function llistat()
    {
        try {
            $parkings = Parking::with(['plantas.plazas'])->get();
            
            foreach ($parkings as $parking) {
                // Calcular plazas disponibles
                $plazasLibres = 0;
                $plazasTotales = 0;
                
                foreach ($parking->plantas as $planta) {
                    foreach ($planta->plazas as $plaza) {
                        $plazasTotales++;
                        if ($plaza->estadoPlaza === 'libre') {
                            $plazasLibres++;
                        }
                    }
                }

                $parking->plazasLibres = $plazasLibres;
                $parking->plazasTotales = $plazasTotales;
                
            }

            return view('examen.llistat', compact('parkings'));
        } catch (\Exception $e) {
            \Log::error('Error' . $e->getMessage());
            return back()->with('error', 'Error al cargar los parkings');
        }
    }

    }


