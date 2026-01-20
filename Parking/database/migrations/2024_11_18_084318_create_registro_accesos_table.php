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
        Schema::create('registro_accesos', function (Blueprint $table) {
            $table->id('idRegistroAcceso');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->unsignedBigInteger('idParkingAcceso');
            $table->unsignedBigInteger('idPlazaAsignadaAcceso');
            $table->string('matricula', 7);
            $table->enum('tipoVehiculo', ['coche', 'moto', 'furgoneta']);
            $table->string('metodoPago');
            $table->datetime('horaEntrada');
            $table->datetime('horaSalida')->nullable(); 
            $table->enum('estado', ['activo', 'finalizado'])->default('activo');
            $table->decimal('tarifaAplicada', 8, 2)->nullable();
            $table->timestamps();

            $table->foreign('idParkingAcceso')
                  ->references('idParking')
                  ->on('parkings')
                  ->onDelete('cascade');

            $table->foreign('idPlazaAsignadaAcceso')
                  ->references('idPlaza')
                  ->on('plazas')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registro_accesos');
    }
};