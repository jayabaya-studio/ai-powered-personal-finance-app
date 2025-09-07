<?php

if (! function_exists('formatCurrency')) {
    /**
     * @param float $amount
     * @param string $currencyCode
     * @return string
     */
    function formatCurrency($amount, $currencyCode = 'USD')
    {
        $symbol = '$';
        if ($currencyCode === 'IDR') {
            $symbol = 'Rp ';
        }

        return $symbol . number_format($amount, 2, '.', ',');
    }
}