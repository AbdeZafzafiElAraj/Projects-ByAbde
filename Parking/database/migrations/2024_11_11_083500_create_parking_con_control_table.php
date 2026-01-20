<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('parkings_con_control', function (Blueprint $table) {
            $table->id('idParkingCC'); // Esto asegura que la clave foránea también sea la clave primaria
            $table->foreign('idParkingCC')->references('idParking')->on('parkings')->onDelete('cascade'); // Relación con Parking
            //$table->foreignId('idParking')->constrained('parkings', 'idParking')->cascadeOnDelete();
            
            $table->integer('contadorPlazasLibres');
            $table->foreignId('idPlazaAsignada')->nullable()->constrained('plazas', 'idPlaza'); // Assuming you have a 'plazas' table
            //$table->string('matricula');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parkings_con_control');
    }
};
