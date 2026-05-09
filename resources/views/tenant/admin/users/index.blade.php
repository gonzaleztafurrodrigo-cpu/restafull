<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuarios</title>
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
                    <p class="font-bold text-gray-800 text-sm">Usuarios</p>
                    <p class="text-xs text-gray-400">Equipo del restaurante</p>
                </div>
            </div>
            <a href="{{ route('tenant.admin.users.create') }}"
                class="bg-orange-500 hover:bg-orange-600 text-white px-3 py-2 rounded-xl text-xs font-medium flex items-center gap-1.5 shadow-sm transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Nuevo usuario
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

        @php
            $roleColors = [
                'admin' => ['bg' => 'bg-purple-50', 'text' => 'text-purple-600', 'icon' => 'bg-purple-100'],
                'cashier' => ['bg' => 'bg-blue-50', 'text' => 'text-blue-600', 'icon' => 'bg-blue-100'],
                'waiter' => ['bg' => 'bg-green-50', 'text' => 'text-green-600', 'icon' => 'bg-green-100'],
                'delivery' => ['bg' => 'bg-yellow-50', 'text' => 'text-yellow-600', 'icon' => 'bg-yellow-100'],
            ];
        @endphp

        @forelse($users as $user)
            @php $colors = $roleColors[$user->role_slug] ?? ['bg' => 'bg-gray-50', 'text' => 'text-gray-600', 'icon' => 'bg-gray-100']; @endphp
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 mb-3 flex items-center gap-4">

                <!-- Avatar -->
                <div class="w-12 h-12 {{ $colors['icon'] }} rounded-2xl flex items-center justify-center flex-shrink-0">
                    <span
                        class="{{ $colors['text'] }} font-bold text-lg">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                </div>

                <!-- Info -->
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <p class="font-semibold text-gray-800 text-sm">{{ $user->name }}</p>
                        <span
                            class="{{ $colors['bg'] }} {{ $colors['text'] }} px-2 py-0.5 rounded-lg text-xs font-medium">
                            {{ $user->role_name }}
                        </span>
                        @if (!$user->is_active)
                            <span
                                class="bg-red-50 text-red-500 px-2 py-0.5 rounded-lg text-xs font-medium">Inactivo</span>
                        @endif
                    </div>
                    <p class="text-xs text-gray-400 mt-0.5 truncate">{{ $user->email }}</p>
                    @if ($user->phone)
                        <p class="text-xs text-gray-400">{{ $user->phone }}</p>
                    @endif
                </div>

                <!-- Acciones -->
                <div class="flex items-center gap-2 flex-shrink-0">
                    <a href="{{ route('tenant.admin.users.edit', $user->id) }}"
                        class="bg-blue-50 hover:bg-blue-100 text-blue-500 w-9 h-9 rounded-xl flex items-center justify-center transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                    </a>
                    <form method="POST" action="{{ route('tenant.admin.users.toggle', $user->id) }}">
                        @csrf
                        @method('PATCH')
                        <button type="submit"
                            class="{{ $user->is_active ? 'bg-yellow-50 hover:bg-yellow-100 text-yellow-600' : 'bg-green-50 hover:bg-green-100 text-green-600' }} w-9 h-9 rounded-xl flex items-center justify-center transition">
                            @if ($user->is_active)
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                </svg>
                            @else
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                            @endif
                        </button>
                    </form>
                    <form method="POST" action="{{ route('tenant.admin.users.destroy', $user->id) }}"
                        onsubmit="return confirm('¿Eliminar este usuario?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="bg-red-50 hover:bg-red-100 text-red-500 w-9 h-9 rounded-xl flex items-center justify-center transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-16 text-center">
                <div class="flex flex-col items-center gap-3 text-gray-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 text-gray-300" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <p class="text-sm">No hay usuarios registrados.</p>
                    <a href="{{ route('tenant.admin.users.create') }}"
                        class="text-orange-500 text-sm font-medium hover:underline">Crear el primero</a>
                </div>
            </div>
        @endforelse
    </div>

</body>

</html>
