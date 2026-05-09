<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RestaFull - Reportes</title>
    @laravelPWA
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * {
            -webkit-tap-highlight-color: transparent;
        }
    </style>
</head>

<body class="bg-gray-50 min-h-screen">

    <nav class="bg-white border-b border-gray-100 sticky top-0 z-20 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 py-3 flex justify-between items-center">
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.dashboard') }}"
                    class="w-9 h-9 bg-gray-100 hover:bg-gray-200 rounded-xl flex items-center justify-center transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-600" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <div class="flex items-center gap-2">
                    <div
                        class="bg-orange-500 text-white w-8 h-8 rounded-xl flex items-center justify-center font-bold text-xs">
                        RF</div>
                    <div>
                        <p class="font-bold text-gray-800 text-sm">Reportes globales</p>
                        <p class="text-xs text-gray-400">Todas las comisiones</p>
                    </div>
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

    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-6 sm:py-8 space-y-6">

        <!-- Métricas globales -->
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 sm:gap-4">
            <div class="bg-white rounded-2xl p-4 sm:p-6 border border-gray-100 shadow-sm col-span-2 sm:col-span-1">
                <p class="text-xs text-gray-400 uppercase tracking-wide mb-2">Ventas totales</p>
                <p class="text-3xl font-bold text-gray-800">${{ number_format($globalSales, 0, ',', '.') }}</p>
                <p class="text-xs text-gray-400 mt-1">procesadas en la plataforma</p>
            </div>
            <div class="bg-orange-50 rounded-2xl p-4 sm:p-6 border border-orange-100 shadow-sm">
                <p class="text-xs text-orange-400 uppercase tracking-wide mb-2">Comisión total</p>
                <p class="text-3xl font-bold text-orange-500">${{ number_format($globalCommission, 0, ',', '.') }}</p>
                <p class="text-xs text-orange-400 mt-1">acumulada</p>
            </div>
            <div class="bg-green-50 rounded-2xl p-4 sm:p-6 border border-green-100 shadow-sm">
                <p class="text-xs text-green-400 uppercase tracking-wide mb-2">Comisión este mes</p>
                <p class="text-3xl font-bold text-green-500">${{ number_format($globalMonthCommission, 0, ',', '.') }}
                </p>
                <p class="text-xs text-green-400 mt-1">ciclo actual</p>
            </div>
        </div>

        @if (session('error'))
            <div id="error-msg"
                class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-xl mb-4 text-sm flex items-center gap-2 transition-all duration-500">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                {{ session('error') }}
            </div>
        @endif

        <!-- Desktop: tabla -->
        <div class="hidden sm:block bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-50 flex justify-between items-center">
                <h3 class="font-semibold text-gray-800">Reporte por restaurante</h3>
                <span class="text-xs text-gray-400">{{ count($reportData) }} restaurante(s)</span>
            </div>
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100 bg-gray-50">
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                            Restaurante</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                            Comisión %</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                            Ventas totales</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                            Pedidos</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                            Comisión total</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                            Comisión mes</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                            Detalle</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($reportData as $data)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-10 h-10 rounded-xl bg-orange-100 text-orange-500 flex items-center justify-center font-bold text-sm flex-shrink-0">
                                        {{ strtoupper(substr($data['tenant']->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-800">{{ $data['tenant']->name }}</p>
                                        <p class="text-xs text-gray-400">{{ $data['tenant']->domain }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span
                                    class="bg-orange-50 text-orange-500 px-2.5 py-1 rounded-lg text-xs font-semibold">{{ $data['tenant']->commission_percentage }}%</span>
                            </td>
                            <td class="px-6 py-4 font-semibold text-gray-800">
                                ${{ number_format($data['total_sales'], 0, ',', '.') }}</td>
                            <td class="px-6 py-4">
                                <span
                                    class="bg-blue-50 text-blue-600 px-2.5 py-1 rounded-lg text-xs font-semibold">{{ $data['total_orders'] }}</span>
                            </td>
                            <td class="px-6 py-4 font-semibold text-orange-500">
                                ${{ number_format($data['total_commission'], 0, ',', '.') }}</td>
                            <td class="px-6 py-4 font-semibold text-green-500">
                                ${{ number_format($data['month_commission'], 0, ',', '.') }}</td>
                            <td class="px-6 py-4">
                                <a href="{{ route('admin.reports.detail', $data['tenant']->id) }}"
                                    class="bg-orange-50 hover:bg-orange-100 text-orange-500 px-3 py-1.5 rounded-xl text-xs font-medium transition">
                                    Ver detalle
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-16 text-center text-sm text-gray-400">No hay restaurantes
                                activos.</td>
                        </tr>
                    @endforelse
                </tbody>
                @if (count($reportData) > 0)
                    <tfoot>
                        <tr class="bg-gray-50 border-t border-gray-100">
                            <td colspan="2" class="px-6 py-4 text-sm font-semibold text-gray-700">Total plataforma
                            </td>
                            <td class="px-6 py-4 font-bold text-gray-800">
                                ${{ number_format($globalSales, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 font-bold text-blue-600">
                                {{ collect($reportData)->sum('total_orders') }}</td>
                            <td class="px-6 py-4 font-bold text-orange-500">
                                ${{ number_format($globalCommission, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 font-bold text-green-500">
                                ${{ number_format($globalMonthCommission, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>

        <!-- Mobile: cards -->
        <div class="sm:hidden space-y-3">
            <h3 class="font-semibold text-gray-800 text-sm">Por restaurante</h3>
            @forelse($reportData as $data)
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
                    <div class="flex items-center gap-3 mb-3">
                        <div
                            class="w-11 h-11 rounded-xl bg-orange-100 text-orange-500 flex items-center justify-center font-bold text-sm">
                            {{ strtoupper(substr($data['tenant']->name, 0, 2)) }}
                        </div>
                        <div>
                            <p class="font-semibold text-gray-800">{{ $data['tenant']->name }}</p>
                            <p class="text-xs text-gray-400">{{ $data['tenant']->domain }}</p>
                        </div>
                        <span
                            class="ml-auto bg-orange-50 text-orange-500 px-2.5 py-1 rounded-lg text-xs font-semibold">{{ $data['tenant']->commission_percentage }}%</span>
                    </div>
                    <div class="grid grid-cols-3 gap-2">
                        <div class="bg-gray-50 rounded-xl p-3 text-center">
                            <p class="text-xs text-gray-400 mb-1">Ventas</p>
                            <p class="text-sm font-bold text-gray-800">
                                ${{ number_format($data['total_sales'], 0, ',', '.') }}</p>
                        </div>
                        <div class="bg-orange-50 rounded-xl p-3 text-center">
                            <p class="text-xs text-orange-400 mb-1">Comisión</p>
                            <p class="text-sm font-bold text-orange-500">
                                ${{ number_format($data['total_commission'], 0, ',', '.') }}</p>
                        </div>
                        <div class="bg-green-50 rounded-xl p-3 text-center">
                            <p class="text-xs text-green-400 mb-1">Este mes</p>
                            <p class="text-sm font-bold text-green-500">
                                ${{ number_format($data['month_commission'], 0, ',', '.') }}</p>
                        </div>
                    </div>
                    <div class="mt-2 flex justify-between items-center">
                        <span class="text-xs text-gray-400">{{ $data['total_orders'] }} pedido(s)</span>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8 text-center">
                    <p class="text-sm text-gray-400">No hay restaurantes activos.</p>
                </div>
            @endforelse

            <a href="{{ route('admin.reports.detail', $data['tenant']->id) }}"
                class="block w-full text-center bg-orange-50 hover:bg-orange-100 text-orange-500 py-2 rounded-xl text-xs font-medium transition mt-3">
                Ver reporte detallado
            </a>
        </div>

    </div>

    <script>
        setTimeout(() => {
            const msg = document.getElementById('error-msg');
            if (msg) {
                msg.style.opacity = '0';
                setTimeout(() => msg.remove(), 500);
            }
        }, 3000);
    </script>

</body>

</html>
