<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión</title>
    @laravelPWA
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @include('tenant.partials.theme')
</head>

<body class="bg-gray-50 min-h-screen flex items-center justify-center px-4">

    <div class="w-full max-w-sm">

        <!-- Logo -->
        <div class="flex flex-col items-center mb-8">
            @php $settings = \Illuminate\Support\Facades\DB::table('settings')->pluck('value', 'key')->toArray(); @endphp
            @if (!empty($settings['logo']))
                <img src="{{ Storage::url($settings['logo']) }}"
                    class="w-14 h-14 rounded-2xl object-cover mb-3 shadow-sm">
            @else
                <div
                    class="bg-orange-500 text-white w-14 h-14 rounded-2xl flex items-center justify-center font-bold text-xl mb-3 shadow-sm">
                    {{ strtoupper(substr($settings['restaurant_name'] ?? 'R', 0, 2)) }}
                </div>
            @endif
            <h1 class="text-xl font-semibold text-gray-800">{{ $settings['restaurant_name'] ?? 'Mi Restaurante' }}</h1>
            <p class="text-sm text-gray-400 mt-1">Inicia sesión para continuar</p>
        </div>

        <!-- Card -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8">

            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-xl mb-5 text-sm">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('tenant.login.post') }}" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Correo electrónico</label>
                    <input type="email" name="email" value="{{ old('email') }}" autofocus
                        class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent transition"
                        placeholder="E-mail">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Contraseña</label>
                    <input type="password" name="password"
                        class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent transition"
                        placeholder="••••••••">
                </div>

                <div class="pt-2">
                    <button type="submit"
                        class="w-full bg-orange-500 hover:bg-orange-600 text-white py-3 rounded-xl font-medium text-sm transition shadow-sm">
                        Iniciar sesión
                    </button>
                </div>

            </form>
        </div>

        <p class="text-center text-xs text-gray-400 mt-6">
            ¿Eres cliente?
            <a href="{{ route('tenant.menu') }}" class="text-orange-500 hover:underline">Ver menú</a>
        </p>

    </div>

</body>

</html>
