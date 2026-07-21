<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\Payments\PaymentGateway;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function __construct(private PaymentGateway $gateway) {}

    public function show(Order $order): View|RedirectResponse
    {
        if ($order->isPaid()) {
            return redirect()->route('order.confirmation', $order);
        }

        $pix = null;
        if ($order->payment_method === 'pix') {
            $pix = $this->gateway->createPixPayment($order);

            if (! $order->payment_id) {
                $order->update(['payment_id' => $pix->paymentId]);
            }
        }

        return view('payment.show', compact('order', 'pix'));
    }

    public function process(Request $request, Order $order): RedirectResponse
    {
        if ($order->isPaid()) {
            return redirect()->route('order.confirmation', $order);
        }

        if (! in_array($order->payment_method, ['credit', 'debit'], true)) {
            return back()->with('error', 'Forma de pagamento inválida para esta operação.');
        }

        $card = $request->validate([
            'card_number' => ['required', 'string', 'min:12', 'max:23'],
            'card_holder' => ['required', 'string', 'max:120'],
            'card_expiry' => ['required', 'string', 'max:7'],
            'card_cvv' => ['required', 'string', 'min:3', 'max:4'],
        ]);

        $result = $this->gateway->createCardPayment($order, [
            'number' => $card['card_number'],
            'holder' => $card['card_holder'],
            'expiry' => $card['card_expiry'],
            'cvv' => $card['card_cvv'],
        ]);

        if ($result->isPaid()) {
            $this->markPaid($order, $result->paymentId);

            return redirect()->route('order.confirmation', $order)
                ->with('success', 'Pagamento aprovado!');
        }

        $order->update(['payment_status' => 'failed', 'payment_id' => $result->paymentId]);

        return back()->with('error', $result->message ?? 'Pagamento recusado. Tente novamente.');
    }

    public function confirmPix(Order $order): RedirectResponse
    {
        if ($order->payment_method !== 'pix') {
            return back()->with('error', 'Este pedido não é pago via PIX.');
        }

        if (! $order->isPaid()) {
            $this->markPaid($order, $order->payment_id ?? 'mock_pix_confirmed');
        }

        return redirect()->route('order.confirmation', $order)
            ->with('success', 'Pagamento PIX confirmado!');
    }

    public function status(Order $order): JsonResponse
    {
        return response()->json([
            'payment_status' => $order->payment_status,
            'status' => $order->status,
        ]);
    }

    public function confirmation(Order $order): View
    {
        $order->load('items', 'customer');

        return view('order.confirmation', compact('order'));
    }

    private function markPaid(Order $order, string $paymentId): void
    {
        $order->update([
            'payment_status' => 'paid',
            'payment_id' => $paymentId,
            'paid_at' => now(),
        ]);
    }
}
