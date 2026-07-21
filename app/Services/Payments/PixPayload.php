<?php

namespace App\Services\Payments;

use Illuminate\Support\Str;

/**
 * Builds a Pix "BR Code" (EMV) copia-e-cola payload.
 *
 * This produces a spec-compliant static Pix string (including CRC16). It is
 * suitable for demos and for real usage when a valid Pix key is configured.
 */
class PixPayload
{
    public function __construct(
        private string $pixKey,
        private string $merchantName,
        private string $merchantCity,
    ) {}

    public function build(float $amount, string $txId): string
    {
        $merchantName = $this->sanitize($this->merchantName, 25);
        $merchantCity = $this->sanitize($this->merchantCity, 15);
        $txId = $this->sanitize(Str::of($txId)->replace('-', '')->limit(25, ''), 25) ?: '***';

        $gui = $this->field('00', 'br.gov.bcb.pix');
        $key = $this->field('01', $this->pixKey);
        $merchantAccountInfo = $this->field('26', $gui.$key);

        $additionalData = $this->field('62', $this->field('05', $txId));

        $payload =
            $this->field('00', '01').
            $merchantAccountInfo.
            $this->field('52', '0000').
            $this->field('53', '986').
            $this->field('54', number_format($amount, 2, '.', '')).
            $this->field('58', 'BR').
            $this->field('59', $merchantName).
            $this->field('60', $merchantCity).
            $additionalData.
            '6304';

        return $payload.$this->crc16($payload);
    }

    private function field(string $id, string $value): string
    {
        return $id.str_pad((string) strlen($value), 2, '0', STR_PAD_LEFT).$value;
    }

    private function sanitize(string $value, int $max): string
    {
        $value = preg_replace('/[^A-Za-z0-9 ]/', '', $this->stripAccents($value)) ?? '';

        return substr(trim($value), 0, $max);
    }

    private function stripAccents(string $value): string
    {
        return strtr($value, [
            'á' => 'a', 'à' => 'a', 'ã' => 'a', 'â' => 'a', 'ä' => 'a',
            'é' => 'e', 'ê' => 'e', 'è' => 'e', 'í' => 'i', 'ì' => 'i',
            'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ò' => 'o', 'ú' => 'u',
            'ù' => 'u', 'ç' => 'c', 'ñ' => 'n',
            'Á' => 'A', 'À' => 'A', 'Ã' => 'A', 'Â' => 'A', 'É' => 'E',
            'Ê' => 'E', 'Í' => 'I', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O',
            'Ú' => 'U', 'Ç' => 'C',
        ]);
    }

    private function crc16(string $payload): string
    {
        $polynomial = 0x1021;
        $result = 0xFFFF;

        for ($i = 0; $i < strlen($payload); $i++) {
            $result ^= (ord($payload[$i]) << 8);
            for ($bit = 0; $bit < 8; $bit++) {
                if ($result & 0x8000) {
                    $result = ($result << 1) ^ $polynomial;
                } else {
                    $result <<= 1;
                }
                $result &= 0xFFFF;
            }
        }

        return strtoupper(str_pad(dechex($result), 4, '0', STR_PAD_LEFT));
    }
}
