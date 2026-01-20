<x-app-layout>
    <div class="container mx-auto px-4 py-8">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden">
            <div class="p-6">
                <!-- Encabezado con estadísticas -->
                <div class="flex justify-between items-center mb-6">
                    <div class="flex items-center">
                        <svg class="w-8 h-8 text-blue-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ $parking->nombreParking }} - Historial de Registros</h1>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Total: {{ $registros->total() }} registros
                            </p>
                        </div>
                    </div>
                    <a href="{{ route('registro-accesos.index') }}" 
                       class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                        <i class="fas fa-arrow-left mr-2"></i>Volver
                    </a>
                </div>

                <!-- Alertas -->
                @if (session('success'))
                    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
                        <p class="font-bold">¡Éxito!</p>
                        <p>{{ session('success') }}</p>
                    </div>
                @endif

                <!-- Panel de Filtros -->
                <div class="mb-6 bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                    <form action="{{ route('registro-accesos.show', $parking->idParking) }}" method="GET" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <!-- Filtro por método de pago -->
                            <div>
                                <label for="metodoPago" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Método de Pago
                                </label>
                                <select id="metodoPago" name="metodoPago" 
                                        class="w-full rounded-md border-gray-300 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                                    @foreach($metodosPago as $valor => $texto)
                                        <option value="{{ $valor }}" {{ $currentMetodoPago == $valor ? 'selected' : '' }}>
                                            {{ $texto }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Ordenar por -->
                            <div>
                                <label for="orderBy" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Ordenar por
                                </label>
                                <select id="orderBy" name="orderBy" 
                                        class="w-full rounded-md border-gray-300 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                                    <option value="fecha" {{ $currentOrderBy == 'fecha' ? 'selected' : '' }}>Fecha</option>
                                    <option value="tarifa" {{ $currentOrderBy == 'tarifa' ? 'selected' : '' }}>Tarifa</option>
                                    <option value="estado" {{ $currentOrderBy == 'estado' ? 'selected' : '' }}>Estado</option>
                                </select>
                            </div>

                            <!-- Dirección -->
                            <div>
                                <label for="orderDirection" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Dirección
                                </label>
                                <select id="orderDirection" name="orderDirection" 
                                        class="w-full rounded-md border-gray-300 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                                    <option value="desc" {{ $currentOrderDirection == 'desc' ? 'selected' : '' }}>Descendente</option>
                                    <option value="asc" {{ $currentOrderDirection == 'asc' ? 'selected' : '' }}>Ascendente</option>
                                </select>
                            </div>

                            <!-- Elementos por página -->
                            <div>
                                <label for="per_page" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Elementos por página
                                </label>
                                <select id="per_page" name="per_page" 
                                        class="w-full rounded-md border-gray-300 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                                    @foreach([10, 25, 50, 100] as $value)
                                        <option value="{{ $value }}" {{ request('per_page', 10) == $value ? 'selected' : '' }}>
                                            {{ $value }} elementos
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="flex justify-end space-x-2">
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                                <i class="fas fa-filter mr-2"></i>Aplicar Filtros
                            </button>
                            <a href="{{ route('registro-accesos.show', $parking->idParking) }}" 
                               class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 transition-colors">
                                <i class="fas fa-undo mr-2"></i>Limpiar
                            </a>
                        </div>
                    </form>
                </div>

                <!-- Grid de Registros -->
                @if($registros->isEmpty())
                    <div class="text-center py-8">
                        <div class="text-gray-500 dark:text-gray-400">
                            No se encontraron registros con los filtros seleccionados.
                        </div>
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($registros as $registro)
                            <div class="bg-white dark:bg-gray-700 rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-all duration-200">
                                <div class="p-6">
                                    <!-- Cabecera del registro -->
                                    <div class="flex justify-between items-start mb-4">
                                        <div>
                                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                                Matrícula: {{ $registro->matricula }}
                                            </h3>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                                Plaza {{ $registro->plaza->numeroPlaza }}
                                            </p>
                                        </div>
                                        <span class="px-3 py-1 rounded-full text-sm font-medium
                                            {{ $registro->estado === 'finalizado' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                                            {{ ucfirst($registro->estado) }}
                                        </span>
                                    </div>

                                    <!-- Detalles del registro -->
                                    <div class="space-y-2 text-sm text-gray-600 dark:text-gray-300">
                                        <p><span class="font-medium">Entrada:</span> {{ \Carbon\Carbon::parse($registro->horaEntrada)->format('d/m/Y H:i') }}</p>
                                        <p><span class="font-medium">Salida:</span> {{ $registro->horaSalida ? \Carbon\Carbon::parse($registro->horaSalida)->format('d/m/Y H:i') : 'En curso' }}</p>
                                        <p><span class="font-medium">Método de Pago:</span> {{ ucfirst($registro->metodoPago) }}</p>
                                        <p><span class="font-medium">Tarifa:</span> {{ number_format($registro->tarifaAplicada, 2) }}€</p>
                                    </div>

                                    <!-- Botón de acción -->
                                    <div class="mt-4">
                                        <a href="{{ route('registro-accesos.ticket', $registro->idRegistroAcceso) }}" 
                                           class="inline-flex items-center justify-center w-full px-4 py-2 bg-blue-100 text-blue-700 rounded-md hover:bg-blue-200 transition-colors">
                                            <i class="fas fa-ticket-alt mr-2"></i>Ver Ticket
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                <!-- Paginación -->
                <div class="mt-6">
                    {{ $registros->withQueryString()->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
