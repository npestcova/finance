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
    const DB_DATE_FORMAT_DEFAULT = 'Y-m-d';

    /**
     * @param $date
     * @return string
     */
    public static function getDbDate($date)
    {
        $dateObj = new \DateTime($date);
        return $dateObj->format(self::DB_DATE_FORMAT_DEFAULT);
    }

    /**
     * @param $date
     * @return string
     */
    public static function getViewDate($date)
    {
        $dateObj = new \DateTime($date);
        return $dateObj->format(self::VIEW_DATE_FORMAT_DEFAULT);
    }

    /**
     * @param $timestamp
     * @return false|string
     */
    public static function getViewDateFromTimestamp($timestamp)
    {
        return date(self::VIEW_DATE_FORMAT_DEFAULT, $timestamp);
    }

    /**
     * @return array
     */
    public static function getMonthsNames()
    {
        $names = [];

        $date = new \DateTime();
        for ($i=1; $i <= 12; $i++) {
            $date->setDate(2001, $i, 1);
            $names[$i] = $date->format('M');
        }

        return $names;
    }

}