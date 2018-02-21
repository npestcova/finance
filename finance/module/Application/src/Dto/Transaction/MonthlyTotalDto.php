<?php
/**
 * Created by PhpStorm.
 * User: npestcova
 * Date: 2/21/2018
 * Time: 2:42 PM
 */

namespace Application\Dto\Transaction;


class MonthlyTotalDto
{
    /** @var string */
    public $period;

    /** @var string */
    public $startDate;

    /** @var string */
    public $endDate;

    /** @var string */
    public $title;

    /** @var float */
    public $total;
}