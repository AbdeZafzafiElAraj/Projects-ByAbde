<?php

namespace App\Http\Controllers;

use App\Models\Planta;
use App\Models\Parking;
use Illuminate\Http\Request;

class PlantaController extends Controller
{
    // Mostrar listado de plantas
    public function index(Request $request)
    {
        // Obtener todos los parkings para el selector
        $parkings = Parking::all();
        
        // Consulta base
        $query = Planta::with(['parking', 'plazas']);
        
        // Aplicar filtro por parking si se seleccionó uno
        if ($request->filled('parking')) {
            $query->where('idParkingPlanta', $request->parking);
        }
        
        // Aplicar filtro por estado si se seleccionó uno
        if ($request->filled('estado')) {
            $query->where('estadoPlanta', $request->estado);
        }
        
        // Obtener plantas paginadas con el número de elementos seleccionado
        $perPage = $request->input('per_page', 10);
        $plantas = $query->paginate($perPage)->withQueryString();
        
        // Calcular ocupación media
        $ocupacionMedia = 0;
        $allPlantas = Planta::with('plazas')->get();
        
        if ($allPlantas->count() > 0) {
            $totalPlazas = 0;
            $plazasOcupadas = 0;
            
            foreach ($allPlantas as $planta) {
                $totalPlazas += $planta->capacidadMaximaPlanta;
                $plazasOcupadas += $planta->plazas->where('estadoPlaza', 'ocupada')->count();
            }
            
            if ($totalPlazas > 0) {
                $ocupacionMedia = ($plazasOcupadas / $totalPlazas) * 100;
            }
        }

        return view('plantas.index', compact('plantas', 'parkings', 'ocupacionMedia'));
    }

    // Mostrar el formulario para crear una nueva planta
    public function create()
    {
        $parkings = Parking::all();
        return view('plantas.create', compact('parkings'));
    }

    // Guardar una nueva planta en la base de datos
    public function store(Request $request)
    {
        $request->validate([
            'nombrePlanta' => 'required|string|max:255',
            'capacidadMaximaPlanta' => 'required|integer|min:1',
            'estadoPlanta' => 'required|in:abierta,cerrada',
            'latitudPlanta' => 'nullable|numeric',
            'longitudPlanta' => 'nullable|numeric',
            'idParkingPlanta' => 'required|exists:parkings,idParking',
        ]);

        // Crear la planta
        $planta = Planta::create($request->all());

        // Configuración del layout de plazas
        $plazaAncho = 40;  // Ancho de cada plaza
        $plazaAlto = 80;   // Alto de cada plaza
        $espaciado = 2;    // Espacio entre plazas
        $plazasPorFila = 10; // Número de plazas por fila

        $plazas = [];
        for ($i = 0; $i < $request->capacidadMaximaPlanta; $i++) {
            // Calcular la posición en la cuadrícula
            $fila = floor($i / $plazasPorFila);
            $columna = $i % $plazasPorFila;

            // Calcular las coordenadas reales
            $x = $columna * ($plazaAncho + $espaciado);
            $y = $fila * ($plazaAlto + $espaciado);

            $plazas[] = [
                'numeroPlaza' => $i + 1,
                'estadoPlaza' => $planta->estadoPlanta === 'abierta' ? 'libre' : 'cerrada',
                // Coordenadas del rectángulo
                'x1' => $x,
                'y1' => $y,
                'x2' => $x + $plazaAncho,
                'y2' => $y,
                'x3' => $x + $plazaAncho,
                'y3' => $y + $plazaAlto,
                'x4' => $x,
                'y4' => $y + $plazaAlto,
                'idPlantaPlaza' => $planta->idPlanta,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Insertar todas las plazas de una vez
        \DB::table('plazas')->insert($plazas);

        return redirect()->route('plantas.index')->with('success', 'Planta creada con éxito y plazas generadas automáticamente.');
    }

    // Mostrar detalles de una planta específica
    public function show(Request $request, $id)
    {
        $planta = Planta::findOrFail($id);
        
        // Consulta base para las plazas
        $query = $planta->plazas();
        
        // Filtrar por estado si se especifica
        if ($request->filled('estado')) {
            $query->where('estadoPlaza', $request->estado);
        }
        
        // Número de elementos por página (por defecto 12)
        $perPage = $request->input('per_page', 12);
        
        // Obtener plazas filtradas y paginadas
        $plazasFiltradas = $query->orderBy('numeroPlaza')
                                ->paginate($perPage)
                                ->withQueryString();
        
        return view('plantas.show', compact('planta', 'plazasFiltradas'));
    }

    // Mostrar el formulario para editar una planta
    public function edit($id)
    {
        $planta = Planta::findOrFail($id);
        $parkings = Parking::all();
        return view('plantas.edit', compact('planta', 'parkings'));
    }

    // Actualizar los datos de una planta en la base de datos
    public function update(Request $request, $id)
    {
        $planta = Planta::findOrFail($id);
        
        $request->validate([
            'nombrePlanta' => 'required|string|max:255',
            'capacidadMaximaPlanta' => 'required|integer|min:1',
            'estadoPlanta' => 'required|in:abierta,cerrada',
            'latitudPlanta' => 'nullable|numeric',
            'longitudPlanta' => 'nullable|numeric',
        ]);

        // Verificar que la nueva capacidad máxima no sea menor que el número actual de plazas
        if ($request->capacidadMaximaPlanta < $planta->capacidadActual) {
            return back()->withErrors(['capacidadMaximaPlanta' => 'La capacidad máxima no puede ser menor que el número actual de plazas']);
        }

        // Actualizar solo los campos permitidos, manteniendo el parking original
        $planta->update([
            'nombrePlanta' => $request->nombrePlanta,
            'capacidadMaximaPlanta' => $request->capacidadMaximaPlanta,
            'estadoPlanta' => $request->estadoPlanta,
            'latitudPlanta' => $request->latitudPlanta,
            'longitudPlanta' => $request->longitudPlanta,
        ]);

        return redirect()->route('plantas.index')->with('success', 'Planta actualizada exitosamente.');
    }

    // Eliminar una planta de la base de datos
    public function destroy($id)
    {
        $planta = Planta::findOrFail($id);
        $planta->delete();

        return redirect()->route('plantas.index')->with('success', 'Planta eliminada exitosamente.');
    }

    public function getPlazas(Planta $planta)
    {
        try {
            $plazas = $planta->plazas()
                ->select('idPlaza', 'numeroPlaza', 'estadoPlaza')
                ->get()
                ->map(function($plaza) {
                    return [
                        'id' => $plaza->idPlaza,
                        'numeroPlaza' => $plaza->numeroPlaza,
                        'estadoPlaza' => $plaza->estadoPlaza
                    ];
                });

            return response()->json($plazas);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al obtener las plazas'], 500);
        }
    }
}
