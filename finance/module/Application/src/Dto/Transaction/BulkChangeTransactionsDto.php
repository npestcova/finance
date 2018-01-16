<?php

namespace Application\Dto\Transaction;


class BulkChangeTransactionsDto
{
    /**
     * @var int[]
     */
    public $ids;

    /**
     * @var int
     */
    public $categoryId;
}