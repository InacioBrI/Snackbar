<?php

if (! function_exists('brl')) {
    /**
     * Format a value as Brazilian Real currency (e.g. R$ 12,50).
     */
    function brl(float|int|string|null $value): string
    {
        return 'R$ '.number_format((float) $value, 2, ',', '.');
    }
}
