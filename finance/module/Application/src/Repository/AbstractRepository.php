<?php
/**
 * Created by PhpStorm.
 * User: npestcova
 * Date: 1/3/2018
 * Time: 3:32 PM
 */

namespace Application\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class AbstractRepository extends EntityRepository
{

    /** @var  string */
    protected $queryBuilderClass;

    public function createQueryBuilder($alias, $indexBy = null)
    {
        if ($this->queryBuilderClass) {
            $queryBuilderClass = $this->queryBuilderClass;

            /** @var QueryBuilder $queryBuilder */
            $queryBuilder = new $queryBuilderClass($this->getEntityManager());
            if (!$queryBuilder instanceof QueryBuilder) {
                return null;
            }

            $queryBuilder->select($alias)
                ->from($this->_entityName, $alias, $indexBy);

            return $queryBuilder;
        } else {
            return parent::createQueryBuilder($alias, $indexBy);
        }
    }


}