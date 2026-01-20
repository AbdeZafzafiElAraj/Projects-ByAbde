<x-app-layout>

<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden">
        <div class="p-6">
            <!-- Encabezado -->
            <div class="flex items-center mb-6">
                <svg class="w-8 h-8 text-blue-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                    Editar Plaza {{ $plaza->numeroPlaza }}
                </h1>
            </div>

            @if ($errors->any())
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
                    <p class="font-bold">Por favor corrige los siguientes errores:</p>
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('plazas.update', $plaza->idPlaza) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Ubicación -->
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

                <!-- Información Básica -->
                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Información Básica</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="numeroPlaza" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Número de Plaza
                            </label>
                            <input type="number" 
                                   name="numeroPlaza" 
                                   id="numeroPlaza" 
                                   value="{{ old('numeroPlaza', $plaza->numeroPlaza) }}"
                                   class="w-full rounded-md border-gray-300 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                            <p class="mt-1 text-sm text-gray-500">El número debe ser único en este parking</p>
                        </div>

                        <div>
                            <label for="tipoVehiculo" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Tipo de Vehículo
                            </label>
                            <select name="tipoVehiculo" id="tipoVehiculo" 
                                    class="w-full rounded-md border-gray-300 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                                <option value="" {{ old('tipoVehiculo', $plaza->tipoVehiculo) == '' ? 'selected' : '' }}>Selecciona un vehículo</option>
                                <option value="coche" {{ old('tipoVehiculo', $plaza->tipoVehiculo) == 'coche' ? 'selected' : '' }}>Coche</option>
                                <option value="moto" {{ old('tipoVehiculo', $plaza->tipoVehiculo) == 'moto' ? 'selected' : '' }}>Moto</option>
                                <option value="furgoneta" {{ old('tipoVehiculo', $plaza->tipoVehiculo) == 'furgoneta' ? 'selected' : '' }}>Furgoneta</option>
                            </select>
                        </div>

                        <div>
                            <label for="estadoPlaza" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Estado
                            </label>
                            <select name="estadoPlaza" id="estadoPlaza" 
                                    class="w-full rounded-md border-gray-300 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                                <option value="libre" {{ old('estadoPlaza', $plaza->estadoPlaza) == 'libre' ? 'selected' : '' }}>Libre</option>
                                <option value="ocupada" {{ old('estadoPlaza', $plaza->estadoPlaza) == 'ocupada' ? 'selected' : '' }}>Ocupada</option>
                                <option value="cerrada" {{ old('estadoPlaza', $plaza->estadoPlaza) == 'cerrada' ? 'selected' : '' }}>Cerrada</option>
                                <option value="mantenimiento" {{ old('estadoPlaza', $plaza->estadoPlaza) == 'mantenimiento' ? 'selected' : '' }}>Mantenimiento</option>
                            </select>
                        </div>

                        <div>
                            <label for="matricula" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Matrícula
                            </label>
                            <input type="text" name="matricula" id="matricula" 
                                   value="{{ old('matricula', $plaza->matricula) }}"
                                   pattern="[0-9]{4}[A-Za-z]{3}"
                                   maxlength="7"
                                   placeholder="1234ABC"
                                   oninput="this.value = this.value.toUpperCase()"
                                   class="w-full rounded-md border-gray-300 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Formato: 4 números seguidos de 3 letras (ej: 1234ABC)</p>
                        </div>
                    </div>
                </div>

                    <!-- Coordenadas -->
                    <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Coordenadas</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Punto 1 -->
                        <div class="bg-white dark:bg-gray-600 p-3 rounded-lg">
                            <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Punto 1</h3>
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label for="x1" class="block text-xs text-gray-500 dark:text-gray-400">X1</label>
                                    <input type="number" step="0.01" name="x1" id="x1" 
                                           value="{{ old('x1', $plaza->x1) }}"
                                           class="w-full rounded-md border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm">
                                </div>
                                <div>
                                    <label for="y1" class="block text-xs text-gray-500 dark:text-gray-400">Y1</label>
                                    <input type="number" step="0.01" name="y1" id="y1" 
                                           value="{{ old('y1', $plaza->y1) }}"
                                           class="w-full rounded-md border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm">
                                </div>
                            </div>
                        </div>

                        <!-- Punto 2 -->
                        <div class="bg-white dark:bg-gray-600 p-3 rounded-lg">
                            <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Punto 2</h3>
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label for="x2" class="block text-xs text-gray-500 dark:text-gray-400">X2</label>
                                    <input type="number" step="0.01" name="x2" id="x2" 
                                           value="{{ old('x2', $plaza->x2) }}"
                                           class="w-full rounded-md border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm">
                                </div>
                                <div>
                                    <label for="y2" class="block text-xs text-gray-500 dark:text-gray-400">Y2</label>
                                    <input type="number" step="0.01" name="y2" id="y2" 
                                           value="{{ old('y2', $plaza->y2) }}"
                                           class="w-full rounded-md border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm">
                                </div>
                            </div>
                        </div>

                        <!-- Punto 3 -->
                        <div class="bg-white dark:bg-gray-600 p-3 rounded-lg">
                            <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Punto 3</h3>
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label for="x3" class="block text-xs text-gray-500 dark:text-gray-400">X3</label>
                                    <input type="number" step="0.01" name="x3" id="x3" 
                                           value="{{ old('x3', $plaza->x3) }}"
                                           class="w-full rounded-md border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm">
                                </div>
                                <div>
                                    <label for="y3" class="block text-xs text-gray-500 dark:text-gray-400">Y3</label>
                                    <input type="number" step="0.01" name="y3" id="y3" 
                                           value="{{ old('y3', $plaza->y3) }}"
                                           class="w-full rounded-md border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm">
                                </div>
                            </div>
                        </div>

                        <!-- Punto 4 -->
                        <div class="bg-white dark:bg-gray-600 p-3 rounded-lg">
                            <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Punto 4</h3>
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label for="x4" class="block text-xs text-gray-500 dark:text-gray-400">X4</label>
                                    <input type="number" step="0.01" name="x4" id="x4" 
                                           value="{{ old('x4', $plaza->x4) }}"
                                           class="w-full rounded-md border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm">
                                </div>
                                <div>
                                    <label for="y4" class="block text-xs text-gray-500 dark:text-gray-400">Y4</label>
                                    <input type="number" step="0.01" name="y4" id="y4" 
                                           value="{{ old('y4', $plaza->y4) }}"
                                           class="w-full rounded-md border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Botones de acción -->
                <div class="flex flex-col sm:flex-row gap-3">
                    <button type="submit" 
                            class="inline-flex justify-center items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Guardar Cambios
                    </button>
                    <a href="{{ route('plazas.index') }}" 
                       class="inline-flex justify-center items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        Cancelar
                    </a>
                </div>
            </form>


            <script>
                // Manejo del cambio de estado
                document.getElementById('estadoPlaza').addEventListener('change', function() {
                    const estadoSeleccionado = this.value;
                    const tipoVehiculoSelect = document.getElementById('tipoVehiculo');
                    const matriculaInput = document.getElementById('matricula');

                    if (estadoSeleccionado === 'cerrada' || estadoSeleccionado === 'mantenimiento') {
                        tipoVehiculoSelect.value = '';
                        matriculaInput.value = '';
                        
                        // Deshabilitar los campos
                        tipoVehiculoSelect.disabled = true;
                        matriculaInput.disabled = true;
                    } else {
                        // Habilitar los campos
                        tipoVehiculoSelect.disabled = false;
                        matriculaInput.disabled = false;
                    }
                });

                // Manejo del cambio de tipo de vehículo
                document.getElementById('tipoVehiculo').addEventListener('change', function() {
                    const tipoVehiculoSeleccionado = this.value;
                    const estadoPlazaSelect = document.getElementById('estadoPlaza');

                    if (tipoVehiculoSeleccionado !== '') {
                        // Si se selecciona un vehículo, cambiar estado a ocupada
                        estadoPlazaSelect.value = 'ocupada';
                    } else {
                        // Si se deselecciona el vehículo, cambiar estado a libre
                        estadoPlazaSelect.value = 'libre';
                    }
                });

                // Validación de matrícula
                document.getElementById('matricula').addEventListener('input', function(e) {
                    let value = e.target.value;
                    
                    // Eliminar caracteres no válidos
                    value = value.replace(/[^0-9A-Za-z]/g, '');
                    
                    // Separar números y letras
                    const numeros = value.replace(/[^0-9]/g, '');
                    const letras = value.replace(/[^A-Za-z]/g, '');
                    
                    // Limitar a 4 números y 3 letras
                    const numerosLimitados = numeros.slice(0, 4);
                    const letrasLimitadas = letras.slice(0, 3);
                    
                    // Combinar y convertir a mayúsculas
                    this.value = (numerosLimitados + letrasLimitadas).toUpperCase();
                });

                // Ejecutar la verificación al cargar la página
                document.getElementById('estadoPlaza').dispatchEvent(new Event('change'));
            </script>
        </div>
    </div>
</div>
</x-app-layout>