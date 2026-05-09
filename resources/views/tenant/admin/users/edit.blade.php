<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Usuario</title>
    @laravelPWA
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @include('tenant.partials.theme')
</head>
<body class="bg-gray-50 min-h-screen">

    <nav class="bg-white border-b border-gray-100 sticky top-0 z-20 shadow-sm">
        <div class="max-w-2xl mx-auto px-4 py-3 flex justify-between items-center">
            <div class="flex items-center gap-3">
                <a href="{{ route('tenant.admin.users') }}" class="w-9 h-9 bg-gray-100 rounded-xl flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                </a>
                <div>
                    <p class="font-bold text-gray-800 text-sm">Editar usuario</p>
                    <p class="text-xs text-gray-400">{{ $user->name }}</p>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-2xl mx-auto px-4 py-6">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">

            @if($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-xl mb-5 text-sm">
                    <ul class="space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('tenant.admin.users.update', $user->id) }}" class="space-y-5">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Nombre completo</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}"
                        class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400"
                        placeholder="Nombre completo">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Correo electrónico</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}"
                        class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400"
                        placeholder="correo@ejemplo.com">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Rol</label>
                    <select name="role_id"
                        class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400 bg-white">
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}" {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>
                                {{ $role->display_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Teléfono <span class="text-gray-400 font-normal">(opcional)</span></label>
                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                        class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400"
                        placeholder="3001234567">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">
                        Nueva contraseña
                        <span class="text-gray-400 font-normal">(dejar vacío para no cambiar)</span>
                    </label>
                    <input type="password" name="password"
                        class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400"
                        placeholder="Mínimo 6 caracteres">
                </div>

                <button type="submit"
                    class="w-full bg-orange-500 hover:bg-orange-600 text-white py-3 rounded-2xl font-semibold text-sm transition shadow-sm">
                    Guardar cambios
                </button>
            </form>
        </div>
    </div>

</body>
</html>
