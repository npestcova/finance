<?php
/**
 * Created by PhpStorm.
 * User: npestcova
 * Date: 1/6/2018
 * Time: 1:54 PM
 */

namespace Application\Dto\Transaction;


class ViewInfoDto
{
    /**
     * @var integer
     */
    public $id;

    /**
     * @var string (Y-m-d)
     */
    public $dateDb;

    /**
     * @var string
     */
    public $date;

    /**
     * @var string
     */
    public $accountName;

    /**
     * @var string
     */
    public $categoryName;

    /**
     * @var string
     */
    public $description;

    /**
     * @var float
     */
    public $amount = 0;
}