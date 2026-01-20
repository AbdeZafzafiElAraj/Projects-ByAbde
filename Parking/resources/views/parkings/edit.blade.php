<x-app-layout>


<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden">
            <div class="p-6">
                <!-- Encabezado con imagen de fondo -->
                <div class="relative h-80 -mx-6 -mt-6 mb-8">
                    <div class="absolute inset-0">
                        @php
                            $imagePath = $parking->imagenParking;
                            $imageUrl = Storage::disk('public')->exists($imagePath) 
                                ? Storage::url($imagePath) 
                                : asset('images/'.$imagePath);
                        @endphp
                        <img src="{{ $imageUrl }}" 
                             alt="{{ $parking->nombreParking }}" 
                             class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/40 to-transparent"></div>
                    </div>
                    <div class="absolute bottom-0 left-0 right-0 p-6">
                        <h1 class="text-3xl font-bold text-white">Editar Parking: {{ $parking->nombreParking }}</h1>
                    </div>
                </div>

                @if ($errors->any())
                <div class="mb-6 bg-red-50 dark:bg-red-900/50 border-l-4 border-red-500 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800 dark:text-red-200">Hay errores en el formulario:</h3>
                            <ul class="mt-2 list-disc list-inside text-sm text-red-700 dark:text-red-300">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
                @endif

                <form action="{{ route('parkings.update', $parking->idParking) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Información Básica -->
                        <div class="space-y-6">
                            <div>
                                <label for="nombreParking" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Nombre del Parking
                                </label>
                                <input type="text" name="nombreParking" id="nombreParking" 
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600"
                                       value="{{ old('nombreParking', $parking->nombreParking) }}" required>
                            </div>

                            <div>
                                <label for="direccionParking" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Dirección
                                </label>
                                <input type="text" name="direccionParking" id="direccionParking" 
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600"
                                       value="{{ old('direccionParking', $parking->direccionParking) }}" required>
                            </div>

                            <div>
                                <label for="ciudadParking" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Ciudad
                                </label>
                                <input type="text" name="ciudadParking" id="ciudadParking" 
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600"
                                       value="{{ old('ciudadParking', $parking->ciudadParking) }}" required>
                            </div>
                        

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Capacidad Total
                                </label>
                                <div class="mt-1 p-3 bg-gray-50 dark:bg-gray-700 rounded-md">
                                    <p class="text-lg font-semibold text-gray-900 dark:text-white">
                                        {{ $parking->capacidadTotal }} plazas
                                    </p>
                                    
                                    <!-- Desglose por plantas -->
                                    <div class="mt-3 space-y-2">
                                        <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300">Desglose por plantas:</h4>
                                        @foreach($parking->plantas as $planta)
                                        <div class="flex justify-between items-center text-sm">
                                            <span class="text-gray-600 dark:text-gray-400">{{ $planta->nombrePlanta }}</span>
                                            <span class="font-medium text-gray-900 dark:text-white">{{ $planta->capacidadTotal }} plazas</span>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Horarios y Capacidad -->
                        <div class="space-y-6">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="horaAperturaParking" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Hora de Apertura
                                    </label>
                                    <input type="time" name="horaAperturaParking" id="horaAperturaParking" 
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600"
                                           value="{{ old('horaAperturaParking', $parking->horaAperturaParking) }}" required>
                                </div>
                                <div>
                                    <label for="horaCierreParking" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Hora de Cierre
                                    </label>
                                    <input type="time" name="horaCierreParking" id="horaCierreParking" 
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600"
                                           value="{{ old('horaCierreParking', $parking->horaCierreParking) }}" required>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="latitudParking" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Latitud
                                    </label>
                                    <input type="number" step="any" name="latitudParking" id="latitudParking" 
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600"
                                           value="{{ old('latitudParking', $parking->latitudParking) }}" required>
                                </div>
                                <div>
                                    <label for="longitudParking" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Longitud
                                    </label>
                                    <input type="number" step="any" name="longitudParking" id="longitudParking" 
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600"
                                           value="{{ old('longitudParking', $parking->longitudParking) }}" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Opciones de Pago -->
                    <div class="space-y-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Opciones de Pago</h3>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            @php
                                $opcionesPago = json_decode($parking->opcionesPagoParking) ?? [];
                            @endphp
                            
                            <div class="relative flex items-start">
                                <div class="flex items-center h-5">
                                    <input id="efectivo" 
                                           name="opcionesPagoParking[]" 
                                           type="checkbox" 
                                           value="efectivo"
                                           {{ in_array('efectivo', $opcionesPago) ? 'checked' : '' }}
                                           class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="efectivo" class="font-medium text-gray-700 dark:text-gray-300">Efectivo</label>
                                </div>
                            </div>

                            <div class="relative flex items-start">
                                <div class="flex items-center h-5">
                                    <input id="tarjeta" 
                                           name="opcionesPagoParking[]" 
                                           type="checkbox" 
                                           value="tarjeta"
                                           {{ in_array('tarjeta', $opcionesPago) ? 'checked' : '' }}
                                           class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="tarjeta" class="font-medium text-gray-700 dark:text-gray-300">Tarjeta</label>
                                </div>
                            </div>

                            <div class="relative flex items-start">
                                <div class="flex items-center h-5">
                                    <input id="movil" 
                                           name="opcionesPagoParking[]" 
                                           type="checkbox" 
                                           value="movil"
                                           {{ in_array('movil', $opcionesPago) ? 'checked' : '' }}
                                           class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="movil" class="font-medium text-gray-700 dark:text-gray-300">Pago Móvil</label>
                                </div>
                            </div>

                            <div class="relative flex items-start">
                                <div class="flex items-center h-5">
                                    <input id="gratuito" 
                                           name="opcionesPagoParking[]" 
                                           type="checkbox" 
                                           value="gratuito"
                                           {{ in_array('gratuito', $opcionesPago) ? 'checked' : '' }}
                                           class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="gratuito" class="font-medium text-gray-700 dark:text-gray-300">Gratuito</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Después de las opciones de pago y antes de la imagen -->
                    <div class="space-y-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Tipo de Parking</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="relative flex items-start">
                                <div class="flex items-center h-5">
                                    <input id="conControl" 
                                           name="tipoParking" 
                                           type="radio" 
                                           value="conControl"
                                           {{ $parking->tipoParking === 'conControl' ? 'checked' : '' }}
                                           class="h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                                </div>
                                <div class="ml-3">
                                    <label for="conControl" class="font-medium text-gray-700 dark:text-gray-300">
                                        Con Control de Acceso
                                    </label>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                    Este parking asigna plazas de forma automática
                                    </p>
                                </div>
                            </div>

                            <div class="relative flex items-start">
                                <div class="flex items-center h-5">
                                    <input id="sinControl" 
                                           name="tipoParking" 
                                           type="radio" 
                                           value="sinControl"
                                           {{ $parking->tipoParking === 'sinControl' ? 'checked' : '' }}
                                           class="h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                                </div>
                                <div class="ml-3">
                                    <label for="sinControl" class="font-medium text-gray-700 dark:text-gray-300">
                                        Sin Control de Acceso
                                    </label>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                    Este parking asigna plazas de forma manual
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Imagen -->
                    <div class="space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Imagen Actual
                            </label>
                            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md relative">
                                <div class="space-y-1 text-center">
                                    <img src="{{ $imageUrl }}" 
                                         alt="Imagen actual del parking" 
                                         class="mx-auto h-32 w-auto object-cover rounded-lg mb-4">
                                    <div class="flex text-sm text-gray-600">
                                        <label for="imagenParking" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                            <span>Cambiar Imagen</span>
                                            <input id="imagenParking" name="imagenParking" type="file" class="sr-only" accept="image/*">
                                        </label>
                                    </div>
                                    <p class="text-xs text-gray-500">PNG, JPG, GIF hasta 10MB</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Botones de acción -->
                    <div class="flex justify-end space-x-3 pt-6">
                        <a href="{{ route('parkings.show', $parking->idParking) }}" 
                           class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-gray-700 bg-gray-100 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                            Cancelar
                        </a>
                        <button type="submit" 
                                class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Mapa para editar ubicación -->
