<x-app-layout>
    <div class="container mx-auto px-4 py-8">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden">
            <div class="p-6">
                <!-- Encabezado con información general -->
                <div class="mb-8">
                    <div class="flex justify-between items-center mb-6">
                        <div class="flex items-center">
                            <svg class="w-8 h-8 text-blue-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                            <div>
                                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ $planta->nombrePlanta }}
                                    <span class="px-3 py-1 rounded-full text-sm font-medium
                                    {{ $planta->estadoPlanta === 'abierta' ? 'bg-green-100 text-green-800' : 
                                    ($planta->estadoPlanta === 'cerrada' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                    {{ ucfirst($planta->estadoPlanta) }}
                                </span>
                                </h1>
                                <p class="text-gray-500 dark:text-gray-400">{{ $planta->parking->nombreParking }}</p>
                            </div>
                        </div>
                        <a href="{{ route('plantas.index') }}" 
                           class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                            Volver al listado
                        </a>
                    </div>

                    <!-- Tarjetas de estadísticas -->
                    <div class="grid grid-cols-1 md:grid-cols-6 gap-6 mb-6">
                        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg p-6 text-white">
                            <div class="text-sm uppercase tracking-wide">Capacidad Máxima</div>
                            <div class="text-3xl font-bold mt-2">{{ $planta->capacidadMaximaPlanta }}</div>
                        </div>
                        <div class="bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-lg p-6 text-white">
                            <div class="text-sm uppercase tracking-wide">Plazas Totales</div>
                            <div class="text-3xl font-bold mt-2">{{ $planta->plazas->count() }}</div>
                        </div>
                        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg p-6 text-white">
                            <div class="text-sm uppercase tracking-wide">Plazas Disponibles</div>
                            <div class="text-3xl font-bold mt-2">{{ $planta->plazas->where('estadoPlaza', 'libre')->count() }}</div>
                        </div>
                        <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-lg p-6 text-white">
                            <div class="text-sm uppercase tracking-wide">Plazas Ocupadas</div>
                            <div class="text-3xl font-bold mt-2">{{ $planta->plazas->where('estadoPlaza', 'ocupada')->count() }}</div>
                        </div>
                        <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-lg p-6 text-white">
                            <div class="text-sm uppercase tracking-wide">Plazas Cerradas</div>
                            <div class="text-3xl font-bold mt-2">{{ $planta->plazas->where('estadoPlaza', 'cerrada')->count() }}</div>
                        </div>
                        <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-lg p-6 text-white">
                            <div class="text-sm uppercase tracking-wide">Plazas en Mantenimiento</div>
                            <div class="text-3xl font-bold mt-2">{{ $planta->plazas->where('estadoPlaza', 'mantenimiento')->count() }}</div>
                        </div>
                    </div>

                    <!-- Filtros para las plazas -->
                    <div class="mt-8">
                        <form action="{{ route('plantas.show', $planta->idPlanta) }}" method="GET" class="mb-6">
                            <div class="flex flex-wrap gap-4 bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                <div class="flex-1 min-w-[200px]">
                                    <label class="block text-sm font-medium mb-2">Estado de Plaza</label>
                                    <select name="estado" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                        <option value="">Todos los estados</option>
                                        <option value="libre" {{ request('estado') == 'libre' ? 'selected' : '' }}>Libres</option>
                                        <option value="ocupada" {{ request('estado') == 'ocupada' ? 'selected' : '' }}>Ocupadas</option>
                                        <option value="cerrada" {{ request('estado') == 'cerrada' ? 'selected' : '' }}>Cerradas</option>
                                        <option value="mantenimiento" {{ request('estado') == 'mantenimiento' ? 'selected' : '' }}>Mantenimiento</option>
                                    </select>
                                </div>

                                <div class="flex-1 min-w-[200px]">
                                    <label class="block text-sm font-medium mb-2">Elementos por página</label>
                                    <select name="per_page" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                        <option value="12" {{ request('per_page') == 12 ? 'selected' : '' }}>12 plazas</option>
                                        <option value="24" {{ request('per_page') == 24 ? 'selected' : '' }}>24 plazas</option>
                                        <option value="48" {{ request('per_page') == 48 ? 'selected' : '' }}>48 plazas</option>
                                        <option value="96" {{ request('per_page') == 96 ? 'selected' : '' }}>96 plazas</option>
                                    </select>
                                </div>

                                <div class="flex items-end space-x-2">
                                    <button type="submit" 
                                            class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 inline-flex items-center">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                                        </svg>
                                        Filtrar
                                    </button>
                                    <a href="{{ route('plantas.show', $planta->idPlanta) }}" 
                                       class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600 inline-flex items-center">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                        </svg>
                                        Limpiar
                                    </a>
                                </div>
                            </div>
                        </form>

                        <!-- Grid de plazas con paginación -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                            @forelse($plazasFiltradas as $plaza)
                                <div class="bg-white dark:bg-gray-700 rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-all duration-200">
                                    <div class="p-4">
                                        <div class="flex justify-between items-center mb-2">
                                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                                Plaza {{ $plaza->numeroPlaza }}
                                            </h3>
                                            <span class="px-2 py-1 rounded-full text-xs font-medium
                                                {{ $plaza->estadoPlaza === 'libre' ? 'bg-green-100 text-green-800' : 
                                                   ($plaza->estadoPlaza === 'ocupada' ? 'bg-red-100 text-red-800' : 
                                                    'bg-yellow-100 text-yellow-800') }}">
                                                {{ ucfirst($plaza->estadoPlaza) }}
                                            </span>
                                        </div>
                                        @if($plaza->estadoPlaza === 'ocupada')
                                            <div class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                                                <p><span class="font-medium">Matrícula:</span> {{ $plaza->matricula ?? 'N/A' }}</p>
                                                <p><span class="font-medium">Tipo:</span> {{ ucfirst($plaza->tipoVehiculo) ?? 'N/A' }}</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <div class="col-span-full">
                                    <div class="text-center p-6 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                        <p class="text-gray-500 dark:text-gray-400">No hay plazas que coincidan con los filtros aplicados.</p>
                                    </div>
                                </div>
                            @endforelse
                        </div>

                        <!-- Paginación -->
                        <div class="mt-6">
                            {{ $plazasFiltradas->appends(request()->query())->links() }}
                        </div>
                    </div>

                    <!-- Botones de acción -->
                    <div class="mt-8 flex justify-end space-x-4">
                        <a href="{{ route('plantas.edit', $planta->idPlanta) }}" 
                           class="inline-flex items-center px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Editar Planta
                        </a>
                        <form action="{{ route('plantas.destroy', $planta->idPlanta) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors"
                                    onclick="return confirm('¿Estás seguro de que quieres eliminar esta planta?')">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                                Eliminar Planta
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        window.Echo.channel('plantas')
            .listen('PlazaUpdated', (event) => {
                location.reload();
            });
    </script>
    @endpush
</x-app-layout>