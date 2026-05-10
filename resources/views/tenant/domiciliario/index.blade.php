<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Domiciliario</title>
    @laravelPWA
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * { -webkit-tap-highlight-color: transparent; }
        ::-webkit-scrollbar { display: none; }
    </style>
    @include('tenant.partials.theme')
</head>

<body class="bg-gray-50 min-h-screen pb-8">

    <!-- Header -->
    <div class="bg-orange-500 px-5 pt-10 pb-4 relative overflow-hidden mb-3">
        <div class="absolute inset-0 opacity-10">
            <div class="absolute -top-4 -right-4 w-32 h-32 bg-white rounded-full"></div>
            <div class="absolute -bottom-8 -left-8 w-40 h-40 bg-white rounded-full"></div>
        </div>
        <div class="relative flex justify-between items-start">
            <div>
                <p class="text-orange-100 text-xs mb-1">Bienvenido</p>
                <h1 class="text-xl font-bold text-white">{{ session('tenant_user')['name'] }}</h1>
                <div class="flex items-center gap-1.5 mt-4">
                    <span class="w-2 h-2 bg-green-300 rounded-full animate-pulse" id="connection-dot"></span>
                    <span class="text-orange-100 text-xs" id="connection-status">Conectando...</span>
                </div>
            </div>
            <form method="POST" action="{{ route('tenant.logout') }}">
                @csrf
                <button type="submit" class="w-10 h-10 bg-white bg-opacity-20 rounded-xl flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h6a2 2 0 012 2v1" />
                    </svg>
                </button>
            </form>
        </div>
    </div>

    <div id="tenant-id" data-id="{{ tenancy()->tenant->id }}" style="display:none;"></div>

    <div class="px-4 mt-6 relative z-10 space-y-4">

        <!-- Pedidos activos -->
        @if ($activeOrders->count() > 0)
            <div>
                <h2 class="text-sm font-semibold text-gray-700 mb-3">En curso ({{ $activeOrders->count() }})</h2>
                <div class="space-y-3">
                    @foreach ($activeOrders as $order)
                        <div class="bg-white rounded-2xl border {{ $order->status === 'dispatched' ? 'border-orange-200' : 'border-red-200' }} shadow-sm overflow-hidden" id="order-{{ $order->id }}">
                            <div class="px-4 py-3 border-b border-gray-50">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <p class="font-semibold text-gray-800 text-sm">{{ $order->order_number }}</p>
                                        <p class="text-xs text-gray-500 mt-0.5">{{ $order->customer_name }}</p>
                                    </div>
                                    @if ($order->status === 'dispatched')
                                        <span class="bg-orange-50 text-orange-500 px-2.5 py-1 rounded-lg text-xs font-medium">En camino</span>
                                    @else
                                        <span class="bg-red-50 text-red-500 px-2.5 py-1 rounded-lg text-xs font-medium">Entregar a caja</span>
                                    @endif
                                </div>
                            </div>
                            <div class="px-4 py-3 border-b border-gray-50 space-y-2">
                                <div class="flex items-start gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-gray-400 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    </svg>
                                    <p class="text-xs text-gray-600">{{ $order->delivery_address }}</p>
                                </div>
                                <div class="flex items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                    </svg>
                                    <a href="tel:{{ $order->customer_phone }}" class="text-xs text-orange-500 font-medium">{{ $order->customer_phone }}</a>
                                </div>
                            </div>
                            <div class="px-4 py-3 flex justify-between items-center">
                                <div>
                                    <span class="font-bold text-gray-800">${{ number_format($order->total, 0, ',', '.') }}</span>
                                    <span class="text-xs text-gray-400 ml-2">{{ $order->payment_method === 'transfer' ? 'Transferencia' : 'Efectivo' }}</span>
                                </div>
                                @if ($order->status === 'dispatched')
                                    <button onclick="markDelivered({{ $order->id }})" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-xl text-xs font-semibold transition">
                                        Marcar entregado
                                    </button>
                                @else
                                    <span class="text-xs text-red-500 font-medium">Entregar dinero a caja</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8 text-center">
                <div class="w-14 h-14 bg-green-50 rounded-2xl flex items-center justify-center mx-auto mb-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <p class="text-sm font-medium text-gray-700">Sin domicilios activos</p>
                <p class="text-xs text-gray-400 mt-1">Espera a que te asignen un pedido</p>
            </div>
        @endif

        <!-- Historial -->
        <div>
            <div class="flex justify-between items-center mb-3">
                <h2 class="text-sm font-semibold text-gray-700">Historial</h2>
                <div class="bg-orange-50 rounded-xl px-3 py-1">
                    <span class="text-xs font-semibold text-orange-500">${{ number_format($totalEarnings, 0, ',', '.') }}</span>
                    <span class="text-xs text-orange-400 ml-1">entregado</span>
                </div>
            </div>

            <!-- Filtros período -->
            <div class="flex gap-2 mb-3 overflow-x-auto">
                <a href="?period=today&page=1" class="px-4 py-2 rounded-xl text-xs font-medium whitespace-nowrap transition {{ $period === 'today' ? 'bg-orange-500 text-white' : 'bg-white border border-gray-200 text-gray-600' }}">Hoy</a>
                <a href="?period=week&page=1" class="px-4 py-2 rounded-xl text-xs font-medium whitespace-nowrap transition {{ $period === 'week' ? 'bg-orange-500 text-white' : 'bg-white border border-gray-200 text-gray-600' }}">Esta semana</a>
                <a href="?period=month&page=1" class="px-4 py-2 rounded-xl text-xs font-medium whitespace-nowrap transition {{ $period === 'month' ? 'bg-orange-500 text-white' : 'bg-white border border-gray-200 text-gray-600' }}">Este mes</a>
            </div>

            @forelse($completedOrders as $order)
                @php
                    $itemsJson = $order->items->map(fn($i) => [
                        'qty' => $i->quantity,
                        'name' => $i->product_name,
                        'subtotal' => $i->subtotal,
                    ])->toJson();
                    $deliveryCost = $order->total - $order->subtotal;
                    $paymentLabel = $order->payment_method === 'transfer' ? 'Transferencia' : 'Efectivo';
                    $formattedDate = \Carbon\Carbon::parse($order->updated_at)->format('d/m/Y h:i a');
                @endphp
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-3 cursor-pointer active:scale-95 transition order-card"
                    data-number="{{ $order->order_number }}"
                    data-customer="{{ $order->customer_name }}"
                    data-address="{{ $order->delivery_address ?? '' }}"
                    data-phone="{{ $order->customer_phone ?? '' }}"
                    data-subtotal="{{ $order->subtotal }}"
                    data-delivery="{{ $deliveryCost }}"
                    data-total="{{ $order->total }}"
                    data-payment="{{ $paymentLabel }}"
                    data-date="{{ $formattedDate }}"
                    data-items="{{ base64_encode($itemsJson) }}">
                    <div class="px-4 py-3 flex justify-between items-center">
                        <div>
                            <p class="text-sm font-semibold text-gray-800">{{ $order->order_number }}</p>
                            <p class="text-xs text-gray-400 mt-0.5">{{ $order->customer_name }}</p>
                            <p class="text-xs text-gray-400">{{ $formattedDate }}</p>
                        </div>
                        <div class="text-right">
                            <p class="font-bold text-gray-800">${{ number_format($order->total, 0, ',', '.') }}</p>
                            <span class="text-xs {{ $order->payment_method === 'transfer' ? 'text-blue-500' : 'text-green-500' }} font-medium">
                                {{ $paymentLabel }}
                            </span>
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8 text-center">
                    <p class="text-sm text-gray-400">Sin entregas en este período.</p>
                </div>
            @endforelse

            <!-- Paginación -->
            @if ($totalPages > 1)
                <div class="flex justify-center items-center gap-2 mt-4">
                    @if ($page > 1)
                        <a href="?period={{ $period }}&page={{ $page - 1 }}" class="w-9 h-9 bg-white border border-gray-200 rounded-xl flex items-center justify-center text-gray-600 hover:bg-gray-50 transition">‹</a>
                    @endif
                    @for ($i = 1; $i <= $totalPages; $i++)
                        <a href="?period={{ $period }}&page={{ $i }}" class="w-9 h-9 rounded-xl flex items-center justify-center text-xs font-medium transition {{ $i == $page ? 'bg-orange-500 text-white' : 'bg-white border border-gray-200 text-gray-600 hover:bg-gray-50' }}">{{ $i }}</a>
                    @endfor
                    @if ($page < $totalPages)
                        <a href="?period={{ $period }}&page={{ $page + 1 }}" class="w-9 h-9 bg-white border border-gray-200 rounded-xl flex items-center justify-center text-gray-600 hover:bg-gray-50 transition">›</a>
                    @endif
                </div>
                <p class="text-center text-xs text-gray-400 mt-2">{{ $totalCompleted }} entrega(s)</p>
            @endif
        </div>
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
                <span class="bg-green-50 text-green-600 px-2.5 py-1 rounded-lg text-xs font-medium">Entregado</span>
            </div>
            <div class="bg-gray-50 rounded-2xl p-4 mb-4">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-3">Cliente</p>
                <p class="text-sm font-medium text-gray-800" id="modal-customer"></p>
                <p class="text-xs text-gray-500 mt-1" id="modal-address"></p>
                <a id="modal-phone" href="#" class="text-xs text-orange-500 font-medium mt-1 block"></a>
            </div>
            <div class="bg-gray-50 rounded-2xl p-4 mb-4">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-3">Productos</p>
                <div id="modal-items" class="space-y-2"></div>
            </div>
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
                <div class="flex justify-between items-center border-t border-gray-100 pt-2">
                    <span class="font-bold text-gray-800">Total</span>
                    <span class="text-xl font-bold text-gray-800" id="modal-total"></span>
                </div>
            </div>
            <button onclick="closeModal()" class="w-full bg-gray-100 text-gray-600 py-3 rounded-2xl font-medium text-sm">Cerrar</button>
        </div>
    </div>

    <script>
        const csrfToken = '{{ csrf_token() }}';

        async function markDelivered(id) {
            if (!confirm('¿Confirmar entrega de este pedido?')) return;
            await fetch(`/domiciliario/pedidos/${id}/entregar`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json' }
            });
            location.reload();
        }

        function closeModal() {
            document.getElementById('modal-order').classList.add('hidden');
        }

        document.addEventListener('DOMContentLoaded', function() {
            if (window.Echo) {
                document.getElementById('connection-status').textContent = 'En línea';
                const tenantId = document.getElementById('tenant-id').dataset.id;
                window.Echo.channel(`orders.${tenantId}`)
                    .listen('.order.status', (data) => {
                        if (['dispatched', 'delivered', 'collecting'].includes(data.status)) {
                            location.reload();
                        }
                    });
            }

            document.querySelectorAll('.order-card').forEach(card => {
                card.addEventListener('click', function() {
                    const subtotal = parseFloat(this.dataset.subtotal);
                    const deliveryCost = parseFloat(this.dataset.delivery);
                    const total = parseFloat(this.dataset.total);
                    const items = JSON.parse(atob(this.dataset.items));

                    document.getElementById('modal-number').textContent = this.dataset.number;
                    document.getElementById('modal-date').textContent = this.dataset.date;
                    document.getElementById('modal-customer').textContent = this.dataset.customer;
                    document.getElementById('modal-address').textContent = this.dataset.address ? '📍 ' + this.dataset.address : '';

                    const phoneEl = document.getElementById('modal-phone');
                    if (this.dataset.phone) {
                        phoneEl.textContent = '📞 ' + this.dataset.phone;
                        phoneEl.href = 'tel:' + this.dataset.phone;
                    } else {
                        phoneEl.textContent = '';
                    }

                    document.getElementById('modal-subtotal').textContent = '$' + subtotal.toLocaleString('es-CO');
                    document.getElementById('modal-total').textContent = '$' + total.toLocaleString('es-CO');
                    document.getElementById('modal-payment').textContent = this.dataset.payment;

                    if (deliveryCost > 0) {
                        document.getElementById('modal-delivery-row').classList.remove('hidden');
                        document.getElementById('modal-delivery').textContent = '$' + deliveryCost.toLocaleString('es-CO');
                    } else {
                        document.getElementById('modal-delivery-row').classList.add('hidden');
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
                });
            });
        });
    </script>

</body>
</html>
