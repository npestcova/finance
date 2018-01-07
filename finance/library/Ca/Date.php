<?php

namespace Ca;

class Date
{
    const SECONDS_IN_DAY = 86400;
    const EMPTY_DATE = '0000-00-00';
    const EMPTY_DATETIME = '0000-00-00 00:00:00';
    const START_OF_DAY_TIME = '00:00:00';
    const END_OF_DAY_TIME = '23:59:59';
    const FAR_FUTURE = '9999-12-31 23:59:59';

    /**
     * @var \DateTime $currentDate
     */
    protected static $currentDate = null;

    public static function test()
    {
        echo 'works';
    }

}