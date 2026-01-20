<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex justify-between items-center mb-6">
                        <div class="flex items-center">
                            <svg class="w-8 h-8 text-blue-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            <h1 class="text-2xl font-bold">Editar Tarifa</h1>
                        </div>
                    </div>

                    @if ($errors->any())
                        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                            <p class="font-bold">Por favor corrige los siguientes errores:</p>
                            <ul class="list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('tarifas.update', $tarifa->idTarifa) }}" method="POST" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <!-- Parking Selector -->
                        <div class="relative">
                            <label for="idParkingTarifa" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Parking
                            </label>
                            <select name="idParkingTarifa" id="idParkingTarifa" required
                                    class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 rounded-md dark:bg-gray-700 dark:border-gray-600">
                                <option value="">Selecciona un parking</option>
                                @foreach($parkings as $parking)
                                    <option value="{{ $parking->idParking }}" 
                                            {{ old('idParkingTarifa', $tarifa->idParkingTarifa) == $parking->idParking ? 'selected' : '' }}>
                                        {{ $parking->nombreParking }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Tipo de Vehículo -->
                        <div class="grid grid-cols-3 gap-4">
                            <div class="relative">
                                <input type="radio" name="tipoVehiculo" id="coche" value="coche" 
                                       class="peer hidden" {{ old('tipoVehiculo', $tarifa->tipoVehiculo) === 'coche' ? 'checked' : '' }}>
                                <label for="coche" class="block p-4 text-center border rounded-lg cursor-pointer peer-checked:border-blue-500 peer-checked:bg-blue-50 hover:bg-gray-50">
                                    <img src="{{ asset('images/coche.jpg') }}" alt="Coche" class="w-20 h-20 mx-auto mb-2 object-cover rounded">
                                    <span class="block text-sm font-medium">Coche</span>
                                </label>
                            </div>
                            <div class="relative">
                                <input type="radio" name="tipoVehiculo" id="moto" value="moto" 
                                       class="peer hidden" {{ old('tipoVehiculo', $tarifa->tipoVehiculo) === 'moto' ? 'checked' : '' }}>
                                <label for="moto" class="block p-4 text-center border rounded-lg cursor-pointer peer-checked:border-blue-500 peer-checked:bg-blue-50 hover:bg-gray-50">
                                    <img src="{{ asset('images/moto.jpg') }}" alt="Moto" class="w-20 h-20 mx-auto mb-2 object-cover rounded">
                                    <span class="block text-sm font-medium">Moto</span>
                                </label>
                            </div>
                            <div class="relative">
                                <input type="radio" name="tipoVehiculo" id="furgoneta" value="furgoneta" 
                                       class="peer hidden" {{ old('tipoVehiculo', $tarifa->tipoVehiculo) === 'furgoneta' ? 'checked' : '' }}>
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
                                       value="{{ old('precio', $tarifa->precio) }}"
                                       required
                                       class="block w-full pl-7 pr-12 py-2 border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600">
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
                                      class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600">{{ old('descripcion', $tarifa->descripcion) }}</textarea>
                        </div>

                        <!-- Botones -->
                        <div class="flex justify-end space-x-2">
                            <a href="{{ route('tarifas.index') }}" 
                               class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600">
                                Cancelar
                            </a>
                            <button type="submit" 
                                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                                Actualizar Tarifa
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const parkingSelect = document.getElementById('idParkingTarifa');
    
    if (parkingSelect) {
        parkingSelect.addEventListener('change', function() {
            const parkingId = this.value;
            const plantaSelect = document.getElementById('idPlantaPlaza');
            
            plantaSelect.innerHTML = '<option value="">Seleccione una planta</option>';
            
            if (parkingId) {
                fetch(`/api/parkings/${parkingId}/plantas`)
                    .then(response => response.json())
                    .then(plantas => {
                        plantas.forEach(planta => {
                            const option = document.createElement('option');
                            option.value = planta.idPlanta;
                            option.textContent = planta.nombrePlanta;
                            plantaSelect.appendChild(option);
                        });
                    })
                    .catch(error => console.error('Error:', error));
            }
        });
    } else {
        console.error('El elemento con ID "idParkingTarifa" no fue encontrado.');
    }
});
</script>

</x-app-layout>