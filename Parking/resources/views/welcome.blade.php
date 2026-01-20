<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Bienvenido a Parkingfy</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link rel="icon" href="{{ asset('images/Logo.png') }}" type="image/x-icon">

    <!-- Styles / Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <style>
        #map { 
            height: 500px; 
            width: 100%;
            z-index: 1;
        }
        .custom-popup .leaflet-popup-content-wrapper {
            padding: 0;
            overflow: hidden;
            border-radius: 0.5rem;
        }
        .custom-popup .leaflet-popup-content {
            margin: 0;
            min-width: 300px;
        }
        .hero-button {
            @apply inline-flex items-center px-6 py-3 rounded-lg font-semibold transition-all duration-300;
            text-shadow: none;
        }
        .btn-primary {
            @apply hero-button bg-blue-600 text-white hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2;
        }
        .btn-secondary {
            @apply hero-button bg-white text-blue-600 hover:bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2;
        }
        .banner {
            position: relative;
            background-image: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.8)), url('{{ asset('images/LandingPageBG1.jpg') }}');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            min-height: 100vh;
        }

        .nav-container {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 50;
            background: rgba(0, 0, 0, 0.8);
            backdrop-filter: blur(10px);
            padding: 1rem 0;
        }

        .nav-content {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo-text {
            font-size: 1.75rem;
            font-weight: 700;
            color: white;
            text-decoration: none;
        }

        .logo-highlight {
            color: #3B82F6;
        }

        .nav-buttons {
            display: flex;
            gap: 1rem;
            align-items: center;
        }

        .nav-button {
            position: relative;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            font-size: 0.95rem;
            color: white;
            background: linear-gradient(135deg, #3B82F6 0%, #2563EB 100%);
            border-radius: 12px;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(59, 130, 246, 0.2);
        }

        .nav-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(59, 130, 246, 0.3);
            background: linear-gradient(135deg, #2563EB 0%, #1D4ED8 100%);
        }

        .nav-button.outline {
            background: transparent;
            border: 2px solid #3B82F6;
            color: #3B82F6;
            box-shadow: none;
        }

        .nav-button.outline:hover {
            background: #3B82F6;
            color: white;
        }

        .hero-content {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 100%;
            max-width: 1200px;
            text-align: center;
            padding: 0 2rem;
        }

        .hero-title {
            font-size: 6rem;
            font-weight: 800;
            color: white;
            margin-bottom: 1.5rem;
            line-height: 1.1;
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
        }

        .hero-subtitle {
            font-size: 1.75rem;
            color: rgba(255, 255, 255, 0.9);
            max-width: 800px;
            margin: 0 auto 4rem auto;
            line-height: 1.6;
        }

        .action-buttons {
            display: flex;
            gap: 1.5rem;
            justify-content: center;
            margin-top: 2rem;
        }

        .action-button {
            position: relative;
            padding: 1rem 2rem;
            font-weight: 600;
            font-size: 1.1rem;
            color: white;
            background: linear-gradient(135deg, #3B82F6 0%, #2563EB 100%);
            border-radius: 14px;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(59, 130, 246, 0.3);
        }

        .action-button:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(59, 130, 246, 0.4);
            background: linear-gradient(135deg, #2563EB 0%, #1D4ED8 100%);
        }

        .action-button.secondary {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 2px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
        }

        .action-button.secondary:hover {
            background: rgba(255, 255, 255, 0.2);
            border-color: rgba(255, 255, 255, 0.3);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
        }

        .action-button i {
            font-size: 1.2rem;
        }

        @media (max-width: 640px) {
            .nav-content {
                padding: 0 1rem;
            }

            .nav-button {
                padding: 0.5rem 1rem;
                font-size: 0.875rem;
            }

            .action-button {
                padding: 0.75rem 1.5rem;
                font-size: 1rem;
            }

            .action-buttons {
                flex-direction: column;
                gap: 1rem;
            }
        }
    </style>
</head>
<body class="bg-gray-100 dark:bg-gray-900">
    <div class="banner">
        <!-- Navigation -->
        <nav class="nav-container">
            <div class="nav-content">
                <a href="/" class="logo-text">
                    Parking<span style="color: #3B82F6">fy</span>
                </a>
                
                <div class="nav-buttons">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="nav-button">
                                <i class="fas fa-tachometer-alt"></i>
                                Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="nav-button">
                                <i class="fas fa-sign-in-alt"></i>
                                Iniciar Sesión
                            </a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="nav-button outline">
                                    <i class="fas fa-user-plus"></i>
                                    Registrarse
                                </a>
                            @endif
                        @endauth
                    @endif
                </div>
            </div>
        </nav>

        <!-- Hero Content -->
        <div class="hero-content">
            <h1 class="hero-title">
                Parking<span style="color: #3B82F6">fy</span>
            </h1>
            <p class="hero-subtitle">
                Encuentra y gestiona tu plaza de parking de manera fácil y eficiente
            </p>
            <div class="action-buttons">
                <a href="#map" class="action-button">
                    <i class="fas fa-map-marker-alt"></i>
                    Buscar Parking
                </a>
                <a href="#estadisticas" class="action-button secondary">
                    <i class="fas fa-chart-bar"></i>
                    Ver Estadísticas
                </a>
            </div>
        </div>

        <!-- Scroll Indicator -->
        <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 animate-bounce">
            <i class="fas fa-chevron-down text-white text-2xl opacity-75"></i>
        </div>
    </div>

    <!-- Estadísticas Section -->
    <div id="estadisticas" class="py-16 bg-white dark:bg-gray-800">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-8 text-center">Resumen General</h2>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-6 text-white transform hover:scale-105 transition-all duration-300">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-blue-100">Total Parkings</p>
                            <h2 class="text-4xl font-bold">{{ $estadisticas['totalParkings'] }}</h2>
                        </div>
                        <div class="bg-blue-400/30 rounded-full p-4">
                            <i class="fas fa-parking text-3xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl p-6 text-white transform hover:scale-105 transition-all duration-300">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-green-100">Plazas Disponibles</p>
                            <h2 class="text-4xl font-bold">{{ $estadisticas['plazasDisponibles'] }}</h2>
                        </div>
                        <div class="bg-green-400/30 rounded-full p-4">
                            <i class="fas fa-check-circle text-3xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-xl p-6 text-white transform hover:scale-105 transition-all duration-300">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-yellow-100">Plazas Ocupadas</p>
                            <h2 class="text-4xl font-bold">{{ $estadisticas['plazasOcupadas'] }}</h2>
                        </div>
                        <div class="bg-yellow-400/30 rounded-full p-4">
                            <i class="fas fa-car text-3xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl p-6 text-white transform hover:scale-105 transition-all duration-300">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-purple-100">Plazas Totales</p>
                            <h2 class="text-4xl font-bold">{{ $estadisticas['plazasTotales'] }}</h2>
                        </div>
                        <div class="bg-purple-400/30 rounded-full p-4">
                            <i class="fas fa-chart-pie text-3xl"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Mapa Section -->
    <div class="py-12 bg-gray-50 dark:bg-gray-900">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-8 text-center">Encuentra tu parking</h2>
            <div id="map" class="rounded-xl shadow-lg"></div>
        </div>
    </div>

    <!-- Parkings Section -->
    <div class="py-12 bg-white dark:bg-gray-800">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-8 text-center">Nuestros Parkings</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($parkings as $parking)
                <div class="bg-white dark:bg-gray-700 rounded-xl shadow-lg overflow-hidden transform hover:scale-105 transition-all duration-300">
                    <div class="relative h-48 bg-gradient-to-r from-blue-500 to-blue-600">
                        <div class="absolute inset-0 p-6 text-white">
                            <h3 class="text-2xl font-bold mb-2">{{ $parking->nombreParking }}</h3>
                            <p class="text-blue-100 mb-4">{{ $parking->direccionParking }}</p>
                            <div class="flex items-center text-sm">
                                <i class="fas fa-clock mr-2"></i>
                                {{ $parking->horaAperturaParking }} - {{ $parking->horaCierreParking }}
                            </div>
                            @if($parking->tarifaMinima)
                                <div class="absolute top-6 right-6 bg-white text-blue-600 px-4 py-2 rounded-full font-bold">
                                    {{ number_format($parking->tarifaMinima, 2) }}€/h
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="p-6">
                        <div class="grid grid-cols-3 gap-4">
                            <div class="text-center p-4 bg-green-50 dark:bg-green-900/20 rounded-lg">
                                <div class="text-2xl font-bold text-green-600 dark:text-green-400">
                                    {{ $parking->plazasLibres }}
                                </div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">Libres</div>
                            </div>
                            <div class="text-center p-4 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg">
                                <div class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">
                                    {{ $parking->plazasOcupadas }}
                                </div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">Ocupadas</div>
                            </div>
                            <div class="text-center p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                                <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                                    {{ $parking->plazasTotales }}
                                </div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">Total</div>
                            </div>
                        </div>

                        <div class="mt-6">
                            <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
                                @if($parking->plazasTotales > 0)
                                    <div class="bg-blue-600 h-2.5 rounded-full" 
                                         style="width: {{ ($parking->plazasOcupadas / $parking->plazasTotales) * 100 }}%">
                                    </div>
                                @else
                                    <div class="bg-gray-400 h-2.5 rounded-full" style="width: 100%"></div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Script del Mapa -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Inicializar el mapa
            const map = L.map('map').setView([41.1183, 1.2444], 13);

            // Añadir la capa de OpenStreetMap
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);

            // Cargar los parkings
            fetch('/mapa-parkings')
                .then(response => response.json())
                .then(parkings => {
                    console.log('Parkings:', parkings); // Debug
                    const bounds = [];

                    parkings.forEach(parking => {
                        if (parking.latitud && parking.longitud) {
                            const marker = L.marker([parking.latitud, parking.longitud])
                                .addTo(map)
                                .bindPopup(`
                                    <div class="p-4">
                                        <h3 class="font-bold text-lg mb-2">${parking.nombre}</h3>
                                        <p class="text-gray-600 mb-2">${parking.direccion}</p>
                                        <p class="text-gray-600 mb-4">${parking.horario}</p>
                                        
                                        <div class="grid grid-cols-3 gap-2 text-center mb-4">
                                            <div class="bg-green-50 p-2 rounded">
                                                <span class="text-green-600 font-bold block">${parking.plazasLibres}</span>
                                                <span class="text-sm">Libres</span>
                                            </div>
                                            <div class="bg-red-50 p-2 rounded">
                                                <span class="text-red-600 font-bold block">${parking.plazasOcupadas}</span>
                                                <span class="text-sm">Ocupadas</span>
                                            </div>
                                            <div class="bg-blue-50 p-2 rounded">
                                                <span class="text-blue-600 font-bold block">${parking.plazasTotales}</span>
                                                <span class="text-sm">Total</span>
                                            </div>
                                        </div>

                                        <div class="bg-gray-50 p-3 rounded mb-4">
                                            <h4 class="font-semibold mb-2">Plantas:</h4>
                                            ${parking.plantas.map(planta => `
                                                <div class="grid grid-cols-2 gap-2 mb-1">
                                                    <span>${planta.nombre}:</span>
                                                    <span>${planta.libres} libres</span>
                                                </div>
                                            `).join('')}
                                        </div>

                                        <div class="bg-blue-50 p-2 rounded text-center">
                                            <span class="text-blue-600 font-bold">${parking.tarifa}</span>
                                        </div>
                                    </div>
                                `, {
                                    className: 'custom-popup'
                                });
                            bounds.push([parking.latitud, parking.longitud]);
                        }
                    });

                    if (bounds.length > 0) {
                        map.fitBounds(bounds, { padding: [50, 50] });
                    }
                })
                .catch(error => {
                    console.error('Error cargando parkings:', error);
                });
        });
    </script>
</body>
</html>
