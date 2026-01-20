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
        Schema::create('parkings_sin_control', function (Blueprint $table) {
            $table->id('idParkingSC'); // Esto asegura que la clave foránea también sea la clave primaria 
            $table->foreign('idParkingSC')->references('idParking')->on('parkings')->onDelete('cascade'); // Relación con Parking
            //$table->foreignId('idParking')->constrained('parkings', 'idParking')->cascadeOnDelete();
            
            $table->integer('contadorPlazasLibres');
            //$table->string('matricula')->nullable(); // Puede ser sin control con indentificacion de matricula o sin control y sin identificacion de matricula
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parkings_sin_control');
    }
};
