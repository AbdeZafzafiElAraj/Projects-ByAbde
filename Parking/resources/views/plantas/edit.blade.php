<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <!-- Encabezado -->
                    <div class="flex items-center mb-6">
                        <svg class="w-8 h-8 text-yellow-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Editar Planta: {{ $planta->nombrePlanta }}</h1>
                    </div>

                    @if ($errors->any())
                    <div class="mb-6 bg-red-50 dark:bg-red-900/50 border-l-4 border-red-500 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-800 dark:text-red-200">Hay errores en el formulario:</h3>
                                <ul class="mt-2 list-disc list-inside text-sm text-red-700 dark:text-red-300">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                    @endif

                    <form action="{{ route('plantas.update', $planta->idPlanta) }}" method="POST" class="space-y-6">
                        @csrf
                        @method('PUT')
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            <!-- Parking -->
                            <div>
                                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg border border-gray-200 dark:border-gray-600">
                                    <div class="flex items-center space-x-2">
                                        <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                        </svg>
                                        <span class="text-gray-700 dark:text-gray-300 font-medium">{{ $planta->parking->nombreParking }}</span>
                                    </div>
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                        {{ $planta->parking->direccionParking }}
                                    </p>
                                </div>
                            </div>

                            <!-- Estado -->
                            <div>
                                <label for="estadoPlanta" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Estado
                                </label>
                                <select name="estadoPlanta" id="estadoPlanta" 
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                        required>
                                    <option value="abierta" {{ old('estadoPlanta', $planta->estadoPlanta) == 'abierta' ? 'selected' : '' }}>Abierta</option>
                                    <option value="cerrada" {{ old('estadoPlanta', $planta->estadoPlanta) == 'cerrada' ? 'selected' : '' }}>Cerrada</option>
                                </select>
                            </div>

                            <!-- Nombre de la Planta -->
                            <div>
                                <label for="nombrePlanta" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Nombre de la Planta
                                </label>
                                <input type="text" name="nombrePlanta" id="nombrePlanta" 
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                        value="{{ old('nombrePlanta', $planta->nombrePlanta) }}" required>
                            </div>

                            <!-- Capacidad Máxima -->
                            <div>
                                <label for="capacidadMaximaPlanta" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Capacidad Máxima
                                </label>
                                <input type="number" name="capacidadMaximaPlanta" id="capacidadMaximaPlanta" 
                                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                       value="{{ old('capacidadMaximaPlanta', $planta->capacidadMaximaPlanta) }}" required min="1">
                            </div>
                        </div>

                        <!-- Botones de acción -->
                        <div class="flex justify-end space-x-3 pt-6">
                            <a href="{{ route('plantas.show', $planta->idPlanta) }}" 
                               class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md inline-flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                                </svg>
                                Cancelar
                            </a>
                            <button type="submit" 
                                    class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-md inline-flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Guardar Cambios
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>