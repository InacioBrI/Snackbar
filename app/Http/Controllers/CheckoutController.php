<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Services\Cart;
use App\Services\OrderService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    public function __construct(private Cart $cart) {}

    public function create(): View|RedirectResponse
    {
        if ($this->cart->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Seu carrinho está vazio.');
        }

        $feePercent = (float) Setting::get('service_fee_percent', 0);
        $serviceFee = round($this->cart->subtotal() * $feePercent / 100, 2);

        return view('checkout.create', [
            'serviceFee' => $serviceFee,
            'total' => round($this->cart->subtotal() + $serviceFee, 2),
        ]);
    }

    public function store(Request $request, OrderService $orderService): RedirectResponse
    {
        if ($this->cart->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Seu carrinho está vazio.');
        }

        $allowedMethods = array_filter(explode(',', (string) Setting::get('payment_methods', 'pix,credit,debit')));

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'phone' => ['required', 'string', 'max:30'],
            'location' => ['required', 'string', 'max:120'],
            'notes' => ['nullable', 'string', 'max:500'],
            'payment_method' => ['required', 'in:'.implode(',', $allowedMethods)],
        ], [], [
            'location' => 'mesa/local de retirada',
            'payment_method' => 'forma de pagamento',
        ]);

        $order = $orderService->createFromCart($validated);

        $this->cart->clear();

        return redirect()->route('payment.show', $order);
    }
}
