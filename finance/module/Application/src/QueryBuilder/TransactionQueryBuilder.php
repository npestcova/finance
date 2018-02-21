<?php

namespace Application\QueryBuilder;


class TransactionQueryBuilder extends AbstractQueryBuilder
{
    /**
     * @param string $date
     * @return $this
     */
    public function fromDate($date)
    {
        if (!$date) {
            return $this;
        }

        $this->andWhere('t.date >= :startDate')
            ->setParameter('startDate', $date);

        return $this;
    }

    /**
     * @param string $date
     * @return $this
     */
    public function toDate($date)
    {
        if (!$date) {
            return $this;
        }

        $this->andWhere('t.date <= :endDate')
            ->setParameter('endDate', $date);

        return $this;
    }

    /**
     * @param int[] $categoriesIds
     * @return $this
     */
    public function fromCategories($categoriesIds)
    {
        if (empty($categoriesIds)) {
            return $this;
        }

        $this->andWhere('t.category in (:categories)')
            ->setParameter('categories', $categoriesIds);

        return $this;
    }

    /**
     * @return $this     *
     */
    public function excludeTransfers()
    {
        $this->joinCategory('c')
            ->andWhere('c.excludeFromCashflow = 0');

        return $this;
    }

    /**
     * @param string $alias
     * @return $this
     */
    public function joinCategory($alias)
    {
        $this->leftJoin('t.category', $alias);

        return $this;
    }
}