<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <!-- Encabezado -->
                    <div class="flex items-center mb-6">
                        <svg class="w-8 h-8 text-blue-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Crear Nueva Planta</h1>
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

                    <form action="{{ route('plantas.store') }}" method="POST" class="space-y-6">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Nombre de la Planta -->
                            <div>
                                <label for="nombrePlanta" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Nombre de la Planta
                                </label>
                                <input type="text" name="nombrePlanta" id="nombrePlanta" 
                                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                       value="{{ old('nombrePlanta') }}" required>
                            </div>

                            <!-- Parking -->
                            <div>
                                <label for="idParkingPlanta" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Parking
                                </label>
                                <select name="idParkingPlanta" id="idParkingPlanta" 
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                        required>
                                    <option value="">Seleccione un parking</option>
                                    @foreach($parkings as $parking)
                                        <option value="{{ $parking->idParking }}" {{ old('idParkingPlanta') == $parking->idParking ? 'selected' : '' }}>
                                            {{ $parking->nombreParking }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Estado -->
                            <div>
                                <label for="estadoPlanta" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Estado
                                </label>
                                <select name="estadoPlanta" id="estadoPlanta" 
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                        required>
                                    <option value="abierta" {{ old('estadoPlanta') == 'abierta' ? 'selected' : '' }}>Abierta</option>
                                    <option value="cerrada" {{ old('estadoPlanta') == 'cerrada' ? 'selected' : '' }}>Cerrada</option>
                                </select>
                            </div>

                            <!-- Capacidad Máxima -->
                            <div>
                                <label for="capacidadMaximaPlanta" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Capacidad Máxima
                                </label>
                                <input type="number" name="capacidadMaximaPlanta" id="capacidadMaximaPlanta" 
                                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                       value="{{ old('capacidadMaximaPlanta') }}" required min="1">
                            </div>
                        </div>

                        <!-- Botones de acción -->
                        <div class="flex justify-end space-x-3 pt-6">
                            <a href="{{ route('plantas.index') }}" 
                               class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md inline-flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                                </svg>
                                Cancelar
                            </a>
                            <button type="submit" 
                                    class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md inline-flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Crear Planta
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>