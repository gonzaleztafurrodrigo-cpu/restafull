<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Turno de Caja</title>
    @laravelPWA
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @include('tenant.partials.theme')
    <style>* { -webkit-tap-highlight-color: transparent; }</style>
</head>
<body class="bg-gray-50 min-h-screen">

    <nav class="bg-white border-b border-gray-100 px-4 py-3 flex justify-between items-center sticky top-0 z-10 shadow-sm">
        <div class="flex items-center gap-3">
            <a href="{{ route('tenant.caja') }}" class="w-9 h-9 bg-gray-100 hover:bg-gray-200 rounded-xl flex items-center justify-center transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
            </a>
            <div>
                <p class="font-bold text-gray-800 text-sm">Turno de caja</p>
                <p class="text-xs text-gray-400">{{ session('tenant_user')['name'] }}</p>
            </div>
        </div>
        @if($openShift)
            <span class="flex items-center gap-1.5 bg-green-50 text-green-600 px-3 py-1.5 rounded-xl text-xs font-semibold">
                <span class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></span>
                Turno activo
            </span>
        @else
            <span class="bg-gray-100 text-gray-500 px-3 py-1.5 rounded-xl text-xs font-semibold">Sin turno</span>
        @endif
    </nav>

    <div class="max-w-2xl mx-auto px-4 py-6 space-y-4">

        @if(session('error'))
            <div id="error-msg" class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-xl text-sm flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                {{ session('error') }}
            </div>
            <script>setTimeout(() => { const m = document.getElementById('error-msg'); if(m){m.style.opacity='0';setTimeout(()=>m.remove(),500);} }, 3000);</script>
        @endif

        @if($openShift)
            <!-- Info turno activo -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="bg-green-500 px-5 py-4">
                    <div class="flex items-center gap-2 mb-1">
                        <span class="w-2 h-2 bg-white rounded-full animate-pulse"></span>
                        <span class="font-bold text-white text-sm">Turno en curso</span>
                    </div>
                    <p class="text-green-100 text-xs">Abierto desde {{ \Carbon\Carbon::parse($openShift->opened_at)->format('d/m/Y h:i a') }}</p>
                </div>
                <div class="px-5 py-4 space-y-3">
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-500">Base inicial</span>
                        <span class="font-semibold text-gray-800">${{ number_format($openShift->opening_amount, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-500">Duración</span>
                        <span class="font-semibold text-gray-800">{{ \Carbon\Carbon::parse($openShift->opened_at)->diffForHumans(null, true) }}</span>
                    </div>
                </div>
            </div>

            <!-- Cerrar turno -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                <h3 class="font-bold text-gray-800 mb-1">Cerrar turno</h3>
                <p class="text-xs text-gray-400 mb-4">Cuenta el efectivo en caja antes de cerrar.</p>
                <form method="POST" action="{{ route('tenant.caja.shift.close') }}" class="space-y-4">
                    @csrf
                    <input type="hidden" name="shift_id" value="{{ $openShift->id }}">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Monto en caja al cerrar</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm">$</span>
                            <input type="number" name="closing_amount" required min="0" autofocus
                                class="w-full border border-gray-200 rounded-xl pl-8 pr-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400"
                                placeholder="0">
                        </div>
                        <p class="text-xs text-gray-400 mt-1">Incluye la base inicial más las ventas en efectivo.</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Notas <span class="text-gray-400 font-normal">(opcional)</span></label>
                        <textarea name="notes" rows="2"
                            class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400 resize-none"
                            placeholder="Observaciones del turno..."></textarea>
                    </div>
                    <button type="submit"
                        class="w-full bg-red-500 hover:bg-red-600 text-white py-3.5 rounded-2xl font-semibold text-sm transition shadow-sm">
                        Cerrar turno
                    </button>
                </form>
            </div>

        @else
            <!-- Abrir turno -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="bg-orange-500 px-5 py-4">
                    <p class="font-bold text-white text-sm">Iniciar turno</p>
                    <p class="text-orange-100 text-xs mt-0.5">Ingresa el monto base para dar cambios.</p>
                </div>
                <div class="p-5">
                    <form method="POST" action="{{ route('tenant.caja.shift.open') }}" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Monto base inicial</label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm">$</span>
                                <input type="number" name="opening_amount" required min="0" autofocus
                                    class="w-full border border-gray-200 rounded-xl pl-8 pr-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400"
                                    placeholder="50000">
                            </div>
                            <p class="text-xs text-gray-400 mt-1">El dinero con el que arrancas el turno.</p>
                        </div>
                        <button type="submit"
                            class="w-full bg-orange-500 hover:bg-orange-600 text-white py-3.5 rounded-2xl font-semibold text-sm transition shadow-sm">
                            Abrir turno
                        </button>
                    </form>
                </div>
            </div>
        @endif

        <!-- Turnos recientes -->
        @if($recentShifts->count() > 0)
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-5 py-3 border-b border-gray-50">
                    <h3 class="font-semibold text-gray-800 text-sm">Turnos anteriores</h3>
                </div>
                @foreach($recentShifts as $shift)
                    <div class="px-5 py-3.5 border-b border-gray-50 last:border-0">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-sm font-semibold text-gray-800">
                                    {{ \Carbon\Carbon::parse($shift->opened_at)->format('d/m/Y') }}
                                </p>
                                <p class="text-xs text-gray-400 mt-0.5">
                                    {{ \Carbon\Carbon::parse($shift->opened_at)->format('h:i a') }} —
                                    {{ \Carbon\Carbon::parse($shift->closed_at)->format('h:i a') }}
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-bold text-gray-800">${{ number_format($shift->total_sales, 0, ',', '.') }}</p>
                                <p class="text-xs {{ $shift->difference >= 0 ? 'text-green-500' : 'text-red-500' }} font-medium">
                                    {{ $shift->difference >= 0 ? '+' : '' }}${{ number_format($shift->difference, 0, ',', '.') }}
                                </p>
                            </div>
                        </div>
                        <a href="{{ route('tenant.caja.shift.report', $shift->id) }}"
                            class="inline-flex items-center gap-1 text-xs text-orange-500 hover:underline mt-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                            Ver reporte
                        </a>
                    </div>
                @endforeach
            </div>
        @endif

    </div>

</body>
</html>
