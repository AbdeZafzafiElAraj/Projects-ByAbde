<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tarifas', function (Blueprint $table) {
            $table->id('idTarifa');
            $table->unsignedBigInteger('idParkingTarifa'); // Cambiado de idParking a idParkingTarifa
            $table->string('tipoVehiculo');
            $table->decimal('precio', 8, 2);
            $table->string('descripcion')->nullable();
            $table->timestamps();

            $table->foreign('idParkingTarifa')
                  ->references('idParking')
                  ->on('parkings')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('tarifas');
    }
};


