<x-app-layout>

<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden">
        <div class="p-6">
            <!-- Encabezado -->
            <div class="flex justify-between items-center mb-6">
                <div class="flex items-center">
                    <svg class="w-8 h-8 text-blue-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-14 0l2-2m12 0l-2-2"/>
                    </svg>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                        Detalles de la Plaza {{ $plaza->numeroPlaza }}
                    </h1>
                </div>
                <span class="px-4 py-2 rounded-full text-sm font-medium
                    @if($plaza->estadoPlaza == 'libre') bg-emerald-100 text-emerald-800
                    @elseif($plaza->estadoPlaza == 'ocupada') bg-rose-100 text-rose-800
                    @elseif($plaza->estadoPlaza == 'cerrada') bg-amber-100 text-amber-800
                    @else bg-purple-100 text-purple-800
                    @endif">
                    {{ ucfirst($plaza->estadoPlaza) }}
                </span>
            </div>

            <!-- Información Principal -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div class="space-y-4">
                    <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">Información Básica</h2>
                        <div class="space-y-2">
                            <p class="text-sm text-gray-600 dark:text-gray-300">
                                <span class="font-medium">Número:</span> {{ $plaza->numeroPlaza }}
                            </p>
                            <p class="text-sm text-gray-600 dark:text-gray-300">
                                <span class="font-medium">Tipo de Vehículo:</span> {{ ucfirst($plaza->tipoVehiculo) }}
                            </p>
                            <p class="text-sm text-gray-600 dark:text-gray-300">
                                <span class="font-medium">Matrícula:</span> {{ $plaza->matricula ?: 'No asignada' }}
                            </p>
                        </div>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">Ubicación</h2>
                        <div class="space-y-2">
                            <p class="text-sm text-gray-600 dark:text-gray-300">
                                <span class="font-medium">Parking:</span> {{ $plaza->planta->parking->nombreParking }}
                            </p>
                            <p class="text-sm text-gray-600 dark:text-gray-300">
                                <span class="font-medium">Planta:</span> {{ $plaza->planta->nombrePlanta }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">Coordenadas</h2>
                    @if($plaza->x1)
                        <div class="grid grid-cols-2 gap-4">
                            <div class="bg-white dark:bg-gray-600 p-3 rounded-lg">
                                <p class="text-sm text-gray-600 dark:text-gray-300">
                                    <span class="font-medium">Punto 1:</span><br>
                                    X: {{ $plaza->x1 }}, Y: {{ $plaza->y1 }}
                                </p>
                            </div>
                            <div class="bg-white dark:bg-gray-600 p-3 rounded-lg">
                                <p class="text-sm text-gray-600 dark:text-gray-300">
                                    <span class="font-medium">Punto 2:</span><br>
                                    X: {{ $plaza->x2 }}, Y: {{ $plaza->y2 }}
                                </p>
                            </div>
                            <div class="bg-white dark:bg-gray-600 p-3 rounded-lg">
                                <p class="text-sm text-gray-600 dark:text-gray-300">
                                    <span class="font-medium">Punto 3:</span><br>
                                    X: {{ $plaza->x3 }}, Y: {{ $plaza->y3 }}
                                </p>
                            </div>
                            <div class="bg-white dark:bg-gray-600 p-3 rounded-lg">
                                <p class="text-sm text-gray-600 dark:text-gray-300">
                                    <span class="font-medium">Punto 4:</span><br>
                                    X: {{ $plaza->x4 }}, Y: {{ $plaza->y4 }}
                                </p>
                            </div>
                        </div>
                    @else
                        <p class="text-sm text-gray-500 dark:text-gray-400">No hay coordenadas asignadas</p>
                    @endif
                </div>
            </div>

            <!-- Botones de Acción -->
            <div class="flex flex-col sm:flex-row gap-3 mt-6">
                <a href="{{ route('plazas.index') }}" 
                   class="inline-flex justify-center items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"/>
                    </svg>
                    Volver al Listado
                </a>
                @if(Auth::user()->isAdmin)
                    <a href="{{ route('plazas.edit', $plaza->idPlaza) }}" 
                       class="inline-flex justify-center items-center px-4 py-2 bg-yellow-100 text-yellow-700 rounded-md hover:bg-yellow-200 transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Editar Plaza
                    </a>
                    <form action="{{ route('plazas.destroy', $plaza->idPlaza) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                onclick="return confirm('¿Estás seguro de que deseas eliminar esta plaza?')"
                                class="inline-flex justify-center items-center px-4 py-2 bg-red-100 text-red-700 rounded-md hover:bg-red-200 transition-colors">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Eliminar Plaza
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>
</x-app-layout>