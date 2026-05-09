<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>{{ $table->name }}</title>
    @laravelPWA
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * { -webkit-tap-highlight-color: transparent; }
        ::-webkit-scrollbar { display: none; }
        .product-card:active { transform: scale(0.97); }
    </style>
    @include('tenant.partials.theme')
</head>
<body class="bg-gray-50 min-h-screen pb-24">

    <!-- Header -->
    <div class="bg-white sticky top-0 z-20 shadow-sm">
        <div class="px-4 py-3 flex justify-between items-center">
            <div class="flex items-center gap-3">
                <a href="{{ route('tenant.mesero') }}" class="w-9 h-9 bg-gray-100 rounded-xl flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                </a>
                @php $settingsWaiter = \Illuminate\Support\Facades\DB::table('settings')->pluck('value', 'key')->toArray(); @endphp
                @if(!empty($settingsWaiter['logo']))
                    <img src="{{ Storage::url($settingsWaiter['logo']) }}" class="w-8 h-8 rounded-xl object-cover">
                @else
                    <div class="w-8 h-8 bg-orange-500 rounded-xl flex items-center justify-center">
                        <span class="text-white font-bold text-xs">RF</span>
                    </div>
                @endif
                <div>
                    <p class="font-bold text-gray-800 text-sm">{{ $table->name }}</p>
                    <p class="text-xs text-gray-400">{{ $order->order_number }}</p>
                </div>
            </div>
            <div class="bg-orange-500 text-white px-3 py-1.5 rounded-xl text-sm font-bold" id="total-display">$0</div>
        </div>

        <!-- Tabs -->
        <div class="flex border-b border-gray-100">
            <button onclick="showTab('menu')" id="tab-menu"
                class="flex-1 py-3 text-sm font-medium border-b-2 border-orange-500 text-orange-500 transition">
                Agregar
            </button>
            <button onclick="showTab('order')" id="tab-order"
                class="flex-1 py-3 text-sm font-medium border-b-2 border-transparent text-gray-400 transition">
                Pedido (0)
            </button>
        </div>

        <!-- Filtros categoría - solo visible en tab menu -->
        <div id="cat-filters" class="px-4 py-3 overflow-x-auto">
            <div class="flex gap-2 w-max">
                <button onclick="filterCat('all')"
                    class="cat-pill flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-medium bg-orange-500 text-white whitespace-nowrap"
                    data-cat="all">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg>
                    Todo
                </button>
                @foreach($categories as $category)
                    @if($category->products->isNotEmpty())
                        <button onclick="filterCat('cat-{{ $category->id }}')"
                            class="cat-pill flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-medium bg-white border border-gray-200 text-gray-600 whitespace-nowrap"
                            data-cat="cat-{{ $category->id }}">
                            {{ $category->name }}
                        </button>
                    @endif
                @endforeach
            </div>
        </div>
    </div>

    <!-- Tab Menú -->
    <div id="panel-menu" class="px-4 py-4">
        @foreach($categories as $category)
            @if($category->products->isNotEmpty())
                <div class="cat-section mb-5" id="cat-{{ $category->id }}">
                    <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3 flex items-center gap-2">
                        <span class="w-1 h-3 bg-orange-500 rounded-full"></span>
                        {{ $category->name }}
                    </h3>
                    <div class="grid grid-cols-2 gap-2">
                        @foreach($category->products as $product)
                            <div class="product-card bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden transition cursor-pointer"
                                onclick="addItem({{ $product->id }}, '{{ addslashes($product->name) }}', {{ $product->price }})">
                                @if($product->image)
                                    <img src="{{ Storage::url($product->image) }}" class="w-full h-24 object-cover">
                                @else
                                    <div class="w-full h-24 bg-orange-50 flex items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-orange-200" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                    </div>
                                @endif
                                <div class="p-2.5">
                                    <p class="text-xs font-medium text-gray-800 leading-tight">{{ $product->name }}</p>
                                    <div class="flex justify-between items-center mt-1.5">
                                        <span class="text-xs font-bold text-orange-500">${{ number_format($product->price, 0, ',', '.') }}</span>
                                        <div class="w-6 h-6 bg-orange-500 rounded-lg flex items-center justify-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" /></svg>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        @endforeach
    </div>

    <!-- Tab Pedido -->
    <div id="panel-order" class="px-4 py-4 hidden">
        <div id="order-list" class="space-y-2 mb-4"></div>
        <div class="mb-4">
            <label class="block text-xs font-medium text-gray-600 mb-1.5">Notas</label>
            <textarea id="order-notes" rows="2"
                class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400 resize-none"
                placeholder="Sin cebolla, extra salsa...">{{ $order->notes }}</textarea>
        </div>
    </div>

    <!-- Barra inferior -->
    <div class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-100 px-4 py-3 z-20">
        <button onclick="submitOrder()"
            class="w-full bg-orange-500 hover:bg-orange-600 text-white py-3.5 rounded-2xl font-semibold text-sm transition shadow-sm">
            Guardar pedido
        </button>
    </div>

    <form id="order-form" method="POST" action="{{ route('tenant.mesero.update', $order->id) }}" class="hidden">
        @csrf
        <input type="hidden" name="notes" id="form-notes">
        <div id="form-items"></div>
    </form>

    <script>
        let items = [];

        @foreach($order->items as $item)
            items.push({
                product_id: {{ $item->product_id }},
                name: '{{ addslashes($item->product_name) }}',
                price: {{ $item->unit_price }},
                quantity: {{ $item->quantity }}
            });
        @endforeach

        renderAll();

        function addItem(id, name, price) {
            const existing = items.find(i => i.product_id == id);
            if (existing) existing.quantity++;
            else items.push({ product_id: id, name, price, quantity: 1 });
            renderAll();
            event.currentTarget.style.opacity = '0.7';
            setTimeout(() => event.currentTarget.style.opacity = '1', 150);
        }

        function changeQty(id, delta) {
            const item = items.find(i => i.product_id == id);
            if (item) {
                item.quantity += delta;
                if (item.quantity <= 0) items = items.filter(i => i.product_id != id);
                renderAll();
            }
        }

        function renderAll() {
            const total = items.reduce((sum, i) => sum + i.price * i.quantity, 0);
            const count = items.reduce((sum, i) => sum + i.quantity, 0);
            document.getElementById('total-display').textContent = '$' + total.toLocaleString('es-CO');
            document.getElementById('tab-order').textContent = `Pedido (${count})`;

            const container = document.getElementById('order-list');
            if (items.length === 0) {
                container.innerHTML = '<p class="text-sm text-gray-400 text-center py-8">Sin productos</p>';
                return;
            }
            container.innerHTML = items.map(item => `
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm px-4 py-3 flex items-center gap-3">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-800 truncate">${item.name}</p>
                        <p class="text-xs text-orange-500 font-medium">$${(item.price * item.quantity).toLocaleString('es-CO')}</p>
                    </div>
                    <div class="flex items-center gap-2 bg-gray-100 rounded-xl px-2 py-1.5">
                        <button type="button" onclick="changeQty(${item.product_id}, -1)"
                            class="w-6 h-6 flex items-center justify-center text-red-400 font-bold text-lg leading-none">−</button>
                        <span class="text-sm font-bold w-5 text-center text-gray-800">${item.quantity}</span>
                        <button type="button" onclick="changeQty(${item.product_id}, 1)"
                            class="w-6 h-6 flex items-center justify-center text-orange-500 font-bold text-lg leading-none">+</button>
                    </div>
                </div>
            `).join('');
        }

        function showTab(tab) {
            const tabMenu = document.getElementById('tab-menu');
            const tabOrder = document.getElementById('tab-order');
            const panelMenu = document.getElementById('panel-menu');
            const panelOrder = document.getElementById('panel-order');
            const catFilters = document.getElementById('cat-filters');

            if (tab === 'menu') {
                tabMenu.classList.add('border-orange-500', 'text-orange-500');
                tabMenu.classList.remove('border-transparent', 'text-gray-400');
                tabOrder.classList.remove('border-orange-500', 'text-orange-500');
                tabOrder.classList.add('border-transparent', 'text-gray-400');
                panelMenu.classList.remove('hidden');
                panelOrder.classList.add('hidden');
                catFilters.classList.remove('hidden');
            } else {
                tabOrder.classList.add('border-orange-500', 'text-orange-500');
                tabOrder.classList.remove('border-transparent', 'text-gray-400');
                tabMenu.classList.remove('border-orange-500', 'text-orange-500');
                tabMenu.classList.add('border-transparent', 'text-gray-400');
                panelOrder.classList.remove('hidden');
                panelMenu.classList.add('hidden');
                catFilters.classList.add('hidden');
            }
        }

        function filterCat(catId) {
            const sections = document.querySelectorAll('.cat-section');
            const pills = document.querySelectorAll('.cat-pill');
            pills.forEach(p => {
                p.classList.remove('bg-orange-500', 'text-white');
                p.classList.add('bg-white', 'border', 'border-gray-200', 'text-gray-600');
            });
            const active = document.querySelector(`[data-cat="${catId}"]`);
            if (active) {
                active.classList.add('bg-orange-500', 'text-white');
                active.classList.remove('bg-white', 'border', 'border-gray-200', 'text-gray-600');
            }
            sections.forEach(s => {
                s.style.display = catId === 'all' || s.id === catId ? 'block' : 'none';
            });
        }

        function submitOrder() {
            if (items.length === 0) { alert('El pedido debe tener al menos un producto.'); return; }
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
