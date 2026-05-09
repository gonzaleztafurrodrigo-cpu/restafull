<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function index()
    {
        $settings = DB::table('settings')->pluck('value', 'key')->toArray();
        $today = now()->startOfDay();

        $totalSalesToday = DB::table('orders')
            ->where('status', 'delivered')
            ->where('updated_at', '>=', $today)
            ->sum('total');

        $totalOrdersToday = DB::table('orders')
            ->where('status', 'delivered')
            ->where('updated_at', '>=', $today)
            ->count();

        $pendingOrders = DB::table('orders')
            ->whereIn('status', ['pending', 'confirmed', 'preparing', 'ready'])
            ->count();

        $activeDeliveries = DB::table('orders')
            ->whereIn('status', ['dispatched', 'collecting'])
            ->count();

        $occupiedTables = DB::table('orders')
            ->whereIn('status', ['pending', 'confirmed', 'preparing', 'ready'])
            ->where('type', 'table')
            ->distinct('table_id')
            ->count('table_id');

        $totalTables = DB::table('tables')->where('is_active', true)->count();

        $commissionToday = DB::table('transactions')
            ->where('created_at', '>=', $today)
            ->sum('commission_amount');

        return view('tenant.admin.index', compact(
            'settings',
            'totalSalesToday',
            'totalOrdersToday',
            'pendingOrders',
            'activeDeliveries',
            'occupiedTables',
            'totalTables',
            'commissionToday'
        ));
    }
}
