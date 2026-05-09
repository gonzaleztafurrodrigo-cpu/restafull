<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Nuevo pedido - {{ $table->name }}</title>
    @laravelPWA
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * { -webkit-tap-highlight-color: transparent; }
        ::-webkit-scrollbar { display: none; }
        .product-card:active { transform: scale(0.97); }
    </style>
    @include('tenant.partials.theme')
</head>
<body class="bg-gray-50 min-h-screen pb-32">

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
                    <p class="text-xs text-gray-400">Nuevo pedido</p>
                </div>
            </div>
            <div class="bg-orange-500 text-white px-3 py-1.5 rounded-xl text-sm font-bold" id="total-display">$0</div>
        </div>

        <!-- Filtros categoría -->
        <div class="px-4 pb-3 overflow-x-auto">
            <div class="flex gap-2 w-max">
                <button onclick="filterCategory('all')"
                    class="category-pill active flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-medium bg-orange-500 text-white whitespace-nowrap"
                    data-cat="all">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg>
                    Todo
                </button>
                @foreach($categories as $category)
                    @if($category->products->isNotEmpty())
                        <button onclick="filterCategory('{{ $category->id }}')"
                            class="category-pill flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-medium bg-white border border-gray-200 text-gray-600 whitespace-nowrap"
                            data-cat="{{ $category->id }}">
                            {{ $category->name }}
                        </button>
                    @endif
                @endforeach
            </div>
        </div>
    </div>

    @if($activeOrder)
        <div class="mx-4 mt-3 bg-orange-50 border border-orange-200 rounded-xl px-4 py-3 text-xs text-orange-700 flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            Mesa con pedido activo. Los productos se agregarán al pedido existente.
        </div>
    @endif

    <!-- Productos -->
    <div class="px-4 py-4" id="menu-content">
        @foreach($categories as $category)
            @if($category->products->isNotEmpty())
                <div class="category-section mb-5" data-cat="{{ $category->id }}">
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

    <!-- Carrito flotante -->
    <div id="cart-btn" class="fixed bottom-6 left-4 right-4 z-20" style="display:none;">
        <button onclick="toggleCart()"
            class="w-full flex items-center justify-between bg-orange-500 text-white px-5 py-4 rounded-2xl shadow-lg">
            <div class="flex items-center gap-3">
                <span class="bg-white bg-opacity-20 text-white text-xs font-bold w-6 h-6 rounded-lg flex items-center justify-center" id="cart-count">0</span>
                <span class="font-medium text-sm">Ver pedido</span>
            </div>
            <span class="font-bold" id="cart-total-btn">$0</span>
        </button>
    </div>

    <!-- Panel pedido -->
    <div id="cart-panel" class="fixed inset-0 z-30 hidden">
        <div class="absolute inset-0 bg-black bg-opacity-50" onclick="toggleCart()"></div>
        <div class="absolute bottom-0 left-0 right-0 bg-white rounded-t-3xl max-h-[85vh] overflow-y-auto">
            <div class="flex justify-center pt-3 pb-1">
                <div class="w-10 h-1 bg-gray-200 rounded-full"></div>
            </div>
            <div class="px-5 pb-6">
                <div class="flex justify-between items-center mb-4 mt-2">
                    <h3 class="text-lg font-semibold text-gray-800">Pedido — {{ $table->name }}</h3>
                    <button onclick="toggleCart()" class="w-8 h-8 bg-gray-100 rounded-xl flex items-center justify-center text-gray-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>

                <div id="cart-items" class="space-y-3 mb-4"></div>

                <div class="border-t border-gray-100 pt-4 mb-4 flex justify-between">
                    <span class="font-medium text-gray-700">Total</span>
                    <span class="font-bold text-gray-800 text-lg" id="cart-total">$0</span>
                </div>

                <div class="mb-4">
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Notas</label>
                    <textarea id="order-notes" rows="2"
                        class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400 resize-none"
                        placeholder="Sin cebolla, extra salsa..."></textarea>
                </div>

                <button onclick="submitOrder()"
                    class="w-full bg-orange-500 hover:bg-orange-600 text-white py-4 rounded-2xl font-semibold text-sm transition">
                    Enviar pedido
                </button>
            </div>
        </div>
    </div>

    <form id="order-form" method="POST" action="{{ route('tenant.mesero.store') }}" class="hidden">
        @csrf
        <input type="hidden" name="table_id" value="{{ $table->id }}">
        <input type="hidden" name="notes" id="form-notes">
        <div id="form-items"></div>
    </form>

    <script>
        let items = [];

        function addItem(id, name, price) {
            const existing = items.find(i => i.product_id == id);
            if (existing) existing.quantity++;
            else items.push({ product_id: id, name, price, quantity: 1 });
            renderCart();
            event.currentTarget.style.opacity = '0.7';
            setTimeout(() => event.currentTarget.style.opacity = '1', 150);
        }

        function changeQty(id, delta) {
            const item = items.find(i => i.product_id == id);
            if (item) {
                item.quantity += delta;
                if (item.quantity <= 0) items = items.filter(i => i.product_id != id);
                renderCart();
            }
        }

        function renderCart() {
            const total = items.reduce((sum, i) => sum + i.price * i.quantity, 0);
            const count = items.reduce((sum, i) => sum + i.quantity, 0);
            const formatted = '$' + total.toLocaleString('es-CO');

            document.getElementById('total-display').textContent = formatted;
            document.getElementById('cart-total').textContent = formatted;
            document.getElementById('cart-total-btn').textContent = formatted;
            document.getElementById('cart-count').textContent = count;
            document.getElementById('cart-btn').style.display = items.length > 0 ? 'block' : 'none';

            const container = document.getElementById('cart-items');
            if (items.length === 0) {
                container.innerHTML = '<p class="text-sm text-gray-400 text-center py-4">Sin productos</p>';
                return;
            }
            container.innerHTML = items.map(item => `
                <div class="flex items-center gap-3">
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

        function toggleCart() {
            document.getElementById('cart-panel').classList.toggle('hidden');
        }

        function filterCategory(catId) {
            const sections = document.querySelectorAll('.category-section');
            const pills = document.querySelectorAll('.category-pill');
            pills.forEach(p => {
                p.classList.remove('active', 'bg-orange-500', 'text-white');
                p.classList.add('bg-white', 'border', 'border-gray-200', 'text-gray-600');
            });
            const active = document.querySelector(`[data-cat="${catId}"]`);
            if (active) {
                active.classList.add('active', 'bg-orange-500', 'text-white');
                active.classList.remove('bg-white', 'border', 'border-gray-200', 'text-gray-600');
            }
            sections.forEach(s => {
                s.style.display = catId === 'all' || s.dataset.cat === catId ? 'block' : 'none';
            });
        }

        function submitOrder() {
            if (items.length === 0) { alert('Agrega al menos un producto.'); return; }
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
