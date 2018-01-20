<?php
/**
 * Created by PhpStorm.
 * User: npestcova
 * Date: 1/20/2018
 * Time: 1:58 PM
 */

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

    public $showHierarchy = true;
}