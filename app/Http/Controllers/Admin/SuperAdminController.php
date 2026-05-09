<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\Request;

class SuperAdminController extends Controller
{
    public function index()
    {
        $tenants = Tenant::all();
        return view('admin.dashboard', compact('tenants'));
    }

    public function create()
    {
        return view('admin.tenants.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:tenants,email',
            'domain' => 'required|string|unique:tenants,domain',
            'commission_percentage' => 'required|numeric|min:0|max:100',
            'plan' => 'required|in:basic,pro',
        ]);

        $monthlyFee = $request->plan === 'pro' ? 15000 : 0;
        $defaultCommission = $request->plan === 'pro' ? 3 : 5;
        $billingStartDate = now()->toDateString();
        $nextBillingDate = now()->endOfMonth()->toDateString();

        $tenant = Tenant::create([
            'name' => $request->name,
            'email' => $request->email,
            'domain' => $request->domain,
            'commission_percentage' => $request->commission_percentage,
            'is_active' => true,
            'plan' => $request->plan,
            'monthly_fee' => $monthlyFee,
            'billing_status' => 'active',
            'billing_start_date' => $billingStartDate,
            'next_billing_date' => $nextBillingDate,
            'last_paid_date' => null,
        ]);

        $tenant->domains()->create([
            'domain' => $request->domain,
        ]);

