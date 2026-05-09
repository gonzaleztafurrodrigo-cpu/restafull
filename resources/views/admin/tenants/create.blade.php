<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo Restaurante</title>
    @laravelPWA
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * {
            -webkit-tap-highlight-color: transparent;
        }
    </style>
</head>

<body class="bg-gray-50 min-h-screen">

    <nav class="bg-white border-b border-gray-100 sticky top-0 z-20 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 py-3 flex justify-between items-center">
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.dashboard') }}"
                    class="w-9 h-9 bg-gray-100 hover:bg-gray-200 rounded-xl flex items-center justify-center transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-600" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <div class="flex items-center gap-2">
                    <div
                        class="bg-orange-500 text-white w-8 h-8 rounded-xl flex items-center justify-center font-bold text-xs">
                        RF</div>
                    <div>
                        <p class="font-bold text-gray-800 text-sm">Nuevo restaurante</p>
                        <p class="text-xs text-gray-400">Registrar en la plataforma</p>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-2xl mx-auto px-4 sm:px-6 py-8">

        @if (session('error'))
            <div
                class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-xl mb-6 text-sm flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-xl mb-6 text-sm">
                <ul class="space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>• {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 sm:p-8">

            <div class="mb-6">
                <h2 class="text-xl font-bold text-gray-800">Datos del restaurante</h2>
                <p class="text-sm text-gray-400 mt-1">Se creará automáticamente la base de datos y el usuario
                    administrador.</p>
            </div>

            <form method="POST" action="{{ route('admin.tenants.store') }}" class="space-y-5" id="create-form">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Nombre del restaurante</label>
                    <input type="text" name="name" value="{{ old('name') }}" autofocus
                        class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400"
                        placeholder="Ej: Pizza Hoot">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Email del administrador</label>
                    <input type="email" name="email" value="{{ old('email') }}"
                        class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400"
                        placeholder="admin@pizzahoot.com">
                    <p class="text-xs text-gray-400 mt-1">Se usará para crear el usuario administrador del restaurante.
                    </p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Dominio</label>
                    <div class="relative">
                        <input type="text" name="domain" value="{{ old('domain') }}"
                            class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400"
                            placeholder="pizzahoot.com">
                    </div>
                    <p class="text-xs text-gray-400 mt-1">Sin https:// ni www. Ej: pizzahoot.com</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Plan</label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="relative cursor-pointer">
                            <input type="radio" name="plan" value="basic" class="peer hidden"
                                {{ old('plan', 'basic') === 'basic' ? 'checked' : '' }}>
                            <div
                                class="border-2 border-gray-200 peer-checked:border-orange-500 peer-checked:bg-orange-50 rounded-xl p-4 transition">
                                <p class="font-semibold text-gray-800 text-sm">Básico</p>
                                <p class="text-xs text-gray-400 mt-1">Subdominio incluido</p>
                                <p class="text-orange-500 font-bold text-sm mt-2">5% comisión</p>
                            </div>
                        </label>
                        <label class="relative cursor-pointer">
                            <input type="radio" name="plan" value="pro" class="peer hidden"
                                {{ old('plan') === 'pro' ? 'checked' : '' }}>
                            <div
                                class="border-2 border-gray-200 peer-checked:border-orange-500 peer-checked:bg-orange-50 rounded-xl p-4 transition">
                                <p class="font-semibold text-gray-800 text-sm">Pro</p>
                                <p class="text-xs text-gray-400 mt-1">Dominio personalizado</p>
                                <p class="text-orange-500 font-bold text-sm mt-2">5% + $15.000/mes</p>
                            </div>
                        </label>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Comisión (%)</label>
                    <div class="relative">
                        <input type="number" name="commission_percentage" value="{{ old('commission_percentage', 5) }}"
                            step="0.01" min="0" max="100"
                            class="w-full border border-gray-200 rounded-xl px-4 py-3 pr-10 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400"
                            placeholder="2">
                        <span class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm">%</span>
                    </div>
                    <p class="text-xs text-gray-400 mt-1">Porcentaje que Restafull cobra sobre las ventas.</p>
                </div>

                <!-- Info box -->
                <div class="bg-blue-50 border border-blue-100 rounded-xl p-4">
                    <p class="text-xs font-semibold text-blue-700 mb-2">¿Qué se creará automáticamente?</p>
                    <ul class="space-y-1 text-xs text-blue-600">
                        <li>✓ Base de datos exclusiva del restaurante</li>
                        <li>✓ Roles: Admin, Cajero, Mesero, Domiciliario</li>
                        <li>✓ Usuario administrador con contraseña: <span class="font-bold">password123</span></li>
                        <li>✓ Configuración básica del restaurante</li>
                        <li>✓ Primer ciclo de facturación proporcional al mes</li>
                    </ul>
                </div>

                <button type="submit" id="submit-btn"
                    class="w-full bg-orange-500 hover:bg-orange-600 text-white py-3.5 rounded-2xl font-semibold text-sm transition shadow-sm">
                    Crear restaurante
                </button>
            </form>
        </div>
    </div>

    <script>
        document.getElementById('create-form').addEventListener('submit', function() {
            const btn = document.getElementById('submit-btn');
            btn.disabled = true;
            btn.textContent = 'Creando restaurante...';
            btn.classList.add('opacity-75', 'cursor-not-allowed');
        });
    </script>

</body>

</html>
