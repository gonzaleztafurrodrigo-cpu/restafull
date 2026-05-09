<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>{{ $settings['restaurant_name'] ?? 'Menú' }}</title>
    @laravelPWA
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * {
            -webkit-tap-highlight-color: transparent;
        }

        body {
            overscroll-behavior: none;
        }

        .category-pill {
            transition: all 0.2s ease;
        }

        .category-pill.active {
            background: #f97316;
            color: white;
        }

        .product-card {
            transition: transform 0.1s ease;
        }

        .product-card:active {
            transform: scale(0.97);
        }

        .cart-btn {
            transition: all 0.3s ease;
        }

        ::-webkit-scrollbar {
            display: none;
        }
    </style>
    @include('tenant.partials.theme')
</head>

<body class="bg-gray-50 min-h-screen pb-32">

    <!-- Header -->
    <div class="bg-white sticky top-0 z-20 shadow-sm">

        <!-- Navbar -->
        <div class="px-4 py-3 flex justify-between items-center">
            <div class="flex items-center gap-2">
                @if (!empty($settings['logo']))
                    <img src="{{ Storage::url($settings['logo']) }}" class="w-9 h-9 rounded-xl object-cover">
                @else
                    <div
                        class="bg-orange-500 text-white w-9 h-9 rounded-xl flex items-center justify-center font-bold text-sm">
                        RF</div>
                @endif
                <div>
                    <p class="font-semibold text-gray-800 text-sm leading-tight">
                        {{ $settings['restaurant_name'] ?? 'Menú' }}</p>
                    @if (!empty($settings['delivery_time']))
                        <p class="text-xs text-gray-400">{{ $settings['delivery_time'] }} min ·
                            @if (!empty($settings['min_order']) && $settings['min_order'] > 0)
                                Mín ${{ number_format($settings['min_order'], 0, ',', '.') }}
                            @else
                                Sin mínimo
                            @endif
                        </p>
                    @endif
                </div>
            </div>
            <button onclick="toggleCart()"
                class="relative w-10 h-10 bg-orange-50 rounded-xl flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-orange-500" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                <span
                    class="absolute -top-1 -right-1 bg-orange-500 text-white text-xs w-5 h-5 rounded-full flex items-center justify-center font-medium"
                    id="cart-count" style="display:none;">0</span>
            </button>
        </div>

        <!-- Filtros por categoría -->
        <div class="px-4 pb-3 overflow-x-auto">
            <div class="flex gap-2 w-max">
                <button onclick="filterCategory('all')"
                    class="category-pill active flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-medium bg-orange-500 text-white whitespace-nowrap"
                    data-cat="all">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                    Todo
                </button>
                @foreach ($categories as $category)
                    @if ($category->products->isNotEmpty())
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

    <!-- Contenido -->
    <div class="px-4 py-4" id="menu-content">
        @foreach ($categories as $category)
            @if ($category->products->isNotEmpty())
                <div class="category-section mb-6" data-cat="{{ $category->id }}">
                    <h3 class="text-sm font-semibold text-gray-700 mb-3 flex items-center gap-2">
                        <span class="w-1 h-4 bg-orange-500 rounded-full inline-block"></span>
                        {{ $category->name }}
                    </h3>
                    <div class="grid grid-cols-2 gap-3">
                        @foreach ($category->products as $product)
                            <div class="product-card bg-white rounded-2xl overflow-hidden shadow-sm border border-gray-100"
                                onclick="addItem({{ $product->id }}, '{{ addslashes($product->name) }}', {{ $product->price }})">
                                @if ($product->image)
                                    <img src="{{ Storage::url($product->image) }}" class="w-full h-28 object-cover">
                                @else
                                    <div class="w-full h-28 bg-orange-50 flex items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 text-orange-200"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                @endif
                                <div class="p-3">
                                    <p class="font-medium text-gray-800 text-sm leading-tight">{{ $product->name }}</p>
                                    @if ($product->description)
                                        <p class="text-xs text-gray-500 mt-1 leading-snug">
                                            {{ Str::limit($product->description, 70) }}</p>
                                    @endif
                                    <div class="flex justify-between items-center mt-2">
                                        <span
                                            class="text-orange-500 font-semibold text-sm">${{ number_format($product->price, 0, ',', '.') }}</span>
                                        <div class="w-7 h-7 bg-orange-500 rounded-lg flex items-center justify-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-white"
                                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                    d="M12 4v16m8-8H4" />
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        @endforeach

        @if ($categories->isEmpty())
            <div class="text-center py-16 text-gray-400">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
                <p class="text-sm">El menú no está disponible aún.</p>
            </div>
        @endif
    </div>

    <!-- Botón flotante carrito -->
    <div class="fixed bottom-6 left-4 right-4 z-20" id="cart-btn" style="display:none;">
        <button onclick="toggleCart()"
            class="cart-btn w-full flex items-center justify-between bg-orange-500 text-white px-5 py-4 rounded-2xl shadow-lg">
            <div class="flex items-center gap-3">
                <span
                    class="bg-white bg-opacity-20 text-white text-xs font-bold w-6 h-6 rounded-lg flex items-center justify-center"
                    id="cart-btn-count">0</span>
                <span class="font-medium text-sm">Ver carrito</span>
            </div>
            <span class="font-bold" id="cart-btn-total">$0</span>
        </button>
    </div>

    <!-- Panel carrito -->
    <div id="cart-panel" class="fixed inset-0 z-30 hidden">
        <div class="absolute inset-0 bg-black bg-opacity-50" onclick="toggleCart()"></div>
        <div class="absolute bottom-0 left-0 right-0 bg-white rounded-t-3xl max-h-[90vh] overflow-y-auto">

            <!-- Handle -->
            <div class="flex justify-center pt-3 pb-1">
                <div class="w-10 h-1 bg-gray-200 rounded-full"></div>
            </div>

            <div class="px-5 pb-6">
                <div class="flex justify-between items-center mb-5 mt-2">
                    <h3 class="text-lg font-semibold text-gray-800">Tu pedido</h3>
                    <button onclick="toggleCart()"
                        class="text-gray-400 hover:text-gray-600 w-8 h-8 bg-gray-100 rounded-xl flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div id="cart-items" class="space-y-3 mb-5"></div>

                <div class="border-t border-gray-100 pt-4 mb-5 space-y-2">
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-500">Subtotal</span>
                        <span class="text-gray-700" id="cart-subtotal">$0</span>
                    </div>
                    @if (($settings['delivery_cost'] ?? 0) > 0)
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-gray-500">Costo de domicilio</span>
                            <span
                                class="text-gray-700">${{ number_format($settings['delivery_cost'], 0, ',', '.') }}</span>
                        </div>
                    @else
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-gray-500">Domicilio</span>
                            <span class="text-green-500 font-medium">Gratis</span>
                        </div>
                    @endif
                    <div class="flex justify-between items-center border-t border-gray-100 pt-2">
                        <span class="font-semibold text-gray-700">Total</span>
                        <span class="font-bold text-gray-800 text-lg" id="cart-total">$0</span>
                    </div>
                </div>

                <!-- Datos cliente -->
                <div class="space-y-3 mb-5">

                    @if ($customer)
                        <div class="bg-orange-50 rounded-xl p-3 flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 bg-orange-500 rounded-lg flex items-center justify-center">
                                    <span
                                        class="text-white text-xs font-bold">{{ strtoupper(substr($customer['name'], 0, 1)) }}</span>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold text-orange-700">{{ $customer['name'] }}</p>
                                    <p class="text-xs text-orange-500">{{ $customer['phone'] }}</p>
                                </div>
                            </div>
                            <a href="{{ route('tenant.client.dashboard') }}"
                                class="text-xs text-orange-500 font-medium">Mi cuenta</a>
                        </div>
                    @endif

                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Nombre completo</label>
                        <input type="text" id="customer-name"
                            class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400"
                            placeholder="Tu nombre" value="{{ $customer['name'] ?? '' }}">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Teléfono</label>
                        <input type="tel" id="customer-phone"
                            class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400"
                            placeholder="3001234567" value="{{ $customer['phone'] ?? '' }}">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Dirección de entrega</label>
                        @if ($addresses->count() > 0)
                            <select id="address-select" onchange="selectAddress(this.value)"
                                class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400 bg-white mb-2">
                                <option value="">Selecciona una dirección</option>
                                @foreach ($addresses as $addr)
                                    <option value="{{ $addr->address }}" {{ $addr->is_default ? 'selected' : '' }}>
                                        {{ $addr->label }} — {{ Str::limit($addr->address, 30) }}
                                    </option>
                                @endforeach
                                <option value="other">Otra dirección...</option>
                            </select>
                        @endif
                        <textarea id="delivery-address" rows="2"
                            class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400 resize-none"
                            placeholder="Calle, barrio, referencias...">{{ $defaultAddress->address ?? '' }}</textarea>
                    </div>

                    <!-- Método de pago -->
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Método de pago</label>
                        <div class="grid grid-cols-2 gap-2">
                            <label
                                class="flex items-center gap-2 p-3 border-2 border-orange-400 rounded-xl cursor-pointer bg-orange-50"
                                id="label-cash">
                                <input type="radio" name="payment" value="cash" class="accent-orange-500"
                                    checked onchange="toggleBankInfo()">
                                <div>
                                    <p class="text-xs font-semibold text-gray-800">Efectivo</p>
                                    <p class="text-xs text-gray-400">Al recibir</p>
                                </div>
                            </label>
                            <label class="flex items-center gap-2 p-3 border border-gray-200 rounded-xl cursor-pointer"
                                id="label-transfer">
                                <input type="radio" name="payment" value="transfer" class="accent-orange-500"
                                    onchange="toggleBankInfo()">
                                <div>
                                    <p class="text-xs font-semibold text-gray-800">Transferencia</p>
                                    <p class="text-xs text-gray-400">Nequi, Bancolombia</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    @php $bankAccounts = \Illuminate\Support\Facades\DB::table('bank_accounts')->where('is_active', true)->get(); @endphp
                    @if ($bankAccounts->count() > 0)
                        <div id="bank-info" class="hidden space-y-3">
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1.5">Selecciona una
                                    cuenta</label>
                                <select id="bank-select" onchange="showBankInfo(this.value)"
                                    class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400 bg-white">
                                    <option value="">Selecciona un banco...</option>
                                    @foreach ($bankAccounts as $bank)
                                        <option value="{{ $bank->id }}">{{ $bank->bank_name }} —
                                            {{ $bank->account_type }}</option>
                                    @endforeach
                                </select>
                            </div>

                            @foreach ($bankAccounts as $bank)
                                <div id="bank-detail-{{ $bank->id }}" class="bg-blue-50 rounded-xl p-4 hidden">
                                    <p class="text-xs font-semibold text-blue-700 mb-2">{{ $bank->bank_name }}</p>
                                    <div class="space-y-1 text-xs text-blue-600">
                                        <p><span class="font-medium">Tipo:</span> {{ $bank->account_type }}</p>
                                        <p><span class="font-medium">Cuenta:</span> {{ $bank->account_number }}</p>
                                        <p><span class="font-medium">Titular:</span> {{ $bank->owner_name }}</p>
                                        @if ($bank->owner_id)
                                            <p><span class="font-medium">NIT/CC:</span> {{ $bank->owner_id }}</p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Notas <span
                                class="text-gray-400 font-normal">(opcional)</span></label>
                        <textarea id="order-notes" rows="2"
                            class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400 resize-none"
                            placeholder="Sin cebolla, extra salsa..."></textarea>
                    </div>
                </div>

                <button onclick="submitOrder()"
                    class="w-full bg-orange-500 hover:bg-orange-600 text-white py-4 rounded-2xl font-semibold text-sm transition shadow-sm"
                    id="submit-btn">
                    Realizar pedido
                </button>
            </div>
        </div>
    </div>

    <form id="order-form" method="POST" action="{{ route('tenant.client.order.store') }}" class="hidden">
        @csrf
        <input type="hidden" name="customer_name" id="form-name">
        <input type="hidden" name="customer_phone" id="form-phone">
        <input type="hidden" name="delivery_address" id="form-address">
        <input type="hidden" name="payment_method" id="form-payment">
        <input type="hidden" name="notes" id="form-notes">
        <div id="form-items"></div>
    </form>

    <script>
        let items = [];
        let submitting = false;
        const deliveryCost = {{ $deliveryCost }};
        const minOrder = {{ (int) ($settings['min_order'] ?? 0) }};

        function addItem(id, name, price) {
            const existing = items.find(i => i.product_id == id);
            if (existing) existing.quantity++;
            else items.push({
                product_id: id,
                name,
                price,
                quantity: 1
            });
            renderCart();

            // Feedback visual
            const btn = event.currentTarget;
            btn.style.transform = 'scale(0.95)';
            setTimeout(() => btn.style.transform = '', 150);
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
            const subtotal = items.reduce((sum, i) => sum + i.price * i.quantity, 0);
            const total = subtotal + deliveryCost;
            const count = items.reduce((sum, i) => sum + i.quantity, 0);

            document.getElementById('cart-subtotal').textContent = '$' + subtotal.toLocaleString('es-CO');
            document.getElementById('cart-total').textContent = '$' + total.toLocaleString('es-CO');
            document.getElementById('cart-btn-total').textContent = '$' + total.toLocaleString('es-CO');
            document.getElementById('cart-btn-count').textContent = count;
            document.getElementById('cart-count').textContent = count;
            document.getElementById('cart-count').style.display = count > 0 ? 'flex' : 'none';
            document.getElementById('cart-btn').style.display = items.length > 0 ? 'block' : 'none';

            const container = document.getElementById('cart-items');
            if (items.length === 0) {
                container.innerHTML = '<p class="text-sm text-gray-400 text-center py-6">Sin productos aún</p>';
                return;
            }

            container.innerHTML = items.map(item => `
        <div class="flex items-center gap-3">
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-gray-800 truncate">${item.name}</p>
                <p class="text-xs text-orange-500 font-medium">$${(item.price * item.quantity).toLocaleString('es-CO')}</p>
            </div>
            <div class="flex items-center gap-2 bg-gray-100 rounded-xl px-2 py-1">
                <button type="button" onclick="changeQty(${item.product_id}, -1)"
                    class="w-6 h-6 flex items-center justify-center text-red-400 font-bold text-lg leading-none">−</button>
                <span class="text-sm font-semibold w-5 text-center text-gray-800">${item.quantity}</span>
                <button type="button" onclick="changeQty(${item.product_id}, 1)"
                    class="w-6 h-6 flex items-center justify-center text-orange-500 font-bold text-lg leading-none">+</button>
            </div>
        </div>
    `).join('');
        }

        function toggleCart() {
            const panel = document.getElementById('cart-panel');
            panel.classList.toggle('hidden');
            renderCart();
        }

        function toggleBankInfo() {
            const bankInfo = document.getElementById('bank-info');
            const transfer = document.querySelector('input[value="transfer"]');
            const labelCash = document.getElementById('label-cash');
            const labelTransfer = document.getElementById('label-transfer');

            if (transfer && transfer.checked) {
                if (bankInfo) bankInfo.classList.remove('hidden');
                labelTransfer.classList.add('border-2', 'border-orange-400', 'bg-orange-50');
                labelTransfer.classList.remove('border', 'border-gray-200');
                labelCash.classList.remove('border-2', 'border-orange-400', 'bg-orange-50');
                labelCash.classList.add('border', 'border-gray-200');
            } else {
                if (bankInfo) bankInfo.classList.add('hidden');
                labelCash.classList.add('border-2', 'border-orange-400', 'bg-orange-50');
                labelCash.classList.remove('border', 'border-gray-200');
                labelTransfer.classList.remove('border-2', 'border-orange-400', 'bg-orange-50');
                labelTransfer.classList.add('border', 'border-gray-200');
            }
        }

        function filterCategory(catId) {
            const sections = document.querySelectorAll('.category-section');
            const pills = document.querySelectorAll('.category-pill');

            pills.forEach(p => {
                p.classList.remove('active', 'bg-orange-500', 'text-white');
                p.classList.add('bg-white', 'border', 'border-gray-200', 'text-gray-600');
            });

            const activePill = document.querySelector(`[data-cat="${catId}"]`);
            if (activePill) {
                activePill.classList.add('active', 'bg-orange-500', 'text-white');
                activePill.classList.remove('bg-white', 'border', 'border-gray-200', 'text-gray-600');
            }

            sections.forEach(section => {
                if (catId === 'all' || section.dataset.cat === catId) {
                    section.style.display = 'block';
                } else {
                    section.style.display = 'none';
                }
            });
        }

        function submitOrder() {
            if (items.length === 0) {
                alert('Agrega productos al carrito.');
                return;
            }
            if (submitting) return;

            const subtotal = items.reduce((sum, i) => sum + i.price * i.quantity, 0);

            if (minOrder > 0 && subtotal < minOrder) {
                alert(
                    `El pedido mínimo es $${minOrder.toLocaleString('es-CO')}. Tu subtotal es $${subtotal.toLocaleString('es-CO')}.`);
                return;
            }

            const name = document.getElementById('customer-name').value.trim();
            const phone = document.getElementById('customer-phone').value.trim();
            const address = document.getElementById('delivery-address').value.trim();
            const payment = document.querySelector('input[name="payment"]:checked').value;

            if (!name) {
                alert('Ingresa tu nombre.');
                return;
            }
            if (!phone) {
                alert('Ingresa tu teléfono.');
                return;
            }
            if (!address) {
                alert('Ingresa tu dirección.');
                return;
            }

            submitting = true;

            const btn = document.getElementById('submit-btn');
            btn.disabled = true;
            btn.textContent = 'Enviando pedido...';
            btn.classList.add('opacity-75', 'cursor-not-allowed');

            document.getElementById('form-name').value = name;
            document.getElementById('form-phone').value = phone;
            document.getElementById('form-address').value = address;
            document.getElementById('form-payment').value = payment;
            document.getElementById('form-notes').value = document.getElementById('order-notes').value;

            const formItems = document.getElementById('form-items');
            formItems.innerHTML = items.map((item, index) => `
        <input type="hidden" name="items[${index}][product_id]" value="${item.product_id}">
        <input type="hidden" name="items[${index}][quantity]" value="${item.quantity}">
    `).join('');

            document.getElementById('order-form').submit();
        }

        renderCart();

        function selectAddress(value) {
            const textarea = document.getElementById('delivery-address');
            if (value === 'other' || value === '') {
                textarea.value = '';
                textarea.focus();
            } else {
                textarea.value = value;
            }
        }

        function showBankInfo(bankId) {
            document.querySelectorAll('[id^="bank-detail-"]').forEach(el => el.classList.add('hidden'));
            if (bankId) {
                document.getElementById('bank-detail-' + bankId)?.classList.remove('hidden');
            }
        }
    </script>

</body>

</html>
