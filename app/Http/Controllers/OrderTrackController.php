<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class OrderTrackController extends Controller
{
    public function index(): View
    {
        return view('track.index');
    }

    public function search(Request $request): RedirectResponse|View
    {
        $validated = $request->validate([
            'identifier' => ['required', 'string', 'max:60'],
        ], [], ['identifier' => 'número do pedido ou telefone']);

        $identifier = trim($validated['identifier']);

        $order = Order::where('order_number', $identifier)->first();

        if ($order) {
            return redirect()->route('track.show', $order);
        }

        $orders = Order::whereHas('customer', fn ($q) => $q->where('phone', $identifier))
            ->latest()
            ->take(20)
            ->get();

        if ($orders->isEmpty()) {
            return back()
                ->withInput()
                ->with('error', 'Nenhum pedido encontrado para o número do pedido ou telefone informado.');
        }

        if ($orders->count() === 1) {
            return redirect()->route('track.show', $orders->first());
        }

        return view('track.index', compact('orders', 'identifier'));
    }

    public function show(Order $order): View
    {
        $order->load('items', 'customer');

        return view('track.show', compact('order'));
    }
}
