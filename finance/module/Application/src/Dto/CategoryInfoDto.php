<?php
/**
 * Created by PhpStorm.
 * User: npestcova
 * Date: 1/4/2018
 * Time: 3:26 PM
 */

namespace Application\Dto;


use Application\Entity\Category;

class CategoryInfoDto
{
    /** @var int */
    public $id;

    /** @var string */
    public $name;

    /** @var Category */
    public $parent;
}