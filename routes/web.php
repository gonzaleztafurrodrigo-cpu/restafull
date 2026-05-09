<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\SuperAdminController;

/*
|--------------------------------------------------------------------------
| Rutas del panel central (superadmin)
|--------------------------------------------------------------------------
*/

Route::middleware(['web'])->group(function () {
    Route::get('/', function () {
        return view('welcome');
    });
});


/*
|--------------------------------------------------------------------------
| Rutas de autenticación central
|--------------------------------------------------------------------------
*/

foreach (config('jetstream.middleware', ['web']) as $middleware) {
    Route::middleware($middleware)->group(function () {
        Route::get('/dashboard', function () {
            return redirect()->route('admin.dashboard');
        })->middleware(['auth', 'verified'])->name('dashboard');
    });
}

/*
|--------------------------------------------------------------------------
| Rutas del superadmin
|--------------------------------------------------------------------------
*/

Route::middleware(['web', 'auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [SuperAdminController::class, 'index'])->name('dashboard');

    Route::get('/tenants/create', [SuperAdminController::class, 'create'])->name('tenants.create');
    Route::post('/tenants', [SuperAdminController::class, 'store'])->name('tenants.store');
    Route::patch('/tenants/{id}/toggle', [SuperAdminController::class, 'toggle'])->name('tenants.toggle');
    Route::get('/tenants/{id}/editar', [SuperAdminController::class, 'editTenant'])->name('tenants.edit');
    Route::put('/tenants/{id}', [SuperAdminController::class, 'updateTenant'])->name('tenants.update');
    Route::delete('/tenants/{id}', [SuperAdminController::class, 'destroyTenant'])->name('tenants.destroy');

    Route::get('/reportes', [SuperAdminController::class, 'reports'])->name('reports');
    Route::get('/reportes/{id}', [SuperAdminController::class, 'reportDetail'])->name('reports.detail');

    Route::get('/facturacion', [SuperAdminController::class, 'billing'])->name('billing');
    Route::get('/facturacion/{id}', [SuperAdminController::class, 'billingDetail'])->name('billing.detail');
    Route::post('/facturacion/{id}/pagar', [SuperAdminController::class, 'markAsPaid'])->name('billing.pay');
    Route::post('/facturacion/generar-ciclos', [SuperAdminController::class, 'generateCycles'])->name('billing.generate');
});
