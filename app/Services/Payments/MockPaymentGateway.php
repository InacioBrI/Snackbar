<?php

namespace App\Services\Payments;

use App\Models\Order;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Support\Str;

/**
 * Local, credential-free gateway used for development and demos.
 *
 * PIX charges stay "pending" until confirmed (simulating the customer paying),
 * while card charges are approved instantly (unless the card number ends in 0,
 * which simulates a decline for testing the failure path).
 */
class MockPaymentGateway implements PaymentGateway
{
    public function createPixPayment(Order $order): PaymentResult
    {
        $pix = new PixPayload(
            (string) config('payments.pix.key'),
            (string) config('payments.pix.merchant_name'),
            (string) config('payments.pix.merchant_city'),
        );

        $payload = $pix->build((float) $order->total, $order->order_number);

        return new PaymentResult(
            paymentId: 'mock_pix_'.Str::random(16),
            status: 'pending',
            pixPayload: $payload,
            pixQrCode: $this->qrCodeDataUri($payload),
            message: 'Escaneie o QR Code ou copie o código PIX para pagar.',
        );
    }

    public function createCardPayment(Order $order, array $cardData): PaymentResult
    {
        $number = preg_replace('/\D/', '', (string) ($cardData['number'] ?? ''));
        $approved = $number === '' || ! str_ends_with($number, '0');

        return new PaymentResult(
            paymentId: 'mock_card_'.Str::random(16),
            status: $approved ? 'paid' : 'failed',
            message: $approved ? 'Pagamento aprovado.' : 'Pagamento recusado pela operadora.',
        );
    }

    public function getStatus(string $paymentId): string
    {
        return 'pending';
    }

    private function qrCodeDataUri(string $payload): string
    {
        $result = (new Builder(
            writer: new PngWriter,
            data: $payload,
            size: 300,
            margin: 12,
        ))->build();

        return $result->getDataUri();
    }
}
