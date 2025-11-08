<?php

namespace Application\Dto\Transaction;

class SaveTransactionDto
{
    /**
     * @var integer|null
     */
    public $id;

    /**
     * @var string
     */
    public $date;

    /**
     * @var integer
     */
    public $accountId;

    /**
     * @var integer
     */
    public $categoryId;

    /**
     * @var string
     */
    public $description;

    /**
     * @var float
     */
    public $amount;
}
