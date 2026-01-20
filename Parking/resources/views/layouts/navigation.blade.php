<nav x-data="{ open: false }" class="fixed w-full top-0 z-50">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-2">
        <div class="bg-white/90 dark:bg-gray-800/90 backdrop-blur-md rounded-2xl shadow-lg border border-gray-200/20 dark:border-gray-700/20">
            <div class="flex justify-between h-16">
                <!-- Logo y Links de Navegación -->
                <div class="flex items-center justify-between flex-1">
                    <!-- Logo con animación hover -->
                    <div class="flex items-center">
                        <a href="{{ route('dashboard') }}" class="ml-4 transition-transform duration-300 hover:scale-110">
                            <img src="{{ asset('images/Logo.png') }}" class="block w-12 h-12">
                        </a>
                    </div>

                    <!-- Digital Clock - Visible en todas las pantallas -->
                    <div class="flex items-center">
                        <div class="bg-gray-100 dark:bg-gray-700 px-3 py-1.5 rounded-lg">
                            <span id="digital-clock" class="text-base sm:text-lg font-mono font-bold text-blue-600 dark:text-blue-400"></span>
                        </div>
                    </div>

                    <!-- Navigation Links - Centrados con nuevos estilos -->
                    <div class="hidden sm:flex items-center space-x-3">
                        <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" 
                            class="group inline-flex items-center px-4 py-2.5 border border-transparent text-sm font-semibold rounded-xl transition-all duration-300 ease-in-out
                            {{ request()->routeIs('dashboard') 
                                ? 'bg-blue-500 text-white shadow-lg shadow-blue-500/30' 
                                : 'text-gray-600 dark:text-gray-300 hover:bg-blue-500/10 dark:hover:bg-blue-400/10' }}">
                            <i class="fa-solid fa-gauge-high mr-2"></i>
                            {{ __('Dashboard') }}
                        </x-nav-link>
                        
                        <x-nav-link :href="route('parkings.index')" :active="request()->routeIs('parkings.index')"
                            class="group inline-flex items-center px-4 py-2.5 border border-transparent text-sm font-semibold rounded-xl transition-all duration-300 ease-in-out
                            {{ request()->routeIs('parkings.index') 
                                ? 'bg-blue-500 text-white shadow-lg shadow-blue-500/30' 
                                : 'text-gray-600 dark:text-gray-300 hover:bg-blue-500/10 dark:hover:bg-blue-400/10' }}">
                            <i class="fa-solid fa-car mr-2"></i>
                            {{ __('Parkings') }}
                        </x-nav-link>

                        @if(Auth::user()->isAdmin)  
                        <x-nav-link :href="route('plantas.index')" :active="request()->routeIs('plantas.index')"
                            class="group inline-flex items-center px-4 py-2.5 border border-transparent text-sm font-semibold rounded-xl transition-all duration-300 ease-in-out
                            {{ request()->routeIs('plantas.index') 
                                ? 'bg-blue-500 text-white shadow-lg shadow-blue-500/30' 
                                : 'text-gray-600 dark:text-gray-300 hover:bg-blue-500/10 dark:hover:bg-blue-400/10' }}">
                            <i class="fa-solid fa-building mr-2"></i>
                            {{ __('Plantas') }}
                        </x-nav-link>
                        @endif

                        @if(Auth::user() && (Auth::user()->isAdmin || Auth::user()->isOperador))
                        <x-nav-link :href="route('plazas.index')" :active="request()->routeIs('plazas.index')"
                            class="group inline-flex items-center px-4 py-2.5 border border-transparent text-sm font-semibold rounded-xl transition-all duration-300 ease-in-out
                            {{ request()->routeIs('plazas.index') 
                                ? 'bg-blue-500 text-white shadow-lg shadow-blue-500/30' 
                                : 'text-gray-600 dark:text-gray-300 hover:bg-blue-500/10 dark:hover:bg-blue-400/10' }}">
                            <i class="fa-solid fa-square-parking mr-2"></i>
                            {{ __('Plazas') }}
                        </x-nav-link>
                        @endif

                        <x-nav-link :href="route('tarifas.index')" :active="request()->routeIs('tarifas.index')"
                            class="group inline-flex items-center px-4 py-2.5 border border-transparent text-sm font-semibold rounded-xl transition-all duration-300 ease-in-out
                            {{ request()->routeIs('tarifas.index') 
                                ? 'bg-blue-500 text-white shadow-lg shadow-blue-500/30' 
                                : 'text-gray-600 dark:text-gray-300 hover:bg-blue-500/10 dark:hover:bg-blue-400/10' }}">
                            <i class="fa-solid fa-money-bill mr-2"></i>
                            {{ __('Tarifas') }}
                        </x-nav-link>

                    </div>

                    <!-- Reloj Digital y Perfil mejorados -->
                    <div class="hidden sm:flex items-center space-x-6 mr-6">
                        <!-- Settings Dropdown mejorado -->
                        @auth
                        <div class="ms-3 relative" x-data="{ open: false }">
                            <button 
                                @click="open = !open" 
                                @keydown.escape.window="open = false"
                                @click.outside="open = false"
                                type="button" 
                                class="inline-flex items-center px-4 py-2.5 border border-gray-200 dark:border-gray-600 text-sm font-semibold rounded-xl bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-all duration-300 ease-in-out"
                            >
                                <i class="fa-solid fa-user mr-2"></i>
                                {{ Auth::user()->name }}
                                <svg class="ml-2 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                </svg>
                            </button>

                            <!-- Dropdown Menu con animaciones mejoradas -->
                            <div 
                                x-show="open"
                                x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="transform opacity-0 scale-95"
                                x-transition:enter-end="transform opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-75"
                                x-transition:leave-start="transform opacity-100 scale-100"
                                x-transition:leave-end="transform opacity-0 scale-95"
                                class="absolute right-0 mt-3 w-48 rounded-xl shadow-lg py-2 bg-white dark:bg-gray-700 border border-gray-100 dark:border-gray-600"
                                style="display: none;"
                            >
                                <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800">
                                    {{ __('Profile') }}
                                </a>

                                <!-- Authentication -->
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800">
                                        {{ __('Log Out') }}
                                    </button>
                                </form>
                            </div>
                        </div>
                        @endauth
                    </div>
                </div>

                <!-- Hamburger -->
                <div class="flex items-center sm:hidden mr-4">
                    <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out">
                        <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>


