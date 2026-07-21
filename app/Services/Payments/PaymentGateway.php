<?php

namespace App\Services\Payments;

use App\Models\Order;

interface PaymentGateway
{
    /**
     * Start a PIX charge for the given order.
     */
    public function createPixPayment(Order $order): PaymentResult;

    /**
     * Start a card charge (credit/debit) for the given order.
     *
     * @param  array<string, mixed>  $cardData
     */
    public function createCardPayment(Order $order, array $cardData): PaymentResult;

    /**
     * Query the current status of a payment (pending, paid, failed).
     */
    public function getStatus(string $paymentId): string;
}
