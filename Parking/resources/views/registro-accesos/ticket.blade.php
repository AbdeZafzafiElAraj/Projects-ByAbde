<x-app-layout>
    @if(session('success'))
        <div class="fixed inset-0 bg-blue-500 bg-opacity-10 backdrop-blur-sm flex items-center justify-center z-50 transition-opacity duration-500">
            <div class="bg-white p-8 rounded-2xl shadow-2xl transform transition-all duration-500 max-w-lg w-full mx-4">
                <div class="text-center">
                    <!-- Icono de éxito -->
                    <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100 mb-4">
                        <svg class="h-10 w-10 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    
                    <!-- Título -->
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">¡Registro Exitoso!</h2>
                    
                    <!-- Mensaje -->
                    <p class="text-gray-600 mb-6">{{ session('success') }}</p>
                    
                    <!-- Línea decorativa -->
                    <div class="w-16 h-1 bg-green-500 mx-auto mb-6"></div>
                    
                    <!-- Información adicional -->
                    <p class="text-sm text-gray-500 mb-6">
                        El ticket se ha generado correctamente. Puede imprimirlo o guardarlo para su referencia.
                    </p>
                    
                    <!-- Botón para cerrar -->
                    <button onclick="this.parentElement.parentElement.parentElement.remove()" 
                            class="bg-green-500 text-white px-6 py-3 rounded-lg hover:bg-green-600 transition-colors duration-300 flex items-center mx-auto">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Continuar
                    </button>
                </div>
            </div>
        </div>
    @endif

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <!-- Cabecera del Ticket -->
                    <div class="text-center mb-6">
                        <h2 class="text-2xl font-bold">{{ $datosTicket['parking'] }}</h2>
                        <p class="text-gray-600">Ticket de Estacionamiento</p>
                    </div>

                    <!-- Detalles del Ticket -->
                    <div class="space-y-4">
                        <div class="flex justify-between border-b pb-2">
                            <span class="font-semibold">Nº Ticket:</span>
                            <span>{{ $datosTicket['idRegistro'] }}</span>
                        </div>

                        <div class="flex justify-between border-b pb-2">
                            <span class="font-semibold">Matrícula:</span>
                            <span>{{ $datosTicket['matricula'] }}</span>
                        </div>

                        <div class="flex justify-between border-b pb-2">
                            <span class="font-semibold">Tipo Vehículo:</span>
                            <span class="capitalize">{{ $datosTicket['tipoVehiculo'] }}</span>
                        </div>

                        <div class="flex justify-between border-b pb-2">
                            <span class="font-semibold">Ubicación:</span>
                            <span>
                                @if(isset($datosTicket['planta']) && $datosTicket['planta'] !== 'N/A')
                                    {{ $datosTicket['planta'] }} - Plaza {{ $datosTicket['plaza'] }}
                                @else
                                    Plaza {{ $datosTicket['plaza'] }}
                                @endif
                            </span>
                        </div>

                        <div class="flex justify-between border-b pb-2">
                            <span class="font-semibold">Hora Entrada:</span>
                            <span>{{ \Carbon\Carbon::parse($datosTicket['horaEntrada'])->format('d/m/Y H:i') }}</span>
                        </div>

                        <div class="flex justify-between border-b pb-2">
                            <span class="font-semibold">Hora Salida Estimada:</span>
                            <span>{{ \Carbon\Carbon::parse($datosTicket['horaSalida'])->format('d/m/Y H:i') }}</span>
                        </div>

                        <div class="flex justify-between border-b pb-2">
                            <span class="font-semibold">Horas Estimadas:</span>
                            <span>{{ $datosTicket['horasEstimadas'] }} horas</span>
                        </div>

                        <div class="flex justify-between border-b pb-2">
                            <span class="font-semibold">Tarifa por Hora:</span>
                            <span>{{ number_format($datosTicket['tarifa'], 2) }}€</span>
                        </div>

                        <div class="flex justify-between border-b pb-2 text-lg font-bold text-blue-600">
                            <span>Total Estimado:</span>
                            <span>{{ number_format($datosTicket['totalEstimado'], 2) }}€</span>
                        </div>

                        <div class="flex justify-between border-b pb-2">
                            <span class="font-semibold">Método de Pago:</span>
                            <span class="capitalize">{{ $datosTicket['metodoPago'] }}</span>
                        </div>
                    </div>

                    <!-- Notas Importantes -->
                    <div class="mt-6 text-sm text-gray-600 bg-gray-100 p-4 rounded">
                        <p class="font-semibold">Notas:</p>
                        <ul class="list-disc list-inside">
                            <li>El total es una estimación basada en la hora de salida prevista</li>
                            <li>El importe final puede variar según la hora real de salida</li>
                            <li>Los precios incluyen IVA</li>
                        </ul>
                    </div>

                    <!-- Pie del Ticket -->
                    <div class="mt-8 text-center text-sm text-gray-600">
                        <p>Conserve este ticket hasta su salida</p>
                        <p>Gracias por usar nuestro servicio</p>
                    </div>

                    <!-- Botones de Acción -->
                    <div class="mt-6 flex justify-center space-x-4">
                        <button onclick="window.print()" 
                                class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                            Imprimir Ticket
                        </button>
                        <a href="{{ route('registro-accesos.index') }}" 
                           class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                            Volver
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
    <style>
        @media print {
            .no-print {
                display: none;
            }
            body {
                padding: 0;
                margin: 0;
            }
            .shadow-lg {
                box-shadow: none !important;
            }
        }

        @keyframes slideIn {
            from {
                transform: translateY(-20px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .backdrop-blur-sm {
            backdrop-filter: blur(8px);
        }

        .transform {
            animation: slideIn 0.5s ease-out;
        }
    </style>
    @endpush
</x-app-layout>
