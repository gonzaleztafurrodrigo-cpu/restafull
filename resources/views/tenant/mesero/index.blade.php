<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Mesero</title>
    @laravelPWA
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * {
            -webkit-tap-highlight-color: transparent;
        }

        ::-webkit-scrollbar {
            display: none;
        }
    </style>
    @include('tenant.partials.theme')
</head>

<body class="bg-gray-50 min-h-screen pb-8">

    <!-- Header -->
    <div class="bg-orange-500 px-5 pt-10 pb-14 relative overflow-hidden">
        <div class="absolute inset-0 opacity-10">
            <div class="absolute -top-4 -right-4 w-32 h-32 bg-white rounded-full"></div>
            <div class="absolute -bottom-8 -left-8 w-40 h-40 bg-white rounded-full"></div>
        </div>
        <div class="relative flex justify-between items-start">
            <div class="flex items-center gap-3">
                @php $settingsWaiter = \Illuminate\Support\Facades\DB::table('settings')->pluck('value', 'key')->toArray(); @endphp
                @if (!empty($settingsWaiter['logo']))
                    <img src="{{ Storage::url($settingsWaiter['logo']) }}" class="w-11 h-11 rounded-xl object-cover">
                @else
                    <div class="w-11 h-11 bg-white bg-opacity-20 rounded-xl flex items-center justify-center">
                        <span class="text-white font-bold text-lg">RF</span>
                    </div>
                @endif
                <div>
                    <p class="text-orange-100 text-xs">Mesero</p>
                    <h1 class="text-lg font-bold text-white">{{ session('tenant_user')['name'] }}</h1>
                </div>
            </div>
            <form method="POST" action="{{ route('tenant.logout') }}">
                @csrf
                <button type="submit"
                    class="w-10 h-10 bg-white bg-opacity-20 rounded-xl flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h6a2 2 0 012 2v1" />
                    </svg>
                </button>
            </form>
        </div>
    </div>

    <div id="tenant-id" data-id="{{ tenancy()->tenant->id }}" style="display:none;"></div>

    <div class="px-4 -mt-8 relative z-10">

        @if (session('success'))
            <div id="success-msg"
                class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl mb-4 text-sm flex items-center gap-2 transition-all duration-500">
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
                        msg.style.transform = 'translateY(-10px)';
                        setTimeout(() => msg.remove(), 500);
                    }
                }, 3000);
            </script>
        @endif

        <!-- Stats -->
        <div class="grid grid-cols-2 gap-3 mb-4">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
                <p class="text-xs text-gray-400 mb-1">Mesas ocupadas</p>
                <p class="text-2xl font-bold text-orange-500">{{ count($activeTableIds) }}</p>
            </div>
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
                <p class="text-xs text-gray-400 mb-1">Mesas libres</p>
                <p class="text-2xl font-bold text-green-500">{{ $tables->count() - count($activeTableIds) }}</p>
            </div>
        </div>

        <!-- Mesas -->
        <h2 class="text-sm font-semibold text-gray-700 mb-3">Mesas</h2>
        <div class="grid grid-cols-2 gap-3">
            @forelse($tables as $table)
                @php
                    $order = $activeOrdersByTable[$table->id] ?? null;
                    $occupied = in_array($table->id, $activeTableIds);
                @endphp

                @if ($occupied && $order)
                    <a href="{{ route('tenant.mesero.order', $order->id) }}"
                        class="bg-white rounded-2xl border-2 border-orange-300 shadow-sm p-4 flex flex-col gap-2 active:scale-95 transition">
                        <div class="flex justify-between items-start">
                            <div class="w-10 h-10 rounded-xl bg-orange-50 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-orange-500" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                </svg>
                            </div>
                            <span
                                class="bg-orange-50 text-orange-500 text-xs px-2 py-0.5 rounded-lg font-medium">Ocupada</span>
                        </div>
                        <div>
                            <p class="font-bold text-gray-800">{{ $table->name }}</p>
                            <p class="text-xs text-gray-400">{{ $order->items->count() }} producto(s)</p>
                        </div>
                        <div class="border-t border-gray-100 pt-2 flex justify-between items-center">
                            <span class="text-xs text-gray-500">Total</span>
                            <span
                                class="text-sm font-bold text-orange-500">${{ number_format($order->total, 0, ',', '.') }}</span>
                        </div>
                    </a>
                @else
                    <a href="{{ route('tenant.mesero.create', ['mesa' => $table->id]) }}"
                        class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 flex flex-col gap-2 active:scale-95 transition">
                        <div class="flex justify-between items-start">
                            <div class="w-10 h-10 rounded-xl bg-green-50 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-green-500" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                </svg>
                            </div>
                            <span
                                class="bg-green-50 text-green-500 text-xs px-2 py-0.5 rounded-lg font-medium">Libre</span>
                        </div>
                        <div>
                            <p class="font-bold text-gray-800">{{ $table->name }}</p>
                            <p class="text-xs text-gray-400">{{ $table->capacity }} personas</p>
                        </div>
                        <div class="border-t border-gray-100 pt-2">
                            <span class="text-xs text-gray-400">Toca para tomar pedido</span>
                        </div>
                    </a>
                @endif

            @empty
                <div class="col-span-2 bg-white rounded-2xl border border-gray-100 shadow-sm p-12 text-center">
                    <p class="text-sm text-gray-400">No hay mesas configuradas.</p>
                </div>
            @endforelse
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (window.Echo) {
                const tenantId = document.getElementById('tenant-id').dataset.id;
                window.Echo.channel(`orders.${tenantId}`)
                    .listen('.order.status', (data) => {
                        if (['delivered', 'cancelled'].includes(data.status)) location.reload();
                    })
                    .listen('.new.order', () => {
                        location.reload();
                    });
            }
        });
    </script>

</body>

</html>
