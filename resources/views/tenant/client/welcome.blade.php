<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>{{ $settings['restaurant_name'] ?? 'Bienvenido' }}</title>
    @laravelPWA
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * {
            -webkit-tap-highlight-color: transparent;
        }
    </style>
    @include('tenant.partials.theme')
</head>

<body class="bg-gray-50 min-h-screen flex flex-col">

    <!-- Hero -->
    <div class="bg-orange-500 px-6 pt-16 pb-20 text-center relative overflow-hidden">
        <div class="absolute inset-0 opacity-10">
            <div class="absolute top-4 left-4 w-32 h-32 bg-white rounded-full"></div>
            <div class="absolute bottom-4 right-4 w-48 h-48 bg-white rounded-full"></div>
        </div>
        <div class="relative">
            @if (!empty($settings['logo']))
                <img src="{{ Storage::url($settings['logo']) }}"
                    class="w-20 h-20 rounded-2xl object-cover mx-auto mb-4 shadow-lg">
            @else
                <div class="w-20 h-20 bg-white bg-opacity-20 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <span class="text-white font-bold text-2xl">RF</span>
                </div>
            @endif
            <h1 class="text-2xl font-bold text-white mb-2">{{ $settings['restaurant_name'] ?? 'Restafull' }}</h1>
            @if (!empty($settings['delivery_time']))
                <p class="text-orange-100 text-sm">🕐 {{ $settings['delivery_time'] }} min de entrega</p>
            @endif
            @if (!empty($settings['restaurant_phone']))
                <p class="text-orange-100 text-sm mt-1">📞 {{ $settings['restaurant_phone'] }}</p>
            @endif
        </div>
    </div>

    <!-- Contenido -->
    <div class="flex-1 px-5 -mt-8 relative z-10">

        @if ($customer)
            <!-- Cliente logueado -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 mb-4">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-11 h-11 bg-orange-100 rounded-xl flex items-center justify-center">
                        <span
                            class="text-orange-500 font-bold text-lg">{{ strtoupper(substr($customer['name'], 0, 1)) }}</span>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-800">Hola, {{ explode(' ', $customer['name'])[0] }}</p>
                        <p class="text-xs text-gray-400">{{ $customer['email'] }}</p>
                    </div>
                </div>
                <a href="{{ route('tenant.menu') }}"
                    class="block w-full bg-orange-500 hover:bg-orange-600 text-white py-3.5 rounded-2xl font-semibold text-sm text-center transition shadow-sm mb-3">
                    Ver menú y pedir
                </a>
                <div class="grid grid-cols-2 gap-2">
                    <a href="{{ route('tenant.client.orders') }}"
                        class="flex items-center justify-center gap-2 bg-gray-50 hover:bg-gray-100 text-gray-700 py-2.5 rounded-xl text-xs font-medium transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        Mis pedidos
                    </a>
                    <a href="{{ route('tenant.client.addresses') }}"
                        class="flex items-center justify-center gap-2 bg-gray-50 hover:bg-gray-100 text-gray-700 py-2.5 rounded-xl text-xs font-medium transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Direcciones
                    </a>
                </div>
            </div>
            <form method="POST" action="{{ route('tenant.client.logout') }}">
                @csrf
                <button type="submit" class="w-full text-center text-sm text-gray-400 hover:text-gray-600 py-2">
                    Cerrar sesión
                </button>
            </form>
        @else
            <!-- No logueado -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 mb-4">
                <h2 class="font-semibold text-gray-800 text-center mb-4">¿Cómo deseas continuar?</h2>

                <a href="{{ route('tenant.menu') }}"
                    class="block w-full bg-orange-500 hover:bg-orange-600 text-white py-3.5 rounded-2xl font-semibold text-sm text-center transition shadow-sm mb-3">
                    Pedir como invitado
                </a>

                <div class="flex gap-2">
                    <a href="{{ route('tenant.client.login') }}"
                        class="flex-1 border border-orange-400 text-orange-500 py-3 rounded-2xl font-medium text-sm text-center hover:bg-orange-50 transition">
                        Iniciar sesión
                    </a>
                    <a href="{{ route('tenant.client.register') }}"
                        class="flex-1 border border-gray-200 text-gray-600 py-3 rounded-2xl font-medium text-sm text-center hover:bg-gray-50 transition">
                        Registrarse
                    </a>
                </div>
            </div>

            <p class="text-center text-xs text-gray-400 mb-4">
                Al registrarte puedes guardar tus direcciones y ver el historial de pedidos.
            </p>

            <!-- Botón instalar PWA -->
            <div id="pwa-install-container" class="hidden">
                <button id="pwa-install-btn"
                    class="w-full flex items-center justify-center gap-3 bg-white border border-orange-200 text-orange-500 py-3.5 rounded-2xl font-medium text-sm shadow-sm hover:bg-orange-50 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    Instalar la app
                </button>
                <p class="text-center text-xs text-gray-400 mt-2">Instala la app para acceder más rápido</p>
            </div>
        @endif

    </div>

    <script>
        let deferredPrompt;

        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            deferredPrompt = e;
            document.getElementById('pwa-install-container').classList.remove('hidden');
        });

        document.getElementById('pwa-install-btn').addEventListener('click', async () => {
            if (!deferredPrompt) return;
            deferredPrompt.prompt();
            const {
                outcome
            } = await deferredPrompt.userChoice;
            deferredPrompt = null;
            // No ocultamos el botón para que puedan reinstalar si la eliminaron
        });

        window.addEventListener('appinstalled', () => {
            // Solo ocultamos cuando se acaba de instalar en esta sesión
            document.getElementById('pwa-install-container').classList.add('hidden');
        });
    </script>

</body>

</html>
