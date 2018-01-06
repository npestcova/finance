<?php
/**
 * Created by PhpStorm.
 * User: npestcova
 * Date: 1/3/2018
 * Time: 3:30 PM
 */

namespace Application\Repository;

use Application\Dto\Transaction\TransactionSearchDto;
use Application\Entity\Transaction;
use Application\QueryBuilder\TransactionQueryBuilder;

class TransactionRepository extends AbstractRepository
{
    protected $queryBuilderClass = TransactionQueryBuilder::class;

    /**
     * @param TransactionSearchDto $filter
     * @return Transaction[]
     */
    public function findTransactions(TransactionSearchDto $filter)
    {
        $qb = $this->createQueryBuilder('t');

        if ($filter->dateFrom) {
            $qb->where('t.date >= :dateFrom')
                ->setParameter('dateFrom', $filter->dateFrom);
        }
        if (0 && $filter->dateTo) {
            $qb->where('t.date <= :dateTo')
                ->setParameter('dateTo', $filter->dateTo);
        }
        if ($filter->accountId) {
            // ...
        }

        $result = $qb->getQuery()->getResult();
        return $result;
    }
}