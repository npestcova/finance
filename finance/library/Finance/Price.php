<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: npestcova
 * Date: 8/30/2019
 * Time: 1:32 PM
 */

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
}