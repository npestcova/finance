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
     * @var integer|null
     */
    public $accountId;

    /**
     * @var integer|null
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
