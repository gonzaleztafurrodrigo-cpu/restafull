<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restafull — Software para administración de restaurantes</title>
    <link rel="icon" type="image/png" href="{{ asset('images/icons/icon-72x72.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/icons/icon-192x192.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * {
            -webkit-tap-highlight-color: transparent;
        }

        html {
            scroll-behavior: smooth;
        }
    </style>
</head>

<body class="bg-white text-gray-800">

    <!-- Navbar -->
    <nav class="bg-white border-b border-gray-100 sticky top-0 z-50 shadow-sm">
        <div class="max-w-6xl mx-auto px-6 py-4 flex justify-between items-center">
            <div class="flex items-center gap-2">
                <div class="w-9 h-9 bg-orange-500 rounded-xl flex items-center justify-center">
                    <span class="text-white font-bold text-sm">RF</span>
                </div>
                <span class="font-bold text-gray-800 text-lg">Restafull</span>
            </div>
            <div class="hidden md:flex items-center gap-8">
                <a href="#funciones" class="text-sm text-gray-500 hover:text-gray-800 transition">Funciones</a>
                <a href="#como-funciona" class="text-sm text-gray-500 hover:text-gray-800 transition">Cómo funciona</a>
                <a href="#precios" class="text-sm text-gray-500 hover:text-gray-800 transition">Precios</a>
            </div>
            <a href="{{ route('login') }}"
                class="bg-orange-500 hover:bg-orange-600 text-white px-5 py-2.5 rounded-xl text-sm font-semibold transition shadow-sm">
                Iniciar sesión
            </a>
        </div>
    </nav>

    <!-- Hero -->
    <section class="bg-gradient-to-br from-orange-50 to-white py-20 px-6">
        <div class="max-w-4xl mx-auto text-center">
            <span class="bg-orange-100 text-orange-500 text-xs font-semibold px-3 py-1.5 rounded-full">
                🚀 Software para restaurantes en la nube
            </span>
            <h1 class="text-4xl sm:text-5xl font-bold text-gray-800 mt-6 mb-5 leading-tight">
                El sistema que tu restaurante<br>
                <span class="text-orange-500">necesitaba</span>
            </h1>
            <p class="text-lg text-gray-500 mb-8 max-w-2xl mx-auto">
                Gestiona pedidos, domicilios, mesas, caja y más desde cualquier dispositivo. Rápido, sencillo y sin
                complicaciones.
            </p>
            <div class="flex flex-col sm:flex-row gap-3 justify-center">
                <a href="{{ route('login') }}"
                    class="bg-orange-500 hover:bg-orange-600 text-white px-8 py-4 rounded-2xl font-semibold text-sm transition shadow-md">
                    Empezar ahora
                </a>
                <a href="#como-funciona"
                    class="bg-white border border-gray-200 text-gray-700 px-8 py-4 rounded-2xl font-semibold text-sm transition hover:bg-gray-50">
                    Ver cómo funciona
                </a>
            </div>
        </div>
    </section>

    <!-- Stats -->
    <section class="py-12 px-6 border-b border-gray-100">
        <div class="max-w-4xl mx-auto grid grid-cols-2 sm:grid-cols-4 gap-6 text-center">
            <div>
                <p class="text-3xl font-bold text-orange-500">100%</p>
                <p class="text-sm text-gray-400 mt-1">En la nube</p>
            </div>
            <div>
                <p class="text-3xl font-bold text-orange-500">PWA</p>
                <p class="text-sm text-gray-400 mt-1">Funciona como app</p>
            </div>
            <div>
                <p class="text-3xl font-bold text-orange-500">∞</p>
                <p class="text-sm text-gray-400 mt-1">Restaurantes</p>
            </div>
            <div>
                <p class="text-3xl font-bold text-orange-500">24/7</p>
                <p class="text-sm text-gray-400 mt-1">Disponible</p>
            </div>
        </div>
    </section>

    <!-- Funciones -->
    <section id="funciones" class="py-20 px-6">
        <div class="max-w-5xl mx-auto">
            <div class="text-center mb-14">
                <h2 class="text-3xl font-bold text-gray-800 mb-3">Todo lo que necesitas</h2>
                <p class="text-gray-400">Una plataforma completa para gestionar tu restaurante</p>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">

                <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm hover:shadow-md transition">
                    <div class="w-12 h-12 bg-blue-50 rounded-2xl flex items-center justify-center mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-blue-500" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
                        </svg>
                    </div>
                    <h3 class="font-bold text-gray-800 mb-2">Domicilios</h3>
                    <p class="text-sm text-gray-400">Gestiona pedidos a domicilio con seguimiento en tiempo real para el
                        cliente y el domiciliario.</p>
                </div>

                <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm hover:shadow-md transition">
                    <div class="w-12 h-12 bg-orange-50 rounded-2xl flex items-center justify-center mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-orange-500" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                        </svg>
                    </div>
                    <h3 class="font-bold text-gray-800 mb-2">Mesas y meseros</h3>
                    <p class="text-sm text-gray-400">Control de mesas en tiempo real. Meseros toman pedidos desde su
                        celular sin errores.</p>
                </div>

                <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm hover:shadow-md transition">
                    <div class="w-12 h-12 bg-green-50 rounded-2xl flex items-center justify-center mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-green-500" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 11h.01M12 11h.01M15 11h.01M4 19h16a2 2 0 002-2V7a2 2 0 00-2-2H4a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <h3 class="font-bold text-gray-800 mb-2">Caja y turnos</h3>
                    <p class="text-sm text-gray-400">Apertura y cierre de turno, arqueo de caja, reporte detallado de
                        ventas por cajero.</p>
                </div>

                <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm hover:shadow-md transition">
                    <div class="w-12 h-12 bg-purple-50 rounded-2xl flex items-center justify-center mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-purple-500" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                    <h3 class="font-bold text-gray-800 mb-2">Reportes</h3>
                    <p class="text-sm text-gray-400">Reportes de ventas, productos más vendidos, comisiones y análisis
                        detallado por período.</p>
                </div>

                <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm hover:shadow-md transition">
                    <div class="w-12 h-12 bg-yellow-50 rounded-2xl flex items-center justify-center mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-yellow-500" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <h3 class="font-bold text-gray-800 mb-2">PWA — Funciona como app</h3>
                    <p class="text-sm text-gray-400">Instálalo en cualquier celular como una app nativa. Sin descargas
                        en Play Store o App Store.</p>
                </div>

                <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm hover:shadow-md transition">
                    <div class="w-12 h-12 bg-red-50 rounded-2xl flex items-center justify-center mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-red-500" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <h3 class="font-bold text-gray-800 mb-2">Multi-rol</h3>
                    <p class="text-sm text-gray-400">Admin, cajero, mesero y domiciliario con vistas y permisos
                        específicos para cada rol.</p>
                </div>

            </div>
        </div>
    </section>

    <!-- Cómo funciona -->
    <section id="como-funciona" class="py-20 px-6 bg-gray-50">
        <div class="max-w-4xl mx-auto">
            <div class="text-center mb-14">
                <h2 class="text-3xl font-bold text-gray-800 mb-3">Cómo funciona</h2>
                <p class="text-gray-400">En 3 simples pasos tu restaurante está listo</p>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-8">
                <div class="text-center">
                    <div
                        class="w-14 h-14 bg-orange-500 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-md">
                        <span class="text-white font-bold text-xl">1</span>
                    </div>
                    <h3 class="font-bold text-gray-800 mb-2">Registra tu restaurante</h3>
                    <p class="text-sm text-gray-400">Crea tu cuenta, configura tu menú, mesas y personal en minutos.
                    </p>
                </div>
                <div class="text-center">
                    <div
                        class="w-14 h-14 bg-orange-500 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-md">
                        <span class="text-white font-bold text-xl">2</span>
                    </div>
                    <h3 class="font-bold text-gray-800 mb-2">Comparte el enlace</h3>
                    <p class="text-sm text-gray-400">Tus clientes acceden al menú digital desde su celular y hacen
                        pedidos.</p>
                </div>
                <div class="text-center">
                    <div
                        class="w-14 h-14 bg-orange-500 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-md">
                        <span class="text-white font-bold text-xl">3</span>
                    </div>
                    <h3 class="font-bold text-gray-800 mb-2">Gestiona en tiempo real</h3>
                    <p class="text-sm text-gray-400">Recibe pedidos, gestiona la caja y controla todo desde un solo
                        lugar.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Precios -->
    <!-- Precios -->
    <section id="precios" class="py-20 px-6">
        <div class="max-w-5xl mx-auto">
            <div class="text-center mb-14">
                <h2 class="text-3xl font-bold text-gray-800 mb-3">Planes simples</h2>
                <p class="text-gray-400">Sin costos ocultos. Elige el plan que mejor se adapte a tu restaurante.</p>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 max-w-3xl mx-auto">

                <!-- Plan Básico -->
                <div class="bg-white border border-gray-100 rounded-2xl shadow-sm p-8">
                    <div class="mb-6">
                        <span class="bg-gray-100 text-gray-600 text-xs font-semibold px-3 py-1.5 rounded-full">Plan
                            Básico</span>
                        <p class="text-3xl font-bold text-gray-800 mb-1">5%</p>
                        <p class="text-sm text-gray-400 mt-1">por venta procesada</p>
                        <p class="text-xs text-gray-400 mt-1">Sin cuota mensual fija</p>
                    </div>
                    <div class="border-t border-gray-100 pt-6 space-y-3 mb-8">
                        <div class="flex items-center gap-2 text-sm text-gray-600">
                            <span class="text-green-500 font-bold">✓</span> Subdominio incluido
                        </div>
                        <div class="flex items-center gap-2 text-sm text-gray-600">
                            <span class="text-green-500 font-bold">✓</span> Pedidos a domicilio
                        </div>
                        <div class="flex items-center gap-2 text-sm text-gray-600">
                            <span class="text-green-500 font-bold">✓</span> Mesas y meseros
                        </div>
                        <div class="flex items-center gap-2 text-sm text-gray-600">
                            <span class="text-green-500 font-bold">✓</span> Caja y turnos
                        </div>
                        <div class="flex items-center gap-2 text-sm text-gray-600">
                            <span class="text-green-500 font-bold">✓</span> Reportes de ventas
                        </div>
                        <div class="flex items-center gap-2 text-sm text-gray-600">
                            <span class="text-green-500 font-bold">✓</span> PWA — funciona como app
                        </div>
                        <div class="flex items-center gap-2 text-sm text-gray-600">
                            <span class="text-green-500 font-bold">✓</span> Multi-rol
                        </div>
                        <div class="flex items-center gap-2 text-sm text-gray-600">
                            <span class="text-green-500 font-bold">✓</span> Capacitación incluida
                        </div>
                        <div class="flex items-center gap-2 text-sm text-gray-400">
                            <span class="text-red-400 font-bold">✗</span> Dominio personalizado
                        </div>
                        <div class="flex items-center gap-2 text-sm text-gray-400">
                            <span class="text-red-400 font-bold">✗</span> Soporte prioritario
                        </div>
                    </div>
                    <a href="{{ route('login') }}"
                        class="block w-full text-center border-2 border-orange-500 text-orange-500 hover:bg-orange-500 hover:text-white py-3.5 rounded-2xl font-semibold text-sm transition">
                        Comenzar gratis
                    </a>
                </div>

                <!-- Plan Pro -->
                <div class="bg-orange-500 rounded-2xl shadow-lg p-8 relative overflow-hidden">
                    <div class="absolute top-4 right-4">
                        <span
                            class="bg-white text-orange-500 text-xs font-bold px-3 py-1.5 rounded-full">Recomendado</span>
                    </div>
                    <div class="absolute -bottom-8 -right-8 w-40 h-40 bg-orange-400 rounded-full opacity-30"></div>
                    <div class="relative">
                        <div class="mb-6">
                            <span class="bg-orange-400 text-white text-xs font-semibold px-3 py-1.5 rounded-full">Plan
                                Pro</span>
                            <div class="mt-4">
                                <p class="text-3xl font-bold text-white">5%</p>
                                <p class="text-sm text-orange-100 mt-1">por venta procesada</p>
                                <p class="text-sm text-white font-semibold mt-1">+ $15.000 COP/mes</p>
                                <p class="text-xs text-orange-100 mt-0.5">por dominio personalizado</p>
                            </div>
                        </div>
                        <div class="border-t border-orange-400 pt-6 space-y-3 mb-8">
                            <div class="flex items-center gap-2 text-sm text-white">
                                <span class="font-bold">✓</span> Dominio personalizado
                            </div>
                            <div class="flex items-center gap-2 text-sm text-white">
                                <span class="font-bold">✓</span> Pedidos a domicilio
                            </div>
                            <div class="flex items-center gap-2 text-sm text-white">
                                <span class="font-bold">✓</span> Mesas y meseros
                            </div>
                            <div class="flex items-center gap-2 text-sm text-white">
                                <span class="font-bold">✓</span> Caja y turnos
                            </div>
                            <div class="flex items-center gap-2 text-sm text-white">
                                <span class="font-bold">✓</span> Reportes de ventas
                            </div>
                            <div class="flex items-center gap-2 text-sm text-white">
                                <span class="font-bold">✓</span> PWA — funciona como app
                            </div>
                            <div class="flex items-center gap-2 text-sm text-white">
                                <span class="font-bold">✓</span> Multi-rol
                            </div>
                            <div class="flex items-center gap-2 text-sm text-white">
                                <span class="font-bold">✓</span> Capacitación incluida
                            </div>
                            <div class="flex items-center gap-2 text-sm text-white">
                                <span class="font-bold">✓</span> Soporte prioritario
                            </div>
                        </div>
                        <a href="{{ route('login') }}"
                            class="block w-full text-center bg-white text-orange-500 hover:bg-orange-50 py-3.5 rounded-2xl font-semibold text-sm transition shadow-sm">
                            Comenzar ahora
                        </a>
                    </div>
                </div>

            </div>

            <p class="text-center text-xs text-gray-400 mt-8">
                * El cobro es proporcional al mes de registro. Todos los planes incluyen 5 días de gracia.
            </p>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-800 text-gray-400 py-10 px-6">
        <div class="max-w-5xl mx-auto flex flex-col sm:flex-row justify-between items-center gap-4">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-orange-500 rounded-xl flex items-center justify-center">
                    <span class="text-white font-bold text-xs">RF</span>
                </div>
                <span class="font-bold text-white">Restafull</span>
            </div>
            <p class="text-xs">© 2026 Restafull. Todos los derechos reservados.</p>
            <a href="{{ route('login') }}" class="text-sm text-orange-400 hover:text-orange-300 transition">
                Iniciar sesión →
            </a>
        </div>
    </footer>

</body>

</html>
