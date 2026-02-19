<?php

if (!function_exists('format_price')) {
    /**
     * Format a number as currency.
     *
     * @param  float|int|string  $amount
     * @param  int  $decimals
     * @param  string  $decimalSeparator
     * @param  string  $thousandsSeparator
     * @param  string  $currencySymbol
     * @param  bool  $symbolBefore
     * @return string
     */
    function format_price(
        $amount,
        int $decimals = 2,
        string $decimalSeparator = '.',
        string $thousandsSeparator = ',',
        string $currencySymbol = '$',
        bool $symbolBefore = true
    ): string {
        // Convert string to float if needed
        $numericAmount = is_numeric($amount) ? (float) $amount : 0.0;
        
        // Format the number
        $formattedNumber = number_format(
            $numericAmount,
            $decimals,
            $decimalSeparator,
            $thousandsSeparator
        );
        
        // Add currency symbol
        if ($symbolBefore) {
            return $currencySymbol . $formattedNumber;
        }
        
        return $formattedNumber . $currencySymbol;
    }
}