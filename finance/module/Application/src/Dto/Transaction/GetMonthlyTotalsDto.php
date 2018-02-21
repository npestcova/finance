<?php

namespace Application\Dto\Transaction;

class GetMonthlyTotalsDto
{
    /** @var string */
    public $startDate;

    /** @var string */
    public $endDate;

    /** @var bool  */
    public $excludeTransfers = true;
}