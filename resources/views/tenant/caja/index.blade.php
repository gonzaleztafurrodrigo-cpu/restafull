<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Caja</title>
    @laravelPWA
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @include('tenant.partials.theme')
    <style>
        * {
            -webkit-tap-highlight-color: transparent;
        }

        ::-webkit-scrollbar {
            width: 4px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }

        ::-webkit-scrollbar-thumb {
            background: #e2e8f0;
            border-radius: 4px;
        }
    </style>
</head>

<body class="bg-gray-50 min-h-screen">

    <nav
        class="bg-white border-b border-gray-100 px-4 py-3 flex justify-between items-center sticky top-0 z-20 shadow-sm">
        <div class="flex items-center gap-3">
            @php $settings = \Illuminate\Support\Facades\DB::table('settings')->pluck('value', 'key')->toArray(); @endphp
            @if (!empty($settings['logo']))
                <img src="{{ Storage::url($settings['logo']) }}" class="w-10 h-10 rounded-xl object-cover">
            @else
                <div
                    class="bg-orange-500 text-white w-10 h-10 rounded-xl flex items-center justify-center font-bold text-sm">
                    RF</div>
            @endif
            <div>
                <div class="flex items-center gap-2">
                    <span class="font-bold text-gray-800">Caja</span>
                    <div class="flex items-center gap-1.5">
                        <span class="w-2 h-2 bg-green-400 rounded-full animate-pulse" id="connection-dot"></span>
                        <span class="text-xs text-gray-400" id="connection-status">Conectando...</span>
                    </div>
                </div>
                <p class="text-xs text-gray-400">{{ $settings['restaurant_name'] ?? '' }}</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('tenant.caja.shift') }}"
                class="bg-gray-100 hover:bg-gray-200 text-gray-600 px-3 py-2 rounded-xl text-xs font-medium transition flex items-center gap-1.5">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Turno
            </a>
            <a href="{{ route('tenant.caja.arqueo') }}"
                class="bg-orange-500 hover:bg-orange-600 text-white px-3 py-2 rounded-xl text-xs font-medium transition flex items-center gap-1.5 shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 11h.01M12 11h.01M15 11h.01M4 19h16a2 2 0 002-2V7a2 2 0 00-2-2H4a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                Arqueo
            </a>
            <div class="flex items-center gap-2 border-l border-gray-100 pl-2">
                <span class="text-xs text-gray-500 hidden sm:block">{{ session('tenant_user')['name'] }}</span>
                <form method="POST" action="{{ route('tenant.logout') }}">
                    @csrf
                    <button type="submit"
                        class="text-gray-400 hover:text-red-400 transition w-9 h-9 flex items-center justify-center bg-gray-50 rounded-xl">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h6a2 2 0 012 2v1" />
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <div id="tenant-id" data-id="{{ tenancy()->tenant->id }}" style="display:none;"></div>

    <div class="max-w-7xl mx-auto px-4 py-4">

        @if ($openShift && $shiftStats)
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-4">
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
                    <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Nuevos</p>
                    <p class="text-3xl font-bold text-blue-500">{{ $shiftStats['new'] }}</p>
                    <p class="text-xs text-gray-400 mt-0.5">En cola</p>
                </div>
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
                    <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Por recaudar</p>
                    <p class="text-3xl font-bold text-orange-500">{{ $shiftStats['collecting'] }}</p>
                    <p class="text-xs text-gray-400 mt-0.5">Domicilios</p>
                </div>
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
                    <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Completados</p>
                    <p class="text-3xl font-bold text-green-500">{{ $shiftStats['delivered'] }}</p>
                    <p class="text-xs text-gray-400 mt-0.5">Este turno</p>
                </div>
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
                    <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Ventas turno</p>
                    <p class="text-2xl font-bold text-gray-800">
                        ${{ number_format($shiftStats['total_sales'], 0, ',', '.') }}</p>
                    <p class="text-xs text-gray-400 mt-0.5">Entregados</p>
                </div>
            </div>
        @else
            <div class="bg-yellow-50 border border-yellow-200 rounded-2xl p-4 mb-4 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-yellow-500" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <p class="text-sm text-yellow-700 font-medium">No tienes un turno abierto</p>
                </div>
                <a href="{{ route('tenant.caja.shift') }}"
                    class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1.5 rounded-xl text-xs font-medium transition">
                    Abrir turno
                </a>
            </div>
        @endif

        <!-- Layout principal: 2 columnas en PC -->
        <div>

            <!-- Columna izquierda: Cola de pedidos -->
            <div class="">
                <div class="flex justify-between items-center mb-3">
                    <h2 class="text-lg font-bold text-gray-800">Cola de pedidos</h2>
                    <span class="bg-orange-50 text-orange-500 px-3 py-1 rounded-xl text-xs font-medium"
                        id="orders-count">
                        {{ $orders->count() }} pedido(s)
                    </span>
                </div>

                <div id="orders-container" class="space-y-3">
                    @forelse($orders as $order)
                        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden cursor-pointer hover:border-orange-200 transition"
                            id="order-{{ $order->id }}" onclick="selectOrder({{ $order->id }})">

                            <!-- Encabezado -->
                            <div class="px-4 py-3 border-b border-gray-50">
                                <div class="flex justify-between items-start gap-2">
                                    <div class="flex items-center gap-2 min-w-0">
                                        <div
                                            class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0 {{ $order->type === 'delivery' ? 'bg-blue-50' : 'bg-orange-50' }}">
                                            @if ($order->type === 'delivery')
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-blue-500"
                                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
                                                </svg>
                                            @else
                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                    class="w-5 h-5 text-orange-500" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                                </svg>
                                            @endif
                                        </div>
                                        <div class="min-w-0">
                                            <p class="font-bold text-gray-800">{{ $order->order_number }}</p>
                                            <p class="text-xs text-gray-400 truncate">
                                                {{ $order->type === 'delivery' ? 'Domicilio — ' . $order->customer_name : 'Mesa — ' . ($order->table->name ?? '') }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="flex flex-col items-end gap-1 flex-shrink-0">
                                        @php
                                            $statusColors = [
                                                'pending' => 'bg-yellow-50 text-yellow-600',
                                                'confirmed' => 'bg-blue-50 text-blue-600',
                                                'preparing' => 'bg-purple-50 text-purple-600',
                                                'ready' => 'bg-green-50 text-green-600',
                                                'dispatched' => 'bg-orange-50 text-orange-600',
                                                'collecting' => 'bg-red-50 text-red-600',
                                            ];
                                            $statusLabels = [
                                                'pending' => 'Pendiente',
                                                'confirmed' => 'Confirmado',
                                                'preparing' => 'Preparando',
                                                'ready' => 'Listo',
                                                'dispatched' => 'Por recaudar',
                                                'collecting' => 'Pend. recaudo',
                                            ];
                                        @endphp
                                        <span
                                            class="{{ $statusColors[$order->status] ?? 'bg-gray-50 text-gray-600' }} px-2.5 py-1 rounded-lg text-xs font-semibold">
                                            {{ $statusLabels[$order->status] ?? $order->status }}
                                        </span>
                                        <span
                                            class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($order->created_at)->format('h:i a') }}</span>
                                    </div>
                                </div>

                                <div class="mt-2 flex items-center gap-2">
                                    @if ($order->payment_method === 'transfer')
                                        @if ($order->payment_status === 'pending')
                                            <span
                                                class="bg-yellow-50 text-yellow-600 px-2 py-0.5 rounded-lg text-xs font-medium">⚠
                                                Verificar pago</span>
                                        @else
                                            <span
                                                class="bg-green-50 text-green-600 px-2 py-0.5 rounded-lg text-xs font-medium">✓
                                                Transferencia verificada</span>
                                        @endif
                                    @else
                                        <span
                                            class="bg-green-50 text-green-600 px-2 py-0.5 rounded-lg text-xs font-medium">Efectivo</span>
                                    @endif
                                    @php $deliveryCost = $order->total - $order->subtotal; @endphp
                                    <span
                                        class="text-sm font-bold text-gray-800 ml-auto">${{ number_format($order->total, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-12 text-center">
                            <div class="flex flex-col items-center gap-3 text-gray-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 text-gray-300"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                                <p class="text-sm">No hay pedidos pendientes.</p>
                            </div>
                        </div>
                    @endforelse
                </div>

                <!-- Completados -->
                @if ($openShift && count($completedOrders) > 0)
                    <div class="mt-6">
                        <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-3">Completados este
                            turno</h3>
                        <div class="space-y-2">
                            @foreach ($completedOrders as $order)
                                @php
                                    $orderItems = \Illuminate\Support\Facades\DB::table('order_items')
                                        ->join('products', 'order_items.product_id', '=', 'products.id')
                                        ->join('categories', 'products.category_id', '=', 'categories.id')
                                        ->select(
                                            'order_items.*',
                                            'products.name as product_name',
                                            'categories.name as category_name',
                                        )
                                        ->where('order_items.order_id', $order->id)
                                        ->orderBy('categories.name')
                                        ->get();
                                    $deliveryUser = $order->delivery_id
                                        ? \Illuminate\Support\Facades\DB::table('tenant_users')
                                            ->where('id', $order->delivery_id)
                                            ->first()
                                        : null;
                                    $waiterUser = $order->waiter_id
                                        ? \Illuminate\Support\Facades\DB::table('tenant_users')
                                            ->where('id', $order->waiter_id)
                                            ->first()
                                        : null;
                                    $tableInfo = $order->table_id
                                        ? \Illuminate\Support\Facades\DB::table('tables')
                                            ->where('id', $order->table_id)
                                            ->first()
                                        : null;
                                    $itemsJson = $orderItems
                                        ->map(
                                            fn($i) => [
                                                'qty' => $i->quantity,
                                                'name' => $i->product_name,
                                                'category' => $i->category_name,
                                                'subtotal' => $i->subtotal,
                                            ],
                                        )
                                        ->toJson();
                                @endphp
                                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm px-4 py-3 flex justify-between items-center cursor-pointer hover:bg-gray-50 transition"
                                    onclick="openDetailModal('{{ $order->order_number }}','{{ $order->type }}','{{ $order->customer_name ?? '' }}','{{ $order->customer_phone ?? '' }}','{{ $order->delivery_address ?? '' }}','{{ $order->payment_method }}','{{ $order->payment_status }}',{{ $order->total }},'{{ $deliveryUser->name ?? '' }}','{{ $waiterUser->name ?? '' }}','{{ $tableInfo->name ?? '' }}','{{ $order->notes ?? '' }}',{{ $itemsJson }},'{{ \Carbon\Carbon::parse($order->updated_at)->format('h:i a') }}')">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-8 h-8 rounded-xl flex items-center justify-center {{ $order->type === 'delivery' ? 'bg-blue-50' : 'bg-orange-50' }}">
                                            @if ($order->type === 'delivery')
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-blue-400"
                                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
                                                </svg>
                                            @else
                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                    class="w-4 h-4 text-orange-400" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                                </svg>
                                            @endif
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-700">{{ $order->order_number }}
                                            </p>
                                            <p class="text-xs text-gray-400">
                                                {{ $order->type === 'delivery' ? $order->customer_name : 'Mesa' }} —
                                                {{ $order->payment_method === 'transfer' ? 'Transferencia' : 'Efectivo' }}
                                                —
                                                {{ \Carbon\Carbon::parse($order->updated_at)->format('h:i a') }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <span
                                            class="text-sm font-semibold text-gray-800">${{ number_format($order->total, 0, ',', '.') }}</span>
                                        <span
                                            class="bg-green-50 text-green-600 px-2 py-0.5 rounded-lg text-xs font-medium">Entregado</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        @if ($completedTotalPages > 1)
                            <div class="flex justify-center items-center gap-2 mt-4">
                                @if ($completedPage > 1)
                                    <a href="?completed_page={{ $completedPage - 1 }}"
                                        class="w-8 h-8 bg-white border border-gray-200 rounded-xl flex items-center justify-center text-gray-600 hover:bg-gray-50 transition text-sm">‹</a>
                                @endif
                                @for ($i = 1; $i <= $completedTotalPages; $i++)
                                    <a href="?completed_page={{ $i }}"
                                        class="w-8 h-8 rounded-xl flex items-center justify-center text-xs font-medium transition {{ $i == $completedPage ? 'bg-orange-500 text-white' : 'bg-white border border-gray-200 text-gray-600 hover:bg-gray-50' }}">{{ $i }}</a>
                                @endfor
                                @if ($completedPage < $completedTotalPages)
                                    <a href="?completed_page={{ $completedPage + 1 }}"
                                        class="w-8 h-8 bg-white border border-gray-200 rounded-xl flex items-center justify-center text-gray-600 hover:bg-gray-50 transition text-sm">›</a>
                                @endif
                                <span class="text-xs text-gray-400 ml-2">{{ $completedTotal }} total</span>
                            </div>
                        @endif
                    </div>
                @endif
                @if ($openShift && $cancelledOrders->count() > 0)
                    <div class="mt-6">
                        <h3 class="text-sm font-semibold text-red-400 uppercase tracking-wide mb-3">Cancelados este
                            turno</h3>
                        <div class="space-y-2">
                            @foreach ($cancelledOrders as $order)
                                <div class="bg-white rounded-2xl border border-red-100 shadow-sm px-4 py-3 flex justify-between items-center cursor-pointer hover:bg-gray-50 transition"
                                    onclick="selectOrder({{ $order->id }})">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-8 h-8 rounded-xl flex items-center justify-center {{ $order->type === 'delivery' ? 'bg-blue-50' : 'bg-orange-50' }}">
                                            @if ($order->type === 'delivery')
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-blue-400"
                                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
                                                </svg>
                                            @else
                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                    class="w-4 h-4 text-orange-400" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                                </svg>
                                            @endif
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-500 line-through">
                                                {{ $order->order_number }}</p>
                                            <p class="text-xs text-gray-400">
                                                {{ $order->type === 'delivery' ? $order->customer_name : 'Mesa' }} —
                                                {{ \Carbon\Carbon::parse($order->updated_at)->format('h:i a') }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <span
                                            class="text-sm font-medium text-gray-400 line-through">${{ number_format($order->total, 0, ',', '.') }}</span>
                                        <span
                                            class="bg-red-50 text-red-500 px-2 py-0.5 rounded-lg text-xs font-medium">Cancelado</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>


        </div>
    </div>

    <!-- Modales -->
    <div id="modal-detail" class="fixed inset-0 z-60 hidden">
        <div class="absolute inset-0 bg-black bg-opacity-50" onclick="closeDetailModal()"></div>
        <div
            class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 bg-white rounded-2xl p-6 w-full max-w-md shadow-xl max-h-[85vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-4">
                <h3 class="font-semibold text-gray-800 text-lg" id="detail-order-number"></h3>
                <button onclick="closeDetailModal()"
                    class="text-gray-400 hover:text-gray-600 w-8 h-8 bg-gray-100 rounded-xl flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div id="detail-content"></div>
        </div>
    </div>

    <div id="modal-close" class="fixed inset-0 hidden" style="z-index: 9999;">
        <div class="absolute inset-0 bg-black bg-opacity-50" onclick="closeModal()"></div>
        <div
            class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 bg-white rounded-2xl p-6 w-full max-w-sm shadow-xl">
            <h3 class="font-semibold text-gray-800 text-lg mb-1">Cerrar mesa</h3>
            <p class="text-sm text-gray-400 mb-4">Pedido: <span id="modal-order-number"
                    class="font-medium text-gray-700"></span></p>
            <div class="bg-gray-50 rounded-xl p-4 mb-4">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Total a cobrar</span>
                    <span class="font-semibold text-gray-800" id="modal-total"></span>
                </div>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Monto recibido</label>
                <div class="relative">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm">$</span>
                    <input type="number" id="modal-received" oninput="calcChange()"
                        class="w-full border border-gray-200 rounded-xl pl-8 pr-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400"
                        placeholder="0">
                </div>
            </div>
            <div class="bg-orange-50 rounded-xl p-4 mb-6 flex justify-between items-center">
                <span class="text-sm font-medium text-orange-700">Cambio a devolver</span>
                <span class="text-xl font-bold text-orange-500" id="modal-change">$0</span>
            </div>
            <div class="flex gap-3">
                <button onclick="closeModal()"
                    class="flex-1 border border-gray-200 text-gray-600 py-3 rounded-xl text-sm font-medium">Cancelar</button>
                <button onclick="confirmClose()"
                    class="flex-1 bg-orange-500 hover:bg-orange-600 text-white py-3 rounded-xl text-sm font-medium transition">Confirmar</button>
            </div>
        </div>
    </div>

    <div id="modal-collect" class="fixed inset-0 hidden" style="z-index: 9999;">
        <div class="absolute inset-0 bg-black bg-opacity-50" onclick="closeCollectModal()"></div>
        <div
            class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 bg-white rounded-2xl p-6 w-full max-w-sm shadow-xl">
            <h3 class="font-semibold text-gray-800 text-lg mb-1">Confirmar recaudo</h3>
            <p class="text-sm text-gray-400 mb-4">Pedido: <span id="collect-order-number"
                    class="font-medium text-gray-700"></span></p>
            <div class="bg-gray-50 rounded-xl p-4 mb-4">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Total a recaudar</span>
                    <span class="font-semibold text-gray-800" id="collect-total"></span>
                </div>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Monto recibido del domiciliario</label>
                <div class="relative">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm">$</span>
                    <input type="number" id="collect-received" oninput="calcCollectChange()"
                        class="w-full border border-gray-200 rounded-xl pl-8 pr-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400"
                        placeholder="0">
                </div>
            </div>
            <div class="bg-orange-50 rounded-xl p-4 mb-6 flex justify-between items-center">
                <span class="text-sm font-medium text-orange-700">Cambio a devolver</span>
                <span class="text-xl font-bold text-orange-500" id="collect-change">$0</span>
            </div>
            <div class="flex gap-3">
                <button onclick="closeCollectModal()"
                    class="flex-1 border border-gray-200 text-gray-600 py-3 rounded-xl text-sm font-medium">Cancelar</button>
                <button onclick="confirmCollect()"
                    class="flex-1 bg-green-500 hover:bg-green-600 text-white py-3 rounded-xl text-sm font-medium transition">Confirmar</button>
            </div>
        </div>
    </div>

    <!-- Modal detalle pedido activo -->
    <div id="modal-order-detail" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-black bg-opacity-50" onclick="closeOrderDetail()"></div>
        <div
            class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 bg-white rounded-2xl w-full max-w-lg shadow-xl max-h-[90vh] overflow-y-auto">

            <!-- Header modal -->
            <div
                class="px-6 py-4 border-b border-gray-100 flex justify-between items-center sticky top-0 bg-white rounded-t-2xl">
                <div>
                    <p class="font-bold text-gray-800 text-lg" id="modal-detail-number"></p>
                    <p class="text-sm text-gray-400" id="modal-detail-type"></p>
                </div>
                <button onclick="closeOrderDetail()"
                    class="w-9 h-9 bg-gray-100 hover:bg-gray-200 rounded-xl flex items-center justify-center text-gray-400 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="px-6 py-4 space-y-4">

                <!-- Estado y pago -->
                <div class="flex items-center gap-2 flex-wrap">
                    <span id="modal-detail-status" class="px-3 py-1 rounded-xl text-sm font-semibold"></span>
                    <span id="modal-detail-payment" class="px-2.5 py-1 rounded-lg text-xs font-medium"></span>
                    <span class="text-xs text-gray-400 ml-auto" id="modal-detail-time"></span>
                </div>

                <!-- Cliente -->
                <div id="modal-detail-client" class="bg-blue-50 rounded-xl p-4 hidden">
                    <p class="text-xs font-semibold text-blue-700 mb-2">Datos del cliente</p>
                    <div id="modal-detail-client-data" class="space-y-1"></div>
                </div>

                <!-- Items -->
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-2">Productos</p>
                    <div id="modal-detail-items" class="space-y-1.5 bg-gray-50 rounded-xl p-3"></div>
                </div>

                <!-- Notas -->
                <div id="modal-detail-notes-container" class="hidden">
                    <p class="text-xs text-gray-400 italic" id="modal-detail-notes"></p>
                </div>

                <!-- Totales -->
                <div class="bg-gray-50 rounded-xl p-4 space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Subtotal</span>
                        <span class="text-gray-700" id="modal-detail-subtotal"></span>
                    </div>
                    <div id="modal-detail-delivery-row" class="flex justify-between text-sm hidden">
                        <span class="text-gray-500">Domicilio</span>
                        <span class="text-gray-700" id="modal-detail-delivery"></span>
                    </div>
                    <div class="flex justify-between items-center border-t border-gray-200 pt-2">
                        <span class="font-bold text-gray-800">Total</span>
                        <span class="text-2xl font-bold text-gray-800" id="modal-detail-total"></span>
                    </div>
                </div>

                <!-- Acciones -->
                <div id="modal-detail-actions"></div>

            </div>
        </div>
    </div>

    <script>
        const csrfToken = '{{ csrf_token() }}';
        const tenantId = document.getElementById('tenant-id').dataset.id;
        let currentOrderId = null;
        let currentTotal = 0;
        let selectedOrderId = null;

        // Datos de pedidos para el panel derecho
        const ordersData = {
            @foreach ($orders as $order)
                {{ $order->id }}: {
                    id: {{ $order->id }},
                    number: '{{ $order->order_number }}',
                    type: '{{ $order->type }}',
                    status: '{{ $order->status }}',
                    paymentMethod: '{{ $order->payment_method }}',
                    paymentStatus: '{{ $order->payment_status }}',
                    customerName: '{{ addslashes($order->customer_name ?? '') }}',
                    customerPhone: '{{ $order->customer_phone ?? '' }}',
                    deliveryAddress: '{{ addslashes($order->delivery_address ?? '') }}',
                    notes: '{{ addslashes($order->notes ?? '') }}',
                    subtotal: {{ $order->subtotal }},
                    total: {{ $order->total }},
                    time: '{{ \Carbon\Carbon::parse($order->created_at)->format('h:i a') }}',
                    tableName: '{{ $order->table->name ?? '' }}',
                    items: [
                        @foreach ($order->items as $item)
                            {
                                qty: {{ $item->quantity }},
                                name: '{{ addslashes($item->product_name) }}',
                                category: '{{ addslashes($item->category_name) }}',
                                subtotal: {{ $item->subtotal }}
                            },
                        @endforeach
                    ],
                    deliveryUsers: [
                        @foreach ($deliveryUsers as $du)
                            {
                                id: {{ $du->id }},
                                name: '{{ addslashes($du->name) }}'
                            },
                        @endforeach
                    ],
                    openShift: {{ $openShift ? 'true' : 'false' }},
                },
            @endforeach

            @foreach ($cancelledOrders as $order)
                {{ $order->id }}: {
                    id: {{ $order->id }},
                    number: '{{ $order->order_number }}',
                    type: '{{ $order->type }}',
                    status: 'cancelled',
                    paymentMethod: '{{ $order->payment_method }}',
                    paymentStatus: '{{ $order->payment_status }}',
                    customerName: '{{ addslashes($order->customer_name ?? '') }}',
                    customerPhone: '{{ $order->customer_phone ?? '' }}',
                    deliveryAddress: '{{ addslashes($order->delivery_address ?? '') }}',
                    notes: '{{ addslashes($order->notes ?? '') }}',
                    subtotal: {{ $order->subtotal }},
                    total: {{ $order->total }},
                    time: '{{ \Carbon\Carbon::parse($order->updated_at)->format('h:i a') }}',
                    tableName: '{{ $order->table->name ?? '' }}',
                    items: [
                        @foreach ($order->items as $item)
                            {
                                qty: {{ $item->quantity }},
                                name: '{{ addslashes($item->product_name) }}',
                                category: '{{ addslashes($item->category_name) }}',
                                subtotal: {{ $item->subtotal }}
                            },
                        @endforeach
                    ],
                    deliveryUsers: [],
                    openShift: false,
                },
            @endforeach
        };

        const statusColors = {
            pending: 'bg-yellow-50 text-yellow-600',
            confirmed: 'bg-blue-50 text-blue-600',
            preparing: 'bg-purple-50 text-purple-600',
            ready: 'bg-green-50 text-green-600',
            dispatched: 'bg-orange-50 text-orange-600',
            collecting: 'bg-red-50 text-red-600',
        };

        const statusLabels = {
            pending: 'Pendiente',
            confirmed: 'Confirmado',
            preparing: 'Preparando',
            ready: 'Listo',
            dispatched: 'Por recaudar',
            collecting: 'Pend. recaudo',
        };

        function renderActions(order) {
            let html = '';
            if (!order.openShift) {
                html = `<div class="w-full bg-yellow-50 border border-yellow-200 rounded-xl px-3 py-2 text-xs text-yellow-700 flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
            Abre un turno para gestionar pedidos
        </div>`;
            } else {
                html = '<div class="flex flex-wrap gap-2">';

                if (order.paymentMethod === 'transfer' && order.paymentStatus === 'pending') {
                    html +=
                        `<button onclick="verifyPayment(${order.id})" class="flex-1 bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-2.5 rounded-xl text-sm font-medium transition">Verificar pago</button>`;
                }

                if (['pending', 'confirmed'].includes(order.status)) {
                    html +=
                        `<a href="/caja/pedidos/${order.id}/editar" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-600 px-3 py-2.5 rounded-xl text-sm font-medium transition text-center">Editar</a>`;
                    html +=
                        `<button onclick="cancelOrder(${order.id}, '${order.number}')" class="bg-red-50 hover:bg-red-100 text-red-500 px-3 py-2.5 rounded-xl text-sm font-medium transition">Cancelar</button>`;
                }

                html +=
                    `<button onclick="window.open('/caja/pedidos/${order.id}/comanda', '_blank', 'width=400,height=600')" class="bg-gray-100 hover:bg-gray-200 text-gray-600 px-3 py-2.5 rounded-xl text-sm font-medium transition">Imprimir</button>`;

                if (order.status === 'pending' && (order.type === 'table' || order.paymentMethod === 'cash' || order
                        .paymentStatus === 'verified')) {
                    html +=
                        `<button onclick="confirmOrder(${order.id})" class="flex-1 bg-blue-500 hover:bg-blue-600 text-white px-3 py-2.5 rounded-xl text-sm font-medium transition">Confirmar</button>`;
                }

                if (['confirmed', 'preparing'].includes(order.status)) {
                    html +=
                        `<button onclick="markReady(${order.id})" class="flex-1 bg-green-500 hover:bg-green-600 text-white px-3 py-2.5 rounded-xl text-sm font-medium transition">Marcar listo</button>`;
                }

                if (order.status === 'ready' && order.type === 'delivery') {
                    html += `<div class="w-full flex gap-2">
                <select id="delivery-select-${order.id}" class="flex-1 border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400 bg-white">
                    <option value="">Asignar domiciliario</option>
                    ${order.deliveryUsers.map(du => `<option value="${du.id}">${du.name}</option>`).join('')}
                </select>
                <button onclick="dispatchOrder(${order.id})" class="bg-orange-500 hover:bg-orange-600 text-white px-4 py-2.5 rounded-xl text-sm font-medium transition">Despachar</button>
            </div>`;
                }

                if (order.status === 'ready' && order.type === 'table') {
                    html +=
                        `<button onclick="openCloseModal(${order.id}, ${order.total}, '${order.number}')" class="flex-1 bg-orange-500 hover:bg-orange-600 text-white px-3 py-2.5 rounded-xl text-sm font-medium transition">Cerrar mesa</button>`;
                }

                if (['dispatched', 'collecting'].includes(order.status) && order.type === 'delivery') {
                    html +=
                        `<button onclick="openCollectModal(${order.id}, ${order.total}, '${order.number}')" class="flex-1 bg-green-500 hover:bg-green-600 text-white px-3 py-2.5 rounded-xl text-sm font-medium transition">Confirmar recaudo</button>`;
                    html +=
                        `<button onclick="cancelOrder(${order.id}, '${order.number}')" class="bg-red-50 hover:bg-red-100 text-red-500 px-3 py-2.5 rounded-xl text-sm font-medium transition">Cancelar</button>`;
                }

                html += '</div>';
            }

            document.getElementById('modal-detail-actions').innerHTML = html;
        }

        async function confirmOrder(id) {
            await fetch(`/caja/pedidos/${id}/confirmar`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json'
                }
            });
            location.reload();
        }

        async function markReady(id) {
            await fetch(`/caja/pedidos/${id}/listo`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json'
                }
            });
            location.reload();
        }

        async function dispatchOrder(id) {
            const deliveryId = document.getElementById(`delivery-select-${id}`).value;
            if (!deliveryId) {
                alert('Selecciona un domiciliario.');
                return;
            }
            await fetch(`/caja/pedidos/${id}/despachar`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    delivery_id: deliveryId
                })
            });
            location.reload();
        }

        async function verifyPayment(id) {
            await fetch(`/caja/pedidos/${id}/verificar-pago`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json'
                }
            });
            location.reload();
        }

        async function cancelOrder(id, orderNumber) {
            if (!confirm(`¿Cancelar el pedido ${orderNumber}? Esta acción no se puede deshacer.`)) return;
            await fetch(`/caja/pedidos/${id}/cancelar`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json'
                }
            });
            location.reload();
        }

        function openCloseModal(id, total, orderNumber) {
            currentOrderId = id;
            currentTotal = total;
            document.getElementById('modal-order-number').textContent = orderNumber;
            document.getElementById('modal-total').textContent = '$' + total.toLocaleString('es-CO');
            document.getElementById('modal-received').value = '';
            document.getElementById('modal-change').textContent = '$0';
            document.getElementById('modal-close').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('modal-close').classList.add('hidden');
        }

        function calcChange() {
            const received = parseFloat(document.getElementById('modal-received').value) || 0;
            const change = received - currentTotal;
            document.getElementById('modal-change').textContent = '$' + Math.max(0, change).toLocaleString('es-CO');
            document.getElementById('modal-change').style.color = change < 0 ? '#ef4444' : '#f97316';
        }

        async function confirmClose() {
            const received = parseFloat(document.getElementById('modal-received').value) || 0;
            if (received < currentTotal) {
                alert('El monto recibido es menor al total del pedido.');
                return;
            }
            closeModal();
            await fetch(`/caja/pedidos/${currentOrderId}/cerrar`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json'
                }
            });
            location.reload();
        }

        function openCollectModal(id, total, orderNumber) {
            currentOrderId = id;
            currentTotal = total;
            document.getElementById('collect-order-number').textContent = orderNumber;
            document.getElementById('collect-total').textContent = '$' + total.toLocaleString('es-CO');
            document.getElementById('collect-received').value = '';
            document.getElementById('collect-change').textContent = '$0';
            document.getElementById('modal-collect').classList.remove('hidden');
        }

        function closeCollectModal() {
            document.getElementById('modal-collect').classList.add('hidden');
        }

        function calcCollectChange() {
            const received = parseFloat(document.getElementById('collect-received').value) || 0;
            const change = received - currentTotal;
            document.getElementById('collect-change').textContent = '$' + Math.max(0, change).toLocaleString('es-CO');
            document.getElementById('collect-change').style.color = change < 0 ? '#ef4444' : '#f97316';
        }

        async function confirmCollect() {
            const received = parseFloat(document.getElementById('collect-received').value) || 0;
            if (received < currentTotal) {
                alert('El monto recibido es menor al total del pedido.');
                return;
            }
            closeCollectModal();
            await fetch(`/caja/pedidos/${currentOrderId}/recaudar`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json'
                }
            });
            location.reload();
        }

        function openDetailModal(orderNumber, type, customerName, customerPhone, deliveryAddress, paymentMethod,
            paymentStatus, total, deliveryUser, waiterUser, tableName, notes, items, time) {
            document.getElementById('detail-order-number').textContent = orderNumber;
            let html = '';
            if (type === 'delivery') {
                html +=
                    `<div class="bg-blue-50 rounded-xl p-4 mb-4"><p class="text-xs font-semibold text-blue-700 mb-2">Datos del cliente</p><p class="text-sm text-blue-700"><span class="font-medium">Nombre:</span> ${customerName}</p><p class="text-sm text-blue-700"><span class="font-medium">Tel:</span> <a href="tel:${customerPhone}" class="underline">${customerPhone}</a></p><p class="text-sm text-blue-700"><span class="font-medium">Dirección:</span> ${deliveryAddress}</p></div>`;
                if (deliveryUser) html +=
                    `<div class="bg-orange-50 rounded-xl p-4 mb-4"><p class="text-xs font-semibold text-orange-700 mb-1">Domiciliario</p><p class="text-sm text-orange-700">${deliveryUser}</p></div>`;
            } else {
                html +=
                    `<div class="bg-orange-50 rounded-xl p-4 mb-4"><p class="text-xs font-semibold text-orange-700 mb-1">Mesa</p><p class="text-sm text-orange-700">${tableName}</p>${waiterUser ? `<p class="text-sm text-orange-700 mt-1"><span class="font-medium">Mesero:</span> ${waiterUser}</p>` : ''}</div>`;
            }
            html +=
                `<div class="bg-gray-50 rounded-xl p-4 mb-4"><p class="text-xs font-semibold text-gray-500 mb-1">Pago</p><p class="text-sm text-gray-700">${paymentMethod === 'transfer' ? 'Transferencia' : 'Efectivo'} — ${time}</p></div>`;
            html +=
                `<div class="mb-4"><p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Productos</p><div class="space-y-1">`;
            let currentCat = '';
            items.forEach(item => {
                if (item.category !== currentCat) {
                    currentCat = item.category;
                    html += `<p class="text-xs font-semibold text-gray-400 uppercase mt-2">${item.category}</p>`;
                }
                html +=
                    `<div class="flex justify-between text-sm"><span class="text-gray-700">${item.qty}x ${item.name}</span><span class="text-gray-500">$${Number(item.subtotal).toLocaleString('es-CO')}</span></div>`;
            });
            html += `</div></div>`;
            if (notes) html += `<p class="text-xs text-gray-400 italic mb-4">Nota: ${notes}</p>`;
            const deliveryCost = total - items.reduce((s, i) => s + i.subtotal, 0);
            html += `<div class="border-t border-gray-100 pt-4 space-y-1">`;
            if (deliveryCost > 0) html +=
                `<div class="flex justify-between text-sm"><span class="text-gray-500">Domicilio</span><span class="text-gray-700">$${Number(deliveryCost).toLocaleString('es-CO')}</span></div>`;
            html +=
                `<div class="flex justify-between items-center"><span class="font-semibold text-gray-700">Total</span><span class="text-xl font-bold text-gray-800">$${Number(total).toLocaleString('es-CO')}</span></div></div>`;
            document.getElementById('detail-content').innerHTML = html;
            document.getElementById('modal-detail').classList.remove('hidden');
        }

        function closeDetailModal() {
            document.getElementById('modal-detail').classList.add('hidden');
        }

        document.addEventListener('DOMContentLoaded', function() {
            if (window.Echo) {
                document.getElementById('connection-status').textContent = 'En línea';
                window.Echo.channel(`orders.${tenantId}`)
                    .listen('.new.order', () => {
                        location.reload();
                    })
                    .listen('.order.status', () => {
                        location.reload();
                    });
            }
        });


        function selectOrder(id) {
            const order = ordersData[id];
            if (!order) return;

            document.getElementById('modal-detail-number').textContent = order.number;
            document.getElementById('modal-detail-type').textContent = order.type === 'delivery' ? 'Domicilio — ' + order
                .customerName : 'Mesa — ' + order.tableName;
            document.getElementById('modal-detail-time').textContent = order.time;

            const statusEl = document.getElementById('modal-detail-status');
            statusEl.textContent = statusLabels[order.status] || order.status;
            statusEl.className = 'px-3 py-1 rounded-xl text-sm font-semibold ' + (statusColors[order.status] ||
                'bg-gray-50 text-gray-600');

            const paymentEl = document.getElementById('modal-detail-payment');
            if (order.paymentMethod === 'transfer') {
                paymentEl.textContent = order.paymentStatus === 'pending' ? '⚠ Verificar pago' : '✓ Transferencia';
                paymentEl.className = 'px-2.5 py-1 rounded-lg text-xs font-medium ' + (order.paymentStatus === 'pending' ?
                    'bg-yellow-50 text-yellow-600' : 'bg-green-50 text-green-600');
            } else {
                paymentEl.textContent = 'Efectivo';
                paymentEl.className = 'px-2.5 py-1 rounded-lg text-xs font-medium bg-green-50 text-green-600';
            }

            // Cliente
            if (order.type === 'delivery') {
                document.getElementById('modal-detail-client').classList.remove('hidden');
                document.getElementById('modal-detail-client-data').innerHTML = `
            <p class="text-sm text-blue-700"><span class="font-medium">Nombre:</span> ${order.customerName}</p>
            <p class="text-sm text-blue-700"><span class="font-medium">Tel:</span> <a href="tel:${order.customerPhone}" class="underline">${order.customerPhone}</a></p>
            <p class="text-sm text-blue-700"><span class="font-medium">Dir:</span> ${order.deliveryAddress}</p>
        `;
            } else {
                document.getElementById('modal-detail-client').classList.add('hidden');
            }

            // Items
            let currentCat = '';
            let itemsHtml = '';
            order.items.forEach(item => {
                if (item.category !== currentCat) {
                    currentCat = item.category;
                    itemsHtml +=
                        `<p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mt-2 first:mt-0">${item.category}</p>`;
                }
                itemsHtml += `<div class="flex justify-between items-center text-sm">
            <span class="text-gray-700">${item.qty}x ${item.name}</span>
            <span class="text-gray-500">$${Number(item.subtotal).toLocaleString('es-CO')}</span>
        </div>`;
            });
            document.getElementById('modal-detail-items').innerHTML = itemsHtml;

            // Notas
            if (order.notes) {
                document.getElementById('modal-detail-notes-container').classList.remove('hidden');
                document.getElementById('modal-detail-notes').textContent = 'Nota: ' + order.notes;
            } else {
                document.getElementById('modal-detail-notes-container').classList.add('hidden');
            }

            // Totales
            const deliveryCost = order.total - order.subtotal;
            document.getElementById('modal-detail-subtotal').textContent = '$' + Number(order.subtotal).toLocaleString(
                'es-CO');
            document.getElementById('modal-detail-total').textContent = '$' + Number(order.total).toLocaleString('es-CO');
            if (deliveryCost > 0) {
                document.getElementById('modal-detail-delivery-row').classList.remove('hidden');
                document.getElementById('modal-detail-delivery').textContent = '$' + Number(deliveryCost).toLocaleString(
                    'es-CO');
            } else {
                document.getElementById('modal-detail-delivery-row').classList.add('hidden');
            }

            // Acciones
            renderActions(order);
            document.getElementById('modal-order-detail').classList.remove('hidden');
        }

        function closeOrderDetail() {
            document.getElementById('modal-order-detail').classList.add('hidden');
        }
    </script>

</body>

</html>
