<?php

namespace App\Http\Controllers;

use App\Models\Tarifa;
use App\Models\Parking;
use Illuminate\Http\Request;

class TarifaController extends Controller
{
    public function index()
    {
        // Obtener todas las tarifas
        $tarifas = Tarifa::all();
        return view('tarifas.index', compact('tarifas'));
    }

    public function create()
    {
        // Obtener todos los parkings para asignar a la tarifa
        $parkings = Parking::all();
        return view('tarifas.create', compact('parkings'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'idParkingTarifa' => 'required',
            'tipoVehiculo' => 'required',
            'precio' => 'required|numeric|min:0', // Cambiado de precioHora a precio
        ]);

        // Crear nueva tarifa
        Tarifa::create($request->all());

        return redirect()->route('tarifas.index')->with('success', 'Tarifa creada correctamente');
    }

    public function show($id)
    {
        $tarifa = Tarifa::findOrFail($id);
        return view('tarifas.show', compact('tarifa'));
    }

    public function edit(Tarifa $tarifa)
    {
        $parkings = Parking::all();
        return view('tarifas.edit', compact('tarifa', 'parkings'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'idParkingTarifa' => 'required',
            'tipoVehiculo' => 'required',
            'precio' => 'required|numeric|min:0', // Cambiado de precioHora a precio
        ]);

        $tarifa = Tarifa::findOrFail($id);
        $tarifa->update($request->all());

        return redirect()->route('tarifas.index')->with('success', 'Tarifa actualizada correctamente');
    }

    public function destroy($id)
    {
        $tarifa = Tarifa::findOrFail($id);
        $tarifa->delete();

        return redirect()->route('tarifas.index')->with('success', 'Tarifa eliminada correctamente');
    }
}
