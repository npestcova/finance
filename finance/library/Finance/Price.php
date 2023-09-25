<?php

declare(strict_types=1);

namespace Finance;

class Price
{
    /**
     * @param float $a
     * @param float $b
     * @return float
     */
    public static function add(float $a, float $b): float
    {
        return self::round($a + $b);
    }

    /**
     * @param float $price
     * @return float
     */
    public static function round(float $price): float
    {
        return round($price, 2);
    }

    public static function fromString(string $price): float
    {
        // is negative number
        $isNegative = strpos((string)$price, '-') !== false;

        // convert "," to "."
        $price = str_replace(',', '.', $price);

        // remove everything except numbers and dot "."
        $price = preg_replace("/[^0-9\.]/", "", $price);

        // remove all seperators from first part and keep the end
        $price = str_replace('.', '',substr($price, 0, -3)) . substr($price, -3);

        // Set negative number
        if( $isNegative ) {
            $price = '-' . $price;
        }

        // return float
        return (float) $price;
    }
}
