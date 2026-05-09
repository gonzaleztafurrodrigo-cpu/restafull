<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Mi cuenta</title>
    @laravelPWA
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * {
            -webkit-tap-highlight-color: transparent;
        }
    </style>
    @include('tenant.partials.theme')
</head>

<body class="bg-gray-50 min-h-screen pb-8">

    <!-- Header -->
    <div class="bg-orange-500 px-5 pt-12 pb-16 relative overflow-hidden">
        <div class="absolute inset-0 opacity-10">
            <div class="absolute -top-4 -right-4 w-32 h-32 bg-white rounded-full"></div>
            <div class="absolute -bottom-8 -left-8 w-40 h-40 bg-white rounded-full"></div>
        </div>
        <div class="relative flex justify-between items-start">
            <div>
                <p class="text-orange-100 text-sm mb-1">Bienvenido de vuelta</p>
                <h1 class="text-2xl font-bold text-white">{{ explode(' ', $customer['name'])[0] }}</h1>
                <p class="text-orange-100 text-sm mt-1">{{ $customer['email'] }}</p>
            </div>
            <div class="w-14 h-14 bg-white bg-opacity-20 rounded-2xl flex items-center justify-center">
                <span class="text-white font-bold text-2xl">{{ strtoupper(substr($customer['name'], 0, 1)) }}</span>
            </div>
        </div>
    </div>

    <div class="px-4 -mt-8 relative z-10 space-y-4">

        <!-- Acciones rápidas -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
            <a href="{{ route('tenant.menu') }}"
                class="block w-full bg-orange-500 text-white py-3.5 rounded-2xl font-semibold text-sm text-center mb-3">
                Hacer un pedido
            </a>
            <div class="grid grid-cols-2 gap-2">
                <a href="{{ route('tenant.client.orders') }}"
                    class="flex flex-col items-center gap-1.5 bg-gray-50 py-4 rounded-xl hover:bg-gray-100 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-orange-500" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    <span class="text-xs font-medium text-gray-700">Mis pedidos</span>
                </a>
                <a href="{{ route('tenant.client.addresses') }}"
                    class="flex flex-col items-center gap-1.5 bg-gray-50 py-4 rounded-xl hover:bg-gray-100 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-orange-500" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span class="text-xs font-medium text-gray-700">Direcciones</span>
                </a>
            </div>
        </div>

        <!-- Dirección predeterminada -->
        @if ($defaultAddress)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-2">Dirección predeterminada</p>
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 bg-orange-50 rounded-xl flex items-center justify-center flex-shrink-0 mt-0.5">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-orange-500" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-800">{{ $defaultAddress->label }}</p>
                        <p class="text-xs text-gray-500 mt-0.5">{{ $defaultAddress->address }}</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Pedidos recientes -->
        @if ($recentOrders->count() > 0)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-4 py-3 border-b border-gray-50 flex justify-between items-center">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Pedidos recientes</p>
                    <a href="{{ route('tenant.client.orders') }}" class="text-xs text-orange-500 font-medium">Ver
                        todos</a>
                </div>
                @foreach ($recentOrders as $order)
                    @php
                        $statusLabels = [
                            'pending' => 'Pendiente',
                            'confirmed' => 'Confirmado',
                            'preparing' => 'Preparando',
                            'ready' => 'Listo',
                            'dispatched' => 'En camino',
                            'collecting' => 'En camino',
                            'delivered' => 'Entregado',
                            'cancelled' => 'Cancelado',
                        ];
                        $statusColors = [
                            'pending' => 'text-yellow-600',
                            'confirmed' => 'text-blue-600',
                            'preparing' => 'text-purple-600',
                            'ready' => 'text-green-600',
                            'dispatched' => 'text-orange-500',
                            'collecting' => 'text-orange-500',
                            'delivered' => 'text-green-600',
                            'cancelled' => 'text-red-500',
                        ];
                    @endphp
                    @php $deliveryCost = $order->total - $order->subtotal; @endphp
                    <div class="px-4 py-3 border-b border-gray-50 last:border-0 flex justify-between items-center">
                        <div>
                            <p class="text-sm font-medium text-gray-800">{{ $order->order_number }}</p>
                            <p class="text-xs {{ $statusColors[$order->status] ?? 'text-gray-400' }} font-medium">
                                {{ $statusLabels[$order->status] ?? $order->status }}
                            </p>
                            @if ($deliveryCost > 0)
                                <p class="text-xs text-gray-400">Dom: ${{ number_format($deliveryCost, 0, ',', '.') }}
                                </p>
                            @endif
                        </div>
                        <span
                            class="text-sm font-semibold text-gray-800">${{ number_format($order->total, 0, ',', '.') }}</span>
                    </div>
                @endforeach
            </div>
        @endif

        <!-- Cerrar sesión -->
        <form method="POST" action="{{ route('tenant.client.logout') }}">
            @csrf
            <button type="submit" class="w-full text-center text-sm text-gray-400 hover:text-gray-600 py-2">
                Cerrar sesión
            </button>
        </form>

    </div>

    <div id="tenant-id" data-id="{{ tenancy()->tenant->id }}" style="display:none;"></div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (window.Echo) {
                const tenantId = document.getElementById('tenant-id').dataset.id;
                window.Echo.channel(`orders.${tenantId}`)
                    .listen('.order.status', (data) => {
                        location.reload();
                    });
            }
        });
    </script>

</body>

</html>
