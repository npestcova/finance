<?php
/**
 * Created by PhpStorm.
 * User: npestcova
 * Date: 1/6/2018
 * Time: 2:02 PM
 */

namespace Finance;

class Date
{
    const VIEW_DATE_FORMAT_DEFAULT = 'm/d/Y';

    /**
     * @param $date
     * @return string
     */
    public static function getViewDate($date)
    {
        $dateObj = new \DateTime($date);
        return $dateObj->format(self::VIEW_DATE_FORMAT_DEFAULT);
    }
}