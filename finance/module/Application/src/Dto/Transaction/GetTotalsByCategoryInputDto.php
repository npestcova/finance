<?php

namespace Application\Dto\Transaction;


class GetTotalsByCategoryInputDto
{
    /** @var array */
    public $categoryIds;

    /** @var string */
    public $startDate;

    /** @var string */
    public $endDate;

    /** @var bool  */
    public $excludeCashFlow = true;

    /** @var bool  */
    public $showHierarchy = true;

    /** @var string  */
    public $type = '';
}