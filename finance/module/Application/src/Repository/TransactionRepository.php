<?php
/**
 * Created by PhpStorm.
 * User: npestcova
 * Date: 1/3/2018
 * Time: 3:30 PM
 */

namespace Application\Repository;

use Application\Dto\Transaction\TransactionSearchDto;
use Application\Entity\Category;
use Application\Entity\Transaction;
use Application\QueryBuilder\TransactionQueryBuilder;
use Application\Service\AccountService;
use Application\Service\CategoryService;

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

        $qb->where('1=1');

        if ($filter->dateFrom) {
            $qb->andWhere('t.date >= :dateFrom')
                ->setParameter('dateFrom', $filter->dateFrom);
        }
        if ($filter->dateTo) {
            $qb->andWhere('t.date <= :dateTo')
                ->setParameter('dateTo', $filter->dateTo);
        }
        if ($filter->accountId != AccountService::NO_FILTER) {
            $qb->andWhere('t.account = :accountId')
                ->setParameter('accountId', $filter->accountId);
        }
        if ($filter->categoryId != CategoryService::NO_FILTER &&
            $filter->categoryId != 0
        ) {
            //@TODO: show children's if parent selected
            $qb->andWhere('t.category = :categoryId')
                ->setParameter('categoryId', $filter->categoryId);
        } elseif ($filter->categoryId == 0) {
            $qb->andWhere('t.category is null');
        }

        if ($filter->description) {
            $qb->andWhere('t.description like :description')
                ->setParameter('description', '%' . $filter->description . '%');
        }
        $result = $qb->getQuery()->getResult();
        return $result;
    }
}