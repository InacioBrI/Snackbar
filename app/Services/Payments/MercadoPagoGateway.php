<?php

namespace App\Services\Payments;

use App\Models\Order;
use RuntimeException;

/**
 * Structural placeholder for a real Mercado Pago integration.
 *
 * The wiring (config, binding, interface) is in place; implement the API calls
 * here once MERCADOPAGO_ACCESS_TOKEN is provided. Until then it fails loudly so
 * the mock driver is used instead of silently pretending to charge.
 */
class MercadoPagoGateway implements PaymentGateway
{
    public function __construct(private ?string $accessToken)
    {
        if (empty($this->accessToken)) {
            throw new RuntimeException('Mercado Pago não configurado: defina MERCADOPAGO_ACCESS_TOKEN.');
        }
    }

    public function createPixPayment(Order $order): PaymentResult
    {
        // TODO: POST /v1/payments with payment_method_id=pix and read point_of_interaction.
        throw new RuntimeException('Integração Mercado Pago PIX ainda não implementada.');
    }

    public function createCardPayment(Order $order, array $cardData): PaymentResult
    {
        // TODO: tokenize card on the client, then POST /v1/payments with the token.
        throw new RuntimeException('Integração Mercado Pago cartão ainda não implementada.');
    }

    public function getStatus(string $paymentId): string
    {
        // TODO: GET /v1/payments/{id} and map "approved" => paid.
        throw new RuntimeException('Consulta de status Mercado Pago ainda não implementada.');
    }
}
