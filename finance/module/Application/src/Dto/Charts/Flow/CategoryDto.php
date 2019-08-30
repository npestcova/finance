<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: npestcova
 * Date: 8/30/2019
 * Time: 11:58 AM
 */

namespace Application\Dto\Charts\Flow;


class CategoryDto
{
    /** @var string */
    public $name;

    /** @var float[] */
    public $totals;
}