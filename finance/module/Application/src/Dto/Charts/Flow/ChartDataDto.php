<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: npestcova
 * Date: 8/30/2019
 * Time: 11:57 AM
 */

namespace Application\Dto\Charts\Flow;


class ChartDataDto
{
    /**
     * @var array
     */
    public $periods = [];

    /**
     * @var CategoryDto[]
     */
    public $categories = [];
}