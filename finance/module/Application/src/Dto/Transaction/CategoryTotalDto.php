<?php
/**
 * Created by PhpStorm.
 * User: npestcova
 * Date: 1/20/2018
 * Time: 2:00 PM
 */

namespace Application\Dto\Transaction;


class CategoryTotalDto
{
    /** @var int */
    public $categoryId;

    /** @var string */
    public $categoryName;

    /** @var float */
    public $amount = 0;

    /** @var string */
    public $startDate;

    /** @var string */
    public $endDate;

    /** @var CategoryTotalDto[] */
    public $subCategories = [];
}