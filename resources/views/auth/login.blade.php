<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restafull — Iniciar sesión</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>* { -webkit-tap-highlight-color: transparent; }</style>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center px-4">

    <div class="w-full max-w-sm">

        <!-- Logo -->
        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-orange-500 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg">
                <span class="text-white font-bold text-2xl">RF</span>
            </div>
            <h1 class="text-2xl font-bold text-gray-800">Restafull</h1>
            <p class="text-sm text-gray-400 mt-1">Panel de administración</p>
        </div>

        <!-- Card -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8">

            @if($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-xl mb-5 text-sm">
                    @foreach($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            @session('status')
                <div class="bg-green-50 border border-green-200 text-green-600 px-4 py-3 rounded-xl mb-5 text-sm">
                    {{ $value }}
                </div>
            @endsession

            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Correo electrónico</label>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus
                        class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400"
                        placeholder="E-mail">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Contraseña</label>
                    <input type="password" name="password" required
                        class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400"
                        placeholder="••••••••">
                </div>

                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember" class="rounded border-gray-300 text-orange-500">
                        <span class="text-sm text-gray-500">Recordarme</span>
                    </label>
                    @if(Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-sm text-orange-500 hover:underline">
                            ¿Olvidaste tu contraseña?
                        </a>
                    @endif
                </div>

                <button type="submit"
                    class="w-full bg-orange-500 hover:bg-orange-600 text-white py-3.5 rounded-2xl font-semibold text-sm transition shadow-sm">
                    Iniciar sesión
                </button>
            </form>
        </div>

        <p class="text-center text-xs text-gray-400 mt-6">© 2026 Restafull. Todos los derechos reservados.</p>
    </div>

</body>
</html>
