<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mesas</title>
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
                    <p class="font-bold text-gray-800 text-sm">Mesas</p>
                    <p class="text-xs text-gray-400">{{ $tables->count() }} mesa(s) configuradas</p>
                </div>
            </div>
            <a href="{{ route('tenant.admin.tables.create') }}"
                class="bg-orange-500 hover:bg-orange-600 text-white px-3 py-2 rounded-xl text-xs font-medium flex items-center gap-1.5 shadow-sm transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Nueva mesa
            </a>
        </div>
    </nav>

    <div class="max-w-4xl mx-auto px-4 py-6">

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

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
            @forelse($tables as $table)
                <div
                    class="bg-white rounded-2xl border {{ $table->is_active ? 'border-gray-100' : 'border-red-100' }} shadow-sm overflow-hidden">

                    <!-- Header -->
                    <div class="p-4">
                        <div class="flex justify-between items-start mb-3">
                            <div
                                class="w-10 h-10 {{ $table->is_active ? 'bg-green-50' : 'bg-red-50' }} rounded-xl flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    class="w-5 h-5 {{ $table->is_active ? 'text-green-500' : 'text-red-400' }}"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                </svg>
                            </div>
                            @if (!$table->is_active)
                                <span
                                    class="bg-red-50 text-red-400 text-xs px-2 py-0.5 rounded-lg font-medium">Inactiva</span>
                            @endif
                        </div>
                        <p class="font-bold text-gray-800">{{ $table->name }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">{{ $table->capacity }} personas</p>
                    </div>

                    <!-- Acciones -->
                    <div class="px-3 pb-3 flex gap-1.5">
                        <a href="{{ route('tenant.admin.tables.edit', $table->id) }}"
                            class="flex-1 text-center bg-blue-50 hover:bg-blue-100 text-blue-600 py-1.5 rounded-xl text-xs font-medium transition">
                            Editar
                        </a>
                        <form method="POST" action="{{ route('tenant.admin.tables.toggle', $table->id) }}"
                            class="flex-1">
                            @csrf
                            @method('PATCH')
                            <button type="submit"
                                class="w-full {{ $table->is_active ? 'bg-yellow-50 hover:bg-yellow-100 text-yellow-600' : 'bg-green-50 hover:bg-green-100 text-green-600' }} py-1.5 rounded-xl text-xs font-medium transition">
                                {{ $table->is_active ? 'Desactivar' : 'Activar' }}
                            </button>
                        </form>
                        <form method="POST" action="{{ route('tenant.admin.tables.destroy', $table->id) }}"
                            onsubmit="return confirm('¿Eliminar esta mesa?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="w-9 bg-red-50 hover:bg-red-100 text-red-500 py-1.5 rounded-xl text-xs font-medium transition flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="col-span-4 bg-white rounded-2xl border border-gray-100 shadow-sm p-16 text-center">
                    <div class="flex flex-col items-center gap-3 text-gray-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 text-gray-300" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                        </svg>
                        <p class="text-sm">No hay mesas configuradas.</p>
                        <a href="{{ route('tenant.admin.tables.create') }}"
                            class="text-orange-500 text-sm font-medium hover:underline">Crear primera mesa</a>
                    </div>
                </div>
            @endforelse

            <!-- Tarjeta agregar -->
            <a href="{{ route('tenant.admin.tables.create') }}"
                class="bg-white rounded-2xl border-2 border-dashed border-gray-200 hover:border-orange-300 p-4 flex flex-col items-center justify-center gap-2 transition group min-h-36">
                <div
                    class="w-10 h-10 bg-orange-50 group-hover:bg-orange-100 rounded-xl flex items-center justify-center transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-orange-500" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                </div>
                <p class="text-xs text-gray-400 group-hover:text-orange-500 transition text-center">Nueva mesa</p>
            </a>
        </div>
    </div>

</body>

</html>
