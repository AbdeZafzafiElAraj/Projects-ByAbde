<x-app-layout>

<div class="container mx-auto px-4 py-8">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden">
        <div class="p-6">
            <!-- Encabezado con estadísticas -->
            <div class="mb-8">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Gestión de Tarifas</h1>
                    @if(Auth::user() && (Auth::user()->isAdmin || Auth::user()->isOperador))
                    <a href="{{ route('tarifas.create') }}" 
                       class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors duration-200">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Nueva Tarifa
                    </a>
                    @endif
                </div>
                
                <!-- Tarjetas de estadísticas -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl p-6 text-white shadow-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-blue-100">Total Tarifas</p>
                                <h2 class="text-3xl font-bold">{{ $tarifas->count() }}</h2>
                            </div>
                            <div class="bg-blue-400/30 rounded-full p-3">
                                <i class="fas fa-tags text-2xl"></i>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-2xl p-6 text-white shadow-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-emerald-100">Precio Medio</p>
                                <h2 class="text-3xl font-bold">{{ number_format($tarifas->avg('precio'), 2) }}€</h2>
                            </div>
                            <div class="bg-emerald-400/30 rounded-full p-3">
                                <i class="fas fa-euro-sign text-2xl"></i>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl p-6 text-white shadow-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-purple-100">Parkings con Tarifas</p>
                                <h2 class="text-3xl font-bold">{{ $tarifas->pluck('parking.nombreParking')->unique()->count() }}</h2>
                            </div>
                            <div class="bg-purple-400/30 rounded-full p-3">
                                <i class="fas fa-parking text-2xl"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Grid de Tarifas -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($tarifas as $tarifa)
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg overflow-hidden hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2">
                    <div class="relative">
                        <!-- Imagen y Badge de tipo -->
                        <div class="h-48 overflow-hidden">
                            @switch(strtolower($tarifa->tipoVehiculo))
                                @case('coche')
                                    <img src="{{ asset('images/coche.jpg') }}" 
                                         class="w-full h-full object-cover transform hover:scale-110 transition-transform duration-500" 
                                         alt="Coche">
                                    @break
                                @case('moto')
                                    <img src="{{ asset('images/moto.jpg') }}" 
                                         class="w-full h-full object-cover transform hover:scale-110 transition-transform duration-500" 
                                         alt="Moto">
                                    @break
                                @case('furgoneta')
                                    <img src="{{ asset('images/furgoneta.jpg') }}" 
                                         class="w-full h-full object-cover transform hover:scale-110 transition-transform duration-500" 
                                         alt="Furgoneta">
                                    @break
                                @default
                                    <img src="{{ asset('images/default.jpg') }}" 
                                         class="w-full h-full object-cover transform hover:scale-110 transition-transform duration-500" 
                                         alt="Vehículo">
                            @endswitch
                        </div>
                        
                        <!-- Badge de tipo de vehículo -->
                        <div class="absolute top-4 left-4 bg-white/90 dark:bg-gray-800/90 backdrop-blur-sm px-4 py-2 rounded-full">
                            <span class="text-gray-700 dark:text-gray-300 font-medium">
                                <i class="fas fa-{{ $tarifa->tipoVehiculo === 'coche' ? 'car' : ($tarifa->tipoVehiculo === 'moto' ? 'motorcycle' : 'truck') }} mr-2"></i>
                                {{ ucfirst($tarifa->tipoVehiculo) }}
                            </span>
                        </div>

                        <!-- Precio -->
                        <div class="absolute top-4 right-4 bg-blue-600 text-white px-4 py-2 rounded-full shadow-lg">
                            <span class="text-2xl font-bold">{{ number_format($tarifa->precio, 2) }}€</span>
                        </div>
                    </div>

                    <div class="p-6">
                        <!-- Información del Parking -->
                        <div class="mb-4">
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">
                                {{ $tarifa->parking->nombreParking }}
                            </h3>
                            <p class="text-gray-600 dark:text-gray-400">
                                {{ $tarifa->descripcion ?? 'Sin descripción' }}
                            </p>
                        </div>

                        <!-- Botones de acción -->
                        <div class="flex flex-wrap gap-2 mt-4">
                            <a href="{{ route('tarifas.show', $tarifa->idTarifa) }}" 
                               class="flex-1 inline-flex items-center justify-center px-4 py-2 bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/50 transition-colors duration-200">
                                <i class="fas fa-eye mr-2"></i>
                                Ver detalles
                            </a>
                            @if(Auth::user() && (Auth::user()->isAdmin || Auth::user()->isOperador))
                                <a href="{{ route('tarifas.edit', $tarifa->idTarifa) }}" 
                                   class="flex-1 inline-flex items-center justify-center px-4 py-2 bg-yellow-50 dark:bg-yellow-900/30 text-yellow-600 dark:text-yellow-400 rounded-lg hover:bg-yellow-100 dark:hover:bg-yellow-900/50 transition-colors duration-200">
                                    <i class="fas fa-edit mr-2"></i>
                                    Editar
                                </a>
                                <form action="{{ route('tarifas.destroy', $tarifa->idTarifa) }}" method="POST" class="flex-1">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            onclick="return confirm('¿Estás seguro de que quieres eliminar esta tarifa?')"
                                            class="w-full inline-flex items-center justify-center px-4 py-2 bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 rounded-lg hover:bg-red-100 dark:hover:bg-red-900/50 transition-colors duration-200">
                                        <i class="fas fa-trash-alt mr-2"></i>
                                        Eliminar
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
</x-app-layout>