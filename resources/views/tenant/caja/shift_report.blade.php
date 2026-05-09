<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Turno</title>
    @laravelPWA
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @media print {
            .no-print {
                display: none !important;
            }

            body {
                background: white;
            }

            @page {
                margin: 10mm;
            }
        }
    </style>
    @include('tenant.partials.theme')
</head>

<body class="bg-gray-50 min-h-screen">

<nav class="bg-white border-b border-gray-100 px-4 py-3 flex justify-between items-center sticky top-0 z-10 shadow-sm no-print">
    <div class="flex items-center gap-3">
        <a href="{{ route('tenant.caja.shift') }}" class="w-9 h-9 bg-gray-100 hover:bg-gray-200 rounded-xl flex items-center justify-center transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
        </a>
        <div>
            <p class="font-bold text-gray-800 text-sm">Reporte de turno</p>
            <p class="text-xs text-gray-400">{{ $cashier->name }}</p>
        </div>
    </div>
    <button onclick="window.print()"
        class="bg-orange-500 hover:bg-orange-600 text-white px-3 py-2 rounded-xl text-xs font-medium transition flex items-center gap-1.5">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
        Imprimir
    </button>
</nav>

    <div class="max-w-2xl mx-auto px-4 py-6">

        <!-- Encabezado -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-4 text-center">
            @if (!empty($settings['logo']))
                <img src="{{ Storage::url($settings['logo']) }}" class="w-16 h-16 rounded-xl object-cover mx-auto mb-3">
            @else
                <div
                    class="bg-orange-500 text-white w-16 h-16 rounded-2xl flex items-center justify-center font-bold text-xl mx-auto mb-3">
                    RF</div>
            @endif
            <h2 class="text-xl font-semibold text-gray-800">{{ $settings['restaurant_name'] ?? $tenant->name }}</h2>
            <p class="text-sm text-gray-400 mt-1">Reporte de turno</p>
            <p class="text-xs text-gray-400 mt-0.5">Cajero: {{ $cashier->name }}</p>
        </div>

        <!-- Resumen -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-4">
            <h3 class="font-semibold text-gray-800 mb-4">Resumen del turno</h3>
            <div class="space-y-3">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Apertura</span>
                    <span
                        class="text-gray-800">{{ \Carbon\Carbon::parse($shift->opened_at)->format('d/m/Y h:i a') }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Cierre</span>
                    <span
                        class="text-gray-800">{{ \Carbon\Carbon::parse($shift->closed_at)->format('d/m/Y h:i a') }}</span>
                </div>
                <div class="border-t border-gray-100 pt-3 flex justify-between text-sm">
                    <span class="text-gray-500">Base inicial</span>
                    <span
                        class="font-medium text-gray-800">${{ number_format($shift->opening_amount, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Ventas en efectivo</span>
                    <span
                        class="font-medium text-green-600">${{ number_format($shift->total_cash, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Ventas por transferencia</span>
                    <span
                        class="font-medium text-blue-600">${{ number_format($shift->total_transfer, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Total ventas</span>
                    <span
                        class="font-semibold text-gray-800">${{ number_format($shift->total_sales, 0, ',', '.') }}</span>
                </div>
                <div class="border-t border-gray-100 pt-3 flex justify-between text-sm">
                    <span class="text-gray-500">Efectivo esperado en caja</span>
                    <span
                        class="font-medium text-gray-800">${{ number_format($shift->opening_amount + $shift->total_cash, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Efectivo contado al cierre</span>
                    <span
                        class="font-medium text-gray-800">${{ number_format($shift->closing_amount, 0, ',', '.') }}</span>
                </div>
                <div
                    class="flex justify-between text-sm font-semibold pt-2 border-t border-gray-100 {{ $shift->difference >= 0 ? 'text-green-600' : 'text-red-500' }}">
                    <span>Diferencia</span>
                    <span>${{ number_format($shift->difference, 0, ',', '.') }}</span>
                </div>
            </div>

            @if ($shift->notes)
                <div class="mt-4 pt-4 border-t border-gray-100">
                    <p class="text-xs text-gray-500"><span class="font-medium">Notas:</span> {{ $shift->notes }}</p>
                </div>
            @endif
        </div>

        <!-- Métricas rápidas -->
        <div class="grid grid-cols-3 gap-3 mb-4">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 text-center">
                <p class="text-xs text-gray-400 mb-1">Pedidos</p>
                <p class="text-xl font-semibold text-gray-800">{{ $orders->count() }}</p>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 text-center">
                <p class="text-xs text-gray-400 mb-1">Efectivo</p>
                <p class="text-lg font-semibold text-green-500">${{ number_format($shift->total_cash, 0, ',', '.') }}
                </p>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 text-center">
                <p class="text-xs text-gray-400 mb-1">Transfer.</p>
                <p class="text-lg font-semibold text-blue-500">
                    ${{ number_format($shift->total_transfer, 0, ',', '.') }}</p>
            </div>
        </div>

        <!-- Detalle pedidos -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-6">
            <div class="px-4 py-3 border-b border-gray-50 flex justify-between items-center">
                <h3 class="font-semibold text-gray-800 text-sm">Detalle de pedidos</h3>
                <span class="text-xs text-gray-400">{{ $orders->count() }} pedido(s)</span>
            </div>
            @forelse($orders as $order)
                @php $deliveryCost = $order->total - $order->subtotal; @endphp
                <div class="px-4 py-3 border-b border-gray-50 last:border-0">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-sm font-medium text-gray-800">{{ $order->order_number }}</p>
                            <p class="text-xs text-gray-400">
                                {{ $order->type === 'delivery' ? $order->customer_name : 'Mesa' }} —
                                {{ $order->payment_method === 'transfer' ? 'Transferencia' : 'Efectivo' }} —
                                {{ \Carbon\Carbon::parse($order->updated_at)->format('h:i a') }}
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-semibold text-gray-800">
                                ${{ number_format($order->total, 0, ',', '.') }}</p>
                            @if ($deliveryCost > 0)
                                <p class="text-xs text-gray-400">Dom: ${{ number_format($deliveryCost, 0, ',', '.') }}
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="px-4 py-6 text-center text-sm text-gray-400">Sin pedidos en este turno.</div>
            @endforelse

            @if ($orders->count() > 0)
                <div class="px-4 py-3 bg-gray-50 flex justify-between items-center">
                    <span class="text-sm font-semibold text-gray-700">Total</span>
                    <span
                        class="font-semibold text-gray-800">${{ number_format($orders->sum('total'), 0, ',', '.') }}</span>
                </div>
            @endif
        </div>

        @if ($cancelledOrders->count() > 0)
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-4">
                <div class="px-4 py-3 border-b border-gray-50 flex justify-between items-center">
                    <h3 class="font-semibold text-red-500 text-sm">Pedidos cancelados</h3>
                    <span class="text-xs text-gray-400">{{ $cancelledOrders->count() }} pedido(s)</span>
                </div>
                @foreach ($cancelledOrders as $order)
                    <div class="px-4 py-3 border-b border-gray-50 last:border-0 flex justify-between items-center">
                        <div>
                            <p class="text-sm font-medium text-gray-800">{{ $order->order_number }}</p>
                            <p class="text-xs text-gray-400">
                                {{ $order->type === 'delivery' ? $order->customer_name : 'Mesa' }} —
                                {{ \Carbon\Carbon::parse($order->updated_at)->format('h:i a') }}
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

        <a href="{{ route('tenant.caja') }}"
            class="no-print block w-full text-center bg-orange-500 hover:bg-orange-600 text-white py-3 rounded-xl font-medium text-sm transition">
            Volver a caja
        </a>

    </div>

</body>

</html>
