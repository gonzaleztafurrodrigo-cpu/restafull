<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Tenant\AuthController;
use App\Http\Controllers\Tenant\UserController;
use App\Http\Controllers\Tenant\CategoryController;
use App\Http\Controllers\Tenant\ProductController;
use App\Http\Controllers\Tenant\TableController;
use App\Http\Controllers\Tenant\OrderController;
use App\Http\Controllers\Tenant\ReportController;
use App\Http\Controllers\Tenant\SettingsController;
use App\Http\Controllers\Tenant\CustomerController;
use App\Http\Controllers\Tenant\AdminController;

Route::middleware(['web', 'tenant'])->group(function () {

    Route::get('/', [CustomerController::class, 'welcome'])->name('tenant.welcome');

    // Autenticación
    Route::get('/login', [AuthController::class, 'showLogin'])->name('tenant.login');
    Route::post('/login', [AuthController::class, 'login'])->name('tenant.login.post');
    Route::post('/logout', [AuthController::class, 'logout'])->name('tenant.logout');

    // Rutas públicas del cliente
    Route::get('/', [CustomerController::class, 'welcome'])->name('tenant.welcome');
    Route::get('/menu', [OrderController::class, 'clientMenu'])->name('tenant.menu');
    Route::post('/menu/pedido', [OrderController::class, 'clientStore'])->name('tenant.client.order.store');

    // Auth cliente
    Route::get('/cliente/login', [CustomerController::class, 'showLogin'])->name('tenant.client.login');
    Route::post('/cliente/login', [CustomerController::class, 'login'])->name('tenant.client.login.post');
    Route::get('/cliente/registro', [CustomerController::class, 'showRegister'])->name('tenant.client.register');
    Route::post('/cliente/registro', [CustomerController::class, 'register'])->name('tenant.client.register.post');
    Route::post('/cliente/logout', [CustomerController::class, 'logout'])->name('tenant.client.logout');

    // Área del cliente (requiere auth cliente)
    Route::middleware(['auth.customer'])->group(function () {
        Route::get('/cliente', [CustomerController::class, 'dashboard'])->name('tenant.client.dashboard');
        Route::get('/cliente/pedidos', [CustomerController::class, 'orders'])->name('tenant.client.orders');
        Route::get('/cliente/direcciones', [CustomerController::class, 'addresses'])->name('tenant.client.addresses');
        Route::post('/cliente/direcciones', [CustomerController::class, 'storeAddress'])->name('tenant.client.addresses.store');
        Route::delete('/cliente/direcciones/{id}', [CustomerController::class, 'destroyAddress'])->name('tenant.client.addresses.destroy');
        Route::patch('/cliente/direcciones/{id}/default', [CustomerController::class, 'setDefaultAddress'])->name('tenant.client.addresses.default');
    });

    Route::get('/empleados/instalar', function () {
        $settings = \Illuminate\Support\Facades\DB::table('settings')->pluck('value', 'key')->toArray();
        return view('tenant.empleados.install', compact('settings'));
    })->name('tenant.empleados.install');


    // Rutas protegidas
    Route::middleware(['auth.tenant'])->group(function () {

        // Admin restaurante
        Route::get('/admin', [AdminController::class, 'index'])->name('tenant.admin');

        // Usuarios
        Route::get('/admin/usuarios', [UserController::class, 'index'])->name('tenant.admin.users');
        Route::get('/admin/usuarios/crear', [UserController::class, 'create'])->name('tenant.admin.users.create');
        Route::post('/admin/usuarios', [UserController::class, 'store'])->name('tenant.admin.users.store');
        Route::patch('/admin/usuarios/{id}/toggle', [UserController::class, 'toggle'])->name('tenant.admin.users.toggle');
        Route::delete('/admin/usuarios/{id}', [UserController::class, 'destroy'])->name('tenant.admin.users.destroy');
        Route::get('/admin/usuarios/{id}/editar', [UserController::class, 'edit'])->name('tenant.admin.users.edit');
        Route::put('/admin/usuarios/{id}', [UserController::class, 'update'])->name('tenant.admin.users.update');

        // Menú - Categorías
        Route::get('/admin/menu', [CategoryController::class, 'index'])->name('tenant.admin.menu');
        Route::get('/admin/menu/categorias/crear', [CategoryController::class, 'create'])->name('tenant.admin.categories.create');
        Route::post('/admin/menu/categorias', [CategoryController::class, 'store'])->name('tenant.admin.categories.store');
        Route::patch('/admin/menu/categorias/{id}/toggle', [CategoryController::class, 'toggle'])->name('tenant.admin.categories.toggle');
        Route::delete('/admin/menu/categorias/{id}', [CategoryController::class, 'destroy'])->name('tenant.admin.categories.destroy');
        Route::get('/admin/menu/categorias/{id}/editar', [CategoryController::class, 'edit'])->name('tenant.admin.categories.edit');
        Route::put('/admin/menu/categorias/{id}', [CategoryController::class, 'update'])->name('tenant.admin.categories.update');

        // Menú - Productos
        Route::get('/admin/menu/productos/crear', [ProductController::class, 'create'])->name('tenant.admin.products.create');
        Route::post('/admin/menu/productos', [ProductController::class, 'store'])->name('tenant.admin.products.store');
        Route::patch('/admin/menu/productos/{id}/toggle', [ProductController::class, 'toggle'])->name('tenant.admin.products.toggle');
        Route::delete('/admin/menu/productos/{id}', [ProductController::class, 'destroy'])->name('tenant.admin.products.destroy');
        Route::get('/admin/menu/productos/{id}/editar', [ProductController::class, 'edit'])->name('tenant.admin.products.edit');
        Route::put('/admin/menu/productos/{id}', [ProductController::class, 'update'])->name('tenant.admin.products.update');

        // Mesas
        Route::get('/admin/mesas', [TableController::class, 'index'])->name('tenant.admin.tables');
        Route::get('/admin/mesas/crear', [TableController::class, 'create'])->name('tenant.admin.tables.create');
        Route::post('/admin/mesas', [TableController::class, 'store'])->name('tenant.admin.tables.store');
        Route::patch('/admin/mesas/{id}/toggle', [TableController::class, 'toggle'])->name('tenant.admin.tables.toggle');
        Route::delete('/admin/mesas/{id}', [TableController::class, 'destroy'])->name('tenant.admin.tables.destroy');
        Route::get('/admin/mesas/{id}/editar', [TableController::class, 'edit'])->name('tenant.admin.tables.edit');
        Route::put('/admin/mesas/{id}', [TableController::class, 'update'])->name('tenant.admin.tables.update');

        // Caja
        Route::get('/caja', [OrderController::class, 'cashierIndex'])->name('tenant.caja');
        Route::post('/caja/pedidos/{id}/confirmar', [OrderController::class, 'cashierConfirm'])->name('tenant.caja.confirm');
        Route::post('/caja/pedidos/{id}/listo', [OrderController::class, 'cashierReady'])->name('tenant.caja.ready');
        Route::post('/caja/pedidos/{id}/despachar', [OrderController::class, 'cashierDispatch'])->name('tenant.caja.dispatch');
        Route::post('/caja/pedidos/{id}/cerrar', [OrderController::class, 'cashierClose'])->name('tenant.caja.close');
        Route::post('/caja/pedidos/{id}/recaudar', [OrderController::class, 'cashierCollect'])->name('tenant.caja.collect');
        Route::get('/caja/pedidos/{id}/editar', [OrderController::class, 'cashierEdit'])->name('tenant.caja.edit');
        Route::post('/caja/pedidos/{id}/editar', [OrderController::class, 'cashierUpdate'])->name('tenant.caja.update');
        Route::get('/caja/arqueo', [OrderController::class, 'cashierArqueo'])->name('tenant.caja.arqueo');
        Route::get('/caja/turno', [OrderController::class, 'cashierShift'])->name('tenant.caja.shift');
        Route::post('/caja/turno/abrir', [OrderController::class, 'cashierOpenShift'])->name('tenant.caja.shift.open');
        Route::post('/caja/turno/cerrar', [OrderController::class, 'cashierCloseShift'])->name('tenant.caja.shift.close');
        Route::get('/caja/turno/{id}/reporte', [OrderController::class, 'cashierShiftReport'])->name('tenant.caja.shift.report');
        Route::post('/caja/pedidos/{id}/cancelar', [OrderController::class, 'cashierCancel'])->name('tenant.caja.cancel');

        // Mesero
        Route::get('/mesero', [OrderController::class, 'waiterIndex'])->name('tenant.mesero');
        Route::get('/mesero/pedido', [OrderController::class, 'waiterCreate'])->name('tenant.mesero.create');
        Route::post('/mesero/pedido', [OrderController::class, 'waiterStore'])->name('tenant.mesero.store');
        Route::get('/mesero/pedido/{id}', [OrderController::class, 'waiterOrder'])->name('tenant.mesero.order');
        Route::post('/mesero/pedido/{id}/actualizar', [OrderController::class, 'waiterUpdate'])->name('tenant.mesero.update');

        // Domiciliario
        Route::get('/domiciliario', [OrderController::class, 'deliveryIndex'])->name('tenant.domiciliario');
        Route::post('/domiciliario/pedidos/{id}/entregar', [OrderController::class, 'deliveryComplete'])->name('tenant.domiciliario.complete');

        // Reportes
        Route::get('/admin/reportes', [ReportController::class, 'index'])->name('tenant.admin.reports');

        // Configuración
        Route::get('/admin/configuracion', [SettingsController::class, 'index'])->name('tenant.admin.settings');
        Route::post('/admin/configuracion', [SettingsController::class, 'update'])->name('tenant.admin.settings.update');

        // Cuentas bancarias
        Route::post('/admin/configuracion/bancos', [SettingsController::class, 'storeBankAccount'])->name('tenant.admin.settings.bank.store');
        Route::delete('/admin/configuracion/bancos/{id}', [SettingsController::class, 'destroyBankAccount'])->name('tenant.admin.settings.bank.destroy');
        Route::patch('/admin/configuracion/bancos/{id}/toggle', [SettingsController::class, 'toggleBankAccount'])->name('tenant.admin.settings.bank.toggle');

        Route::post('/caja/pedidos/{id}/verificar-pago', [OrderController::class, 'cashierVerifyPayment'])->name('tenant.caja.verify');

        // Impresión de comanda
        Route::get('/caja/pedidos/{id}/comanda', [OrderController::class, 'printComanda'])->name('tenant.caja.print');
    });

    Route::get('/manifest.json', function () {
        $settings = \Illuminate\Support\Facades\DB::table('settings')->pluck('value', 'key')->toArray();
        $name = $settings['restaurant_name'] ?? 'Mi Restaurante';
        $logo = !empty($settings['logo'])
            ? Storage::url($settings['logo'])
            : '/images/icons/icon-192x192.png';
        $color = $settings['primary_color'] ?? '#f97316';

        return response()->json([
            'name' => $name,
            'short_name' => $name,
            'start_url' => '/',
            'display' => 'standalone',
            'background_color' => '#ffffff',
            'theme_color' => $color,
            'icons' => [
                ['src' => $logo, 'sizes' => '192x192', 'type' => 'image/png'],
                ['src' => $logo, 'sizes' => '512x512', 'type' => 'image/png'],
            ],
        ])->header('Content-Type', 'application/manifest+json');
    })->name('tenant.manifest');

    Route::get('/empleados/manifest.json', function () {
        $settings = \Illuminate\Support\Facades\DB::table('settings')->pluck('value', 'key')->toArray();
        $name = ($settings['restaurant_name'] ?? 'Mi Restaurante') . ' - Empleados';
        $logo = !empty($settings['logo'])
            ? Storage::url($settings['logo'])
            : '/images/icons/icon-192x192.png';
        $color = $settings['primary_color'] ?? '#f97316';

        return response()->json([
            'name' => $name,
            'short_name' => $name,
            'start_url' => '/login',
            'display' => 'standalone',
            'background_color' => '#1f2937',
            'theme_color' => $color,
            'icons' => [
                ['src' => $logo, 'sizes' => '192x192', 'type' => 'image/png'],
                ['src' => $logo, 'sizes' => '512x512', 'type' => 'image/png'],
            ],
        ])->header('Content-Type', 'application/manifest+json');
    })->name('tenant.manifest.empleados');
});
