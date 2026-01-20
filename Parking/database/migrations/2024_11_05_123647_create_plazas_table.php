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
        Schema::create('plazas', function (Blueprint $table) {
            $table->id('idPlaza');
            $table->integer('numeroPlaza');
            $table->enum('tipoVehiculo', ['coche', 'furgoneta', 'moto'])->nullable();
            $table->string('matricula')->nullable(); // Puede ser sin control con indentificacion de matricula o sin control y sin identificacion de matricula
            $table->enum('estadoPlaza', ['libre', 'ocupada', 'cerrada', 'mantenimiento'])->default('libre');
            $table->foreignId('idPlantaPlaza')->constrained('plantas', 'idPlanta')->cascadeOnDelete(); // Relación con la tabla plantas
            // Coordenadas de la plaza
            $table->double('x1');
            $table->double('y1');
            $table->double('x2');
            $table->double('y2');
            $table->double('x3');
            $table->double('y3');
            $table->double('x4');
            $table->double('y4');
            $table->timestamps();

            // Añadir índice único compuesto para numeroPlaza y parking
            $table->unique(['numeroPlaza', 'idPlantaPlaza']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plazas');
    }
};
