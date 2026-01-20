<x-app-layout>
    <div class="main-container max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Mensaje de Bienvenida con fondo azul -->
        <div class="relative overflow-hidden bg-blue-600 rounded-3xl p-8 mb-8">
            <div class="relative text-center py-6">
                <h1 class="text-4xl font-bold text-white mb-3">
                    ¡Bienvenido, {{ Auth::user()->name }}!
                </h1>
                <p class="text-white/90 text-lg">
                    @if(Auth::user()->isAdmin)
                        <span class="inline-flex items-center px-4 py-1 rounded-full bg-white/20">
                            <i class="fas fa-shield-alt mr-2"></i>
                            Administrador del Sistema
                        </span>
                    @elseif(Auth::user()->isOperador)
                        <span class="inline-flex items-center px-4 py-1 rounded-full bg-white/20">
                            <i class="fas fa-user-tie mr-2"></i>
                            Operador de Parking
                        </span>
                    @else
                        <span class="inline-flex items-center px-4 py-1 rounded-full bg-white/20">
                            <i class="fas fa-user mr-2"></i>
                            Cliente
                        </span>
                    @endif
                </p>
            </div>
            
        </div>
      
        
                <!-- EXAMEN -->
        <div class="p-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">
        <a href="{{ route('examen.llistat') }}" 
               class="group p-6 bg-blue-600 rounded-2xl shadow-lg transition-all duration-300 hover:shadow-blue-500/25 hover:scale-105">
                <div class="bg-white/20 p-4 rounded-xl mb-4 w-fit group-hover:scale-110 transition-transform duration-300">
                    <i class="fas fa-parking text-3xl text-white"></i>
                </div>
                <h3 class="text-lg font-semibold text-white">EXAMEN</h3>
            </a>   </div>
        </div> 



        <!-- Galería de Imágenes - Solo para Administradores -->
        @if(Auth::user() && Auth::user()->isAdmin)
        <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-xl overflow-hidden mb-8">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="p-3 bg-blue-600 rounded-2xl">
                            <i class="fas fa-images text-2xl text-white"></i>
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold text-gray-800 dark:text-white">
                                Galería de Imágenes
                            </h2>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Gestiona las imágenes de los parkings
                            </p>
                        </div>
                    </div>
                    <label class="cursor-pointer bg-blue-600 text-white px-6 py-2 rounded-xl hover:bg-blue-700 transition-colors flex items-center justify-center gap-2 relative overflow-hidden group">
                        <span class="flex items-center">
                            <i class="fas fa-upload mr-2"></i> 
                            Subir Imágenes
                        </span>
                        <input type="file" id="image-upload" multiple accept="image/jpeg,image/png,image/gif" class="hidden">
                        <div class="upload-progress absolute inset-0 bg-green-500 transform -translate-x-full transition-transform duration-300"></div>
                    </label>
                </div>
            </div>

            <div class="relative">
                <div id="gallery-container" class="overflow-x-auto hide-scrollbar">
                    <div id="gallery" class="flex p-6 space-x-4 transition-transform duration-300 snap-x snap-mandatory">
                        @foreach($images as $image)
                        <div class="flex-none w-72 snap-start" data-image-path="{{ $image['path'] }}" data-image-type="{{ $image['type'] }}">
                            <div class="relative h-48 group rounded-xl overflow-hidden">
                                <img src="{{ asset($image['path']) }}" 
                                     alt="{{ $image['name'] }}" 
                                     class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-110">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity">
                                    <div class="absolute bottom-0 left-0 right-0 p-4">
                                        <div class="flex items-center justify-between">
                                            <span class="text-white text-sm truncate max-w-[200px]">
                                                {{ $image['name'] }}
                                            </span>
                                            <div class="flex space-x-2">
                                                <a href="{{ asset($image['path']) }}" 
                                                   target="_blank" 
                                                   class="p-2 bg-white/20 rounded-full hover:bg-white/40 transition-colors">
                                                    <i class="fas fa-eye text-white"></i>
                                                </a>
                                                <button onclick="deleteImage('{{ $image['path'] }}', '{{ $image['type'] }}')" 
                                                        class="p-2 bg-white/20 rounded-full hover:bg-red-500/70 transition-colors">
                                                    <i class="fas fa-trash text-white"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                
                <div class="absolute left-0 top-0 bottom-0 w-20 bg-gradient-to-r from-white dark:from-gray-800 pointer-events-none"></div>
                <div class="absolute right-0 top-0 bottom-0 w-20 bg-gradient-to-l from-white dark:from-gray-800 pointer-events-none"></div>
                
                <button id="scroll-left" class="absolute left-4 top-1/2 -translate-y-1/2 bg-blue-600 text-white p-3 rounded-full shadow-lg hover:bg-blue-700 transition-colors opacity-0 pointer-events-none">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <button id="scroll-right" class="absolute right-4 top-1/2 -translate-y-1/2 bg-blue-600 text-white p-3 rounded-full shadow-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
        </div>
        @endif

    <!-- Vista de Parkings -->
    <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-xl overflow-hidden mb-8">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center space-x-4">
                <div class="p-3 bg-blue-600 rounded-2xl">
                    <i class="fas fa-parking text-2xl text-white"></i>
                </div>
                <h2 class="text-2xl font-bold text-gray-800 dark:text-white">
                    Descubre Nuestros Parkings
                </h2>
            </div>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($parkings as $parking)
                <a href="{{ route('parkings.show', $parking->idParking) }}" 
                   class="group relative h-96 rounded-2xl overflow-hidden shadow-lg transition-all duration-500 hover:shadow-2xl hover:scale-[1.02]">
                    <!-- Imagen de fondo con mejor calidad -->
                    <div class="absolute inset-0 bg-cover bg-center transition-transform duration-700 group-hover:scale-110"
                         style="background-image: linear-gradient(rgba(0, 0, 0, 0.1), rgba(0, 0, 0, 0.1)), url(
                            @if ($parking->imagenParking && file_exists(public_path('storage/' . $parking->imagenParking)))
                                '{{ asset('storage/' . $parking->imagenParking) }}'
                            @elseif($parking->imagenParking && file_exists(public_path('images/' . $parking->imagenParking)))
                                '{{ asset('images/' . $parking->imagenParking) }}'
                            @else
                                '{{ asset('images/default-parking-image.jpg') }}'
                            @endif
                        );
                        background-size: cover;
                        background-position: center;
                        image-rendering: -webkit-optimize-contrast;">
                    </div>
                    
                    <!-- Overlay más sutil -->
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent transition-opacity duration-300"></div>

                    <!-- Contenido mejorado -->
                    <div class="absolute bottom-0 left-0 right-0 p-6">
                        <div class="bg-white/10 rounded-2xl p-6 backdrop-blur-sm transform transition-all duration-300 group-hover:translate-y-[-8px]">
                            <div class="space-y-4">
                                <h3 class="text-2xl font-bold text-white">
                                    {{ $parking->nombreParking }}
                                </h3>
                                <p class="flex items-center text-white/90 text-lg">
                                    <i class="fa-solid fa-location-dot mr-3"></i>
                                    {{ $parking->direccionParking }}
                                </p>
                                <div class="flex items-center space-x-4 pt-2">
                                    <span class="flex items-center bg-white/20 rounded-xl px-4 py-2 text-white">
                                        <i class="fa-solid fa-car text-lg mr-2"></i>
                                        {{ $parking->capacidadTotal }} plazas
                                    </span>
                                    <span class="flex items-center bg-green-500 rounded-xl px-4 py-2 text-white">
                                        <i class="fa-solid fa-check-circle text-lg mr-2"></i>
                                        {{ $parking->plazasDisponibles }} libres
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Etiqueta de estado en la esquina superior -->
                    <div class="absolute top-4 right-4">
                        @if($parking->plazasDisponibles > 0)
                            <span class="bg-green-500 text-white px-4 py-2 rounded-full text-sm font-semibold shadow-lg">
                                Disponible
                            </span>
                        @else
                            <span class="bg-red-500 text-white px-4 py-2 rounded-full text-sm font-semibold shadow-lg">
                                Completo
                            </span>
                        @endif
                    </div>
                </a>
                @endforeach
            </div>
        </div>
    </div>


            

    <!-- Panel de Control - Solo para admin y operador -->
    @if(Auth::user() && (Auth::user()->isAdmin || Auth::user()->isOperador))
    <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-xl overflow-hidden mb-8">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center space-x-4">
                <div class="p-3 bg-blue-600 rounded-2xl">
                    <i class="fas fa-tools text-2xl text-white"></i>
                </div>
                <h2 class="text-2xl font-bold text-gray-800 dark:text-white">
                    Panel de Control
                </h2>
            </div>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">
                <a href="{{ route('parkings.index') }}" 
                   class="group p-6 bg-blue-600 rounded-2xl shadow-lg transition-all duration-300 hover:shadow-blue-500/25 hover:scale-105">
                    <div class="bg-white/20 p-4 rounded-xl mb-4 w-fit group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-parking text-3xl text-white"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-white">Gestión de Parkings</h3>
                </a>

                <a href="{{ route('tarifas.index') }}" 
                   class="group p-6 bg-blue-600 rounded-2xl shadow-lg transition-all duration-300 hover:shadow-blue-500/25 hover:scale-105">
                    <div class="bg-white/20 p-4 rounded-xl mb-4 w-fit group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-euro-sign text-3xl text-white"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-white">Gestión de Tarifas</h3>
                </a>

                <a href="{{ route('plazas.index') }}" 
                   class="group p-6 bg-blue-600 rounded-2xl shadow-lg transition-all duration-300 hover:shadow-blue-500/25 hover:scale-105">
                    <div class="bg-white/20 p-4 rounded-xl mb-4 w-fit group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-car text-3xl text-white"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-white">Gestión de Plazas</h3>
                </a>

                @if(Auth::user() && Auth::user()->isAdmin)
                <a href="{{ route('plantas.index') }}" 
                   class="group p-6 bg-blue-600 rounded-2xl shadow-lg transition-all duration-300 hover:shadow-blue-500/25 hover:scale-105">
                    <div class="bg-white/20 p-4 rounded-xl mb-4 w-fit group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-building text-3xl text-white"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-white">Gestión de Plantas</h3>
                </a>

                @endif
            </div>
        </div>
    </div>
    @endif

    <!-- Registro de Accesos -->
    <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-xl overflow-hidden">
        <a href="{{ route('registro-accesos.index') }}" 
           class="block bg-blue-600 p-8 text-white transition-all duration-300 hover:bg-blue-700 group">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <h3 class="text-2xl font-bold mb-3 group-hover:translate-x-2 transition-transform duration-300">
                        Registrar Acceso
                    </h3>
                    <p class="text-white/90 max-w-xl group-hover:translate-x-2 transition-transform duration-300 delay-75">
                        Gestiona tus entradas y salidas del parking de forma rápida y sencilla. Mantén un registro detallado de todos tus movimientos.
                    </p>
                </div>
                <div class="bg-white/20 p-6 rounded-2xl group-hover:scale-110 transition-transform duration-300">
                    <i class="fas fa-sign-in-alt text-4xl text-white"></i>
                </div>
            </div>
        </a>
    </div>
