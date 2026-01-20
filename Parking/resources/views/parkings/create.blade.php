<x-app-layout>

<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden">
            <div class="p-6">
                <div class="flex items-center mb-6">
                    <svg class="w-8 h-8 text-blue-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Crear Nuevo Parking</h1>
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

                <form action="{{ route('parkings.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Información Básica -->
                        <div class="space-y-6">
                            <div>
                                <label for="nombreParking" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Nombre del Parking
                                </label>
                                <input type="text" name="nombreParking" id="nombreParking" 
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600"
                                       value="{{ old('nombreParking') }}" required>
                            </div>

                            <div>
                                <label for="direccionParking" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Dirección
                                </label>
                                <input type="text" name="direccionParking" id="direccionParking" 
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600"
                                       value="{{ old('direccionParking') }}" required>
                            </div>

                            <div>
                                <label for="ciudadParking" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Ciudad
                                </label>
                                <input type="text" name="ciudadParking" id="ciudadParking" 
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600"
                                       value="{{ old('ciudadParking') }}" required>
                            </div>
                        </div>

                        <!-- Ubicación y Horarios -->
                        <div class="space-y-6">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="latitudParking" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Latitud
                                    </label>
                                    <input type="number" step="any" name="latitudParking" id="latitudParking" 
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600"
                                           value="{{ old('latitudParking') }}" required>
                                </div>
                                <div>
                                    <label for="longitudParking" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Longitud
                                    </label>
                                    <input type="number" step="any" name="longitudParking" id="longitudParking" 
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600"
                                           value="{{ old('longitudParking') }}" required>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="horaAperturaParking" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Hora de Apertura
                                    </label>
                                    <input type="time" name="horaAperturaParking" id="horaAperturaParking" 
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600"
                                           value="{{ old('horaAperturaParking') }}" required>
                                </div>
                                <div>
                                    <label for="horaCierreParking" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Hora de Cierre
                                    </label>
                                    <input type="time" name="horaCierreParking" id="horaCierreParking" 
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600"
                                           value="{{ old('horaCierreParking') }}" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Mapa para seleccionar ubicación -->
                    <h2 class="text-lg font-bold mb-4 text-gray-900 dark:text-white">Seleccionar Ubicación en el Mapa</h2>
                    <div id="map" class="h-[400px] w-full rounded-lg"></div>

                    <!-- Después de la sección de horarios y antes del tipo de parking -->
                    <div class="space-y-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Configuración de Plantas</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="numPlantas" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Número de Plantas
                                </label>
                                <input type="number" 
                                       name="numPlantas" 
                                       id="numPlantas" 
                                       min="1"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600"
                                       value="{{ old('numPlantas', 1) }}" 
                                       required>
                            </div>
                        </div>

                        <div id="plantasContainer" class="space-y-4">
                            <div class="planta-input">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Plazas para Planta 1
                                </label>
                                <input type="number" 
                                       name="plazasPorPlanta[]" 
                                       min="1"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600"
                                       value="{{ old('plazasPorPlanta.0') }}" 
                                       required>
                            </div>
                        </div>

                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Capacidad total: <span id="capacidadTotal">0</span> plazas
                        </p>
                    </div>

                    <!-- Tipo de Parking -->
                    <div class="space-y-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Tipo de Parking</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="relative flex items-start">
                                <div class="flex items-center h-5">
                                    <input id="conControl" 
                                           name="tipoParking" 
                                           type="radio" 
                                           value="conControl"
                                           {{ old('tipoParking') === 'conControl' ? 'checked' : '' }}
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
                                           {{ old('tipoParking') === 'sinControl' ? 'checked' : '' }}
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

                    <!-- Opciones de Pago -->
                    <div class="space-y-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Opciones de Pago</h3>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            @php
                                $opcionesPago = old('opcionesPagoParking', []);
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

                    <!-- Imagen -->
                    <div>
                        <label for="imagenParking" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Imagen del Parking
                        </label>
                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                            <div class="space-y-1 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" 
                                          stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                <div class="flex text-sm text-gray-600">
                                    <label for="file-upload" class="relative cursor-pointer rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                        <span>Subir imagen</span>
                                        <input id="file-upload" name="imagenParking" type="file" class="sr-only" accept="image/*">
                                    </label>
                                    <p class="pl-1">o arrastrar y soltar</p>
                                </div>
                                <p class="text-xs text-gray-500">PNG, JPG, GIF hasta 10MB</p>
                            </div>
                        </div>
                    </div>

                    <!-- Botones de acción -->
                    <div class="flex justify-end space-x-3 pt-6">
                        <a href="{{ route('parkings.index') }}" 
                           class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-gray-700 bg-gray-100 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                            Cancelar
                        </a>
                        <button type="submit" 
                                class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Crear Parking
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<script>
    // Script para el mapa
    const map = L.map('map').setView([40.416775, -3.703790], 6);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    let marker;

    map.on('click', function(e) {
        const lat = e.latlng.lat;
        const lng = e.latlng.lng;
        
        document.getElementById('latitudParking').value = lat;
        document.getElementById('longitudParking').value = lng;

        if (marker) {
            marker.setLatLng(e.latlng);
        } else {
            marker = L.marker(e.latlng).addTo(map);
        }
    });

    // Preview de imagen
    document.getElementById('file-upload').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.createElement('img');
                preview.src = e.target.result;
                preview.className = 'mt-2 rounded-lg max-h-40 mx-auto';
                const container = document.querySelector('.space-y-1');
                const existingPreview = container.querySelector('img');
                if (existingPreview) {
                    existingPreview.remove();
                }
                container.appendChild(preview);
            }
            reader.readAsDataURL(file);
        }
    });

    // Función para generar campos de plazas por planta
    function actualizarCamposPlazas() {
        const numPlantas = parseInt(document.getElementById('numPlantas').value) || 1;
        const container = document.getElementById('plantasContainer');
        const plantasActuales = container.getElementsByClassName('planta-input').length;

        // Si hay que añadir más campos
        if (numPlantas > plantasActuales) {
            for (let i = plantasActuales + 1; i <= numPlantas; i++) {
                const div = document.createElement('div');
                div.className = 'planta-input';
                div.innerHTML = `
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Plazas para Planta ${i}
                    </label>
                    <input type="number" 
                           name="plazasPorPlanta[]" 
                           min="1"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600"
                           required>
                `;
                container.appendChild(div);
            }
        }
        // Si hay que eliminar campos
        else if (numPlantas < plantasActuales) {
            for (let i = plantasActuales; i > numPlantas; i--) {
                container.removeChild(container.lastChild);
            }
        }
    }

    // Modificar la función de calcular capacidad Maxima
    function calcularCapacidadTotal() {
        const inputs = document.getElementsByName('plazasPorPlanta[]');
        let total = 0;
        inputs.forEach(input => {
            total += parseInt(input.value) || 0;
        });
        document.getElementById('capacidadTotal').textContent = total;
    }

    // Event listeners
    document.getElementById('numPlantas').addEventListener('input', actualizarCamposPlazas);
    document.getElementById('plantasContainer').addEventListener('input', calcularCapacidadTotal);

    // Inicializar los campos
    actualizarCamposPlazas();
    calcularCapacidadTotal();
</script>
</x-app-layout>