<div class="container mx-auto px-4 mt-8">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden">
            <div class="p-6">
                <h2 class="text-lg font-bold mb-4 text-gray-900 dark:text-white">Editar Ubicación en el Mapa</h2>
                <div id="map" class="h-[400px] w-full rounded-lg"></div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    .custom-popup .leaflet-popup-content-wrapper {
        background: rgba(255, 255, 255, 0.95);
        border-radius: 0.5rem;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    .custom-popup .leaflet-popup-content {
        margin: 0;
        padding: 1rem;
    }
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const map = L.map('map').setView([{{ $parking->latitudParking }}, {{ $parking->longitudParking }}], 15);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        const icon = L.divIcon({
            className: 'custom-div-icon',
            html: `<div class="bg-blue-500 w-8 h-8 rounded-full flex items-center justify-center text-white font-bold shadow-lg transform hover:scale-110 transition-transform duration-200">
                    <span class="text-sm">P</span>
                  </div>`,
            iconSize: [32, 32],
            iconAnchor: [16, 16],
            popupAnchor: [0, -16]
        });

        let marker = L.marker([{{ $parking->latitudParking }}, {{ $parking->longitudParking }}], {
            icon: icon,
            draggable: true
        }).addTo(map);

        const popupContent = `
            <div class="min-w-[200px]">
                <div class="bg-gradient-to-r from-blue-500 to-blue-600 text-white p-3 -mt-1 -mx-1 rounded-t-lg">
                    <h3 class="font-bold text-lg">${'{{ $parking->nombreParking }}'}</h3>
                    <p class="text-sm text-blue-100">${'{{ $parking->direccionParking }}'}</p>
                </div>
                <div class="p-3">
                    <div class="text-sm text-gray-600">
                        <p>Horario: ${'{{ $parking->horaAperturaParking }}'} - ${'{{ $parking->horaCierreParking }}'}</p>
                        <p class="mt-1">Capacidad Total: ${'{{ $parking->capacidadTotal }}'} plazas</p>
                    </div>
                </div>
            </div>
        `;

        marker.bindPopup(popupContent, {
            className: 'custom-popup'
        }).openPopup();

        marker.on('dragend', function(e) {
            document.getElementById('latitudParking').value = marker.getLatLng().lat.toFixed(6);
            document.getElementById('longitudParking').value = marker.getLatLng().lng.toFixed(6);
        });

        // Preview de imagen
        document.getElementById('imagenParking').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.querySelector('img[alt="Imagen actual del parking"]');
                    preview.src = e.target.result;
                }
                reader.readAsDataURL(file);
            }
        });
    });
</script>
@endpush

</x-app-layout>