<?php

use App\Http\Controllers\{
    ParkingController,
    PlantaController,
    PlazaController,
    TarifaController,
    RegistroAccesoController,
    ProfileController,
    DashboardController,
    PublicController,
    ExamenController,

};
use App\Models\Planta;
use Illuminate\Support\Facades\Route;

// Rutas Públicas
Route::get('/', [PublicController::class, 'welcome'])->name('welcome');
Route::get('/mapa-parkings', [PublicController::class, 'mapaParking'])->name('mapa-parkings');
Route::get('/disponibilidad', [PublicController::class, 'disponibilidad'])->name('public.disponibilidad');
Route::get('/parking/{parking}', [PublicController::class, 'infoParking'])->name('public.parking.info');






// APIs Públicas
Route::get('/api/parkings/{id}/plantas', function ($id) {
    return Planta::where('idParkingPlanta', $id)->get(['idPlanta', 'nombrePlanta']);
});
Route::get('/plantas/{planta}/plazas', function ($planta) {
    return \App\Models\Plaza::where('idPlantaPlaza', $planta)
        ->get(['numeroPlaza', 'tipoVehiculo', 'matricula', 'estadoPlaza', 'x1', 'y1', 'x2', 'y2', 'x3', 'y3', 'x4', 'y4']);
});

// Rutas de Autenticación
require __DIR__.'/auth.php';


// Rutas Protegidas por Autenticación
Route::middleware(['auth'])->group(function () {

    // EXAMEN
Route::get('/examen', [ExamenController::class, 'llistat'])->name('examen.llistat');


    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Perfil de Usuario
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Parkings (Solo Lectura)
    Route::get('/parkings', [ParkingController::class, 'index'])->name('parkings.index');
    


    // Esta ruta debe ir después de las rutas específicas
    Route::get('/parkings/{parking}', [ParkingController::class, 'show'])->name('parkings.show');

    // Tarifas (Solo Lectura)
    Route::get('/tarifas', [TarifaController::class, 'index'])->name('tarifas.index');
    Route::get('/tarifas/{tarifa}', [TarifaController::class, 'show'])->name('tarifas.show');

    // Registro de Accesos
    Route::get('/registro-accesos', [RegistroAccesoController::class, 'index'])->name('registro-accesos.index');
    Route::get('/registro-accesos/create/{parkingId}', [RegistroAccesoController::class, 'create'])->name('registro-accesos.create');
    Route::post('/registro-accesos', [RegistroAccesoController::class, 'store'])->name('registro-accesos.store');
    Route::get('/registro-accesos/parking/{parking}', [RegistroAccesoController::class, 'show'])->name('registro-accesos.show');
    Route::get('/registro-accesos/ticket/{registro}', [RegistroAccesoController::class, 'ticket'])->name('registro-accesos.ticket');
});



    // Rutas para Operadores y Administradores
    Route::middleware(['auth', 'role:admin,operador'])->group(function () {
        // Plantas
        Route::resource('plantas', PlantaController::class);

        // Plazas
        Route::resource('plazas', PlazaController::class);
        Route::get('/plazas', [PlazaController::class, 'index'])->name('plazas.index');
        Route::get('/plazas/{plaza}', [PlazaController::class, 'show'])->name('plazas.show');
        Route::post('/plazas/{plaza}/aparcar', [PlazaController::class, 'aparcar'])->name('plazas.aparcar');
        Route::post('/plazas/{plaza}/desaparcar', [PlazaController::class, 'desaparcar'])->name('plazas.desaparcar');
        Route::post('/plazas/{plaza}/bloquear', [PlazaController::class, 'bloquear'])->name('plazas.bloquear');
        Route::post('/plazas/{plaza}/desbloquear', [PlazaController::class, 'desbloquear'])->name('plazas.desbloquear');
        Route::post('/plazas/{plaza}/cerrar', [PlazaController::class, 'cerrar'])->name('plazas.cerrar');
        Route::post('/plazas/{plaza}/abrir', [PlazaController::class, 'abrir'])->name('plazas.abrir');

        // Tarifas 
         Route::get('/tarifas/create', [TarifaController::class, 'create'])->name('tarifas.create');
         Route::post('/tarifas', [TarifaController::class, 'store'])->name('tarifas.store');
         Route::get('/tarifas/{tarifa}/edit', [TarifaController::class, 'edit'])->name('tarifas.edit');
         Route::put('/tarifas/{tarifa}', [TarifaController::class, 'update'])->name('tarifas.update');
         Route::delete('/tarifas/{tarifa}', [TarifaController::class, 'destroy'])->name('tarifas.destroy');
    
        });


    // Rutas de administrador para parkings
    Route::middleware(['auth', 'role:admin'])->group(function () {

        // Rutas de Parking explícitas
        Route::get('/parkings/create', [ParkingController::class, 'create'])->name('parkings.create');
        Route::post('/parkings', [ParkingController::class, 'store'])->name('parkings.store');
        Route::get('/parkings/{parking}/edit', [ParkingController::class, 'edit'])->name('parkings.edit');
        Route::put('/parkings/{parking}', [ParkingController::class, 'update'])->name('parkings.update');
        Route::delete('/parkings/{parking}', [ParkingController::class, 'destroy'])->name('parkings.destroy');
        
        // Rutas para la gestión de imágenes
        Route::post('/dashboard/upload', [DashboardController::class, 'upload'])
            ->name('dashboard.upload');
            
        Route::post('/dashboard/delete-image', [DashboardController::class, 'deleteImage'])
            ->name('dashboard.deleteImage');

     
        // Rutas de Plazas explícitas
        Route::get('/plazas/create', [PlazaController::class, 'create'])->name('plazas.create');
        Route::post('/plazas', [PlazaController::class, 'store'])->name('plazas.store');
        Route::get('/plazas/{plaza}/edit', [PlazaController::class, 'edit'])->name('plazas.edit');
        Route::put('/plazas/{plaza}', [PlazaController::class, 'update'])->name('plazas.update');
        Route::delete('/plazas/{plaza}', [PlazaController::class, 'destroy'])->name('plazas.destroy');
    

    });


