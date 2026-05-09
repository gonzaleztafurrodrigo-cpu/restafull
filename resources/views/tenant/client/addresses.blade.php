<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Mis direcciones</title>
    @laravelPWA
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>* { -webkit-tap-highlight-color: transparent; }</style>
    @include('tenant.partials.theme')
</head>
<body class="bg-gray-50 min-h-screen pb-8">

    <!-- Header -->
    <div class="bg-white px-4 py-3 flex items-center gap-3 border-b border-gray-100 sticky top-0 z-10">
        <a href="{{ route('tenant.client.dashboard') }}" class="w-9 h-9 bg-gray-100 rounded-xl flex items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
        </a>
        <p class="font-semibold text-gray-800">Mis direcciones</p>
    </div>

    <div class="px-4 py-4 space-y-3">

        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-sm flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                {{ session('success') }}
            </div>
        @endif

        <!-- Lista de direcciones -->
        @forelse($addresses as $address)
            <div class="bg-white rounded-2xl border {{ $address->is_default ? 'border-orange-200' : 'border-gray-100' }} shadow-sm p-4">
                <div class="flex items-start justify-between gap-3">
                    <div class="flex items-start gap-3">
                        <div class="w-9 h-9 {{ $address->is_default ? 'bg-orange-50' : 'bg-gray-50' }} rounded-xl flex items-center justify-center flex-shrink-0 mt-0.5">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 {{ $address->is_default ? 'text-orange-500' : 'text-gray-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <p class="text-sm font-semibold text-gray-800">{{ $address->label }}</p>
                                @if($address->is_default)
                                    <span class="bg-orange-50 text-orange-500 text-xs px-2 py-0.5 rounded-lg font-medium">Predeterminada</span>
                                @endif
                            </div>
                            <p class="text-xs text-gray-500 mt-0.5">{{ $address->address }}</p>
                        </div>
                    </div>
                </div>

                <div class="flex gap-2 mt-3">
                    @if(!$address->is_default)
                        <form method="POST" action="{{ route('tenant.client.addresses.default', $address->id) }}" class="flex-1">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="w-full text-xs text-orange-500 border border-orange-200 py-2 rounded-xl hover:bg-orange-50 transition font-medium">
                                Usar como predeterminada
                            </button>
                        </form>
                    @endif
                    <form method="POST" action="{{ route('tenant.client.addresses.destroy', $address->id) }}"
                        onsubmit="return confirm('¿Eliminar esta dirección?')"
                        class="{{ $address->is_default ? 'flex-1' : '' }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full text-xs text-red-400 border border-red-100 py-2 rounded-xl hover:bg-red-50 transition font-medium px-3">
                            Eliminar
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8 text-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 text-gray-300 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /></svg>
                <p class="text-sm text-gray-400">No tienes direcciones guardadas.</p>
            </div>
        @endforelse

        <!-- Agregar dirección -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <h3 class="font-semibold text-gray-800 text-sm mb-4">Agregar dirección</h3>
            <form method="POST" action="{{ route('tenant.client.addresses.store') }}" class="space-y-3">
                @csrf

                @if($errors->any())
                    <div class="bg-red-50 border border-red-200 text-red-600 px-3 py-2 rounded-xl text-xs">
                        {{ $errors->first() }}
                    </div>
                @endif

                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Etiqueta</label>
                    <div class="flex gap-2 mb-2">
                        @foreach(['Casa', 'Trabajo', 'Otro'] as $label)
                            <button type="button" onclick="setLabel('{{ $label }}')"
                                class="label-btn px-3 py-1.5 rounded-xl text-xs font-medium border border-gray-200 text-gray-600 hover:border-orange-400 hover:text-orange-500 transition">
                                {{ $label }}
                            </button>
                        @endforeach
                    </div>
                    <input type="text" name="label" id="label-input" value="{{ old('label', 'Casa') }}"
                        class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400"
                        placeholder="Ej: Casa, Trabajo...">
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Dirección completa</label>
                    <textarea name="address" rows="2"
                        class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400 resize-none"
                        placeholder="Calle, barrio, referencias...">{{ old('address') }}</textarea>
                </div>

                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_default" value="1" class="accent-orange-500">
                    <span class="text-xs text-gray-600">Usar como predeterminada</span>
                </label>

                <button type="submit"
                    class="w-full bg-orange-500 hover:bg-orange-600 text-white py-3 rounded-2xl font-semibold text-sm transition">
                    Guardar dirección
                </button>
            </form>
        </div>

    </div>

    <script>
        function setLabel(label) {
            document.getElementById('label-input').value = label;
            document.querySelectorAll('.label-btn').forEach(btn => {
                btn.classList.remove('border-orange-400', 'text-orange-500', 'bg-orange-50');
                btn.classList.add('border-gray-200', 'text-gray-600');
            });
            event.target.classList.add('border-orange-400', 'text-orange-500', 'bg-orange-50');
            event.target.classList.remove('border-gray-200', 'text-gray-600');
        }
    </script>

</body>
</html>
