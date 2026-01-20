<x-app-layout>

<div class="container mx-auto px-4 py-8">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden">
        <div class="p-6">
            <!-- Encabezado con estadísticas -->
            <div class="flex justify-between items-center mb-6">
                <div class="flex items-center">
                    <svg class="w-8 h-8 text-blue-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-14 0l2-2m12 0l-2-2"/>
                    </svg>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Gestión de Plazas</h1>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Total: {{ $plazas->total() }} plazas
                        </p>
                    </div>
                </div>
                @if(Auth::user()->isAdmin)
                <a href="{{ route('plazas.create') }}" 
                   class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Nueva Plaza
                </a>
                @endif
            </div>

            <!-- Alertas -->
            @if (session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
                    <p class="font-bold">¡Éxito!</p>
                    <p>{{ session('success') }}</p>
                </div>
            @endif

            @if (session('error'))
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
                    <p class="font-bold">¡Error!</p>
                    <p>{{ session('error') }}</p>
                </div>
            @endif

            <!-- Panel de Filtros -->
            <div class="mb-6 bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                <form action="{{ route('plazas.index') }}" method="GET" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <!-- Filtro Parking -->
                        <div>
                            <label for="parking" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Parking
                            </label>
                            <select id="parking" name="parking" class="w-full rounded-md border-gray-300 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                                <option value="">Todos los parkings</option>
                                @foreach($parkings as $parking)
                                    <option value="{{ $parking->idParking }}" {{ request('parking') == $parking->idParking ? 'selected' : '' }}>
                                        {{ $parking->nombreParking }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Filtro Planta -->
                        <div>
                            <label for="planta" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Planta
                            </label>
                            <select id="planta" name="planta" class="w-full rounded-md border-gray-300 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                                <option value="">Todas las plantas</option>
                                @foreach($plantas as $planta)
                                    <option value="{{ $planta->idPlanta }}" {{ request('planta') == $planta->idPlanta ? 'selected' : '' }}>
                                        {{ $planta->nombrePlanta }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Filtro Estado -->
                        <div>
                            <label for="estado" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Estado
                            </label>
                            <select id="estado" name="estado" class="w-full rounded-md border-gray-300 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                                <option value="">Todos los estados</option>
                                <option value="libre" {{ request('estado') == 'libre' ? 'selected' : '' }}>Libre</option>
                                <option value="ocupada" {{ request('estado') == 'ocupada' ? 'selected' : '' }}>Ocupada</option>
                                <option value="cerrada" {{ request('estado') == 'cerrada' ? 'selected' : '' }}>Cerrada</option>
                                <option value="mantenimiento" {{ request('estado') == 'mantenimiento' ? 'selected' : '' }}>Mantenimiento</option>
                            </select>
                        </div>

                        <!-- Elementos por página -->
                        <div>
                            <label for="per_page" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Elementos por página
                            </label>
                            <select id="per_page" name="per_page" class="w-full rounded-md border-gray-300 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
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
                            Aplicar Filtros
                        </button>
                        <a href="{{ route('plazas.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 transition-colors">
                            Limpiar
                        </a>
                    </div>
                </form>
            </div>

            <!-- Grid de Plazas -->
            @if($plazas->isEmpty())
                <div class="text-center py-8">
                    <div class="text-gray-500 dark:text-gray-400">
                        No se encontraron plazas con los filtros seleccionados.
                    </div>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                    @foreach($plazas as $plaza)
                        <div class="bg-white dark:bg-gray-700 p-4 rounded-lg shadow relative" 
                             oncontextmenu="showContextMenu(event, {{ $plaza->idPlaza }}, '{{ $plaza->estadoPlaza }}', '{{ $plaza->matricula }}'); return false;">
                            <div class="p-6">
                                <!-- Cabecera de la plaza -->
                                <div class="flex justify-between items-start mb-4">
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                            Plaza {{ $plaza->numeroPlaza }}
                                        </h3>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ $plaza->planta->parking->nombreParking }} - {{ $plaza->planta->nombrePlanta }}
                                        </p>
                                    </div>
                                    <span class="px-3 py-1 rounded-full text-sm font-medium
                                        @if($plaza->estadoPlaza == 'libre') bg-emerald-100 text-emerald-800
                                        @elseif($plaza->estadoPlaza == 'ocupada') bg-rose-100 text-rose-800
                                        @elseif($plaza->estadoPlaza == 'cerrada') bg-amber-100 text-amber-800
                                        @else bg-purple-100 text-purple-800
                                        @endif">
                                        {{ ucfirst($plaza->estadoPlaza) }}
                                    </span>
                                </div>

                                <!-- Detalles de la plaza -->
                                <div class="space-y-2 text-sm text-gray-600 dark:text-gray-300">
                                    <p><span class="font-medium">Tipo:</span> {{ ucfirst($plaza->tipoVehiculo) }}</p>
                                    @if($plaza->matricula)
                                        <p><span class="font-medium">Matrícula:</span> {{ $plaza->matricula }}</p>
                                    @endif
                                    @if($plaza->x1)
                                        <p class="font-medium">Coordenadas:</p>
                                        <div class="grid grid-cols-2 gap-2 text-xs">
                                            <p>P1: ({{ $plaza->x1 }}, {{ $plaza->y1 }})</p>
                                            <p>P2: ({{ $plaza->x2 }}, {{ $plaza->y2 }})</p>
                                            <p>P3: ({{ $plaza->x3 }}, {{ $plaza->y3 }})</p>
                                            <p>P4: ({{ $plaza->x4 }}, {{ $plaza->y4 }})</p>
                                        </div>
                                    @endif
                                </div>

                                <!-- Botones de acción -->
                                <div class="mt-4 flex space-x-2">
                                    <a href="{{ route('plazas.show', $plaza->idPlaza) }}" 
                                    
                                       class="flex-1 inline-flex justify-center items-center px-4 py-2 bg-blue-100 text-blue-700 rounded-md hover:bg-blue-200 transition-colors">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                        Ver
                                    </a>
                                    @if(Auth::user()->isAdmin)
                                        <a href="{{ route('plazas.edit', $plaza->idPlaza) }}" 
                                           class="inline-flex items-center px-3 py-2 bg-yellow-100 text-yellow-600 rounded-md hover:bg-yellow-200 transition-colors">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </a>
                                        <form action="{{ route('plazas.destroy', $plaza->idPlaza) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" onclick="return confirm('¿Estás seguro de que deseas eliminar esta plaza?')"
                                                    class="inline-flex items-center px-3 py-2 bg-red-100 text-red-600 rounded-md hover:bg-red-200 transition-colors">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            <!-- Paginación -->
            <div class="mt-6">
                {{ $plazas->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Menú contextual -->
<div id="contextMenu" class="hidden fixed z-50 bg-white dark:bg-gray-800 shadow-lg rounded-lg overflow-hidden border dark:border-gray-700">
    <div class="py-2">
        <button id="btnAparcar" onclick="aparcarVehiculo()" 
                class="w-full px-4 py-2 text-left hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center space-x-2 text-sm hidden">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <span>Aparcar vehículo</span>
        </button>
        
        <button id="btnDesaparcar" onclick="desaparcarVehiculo()" 
                class="w-full px-4 py-2 text-left hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center space-x-2 text-sm hidden">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
            </svg>
            <span>Desaparcar vehículo</span>
        </button>
        
        <button id="btnCerrar" onclick="cerrarPlaza()" 
                class="w-full px-4 py-2 text-left hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center space-x-2 text-sm hidden">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
            </svg>
            <span>Cerrar plaza</span>
        </button>
        
        <button id="btnAbrir" onclick="abrirPlaza()" 
                class="w-full px-4 py-2 text-left hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center space-x-2 text-sm hidden">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/>
            </svg>
            <span>Abrir plaza</span>
        </button>

        <button id="btnBloquear" onclick="bloquearPlaza()" 
                class="w-full px-4 py-2 text-left hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center space-x-2 text-sm hidden">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            <span>Poner en mantenimiento</span>
        </button>
        
        <button id="btnDesbloquear" onclick="desbloquearPlaza()" 
                class="w-full px-4 py-2 text-left hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center space-x-2 text-sm hidden">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
            <span>Quitar mantenimiento</span>
        </button>
    </div>
</div>

@push('scripts')
<script>
// Configurar el token CSRF para todas las peticiones AJAX
axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

let selectedPlazaId = null;
let selectedMatricula = null;

// Cerrar menú contextual al hacer clic fuera
document.addEventListener('click', function(e) {
    const contextMenu = document.getElementById('contextMenu');
    if (!contextMenu.contains(e.target)) {
        contextMenu.classList.add('hidden');
    }
});

function showContextMenu(event, plazaId, estado, matricula) {
    event.preventDefault();
    selectedPlazaId = plazaId;
    selectedMatricula = matricula;
    
    const contextMenu = document.getElementById('contextMenu');
    
    // Posicionar el menú evitando que se salga de la pantalla
    const x = Math.min(event.pageX, window.innerWidth - contextMenu.offsetWidth);
    const y = Math.min(event.pageY, window.innerHeight - contextMenu.offsetHeight);
    
    contextMenu.style.left = `${x}px`;
    contextMenu.style.top = `${y}px`;
    contextMenu.classList.remove('hidden');

    // Mostrar/ocultar opciones según el estado
    document.getElementById('btnAparcar').classList.toggle('hidden', estado !== 'libre');
    document.getElementById('btnDesaparcar').classList.toggle('hidden', estado !== 'ocupada');
    document.getElementById('btnCerrar').classList.toggle('hidden', estado !== 'libre');
    document.getElementById('btnAbrir').classList.toggle('hidden', estado !== 'cerrada');
    document.getElementById('btnBloquear').classList.toggle('hidden', estado !== 'libre');
    document.getElementById('btnDesbloquear').classList.toggle('hidden', estado !== 'mantenimiento');
}

function aparcarVehiculo() {
    if (!selectedPlazaId) return;
    
    Swal.fire({
        title: 'Aparcar vehículo',
        html: `
            <div class="space-y-4">
                <div class="text-left">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Matrícula
                    </label>
                    <input type="text" id="matricula" 
                           class="w-full px-3 py-2 border rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white" 
                           placeholder="4 números + 3 letras">
                </div>
                <div class="text-left">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Tipo de vehículo
                    </label>
                    <select id="tipoVehiculo" 
                            class="w-full px-3 py-2 border rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="coche">Coche</option>
                        <option value="moto">Moto</option>
                        <option value="furgoneta">Furgoneta</option>
                    </select>
                </div>
                <div class="text-left">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Método de pago
                    </label>
                    <select id="metodoPago" 
                            class="w-full px-3 py-2 border rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="efectivo">Efectivo</option>
                        <option value="tarjeta">Tarjeta</option>
                        <option value="movil">Móvil</option>
                    </select>
                </div>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Aparcar',
        cancelButtonText: 'Cancelar',
        customClass: {
            popup: 'dark:bg-gray-800 dark:text-white',
            input: 'dark:bg-gray-700 dark:border-gray-600 dark:text-white',
            confirmButton: 'bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded',
            cancelButton: 'bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded ml-2'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const data = {
                matricula: document.getElementById('matricula').value,
                tipoVehiculo: document.getElementById('tipoVehiculo').value,
                metodoPago: document.getElementById('metodoPago').value
            };

            axios.post(`/plazas/${selectedPlazaId}/aparcar`, data)
                .then(response => {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: 'Vehículo aparcado correctamente',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        location.reload();
                    });
                })
                .catch(error => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: error.response?.data?.message || 'Error al aparcar el vehículo',
                        customClass: {
                            popup: 'dark:bg-gray-800 dark:text-white'
                        }
                    });
                });
        }
    });
    
    document.getElementById('contextMenu').classList.add('hidden');
}

function desaparcarVehiculo() {
    if (!selectedPlazaId || !selectedMatricula) return;
    
    Swal.fire({
        icon: 'question',
        title: 'Desaparcar vehículo',
        text: `¿Deseas desaparcar el vehículo con matrícula ${selectedMatricula}?`,
        showCancelButton: true,
        confirmButtonText: 'Desaparcar',
        cancelButtonText: 'Cancelar',
        customClass: {
            popup: 'dark:bg-gray-800 dark:text-white',
            confirmButton: 'bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded',
            cancelButton: 'bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded ml-2'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            axios.post(`/plazas/${selectedPlazaId}/desaparcar`)
                .then(response => {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: 'Vehículo desaparcado correctamente',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        location.reload();
                    });
                })
                .catch(error => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: error.response?.data?.message || 'Error al desaparcar el vehículo',
                        customClass: {
                            popup: 'dark:bg-gray-800 dark:text-white'
                        }
                    });
                });
        }
    });
    
    document.getElementById('contextMenu').classList.add('hidden');
}

function bloquearPlaza() {
    if (!selectedPlazaId) return;
    
    Swal.fire({
        icon: 'warning',
        title: 'Poner plaza en mantenimiento',
        text: '¿Deseas poner esta plaza en mantenimiento?',
        showCancelButton: true,
        confirmButtonText: 'Confirmar',
        cancelButtonText: 'Cancelar',
        customClass: {
            popup: 'dark:bg-gray-800 dark:text-white',
            confirmButton: 'bg-yellow-600 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded',
            cancelButton: 'bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded ml-2'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            axios.post(`/plazas/${selectedPlazaId}/bloquear`)
                .then(response => {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: 'Plaza puesta en mantenimiento correctamente',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        location.reload();
                    });
                })
                .catch(error => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: error.response?.data?.message || 'Error al poner la plaza en mantenimiento',
                        customClass: {
                            popup: 'dark:bg-gray-800 dark:text-white'
                        }
                    });
                });
        }
    });
    
    document.getElementById('contextMenu').classList.add('hidden');
}

function desbloquearPlaza() {
    if (!selectedPlazaId) return;
    
    Swal.fire({
        icon: 'question',
        title: 'Habilitar plaza',
        text: '¿Deseas habilitar esta plaza?',
        showCancelButton: true,
        confirmButtonText: 'Confirmar',
        cancelButtonText: 'Cancelar',
        customClass: {
            popup: 'dark:bg-gray-800 dark:text-white',
            confirmButton: 'bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded',
            cancelButton: 'bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded ml-2'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            axios.post(`/plazas/${selectedPlazaId}/desbloquear`)
                .then(response => {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: 'Plaza habilitada correctamente',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        location.reload();
                    });
                })
                .catch(error => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: error.response?.data?.message || 'Error al habilitar la plaza',
                        customClass: {
                            popup: 'dark:bg-gray-800 dark:text-white'
                        }
                    });
                });
        }
    });
    
    document.getElementById('contextMenu').classList.add('hidden');
}

function cerrarPlaza() {
    if (!selectedPlazaId) return;
    
    Swal.fire({
        icon: 'warning',
        title: 'Cerrar plaza',
        text: '¿Deseas cerrar esta plaza?',
        showCancelButton: true,
        confirmButtonText: 'Confirmar',
        cancelButtonText: 'Cancelar',
        customClass: {
            popup: 'dark:bg-gray-800 dark:text-white',
            confirmButton: 'bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded',
            cancelButton: 'bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded ml-2'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            axios.post(`/plazas/${selectedPlazaId}/cerrar`)
                .then(response => {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: 'Plaza cerrada correctamente',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        location.reload();
                    });
                })
                .catch(error => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: error.response?.data?.message || 'Error al cerrar la plaza',
                        customClass: {
                            popup: 'dark:bg-gray-800 dark:text-white'
                        }
                    });
                });
        }
    });
    
    document.getElementById('contextMenu').classList.add('hidden');
}

function abrirPlaza() {
    if (!selectedPlazaId) return;
    
    Swal.fire({
        icon: 'question',
        title: 'Abrir plaza',
        text: '¿Deseas abrir esta plaza?',
        showCancelButton: true,
        confirmButtonText: 'Confirmar',
        cancelButtonText: 'Cancelar',
        customClass: {
            popup: 'dark:bg-gray-800 dark:text-white',
            confirmButton: 'bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded',
            cancelButton: 'bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded ml-2'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            axios.post(`/plazas/${selectedPlazaId}/abrir`)
                .then(response => {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: 'Plaza abierta correctamente',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        location.reload();
                    });
                })
                .catch(error => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: error.response?.data?.message || 'Error al abrir la plaza',
                        customClass: {
                            popup: 'dark:bg-gray-800 dark:text-white'
                        }
                    });
                });
        }
    });
    
    document.getElementById('contextMenu').classList.add('hidden');
}
</script>
@endpush

<script>
document.getElementById('parking').addEventListener('change', function() {
    const parkingId = this.value;
    const plantaSelect = document.getElementById('planta');
    
    plantaSelect.innerHTML = '<option value="">Todas las plantas</option>';
    
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
</script>
</x-app-layout>