<?php
/**
 * Created by PhpStorm.
 * User: npestcova
 * Date: 1/3/2018
 * Time: 3:30 PM
 */

namespace Application\Repository;

use Application\QueryBuilder\CategoryQueryBuilder;

class CategoryRepository extends AbstractRepository
{
    protected $queryBuilderClass = CategoryQueryBuilder::class;
}