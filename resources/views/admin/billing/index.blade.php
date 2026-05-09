<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Facturación — Restafull</title>
    <link rel="icon" type="image/png" href="{{ asset('images/icons/icon-72x72.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/icons/icon-192x192.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * {
            -webkit-tap-highlight-color: transparent;
        }
    </style>
</head>

<body class="bg-gray-50 min-h-screen">

    <nav
        class="bg-white border-b border-gray-100 px-4 py-3 flex justify-between items-center sticky top-0 z-20 shadow-sm">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.dashboard') }}"
                class="w-9 h-9 bg-gray-100 hover:bg-gray-200 rounded-xl flex items-center justify-center transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-600" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <div>
                <p class="font-bold text-gray-800 text-sm">Facturación</p>
                <p class="text-xs text-gray-400">Control de pagos</p>
            </div>
        </div>
        <span class="text-xs text-gray-400">{{ now()->format('d/m/Y') }}</span>
    </nav>

    <div class="max-w-5xl mx-auto px-4 py-6 space-y-6">

        @if (session('success'))
            <div id="success-msg"
                class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-sm flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                {{ session('success') }}
            </div>
            <script>
                setTimeout(() => {
                    const m = document.getElementById('success-msg');
                    if (m) {
                        m.style.opacity = '0';
                        setTimeout(() => m.remove(), 500);
                    }
                }, 3000);
            </script>
        @endif

        <!-- Métricas -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
                <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Activos</p>
                <p class="text-3xl font-bold text-green-500">{{ $active->count() }}</p>
                <p class="text-xs text-gray-400 mt-0.5">restaurantes</p>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
                <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Por vencer</p>
                <p class="text-3xl font-bold text-yellow-500">{{ $dueSoon->count() }}</p>
                <p class="text-xs text-gray-400 mt-0.5">en 5 días</p>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
                <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Vencidos</p>
                <p class="text-3xl font-bold text-red-500">{{ $overdue->count() }}</p>
                <p class="text-xs text-gray-400 mt-0.5">desactivados</p>
            </div>
            <div class="bg-orange-50 rounded-2xl border border-orange-100 shadow-sm p-4">
                <p class="text-xs text-orange-400 uppercase tracking-wide mb-1">Cobrado mes</p>
                <p class="text-2xl font-bold text-orange-500">${{ number_format($totalPaidMonth, 0, ',', '.') }}</p>
                <p class="text-xs text-orange-400 mt-0.5">este mes</p>
            </div>
        </div>

        <!-- Ciclos pendientes de cobro -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-5 py-3 border-b border-gray-50 flex justify-between items-center">
                <h3 class="font-semibold text-gray-800 text-sm">Ciclos pendientes de cobro</h3>
                <span class="bg-orange-50 text-orange-500 text-xs px-2.5 py-1 rounded-lg font-medium">
                    ${{ number_format($pendingCycles->sum('total_amount'), 0, ',', '.') }} total
                </span>
            </div>
            @forelse($pendingCycles as $cycle)
                @php $tenant = $tenants->firstWhere('id', $cycle->tenant_id); @endphp
                @if ($tenant)
                    <div class="px-5 py-3 border-b border-gray-50 last:border-0 flex justify-between items-center">
                        <div>
                            <p class="text-sm font-semibold text-gray-800">{{ $tenant->name }}</p>
                            <p class="text-xs text-gray-400">
                                {{ \Carbon\Carbon::parse($cycle->period_start)->format('d/m/Y') }} —
                                {{ \Carbon\Carbon::parse($cycle->period_end)->format('d/m/Y') }} |
                                Plan {{ strtoupper($tenant->plan) }}
                            </p>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="text-right">
                                <p class="text-sm font-bold text-gray-800">
                                    ${{ number_format($cycle->total_amount, 0, ',', '.') }}</p>
                                <p class="text-xs text-gray-400">Vence:
                                    {{ \Carbon\Carbon::parse($cycle->due_date)->format('d/m/Y') }}</p>
                            </div>
                            <a href="{{ route('admin.billing.detail', $tenant->id) }}"
                                class="bg-orange-500 hover:bg-orange-600 text-white px-3 py-1.5 rounded-xl text-xs font-medium transition">
                                Gestionar
                            </a>
                        </div>
                    </div>
                @endif
            @empty
                <div class="px-5 py-8 text-center text-sm text-gray-400">
                    @if (now()->day < 27)
                        Los ciclos pendientes aparecerán a partir del día 27 de cada mes.
                    @else
                        No hay ciclos pendientes de cobro.
                    @endif
                </div>
            @endforelse
        </div>

        <!-- Cobro inmediato -->
        @if ($immediateCollections->count() > 0)
            <div class="bg-red-50 border border-red-200 rounded-2xl overflow-hidden">
                <div class="px-5 py-3 border-b border-red-200 flex justify-between items-center">
                    <h3 class="font-semibold text-red-600 text-sm flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        Cobro inmediato
                    </h3>
                    <span class="text-xs text-red-500 font-medium">
                        ${{ number_format($immediateCollections->sum('total_amount'), 0, ',', '.') }} total
                    </span>
                </div>
                @foreach ($immediateCollections as $cycle)
                    @php $tenant = $tenants->firstWhere('id', $cycle->tenant_id); @endphp
                    @if ($tenant)
                        <div class="px-5 py-3 border-b border-red-100 last:border-0 flex justify-between items-center">
                            <div>
                                <p class="text-sm font-semibold text-red-700">{{ $tenant->name }}</p>
                                <p class="text-xs text-red-400">
                                    {{ \Carbon\Carbon::parse($cycle->period_start)->format('d/m/Y') }} —
                                    {{ \Carbon\Carbon::parse($cycle->period_end)->format('d/m/Y') }} |
                                    Plan {{ strtoupper($tenant->plan) }}
                                </p>
                                <p class="text-xs text-red-400">Venció:
                                    {{ \Carbon\Carbon::parse($cycle->due_date)->format('d/m/Y') }}</p>
                            </div>
                            <div class="flex items-center gap-3">
                                <p class="text-sm font-bold text-red-600">
                                    ${{ number_format($cycle->total_amount, 0, ',', '.') }}</p>
                                <a href="{{ route('admin.billing.detail', $tenant->id) }}"
                                    class="bg-red-500 hover:bg-red-600 text-white px-3 py-1.5 rounded-xl text-xs font-medium transition">
                                    Gestionar
                                </a>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        @endif

        <!-- Todos los restaurantes -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-5 py-3 border-b border-gray-50">
                <div class="flex justify-between items-center mb-3">
                    <h3 class="font-semibold text-gray-800 text-sm">Todos los restaurantes</h3>
                    <span class="text-xs text-gray-400">{{ $totalTenants }} restaurantes</span>
                </div>

                <!-- Filtros -->
                <form method="GET" action="{{ route('admin.billing') }}" class="flex flex-wrap gap-2">
                    <input type="text" name="search" value="{{ $search }}"
                        placeholder="Buscar por nombre o dominio..."
                        class="flex-1 min-w-0 border border-gray-200 rounded-xl px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-orange-400">

                    <select name="plan"
                        class="border border-gray-200 rounded-xl px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-orange-400 bg-white">
                        <option value="">Todos los planes</option>
                        <option value="basic" {{ $filterPlan === 'basic' ? 'selected' : '' }}>Basic</option>
                        <option value="pro" {{ $filterPlan === 'pro' ? 'selected' : '' }}>Pro</option>
                    </select>

                    <select name="status"
                        class="border border-gray-200 rounded-xl px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-orange-400 bg-white">
                        <option value="">Todos los estados</option>
                        <option value="active" {{ $filterStatus === 'active' ? 'selected' : '' }}>Activo</option>
                        <option value="due_soon" {{ $filterStatus === 'due_soon' ? 'selected' : '' }}>Por vencer
                        </option>
                        <option value="overdue" {{ $filterStatus === 'overdue' ? 'selected' : '' }}>Vencido</option>
                    </select>

                    <button type="submit"
                        class="bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded-xl text-xs font-medium transition">
                        Filtrar
                    </button>

                    @if ($search || $filterPlan || $filterStatus)
                        <a href="{{ route('admin.billing') }}"
                            class="bg-gray-100 hover:bg-gray-200 text-gray-600 px-4 py-2 rounded-xl text-xs font-medium transition">
                            Limpiar
                        </a>
                    @endif
                </form>
            </div>

            @forelse($tenantsPage as $tenant)
                <div class="px-5 py-3 border-b border-gray-50 last:border-0 flex justify-between items-center">
                    <div>
                        <div class="flex items-center gap-2">
                            <p class="text-sm font-semibold text-gray-800">{{ $tenant->name }}</p>
                            <span
                                class="text-xs px-2 py-0.5 rounded-lg font-medium
                                {{ $tenant->plan === 'pro' ? 'bg-orange-50 text-orange-500' : 'bg-gray-100 text-gray-500' }}">
                                {{ strtoupper($tenant->plan) }}
                            </span>
                            <span
                                class="text-xs px-2 py-0.5 rounded-lg font-medium
                                {{ $tenant->billing_status === 'active'
                                    ? 'bg-green-50 text-green-600'
                                    : ($tenant->billing_status === 'due_soon'
                                        ? 'bg-yellow-50 text-yellow-600'
                                        : 'bg-red-50 text-red-500') }}">
                                {{ $tenant->billing_status === 'active'
                                    ? 'Activo'
                                    : ($tenant->billing_status === 'due_soon'
                                        ? 'Por vencer'
                                        : 'Vencido') }}
                            </span>
                        </div>
                        <p class="text-xs text-gray-400">{{ $tenant->domain }} —
                            {{ $tenant->commission_percentage }}% comisión</p>
                    </div>
                    <a href="{{ route('admin.billing.detail', $tenant->id) }}"
                        class="bg-gray-100 hover:bg-gray-200 text-gray-600 px-3 py-1.5 rounded-xl text-xs font-medium transition">
                        Ver
                    </a>
                </div>
            @empty
                <div class="px-5 py-8 text-center text-sm text-gray-400">No se encontraron restaurantes.</div>
            @endforelse

            <!-- Paginación -->
            @if ($totalPages > 1)
                <div class="px-5 py-3 border-t border-gray-50 flex justify-center items-center gap-2">
                    @if ($page > 1)
                        <a href="?page={{ $page - 1 }}&search={{ $search }}&plan={{ $filterPlan }}&status={{ $filterStatus }}"
                            class="w-8 h-8 bg-white border border-gray-200 rounded-xl flex items-center justify-center text-gray-600 hover:bg-gray-50 transition text-sm">‹</a>
                    @endif
                    @for ($i = 1; $i <= $totalPages; $i++)
                        <a href="?page={{ $i }}&search={{ $search }}&plan={{ $filterPlan }}&status={{ $filterStatus }}"
                            class="w-8 h-8 rounded-xl flex items-center justify-center text-xs font-medium transition
                            {{ $i == $page ? 'bg-orange-500 text-white' : 'bg-white border border-gray-200 text-gray-600 hover:bg-gray-50' }}">
                            {{ $i }}
                        </a>
                    @endfor
                    @if ($page < $totalPages)
                        <a href="?page={{ $page + 1 }}&search={{ $search }}&plan={{ $filterPlan }}&status={{ $filterStatus }}"
                            class="w-8 h-8 bg-white border border-gray-200 rounded-xl flex items-center justify-center text-gray-600 hover:bg-gray-50 transition text-sm">›</a>
                    @endif
                    <span class="text-xs text-gray-400 ml-2">{{ $totalTenants }} total</span>
                </div>
            @endif
        </div>

    </div>

</body>

</html>
