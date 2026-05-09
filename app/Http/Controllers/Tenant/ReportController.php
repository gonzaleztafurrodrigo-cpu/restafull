<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->query('period', 'today');
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');

        if ($dateFrom && $dateTo) {
            $startDate = \Carbon\Carbon::parse($dateFrom)->startOfDay();
            $endDate = \Carbon\Carbon::parse($dateTo)->endOfDay();
            $period = 'custom';
        } else {
            $startDate = match ($period) {
                'today' => now()->startOfDay(),
                'week' => now()->startOfWeek(),
                'month' => now()->startOfMonth(),
                default => now()->startOfDay(),
            };
            $endDate = now()->endOfDay();
        }

        $totalSales = DB::table('orders')
            ->where('status', 'delivered')
            ->whereBetween('updated_at', [$startDate, $endDate])
            ->sum('total');

        $totalOrders = DB::table('orders')
            ->where('status', 'delivered')
            ->whereBetween('updated_at', [$startDate, $endDate])
            ->count();

        $tableOrders = DB::table('orders')
            ->where('status', 'delivered')
            ->where('type', 'table')
            ->whereBetween('updated_at', [$startDate, $endDate])
            ->count();

        $deliveryOrders = DB::table('orders')
            ->where('status', 'delivered')
            ->where('type', 'delivery')
            ->whereBetween('updated_at', [$startDate, $endDate])
            ->count();

        $cancelledOrders = DB::table('orders')
            ->where('status', 'cancelled')
            ->whereBetween('updated_at', [$startDate, $endDate])
            ->get();

        $commissionRate = DB::table('settings')->where('key', 'commission_percentage')->value('value') ?? 5;
        $totalCommission = $totalSales * ($commissionRate / 100);

        $avgTicket = $totalOrders > 0 ? $totalSales / $totalOrders : 0;

        $topProducts = DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.status', 'delivered')
            ->whereBetween('orders.updated_at', [$startDate, $endDate])
            ->select('products.name', DB::raw('SUM(order_items.quantity) as total_qty'), DB::raw('SUM(order_items.subtotal) as total_revenue'))
            ->groupBy('products.id', 'products.name')
            ->orderBy('total_qty', 'desc')
            ->limit(5)
            ->get();

        $recentOrders = DB::table('orders')
            ->where('status', 'delivered')
            ->whereBetween('updated_at', [$startDate, $endDate])
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get();

        // Turnos de caja en el período
        $cashShifts = DB::table('cash_registers')
            ->whereBetween('opened_at', [$startDate, $endDate])
            ->orWhereBetween('closed_at', [$startDate, $endDate])
            ->orderBy('opened_at', 'desc')
            ->get();

        foreach ($cashShifts as $shift) {
            $shift->cashier = DB::table('tenant_users')->where('id', $shift->user_id)->first();
        }

        $totalCustomers = DB::table('customers')->count();

        return view('tenant.admin.reports.index', compact(
            'totalSales',
            'totalOrders',
            'tableOrders',
            'deliveryOrders',
            'totalCommission',
            'avgTicket',
            'topProducts',
            'recentOrders',
            'period',
            'totalCustomers',
            'cancelledOrders',
            'cashShifts',
            'dateFrom',
            'dateTo',
            'startDate',
            'endDate',
            'commissionRate'
        ));
    }
}