</div>

        <style>
        .hide-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .hide-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
        .snap-x {
            scroll-snap-type: x mandatory;
        }
        .snap-start {
            scroll-snap-align: start;
        }
        </style>

        <!-- Añadir modal de previsualización -->
        <div id="preview-modal" class="fixed inset-0 bg-black/50 z-50 hidden flex items-center justify-center">
            <div class="bg-white dark:bg-gray-800 rounded-2xl max-w-2xl w-full mx-4">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xl font-bold">Previsualización de imágenes</h3>
                        <button onclick="closePreviewModal()" class="text-gray-500 hover:text-gray-700">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                <div class="p-6">
                    <div id="preview-container" class="grid grid-cols-2 gap-4 mb-6"></div>
                    <div class="flex justify-end space-x-4">
                        <button onclick="closePreviewModal()" class="px-4 py-2 text-gray-600 hover:text-gray-800">
                            Cancelar
                        </button>
                        <button onclick="uploadImages()" class="px-4 py-2 bg-blue-600 text-white rounded-xl hover:bg-blue-700">
                            Guardar Imágenes
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Script para la galería -->
        @if(Auth::user() && Auth::user()->isAdmin)
        <script>
  document.addEventListener('DOMContentLoaded', function() {
    const imageUpload = document.getElementById('image-upload');
    const gallery = document.getElementById('gallery');
    const galleryContainer = document.getElementById('gallery-container');
    let autoScrollInterval;
    const scrollSpeed = 1; // Velocidad del scroll automático
    let selectedFiles = [];
    const previewModal = document.getElementById('preview-modal');
    const previewContainer = document.getElementById('preview-container');
    const scrollLeftBtn = document.getElementById('scroll-left');
    const scrollRightBtn = document.getElementById('scroll-right');

    // Duplicar las imágenes en la galería
    const originalImages = gallery.innerHTML;
    gallery.innerHTML += originalImages;

    // Función para mostrar notificaciones
    function showNotification(message, type = 'success') {
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 ${
            type === 'success' ? 'bg-green-500' : 'bg-red-500'
        } text-white`;
        notification.textContent = message;
        document.body.appendChild(notification);
        setTimeout(() => notification.remove(), 3000);
    }

    // Iniciar scroll automático
    function startAutoScroll() {
        stopAutoScroll(); // Detener cualquier scroll existente
        autoScrollInterval = setInterval(() => {
            if (galleryContainer.scrollLeft >= (gallery.scrollWidth / 2)) {
                galleryContainer.scrollTo({
                    left: 0,
                    behavior: 'auto' // Cambiar a 'auto' para un reinicio instantáneo
                });
            } else {
                galleryContainer.scrollLeft += scrollSpeed;
            }
            updateScrollButtons();
        }, 30);
    }

    // Detener scroll automático
    function stopAutoScroll() {
        if (autoScrollInterval) {
            clearInterval(autoScrollInterval);
        }
    }

    // Eventos para pausar/reanudar el scroll automático
    galleryContainer.addEventListener('mouseenter', stopAutoScroll);
    galleryContainer.addEventListener('mouseleave', startAutoScroll);
    galleryContainer.addEventListener('touchstart', stopAutoScroll);
    galleryContainer.addEventListener('touchend', startAutoScroll);

    // Función para eliminar imágenes
    window.deleteImage = async function(path, type) {
        if (!confirm('¿Estás seguro de que deseas eliminar esta imagen?')) {
            return;
        }

        try {
            const response = await fetch('{{ route("dashboard.deleteImage") }}', { 
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ path, type })
            });

            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.error || 'Error al eliminar la imagen');
            }

            // Eliminar el elemento del DOM
            const containerElement = document.querySelector(`[data-image-path="${path}"]`);
            if (containerElement) {
                containerElement.style.opacity = '0';
                containerElement.style.transform = 'scale(0.8)';
                setTimeout(() => {
                    containerElement.remove();
                    if (gallery.children.length === 0) {
                        stopAutoScroll();
                    }
                }, 300);
                showNotification('Imagen eliminada correctamente');
            }

        } catch (error) {
            console.error('Error:', error);
            showNotification(error.message, 'error');
        }
    };

    // Modificar el evento change del input
    imageUpload.addEventListener('change', function(e) {
        const files = e.target.files;
        if (!files.length) return;

        selectedFiles = Array.from(files);
        showPreviewModal(selectedFiles);
    });

    function showPreviewModal(files) {
        previewContainer.innerHTML = '';
        files.forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const div = document.createElement('div');
                div.className = 'relative aspect-video rounded-xl overflow-hidden';
                div.innerHTML = `
                    <img src="${e.target.result}" class="w-full h-full object-cover">
                    <button onclick="removePreview(${index})" class="absolute top-2 right-2 p-2 bg-red-500 rounded-full text-white hover:bg-red-600">
                        <i class="fas fa-times"></i>
                    </button>
                `;
                previewContainer.appendChild(div);
            };
            reader.readAsDataURL(file);
        });
        previewModal.classList.remove('hidden');
    }

    window.closePreviewModal = function() {
        previewModal.classList.add('hidden');
        selectedFiles = [];
        imageUpload.value = '';
    };

    window.removePreview = function(index) {
        selectedFiles.splice(index, 1);
        showPreviewModal(selectedFiles);
        if (selectedFiles.length === 0) {
            closePreviewModal();
        }
    };

    window.uploadImages = async function() {
        if (!selectedFiles.length) return;

        const formData = new FormData();
        selectedFiles.forEach(file => {
            formData.append('images[]', file);
        });

        try {
            const response = await fetch('{{ route("dashboard.upload") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });

            if (!response.ok) {
                const data = await response.json();
                throw new Error(data.error || 'Error al subir las imágenes');
            }

            const data = await response.json();

            data.forEach(image => {
                const div = document.createElement('div');
                div.className = 'flex-none w-72 snap-start opacity-0';
                div.style.transition = 'all 0.3s ease';
                div.innerHTML = `
                    <div class="relative h-48 group rounded-xl overflow-hidden">
                        <img src="/storage/${image.path}" 
                             alt="${image.name}" 
                             class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-110">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity">
                            <div class="absolute bottom-0 left-0 right-0 p-4">
                                <div class="flex items-center justify-between">
                                    <span class="text-white text-sm truncate max-w-[200px]">
                                        ${image.name}
                                    </span>
                                    <div class="flex space-x-2">
                                        <a href="/storage/${image.path}" 
                                           target="_blank" 
                                           class="p-2 bg-white/20 rounded-full hover:bg-white/40 transition-colors">
                                            <i class="fas fa-eye text-white"></i>
                                        </a>
                                        <button onclick="deleteImage('${image.path}', '${image.type}')" 
                                                class="p-2 bg-white/20 rounded-full hover:bg-red-500/70 transition-colors">
                                            <i class="fas fa-trash text-white"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                gallery.appendChild(div);
                
                // Animar entrada
                requestAnimationFrame(() => {
                    div.style.opacity = '1';
                });
            });

            showNotification(`${selectedFiles.length} imágenes subidas correctamente`);
            this.value = '';

            // Iniciar scroll automático si hay imágenes
            if (gallery.children.length > 0) {
                startAutoScroll();
            }

            closePreviewModal();
        } catch (error) {
            console.error('Error:', error);
            showNotification(error.message, 'error');
        }
    };

    // Mejorar el scroll infinito
    function updateScrollButtons() {
        const isAtStart = galleryContainer.scrollLeft <= 0;
        const isAtEnd = galleryContainer.scrollLeft >= (gallery.scrollWidth / 2);
        
        scrollLeftBtn.classList.toggle('opacity-0', isAtStart);
        scrollLeftBtn.classList.toggle('pointer-events-none', isAtStart);
        scrollRightBtn.classList.toggle('opacity-0', isAtEnd);
        scrollRightBtn.classList.toggle('pointer-events-none', isAtEnd);
    }

    // Añadir eventos para los botones de navegación
    scrollLeftBtn.addEventListener('click', () => {
        stopAutoScroll();
        galleryContainer.scrollBy({
            left: -300,
            behavior: 'smooth'
        });
        setTimeout(startAutoScroll, 1000);
    });

    scrollRightBtn.addEventListener('click', () => {
        stopAutoScroll();
        galleryContainer.scrollBy({
            left: 300,
            behavior: 'smooth'
        });
        setTimeout(startAutoScroll, 1000);
    });

    galleryContainer.addEventListener('scroll', updateScrollButtons);
    updateScrollButtons();

    // Iniciar el scroll automático al cargar la página
    startAutoScroll();
});
        </script>
        @endif
        
</x-app-layout>