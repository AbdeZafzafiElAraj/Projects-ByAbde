<x-app-layout>

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden">
        <div class="p-6">
            <!-- Encabezado con imagen de fondo -->
            <div class="relative h-80 -mx-6 -mt-6 mb-8">
                <div class="absolute inset-0">
                    @php
                        $imagePath = $parking->imagenParking;
                        $imageUrl = file_exists(public_path('storage/'.$imagePath)) 
                            ? asset('storage/'.$imagePath) 
                            : asset('images/'.$imagePath);
                    @endphp
                    <img src="{{ $imageUrl }}" 
                         alt="{{ $parking->nombreParking }}" 
                         class="w-full h-full object-cover">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/40 to-transparent"></div>
                </div>
                <div class="absolute bottom-0 left-0 right-0 p-6">
                    <div class="flex justify-between items-end">
                        <div>
                            <h1 class="text-4xl font-bold text-white mb-2">{{ $parking->nombreParking }}</h1>
                            <div class="flex items-center text-gray-200 space-x-4">
                                <span class="flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    </svg>
                                    {{ $parking->direccionParking }}
                                </span>
                                <span class="flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    {{ $parking->horaAperturaParking }} - {{ $parking->horaCierreParking }}
                                </span>
                            </div>
                        </div>
                        @if(Auth::user() && Auth::user()->isAdmin)
                        <div class="flex space-x-3">
                            <a href="{{ route('parkings.edit', $parking->idParking) }}" 
                               class="inline-flex items-center px-4 py-2 bg-yellow-500/90 hover:bg-yellow-500 text-white rounded-lg transition">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                Editar
                            </a>
                            <form action="{{ route('parkings.destroy', $parking->idParking) }}" 
                                  method="POST" 
                                  class="inline-block"
                                  onsubmit="return confirm('¿Estás seguro de eliminar este parking?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600/90 hover:bg-red-600 text-white rounded-lg transition">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                    Eliminar
                                </button>
                            </form>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Tarjetas de estadísticas -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
                <div class="bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl p-6 text-white">
                    <div class="flex items-center justify-between mb-2">
                        <svg class="w-8 h-8 text-emerald-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                        <span class="text-xs font-semibold text-emerald-100 uppercase tracking-wider">Capacidad Maxima</span>
                    </div>
                    <p class="text-3xl font-bold">{{ $parking->capacidadTotal }}</p>
                </div>

                <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl p-6 text-white">
                    <div class="flex items-center justify-between mb-2">
                        <svg class="w-8 h-8 text-purple-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <span class="text-xs font-semibold text-purple-100 uppercase tracking-wider">Plazas Actuales</span>
                    </div>
                    <p class="text-3xl font-bold">{{ $parking->plazasDisponibles + $parking->plazasOcupadas + $parking->plazasCerradas }}</p>
                </div>

                <div class="bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl p-6 text-white">
                    <div class="flex items-center justify-between mb-2">
                        <svg class="w-8 h-8 text-emerald-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span class="text-xs font-semibold text-emerald-100 uppercase tracking-wider">Disponibles</span>
                    </div>
                    <p class="text-3xl font-bold">{{ $parking->plazasDisponibles }}</p>
                </div>

                <div class="bg-gradient-to-br from-rose-500 to-rose-600 rounded-xl p-6 text-white">
                    <div class="flex items-center justify-between mb-2">
                        <svg class="w-8 h-8 text-rose-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span class="text-xs font-semibold text-rose-100 uppercase tracking-wider">Ocupadas</span>
                    </div>
                    <p class="text-3xl font-bold">{{ $parking->plazasOcupadas }}</p>
                </div>

                <div class="bg-gradient-to-br from-amber-500 to-amber-600 rounded-xl p-6 text-white">
                    <div class="flex items-center justify-between mb-2">
                        <svg class="w-8 h-8 text-amber-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <span class="text-xs font-semibold text-amber-100 uppercase tracking-wider">Cerradas</span>
                    </div>
                    <p class="text-3xl font-bold">{{ $parking->plazasCerradas }}</p>
                </div>
            </div>

            <!-- Desglose por Plantas -->
            @if(Auth::user() && (Auth::user()->isAdmin))
            <div class="mb-8">
                <h2 class="text-2xl font-bold mb-6 text-gray-800 dark:text-white flex items-center">
                    <svg class="w-6 h-6 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    Desglose por Plantas
                </h2>
                @if($parking->plantas->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach ($parking->plantas as $planta)
                            <div class="bg-white dark:bg-gray-700 rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-all duration-200">
                                <div class="p-6">
                                    <div class="flex justify-between items-center mb-4">
                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                            {{ $planta->nombrePlanta }}
                                        </h3>
                                        <span class="px-3 py-1 rounded-full text-sm font-medium
                                            {{ $planta->estadoPlanta === 'abierta' ? 'bg-emerald-100 text-emerald-800' : 'bg-yellow-100 text-yellow-800' }}">
                                            {{ ucfirst($planta->estadoPlanta) }}
                                        </span>
                                    </div>
                                    
                                    <div class="space-y-4">
                                        <div class="flex justify-between text-sm text-gray-600 dark:text-gray-300">
                                            <span>Capacidad Máxima</span>
                                            <span class="font-medium">{{ $planta->capacidadMaximaPlanta }}</span>
                                        </div>
                                        
                                        <div class="grid grid-cols-3 gap-4">
                                            <div class="text-center">
                                                <span class="block text-sm text-gray-500 dark:text-gray-400">Disponibles</span>
                                                <span class="text-lg font-semibold text-emerald-600 dark:text-emerald-400">
                                                    {{ $planta->plazas->where('estadoPlaza', 'libre')->count() }}
                                                </span>
                                            </div>
                                            <div class="text-center">
                                                <span class="block text-sm text-gray-500 dark:text-gray-400">Ocupadas</span>
                                                <span class="text-lg font-semibold text-rose-600 dark:text-rose-400">
                                                    {{ $planta->plazas->where('estadoPlaza', 'ocupada')->count() }}
                                                </span>
                                            </div>
                                            <div class="text-center">
                                                <span class="block text-sm text-gray-500 dark:text-gray-400">Cerradas</span>
                                                <span class="text-lg font-semibold text-amber-600 dark:text-amber-400">
                                                    {{ $planta->plazas->where('estadoPlaza', 'cerrada')->count() }}
                                                </span>
                                            </div>
                                        </div>

                                        <!-- Barra de progreso -->
                                        <div class="w-full bg-gray-200 rounded-full h-2 dark:bg-gray-600">
                                            @php
                                                $ocupacion = $planta->plazas->count() > 0 
                                                    ? ($planta->plazas->where('estadoPlaza', 'ocupada')->count() / $planta->plazas->count()) * 100 
                                                    : 0;
                                            @endphp
                                            <div class="bg-emerald-600 h-2 rounded-full" style="width: {{ $ocupacion }}%"></div>
                                        </div>

                                        <a href="{{ route('plantas.show', $planta->idPlanta) }}" 
                                           class="block w-full text-center px-4 py-2 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition-colors">
                                            <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                            Ver Detalles
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="bg-white dark:bg-gray-700 rounded-lg shadow-md p-6 text-center">
                        <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                        </svg>
                        <p class="text-gray-500 dark:text-gray-400">No hay plantas registradas en este momento.</p>
                    </div>
                @endif
            </div>
            @endif

            <!-- Métodos de Pago -->
            <div class="mb-8">
                <h2 class="text-2xl font-bold mb-6 text-gray-800 dark:text-white flex items-center">
                    <svg class="w-6 h-6 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    Métodos de Pago Aceptados
                </h2>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                    @php
                        $metodosPago = is_array($parking->opcionesPagoParking) 
                            ? $parking->opcionesPagoParking 
                            : json_decode($parking->opcionesPagoParking) ?? [];
                        $iconos = [
                            'efectivo' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>',
                            'tarjeta' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>',
                            'movil' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>',
                            'gratuito' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>'
                        ];
                        $nombres = [
                            'efectivo' => 'Efectivo',
                            'tarjeta' => 'Tarjeta',
                            'movil' => 'Pago Móvil',
                            'gratuito' => 'Gratuito'
                        ];
                    @endphp

                    @foreach(['efectivo', 'tarjeta', 'movil', 'gratuito'] as $metodo)
                        <div class="bg-white dark:bg-gray-700 rounded-lg shadow-md overflow-hidden">
                            <div class="p-6">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0">
                                            <div class="{{ in_array($metodo, $metodosPago) ? 'text-emerald-500' : 'text-gray-400' }}">
                                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    {!! $iconos[$metodo] !!}
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                                                {{ $nombres[$metodo] }}
                                            </h3>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ in_array($metodo, $metodosPago) ? 'Disponible' : 'No disponible' }}
                                            </p>
                                        </div>
                                    </div>
                                    @if(in_array($metodo, $metodosPago))
                                        <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    @else
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Después de las opciones de pago -->
            <div class="mb-8">
                <h2 class="text-2xl font-bold mb-6 text-gray-800 dark:text-white flex items-center">
                    <svg class="w-6 h-6 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                    Tipo de Control de Acceso
                </h2>

                <div class="bg-white dark:bg-gray-700 rounded-lg shadow-md overflow-hidden">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                @if($parking->tipoParking === 'conControl')
                                    <div class="text-emerald-500">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                        </svg>
                                    </div>
                                @else
                                    <div class="text-blue-500">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                                    {{ $parking->tipoParking === 'conControl' ? 'Con Control de Acceso' : 'Sin Control de Acceso' }}
                                </h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    @if($parking->tipoParking === 'conControl')
                                        Este parking asigna plazas de forma automática
                                    @else
                                        Este parking asigna plazas de forma manual
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tarifas -->
            <div class="mb-8">
                <h2 class="text-2xl font-bold mb-6 text-gray-800 dark:text-white flex items-center">
                    <svg class="w-6 h-6 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Tarifas
                </h2>
                @if ($parking->tarifas->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        @foreach ($parking->tarifas as $tarifa)
                            <div class="bg-white dark:bg-gray-700 rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-all duration-200">
                                <div class="relative h-32">
                                    <img src="{{ asset('images/' . $tarifa->tipoVehiculo . '.jpg') }}" 
                                         alt="{{ ucfirst($tarifa->tipoVehiculo) }}"
                                         class="w-full h-full object-cover"
                                         onerror="this.src='{{ asset('images/default-vehicle.jpg') }}'">
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                                    <div class="absolute bottom-0 left-0 right-0 p-4">
                                        <h3 class="text-lg font-semibold text-white">
                                            {{ ucfirst($tarifa->tipoVehiculo) }}
                                        </h3>
                                    </div>
                                </div>
                                <div class="p-6">
                                    <div class="flex justify-between items-center mb-4">
                                        <span class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                                            {{ number_format($tarifa->precio, 2) }}€
                                        </span>
                                    </div>
                                    <p class="text-gray-600 dark:text-gray-300 text-sm mb-4">
                                        {{ $tarifa->descripcion ?? 'Sin descripción disponible' }}
                                    </p>
                                    @if(Auth::user() && (Auth::user()->isAdmin || Auth::user()->isOperador))
                                        <a href="{{ route('tarifas.edit', $tarifa->idTarifa) }}" 
                                           class="block w-full text-center px-4 py-2 bg-yellow-100 text-yellow-700 rounded-lg hover:bg-yellow-200 transition-colors">
                                            <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                            Editar Tarifa
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="bg-white dark:bg-gray-700 rounded-lg shadow-md p-6 text-center">
                        <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="text-gray-500 dark:text-gray-400">No hay tarifas registradas para este parking.</p>
                    </div>
                @endif
            </div>

            <!-- Mapa -->
            <div class="mb-8">
                <h2 class="text-2xl font-bold mb-6 text-gray-800 dark:text-white flex items-center">
                    <svg class="w-6 h-6 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                    </svg>
                    Ubicación
                </h2>
                <div class="bg-white dark:bg-gray-700 rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-all duration-200">
                    <div id="map" class="h-[500px] w-full"></div>
                </div>
            </div>

            <!-- Panel de Control -->
            @if(Auth::user() && (Auth::user()->isAdmin || Auth::user()->isOperador))
            <div class="mb-8">
                <h2 class="text-2xl font-bold mb-6 text-gray-800 dark:text-white flex items-center">
                    <svg class="w-6 h-6 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    Panel de Control
                </h2>

                <div class="bg-white dark:bg-gray-700 rounded-lg shadow-md overflow-hidden">
                    <div class="p-6">
                        @if($parking->plantas->count() > 0)
                            <!-- Selector de Planta -->
                            <div class="mb-6">
                                <label for="planta" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Seleccionar Planta
                                </label>
                                <select id="planta" 
                                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                    @foreach($parking->plantas as $planta)
                                        <option value="{{ $planta->idPlanta }}">
                                            {{ $planta->nombrePlanta }} ({{ $planta->plazas->where('estadoPlaza', 'libre')->count() }} disponibles)
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Contenedor del Mapa SVG -->
                            <div class="bg-gray-100 dark:bg-gray-800 p-6 rounded-lg">
                                <div id="contenedorMapa" class="relative border border-gray-200 dark:border-gray-700 rounded-lg" style="width: 100%; height: 500px;">
                                    <div id="loadingIndicator" class="hidden absolute inset-0 flex items-center justify-center bg-gray-900 bg-opacity-50">
                                        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500"></div>
                                    </div>
                                    <svg id="mapaPlanta" class="w-full h-full"></svg>
                                </div>

                                <!-- Leyenda -->
                                <div class="mt-4 flex flex-wrap gap-4 justify-center">
                                    <div class="flex items-center">
                                        <div class="w-4 h-4 bg-emerald-500 rounded mr-2"></div>
                                        <span class="text-sm text-gray-600 dark:text-gray-300">Libre</span>
                                    </div>
                                    <div class="flex items-center">
                                        <div class="w-4 h-4 bg-rose-500 rounded mr-2"></div>
                                        <span class="text-sm text-gray-600 dark:text-gray-300">Ocupada</span>
                                    </div>
                                    <div class="flex items-center">
                                        <div class="w-4 h-4 bg-amber-500 rounded mr-2"></div>
                                        <span class="text-sm text-gray-600 dark:text-gray-300">Cerrada</span>
                                    </div>
                                    <div class="flex items-center">
                                        <div class="w-4 h-4 bg-purple-500 rounded mr-2"></div>
                                        <span class="text-sm text-gray-600 dark:text-gray-300">Mantenimiento</span>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="text-center py-12">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">No hay plantas</h3>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Este parking no tiene plantas registradas.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif
            
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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

        const marker = L.marker([{{ $parking->latitudParking }}, {{ $parking->longitudParking }}], {
            icon: icon
        }).addTo(map);

        const popupContent = `
            <div class="min-w-[200px]">
                <div class="bg-gradient-to-r from-blue-500 to-blue-600 text-white p-3 -mt-1 -mx-1 rounded-t-lg">
                    <h3 class="font-bold text-lg">${'{{ $parking->nombreParking }}'}</h3>
                    <p class="text-sm text-blue-100">${'{{ $parking->direccionParking }}'}</p>
                </div>
                <div class="p-3">
                    <div class="flex items-center justify-between text-sm mb-2">
                        <span class="text-gray-600">Estado:</span>
                        <span class="px-2 py-1 rounded-full text-sm font-medium 
                            ${'{{ $parking->plazasDisponibles > 0 ? "bg-emerald-100 text-emerald-800" : "bg-rose-100 text-rose-800" }}'}">
                            ${'{{ $parking->plazasDisponibles }}'}/{{ $parking->capacidadTotal }} plazas
                        </span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2 mb-2">
                        <div class="h-full ${'{{ $parking->plazasDisponibles > 0 ? "bg-emerald-500" : "bg-rose-500" }}'} rounded-full"
                             style="width: ${'{{ ($parking->plazasDisponibles / $parking->capacidadTotal) * 100 }}%'}">
                        </div>
                    </div>
                    <div class="text-sm text-gray-600">
                        <p>Horario: ${'{{ $parking->horaAperturaParking }}'} - ${'{{ $parking->horaCierreParking }}'}</p>
                    </div>
                </div>
            </div>
        `;

        marker.bindPopup(popupContent, {
            className: 'custom-popup'
        }).openPopup();
    });

