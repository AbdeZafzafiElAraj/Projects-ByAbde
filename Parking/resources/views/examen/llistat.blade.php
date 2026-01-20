<x-app-layout>

            
<!-- Grid de Parkings -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    
    @foreach($parkings as $parking)
    <div class="parking-card" data-parking-id="{{ $parking->idParking }}">
        
        <div class="bg-white dark:bg-gray-700 rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-all duration-200">
            <div class="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-700 px-6 py-4">
                <h3 class="text-xl font-bold text-gray-800 dark:text-white">{{ $parking->nombreParking }}</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $parking->direccionParking }}</p>
            </div>
            
            <div class="p-6 space-y-4">
                
                <!-- Estado actual -->
                <div class="bg-gray-50 dark:bg-gray-800 p-4 rounded-lg">
                    <div>
                        @if($parking->plazasDisponibles > 0)
                            <span class="bg-green-500 text-white px-4 py-2 rounded-full text-sm font-semibold shadow-lg">
                                Disponible
                            </span>
                        @else
                            <span class="bg-red-500 text-white px-4 py-2 rounded-full text-sm font-semibold shadow-lg">
                                Completo
                            </span>
                        @endif
                    </div>
                    <p class="text-gray-700 dark:text-gray-300 font-medium mb-2">Estado actual:</p>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-lg font-bold px-3 py-1 rounded-lg {{ $parking->plazasLibres > 0 ? 'text-green-600 bg-green-100' : 'text-red-600 bg-red-100' }}">
                            {{ $parking->plazasLibres }}/{{ $parking->plazasTotales }} plazas
                        </span>
                    </div>
                    <div class="overflow-hidden h-2 mb-2 text-xs flex rounded-full bg-gray-200 dark:bg-gray-600">
                        <div style="width:{{ ($parking->plazasTotales > 0) ? ($parking->plazasLibres / $parking->plazasTotales * 100) : 0 }}%" 
                             class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center transition-all duration-500 {{ $parking->plazasLibres > 0 ? 'bg-gradient-to-r from-green-400 to-green-500' : 'bg-gradient-to-r from-red-400 to-red-500' }}">
                        </div>
                    </div>

                    <!-- Horario -->
                    <div class="flex items-center mb-4">
                        <svg class="w-5 h-5 text-gray-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span class="text-sm text-gray-600 dark:text-gray-300">
                            {{ $parking->horaAperturaParking }} - {{ $parking->horaCierreParking }}
                        </span>
                    </div>

                    <!-- Informacion -->
                    <div class="grid grid-cols-4 gap-4 mb-4">
                        <div class="text-center">
                            <div class="text-sm text-gray-500 dark:text-gray-400">Totales</div>
                            <div class="text-lg font-semibold text-blue-600">
                                {{ $parking->plazas->count() }}
                            </div>
                        </div>
                        <div class="text-center">
                            <div class="text-sm text-gray-500 dark:text-gray-400">Disponibles</div>
                            <div class="text-lg font-semibold text-green-600">
                                {{ $parking->plazasDisponibles }}
                            </div>
                        </div>
                        <div class="text-center">
                            <div class="text-sm text-gray-500 dark:text-gray-400">Ocupadas</div>
                            <div class="text-lg font-semibold text-yellow-600">
                                {{ $parking->plazasOcupadas }}
                            </div>
                        </div>
                        <div class="text-center">
                            <div class="text-sm text-gray-500 dark:text-gray-400">Cerradas</div>
                            <div class="text-lg font-semibold text-red-600">
                                {{ $parking->plazasCerradas }}
                            </div>
                        </div>
                        <div class="text-center">
                            <div class="text-sm text-gray-500 dark:text-gray-400">Plantas</div>
                            <div class="text-lg font-semibold text-blue-600">
                                {{ $parking->plantas->count() }}
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>
    @endforeach
</div>


