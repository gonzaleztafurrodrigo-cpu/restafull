<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RestaFull - SuperAdmin</title>
    @laravelPWA
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * {
            -webkit-tap-highlight-color: transparent;
        }
    </style>
</head>

<body class="bg-gray-50 min-h-screen">

    <!-- Navbar -->
    <nav class="bg-white border-b border-gray-100 sticky top-0 z-20 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 py-3 flex justify-between items-center">
            <div class="flex items-center gap-3">
                <div
                    class="bg-orange-500 text-white w-10 h-10 rounded-xl flex items-center justify-center font-bold text-sm">
                    RF</div>
                <div>
                    <p class="font-bold text-gray-800">RestaFull</p>
                    <p class="text-xs text-gray-400">Panel de administración</p>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="flex items-center gap-2 text-sm text-gray-500 hover:text-red-500 transition bg-gray-50 hover:bg-red-50 px-4 py-2 rounded-xl">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h6a2 2 0 012 2v1" />
                    </svg>
                    <span class="hidden sm:block">Cerrar sesión</span>
                </button>
            </form>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-6 sm:py-8">

        @if (session('success'))
            <div id="success-msg"
                class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl mb-6 flex items-center gap-2 text-sm transition-all duration-500">
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

        <!-- Hero -->
        <div
            class="bg-gradient-to-r from-orange-500 to-orange-400 rounded-2xl p-6 sm:p-8 mb-6 text-white relative overflow-hidden">
            <div class="absolute inset-0 opacity-10">
                <div class="absolute -top-4 -right-4 w-48 h-48 bg-white rounded-full"></div>
                <div class="absolute -bottom-8 -left-8 w-56 h-56 bg-white rounded-full"></div>
            </div>
            <div class="relative">
                <p class="text-orange-100 text-sm mb-1">Bienvenido al panel</p>
                <h1 class="text-2xl sm:text-3xl font-bold mb-1">RestaFull Admin</h1>
                <p class="text-orange-100 text-sm">Gestiona todos los restaurantes de la plataforma</p>
            </div>
        </div>

        <!-- Métricas -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4 mb-6">
            <div class="bg-white rounded-2xl p-4 sm:p-5 border border-gray-100 shadow-sm">
                <p class="text-xs text-gray-400 uppercase tracking-wide mb-2">Total</p>
                <p class="text-3xl font-bold text-gray-800">{{ $tenants->count() }}</p>
                <p class="text-xs text-gray-400 mt-1">restaurantes</p>
            </div>
            <div class="bg-white rounded-2xl p-4 sm:p-5 border border-gray-100 shadow-sm">
                <p class="text-xs text-gray-400 uppercase tracking-wide mb-2">Activos</p>
                <p class="text-3xl font-bold text-green-500">{{ $tenants->where('is_active', true)->count() }}</p>
                <p class="text-xs text-gray-400 mt-1">en línea</p>
            </div>
            <div class="bg-white rounded-2xl p-4 sm:p-5 border border-gray-100 shadow-sm">
                <p class="text-xs text-gray-400 uppercase tracking-wide mb-2">Inactivos</p>
                <p class="text-3xl font-bold text-red-400">{{ $tenants->where('is_active', false)->count() }}</p>
                <p class="text-xs text-gray-400 mt-1">suspendidos</p>
            </div>
            <div class="bg-orange-50 rounded-2xl p-4 sm:p-5 border border-orange-100 shadow-sm">
                <p class="text-xs text-orange-400 uppercase tracking-wide mb-2">Comisión</p>
                <p class="text-3xl font-bold text-orange-500">5%</p>
                <p class="text-xs text-orange-400 mt-1">por defecto</p>
            </div>
        </div>

        <!-- Acciones -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 mb-4">
            <h2 class="text-lg font-bold text-gray-800">Restaurantes registrados</h2>
            <div class="flex gap-2 w-full sm:w-auto">
                <a href="{{ route('admin.reports') }}"
                    class="flex-1 sm:flex-none bg-white hover:bg-gray-50 text-gray-700 px-4 py-2.5 rounded-xl text-sm font-medium transition flex items-center justify-center gap-2 shadow-sm border border-gray-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    Reportes
                </a>
                <a href="{{ route('admin.billing') }}"
                    class="flex-1 sm:flex-none bg-white hover:bg-gray-50 text-gray-700 px-4 py-2.5 rounded-xl text-sm font-medium transition flex items-center justify-center gap-2 shadow-sm border border-gray-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Facturación
                </a>
                <a href="{{ route('admin.tenants.create') }}"
                    class="flex-1 sm:flex-none bg-orange-500 hover:bg-orange-600 text-white px-4 py-2.5 rounded-xl text-sm font-medium transition flex items-center justify-center gap-2 shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Nuevo restaurante
                </a>
            </div>
        </div>

        <!-- Lista restaurantes -->
        <!-- Desktop: tabla -->
        <div class="hidden sm:block bg-white rounded-2xl border border-gray-100 shadow-sm overflow-visible">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100 bg-gray-50">
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                            Restaurante</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                            Dominio</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                            Email</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                            Comisión</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                            Estado</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                            Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($tenants as $tenant)
                        <tr class="hover:bg-gray-50 transition relative">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-10 h-10 rounded-xl bg-orange-100 text-orange-500 flex items-center justify-center font-bold text-sm flex-shrink-0">
                                        {{ strtoupper(substr($tenant->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-800">{{ $tenant->name }}</p>
                                        <p class="text-xs text-gray-400">ID: {{ substr($tenant->id, 0, 8) }}...</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <a href="http://{{ $tenant->domain }}:8000" target="_blank"
                                    class="text-blue-500 hover:underline text-sm">{{ $tenant->domain }}</a>
                            </td>
                            <td class="px-6 py-4 text-gray-500">{{ $tenant->email }}</td>
                            <td class="px-6 py-4">
                                <span
                                    class="bg-orange-50 text-orange-500 px-2.5 py-1 rounded-lg text-xs font-semibold">{{ $tenant->commission_percentage }}%</span>
                            </td>
                            <td class="px-6 py-4">
                                @if ($tenant->is_active)
                                    <span
                                        class="bg-green-50 text-green-600 px-2.5 py-1 rounded-lg text-xs font-semibold flex items-center gap-1 w-fit">
                                        <span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span>
                                        Activo
                                    </span>
                                @else
                                    <span
                                        class="bg-red-50 text-red-500 px-2.5 py-1 rounded-lg text-xs font-semibold flex items-center gap-1 w-fit">
                                        <span class="w-1.5 h-1.5 bg-red-500 rounded-full"></span>
                                        Inactivo
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="relative">
                                    <button onclick="toggleMenu('menu-{{ $tenant->id }}')"
                                        class="bg-gray-100 hover:bg-gray-200 text-gray-600 px-3 py-1.5 rounded-xl text-xs font-medium transition flex items-center gap-1.5">
                                        Acciones
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </button>
                                    <div id="menu-{{ $tenant->id }}"
                                        class="hidden absolute right-0 mt-2 w-44 bg-white rounded-2xl shadow-lg border border-gray-100 z-50 overflow-hidden">
                                        <a href="{{ route('admin.tenants.edit', $tenant->id) }}"
                                            class="flex items-center gap-2 px-4 py-2.5 text-xs text-blue-600 hover:bg-blue-50 transition">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                            Editar
                                        </a>
                                        <form method="POST"
                                            action="{{ route('admin.tenants.toggle', $tenant->id) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                class="w-full flex items-center gap-2 px-4 py-2.5 text-xs {{ $tenant->is_active ? 'text-yellow-600 hover:bg-yellow-50' : 'text-green-600 hover:bg-green-50' }} transition">
                                                @if ($tenant->is_active)
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4"
                                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                                    </svg>
                                                    Suspender
                                                @else
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4"
                                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M5 13l4 4L19 7" />
                                                    </svg>
                                                    Activar
                                                @endif
                                            </button>
                                        </form>
                                        <a href="{{ route('admin.reports.detail', $tenant->id) }}"
                                            class="flex items-center gap-2 px-4 py-2.5 text-xs text-orange-500 hover:bg-orange-50 transition">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                            </svg>
                                            Ver reporte
                                        </a>
                                        <div class="border-t border-gray-100"></div>
                                        <button
                                            onclick="openDeleteModal('{{ $tenant->id }}', '{{ $tenant->name }}')"
                                            class="w-full flex items-center gap-2 px-4 py-2.5 text-xs text-red-500 hover:bg-red-50 transition">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                            Eliminar
                                        </button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center gap-3 text-gray-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 text-gray-300"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                    <p class="text-sm">No hay restaurantes registrados.</p>
                                    <a href="{{ route('admin.tenants.create') }}"
                                        class="text-orange-500 text-sm font-medium hover:underline">Crear el
                                        primero</a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile: cards -->
        <div class="sm:hidden space-y-3">
            @forelse($tenants as $tenant)
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-11 h-11 rounded-xl bg-orange-100 text-orange-500 flex items-center justify-center font-bold text-sm">
                                {{ strtoupper(substr($tenant->name, 0, 2)) }}
                            </div>
                            <div>
                                <p class="font-semibold text-gray-800">{{ $tenant->name }}</p>
                                <p class="text-xs text-blue-500">{{ $tenant->domain }}</p>
                            </div>
                        </div>
                        @if ($tenant->is_active)
                            <span
                                class="bg-green-50 text-green-600 px-2.5 py-1 rounded-lg text-xs font-semibold">Activo</span>
                        @else
                            <span
                                class="bg-red-50 text-red-500 px-2.5 py-1 rounded-lg text-xs font-semibold">Inactivo</span>
                        @endif
                    </div>
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs text-gray-400">{{ $tenant->email }}</p>
                            <span
                                class="bg-orange-50 text-orange-500 px-2 py-0.5 rounded-lg text-xs font-semibold mt-1 inline-block">{{ $tenant->commission_percentage }}%
                                comisión</span>
                        </div>
                        <div class="relative mt-3 w-full">
                            <button onclick="toggleMenu('menu-mobile-{{ $tenant->id }}')"
                                class="w-full bg-gray-100 hover:bg-gray-200 text-gray-600 py-2 rounded-xl text-xs font-medium transition flex items-center justify-center gap-1.5">
                                Acciones
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div id="menu-mobile-{{ $tenant->id }}"
                                class="hidden absolute left-0 right-0 mt-2 bg-white rounded-2xl shadow-lg border border-gray-100 z-50 overflow-hidden">
                                <a href="{{ route('admin.tenants.edit', $tenant->id) }}"
                                    class="flex items-center gap-2 px-4 py-2.5 text-xs text-blue-600 hover:bg-blue-50 transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                    Editar
                                </a>
                                <form method="POST" action="{{ route('admin.tenants.toggle', $tenant->id) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit"
                                        class="w-full flex items-center gap-2 px-4 py-2.5 text-xs {{ $tenant->is_active ? 'text-yellow-600 hover:bg-yellow-50' : 'text-green-600 hover:bg-green-50' }} transition">
                                        @if ($tenant->is_active)
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                            </svg>
                                            Suspender
                                        @else
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 13l4 4L19 7" />
                                            </svg>
                                            Activar
                                        @endif
                                    </button>
                                </form>
                                <a href="{{ route('admin.reports.detail', $tenant->id) }}"
                                    class="flex items-center gap-2 px-4 py-2.5 text-xs text-orange-500 hover:bg-orange-50 transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                    </svg>
                                    Ver reporte
                                </a>
                                <div class="border-t border-gray-100"></div>
                                <button onclick="openDeleteModal('{{ $tenant->id }}', '{{ $tenant->name }}')"
                                    class="w-full flex items-center gap-2 px-4 py-2.5 text-xs text-red-500 hover:bg-red-50 transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                    Eliminar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-12 text-center">
                    <p class="text-sm text-gray-400">No hay restaurantes registrados.</p>
                    <a href="{{ route('admin.tenants.create') }}"
                        class="text-orange-500 text-sm font-medium hover:underline mt-2 block">Crear el primero</a>
                </div>
            @endforelse
        </div>

    </div>

    <!-- Modal eliminar restaurante -->
    <div id="modal-delete" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-black bg-opacity-50" onclick="closeDeleteModal()"></div>
        <div
            class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 bg-white rounded-2xl p-6 w-full max-w-md mx-4 shadow-xl">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 bg-red-100 rounded-xl flex items-center justify-center flex-shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-red-500" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </div>
                <div>
                    <h3 class="font-bold text-gray-800">Eliminar restaurante</h3>
                    <p class="text-xs text-gray-400" id="modal-delete-name"></p>
                </div>
            </div>

            <div class="bg-red-50 border border-red-200 rounded-xl p-3 mb-4">
                <p class="text-xs text-red-600">⚠️ Esta acción es irreversible. Se eliminará el restaurante y toda su
                    base de datos incluyendo pedidos, usuarios y configuración.</p>
            </div>

            <form method="POST" id="delete-form" class="space-y-4">
                @csrf
                @method('DELETE')
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Confirma tu contraseña de
                        administrador</label>
                    <input type="password" name="password" id="delete-password"
                        class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-red-400"
                        placeholder="Tu contraseña">
                </div>
                <div class="flex gap-3">
                    <button type="button" onclick="closeDeleteModal()"
                        class="flex-1 border border-gray-200 text-gray-600 py-3 rounded-xl text-sm font-medium hover:bg-gray-50 transition">
                        Cancelar
                    </button>
                    <button type="submit"
                        class="flex-1 bg-red-500 hover:bg-red-600 text-white py-3 rounded-xl text-sm font-medium transition">
                        Eliminar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openDeleteModal(id, name) {
            document.getElementById('modal-delete-name').textContent = name;
            document.getElementById('delete-form').action = '/admin/tenants/' + id;
            document.getElementById('delete-password').value = '';
            document.getElementById('modal-delete').classList.remove('hidden');
        }

        function closeDeleteModal() {
            document.getElementById('modal-delete').classList.add('hidden');
        }
    </script>

    <script>
        function toggleMenu(id) {
            const menu = document.getElementById(id);
            const allMenus = document.querySelectorAll('[id^="menu-"]');
            allMenus.forEach(m => {
                if (m.id !== id) m.classList.add('hidden');
            });
            menu.classList.toggle('hidden');
        }

        document.addEventListener('click', function(e) {
            if (!e.target.closest('[onclick^="toggleMenu"]') && !e.target.closest('[id^="menu-"]')) {
                document.querySelectorAll('[id^="menu-"]').forEach(m => m.classList.add('hidden'));
            }
        });

        function openDeleteModal(id, name) {
            document.getElementById('modal-delete-name').textContent = name;
            document.getElementById('delete-form').action = '/admin/tenants/' + id;
            document.getElementById('delete-password').value = '';
            document.getElementById('modal-delete').classList.remove('hidden');
        }

        function closeDeleteModal() {
            document.getElementById('modal-delete').classList.add('hidden');
        }
    </script>

</body>

</html>