        \Illuminate\Support\Facades\DB::table('billing_cycles')->insert([
            'tenant_id' => $tenant->id,
            'period_start' => $billingStartDate,
            'period_end' => $nextBillingDate,
            'sales_amount' => 0,
            'commission_amount' => 0,
            'monthly_fee' => $monthlyFee,
            'total_amount' => $monthlyFee,
            'status' => 'pending',
            'due_date' => $nextBillingDate,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $dbName = 'tenant_' . $tenant->id;

        try {
            \Illuminate\Support\Facades\DB::statement("CREATE DATABASE IF NOT EXISTS `{$dbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

            config(['database.connections.tenant.database' => $dbName]);
            \Illuminate\Support\Facades\DB::purge('tenant');
            \Illuminate\Support\Facades\DB::reconnect('tenant');
            \Illuminate\Support\Facades\DB::setDefaultConnection('tenant');

            \Illuminate\Support\Facades\Artisan::call('migrate', [
                '--path' => 'database/migrations/tenant',
                '--database' => 'tenant',
                '--force' => true,
            ]);

            \Illuminate\Support\Facades\DB::table('settings')->insert([
                ['key' => 'restaurant_name', 'value' => $request->name, 'created_at' => now(), 'updated_at' => now()],
                ['key' => 'restaurant_email', 'value' => $request->email, 'created_at' => now(), 'updated_at' => now()],
                ['key' => 'commission_percentage', 'value' => $request->commission_percentage, 'created_at' => now(), 'updated_at' => now()],
                ['key' => 'delivery_time', 'value' => '30-45', 'created_at' => now(), 'updated_at' => now()],
                ['key' => 'min_order', 'value' => '0', 'created_at' => now(), 'updated_at' => now()],
            ]);

            \Illuminate\Support\Facades\DB::table('roles')->insert([
                ['name' => 'admin', 'display_name' => 'Administrador', 'created_at' => now(), 'updated_at' => now()],
                ['name' => 'cashier', 'display_name' => 'Cajero', 'created_at' => now(), 'updated_at' => now()],
                ['name' => 'waiter', 'display_name' => 'Mesero', 'created_at' => now(), 'updated_at' => now()],
                ['name' => 'delivery', 'display_name' => 'Domiciliario', 'created_at' => now(), 'updated_at' => now()],
            ]);

            $adminRole = \Illuminate\Support\Facades\DB::table('roles')->where('name', 'admin')->first();
            \Illuminate\Support\Facades\DB::table('tenant_users')->insert([
                'name' => 'Administrador',
                'email' => $request->email,
                'password' => \Illuminate\Support\Facades\Hash::make('password123'),
                'role_id' => $adminRole->id,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            \Illuminate\Support\Facades\DB::setDefaultConnection('central');
        } catch (\Exception $e) {
            $tenant->delete();
            return redirect()->route('admin.tenants.create')
                ->with('error', 'Error al crear la base de datos: ' . $e->getMessage());
        }

        return redirect()->route('admin.dashboard')
            ->with('success', 'Restaurante creado. Plan: ' . strtoupper($request->plan) . ' — Usuario admin: ' . $request->email . ' / Contraseña: password123');
    }

    public function toggle($id)
    {
        $tenant = Tenant::findOrFail($id);
        $tenant->update(['is_active' => !$tenant->is_active]);
        return redirect()->route('admin.dashboard')->with('success', 'Estado del restaurante actualizado.');
    }

    public function reports()
    {
        $tenants = Tenant::where('is_active', true)->get();
        $reportData = [];
        $startOfMonth = now()->startOfMonth();

        foreach ($tenants as $tenant) {
            $dbName = 'tenant_' . $tenant->id;
            try {
                config(['database.connections.tenant_' . $tenant->id => [
                    'driver' => 'mysql',
                    'host' => env('DB_HOST', '127.0.0.1'),
                    'port' => env('DB_PORT', '3306'),
                    'database' => $dbName,
                    'username' => env('DB_USERNAME', 'root'),
                    'password' => env('DB_PASSWORD', ''),
                    'charset' => 'utf8mb4',
                    'collation' => 'utf8mb4_unicode_ci',
                    'prefix' => '',
                    'strict' => true,
                    'engine' => null,
                ]]);
                $db = \Illuminate\Support\Facades\DB::connection('tenant_' . $tenant->id);
                $db->getPdo();
            } catch (\Exception $e) {
                continue;
            }

            $totalSales = $db->table('orders')->where('status', 'delivered')->sum('total');
            $totalOrders = $db->table('orders')->where('status', 'delivered')->count();
            $monthSales = $db->table('orders')->where('status', 'delivered')->where('updated_at', '>=', $startOfMonth)->sum('total');
            $commissionRate = $tenant->commission_percentage / 100;
            $totalCommission = $totalSales * $commissionRate;
            $monthCommission = $monthSales * $commissionRate;

            $reportData[] = [
                'tenant' => $tenant,
                'total_sales' => $totalSales,
                'total_orders' => $totalOrders,
                'total_commission' => $totalCommission,
                'month_sales' => $monthSales,
                'month_commission' => $monthCommission,
            ];

            \Illuminate\Support\Facades\DB::purge('tenant_' . $tenant->id);
        }

        $globalCommission = array_sum(array_column($reportData, 'total_commission'));
        $globalMonthCommission = array_sum(array_column($reportData, 'month_commission'));
        $globalSales = array_sum(array_column($reportData, 'total_sales'));

        return view('admin.reports', compact('reportData', 'globalCommission', 'globalMonthCommission', 'globalSales'));
    }

    public function reportDetail(Request $request, $id)
    {
        $tenant = Tenant::findOrFail($id);
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');
        $period = $request->query('period', 'month');
        $page = max(1, (int) $request->query('page', 1));
        $perPage = 5;

        if ($dateFrom && $dateTo) {
            $startDate = \Carbon\Carbon::parse($dateFrom)->startOfDay();
            $endDate = \Carbon\Carbon::parse($dateTo)->endOfDay();
            $period = 'custom';
        } else {
            $startDate = match ($period) {
                'today' => now()->startOfDay(),
                'week' => now()->startOfWeek(),
                'month' => now()->startOfMonth(),
                'all' => now()->subYears(10),
                default => now()->startOfMonth(),
            };
            $endDate = now()->endOfDay();
        }

        $dbName = 'tenant_' . $tenant->id;
        try {
            config(['database.connections.tenant.database' => $dbName]);
            \Illuminate\Support\Facades\DB::purge('tenant');
            \Illuminate\Support\Facades\DB::reconnect('tenant');
            $db = \Illuminate\Support\Facades\DB::connection('tenant');
            $db->getPdo();
        } catch (\Exception $e) {
            return redirect()->route('admin.reports')->with('error', 'Este restaurante no tiene base de datos configurada.');
        }

        $totalSales = $db->table('orders')->where('status', 'delivered')->whereBetween('updated_at', [$startDate, $endDate])->sum('total');
        $totalOrders = $db->table('orders')->where('status', 'delivered')->whereBetween('updated_at', [$startDate, $endDate])->count();
        $tableOrders = $db->table('orders')->where('status', 'delivered')->where('type', 'table')->whereBetween('updated_at', [$startDate, $endDate])->count();
        $deliveryOrders = $db->table('orders')->where('status', 'delivered')->where('type', 'delivery')->whereBetween('updated_at', [$startDate, $endDate])->count();
        $commissionRate = $tenant->commission_percentage / 100;
        $totalCommission = $totalSales * $commissionRate;

        $topProducts = $db->table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.status', 'delivered')
            ->whereBetween('orders.updated_at', [$startDate, $endDate])
            ->select('products.name', $db->raw('SUM(order_items.quantity) as total_qty'), $db->raw('SUM(order_items.subtotal) as total_revenue'))
            ->groupBy('products.id', 'products.name')
            ->orderBy('total_qty', 'desc')
            ->limit(5)
            ->get();

        $ordersTotal = $db->table('orders')->where('status', 'delivered')->whereBetween('updated_at', [$startDate, $endDate])->count();
        $orders = $db->table('orders')->where('status', 'delivered')->whereBetween('updated_at', [$startDate, $endDate])->orderBy('updated_at', 'desc')->skip(($page - 1) * $perPage)->take($perPage)->get();
        $ordersPages = ceil($ordersTotal / $perPage);

        $cancelledTotal = $db->table('orders')->where('status', 'cancelled')->whereBetween('updated_at', [$startDate, $endDate])->count();
        $cancelledOrders = $db->table('orders')->where('status', 'cancelled')->whereBetween('updated_at', [$startDate, $endDate])->orderBy('updated_at', 'desc')->skip(($page - 1) * $perPage)->take($perPage)->get();
        $cancelledPages = ceil($cancelledTotal / $perPage);

        $shiftsTotal = $db->table('cash_registers')->whereBetween('opened_at', [$startDate, $endDate])->count();
        $cashShifts = $db->table('cash_registers')->whereBetween('opened_at', [$startDate, $endDate])->orderBy('opened_at', 'desc')->skip(($page - 1) * $perPage)->take($perPage)->get();
        $shiftsPages = ceil($shiftsTotal / $perPage);

        foreach ($cashShifts as $shift) {
            $shift->cashier = $db->table('tenant_users')->where('id', $shift->user_id)->first();
        }

        return view('admin.reports.detail', compact(
            'tenant',
            'totalSales',
            'totalOrders',
            'tableOrders',
            'deliveryOrders',
            'cancelledOrders',
            'totalCommission',
            'topProducts',
            'orders',
            'cashShifts',
            'period',
            'dateFrom',
            'dateTo',
            'startDate',
            'endDate',
            'page',
            'ordersPages',
            'ordersTotal',
            'cancelledPages',
            'cancelledTotal',
            'shiftsPages',
            'shiftsTotal'
        ));
    }

    public function editTenant($id)
    {
        $tenant = Tenant::findOrFail($id);
        return view('admin.tenants.edit', compact('tenant'));
    }

    public function updateTenant(Request $request, $id)
    {
        $tenant = Tenant::findOrFail($id);
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:tenants,email,' . $id,
            'commission_percentage' => 'required|numeric|min:0|max:100',
        ]);

        $tenant->update([
            'name' => $request->name,
            'email' => $request->email,
            'commission_percentage' => $request->commission_percentage,
        ]);

        $dbName = 'tenant_' . $tenant->id;
        try {
            config(['database.connections.tenant.database' => $dbName]);
            \Illuminate\Support\Facades\DB::purge('tenant');
            \Illuminate\Support\Facades\DB::reconnect('tenant');
            $db = \Illuminate\Support\Facades\DB::connection('tenant');
            $db->getPdo();
            $db->table('settings')->updateOrInsert(['key' => 'restaurant_name'], ['value' => $request->name, 'updated_at' => now()]);
            $db->table('settings')->updateOrInsert(['key' => 'commission_percentage'], ['value' => $request->commission_percentage, 'updated_at' => now()]);
            \Illuminate\Support\Facades\DB::setDefaultConnection('central');
        } catch (\Exception $e) {
        }

        return redirect()->route('admin.dashboard')->with('success', 'Restaurante actualizado correctamente.');
    }

    public function destroyTenant(Request $request, $id)
    {
        $request->validate(['password' => 'required']);
        $user = auth()->user();
        if (!\Illuminate\Support\Facades\Hash::check($request->password, $user->password)) {
            return back()->with('error', 'Contraseña incorrecta. No se eliminó el restaurante.');
        }

        $tenant = Tenant::findOrFail($id);
        $dbName = 'tenant_' . $tenant->id;
        try {
            \Illuminate\Support\Facades\DB::statement("DROP DATABASE IF EXISTS `{$dbName}`");
        } catch (\Exception $e) {
        }

        $tenant->domains()->delete();
        $tenant->delete();
        return redirect()->route('admin.dashboard')->with('success', 'Restaurante eliminado correctamente.');
    }

    public function billing(Request $request)
    {
        $tenants = Tenant::all();

        // Actualizar estados de facturación
        foreach ($tenants as $tenant) {
            if ($tenant->next_billing_date) {
                // Fecha límite de pago = día 5 del mes siguiente al corte
                $paymentDeadline = \Carbon\Carbon::parse($tenant->next_billing_date)
                    ->addMonth()->startOfMonth()->addDays(4); // día 5 del mes siguiente

                $daysUntilDeadline = now()->diffInDays($paymentDeadline, false);
                $daysUntilCut = now()->diffInDays($tenant->next_billing_date, false);

                if ($daysUntilDeadline < 0) {
                    // Pasó el día 5 del mes siguiente sin pagar → vencido
                    $tenant->update(['billing_status' => 'overdue', 'is_active' => false]);
                } elseif ($daysUntilCut <= 5 && $daysUntilCut >= 0) {
                    // Faltan 5 días o menos para el corte → por vencer
                    $tenant->update(['billing_status' => 'due_soon']);
                } elseif ($tenant->billing_status !== 'active') {
                    // Si estaba vencido o por vencer pero ya pagó → restaurar activo
                    if ($tenant->last_paid_date) {
                        $paidAfterCut = \Carbon\Carbon::parse($tenant->last_paid_date)
                            ->gte(\Carbon\Carbon::parse($tenant->next_billing_date)->startOfMonth());
                        if ($paidAfterCut) {
                            $tenant->update(['billing_status' => 'active', 'is_active' => true]);
                        }
                    }
                }
            }
        }

        $dueSoon = Tenant::where('billing_status', 'due_soon')->get();
        $overdue = Tenant::where('billing_status', 'overdue')->get();
        $active = Tenant::where('billing_status', 'active')->where('is_active', true)->get();

        // Ciclos pendientes de cobro — desde día 27
        $showFrom = now()->day >= 27
            ? now()->startOfDay()
            : now()->subMonth()->setDay(27)->startOfDay();

        $pendingCycles = \Illuminate\Support\Facades\DB::table('billing_cycles')
            ->where('status', 'pending')
            ->where('total_amount', '>', 0)
            ->whereMonth('due_date', now()->month)
            ->whereYear('due_date', now()->year)
            ->where('due_date', '>=', $showFrom->toDateString())
            ->orderBy('due_date', 'asc')
            ->get();

        // Cobro inmediato
        $immediateCollections = \Illuminate\Support\Facades\DB::table('billing_cycles')
            ->where('status', 'pending')
            ->where(function ($query) {
                $query->where(function ($q) {
                    $q->whereMonth('due_date', '<', now()->month)
                        ->whereYear('due_date', now()->year);
                })->orWhereYear('due_date', '<', now()->year);
            })
            ->orderBy('due_date', 'asc')
            ->get();

        $totalPending = $pendingCycles->sum('total_amount') + $immediateCollections->sum('total_amount');
        $totalPaidMonth = \Illuminate\Support\Facades\DB::table('billing_cycles')
            ->where('status', 'paid')
            ->whereMonth('paid_date', now()->month)
            ->sum('total_amount');

        // Filtros para todos los restaurantes
        $search = $request->query('search', '');
        $filterPlan = $request->query('plan', '');
        $filterStatus = $request->query('status', '');

        $filteredTenants = $tenants->filter(function ($tenant) use ($search, $filterPlan, $filterStatus) {
            if (
                $search && !str_contains(strtolower($tenant->name), strtolower($search)) &&
                !str_contains(strtolower($tenant->domain), strtolower($search))
            ) {
                return false;
            }
            if ($filterPlan && $tenant->plan !== $filterPlan) return false;
            if ($filterStatus && $tenant->billing_status !== $filterStatus) return false;
            return true;
        });

        // Paginación
        $page = max(1, (int) $request->query('page', 1));
        $perPage = 5;
        $totalTenants = $filteredTenants->count();
        $tenantsPage = $filteredTenants->forPage($page, $perPage);
        $totalPages = ceil($totalTenants / $perPage);

        return view('admin.billing.index', compact(
            'tenantsPage',
            'tenants',
            'dueSoon',
            'overdue',
            'active',
            'pendingCycles',
            'immediateCollections',
            'totalPending',
            'totalPaidMonth',
            'page',
            'totalPages',
            'totalTenants',
            'search',
            'filterPlan',
            'filterStatus'
        ));
    }

    public function billingDetail($id)
    {
        $tenant = Tenant::findOrFail($id);

        $cycles = \Illuminate\Support\Facades\DB::table('billing_cycles')
            ->where('tenant_id', $id)
            ->orderBy('period_start', 'desc')
            ->get();

        $currentSales = 0;
        $currentCommission = 0;
        try {
            $dbName = 'tenant_' . $tenant->id;
            config(['database.connections.tenant.database' => $dbName]);
            \Illuminate\Support\Facades\DB::purge('tenant');
            \Illuminate\Support\Facades\DB::reconnect('tenant');
            $db = \Illuminate\Support\Facades\DB::connection('tenant');
            $db->getPdo();
            $currentSales = $db->table('orders')->where('status', 'delivered')->whereMonth('updated_at', now()->month)->sum('total');
            $currentCommission = $currentSales * ($tenant->commission_percentage / 100);
            \Illuminate\Support\Facades\DB::setDefaultConnection('central');
        } catch (\Exception $e) {
        }

        return view('admin.billing.detail', compact('tenant', 'cycles', 'currentSales', 'currentCommission'));
    }

    public function markAsPaid(Request $request, $id)
    {
        $request->validate([
            'cycle_id' => 'required',
            'notes' => 'nullable|string',
        ]);

        $cycle = \Illuminate\Support\Facades\DB::table('billing_cycles')
            ->where('id', $request->cycle_id)
            ->where('tenant_id', $id)
            ->first();

        if (!$cycle) {
            return back()->with('error', 'Ciclo no encontrado.');
        }

        $tenant = Tenant::findOrFail($id);
        $sales = 0;
        $commission = 0;

        try {
            $dbName = 'tenant_' . $tenant->id;
            config(['database.connections.tenant.database' => $dbName]);
            \Illuminate\Support\Facades\DB::purge('tenant');
            \Illuminate\Support\Facades\DB::reconnect('tenant');
            $db = \Illuminate\Support\Facades\DB::connection('tenant');
            $db->getPdo();
            $sales = $db->table('orders')->where('status', 'delivered')->whereBetween('updated_at', [$cycle->period_start, $cycle->period_end])->sum('total');
            $commission = $sales * ($tenant->commission_percentage / 100);
            \Illuminate\Support\Facades\DB::setDefaultConnection('central');
        } catch (\Exception $e) {
        }

        $totalAmount = $commission + $cycle->monthly_fee;

        \Illuminate\Support\Facades\DB::table('billing_cycles')
            ->where('id', $request->cycle_id)
            ->update([
                'status' => 'paid',
                'sales_amount' => $sales,
                'commission_amount' => $commission,
                'total_amount' => $totalAmount,
                'paid_date' => now()->toDateString(),
                'notes' => $request->notes,
                'updated_at' => now(),
            ]);

        $tenant->update([
            'billing_status' => 'active',
            'is_active' => true,
            'last_paid_date' => now()->toDateString(),
            'next_billing_date' => now()->endOfMonth()->toDateString(),
        ]);

        return redirect()->route('admin.billing.detail', $id)->with('success', 'Pago registrado correctamente.');
    }
}