<!-- Script para el mapa SVG -->

document.addEventListener('DOMContentLoaded', function() {
    const selectPlanta = document.getElementById('planta');
    const loadingIndicator = document.getElementById('loadingIndicator');

    
    if (!selectPlanta) return;

    // Función para cargar y mostrar el mapa de plazas
    async function cargarMapaPlazas(plantaId) {
        try {
            const response = await fetch(`/plantas/${plantaId}/plazas`);
            if (!response.ok) throw new Error('Error al cargar las plazas');
            
            const plazas = await response.json();
            actualizarMapaSVG(plazas);
        } catch (error) {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'No se pudieron cargar las plazas'
            });
        }
    }

    // Actualizar mapa cuando cambie la planta seleccionada
    document.getElementById('planta').addEventListener('change', function() {
        cargarMapaPlazas(this.value);
    });

    // Cargar mapa inicial
    if (document.getElementById('planta').value) {
        cargarMapaPlazas(document.getElementById('planta').value);
    }

    // Función para actualizar el mapa SVG
    function actualizarMapaSVG(plazas) {
        const svg = document.getElementById('mapaPlanta');
        const contenedor = document.getElementById('contenedorMapa');
        svg.innerHTML = '';

        // Dimensiones del contenedor
        const contenedorWidth = contenedor.clientWidth;
        const contenedorHeight = contenedor.clientHeight;
        svg.setAttribute('viewBox', `0 0 ${contenedorWidth} ${contenedorHeight}`);

        // Calcular la disposición de las plazas
        const numPlazas = plazas.length;
        const numColumnas = Math.ceil(Math.sqrt(numPlazas));
        const numFilas = Math.ceil(numPlazas / numColumnas);

        // Dimensiones de cada plaza
        const plazaWidth = (contenedorWidth / numColumnas) * 0.8;
        const plazaHeight = (contenedorHeight / numFilas) * 0.8;
        const espaciadoX = contenedorWidth / numColumnas;
        const espaciadoY = contenedorHeight / numFilas;

        // Colores según estado
        const colores = {
            'libre': '#10b981', // emerald-500
            'ocupada': '#f43f5e', // rose-500
            'cerrada': '#f59e0b', // amber-500
            'mantenimiento': '#a855f7' // purple-500
        };

        // Crear elementos SVG para cada plaza
        plazas.forEach((plaza, index) => {
            const fila = Math.floor(index / numColumnas);
            const columna = index % numColumnas;

            const x = (columna * espaciadoX) + (espaciadoX - plazaWidth) / 2;
            const y = (fila * espaciadoY) + (espaciadoY - plazaHeight) / 2;

            // Crear grupo para la plaza
            const g = document.createElementNS("http://www.w3.org/2000/svg", "g");
            g.setAttribute('class', 'plaza-grupo');
            g.setAttribute('data-plaza-id', plaza.idPlaza);

            // Crear rectángulo de la plaza
            const rect = document.createElementNS("http://www.w3.org/2000/svg", "rect");
            rect.setAttribute('x', x);
            rect.setAttribute('y', y);
            rect.setAttribute('width', plazaWidth);
            rect.setAttribute('height', plazaHeight);
            rect.setAttribute('rx', '4');
            rect.setAttribute('fill', colores[plaza.estadoPlaza]);
            rect.setAttribute('stroke', 'white');
            rect.setAttribute('stroke-width', '1');

            // Crear texto para el número de plaza
            const text = document.createElementNS("http://www.w3.org/2000/svg", "text");
            text.setAttribute('x', x + plazaWidth/2);
            text.setAttribute('y', y + plazaHeight/2);
            text.setAttribute('text-anchor', 'middle');
            text.setAttribute('dominant-baseline', 'middle');
            text.setAttribute('fill', 'white');
            text.setAttribute('font-size', '14');
            text.textContent = plaza.numeroPlaza;

            // Añadir eventos de interacción
            g.addEventListener('contextmenu', function(e) {
                e.preventDefault();
                selectedPlazaId = plaza.idPlaza;
                selectedPlazaElement = g;
                
                // Actualizar texto del menú contextual
                document.getElementById('plazaSeleccionada').textContent = plaza.numeroPlaza;
                
                // Posicionar y mostrar menú contextual
                contextMenu.style.left = `${e.pageX}px`;
                contextMenu.style.top = `${e.pageY}px`;
                contextMenu.classList.remove('hidden');
            });

            // Añadir elementos al grupo y al SVG
            g.appendChild(rect);
            g.appendChild(text);
            svg.appendChild(g);
        });
    }
});
</script>
@endpush

</x-app-layout>
