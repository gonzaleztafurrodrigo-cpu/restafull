<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Admin</title>
    @laravelPWA
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * {
            -webkit-tap-highlight-color: transparent;
        }

        ::-webkit-scrollbar {
            width: 4px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }

        ::-webkit-scrollbar-thumb {
            background: #e2e8f0;
            border-radius: 4px;
        }
    </style>
    @include('tenant.partials.theme')
</head>

<body class="bg-gray-50 min-h-screen">

    <!-- Navbar -->
    <nav class="bg-white border-b border-gray-100 sticky top-0 z-20 shadow-sm">
        <div class="max-w-6xl mx-auto px-4 py-3 flex justify-between items-center">
            <div class="flex items-center gap-3">
                @php $settingsAdmin = \Illuminate\Support\Facades\DB::table('settings')->pluck('value', 'key')->toArray(); @endphp
                @if (!empty($settingsAdmin['logo']))
                    <img src="{{ Storage::url($settingsAdmin['logo']) }}" class="w-10 h-10 rounded-xl object-cover">
                @else
                    <div class="w-10 h-10 bg-orange-500 rounded-xl flex items-center justify-center">
                        <span class="text-white font-bold text-sm">RF</span>
                    </div>
                @endif
                <div>
                    <p class="font-bold text-gray-800 text-sm">
                        {{ $settingsAdmin['restaurant_name'] ?? 'Mi Restaurante' }}</p>
                    <p class="text-xs text-gray-400">Panel de administración</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <span class="text-sm text-gray-500 hidden sm:block">{{ session('tenant_user')['name'] }}</span>
                <form method="POST" action="{{ route('tenant.logout') }}">
                    @csrf
                    <button type="submit"
                        class="w-9 h-9 bg-gray-100 hover:bg-red-50 rounded-xl flex items-center justify-center transition text-gray-400 hover:text-red-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h6a2 2 0 012 2v1" />
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <div class="max-w-6xl mx-auto px-4 py-6">

        <!-- Bienvenida -->
        <div
            class="bg-gradient-to-r from-orange-500 to-orange-400 rounded-2xl p-6 mb-6 text-white relative overflow-hidden">
            <div class="absolute inset-0 opacity-10">
                <div class="absolute -top-4 -right-4 w-32 h-32 bg-white rounded-full"></div>
                <div class="absolute -bottom-8 -left-8 w-40 h-40 bg-white rounded-full"></div>
            </div>
            <div class="relative">
                <p class="text-orange-100 text-sm mb-1">Bienvenido</p>
                <h1 class="text-2xl font-bold mb-1">{{ $settingsAdmin['restaurant_name'] ?? 'Mi Restaurante' }}</h1>
                <p class="text-orange-100 text-sm">Gestiona tu restaurante desde aquí</p>
            </div>
        </div>

        <!-- Módulos -->
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-3 gap-4">

            <a href="{{ route('tenant.admin.menu') }}"
                class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 hover:shadow-md transition group active:scale-95">
                <div
                    class="w-12 h-12 bg-orange-50 group-hover:bg-orange-100 rounded-xl flex items-center justify-center mb-4 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-orange-500" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                </div>
                <h3 class="font-semibold text-gray-800 text-sm mb-1">Menú</h3>
                <p class="text-xs text-gray-400">Categorías y productos</p>
            </a>

            <a href="{{ route('tenant.admin.users') }}"
                class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 hover:shadow-md transition group active:scale-95">
                <div
                    class="w-12 h-12 bg-blue-50 group-hover:bg-blue-100 rounded-xl flex items-center justify-center mb-4 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-blue-500" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
                <h3 class="font-semibold text-gray-800 text-sm mb-1">Usuarios</h3>
                <p class="text-xs text-gray-400">Meseros, cajeros y más</p>
            </a>

            <a href="{{ route('tenant.admin.tables') }}"
                class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 hover:shadow-md transition group active:scale-95">
                <div
                    class="w-12 h-12 bg-green-50 group-hover:bg-green-100 rounded-xl flex items-center justify-center mb-4 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-green-500" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                    </svg>
                </div>
                <h3 class="font-semibold text-gray-800 text-sm mb-1">Mesas</h3>
                <p class="text-xs text-gray-400">Gestiona las mesas</p>
            </a>

            <a href="{{ route('tenant.admin.reports') }}"
                class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 hover:shadow-md transition group active:scale-95">
                <div
                    class="w-12 h-12 bg-purple-50 group-hover:bg-purple-100 rounded-xl flex items-center justify-center mb-4 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-purple-500" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </div>
                <h3 class="font-semibold text-gray-800 text-sm mb-1">Reportes</h3>
                <p class="text-xs text-gray-400">Ventas y transacciones</p>
            </a>

            <a href="{{ route('tenant.admin.settings') }}"
                class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 hover:shadow-md transition group active:scale-95">
                <div
                    class="w-12 h-12 bg-yellow-50 group-hover:bg-yellow-100 rounded-xl flex items-center justify-center mb-4 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-yellow-500" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
                <h3 class="font-semibold text-gray-800 text-sm mb-1">Configuración</h3>
                <p class="text-xs text-gray-400">Ajustes del restaurante</p>
            </a>

            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                <div class="flex items-center gap-2 mb-4">
                    <div class="w-8 h-8 bg-orange-50 rounded-xl flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-orange-500" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-800 text-sm">Resumen del día</h3>
                </div>
                <div class="space-y-3">
                    <div class="flex justify-between items-center py-2 border-b border-gray-50">
                        <span class="text-xs text-gray-500">Ventas hoy</span>
                        <span
                            class="text-sm font-bold text-gray-800">${{ number_format($totalSalesToday, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-50">
                        <span class="text-xs text-gray-500">Mesas ocupadas</span>
                        <span class="text-sm font-bold text-purple-500">{{ $occupiedTables }} /
                            {{ $totalTables }}</span>
                    </div>
                </div>
            </div>

        </div>

        <!-- Estado de cuenta -->
        @php
            $tenant = tenancy()->tenant;
            $billingCycle = \Illuminate\Support\Facades\DB::connection('central')
                ->table('billing_cycles')
                ->where('tenant_id', $tenant->id)
                ->orderBy('period_start', 'desc')
                ->first();

            // Ventas y comisión acumulada del mes actual
            $monthSales = \Illuminate\Support\Facades\DB::table('orders')
                ->where('status', 'delivered')
                ->whereMonth('updated_at', now()->month)
                ->whereYear('updated_at', now()->year)
                ->sum('total');

            $commissionRate = $tenant->commission_percentage;
            $monthCommission = $monthSales * ($commissionRate / 100);
            $totalDue = $monthCommission + $tenant->monthly_fee;
        @endphp

        @if ($billingCycle)
            <div class="mt-6">
                <div
                    class="bg-white rounded-2xl border shadow-sm p-5
                    {{ $tenant->billing_status === 'overdue'
                        ? 'border-red-200'
                        : ($tenant->billing_status === 'due_soon'
                            ? 'border-yellow-200'
                            : 'border-gray-100') }}">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h3 class="font-semibold text-gray-800 text-sm">Estado de cuenta</h3>
                            <p class="text-xs text-gray-400 mt-0.5">Plan {{ strtoupper($tenant->plan) }}</p>
                        </div>
                        <span
                            class="text-xs px-2.5 py-1 rounded-lg font-semibold
                            {{ $tenant->billing_status === 'active'
                                ? 'bg-green-50 text-green-600'
                                : ($tenant->billing_status === 'due_soon'
                                    ? 'bg-yellow-50 text-yellow-600'
                                    : 'bg-red-50 text-red-500') }}">
                            {{ $tenant->billing_status === 'active'
                                ? 'Al día'
                                : ($tenant->billing_status === 'due_soon'
                                    ? 'Próximo a vencer'
                                    : 'Vencido') }}
                        </span>
                    </div>

                    @if ($tenant->billing_status === 'overdue')
                        <div class="bg-red-50 border border-red-200 rounded-xl p-3 mb-4">
                            <p class="text-xs text-red-600 font-medium">⚠ Tu cuenta está vencida. Contacta a Restafull
                                para regularizar tu pago y reactivar el servicio.</p>
                        </div>
                    @elseif($tenant->billing_status === 'due_soon')
                        <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-3 mb-4">
                            <p class="text-xs text-yellow-700 font-medium">⏰ Tu cuenta está próxima a vencer. Contacta
                                a Restafull para realizar el pago.</p>
                        </div>
                    @endif

                    <div class="space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Período actual</span>
                            <span class="text-gray-700">
                                {{ \Carbon\Carbon::parse($billingCycle->period_start)->format('d/m/Y') }} —
                                {{ \Carbon\Carbon::parse($billingCycle->period_end)->format('d/m/Y') }}
                            </span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Próximo corte</span>
                            <span
                                class="text-gray-700">{{ \Carbon\Carbon::parse($tenant->next_billing_date)->format('d/m/Y') }}</span>
                        </div>
                        @if ($billingCycle->status === 'paid' && $billingCycle->paid_date)
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">Último pago</span>
                                <span
                                    class="text-green-600 font-medium">{{ \Carbon\Carbon::parse($billingCycle->paid_date)->format('d/m/Y') }}</span>
                            </div>
                        @endif
                        <div class="border-t border-gray-100 pt-2 space-y-2">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">Ventas del mes</span>
                                <span class="text-gray-700">${{ number_format($monthSales, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">Comisión acumulada ({{ $commissionRate }}%)</span>
                                <span
                                    class="text-orange-500 font-medium">${{ number_format($monthCommission, 0, ',', '.') }}</span>
                            </div>
                            @if ($tenant->monthly_fee > 0)
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-500">Cuota mensual fija</span>
                                    <span
                                        class="text-gray-700">${{ number_format($tenant->monthly_fee, 0, ',', '.') }}</span>
                                </div>
                            @endif
                            <div class="flex justify-between items-center border-t border-gray-100 pt-2">
                                <span class="text-sm font-semibold text-gray-700">Total estimado del mes</span>
                                <span
                                    class="text-lg font-bold text-gray-800">${{ number_format($totalDue, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

    </div>

</body>

</html>
