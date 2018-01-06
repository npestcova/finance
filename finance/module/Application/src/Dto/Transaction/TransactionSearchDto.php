<?php
/**
 * Created by PhpStorm.
 * User: npestcova
 * Date: 1/6/2018
 * Time: 1:43 PM
 */

namespace Application\Dto\Transaction;


class TransactionSearchDto
{
    /**
     * date Y-m-d
     * @var string
     */
    public $dateFrom;

    /**
     * date Y-m-d
     * @var string
     */
    public $dateTo;

    /**
     * @var int
     */
    public $categoryId;

    /**
     * @var int
     */
    public $accountId;

    /**
     * @var string
     */
    public $description;

}