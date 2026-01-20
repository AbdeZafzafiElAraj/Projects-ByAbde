<x-app-layout>
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden">
            <!-- Encabezado -->
            <div class="relative h-48 bg-gradient-to-r from-blue-500 to-blue-600">
                <div class="absolute inset-0 bg-black opacity-20"></div>
                <div class="absolute bottom-0 left-0 p-6">
                    <h1 class="text-3xl font-bold text-white mb-2">
                        Tarifa para {{ ucfirst($tarifa->tipoVehiculo) }}
                    </h1>
                    <p class="text-blue-100">
                        {{ $tarifa->parking->nombreParking }}
                    </p>
                </div>
            </div>

            <!-- Contenido principal -->
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Detalles de la tarifa -->
                    <div class="space-y-6">
                        <!-- Precio -->
                        <div class="bg-blue-50 dark:bg-blue-900/30 rounded-lg p-6">
                            <h2 class="text-lg font-semibold text-blue-800 dark:text-blue-200 mb-2">
                                Precio por Hora
                            </h2>
                            <div class="text-4xl font-bold text-blue-600 dark:text-blue-400">
                                {{ number_format($tarifa->precio, 2) }}€
                            </div>
                        </div>

                        <!-- Información del vehículo -->
                        <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-6">
                            <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">
                                Tipo de Vehículo
                            </h2>
                            <div class="flex items-center space-x-4">
                                <img src="{{ asset('images/' . $tarifa->tipoVehiculo . '.jpg') }}" 
                                     alt="{{ ucfirst($tarifa->tipoVehiculo) }}" 
                                     class="w-24 h-24 object-cover rounded-lg">
                                <div>
                                    <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-200">
                                        {{ ucfirst($tarifa->tipoVehiculo) }}
                                    </h3>
                                    <p class="text-gray-600 dark:text-gray-400 mt-1">
                                        {{ $tarifa->descripcion ?? 'Sin descripción disponible' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Información del parking -->
                    <div class="space-y-6">
                        <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-6">
                            <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">
                                Detalles del Parking
                            </h2>
                            <div class="space-y-3">
                                <p class="flex items-center text-gray-600 dark:text-gray-400">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                              d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                              d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    {{ $tarifa->parking->direccionParking }}
                                </p>
                                <p class="flex items-center text-gray-600 dark:text-gray-400">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                              d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    {{ $tarifa->parking->horaAperturaParking }} - {{ $tarifa->parking->horaCierreParking }}
                                </p>
                            </div>
                        </div>

                        <!-- Información adicional -->
                        <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-6">
                            <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">
                                Información Adicional
                            </h2>
                            <div class="space-y-2 text-gray-600 dark:text-gray-400">
                                <p>Última actualización: {{ $tarifa->updated_at->format('d/m/Y H:i') }}</p>
                                <p>Creado: {{ $tarifa->created_at->format('d/m/Y') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Botones de acción -->
                <div class="flex justify-end space-x-3 mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <a href="{{ route('tarifas.index') }}" 
                       class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 transition-colors duration-200">
                        Volver
                    </a>
                    @if(Auth::user()->role === 'admin')
                        <a href="{{ route('tarifas.edit', $tarifa->idTarifa) }}" 
                           class="px-4 py-2 bg-yellow-100 text-yellow-700 rounded-md hover:bg-yellow-200 transition-colors duration-200">
                            Editar
                        </a>
                        <form action="{{ route('tarifas.destroy', $tarifa->idTarifa) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    onclick="return confirm('¿Estás seguro de que quieres eliminar esta tarifa?')"
                                    class="px-4 py-2 bg-red-100 text-red-700 rounded-md hover:bg-red-200 transition-colors duration-200">
                                Eliminar
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
</x-app-layout>