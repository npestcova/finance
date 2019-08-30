<?php
/**
 * Created by PhpStorm.
 * User: npestcova
 * Date: 1/3/2018
 * Time: 3:30 PM
 */

namespace Application\Repository;

use Application\Dto\Transaction\BulkChangeTransactionsDto;
use Application\Dto\Transaction\GetMonthlyTotalsDto;
use Application\Dto\Transaction\GetTotalsByCategoryInputDto;
use Application\Dto\Transaction\TransactionSearchDto;
use Application\Entity\Category;
use Application\Entity\Transaction;
use Application\QueryBuilder\TransactionQueryBuilder;
use Application\Service\AccountService;
use Application\Service\CategoryService;
use Application\Service\TransactionService;

class TransactionRepository extends AbstractRepository
{
    protected $queryBuilderClass = TransactionQueryBuilder::class;

    /**
     * @param TransactionSearchDto $filter
     * @return Transaction[]
     */
    public function findTransactions(TransactionSearchDto $filter)
    {
        /** @var TransactionQueryBuilder $qb */
        $qb = $this->createQueryBuilder('t');

        $qb->where('1=1');

        $qb->fromDate($filter->dateFrom)
            ->toDate($filter->dateTo);

        if ($filter->accountId != AccountService::NO_FILTER) {
            $qb->andWhere('t.account = :accountId')
                ->setParameter('accountId', $filter->accountId);
        }
        if ($filter->categoryId != CategoryService::NO_FILTER &&
            $filter->categoryId != 0
        ) {
            $qb->leftJoin('t.category', 'c')
                ->andWhere('c.id = :categoryId OR c.parent = :categoryId')
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

    /**
     * @param BulkChangeTransactionsDto $inputDto
     */
    public function bulkChangeTransactions(BulkChangeTransactionsDto $inputDto)
    {
        $qb = $this->createQueryBuilder('t');
        $qb->update(Transaction::class, 't')
            ->set('t.category', ':category')
            ->setParameter('category', $inputDto->categoryId)
            ->where('t.id IN (:id)')
            ->setParameter('id', $inputDto->ids);

        $query = $qb->getQuery();
        $query->execute();
    }

    /**
     * @param GetTotalsByCategoryInputDto $inputDto
     * @return array
     */
    public function findTotalsByCategory(GetTotalsByCategoryInputDto $inputDto)
    {
        /** @var TransactionQueryBuilder $queryBuilder */
        $queryBuilder = $this->createQueryBuilder('t')
            ->select('SUM(t.amount) as total')
            ->addSelect('IDENTITY(t.category) as category_id')
            ->addSelect('t.date')
            ->groupBy('t.category');

        $queryBuilder->fromDate($inputDto->startDate)
            ->toDate($inputDto->endDate)
            ->fromCategories($inputDto->categoryIds);

        if (!empty($inputDto->type)) {
            $type = $inputDto->type == TransactionService::CASHFLOW_TYPE_INCOME
                ? Category::TYPE_INCOME
                : Category::TYPE_EXPENSE;

            $queryBuilder->leftJoin('t.category', 'c')
                ->andWhere('c.type = :categoryType')
                ->setParameter('categoryType', $type);
        }

        if ($inputDto->groupBy) {
            $queryBuilder->addGroupBy('t.' . $inputDto->groupBy);
        }

        $result = $queryBuilder->getQuery()->getResult();
        return $result;
    }

    /**
     * @param $inputDto
     * @return array
     */
    public function findMonthlyTotals(GetMonthlyTotalsDto $inputDto)
    {
        /** @var TransactionQueryBuilder $queryBuilder */
        $queryBuilder = $this->createQueryBuilder('t')
            ->select('SUM(t.amount) as total')
            ->addSelect('substring(t.date, 1, 7) as period')
            ->orderBy('period', 'DESC')
            ->groupBy('period');

        $queryBuilder->fromDate($inputDto->startDate)
            ->toDate($inputDto->endDate);

        if ($inputDto->excludeTransfers) {
            $queryBuilder->excludeTransfers();
        }

        $result = $queryBuilder->getQuery()->getResult();
        return $result;
    }
}