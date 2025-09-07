<?php

if (! function_exists('formatCurrency')) {
    /**
     * Memformat angka menjadi format mata uang yang benar.
     *
     * @param float $amount
     * @param string $currencyCode
     * @return string
     */
    function formatCurrency($amount, $currencyCode = 'USD')
    {
        $symbol = '$'; // Default symbol
        if ($currencyCode === 'IDR') {
            $symbol = 'Rp ';
        }
        // Tambahkan logika untuk mata uang lain di sini jika perlu

        return $symbol . number_format($amount, 2, '.', ',');
    }
}