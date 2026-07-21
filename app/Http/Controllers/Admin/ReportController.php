<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $validated = $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
        ]);

        $from = Carbon::parse($validated['from'] ?? Carbon::today()->subDays(29))->startOfDay();
        $to = Carbon::parse($validated['to'] ?? Carbon::today())->endOfDay();

        $paidInRange = Order::where('payment_status', 'paid')->whereBetween('paid_at', [$from, $to]);

        $revenue = (float) (clone $paidInRange)->sum('total');
        $ordersCount = (clone $paidInRange)->count();
        $averageTicket = $ordersCount > 0 ? $revenue / $ordersCount : 0.0;
        $itemsSold = (int) OrderItem::whereHas('order', fn ($q) => $q
            ->where('payment_status', 'paid')
            ->whereBetween('paid_at', [$from, $to]))
            ->sum('quantity');

        $topProducts = OrderItem::query()
            ->selectRaw('product_name, SUM(quantity) as qty, SUM(line_total) as revenue')
            ->whereHas('order', fn ($q) => $q
                ->where('payment_status', 'paid')
                ->whereBetween('paid_at', [$from, $to]))
            ->groupBy('product_name')
            ->orderByDesc('qty')
            ->take(10)
            ->get();

        $byDay = (clone $paidInRange)
            ->selectRaw('DATE(paid_at) as day, COUNT(*) as orders, SUM(total) as revenue')
            ->groupBy('day')
            ->orderBy('day')
            ->get();

        $byPaymentMethod = (clone $paidInRange)
            ->selectRaw('payment_method, COUNT(*) as orders, SUM(total) as revenue')
            ->groupBy('payment_method')
            ->get();

        return view('admin.reports.index', [
            'from' => $from->toDateString(),
            'to' => $to->toDateString(),
            'revenue' => $revenue,
            'ordersCount' => $ordersCount,
            'averageTicket' => $averageTicket,
            'itemsSold' => $itemsSold,
            'topProducts' => $topProducts,
            'byDay' => $byDay,
            'byPaymentMethod' => $byPaymentMethod,
        ]);
    }
}
