<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Pedido</title>
    @laravelPWA
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @include('tenant.partials.theme')
</head>

<body class="bg-gray-50 min-h-screen">

    <nav
        class="bg-white border-b border-gray-100 px-4 py-3 flex justify-between items-center sticky top-0 z-10 shadow-sm">
        <div class="flex items-center gap-3">
            <a href="{{ route('tenant.caja') }}"
                class="w-9 h-9 bg-gray-100 hover:bg-gray-200 rounded-xl flex items-center justify-center transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-600" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <div>
                <p class="font-bold text-gray-800 text-sm">Editar pedido</p>
                <p class="text-xs text-gray-400">{{ $order->order_number }}</p>
            </div>
        </div>
        <div class="bg-orange-50 text-orange-500 px-3 py-1.5 rounded-xl text-sm font-semibold" id="total-display">
            $0
        </div>
    </nav>

    <div class="max-w-6xl mx-auto px-4 py-6">

        @if ($order->type === 'delivery')
            <div class="bg-blue-50 border border-blue-200 rounded-xl px-4 py-3 mb-6 text-sm text-blue-700">
                <span class="font-medium">Cliente:</span> {{ $order->customer_name }} —
                <a href="tel:{{ $order->customer_phone }}" class="underline">{{ $order->customer_phone }}</a> —
                {{ $order->delivery_address }}
            </div>
        @else
            <div class="bg-orange-50 border border-orange-200 rounded-xl px-4 py-3 mb-6 text-sm text-orange-700">
                <span class="font-medium">Mesa:</span> {{ $order->table->name ?? '' }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- Menú -->
            <div class="lg:col-span-2">
                @foreach ($categories as $category)
                    @if ($category->products->isNotEmpty())
                        <div class="mb-6">
                            <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-3">
                                {{ $category->name }}</h3>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                @foreach ($category->products as $product)
                                    <div
                                        class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 flex items-center gap-4">
                                        @if ($product->image)
                                            <img src="{{ Storage::url($product->image) }}"
                                                class="w-14 h-14 rounded-xl object-cover flex-shrink-0">
                                        @else
                                            <div
                                                class="w-14 h-14 rounded-xl bg-gray-100 flex items-center justify-center flex-shrink-0">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-gray-400"
                                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                            </div>
                                        @endif
                                        <div class="flex-1 min-w-0">
                                            <p class="font-medium text-gray-800 text-sm">{{ $product->name }}</p>
                                            <p class="text-orange-500 text-sm font-semibold">
                                                ${{ number_format($product->price, 0, ',', '.') }}</p>
                                        </div>
                                        <button type="button"
                                            onclick="addItem({{ $product->id }}, '{{ addslashes($product->name) }}', {{ $product->price }})"
                                            class="w-8 h-8 bg-orange-500 hover:bg-orange-600 text-white rounded-lg flex items-center justify-center transition flex-shrink-0">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 4v16m8-8H4" />
                                            </svg>
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>

            <!-- Resumen -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 sticky top-20">
                    <h3 class="font-semibold text-gray-800 mb-4">Pedido</h3>

                    <div id="order-items" class="space-y-3 mb-4 min-h-16"></div>

                    <div class="border-t border-gray-100 pt-4 mb-4">
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium text-gray-700">Total</span>
                            <span class="font-semibold text-gray-800" id="total-amount">$0</span>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Notas</label>
                        <textarea id="order-notes" rows="2"
                            class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400 resize-none"
                            placeholder="Instrucciones especiales...">{{ $order->notes }}</textarea>
                    </div>

                    <button onclick="submitOrder()"
                        class="w-full bg-orange-500 hover:bg-orange-600 text-white py-3 rounded-xl font-medium text-sm transition shadow-sm">
                        Guardar cambios
                    </button>

                    <a href="{{ route('tenant.caja') }}"
                        class="block w-full text-center mt-3 text-sm text-gray-500 hover:text-gray-700 transition">
                        Cancelar
                    </a>
                </div>
            </div>
        </div>
    </div>

    <form id="order-form" method="POST" action="{{ route('tenant.caja.update', $order->id) }}" class="hidden">
        @csrf
        <input type="hidden" name="notes" id="form-notes">
        <div id="form-items"></div>
    </form>

    <script>
        let items = [];

        // Cargar items existentes
        @foreach ($order->items as $item)
            items.push({
                product_id: {{ $item->product_id }},
                name: '{{ addslashes($item->product_name) }}',
                price: {{ $item->unit_price }},
                quantity: {{ $item->quantity }}
            });
        @endforeach

        renderItems();

        function addItem(id, name, price) {
            const existing = items.find(i => i.product_id == id);
            if (existing) existing.quantity++;
            else items.push({
                product_id: id,
                name,
                price,
                quantity: 1
            });
            renderItems();
        }

        function removeItem(id) {
            items = items.filter(i => i.product_id != id);
            renderItems();
        }

        function changeQty(id, delta) {
            const item = items.find(i => i.product_id == id);
            if (item) {
                item.quantity += delta;
                if (item.quantity <= 0) removeItem(id);
                else renderItems();
            }
        }

        function renderItems() {
            const container = document.getElementById('order-items');
            const total = items.reduce((sum, i) => sum + i.price * i.quantity, 0);
            const formatted = '$' + total.toLocaleString('es-CO');

            document.getElementById('total-amount').textContent = formatted;
            document.getElementById('total-display').textContent = 'Total: ' + formatted;

            if (items.length === 0) {
                container.innerHTML = '<p class="text-sm text-gray-400 text-center py-4">Sin items</p>';
                return;
            }

            container.innerHTML = items.map(item => `
                <div class="flex items-center gap-2">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-800 truncate">${item.name}</p>
                        <p class="text-xs text-gray-400">$${(item.price * item.quantity).toLocaleString('es-CO')}</p>
                    </div>
                    <div class="flex items-center gap-1">
                        <button type="button" onclick="changeQty(${item.product_id}, -1)"
                            class="w-6 h-6 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-600 flex items-center justify-center text-xs">−</button>
                        <span class="text-sm font-medium w-5 text-center">${item.quantity}</span>
                        <button type="button" onclick="changeQty(${item.product_id}, 1)"
                            class="w-6 h-6 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-600 flex items-center justify-center text-xs">+</button>
                        <button type="button" onclick="removeItem(${item.product_id})"
                            class="w-6 h-6 rounded-lg bg-red-50 hover:bg-red-100 text-red-500 flex items-center justify-center text-xs ml-1">×</button>
                    </div>
                </div>
            `).join('');
        }

        function submitOrder() {
            if (items.length === 0) {
                alert('El pedido debe tener al menos un producto.');
                return;
            }

            const form = document.getElementById('order-form');
            const formItems = document.getElementById('form-items');
            document.getElementById('form-notes').value = document.getElementById('order-notes').value;

            formItems.innerHTML = items.map((item, index) => `
                <input type="hidden" name="items[${index}][product_id]" value="${item.product_id}">
                <input type="hidden" name="items[${index}][quantity]" value="${item.quantity}">
            `).join('');

            form.submit();
        }
    </script>

</body>

</html>
