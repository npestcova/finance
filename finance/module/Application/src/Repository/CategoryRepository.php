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

    /**
     * @param int|null $parentId
     * @param $orderBy
     * @return array
     */
    public function findByParent(?int $parentId, $orderBy): array
    {
        return $this->findBy(['parent' => $parentId], $orderBy);
    }

    /**
     * @param int|null $parentId
     * @return array
     */
    public function findIdsByParent(?int $parentId): array
    {
        /** @var CategoryQueryBuilder $queryBuilder */
        $queryBuilder = $this->createQueryBuilder('c')
            ->select('id');

        if ($parentId === null) {
            $queryBuilder->where('c.parent is null');
        } else {
            $queryBuilder->where('c.parent = :parent')
                ->setParameter('parent', $parentId);
        }

        $result = $queryBuilder->getQuery()->getResult();
        return $result;
    }
}