<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Restaurante</title>
    @laravelPWA
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>* { -webkit-tap-highlight-color: transparent; }</style>
</head>
<body class="bg-gray-50 min-h-screen">

    <nav class="bg-white border-b border-gray-100 sticky top-0 z-20 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 py-3 flex justify-between items-center">
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.dashboard') }}" class="w-9 h-9 bg-gray-100 hover:bg-gray-200 rounded-xl flex items-center justify-center transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                </a>
                <div class="flex items-center gap-2">
                    <div class="w-9 h-9 bg-orange-100 text-orange-500 rounded-xl flex items-center justify-center font-bold text-sm">
                        {{ strtoupper(substr($tenant->name, 0, 2)) }}
                    </div>
                    <div>
                        <p class="font-bold text-gray-800 text-sm">Editar restaurante</p>
                        <p class="text-xs text-gray-400">{{ $tenant->name }}</p>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-2xl mx-auto px-4 sm:px-6 py-8">

        @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-xl mb-6 text-sm">
                <ul class="space-y-1">
                    @foreach($errors->all() as $error)
                        <li>• {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 sm:p-8">

            <div class="mb-6">
                <h2 class="text-xl font-bold text-gray-800">Datos del restaurante</h2>
                <p class="text-sm text-gray-400 mt-1">El dominio no se puede cambiar.</p>
            </div>

            <form method="POST" action="{{ route('admin.tenants.update', $tenant->id) }}" class="space-y-5">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Nombre del restaurante</label>
                    <input type="text" name="name" value="{{ old('name', $tenant->name) }}"
                        class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400"
                        placeholder="Ej: Pizza Hoot">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Email del administrador</label>
                    <input type="email" name="email" value="{{ old('email', $tenant->email) }}"
                        class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400"
                        placeholder="admin@pizzahoot.com">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Dominio</label>
                    <input type="text" value="{{ $tenant->domain }}" disabled
                        class="w-full border border-gray-100 bg-gray-50 rounded-xl px-4 py-3 text-sm text-gray-400 cursor-not-allowed">
                    <p class="text-xs text-gray-400 mt-1">El dominio no se puede modificar.</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Comisión (%)</label>
                    <div class="relative">
                        <input type="number" name="commission_percentage" value="{{ old('commission_percentage', $tenant->commission_percentage) }}"
                            step="0.01" min="0" max="100"
                            class="w-full border border-gray-200 rounded-xl px-4 py-3 pr-10 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400">
                        <span class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm">%</span>
                    </div>
                </div>

                <button type="submit"
                    class="w-full bg-orange-500 hover:bg-orange-600 text-white py-3.5 rounded-2xl font-semibold text-sm transition shadow-sm">
                    Guardar cambios
                </button>
            </form>
        </div>
    </div>

</body>
</html>
