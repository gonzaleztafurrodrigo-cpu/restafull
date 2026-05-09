<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Instalar App Empleados</title>
    <link rel="manifest" href="/empleados/manifest.json">
    <meta name="theme-color" content="{{ $settings['primary_color'] ?? '#f97316' }}">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title"
        content="{{ ($settings['restaurant_name'] ?? 'Mi Restaurante') . ' - Empleados' }}">
    @if (!empty($settings['logo']))
        <link rel="apple-touch-icon" href="{{ Storage::url($settings['logo']) }}">
    @else
        <link rel="apple-touch-icon" href="/images/icons/icon-192x192.png">
    @endif
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @include('tenant.partials.theme')
</head>

<body class="bg-gray-900 min-h-screen flex items-center justify-center px-4">
    <div class="w-full max-w-sm text-center">
        @if (!empty($settings['logo']))
            <img src="{{ Storage::url($settings['logo']) }}"
                class="w-20 h-20 rounded-2xl object-cover mx-auto mb-4 shadow-lg">
        @else
            <div class="w-20 h-20 bg-orange-500 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg">
                <span
                    class="text-white font-bold text-2xl">{{ strtoupper(substr($settings['restaurant_name'] ?? 'R', 0, 2)) }}</span>
            </div>
        @endif

        <h1 class="text-2xl font-bold text-white mb-1">{{ $settings['restaurant_name'] ?? 'Mi Restaurante' }}</h1>
        <p class="text-gray-400 text-sm mb-8">App para empleados</p>

        <div class="bg-gray-800 rounded-2xl p-6 mb-6 text-left space-y-4">
            <p class="text-white font-semibold text-sm mb-3">📱 Cómo instalar:</p>
            <div class="flex items-start gap-3">
                <span
                    class="bg-orange-500 text-white w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0">1</span>
                <p class="text-gray-300 text-sm">En Chrome, toca el menú ⋮ y selecciona <strong
                        class="text-white">"Añadir a pantalla de inicio"</strong></p>
            </div>
            <div class="flex items-start gap-3">
                <span
                    class="bg-orange-500 text-white w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0">2</span>
                <p class="text-gray-300 text-sm">En Safari, toca <strong class="text-white">compartir ↑</strong> y
                    selecciona <strong class="text-white">"Añadir a inicio"</strong></p>
            </div>
            <div class="flex items-start gap-3">
                <span
                    class="bg-orange-500 text-white w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0">3</span>
                <p class="text-gray-300 text-sm">La app abrirá directamente en el <strong class="text-white">login de
                        empleados</strong></p>
            </div>
        </div>

        <button id="install-btn"
            class="hidden w-full bg-orange-500 hover:bg-orange-600 text-white py-4 rounded-2xl font-semibold text-sm transition shadow-lg mb-4">
            📲 Instalar app ahora
        </button>

        <button id="install-btn"
            class="w-full bg-orange-500 hover:bg-orange-600 text-white py-4 rounded-2xl font-semibold text-sm transition shadow-lg mb-4 flex items-center justify-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
            </svg>
            Instalar app
        </button>

        <a href="/login"
            class="block w-full bg-gray-700 hover:bg-gray-600 text-white py-4 rounded-2xl font-semibold text-sm transition text-center">
            Ir al login
        </a>
    </div>

    <script>
        let deferredPrompt;

        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            deferredPrompt = e;
        });

        document.getElementById('install-btn').addEventListener('click', async () => {
            if (deferredPrompt) {
                deferredPrompt.prompt();
                const { outcome } = await deferredPrompt.userChoice;
                deferredPrompt = null;
            } else {
                // Si no hay prompt (iOS o ya instalado) mostrar instrucciones
                alert('Para instalar: en Safari toca compartir ↑ y selecciona "Añadir a inicio". En Chrome toca ⋮ y "Añadir a pantalla de inicio".');
            }
        });
    </script>
</body>

</html>
