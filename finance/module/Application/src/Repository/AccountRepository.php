<?php
/**
 * Created by PhpStorm.
 * User: npestcova
 * Date: 1/3/2018
 * Time: 3:30 PM
 */

namespace Application\Repository;

use Application\Entity\Account;
use Application\QueryBuilder\AccountQueryBuilder;

class AccountRepository extends AbstractRepository
{
    protected $queryBuilderClass = AccountQueryBuilder::class;

    /**
     * @param $name
     * @return null|Account
     */
    public function findOneByName($name)
    {
        /** @var Account $record */
        $record = $this->findOneBy(['name' => $name]);
        return $record;
    }
}