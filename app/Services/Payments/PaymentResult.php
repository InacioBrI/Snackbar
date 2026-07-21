<?php

namespace App\Services\Payments;

class PaymentResult
{
    public function __construct(
        public string $paymentId,
        public string $status, // pending, paid, failed
        public ?string $pixPayload = null,
        public ?string $pixQrCode = null, // data URI (base64 PNG)
        public ?string $message = null,
    ) {}

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }
}
