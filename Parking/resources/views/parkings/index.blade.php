<x-app-layout>

<div class="container mx-auto px-4 py-8">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden">
        <div class="p-6">
            <!-- Encabezado con estadísticas -->
            <div class="mb-8">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Nuestros Parkings</h1>
                    @if(Auth::user() && Auth::user()->isAdmin)
                    <a href="{{ route('parkings.create') }}" 
                       class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors duration-200">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Crear nuevo Parking
                    </a>
                    @endif
                </div>

                @if (session('success'))
                    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
                        <p class="font-bold">¡Éxito!</p>
                        <p>{{ session('success') }}</p>
                    </div>
                @endif
                
                <!-- Tarjetas de estadísticas -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                    <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg p-6 text-white">
                        <div class="text-sm uppercase tracking-wide">Total Parkings</div>
                        <div class="text-3xl font-bold mt-2">{{ $parkings->count() }}</div>
                    </div>
                    <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg p-6 text-white">
                        <div class="text-sm uppercase tracking-wide">Plazas Disponibles</div>
                        <div class="text-3xl font-bold mt-2">{{ $parkings->sum('plazasDisponibles') }}</div>
                    </div>
                    <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-lg p-6 text-white">
                        <div class="text-sm uppercase tracking-wide">Plazas Ocupadas</div>
                        <div class="text-3xl font-bold mt-2">{{ $parkings->sum('plazasOcupadas') }}</div>
                    </div>
                    <div class="bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-lg p-6 text-white">
                        <div class="text-sm uppercase tracking-wide">Plazas Totales</div>
                        <div class="text-3xl font-bold mt-2">{{ $parkings->sum('plazasDisponibles') + $parkings->sum('plazasOcupadas') + $parkings->sum('plazasCerradas') }}</div>
                    </div>
                </div>
            </div>

            <!-- Grid de Parkings -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                @foreach($parkings as $parking)
                <div class="bg-white dark:bg-gray-700 rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-200">
                    <div class="relative h-48">
                        <img src="@if ($parking->imagenParking && file_exists(public_path('storage/' . $parking->imagenParking)))
                                    {{ asset('storage/' . $parking->imagenParking) }}
                                @elseif($parking->imagenParking && file_exists(public_path('images/' . $parking->imagenParking)))
                                    {{ asset('images/' . $parking->imagenParking) }}
                                @else
                                    {{ asset('images/default-parking-image.jpg') }}
                                @endif"
                             alt="{{ $parking->nombreParking }}"
                             class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                        <div class="absolute bottom-0 left-0 p-4">
                            <h3 class="text-xl font-bold text-white">{{ $parking->nombreParking }}</h3>
                            <p class="text-sm text-gray-200">{{ $parking->direccionParking }}</p>
                        </div>
                    </div>
                    
                    <div class="p-4">
                        <!-- Estado del parking -->
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

                        <!-- Botones de acción -->
                        <div class="flex justify-end space-x-2 mt-4">
                            <a href="{{ route('parkings.show', $parking->idParking) }}" 
                               class="inline-flex items-center px-3 py-1 bg-blue-100 text-blue-600 rounded-full hover:bg-blue-200 transition-colors duration-200">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                Ver detalles
                            </a>
                            @if(Auth::user()->isAdmin)
                            <a href="{{ route('parkings.edit', $parking->idParking) }}" 
                               class="inline-flex items-center px-3 py-1 bg-yellow-100 text-yellow-600 rounded-full hover:bg-yellow-200 transition-colors duration-200">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                Editar
                            </a>
                            <form action="{{ route('parkings.destroy', $parking->idParking) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        onclick="return confirm('¿Estás seguro de que quieres eliminar este parking?')"
                                        class="inline-flex items-center px-3 py-1 bg-red-100 text-red-600 rounded-full hover:bg-red-200 transition-colors duration-200">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                    Eliminar
                                </button>
                            </form>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Mapa Section -->
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
                    'plazasDisponibles' => $parking->plazasDisponibles,
                    'plazasOcupadas' => $parking->plazasOcupadas,
                    'plazasCerradas' => $parking->plazasCerradas,
                    'capacidadTotal' => $parking->capacidadTotal,
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
                                <div class="grid grid-cols-3 gap-2 mb-2">
                                    <div class="text-center">
                                        <div class="text-sm text-gray-500">Disponibles</div>
                                        <div class="text-lg font-semibold text-green-600">
                                            ${parking.plazasDisponibles}
                                        </div>
                                    </div>
                                    <div class="text-center">
                                        <div class="text-sm text-gray-500">Ocupadas</div>
                                        <div class="text-lg font-semibold text-yellow-600">
                                            ${parking.plazasOcupadas}
                                        </div>
                                    </div>
                                    <div class="text-center">
                                        <div class="text-sm text-gray-500">Cerradas</div>
                                        <div class="text-lg font-semibold text-red-600">
                                            ${parking.plazasCerradas}
                                        </div>
                                    </div>
                                </div>

                                <!-- Horario -->
                                <div class="flex items-center justify-between">
                                    <span class="text-gray-600 text-sm">Horario:</span>
                                    <span class="font-medium text-gray-800">${parking.horario}</span>
                                </div>

                                <!-- Barra de progreso -->
                                <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                                    <div class="h-full bg-green-500"
                                         style="width: ${(parking.plazasDisponibles / parking.capacidadTotal) * 100}%">
                                    </div>
                                </div>

                                <!-- Botones de acción -->
                                <div class="flex space-x-2 mt-3">
                                    <a href="/parkings/${parking.id}" 
                                       class="flex-1 text-center bg-blue-600 hover:bg-blue-700 text-white py-2 px-3 rounded-lg transition-colors duration-200 text-sm">
                                        Ver detalles
                                    </a>
                                    @if(Auth::user() && Auth::user()->isAdmin)
                                    <a href="/parkings/${parking.id}/edit" 
                                       class="flex-1 text-center bg-yellow-100 hover:bg-yellow-200 text-yellow-600 py-2 px-3 rounded-lg transition-colors duration-200 text-sm">
                                        Editar
                                    </a>
                                    @endif
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

                    marker.on('click', function(e) {
                        if (activeMarker && activeMarker !== marker) {
                            activeMarker.closePopup();
                        }
                        activeMarker = marker;
                        activePopup = popup;
                        
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
</x-app-layout>