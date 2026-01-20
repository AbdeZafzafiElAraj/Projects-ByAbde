<?php

use App\Http\Controllers\ParkingController;
use App\Http\Controllers\PlantaController;
use App\Http\Controllers\PlazaController;
use App\Http\Controllers\TarifaController;
use App\Http\Controllers\RegistroAccesoController;
use App\Http\Controllers\ProfileController;

// Quita el middleware auth:sanctum y usa web para mantener la sesión
Route::middleware('web')->group(function () {
    Route::get('/plantas/{planta}/plazas', [PlazaController::class, 'getPlazas']);
    Route::post('/plazas/{plaza}/aparcar', [PlazaController::class, 'aparcar']);
    Route::post('/plazas/{plaza}/desaparcar', [PlazaController::class, 'desaparcar']);
    Route::post('/plazas/{plaza}/bloquear', [PlazaController::class, 'bloquear']);
    Route::post('/plazas/{plaza}/desbloquear', [PlazaController::class, 'desbloquear']);
}); 