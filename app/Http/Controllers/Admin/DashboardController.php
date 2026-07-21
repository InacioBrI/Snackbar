<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index(): View
    {
        $today = Carbon::today();

        $paid = Order::where('payment_status', 'paid');

        $totalRevenue = (float) (clone $paid)->sum('total');
        $totalSales = (clone $paid)->count();
        $todayRevenue = (float) (clone $paid)->whereDate('paid_at', $today)->sum('total');
        $ordersToday = Order::whereDate('created_at', $today)->count();
        $averageTicket = $totalSales > 0 ? $totalRevenue / $totalSales : 0.0;

        $statusCounts = collect(Order::STATUSES)->mapWithKeys(fn ($label, $key) => [
            $key => Order::where('status', $key)->count(),
        ]);

        $topProducts = OrderItem::query()
            ->selectRaw('product_name, SUM(quantity) as qty, SUM(line_total) as revenue')
            ->whereHas('order', fn ($q) => $q->where('payment_status', 'paid'))
            ->groupBy('product_name')
            ->orderByDesc('qty')
            ->take(5)
            ->get();

        $recentOrders = Order::with('customer')->latest()->take(8)->get();

        $salesLast7Days = collect(range(6, 0))->map(function ($daysAgo) {
            $date = Carbon::today()->subDays($daysAgo);

            return [
                'label' => $date->format('d/m'),
                'total' => (float) Order::where('payment_status', 'paid')
                    ->whereDate('paid_at', $date)
                    ->sum('total'),
            ];
        });

        return view('admin.dashboard', compact(
            'totalRevenue',
            'totalSales',
            'todayRevenue',
            'ordersToday',
            'averageTicket',
            'statusCounts',
            'topProducts',
            'recentOrders',
            'salesLast7Days',
        ));
    }
}
