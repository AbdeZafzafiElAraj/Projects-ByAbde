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
        Schema::create('parkings', function (Blueprint $table) {
            $table->id('idParking');
            $table->string('nombreParking');
            $table->string('direccionParking');
            $table->string('ciudadParking');
            $table->time('horaAperturaParking');
            $table->time('horaCierreParking');
            $table->json('opcionesPagoParking');
            $table->string('imagenParking')->nullable();
            $table->double('latitudParking');
            $table->double('longitudParking');
            $table->enum('tipoParking', ['conControl', 'sinControl']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parkings');
    }
};

