<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Iniciar sesión</title>
    @laravelPWA
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @include('tenant.partials.theme')
</head>
<body class="bg-gray-50 min-h-screen flex flex-col">

    <!-- Header -->
    <div class="bg-white px-4 py-3 flex items-center gap-3 border-b border-gray-100">
        <a href="{{ route('tenant.welcome') }}" class="w-9 h-9 bg-gray-100 rounded-xl flex items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
        </a>
        <p class="font-semibold text-gray-800">Iniciar sesión</p>
    </div>

    <div class="flex-1 px-5 py-8">

        <div class="mb-8">
            @if(!empty($settings['logo']))
                <img src="{{ Storage::url($settings['logo']) }}" class="w-14 h-14 rounded-2xl object-cover mx-auto mb-3">
            @else
                <div class="w-14 h-14 bg-orange-500 rounded-2xl flex items-center justify-center mx-auto mb-3">
                    <span class="text-white font-bold text-xl">RF</span>
                </div>
            @endif
            <p class="text-center text-gray-500 text-sm">{{ $settings['restaurant_name'] ?? 'Restafull' }}</p>
        </div>

        @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-xl mb-5 text-sm">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('tenant.client.login.post') }}" class="space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1.5">Correo electrónico</label>
                <input type="email" name="email" value="{{ old('email') }}" autofocus
                    class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400"
                    placeholder="E-mail">
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1.5">Contraseña</label>
                <input type="password" name="password"
                    class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400"
                    placeholder="••••••••">
            </div>

            <button type="submit"
                class="w-full bg-orange-500 hover:bg-orange-600 text-white py-3.5 rounded-2xl font-semibold text-sm transition shadow-sm mt-2">
                Iniciar sesión
            </button>
        </form>

        <p class="text-center text-sm text-gray-500 mt-6">
            ¿No tienes cuenta?
            <a href="{{ route('tenant.client.register') }}" class="text-orange-500 font-medium hover:underline">Regístrate</a>
        </p>

        <div class="mt-4 text-center">
            <a href="{{ route('tenant.menu') }}" class="text-xs text-gray-400 hover:text-gray-600">
                Continuar como invitado →
            </a>
        </div>

    </div>

</body>
</html>
