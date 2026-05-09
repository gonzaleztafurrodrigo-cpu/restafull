<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Arqueo de Caja</title>
    @laravelPWA
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @include('tenant.partials.theme')
    <style>
        * { -webkit-tap-highlight-color: transparent; }
        @media print {
            .no-print { display: none !important; }
            body { background: white; }
            @page { margin: 10mm; }
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">

    <nav class="bg-white border-b border-gray-100 px-4 py-3 flex justify-between items-center sticky top-0 z-10 shadow-sm no-print">
        <div class="flex items-center gap-3">
            <a href="{{ route('tenant.caja') }}" class="w-9 h-9 bg-gray-100 hover:bg-gray-200 rounded-xl flex items-center justify-center transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
            </a>
            <div>
                <p class="font-bold text-gray-800 text-sm">Arqueo de caja</p>
                <p class="text-xs text-gray-400">{{ now()->format('d/m/Y') }}</p>
            </div>
        </div>
        <button onclick="window.print()"
            class="bg-gray-800 hover:bg-gray-900 text-white px-3 py-2 rounded-xl text-xs font-medium transition flex items-center gap-1.5">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
            Imprimir
        </button>
    </nav>

    <div class="max-w-2xl mx-auto px-4 py-6 space-y-4">

        <!-- Resumen principal -->
        <div class="grid grid-cols-2 gap-3">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
                <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Efectivo</p>
                <p class="text-2xl font-bold text-green-500">${{ number_format($totalCash, 0, ',', '.') }}</p>
                <p class="text-xs text-gray-400 mt-0.5">Mesas + domicilios</p>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
                <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Transferencias</p>
                <p class="text-2xl font-bold text-blue-500">${{ number_format($totalDeliveryTransfer, 0, ',', '.') }}</p>
                <p class="text-xs text-gray-400 mt-0.5">Domicilios verificados</p>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
                <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Pendiente</p>
                <p class="text-2xl font-bold text-orange-500">${{ number_format($totalPending, 0, ',', '.') }}</p>
                <p class="text-xs text-gray-400 mt-0.5">{{ $ordersPending->count() }} por recaudar</p>
            </div>
            <div class="bg-orange-50 rounded-2xl border border-orange-100 shadow-sm p-4">
                <p class="text-xs text-orange-400 uppercase tracking-wide mb-1">Total general</p>
                <p class="text-2xl font-bold text-orange-500">${{ number_format($totalGeneral, 0, ',', '.') }}</p>
                <p class="text-xs text-orange-400 mt-0.5">Todo el día</p>
            </div>
        </div>

        <!-- Mesas -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-5 py-3 border-b border-gray-50 flex justify-between items-center">
                <h3 class="font-semibold text-gray-800 text-sm">Mesas cerradas</h3>
                <span class="bg-orange-50 text-orange-500 text-xs px-2.5 py-1 rounded-lg font-medium">{{ $ordersTable->count() }} pedido(s)</span>
            </div>
            @forelse($ordersTable as $order)
                @php $deliveryCost = $order->total - $order->subtotal; @endphp
                <div class="px-5 py-3 border-b border-gray-50 last:border-0 flex justify-between items-center">
                    <div>
                        <p class="text-sm font-medium text-gray-800">{{ $order->order_number }}</p>
                        <p class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($order->delivered_at)->format('h:i a') }}</p>
                    </div>
                    <span class="font-semibold text-gray-800 text-sm">${{ number_format($order->total, 0, ',', '.') }}</span>
                </div>
            @empty
                <div class="px-5 py-8 text-center text-sm text-gray-400">Sin pedidos de mesa hoy.</div>
            @endforelse
            @if($ordersTable->count() > 0)
                <div class="px-5 py-3 bg-gray-50 flex justify-between items-center">
                    <span class="text-sm font-semibold text-gray-700">Subtotal mesas</span>
                    <span class="font-bold text-gray-800">${{ number_format($totalTable, 0, ',', '.') }}</span>
                </div>
            @endif
        </div>

        <!-- Domicilios -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-5 py-3 border-b border-gray-50 flex justify-between items-center">
                <h3 class="font-semibold text-gray-800 text-sm">Domicilios entregados</h3>
                <span class="bg-blue-50 text-blue-500 text-xs px-2.5 py-1 rounded-lg font-medium">{{ $ordersDelivery->count() }} pedido(s)</span>
            </div>
            @forelse($ordersDelivery as $order)
                @php $deliveryCost = $order->total - $order->subtotal; @endphp
                <div class="px-5 py-3 border-b border-gray-50 last:border-0">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-sm font-medium text-gray-800">{{ $order->order_number }} — {{ $order->customer_name }}</p>
                            <p class="text-xs text-gray-400">
                                {{ \Carbon\Carbon::parse($order->delivered_at)->format('h:i a') }} —
                                {{ $order->payment_method === 'transfer' ? 'Transferencia' : 'Efectivo' }}
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-semibold text-gray-800">${{ number_format($order->total, 0, ',', '.') }}</p>
                            @if($deliveryCost > 0)
                                <p class="text-xs text-gray-400">Dom: ${{ number_format($deliveryCost, 0, ',', '.') }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="px-5 py-8 text-center text-sm text-gray-400">Sin domicilios hoy.</div>
            @endforelse
            @if($ordersDelivery->count() > 0)
                <div class="px-5 py-3 bg-gray-50 space-y-1">
                    <div class="flex justify-between items-center">
                        <span class="text-xs text-gray-500">Efectivo</span>
                        <span class="text-sm font-medium text-gray-700">${{ number_format($totalDeliveryCash, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-xs text-gray-500">Transferencia</span>
                        <span class="text-sm font-medium text-gray-700">${{ number_format($totalDeliveryTransfer, 0, ',', '.') }}</span>
                    </div>
                </div>
            @endif
        </div>

        <!-- Pendientes -->
        @if($ordersPending->count() > 0)
            <div class="bg-orange-50 border border-orange-100 rounded-2xl overflow-hidden">
                <div class="px-5 py-3 border-b border-orange-100 flex justify-between items-center">
                    <h3 class="font-semibold text-orange-700 text-sm">Por recaudar</h3>
                    <span class="bg-orange-100 text-orange-600 text-xs px-2.5 py-1 rounded-lg font-medium">{{ $ordersPending->count() }}</span>
                </div>
                @foreach($ordersPending as $order)
                    @php $deliveryCost = $order->total - $order->subtotal; @endphp
                    <div class="px-5 py-3 border-b border-orange-100 last:border-0 flex justify-between items-center">
                        <div>
                            <p class="text-sm font-medium text-orange-800">{{ $order->order_number }} — {{ $order->customer_name }}</p>
                            <p class="text-xs text-orange-500">{{ $order->payment_method === 'transfer' ? 'Transferencia' : 'Efectivo' }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-semibold text-orange-700">${{ number_format($order->total, 0, ',', '.') }}</p>
                            @if($deliveryCost > 0)
                                <p class="text-xs text-orange-400">Dom: ${{ number_format($deliveryCost, 0, ',', '.') }}</p>
                            @endif
                        </div>
                    </div>
                @endforeach
                <div class="px-5 py-3 bg-orange-100 flex justify-between items-center">
                    <span class="text-sm font-semibold text-orange-700">Total pendiente</span>
                    <span class="font-bold text-orange-700">${{ number_format($totalPending, 0, ',', '.') }}</span>
                </div>
            </div>
        @endif

    </div>

</body>
</html>
