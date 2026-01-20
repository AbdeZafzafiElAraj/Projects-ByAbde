<x-app-layout>
@push('styles')
<style>
.font-digital {
    font-family: 'Digital-7', monospace;
}

.peer:checked + label img {
    transform: scale(1.05);
    transition: transform 0.2s;
}
</style>
@endpush
<div class="min-h-screen bg-gray-100 py-6">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <!-- Mensajes de Error -->
        @if ($errors->any())
            <div class="mb-4 bg-red-50 border-l-4 border-red-500 p-4">
                <div class="font-medium text-red-800">Hay errores en el formulario:</div>
                <ul class="mt-3 list-disc list-inside text-sm text-red-700">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Mensaje de Éxito -->
        @if(session('success'))
            <div class="mb-6">
                <div class="bg-green-50 border-l-4 border-green-500 p-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-800">
                                {{ session('success') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Tarjeta Principal -->
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
            <div class="p-6">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">Nuevo Registro de Acceso</h2>
                <form action="{{ route('registro-accesos.store') }}" method="POST" id="registroForm">
                    @csrf
                    <input type="hidden" name="idParkingAcceso" value="{{ $parking->idParking }}">
                    <input type="hidden" name="horaEntrada" id="horaEntrada" value="{{ $horaEntrada->format('Y-m-d H:i:s') }}">

                    <!-- Información del Parking -->
                    <div class="mb-6 p-4 bg-blue-50 rounded-lg">
                        <h3 class="text-lg font-semibold text-blue-800">{{ $parking->nombreParking }}</h3>
                        <p class="text-sm text-blue-600">Plazas disponibles: {{ $plazasLibres }}</p>
                    </div>

                    <!-- Tipo de Vehículo -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tipo de Vehículo</label>
                        <div class="grid grid-cols-3 gap-6">
                            @foreach(['coche', 'moto', 'furgoneta'] as $tipo)
                                <label class="relative cursor-pointer">
                                    <input type="radio" 
                                           name="tipoVehiculo" 
                                           value="{{ $tipo }}" 
                                           class="vehiculo-radio sr-only peer"
                                           required
                                           @change="console.log('Radio changed:', '{{ $tipo }}')">
                                    <div class="overflow-hidden rounded-lg border-2 peer-checked:border-blue-500 peer-checked:ring-2 peer-checked:ring-blue-500 transition-all hover:shadow-lg">
                                        <img src="{{ asset('images/' . $tipo . '.jpg') }}" 
                                             alt="{{ ucfirst($tipo) }}" 
                                             class="w-full h-32 object-cover">
                                        <div class="p-3 bg-white">
                                            <p class="text-center font-medium capitalize">{{ $tipo }}</p>
                                            @foreach($parking->tarifas as $tarifa)
                                                @if($tarifa->tipoVehiculo == $tipo)
                                                    <p class="text-center text-blue-600 font-bold">
                                                        {{ number_format($tarifa->precio, 2) }}€/hora
                                                    </p>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Matrícula con teclado virtual -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Matrícula</label>
                        <div class="relative">
                            <input type="text" 
                                   id="matricula"
                                   name="matricula" 
                                   class="w-full p-2 border rounded-lg uppercase"
                                   pattern="[0-9]{4}[A-Z]{3}"
                                   placeholder="0000AAA"
                                   maxlength="7"
                                   readonly
                                   required>
                            
                            <!-- Teclado Virtual -->
                            <div class="mt-4 bg-gray-50 p-4 rounded-lg shadow-md">
                                <!-- Números -->
                                <div class="grid grid-cols-5 gap-2 mb-4">
                                    @foreach(range(0, 9) as $number)
                                        <button type="button" 
                                                class="virtual-key p-2 bg-white border rounded-lg hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                                data-key="{{ $number }}">
                                            {{ $number }}
                                        </button>
                                    @endforeach
                                </div>

                                <!-- Letras -->
                                <div class="grid grid-cols-7 gap-2">
                                    @foreach(range('A', 'Z') as $letter)
                                        <button type="button" 
                                                class="virtual-key p-2 bg-white border rounded-lg hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                                data-key="{{ $letter }}">
                                            {{ $letter }}
                                        </button>
                                    @endforeach
                                </div>

                                <!-- Botones de control -->
                                <div class="flex justify-between mt-4">
                                    <button type="button" 
                                            id="backspace"
                                            class="px-4 py-2 bg-red-100 text-red-600 rounded-lg hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-red-500">
                                        <i class="fas fa-backspace"></i> Borrar
                                    </button>
                                    <button type="button" 
                                            id="clear"
                                            class="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500">
                                        <i class="fas fa-times"></i> Limpiar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Hora de Salida -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Hora de Salida (Opcional)
                        </label>
                        <input type="datetime-local" 
                               name="horaSalida" 
                               id="horaSalida"
                               min="{{ $horaEntrada->format('Y-m-d\TH:i') }}"
                               class="w-full p-2 border rounded-lg">
                    </div>

                    <!-- Resumen de Tarifa -->
                    <div class="mb-6 p-6 bg-white shadow-lg rounded-lg border border-gray-200">
    <h3 class="text-xl font-semibold text-gray-900 mb-4">Resumen de Tarifa</h3>
    <div class="space-y-4">
        <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
            <span class="text-gray-600">Tarifa base:</span>
            <span class="font-medium text-gray-900" id="tarifaBase">-</span>
        </div>
        <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
            <span class="text-gray-600">Duración estimada:</span>
            <span class="font-medium text-gray-900" id="duracionEstimada">-</span>
        </div>
        <div class="flex justify-between items-center p-4 bg-blue-50 rounded-lg">
            <span class="text-lg font-semibold text-gray-900">Total estimado:</span>
            <span class="text-2xl font-bold text-blue-600" id="totalEstimado">-</span>
        </div>
    </div>
</div>

                    <!-- Método de Pago -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Método de Pago</label>
                        <div class="grid grid-cols-3 gap-4">
                            @foreach($opcionesPago as $metodo)
                                <label class="flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50">
                                    <input type="radio" 
                                           name="metodoPago" 
                                           value="{{ $metodo }}" 
                                           class="mr-2"
                                           required>
                                    <span class="capitalize">{{ $metodo }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Selección de Plaza (solo para parking sin control) -->
                    @if($parking->tipoParking === 'sinControl')
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Seleccione la Plaza</label>
                            <div class="space-y-4">
                                @foreach($plazasPorPlanta as $plantaId => $planta)
                                    <div class="bg-gray-50 p-4 rounded-lg">
                                        <h4 class="font-medium text-gray-900 mb-2">{{ $planta['nombrePlanta'] }}</h4>
                                        <div class="grid grid-cols-5 gap-2">
                                            @foreach($planta['plazas'] as $plaza)
                                                <label class="relative">
                                                    <input type="radio" 
                                                           name="idPlazaAsignadaAcceso" 
                                                           value="{{ $plaza->idPlaza }}" 
                                                           class="sr-only peer" 
                                                           required>
                                                    <div class="border-2 p-2 rounded-lg text-center cursor-pointer
                                                                peer-checked:border-blue-500 peer-checked:bg-blue-50
                                                                hover:bg-gray-100 transition-colors">
                                                        <span class="block font-medium">Plaza</span>
                                                        <span class="text-sm">{{ $plaza->numeroPlaza }}</span>
                                                    </div>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Botones -->
                    <div class="flex justify-end space-x-4">
                        <a href="{{ route('dashboard') }}" 
                           class="px-4 py-2 border rounded-lg hover:bg-gray-50">
                            Cancelar
                        </a>
                        <button type="submit" 
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            Registrar Entrada
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Obtener elementos
        const vehiculoRadios = document.querySelectorAll('input[name="tipoVehiculo"]');
        const horaSalidaInput = document.getElementById('horaSalida');
        const tarifaBaseSpan = document.getElementById('tarifaBase');
        const duracionEstimadaSpan = document.getElementById('duracionEstimada');
        const totalEstimadoSpan = document.getElementById('totalEstimado');
        const matriculaInput = document.getElementById('matricula');
        const virtualKeys = document.querySelectorAll('.virtual-key');
        const backspaceBtn = document.getElementById('backspace');
        const clearBtn = document.getElementById('clear');

        // Definir tarifas
        const tarifas = {
            @foreach($parking->tarifas as $tarifa)
                '{{ $tarifa->tipoVehiculo }}': {{ $tarifa->precio }},
            @endforeach
        };

        function actualizarResumen() {
            const vehiculoSeleccionado = document.querySelector('input[name="tipoVehiculo"]:checked');
            const horaSalida = horaSalidaInput.value;

            if (vehiculoSeleccionado) {
                const precio = tarifas[vehiculoSeleccionado.value];
                tarifaBaseSpan.textContent = `${precio.toFixed(2)}€/hora`;

                if (horaSalida) {
                    const entrada = new Date('{{ $horaEntrada->format('Y-m-d H:i:s') }}');
                    const salida = new Date(horaSalida);
                    const horas = Math.max(Math.ceil((salida - entrada) / (1000 * 60 * 60)), 1);
                    const total = precio * horas;

                    duracionEstimadaSpan.textContent = `${horas} hora${horas !== 1 ? 's' : ''}`;
                    totalEstimadoSpan.textContent = `${total.toFixed(2)}€`;
                }
            }
        }

        // Event Listeners
        vehiculoRadios.forEach(radio => {
            radio.addEventListener('change', actualizarResumen);
        });

        horaSalidaInput.addEventListener('change', actualizarResumen);

        // Función para validar el formato de la matrícula
        function isValidKey(currentValue, newKey) {
            const position = currentValue.length;
            if (position < 4) {
                return /[0-9]/.test(newKey);
            } else {
                return /[A-Z]/.test(newKey);
            }
        }

        // Manejar clics en las teclas virtuales
        virtualKeys.forEach(key => {
            key.addEventListener('click', function() {
                const keyValue = this.dataset.key;
                if (matriculaInput.value.length < 7 && isValidKey(matriculaInput.value, keyValue)) {
                    matriculaInput.value += keyValue;
                }
                updateKeyboardState();
            });
        });

        // Borrar último carácter
        backspaceBtn.addEventListener('click', function() {
            matriculaInput.value = matriculaInput.value.slice(0, -1);
            updateKeyboardState();
        });

        // Limpiar todo
        clearBtn.addEventListener('click', function() {
            matriculaInput.value = '';
            updateKeyboardState();
        });

        // Actualizar estado del teclado
        function updateKeyboardState() {
            const currentValue = matriculaInput.value;
            const position = currentValue.length;

            virtualKeys.forEach(key => {
                const keyValue = key.dataset.key;
                const isNumber = /[0-9]/.test(keyValue);
                const isLetter = /[A-Z]/.test(keyValue);

                // Deshabilitar teclas según la posición
                if (position < 4) {
                    key.disabled = !isNumber;
                    key.classList.toggle('opacity-50', !isNumber);
                } else if (position < 7) {
                    key.disabled = !isLetter;
                    key.classList.toggle('opacity-50', !isLetter);
                } else {
                    key.disabled = true;
                    key.classList.add('opacity-50');
                }
            });

            // Deshabilitar backspace si está vacío
            backspaceBtn.disabled = currentValue.length === 0;
            backspaceBtn.classList.toggle('opacity-50', currentValue.length === 0);
        }

        // Inicializar estado del teclado
        updateKeyboardState();

        // Ejecutar cálculo inicial si hay valores preseleccionados
        actualizarResumen();
    });
</script>
</x-app-layout>