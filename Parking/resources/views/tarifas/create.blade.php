<x-app-layout>
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-2xl mx-auto">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden">
                <div class="p-6">
                    <div class="flex items-center mb-6">
                        <svg class="w-8 h-8 text-blue-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Nueva Tarifa</h1>
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

                    <form action="{{ route('tarifas.store') }}" method="POST" class="space-y-6">
                        @csrf
                        
                        <!-- Parking Selector -->
                        <div class="relative">
                            <label for="idParkingTarifa" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Parking
                            </label>
                            <select name="idParkingTarifa" id="idParkingTarifa" required
                                    class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 rounded-md dark:bg-gray-700 dark:border-gray-600">
                                <option value="">Selecciona un parking</option>
                                @foreach($parkings as $parking)
                                    <option value="{{ $parking->idParking }}">
                                        {{ $parking->nombreParking }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Tipo de Vehículo -->
                        <div class="grid grid-cols-3 gap-4">
                            <div class="relative">
                                <input type="radio" name="tipoVehiculo" id="coche" value="coche" class="peer hidden" checked>
                                <label for="coche" class="block p-4 text-center border rounded-lg cursor-pointer peer-checked:border-blue-500 peer-checked:bg-blue-50 hover:bg-gray-50">
                                    <img src="{{ asset('images/coche.jpg') }}" alt="Coche" class="w-20 h-20 mx-auto mb-2 object-cover rounded">
                                    <span class="block text-sm font-medium">Coche</span>
                                </label>
                            </div>
                            <div class="relative">
                                <input type="radio" name="tipoVehiculo" id="moto" value="moto" class="peer hidden">
                                <label for="moto" class="block p-4 text-center border rounded-lg cursor-pointer peer-checked:border-blue-500 peer-checked:bg-blue-50 hover:bg-gray-50">
                                    <img src="{{ asset('images/moto.jpg') }}" alt="Moto" class="w-20 h-20 mx-auto mb-2 object-cover rounded">
                                    <span class="block text-sm font-medium">Moto</span>
                                </label>
                            </div>
                            <div class="relative">
                                <input type="radio" name="tipoVehiculo" id="furgoneta" value="furgoneta" class="peer hidden">
                                <label for="furgoneta" class="block p-4 text-center border rounded-lg cursor-pointer peer-checked:border-blue-500 peer-checked:bg-blue-50 hover:bg-gray-50">
                                    <img src="{{ asset('images/furgoneta.jpg') }}" alt="Furgoneta" class="w-20 h-20 mx-auto mb-2 object-cover rounded">
                                    <span class="block text-sm font-medium">Furgoneta</span>
                                </label>
                            </div>
                        </div>

                        <!-- Precio -->
                        <div class="relative">
                            <label for="precio" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Precio por Hora
                            </label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">€</span>
                                </div>
                                <input type="number" 
                                       name="precio" 
                                       id="precio" 
                                       step="0.01" 
                                       min="0" 
                                       required
                                       class="block w-full pl-7 pr-12 py-2 border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600"
                                       placeholder="0.00">
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">/hora</span>
                                </div>
                            </div>
                        </div>

                        <!-- Descripción -->
                        <div>
                            <label for="descripcion" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Descripción
                            </label>
                            <textarea name="descripcion" 
                                      id="descripcion" 
                                      rows="3"
                                      class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600"
                                      placeholder="Describe los detalles de la tarifa..."></textarea>
                        </div>

                        <!-- Botones de acción -->
                        <div class="flex justify-end space-x-3 pt-6">
                            <a href="{{ route('tarifas.index') }}" 
                               class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-gray-700 bg-gray-100 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                                Cancelar
                            </a>
                            <button type="submit" 
                                    class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Crear Tarifa
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
