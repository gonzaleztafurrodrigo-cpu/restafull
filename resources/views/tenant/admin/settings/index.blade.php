<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuración</title>
    @laravelPWA
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * {
            -webkit-tap-highlight-color: transparent;
        }
    </style>
    @include('tenant.partials.theme')
</head>

<body class="bg-gray-50 min-h-screen">

    <nav class="bg-white border-b border-gray-100 sticky top-0 z-20 shadow-sm">
        <div class="max-w-4xl mx-auto px-4 py-3 flex justify-between items-center">
            <div class="flex items-center gap-3">
                <a href="{{ route('tenant.admin') }}"
                    class="w-9 h-9 bg-gray-100 rounded-xl flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-600" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <div>
                    <p class="font-bold text-gray-800 text-sm">Configuración</p>
                    <p class="text-xs text-gray-400">Ajustes del restaurante</p>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-4xl mx-auto px-4 py-6 space-y-6">

        @if (session('success'))
            <div id="success-msg"
                class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl flex items-center gap-2 text-sm transition-all duration-500">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                {{ session('success') }}
            </div>
            <script>
                setTimeout(() => {
                    const msg = document.getElementById('success-msg');
                    if (msg) {
                        msg.style.opacity = '0';
                        setTimeout(() => msg.remove(), 500);
                    }
                }, 3000);
            </script>
        @endif

        @if ($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-xl text-sm">
                <ul class="space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('tenant.admin.settings.update') }}" enctype="multipart/form-data"
            class="space-y-6">
            @csrf

            <!-- Información general -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <h3 class="font-semibold text-gray-800 mb-5 flex items-center gap-2">
                    <div class="w-8 h-8 bg-orange-50 rounded-xl flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-orange-500" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    Información del restaurante
                </h3>

                <!-- Logo -->
                <div class="flex items-center gap-4 mb-5">
                    @if (!empty($settings['logo']))
                        <img src="{{ Storage::url($settings['logo']) }}"
                            class="w-20 h-20 rounded-2xl object-cover border border-gray-100">
                    @else
                        <div class="w-20 h-20 bg-orange-50 rounded-2xl flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-orange-200" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                    @endif
                    <div class="flex-1">
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Logo del restaurante</label>
                        <input type="file" name="logo" accept="image/*"
                            class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400 bg-white">
                        <p class="text-xs text-gray-400 mt-1">Máximo 2MB. JPG, PNG, WebP.</p>
                    </div>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Nombre del restaurante</label>
                        <input type="text" name="restaurant_name"
                            value="{{ old('restaurant_name', $settings['restaurant_name'] ?? '') }}"
                            class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400"
                            placeholder="Ej: Pizza Hoot">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1.5">Teléfono</label>
                            <input type="text" name="restaurant_phone"
                                value="{{ old('restaurant_phone', $settings['restaurant_phone'] ?? '') }}"
                                class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400"
                                placeholder="3001234567">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1.5">Email</label>
                            <input type="email" name="restaurant_email"
                                value="{{ old('restaurant_email', $settings['restaurant_email'] ?? '') }}"
                                class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400"
                                placeholder="contacto@restaurante.com">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Dirección</label>
                        <textarea name="restaurant_address" rows="2"
                            class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400 resize-none"
                            placeholder="Calle 123 # 45-67, Barrio, Ciudad">{{ old('restaurant_address', $settings['restaurant_address'] ?? '') }}</textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Color principal de la
                            tienda</label>
                        <div class="flex items-center gap-3">
                            <input type="color" name="primary_color"
                                value="{{ old('primary_color', $settings['primary_color'] ?? '#f97316') }}"
                                class="w-12 h-12 rounded-xl border border-gray-200 cursor-pointer p-1">
                            <div>
                                <p class="text-xs text-gray-500">Este color se aplicará a botones, textos destacados y
                                    elementos de tu tienda.</p>
                                <div class="flex gap-2 mt-2">
                                    @foreach (['#f97316', '#ef4444', '#8b5cf6', '#3b82f6', '#10b981', '#f59e0b', '#ec4899', '#14b8a6'] as $color)
                                        <button type="button" onclick="setColor('{{ $color }}')"
                                            class="w-6 h-6 rounded-lg border-2 border-white shadow-sm hover:scale-110 transition"
                                            style="background-color: {{ $color }}">
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Horarios y domicilio -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <h3 class="font-semibold text-gray-800 mb-5 flex items-center gap-2">
                    <div class="w-8 h-8 bg-blue-50 rounded-xl flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-blue-500" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    Horarios y domicilio
                </h3>
                <div class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1.5">Lunes a Viernes</label>
                            <input type="text" name="schedule_mon_fri"
                                value="{{ old('schedule_mon_fri', $settings['schedule_mon_fri'] ?? '') }}"
                                class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400"
                                placeholder="10am - 10pm">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1.5">Sábado</label>
                            <input type="text" name="schedule_sat"
                                value="{{ old('schedule_sat', $settings['schedule_sat'] ?? '') }}"
                                class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400"
                                placeholder="10am - 11pm">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1.5">Domingo</label>
                            <input type="text" name="schedule_sun"
                                value="{{ old('schedule_sun', $settings['schedule_sun'] ?? '') }}"
                                class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400"
                                placeholder="12pm - 9pm">
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1.5">Tiempo de entrega</label>
                            <input type="text" name="delivery_time"
                                value="{{ old('delivery_time', $settings['delivery_time'] ?? '') }}"
                                class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400"
                                placeholder="30-45 min">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1.5">Pedido mínimo (COP)</label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm">$</span>
                                <input type="number" name="min_order"
                                    value="{{ old('min_order', (int) ($settings['min_order'] ?? 0)) }}"
                                    class="w-full border border-gray-200 rounded-xl pl-8 pr-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400"
                                    placeholder="0">
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1.5">Costo de domicilio
                                (COP)</label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm">$</span>
                                <input type="number" name="delivery_cost"
                                    value="{{ old('delivery_cost', (int) ($settings['delivery_cost'] ?? 0)) }}"
                                    class="w-full border border-gray-200 rounded-xl pl-8 pr-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400"
                                    placeholder="0">
                            </div>
                            <p class="text-xs text-gray-400 mt-1">0 = domicilio gratis.</p>
                        </div>
                    </div>
                </div>
            </div>

            <button type="submit"
                class="w-full bg-orange-500 hover:bg-orange-600 text-white py-3 rounded-2xl font-semibold text-sm transition shadow-sm">
                Guardar configuración
            </button>
        </form>

        <!-- Cuentas bancarias -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <h3 class="font-semibold text-gray-800 mb-5 flex items-center gap-2">
                <div class="w-8 h-8 bg-green-50 rounded-xl flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-green-500" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                    </svg>
                </div>
                Cuentas bancarias para transferencias
            </h3>

            @forelse($bankAccounts as $account)
                <div
                    class="border border-gray-100 rounded-2xl p-4 mb-3 {{ !$account->is_active ? 'opacity-60' : '' }}">
                    <div class="flex justify-between items-start">
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <p class="font-semibold text-gray-800 text-sm">{{ $account->bank_name }}</p>
                                <span
                                    class="bg-blue-50 text-blue-500 text-xs px-2 py-0.5 rounded-lg">{{ $account->account_type }}</span>
                                @if (!$account->is_active)
                                    <span class="bg-red-50 text-red-400 text-xs px-2 py-0.5 rounded-lg">Inactiva</span>
                                @endif
                            </div>
                            <p class="text-xs text-gray-500">{{ $account->account_number }}</p>
                            <p class="text-xs text-gray-500">
                                {{ $account->owner_name }}{{ $account->owner_id ? ' — ' . $account->owner_id : '' }}
                            </p>
                        </div>
                        <div class="flex items-center gap-2">
                            <form method="POST"
                                action="{{ route('tenant.admin.settings.bank.toggle', $account->id) }}">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                    class="{{ $account->is_active ? 'bg-yellow-50 hover:bg-yellow-100 text-yellow-600' : 'bg-green-50 hover:bg-green-100 text-green-600' }} w-8 h-8 rounded-xl flex items-center justify-center transition">
                                    @if ($account->is_active)
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                        </svg>
                                    @else
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7" />
                                        </svg>
                                    @endif
                                </button>
                            </form>
                            <form method="POST"
                                action="{{ route('tenant.admin.settings.bank.destroy', $account->id) }}"
                                onsubmit="return confirm('¿Eliminar esta cuenta bancaria?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="bg-red-50 hover:bg-red-100 text-red-500 w-8 h-8 rounded-xl flex items-center justify-center transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="border border-dashed border-gray-200 rounded-2xl p-6 text-center mb-4">
                    <p class="text-sm text-gray-400">No hay cuentas bancarias configuradas.</p>
                </div>
            @endforelse

            <!-- Agregar cuenta -->
            <div class="border-t border-gray-100 pt-5 mt-2">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-4">Agregar cuenta bancaria</p>
                <form method="POST" action="{{ route('tenant.admin.settings.bank.store') }}" class="space-y-3">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1.5">Banco</label>
                            <input type="text" name="bank_name"
                                class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400"
                                placeholder="Bancolombia, Nequi...">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1.5">Tipo de cuenta</label>
                            <select name="account_type"
                                class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400 bg-white">
                                <option value="Ahorros">Ahorros</option>
                                <option value="Corriente">Corriente</option>
                                <option value="Nequi">Nequi</option>
                                <option value="Daviplata">Daviplata</option>
                                <option value="PSE">PSE</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1.5">Número de cuenta</label>
                            <input type="text" name="account_number"
                                class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400"
                                placeholder="123456789">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1.5">Titular</label>
                            <input type="text" name="owner_name"
                                class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400"
                                placeholder="Nombre del titular">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-medium text-gray-600 mb-1.5">NIT / Cédula <span
                                    class="text-gray-400 font-normal">(opcional)</span></label>
                            <input type="text" name="owner_id"
                                class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400"
                                placeholder="123456789">
                        </div>
                    </div>
                    <button type="submit"
                        class="w-full bg-green-500 hover:bg-green-600 text-white py-3 rounded-2xl font-semibold text-sm transition shadow-sm">
                        Agregar cuenta
                    </button>
                </form>
            </div>
        </div>

    </div>

    <script>
        function setColor(color) {
            document.querySelector('input[name="primary_color"]').value = color;
        }
    </script>

</body>

</html>
