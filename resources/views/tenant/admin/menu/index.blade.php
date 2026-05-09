<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menú</title>
    @laravelPWA
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * {
            -webkit-tap-highlight-color: transparent;
        }
    </style>
    @include('tenant.partials.theme')
</head>

<body class="bg-gray-50 min-h-screen">

    <nav class="bg-white border-b border-gray-100 sticky top-0 z-20 shadow-sm">
        <div class="max-w-6xl mx-auto px-4 py-3 flex justify-between items-center">
            <div class="flex items-center gap-3">
                <a href="{{ route('tenant.admin') }}"
                    class="w-9 h-9 bg-gray-100 rounded-xl flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-600" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <p class="font-bold text-gray-800">Menú</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('tenant.admin.products.create') }}"
                    class="bg-white border border-gray-200 text-gray-700 px-3 py-2 rounded-xl text-xs font-medium hover:bg-gray-50 flex items-center gap-1.5">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Producto
                </a>
                <a href="{{ route('tenant.admin.categories.create') }}"
                    class="bg-orange-500 hover:bg-orange-600 text-white px-3 py-2 rounded-xl text-xs font-medium flex items-center gap-1.5 shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Categoría
                </a>
            </div>
        </div>
    </nav>

    <div class="max-w-6xl mx-auto px-4 py-6">

        @if (session('success'))
            <div id="success-msg"
                class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl mb-4 flex items-center gap-2 text-sm transition-all duration-500">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                {{ session('success') }}
            </div>
            <script>
                setTimeout(() => {
                    const msg = document.getElementById('success-msg');
                    if (msg) {
                        msg.style.opacity = '0';
                        setTimeout(() => msg.remove(), 500);
                    }
                }, 3000);
            </script>
        @endif

        @forelse($categories as $category)
            <div class="mb-8">
                <!-- Header categoría -->
                <div class="flex justify-between items-center mb-3">
                    <div class="flex items-center gap-2">
                        <span class="w-1.5 h-6 bg-orange-500 rounded-full"></span>
                        <div>
                            <h3 class="font-bold text-gray-800">{{ $category->name }}</h3>
                            <p class="text-xs text-gray-400">{{ $category->products_count }} producto(s)</p>
                        </div>
                        @if (!$category->is_active)
                            <span
                                class="bg-red-50 text-red-500 px-2 py-0.5 rounded-lg text-xs font-medium">Inactiva</span>
                        @endif
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('tenant.admin.categories.edit', $category->id) }}"
                            class="bg-blue-50 hover:bg-blue-100 text-blue-600 px-3 py-1.5 rounded-xl text-xs font-medium transition">
                            Editar
                        </a>
                        <form method="POST" action="{{ route('tenant.admin.categories.toggle', $category->id) }}"
                            class="inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit"
                                class="{{ $category->is_active ? 'bg-yellow-50 hover:bg-yellow-100 text-yellow-600' : 'bg-green-50 hover:bg-green-100 text-green-600' }} px-3 py-1.5 rounded-xl text-xs font-medium transition">
                                {{ $category->is_active ? 'Pausar' : 'Activar' }}
                            </button>
                        </form>
                        <form method="POST" action="{{ route('tenant.admin.categories.destroy', $category->id) }}"
                            class="inline" onsubmit="return confirm('¿Eliminar esta categoría y todos sus productos?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="bg-red-50 hover:bg-red-100 text-red-500 px-3 py-1.5 rounded-xl text-xs font-medium transition">
                                Eliminar
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Grid productos -->
                @php
                    $products = \Illuminate\Support\Facades\DB::table('products')
                        ->where('category_id', $category->id)
                        ->orderBy('order')
                        ->get();
                @endphp

                @if ($products->isEmpty())
                    <div class="bg-white rounded-2xl border border-dashed border-gray-200 p-8 text-center">
                        <p class="text-sm text-gray-400 mb-2">Sin productos en esta categoría</p>
                        <a href="{{ route('tenant.admin.products.create') }}"
                            class="text-orange-500 text-xs font-medium hover:underline">+ Agregar producto</a>
                    </div>
                @else
                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
                        @foreach ($products as $product)
                            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden group">
                                <!-- Imagen -->
                                <div class="relative">
                                    @if ($product->image)
                                        <img src="{{ Storage::url($product->image) }}"
                                            class="w-full h-32 object-cover">
                                    @else
                                        <div class="w-full h-32 bg-orange-50 flex items-center justify-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 text-orange-200"
                                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                    @endif
                                    @if (!$product->is_active)
                                        <div
                                            class="absolute inset-0 bg-black bg-opacity-40 flex items-center justify-center">
                                            <span
                                                class="bg-red-500 text-white text-xs font-medium px-2 py-1 rounded-lg">Inactivo</span>
                                        </div>
                                    @endif
                                </div>

                                <!-- Info -->
                                <div class="p-3">
                                    <p class="text-sm font-semibold text-gray-800 leading-tight truncate">
                                        {{ $product->name }}</p>
                                    @if ($product->description)
                                        <p class="text-xs text-gray-400 mt-0.5 truncate">{{ $product->description }}
                                        </p>
                                    @endif
                                    <p class="text-orange-500 font-bold text-sm mt-1">
                                        ${{ number_format($product->price, 0, ',', '.') }}</p>
                                </div>

                                <!-- Acciones -->
                                <div class="px-3 pb-3 flex gap-1.5">
                                    <a href="{{ route('tenant.admin.products.edit', $product->id) }}"
                                        class="flex-1 text-center bg-blue-50 hover:bg-blue-100 text-blue-600 py-1.5 rounded-xl text-xs font-medium transition">
                                        Editar
                                    </a>
                                    <form method="POST"
                                        action="{{ route('tenant.admin.products.toggle', $product->id) }}"
                                        class="flex-1">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                            class="w-full {{ $product->is_active ? 'bg-yellow-50 hover:bg-yellow-100 text-yellow-600' : 'bg-green-50 hover:bg-green-100 text-green-600' }} py-1.5 rounded-xl text-xs font-medium transition">
                                            {{ $product->is_active ? 'Pausar' : 'Activar' }}
                                        </button>
                                    </form>
                                    <form method="POST"
                                        action="{{ route('tenant.admin.products.destroy', $product->id) }}"
                                        onsubmit="return confirm('¿Eliminar este producto?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="w-8 bg-red-50 hover:bg-red-100 text-red-500 py-1.5 rounded-xl text-xs font-medium transition flex items-center justify-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5"
                                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach

                        <!-- Tarjeta agregar producto -->
                        <a href="{{ route('tenant.admin.products.create') }}"
                            class="bg-white rounded-2xl border-2 border-dashed border-gray-200 hover:border-orange-300 p-4 flex flex-col items-center justify-center gap-2 transition group min-h-48">
                            <div
                                class="w-10 h-10 bg-orange-50 group-hover:bg-orange-100 rounded-xl flex items-center justify-center transition">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-orange-500"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v16m8-8H4" />
                                </svg>
                            </div>
                            <p class="text-xs text-gray-400 group-hover:text-orange-500 transition text-center">Agregar
                                producto</p>
                        </a>
                    </div>
                @endif
            </div>
        @empty
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-16 text-center">
                <div class="flex flex-col items-center gap-3 text-gray-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 text-gray-300" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                    <p class="text-sm">No hay categorías creadas aún.</p>
                    <a href="{{ route('tenant.admin.categories.create') }}"
                        class="text-orange-500 text-sm hover:underline font-medium">Crear primera categoría</a>
                </div>
            </div>
        @endforelse
    </div>

</body>

</html>
