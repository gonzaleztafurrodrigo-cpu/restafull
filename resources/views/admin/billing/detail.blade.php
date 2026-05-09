<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Facturación — {{ $tenant->name }}</title>
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
            <a href="{{ route('admin.billing') }}"
                class="w-9 h-9 bg-gray-100 hover:bg-gray-200 rounded-xl flex items-center justify-center transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-600" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <div>
                <p class="font-bold text-gray-800 text-sm">{{ $tenant->name }}</p>
                <p class="text-xs text-gray-400">Detalle de facturación</p>
            </div>
        </div>
        <span
            class="text-xs px-2.5 py-1 rounded-lg font-medium
            {{ $tenant->billing_status === 'active'
                ? 'bg-green-50 text-green-600'
                : ($tenant->billing_status === 'due_soon'
                    ? 'bg-yellow-50 text-yellow-600'
                    : 'bg-red-50 text-red-500') }}">
            {{ $tenant->billing_status === 'active'
                ? 'Al día'
                : ($tenant->billing_status === 'due_soon'
                    ? 'Por vencer'
                    : 'Vencido') }}
        </span>
    </nav>

    <div class="max-w-2xl mx-auto px-4 py-6 space-y-4">

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

        <!-- Info del tenant -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <div class="flex justify-between items-start">
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <p class="font-bold text-gray-800">{{ $tenant->name }}</p>
                        <span
                            class="text-xs px-2 py-0.5 rounded-lg font-medium
                            {{ $tenant->plan === 'pro' ? 'bg-orange-50 text-orange-500' : 'bg-gray-100 text-gray-500' }}">
                            Plan {{ strtoupper($tenant->plan) }}
                        </span>
                    </div>
                    <p class="text-xs text-gray-400">{{ $tenant->domain }}</p>
                    <p class="text-xs text-gray-400">{{ $tenant->commission_percentage }}%
                        comisión{{ $tenant->monthly_fee > 0 ? ' + $' . number_format($tenant->monthly_fee, 0, ',', '.') . '/mes' : '' }}
                    </p>
                </div>
                <div class="text-right">
                    <p class="text-xs text-gray-400">Próximo corte</p>
                    <p class="text-sm font-semibold text-gray-800">
                        {{ $tenant->next_billing_date ? \Carbon\Carbon::parse($tenant->next_billing_date)->format('d/m/Y') : 'N/A' }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Ventas del mes actual -->
        <div class="grid grid-cols-2 gap-3">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
                <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Ventas este mes</p>
                <p class="text-xl font-bold text-gray-800">${{ number_format($currentSales, 0, ',', '.') }}</p>
            </div>
            <div class="bg-orange-50 rounded-2xl border border-orange-100 shadow-sm p-4">
                <p class="text-xs text-orange-400 uppercase tracking-wide mb-1">Comisión este mes</p>
                <p class="text-xl font-bold text-orange-500">${{ number_format($currentCommission, 0, ',', '.') }}</p>
            </div>
        </div>

        <!-- Ciclos de facturación -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-5 py-3 border-b border-gray-50">
                <h3 class="font-semibold text-gray-800 text-sm">Historial de ciclos</h3>
            </div>
            @forelse($cycles as $cycle)
                <div class="px-5 py-4 border-b border-gray-50 last:border-0">
                    <div class="flex justify-between items-start mb-3">
                        <div>
                            <p class="text-sm font-semibold text-gray-800">
                                {{ \Carbon\Carbon::parse($cycle->period_start)->format('d/m/Y') }} —
                                {{ \Carbon\Carbon::parse($cycle->period_end)->format('d/m/Y') }}
                            </p>
                            <span
                                class="text-xs px-2 py-0.5 rounded-lg font-medium mt-1 inline-block
                                {{ $cycle->status === 'paid'
                                    ? 'bg-green-50 text-green-600'
                                    : ($cycle->status === 'pending'
                                        ? 'bg-yellow-50 text-yellow-600'
                                        : 'bg-red-50 text-red-500') }}">
                                {{ $cycle->status === 'paid' ? 'Pagado' : ($cycle->status === 'pending' ? 'Pendiente' : 'Vencido') }}
                            </span>
                        </div>
                        <p class="text-lg font-bold text-gray-800">
                            ${{ number_format($cycle->total_amount, 0, ',', '.') }}</p>
                    </div>

                    <div class="bg-gray-50 rounded-xl p-3 space-y-1.5 text-xs">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Ventas del período</span>
                            <span class="text-gray-700">${{ number_format($cycle->sales_amount, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Comisión ({{ $tenant->commission_percentage }}%)</span>
                            <span
                                class="text-gray-700">${{ number_format($cycle->commission_amount, 0, ',', '.') }}</span>
                        </div>
                        @if ($cycle->monthly_fee > 0)
                            <div class="flex justify-between">
                                <span class="text-gray-500">Cuota mensual</span>
                                <span
                                    class="text-gray-700">${{ number_format($cycle->monthly_fee, 0, ',', '.') }}</span>
                            </div>
                        @endif
                        @if ($cycle->status === 'paid' && $cycle->paid_date)
                            <div class="flex justify-between border-t border-gray-200 pt-1.5">
                                <span class="text-green-600 font-medium">Pagado el</span>
                                <span
                                    class="text-green-600 font-medium">{{ \Carbon\Carbon::parse($cycle->paid_date)->format('d/m/Y') }}</span>
                            </div>
                        @endif
                        @if ($cycle->notes)
                            <p class="text-gray-400 italic pt-1">{{ $cycle->notes }}</p>
                        @endif
                    </div>

                    @php
                        $currentMonthPaid = $cycles->contains(function ($c) {
                            return $c->status === 'paid' &&
                                \Carbon\Carbon::parse($c->period_start)->month === now()->month &&
                                \Carbon\Carbon::parse($c->period_start)->year === now()->year;
                        });
                    @endphp
                    @if ($cycle->status === 'pending' && $loop->first)
                        @php
                            $cycleMonth = \Carbon\Carbon::parse($cycle->period_end)->month;
                            $cycleYear = \Carbon\Carbon::parse($cycle->period_end)->year;
                            $canPay = now()->month > $cycleMonth || now()->year > $cycleYear;
                        @endphp
                        @if ($canPay)
                            <button onclick="openPayModal({{ $cycle->id }}, {{ $cycle->total_amount }})"
                                class="w-full mt-3 bg-green-500 hover:bg-green-600 text-white py-2.5 rounded-xl text-sm font-medium transition">
                                Registrar pago
                            </button>
                        @else
                            <div
                                class="w-full mt-3 bg-gray-100 text-gray-400 py-2.5 rounded-xl text-sm font-medium text-center cursor-not-allowed">
                                Disponible el 1 de
                                {{ ucfirst(\Carbon\Carbon::parse($cycle->period_end)->addMonth()->locale('es')->translatedFormat('F')) }}
                            </div>
                        @endif
                    @endif
                </div>
            @empty
                <div class="px-5 py-8 text-center text-sm text-gray-400">Sin ciclos de facturación.</div>
            @endforelse
        </div>

    </div>

    <!-- Modal pago -->
    <div id="modal-pay" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-black bg-opacity-50" onclick="closePayModal()"></div>
        <div
            class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 bg-white rounded-2xl p-6 w-full max-w-sm shadow-xl">
            <h3 class="font-bold text-gray-800 mb-1">Registrar pago</h3>
            <p class="text-sm text-gray-400 mb-4">Total a cobrar: <span id="pay-amount"
                    class="font-semibold text-gray-800"></span></p>

            <form method="POST" id="pay-form" action="" class="space-y-4">
                @csrf
                <input type="hidden" name="cycle_id" id="pay-cycle-id">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Notas del pago <span
                            class="text-gray-400 font-normal">(opcional)</span></label>
                    <textarea name="notes" rows="2"
                        class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-400 resize-none"
                        placeholder="Nequi, transferencia, efectivo..."></textarea>
                </div>
                <div class="flex gap-3">
                    <button type="button" onclick="closePayModal()"
                        class="flex-1 border border-gray-200 text-gray-600 py-3 rounded-xl text-sm font-medium">
                        Cancelar
                    </button>
                    <button type="submit"
                        class="flex-1 bg-green-500 hover:bg-green-600 text-white py-3 rounded-xl text-sm font-medium transition">
                        Confirmar pago
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openPayModal(cycleId, amount) {
            document.getElementById('pay-cycle-id').value = cycleId;
            document.getElementById('pay-amount').textContent = '$' + amount.toLocaleString('es-CO');
            document.getElementById('pay-form').action = '/admin/facturacion/{{ $tenant->id }}/pagar';
            document.getElementById('modal-pay').classList.remove('hidden');
        }

        function closePayModal() {
            document.getElementById('modal-pay').classList.add('hidden');
        }
    </script>

</body>

</html>
