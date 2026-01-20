<?php

namespace App\Http\Controllers;

use App\Models\Parking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;

class DashboardController extends Controller
{
    // Método para mostrar la vista del dashboard
    public function index()
    {
        $parkings = Parking::paginate(10);
        $images = [];
        
        // Obtener imágenes de public/images
        $publicImages = File::files(public_path('images'));
        foreach ($publicImages as $image) {
            if (in_array($image->getExtension(), ['jpg', 'jpeg', 'png', 'gif'])) {
                $images[] = [
                    'path' => 'images/' . $image->getFilename(),
                    'name' => $image->getFilename(),
                    'type' => 'public'
                ];
            }
        }

        // Obtener imágenes de storage/app/public/images
        $storageImages = Storage::disk('public')->files('images'); // Cambiado a 'images'
        foreach ($storageImages as $image) {
            $images[] = [
                'path' => 'storage/' . $image, // Cambiado para que la ruta sea correcta
                'name' => basename($image),
                'type' => 'storage'
            ];
        }

        return view('dashboard', compact('parkings', 'images'));
    }


    public function upload(Request $request)
    {
        try {
            if (!$request->hasFile('images')) {
                return response()->json(['error' => 'No se ha enviado ninguna imagen'], 400);
            }

            $request->validate([
                'images.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120'
            ]);

            $paths = [];
            foreach ($request->file('images') as $image) {
                // Generar nombre único
                $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                
                // Guardar la imagen
                $path = $image->storeAs('images', $filename, 'public');
                
                if (!$path) {
                    throw new \Exception('Error al guardar la imagen: ' . $filename);
                }

                Log::info("Imagen subida exitosamente", [
                    'filename' => $filename,
                    'path' => $path
                ]);

                $paths[] = [
                    'path' => $path,
                    'name' => $filename
                ];
            }

            return response()->json($paths);

        } catch (\Exception $e) {
            Log::error('Error en la subida de imágenes: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function deleteImage(Request $request)
    {
        try {
            $path = $request->input('path');
            $type = $request->input('type');
            
            if (!$path) {
                return response()->json(['error' => 'Path no proporcionado'], 400);
            }

            // Verificar si la imagen está en storage/public/images
            if ($type === 'storage') {
                if (Storage::disk('public')->exists($path)) {
                    Storage::disk('public')->delete($path);
                    return response()->json(['success' => true, 'message' => 'Imagen eliminada correctamente']);
                }
            } 
            
            // Verificar si la imagen está en public/images
            $publicPath = public_path($path);
            if (file_exists($publicPath)) {
                unlink($publicPath);
                return response()->json(['success' => true, 'message' => 'Imagen eliminada correctamente']);
            }

            // Si no se encontró la imagen en ninguna ubicación
            return response()->json(['error' => 'Imagen no encontrada'], 404);

        } catch (\Exception $e) {
            \Log::error('Error al eliminar imagen', [
                'error' => $e->getMessage(),
                'path' => $path,
                'type' => $type
            ]);
            
            return response()->json([
                'error' => 'Error al eliminar la imagen: ' . $e->getMessage()
            ], 500);
        }
    }

    public function __construct()
    {
        try {
            // Crear enlace simbólico si no existe
            if (!file_exists(public_path('storage'))) {
                app('files')->link(storage_path('app/public'), public_path('storage'));
                Log::info('Symbolic link created successfully');
            }
        } catch (\Exception $e) {
            Log::error('Error creating symbolic link: ' . $e->getMessage());
        }
    }
}