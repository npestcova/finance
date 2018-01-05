<?php
/**
 * Created by PhpStorm.
 * User: npestcova
 * Date: 1/3/2018
 * Time: 3:30 PM
 */

namespace Application\Repository;

use Application\Entity\Category;
use Application\QueryBuilder\CategoryQueryBuilder;

class CategoryRepository extends AbstractRepository
{
    protected $queryBuilderClass = CategoryQueryBuilder::class;

    /**
     * @param $name
     * @return null|Category
     */
    public function findOneByName($name)
    {
        /** @var Category $record */
        $record = $this->findOneBy(['name' => $name]);
        return $record;
    }
}