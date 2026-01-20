<x-app-layout>

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
         #map { 
            width: 100%; 
            height: 400px; 
            z-index: 1; 
        }
        .custom-popup {
            padding: 0;
        }
        .custom-popup .leaflet-popup-content-wrapper {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 0.5rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .custom-popup .leaflet-popup-content {
            margin: 0;
            padding: 0;
        }
        .popup-content {
            padding: 1rem;
            min-width: 200px;
        }
        .marker-pin {
            transform: scale(1);
            transition: transform 0.2s, box-shadow 0.2s;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .marker-pin:hover {
            transform: scale(1.1);
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
    </style>
@endpush

@push('scripts')
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

                                <!-- Tarifa -->
                                <div class="flex items-center justify-between">
                                    <span class="text-gray-600 text-sm">Tarifa:</span>
                                    <span class="font-medium text-blue-600">${parking.tarifa}</span>
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

                                <!-- Botón de acción -->
                                ${parking.plazasLibres > 0 ? `
                                    <a href="/registro-accesos/create/${parking.id}" 
                                       class="block w-full text-center bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-lg transition-colors duration-200">
                                        <i class="fas fa-car-side mr-2"></i>Registrar Acceso
                                    </a>
                                ` : `
                                    <button disabled class="block w-full text-center bg-red-500 text-white py-2 px-4 rounded-lg opacity-75 cursor-not-allowed">
                                        <i class="fas fa-ban mr-2"></i>Parking Completo
                                    </button>
                                `}
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

                    marker.on('click', function(e) {
                        if (activeMarker && activeMarker !== marker) {
                            activeMarker.closePopup();
                        }
                        activeMarker = marker;
                        activePopup = popup;
                        
                        // Resaltar la tarjeta correspondiente
                        const cards = document.querySelectorAll('.parking-card');
                        cards.forEach(card => {
                            if (card.dataset.parkingId === parking.id.toString()) {
                                card.scrollIntoView({ behavior: 'smooth', block: 'center' });
                                card.classList.add('ring-2', 'ring-blue-500');
                                setTimeout(() => card.classList.remove('ring-2', 'ring-blue-500'), 2000);
                            }
                        });
                    });
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
@endpush

<div class="container mx-auto px-4 py-8">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden">
        <div class="p-6">
            <!-- Encabezado con estadísticas -->
            <div class="mb-8">
                <div class="flex justify-between items-center mb-6">
                    <div class="flex items-center">
                        <svg class="w-8 h-8 text-blue-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Control de Acceso a Parkings</h1>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Gestiona los accesos a los parkings disponibles
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Tarjetas de estadísticas -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg p-6 text-white">
                        <div class="text-sm uppercase tracking-wide">Total Parkings</div>
                        <div class="text-3xl font-bold mt-2">{{ $parkings->count() }}</div>
                    </div>
                    <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg p-6 text-white">
                        <div class="text-sm uppercase tracking-wide">Plazas Disponibles</div>
                        <div class="text-3xl font-bold mt-2">{{ $parkings->sum('plazasLibres') }}</div>
                    </div>
                    <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg p-6 text-white">
                        <div class="text-sm uppercase tracking-wide">Plazas Totales</div>
                        <div class="text-3xl font-bold mt-2">{{ $parkings->sum('plazasTotales') }}</div>
                    </div>
                </div>
            </div>

            <!-- Mapa de Parkings -->
            <div class="mb-8">
                <div class="bg-white dark:bg-gray-700 rounded-lg shadow-md overflow-hidden">
                    <div class="p-4">
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Ubicación de Parkings</h2>
                        <div id="map" class="h-[400px] rounded-lg"></div>
                    </div>
                </div>
            </div>

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
                            </div>

                            <!-- Tarifa -->
                            <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg">
                                <p class="text-gray-700 dark:text-gray-300 font-medium mb-2">Tarifa base:</p>
                                <span class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                                    {{ $parking->tarifaMinima > 0 ? number_format($parking->tarifaMinima, 2) . '€/hora' : 'No disponible' }}
                                </span>
                            </div>

                            <!-- Botones de acción -->
                            <div class="flex flex-col space-y-2 pt-4">
                                @if($parking->plazasLibres > 0)
                                    <a href="{{ route('registro-accesos.create', ['parkingId' => $parking->idParking]) }}" 
                                       class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                        <i class="fas fa-car-side mr-2"></i>Registrar Acceso
                                    </a>
                                @else
                                    <button disabled 
                                            class="inline-flex items-center justify-center px-4 py-2 bg-red-500 text-white rounded-lg cursor-not-allowed opacity-75">
                                        <i class="fas fa-ban mr-2"></i>Parking Completo
                                    </button>
                                @endif

                                @if(Auth::user()->isAdmin)
                                    <a href="{{ route('registro-accesos.show', ['parking' => $parking->idParking]) }}" 
                                       class="inline-flex items-center justify-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                                        <i class="fas fa-history mr-2"></i>Ver Historial
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>

                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

</x-app-layout>