<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div class="parking-card" data-parking-id="{{ $parking->idParking }}">
        
        <div class="bg-white dark:bg-gray-700 rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-all duration-200">
            <div class="mb-8">
                <div class="bg-white dark:bg-gray-700 rounded-lg shadow-md overflow-hidden">
                    <div class="p-4">
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Ubicación de Parkings</h2>
                        <div id="map" class="h-[400px] rounded-lg"></div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

    
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
    </style>
    
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const map = L.map('map');

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);

            const parkings = {!! json_encode($parkings->map(function($parking) {
                return [
                    'id' => $parking->idParking,
                    'nombre' => $parking->nombreParking,
                    'direccion' => $parking->direccionParking,
                    'lat' => floatval($parking->latitudParking),
                    'lng' => floatval($parking->longitudParking),
                    'plazasLibres' => $parking->plazasLibres,
                    'plazasTotales' => $parking->plazasTotales,
                    'tarifa' => $parking->tarifaMinima > 0 ? number_format($parking->tarifaMinima, 2) . '€/hora' : 'No disponible',
                    'horario' => $parking->horaAperturaParking . ' - ' . $parking->horaCierreParking
                ];
            })) !!};

            const bounds = L.latLngBounds();
            let hasValidCoordinates = false;
            let activeMarker = null;
            let activePopup = null;

            parkings.forEach(parking => {
                if (parking.lat && parking.lng && !isNaN(parking.lat) && !isNaN(parking.lng)) {
                    hasValidCoordinates = true;
                    
                    const icon = L.divIcon({
                        className: 'custom-div-icon',
                        html: `<div class="marker-pin bg-blue-500 w-8 h-8 rounded-full flex items-center justify-center text-white font-bold shadow-lg">
                                    <span class="text-sm">P</span>
                                  </div>`,
                        iconSize: [32, 32],
                        iconAnchor: [16, 16],
                        popupAnchor: [0, -16]
                    });

                    const marker = L.marker([parking.lat, parking.lng], {icon: icon}).addTo(map);
                    bounds.extend([parking.lat, parking.lng]);

                    const popupContent = `
                        <div class="popup-content">
                            <div class="bg-gradient-to-r from-blue-500 to-blue-600 text-white p-3 -mt-1 -mx-1 rounded-t-lg">
                                <h3 class="font-bold text-lg">${parking.nombre}</h3>
                                <p class="text-sm text-blue-100">${parking.direccion}</p>
                            </div>
                            
                            <div class="space-y-3 mt-3">
                                <!-- Estado -->
                                <div class="flex items-center justify-between">
                                    <span class="text-gray-600 text-sm">Estado:</span>
                                    <span class="px-2 py-1 rounded-full text-sm font-medium 
                                        ${parking.plazasLibres > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                                        ${parking.plazasLibres}/${parking.plazasTotales} plazas
                                    </span>
                                </div>

                                <!-- Horario -->
                                <div class="flex items-center justify-between">
                                    <span class="text-gray-600 text-sm">Horario:</span>
                                    <span class="font-medium text-gray-800">${parking.horario}</span>
                                </div>

                                <!-- Barra de progreso -->
                                <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                                    <div class="h-full ${parking.plazasLibres > 0 ? 'bg-green-500' : 'bg-red-500'}"
                                         style="width: ${(parking.plazasLibres / parking.plazasTotales) * 100}%">
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;

                    const popup = L.popup({
                        className: 'custom-popup',
                        maxWidth: 300,
                        closeButton: true,
                        autoClose: false
                    }).setContent(popupContent);

                    marker.bindPopup(popup);

                }
            });

            if (hasValidCoordinates) {
                map.fitBounds(bounds, {
                    padding: [30, 30],
                    maxZoom: 15,
                    animate: true
                });
            } else {
                map.setView([40.4168, -3.7038], 6);
            }

            map.on('click', function() {
                if (activeMarker) {
                    activeMarker.closePopup();
                }
            });
        });
    </script>
    
    </x-app-layout>