<!-- Responsive Navigation Menu -->
<div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
    <div class="fixed inset-0 bg-gray-900/95 backdrop-blur-md z-[99999]">
        <!-- Cabecera del menú móvil -->
        <div class="flex items-center justify-between p-4 border-b border-gray-700">
            <div class="flex items-center">
                <img src="{{ asset('images/Logo.png') }}" class="w-8 h-8">
                <span class="ml-3 text-white font-semibold">{{ Auth::user()->name }}</span>
            </div>
            <button @click="open = false" class="text-gray-400 hover:text-white">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Enlaces de navegación -->
        <div class="px-4 py-6 space-y-3">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')"
                class="flex items-center p-3 text-base font-medium text-white rounded-lg hover:bg-blue-500/20 group border-l-4 {{ request()->routeIs('dashboard') ? 'border-blue-500 bg-blue-500/20' : 'border-transparent' }}">
                <i class="fa-solid fa-gauge-high w-6"></i>
                <span class="ml-3">{{ __('Dashboard') }}</span>
            </x-responsive-nav-link>

            <x-responsive-nav-link :href="route('parkings.index')" :active="request()->routeIs('parkings.index')"
                class="flex items-center p-3 text-base font-medium text-white rounded-lg hover:bg-blue-500/20 group border-l-4 {{ request()->routeIs('parkings.index') ? 'border-blue-500 bg-blue-500/20' : 'border-transparent' }}">
                <i class="fa-solid fa-car w-6"></i>
                <span class="ml-3">{{ __('Parkings') }}</span>
            </x-responsive-nav-link>

            @if(Auth::user()->isAdmin)  
            <x-responsive-nav-link :href="route('plantas.index')" :active="request()->routeIs('plantas.index')"
                class="flex items-center p-3 text-base font-medium text-white rounded-lg hover:bg-blue-500/20 group border-l-4 {{ request()->routeIs('plantas.index') ? 'border-blue-500 bg-blue-500/20' : 'border-transparent' }}">
                <i class="fa-solid fa-building w-6"></i>
                <span class="ml-3">{{ __('Plantas') }}</span>
            </x-responsive-nav-link>
            @endif
            
            @if(Auth::user() && (Auth::user()->isAdmin || Auth::user()->isOperador))
            <x-responsive-nav-link :href="route('plazas.index')" :active="request()->routeIs('plazas.index')"
                class="flex items-center p-3 text-base font-medium text-white rounded-lg hover:bg-blue-500/20 group border-l-4 {{ request()->routeIs('plazas.index') ? 'border-blue-500 bg-blue-500/20' : 'border-transparent' }}">
                <i class="fa-solid fa-square-parking w-6"></i>
                <span class="ml-3">{{ __('Plazas') }}</span>
            </x-responsive-nav-link>
            @endif

            <x-responsive-nav-link :href="route('tarifas.index')" :active="request()->routeIs('tarifas.index')"
                class="flex items-center p-3 text-base font-medium text-white rounded-lg hover:bg-blue-500/20 group border-l-4 {{ request()->routeIs('tarifas.index') ? 'border-blue-500 bg-blue-500/20' : 'border-transparent' }}">
                <i class="fa-solid fa-money-bill w-6"></i>
                <span class="ml-3">{{ __('Tarifas') }}</span>
            </x-responsive-nav-link>
        </div>

        <!-- Footer del menú móvil -->
        <div class="border-t border-gray-700 p-4 absolute bottom-0 w-full">
            <div class="flex flex-col space-y-4">
                <x-responsive-nav-link :href="route('profile.edit')"
                    class="flex items-center p-3 text-base font-medium text-white rounded-lg hover:bg-gray-700 group">
                    <i class="fa-solid fa-user w-6"></i>
                    <span class="ml-3">{{ __('Profile.edit') }}</span>
                </x-responsive-nav-link>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                        onclick="event.preventDefault(); this.closest('form').submit();"
                        class="flex items-center p-3 text-base font-medium text-white rounded-lg hover:bg-gray-700 group">
                        <i class="fa-solid fa-right-from-bracket w-6"></i>
                        <span class="ml-3">{{ __('Log Out') }}</span>
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</div>
</nav>

<!-- Añadir margen superior al contenido principal para compensar el nav fijo -->
<div class="pt-20"></div>

<!-- Script del reloj mejorado -->
<script>
    function updateClock() {
        const now = new Date();
        const hours = now.getHours().toString().padStart(2, '0');
        const minutes = now.getMinutes().toString().padStart(2, '0');
        const seconds = now.getSeconds().toString().padStart(2, '0');
        const clock = document.getElementById('digital-clock');
        if (clock) {
            clock.textContent = `${hours}:${minutes}:${seconds}`;
            // Animación suave al cambiar los segundos
            clock.classList.add('scale-105');
            setTimeout(() => clock.classList.remove('scale-105'), 200);
        }
    }

    // Iniciar el reloj inmediatamente y actualizarlo cada segundo
    updateClock();
    setInterval(updateClock, 1000);
</script>
