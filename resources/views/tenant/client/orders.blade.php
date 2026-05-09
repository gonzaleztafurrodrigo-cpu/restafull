<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Mis pedidos</title>
    @laravelPWA
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * { -webkit-tap-highlight-color: transparent; }
    </style>
    @include('tenant.partials.theme')
</head>

<body class="bg-gray-50 min-h-screen pb-8">

    <!-- Header -->
    <div class="bg-white px-4 py-3 flex items-center gap-3 border-b border-gray-100 sticky top-0 z-10">
        <a href="{{ route('tenant.client.dashboard') }}" class="w-9 h-9 bg-gray-100 rounded-xl flex items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
        </a>
        <p class="font-semibold text-gray-800">Mis pedidos</p>
    </div>

    <div class="px-4 py-4 space-y-3">
        @forelse($orders as $order)
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
                    'pending' => 'bg-yellow-50 text-yellow-600',
                    'confirmed' => 'bg-blue-50 text-blue-600',
                    'preparing' => 'bg-purple-50 text-purple-600',
                    'ready' => 'bg-green-50 text-green-600',
                    'dispatched' => 'bg-orange-50 text-orange-500',
                    'collecting' => 'bg-orange-50 text-orange-500',
                    'delivered' => 'bg-green-50 text-green-600',
                    'cancelled' => 'bg-red-50 text-red-500',
                ];
                $deliveryCost = $order->total - $order->subtotal;
                $itemsJson = $order->items->map(fn($i) => [
                    'qty' => $i->quantity,
                    'name' => $i->product_name,
                    'subtotal' => $i->subtotal,
                ])->toJson();
            @endphp
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden cursor-pointer active:scale-95 transition"
                onclick="openModal(
                    '{{ $order->order_number }}',
                    '{{ $order->status }}',
                    '{{ $statusLabels[$order->status] ?? $order->status }}',
                    '{{ $statusColors[$order->status] ?? 'bg-gray-50 text-gray-500' }}',
                    {{ $order->subtotal }},
                    {{ $deliveryCost }},
                    {{ $order->total }},
                    '{{ $order->payment_method === 'transfer' ? 'Transferencia' : 'Efectivo' }}',
                    '{{ addslashes($order->delivery_address ?? '') }}',
                    '{{ \Carbon\Carbon::parse($order->created_at)->format('d/m/Y h:i a') }}',
                    {{ $itemsJson }}
                )">
                <div class="px-4 py-3 flex justify-between items-center">
                    <div>
                        <p class="font-semibold text-gray-800 text-sm">{{ $order->order_number }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">{{ \Carbon\Carbon::parse($order->created_at)->format('d/m/Y h:i a') }}</p>
                    </div>
                    <span class="{{ $statusColors[$order->status] ?? 'bg-gray-50 text-gray-500' }} px-2.5 py-1 rounded-lg text-xs font-medium">
                        {{ $statusLabels[$order->status] ?? $order->status }}
                    </span>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-12 text-center">
                <div class="flex flex-col items-center gap-3 text-gray-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    <p class="text-sm">No tienes pedidos aún.</p>
                    <a href="{{ route('tenant.menu') }}" class="text-orange-500 text-sm font-medium hover:underline">Hacer mi primer pedido</a>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Modal detalle pedido -->
    <div id="modal-order" class="fixed inset-0 hidden" style="z-index: 9999;">
        <div class="absolute inset-0 bg-black bg-opacity-50" onclick="closeModal()"></div>
        <div class="absolute bottom-0 left-0 right-0 bg-white rounded-t-3xl p-6 max-h-[85vh] overflow-y-auto">
            <div class="w-10 h-1 bg-gray-200 rounded-full mx-auto mb-5"></div>

            <div class="flex justify-between items-start mb-4">
                <div>
                    <p class="font-bold text-gray-800 text-lg" id="modal-number"></p>
                    <p class="text-xs text-gray-400 mt-0.5" id="modal-date"></p>
                </div>
                <span id="modal-status" class="px-2.5 py-1 rounded-lg text-xs font-medium"></span>
            </div>

            <!-- Items -->
            <div class="bg-gray-50 rounded-2xl p-4 mb-4">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-3">Productos</p>
                <div id="modal-items" class="space-y-2"></div>
            </div>

            <!-- Totales -->
            <div class="space-y-2 mb-4">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Subtotal</span>
                    <span class="text-gray-700" id="modal-subtotal"></span>
                </div>
                <div id="modal-delivery-row" class="flex justify-between text-sm hidden">
                    <span class="text-gray-500">Domicilio</span>
                    <span class="text-gray-700" id="modal-delivery"></span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Método de pago</span>
                    <span class="text-gray-700" id="modal-payment"></span>
                </div>
                <div id="modal-address-row" class="hidden">
                    <p class="text-xs text-gray-400" id="modal-address"></p>
                </div>
                <div class="flex justify-between items-center border-t border-gray-100 pt-2">
                    <span class="font-bold text-gray-800">Total</span>
                    <span class="text-xl font-bold text-gray-800" id="modal-total"></span>
                </div>
            </div>

            <button onclick="closeModal()" class="w-full bg-gray-100 text-gray-600 py-3 rounded-2xl font-medium text-sm">
                Cerrar
            </button>
        </div>
    </div>

    <div id="tenant-id" data-id="{{ tenancy()->tenant->id }}" style="display:none;"></div>

    <script>
        function openModal(number, status, statusLabel, statusColor, subtotal, deliveryCost, total, payment, address, date, items) {
            document.getElementById('modal-number').textContent = number;
            document.getElementById('modal-date').textContent = date;
            document.getElementById('modal-status').textContent = statusLabel;
            document.getElementById('modal-status').className = statusColor + ' px-2.5 py-1 rounded-lg text-xs font-medium';
            document.getElementById('modal-subtotal').textContent = '$' + subtotal.toLocaleString('es-CO');
            document.getElementById('modal-total').textContent = '$' + total.toLocaleString('es-CO');
            document.getElementById('modal-payment').textContent = payment;

            if (deliveryCost > 0) {
                document.getElementById('modal-delivery-row').classList.remove('hidden');
                document.getElementById('modal-delivery').textContent = '$' + deliveryCost.toLocaleString('es-CO');
            } else {
                document.getElementById('modal-delivery-row').classList.add('hidden');
            }

            if (address) {
                document.getElementById('modal-address-row').classList.remove('hidden');
                document.getElementById('modal-address').textContent = '📍 ' + address;
            } else {
                document.getElementById('modal-address-row').classList.add('hidden');
            }

            let itemsHtml = '';
            items.forEach(item => {
                itemsHtml += `<div class="flex justify-between text-sm">
                    <span class="text-gray-700">${item.qty}x ${item.name}</span>
                    <span class="text-gray-500">$${Number(item.subtotal).toLocaleString('es-CO')}</span>
                </div>`;
            });
            document.getElementById('modal-items').innerHTML = itemsHtml;

            document.getElementById('modal-order').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('modal-order').classList.add('hidden');
        }

        document.addEventListener('DOMContentLoaded', function() {
            if (window.Echo) {
                const tenantId = document.getElementById('tenant-id').dataset.id;
                window.Echo.channel(`orders.${tenantId}`)
                    .listen('.order.status', () => { location.reload(); });
            }
        });
    </script>
</body>

</html>
