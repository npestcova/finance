<?php
/**
 * Created by PhpStorm.
 * User: npestcova
 * Date: 1/3/2018
 * Time: 4:58 PM
 */

namespace Application\Dto\Transaction;


use Application\Entity\Account;
use Application\Entity\Category;

class TransactionInfoDto
{
    /**
     * @var integer
     */
    public $id;

    /**
     * @var \DateTime
     */
    public $date;

    /**
     * @var Account
     */
    public $account;

    /**
     * @var Category
     */
    public $category;

    /**
     * @var string
     */
    public $description;

    /**
     * @var float
     */
    public $amount = 0;
}