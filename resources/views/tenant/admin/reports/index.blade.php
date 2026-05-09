<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reportes</title>
    @laravelPWA
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @include('tenant.partials.theme')
    <style>
        * {
            -webkit-tap-highlight-color: transparent;
        }
    </style>
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
                    <p class="font-bold text-gray-800 text-sm">Reportes</p>
                    <p class="text-xs text-gray-400">
                        @if ($period === 'custom')
                            {{ \Carbon\Carbon::parse($dateFrom)->format('d/m/Y') }} —
                            {{ \Carbon\Carbon::parse($dateTo)->format('d/m/Y') }}
                        @elseif($period === 'today')
                            Hoy
                        @elseif($period === 'week')
                            Esta semana
                        @else
                            Este mes
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-4xl mx-auto px-4 py-4 space-y-4">

        <!-- Filtros rápidos -->
        <div class="flex gap-2 overflow-x-auto pb-1">
            <a href="{{ route('tenant.admin.reports', ['period' => 'today']) }}"
                class="px-4 py-2 rounded-xl text-xs font-medium whitespace-nowrap transition {{ $period === 'today' ? 'bg-orange-500 text-white' : 'bg-white border border-gray-200 text-gray-600 hover:bg-gray-50' }}">
                Hoy
            </a>
            <a href="{{ route('tenant.admin.reports', ['period' => 'week']) }}"
                class="px-4 py-2 rounded-xl text-xs font-medium whitespace-nowrap transition {{ $period === 'week' ? 'bg-orange-500 text-white' : 'bg-white border border-gray-200 text-gray-600 hover:bg-gray-50' }}">
                Semana
            </a>
            <a href="{{ route('tenant.admin.reports', ['period' => 'month']) }}"
                class="px-4 py-2 rounded-xl text-xs font-medium whitespace-nowrap transition {{ $period === 'month' ? 'bg-orange-500 text-white' : 'bg-white border border-gray-200 text-gray-600 hover:bg-gray-50' }}">
                Mes
            </a>
            <button onclick="toggleCalendar()"
                class="px-4 py-2 rounded-xl text-xs font-medium whitespace-nowrap transition flex items-center gap-1.5 {{ $period === 'custom' ? 'bg-orange-500 text-white' : 'bg-white border border-gray-200 text-gray-600 hover:bg-gray-50' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                Rango
            </button>
        </div>

        <!-- Filtro por calendario -->
        <div id="calendar-filter"
            class="{{ $period === 'custom' ? '' : 'hidden' }} bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
            <form method="GET" action="{{ route('tenant.admin.reports') }}"
                class="flex flex-col sm:flex-row gap-3 items-end">
                <div class="flex-1">
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Desde</label>
                    <input type="date" name="date_from"
                        value="{{ $dateFrom ?? now()->startOfMonth()->format('Y-m-d') }}"
                        class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400">
                </div>
                <div class="flex-1">
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Hasta</label>
                    <input type="date" name="date_to" value="{{ $dateTo ?? now()->format('Y-m-d') }}"
                        class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400">
                </div>
                <button type="submit"
                    class="bg-orange-500 hover:bg-orange-600 text-white px-5 py-2.5 rounded-xl text-sm font-medium transition whitespace-nowrap">
                    Filtrar
                </button>
            </form>
        </div>

        <!-- Métricas principales -->
        <div class="grid grid-cols-2 gap-3">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 col-span-2">
                <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Ventas totales</p>
                <p class="text-3xl font-bold text-gray-800">${{ number_format($totalSales, 0, ',', '.') }}</p>
                <p class="text-xs text-gray-400 mt-1">{{ $totalOrders }} pedido(s) completado(s)</p>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
                <p class="text-xs text-gray-400 mb-1">Mesas</p>
                <p class="text-2xl font-bold text-orange-500">{{ $tableOrders }}</p>
                <p class="text-xs text-gray-400 mt-0.5">pedidos</p>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
                <p class="text-xs text-gray-400 mb-1">Domicilios</p>
                <p class="text-2xl font-bold text-blue-500">{{ $deliveryOrders }}</p>
                <p class="text-xs text-gray-400 mt-0.5">pedidos</p>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
                <p class="text-xs text-gray-400 mb-1">Clientes registrados</p>
                <p class="text-xl font-bold text-purple-500">{{ $totalCustomers }}</p>
                <p class="text-xs text-gray-400 mt-0.5">en total</p>
            </div>
            <div class="bg-orange-50 rounded-2xl border border-orange-100 shadow-sm p-4">
                <p class="text-xs text-orange-400 mb-1">Comisión RestaFull</p>
                <p class="text-xl font-bold text-orange-500">${{ number_format($totalCommission, 0, ',', '.') }}</p>
                <p class="text-xs text-orange-400 mt-0.5">5% de ventas</p>
            </div>
        </div>

        <!-- Productos más vendidos -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-50">
                <h3 class="font-semibold text-gray-800 text-sm">Productos más vendidos</h3>
            </div>
            @forelse($topProducts as $product)
                <div class="px-4 py-3 border-b border-gray-50 last:border-0 flex items-center gap-3">
                    <div class="w-8 h-8 bg-orange-50 rounded-xl flex items-center justify-center flex-shrink-0">
                        <span class="text-orange-500 font-bold text-xs">{{ $loop->iteration }}</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-800 truncate">{{ $product->name }}</p>
                        <p class="text-xs text-gray-400">{{ $product->total_qty }} unidades</p>
                    </div>
                    <span
                        class="text-sm font-semibold text-gray-800">${{ number_format($product->total_revenue, 0, ',', '.') }}</span>
                </div>
            @empty
                <div class="px-4 py-8 text-center text-sm text-gray-400">Sin datos en este período.</div>
            @endforelse
        </div>

        <!-- Turnos de caja -->
        @if ($cashShifts->count() > 0)
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-4 py-3 border-b border-gray-50">
                    <h3 class="font-semibold text-gray-800 text-sm">Turnos de caja</h3>
                </div>
                @foreach ($cashShifts as $shift)
                    <div class="px-4 py-3 border-b border-gray-50 last:border-0">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-sm font-medium text-gray-800">{{ $shift->cashier->name ?? 'Cajero' }}
                                </p>
                                <p class="text-xs text-gray-400">
                                    {{ \Carbon\Carbon::parse($shift->opened_at)->format('d/m/Y h:i a') }}
                                    @if ($shift->closed_at)
                                        — {{ \Carbon\Carbon::parse($shift->closed_at)->format('h:i a') }}
                                    @else
                                        — <span class="text-green-500 font-medium">Activo</span>
                                    @endif
                                </p>
                            </div>
                            <div class="text-right">
                                @if ($shift->total_sales)
                                    <p class="text-sm font-semibold text-gray-800">
                                        ${{ number_format($shift->total_sales, 0, ',', '.') }}</p>
                                @endif
                                @if ($shift->difference !== null)
                                    <p
                                        class="text-xs {{ $shift->difference >= 0 ? 'text-green-500' : 'text-red-500' }} font-medium">
                                        Dif: ${{ number_format($shift->difference, 0, ',', '.') }}
                                    </p>
                                @endif
                            </div>
                        </div>
                        @if ($shift->status === 'closed')
                            <div class="mt-2 grid grid-cols-3 gap-2">
                                <div class="bg-gray-50 rounded-xl p-2 text-center">
                                    <p class="text-xs text-gray-400">Base</p>
                                    <p class="text-xs font-semibold text-gray-700">
                                        ${{ number_format($shift->opening_amount, 0, ',', '.') }}</p>
                                </div>
                                <div class="bg-green-50 rounded-xl p-2 text-center">
                                    <p class="text-xs text-green-400">Efectivo</p>
                                    <p class="text-xs font-semibold text-green-600">
                                        ${{ number_format($shift->total_cash, 0, ',', '.') }}</p>
                                </div>
                                <div class="bg-blue-50 rounded-xl p-2 text-center">
                                    <p class="text-xs text-blue-400">Transfer.</p>
                                    <p class="text-xs font-semibold text-blue-600">
                                        ${{ number_format($shift->total_transfer, 0, ',', '.') }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif

        <!-- Pedidos cancelados -->
        <!-- Pedidos cancelados -->
        @if ($cancelledOrders->count() > 0)
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-4 py-3 border-b border-gray-50 flex justify-between items-center">
                    <h3 class="font-semibold text-gray-800 text-sm">Pedidos cancelados</h3>
                    <span
                        class="bg-red-50 text-red-500 text-xs px-2.5 py-1 rounded-lg font-medium">{{ $cancelledOrders->count() }}</span>
                </div>
                @foreach ($cancelledOrders as $order)
                    @php
                        $cancelledItems = \Illuminate\Support\Facades\DB::table('order_items')
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
                        $cancelledItemsJson = $cancelledItems
                            ->map(
                                fn($i) => [
                                    'qty' => $i->quantity,
                                    'name' => $i->product_name,
                                    'category' => $i->category_name,
                                    'subtotal' => $i->subtotal,
                                ],
                            )
                            ->toJson();
                        $cancelledTable = $order->table_id
                            ? \Illuminate\Support\Facades\DB::table('tables')->where('id', $order->table_id)->first()
                            : null;
                    @endphp
                    <div class="px-4 py-3 border-b border-gray-50 last:border-0 flex justify-between items-center cursor-pointer hover:bg-gray-50 transition"
                        onclick="openDetailModal('{{ $order->order_number }}','{{ $order->type }}','{{ $order->customer_name ?? '' }}','{{ $order->customer_phone ?? '' }}','{{ $order->delivery_address ?? '' }}','{{ $order->payment_method }}','{{ $order->payment_status }}',{{ $order->total }},'','','{{ $cancelledTable->name ?? '' }}','{{ $order->notes ?? '' }}',{{ $cancelledItemsJson }},'{{ \Carbon\Carbon::parse($order->updated_at)->format('h:i a') }}')">
                        <div>
                            <p class="text-sm font-medium text-gray-800">{{ $order->order_number }}</p>
                            <p class="text-xs text-gray-400">
                                {{ $order->type === 'delivery' ? $order->customer_name ?? 'Cliente' : 'Mesa' }} —
                                {{ \Carbon\Carbon::parse($order->updated_at)->format('d/m/Y h:i a') }}
                            </p>
                        </div>
                        <div class="text-right">
                            <span
                                class="text-sm font-medium text-gray-400 line-through">${{ number_format($order->total, 0, ',', '.') }}</span>
                            <p class="text-xs text-red-400 font-medium">Cancelado</p>
                        </div>
                    </div>
                @endforeach
                <div class="px-4 py-3 bg-red-50 flex justify-between items-center">
                    <span class="text-sm font-semibold text-red-600">Total cancelado</span>
                    <span
                        class="font-semibold text-red-600">${{ number_format($cancelledOrders->sum('total'), 0, ',', '.') }}</span>
                </div>
            </div>
        @endif

        <!-- Pedidos recientes -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-50">
                <h3 class="font-semibold text-gray-800 text-sm">Pedidos completados</h3>
            </div>
            @forelse($recentOrders as $order)
                <div class="px-4 py-3 border-b border-gray-50 last:border-0 flex justify-between items-center">
                    <div>
                        <p class="text-sm font-medium text-gray-800">{{ $order->order_number }}</p>
                        <p class="text-xs text-gray-400">
                            {{ $order->type === 'delivery' ? $order->customer_name ?? 'Cliente' : 'Mesa' }} —
                            {{ \Carbon\Carbon::parse($order->updated_at)->format('d/m/Y h:i a') }}
                        </p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-semibold text-gray-800">
                            ${{ number_format($order->total, 0, ',', '.') }}</p>
                        <span
                            class="{{ $order->type === 'delivery' ? 'text-blue-500' : 'text-orange-500' }} text-xs font-medium">
                            {{ $order->type === 'delivery' ? 'Domicilio' : 'Mesa' }}
                        </span>
                    </div>
                </div>
            @empty
                <div class="px-4 py-8 text-center text-sm text-gray-400">Sin pedidos en este período.</div>
            @endforelse
        </div>

    </div>

    <script>
        function toggleCalendar() {
            document.getElementById('calendar-filter').classList.toggle('hidden');
        }
    </script>

</body>

</html>
