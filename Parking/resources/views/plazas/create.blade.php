<x-app-layout>

<div class="container mx-auto px-4 py-8">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden">
        <div class="p-6">
            <!-- Encabezado -->
            <div class="flex justify-between items-center mb-6">
                <div class="flex items-center">
                    <svg class="w-8 h-8 text-blue-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Nueva Plaza</h1>
                </div>
            </div>

            @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Error:</strong>
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form action="{{ route('plazas.store') }}" method="POST">
                @csrf
                
                <!-- Información Básica -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <!-- Número de Plaza -->
                    <div>
                        <label for="numeroPlaza" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Número de Plaza
                        </label>
                        <div class="relative">
                            <input type="text" 
                                   id="numeroPlaza" 
                                   disabled
                                   class="w-full rounded-md border-gray-300 bg-gray-100 dark:bg-gray-600 dark:border-gray-500 dark:text-gray-300 cursor-not-allowed"
                                   placeholder="Se asignará automáticamente">
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                Se asignará automáticamente el siguiente número disponible
                            </p>
                        </div>
                    </div>

                    <!-- Estado -->
                    <div>
                        <label for="estadoPlaza" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Estado *
                        </label>
                        <select name="estadoPlaza" 
                                id="estadoPlaza" 
                                required
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            <option value="">Seleccione un estado</option>
                            <option value="libre" {{ old('estadoPlaza', 'libre') == 'libre' ? 'selected' : '' }}>Libre</option>
                            <option value="ocupada" {{ old('estadoPlaza') == 'ocupada' ? 'selected' : '' }}>Ocupada</option>
                            <option value="mantenimiento" {{ old('estadoPlaza') == 'mantenimiento' ? 'selected' : '' }}>Mantenimiento</option>
                        </select>
                    </div>
                </div>

                <!-- Ubicación -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <!-- Parking -->
                    <div>
                        <label for="parking_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Parking *
                        </label>
                        <select name="parking_id" 
                                id="parking_id" 
                                required
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            <option value="">Seleccione un parking</option>
                            @foreach($parkings as $parking)
                                <option value="{{ $parking->idParking }}" {{ old('parking_id') == $parking->idParking ? 'selected' : '' }}>
                                    {{ $parking->nombreParking }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Planta -->
                    <div>
                        <label for="idPlantaPlaza" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Planta *
                        </label>
                        <select name="idPlantaPlaza" 
                                id="idPlantaPlaza" 
                                required
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            <option value="">Primero seleccione un parking</option>
                        </select>
                    </div>
                </div>

                <!-- Coordenadas -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <!-- Punto 1 -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Punto 1</label>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <input type="number" step="0.01" name="x1" placeholder="X1" value="{{ old('x1') }}"
                                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>
                            <div>
                                <input type="number" step="0.01" name="y1" placeholder="Y1" value="{{ old('y1') }}"
                                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>
                        </div>
                    </div>

                    <!-- Punto 2 -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Punto 2</label>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <input type="number" step="0.01" name="x2" placeholder="X2" value="{{ old('x2') }}"
                                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>
                            <div>
                                <input type="number" step="0.01" name="y2" placeholder="Y2" value="{{ old('y2') }}"
                                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>
                        </div>
                    </div>

                    <!-- Punto 3 -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Punto 3</label>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <input type="number" step="0.01" name="x3" placeholder="X3" value="{{ old('x3') }}"
                                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>
                            <div>
                                <input type="number" step="0.01" name="y3" placeholder="Y3" value="{{ old('y3') }}"
                                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>
                        </div>
                    </div>

                    <!-- Punto 4 -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Punto 4</label>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <input type="number" step="0.01" name="x4" placeholder="X4" value="{{ old('x4') }}"
                                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>
                            <div>
                                <input type="number" step="0.01" name="y4" placeholder="Y4" value="{{ old('y4') }}"
                                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Botones -->
                <div class="flex justify-end space-x-2">
                    <a href="{{ route('plazas.index') }}" 
                       class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200">
                        Cancelar
                    </a>
                    <button type="submit" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                        Crear Plaza
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('parking_id').addEventListener('change', function() {
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

document.getElementById('estadoPlaza').addEventListener('change', function() {
    const matriculaContainer = document.getElementById('matriculaContainer');
    const matriculaInput = document.getElementById('matricula');
    
    if (this.value === 'ocupada') {
        matriculaContainer.style.display = 'block';
        matriculaInput.required = true;
    } else {
        matriculaContainer.style.display = 'none';
        matriculaInput.required = false;
        matriculaInput.value = '';
    }
});
</script>
</x-app-layout>