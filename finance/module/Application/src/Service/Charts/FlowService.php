<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: npestcova
 * Date: 8/9/2019
 * Time: 10:53 PM
 */

namespace Application\Service\Charts;


use Application\Dto\Charts\Flow\CategoryDto;
use Application\Dto\Charts\Flow\ChartDataDto;
use Application\Entity\Category;
use Application\Service\AbstractService;
use Application\Service\CategoryService;
use Application\Service\TransactionService;
use Doctrine\ORM\EntityManager;
use Finance\Date;
use Finance\Exception\NotFoundException;
use Finance\Price;

class FlowService extends AbstractService
{
    const PERIOD_TYPE_MONTH = 'month';

    /**
     * @var CategoryService
     */
    protected $categoryService;

    /**
     * @var TransactionService
     */
    protected $transactionService;

    public function __construct(
        EntityManager $entityManager,
        CategoryService $categoryService,
        TransactionService $transactionService
    ) {
        parent::__construct($entityManager);

        $this->categoryService = $categoryService;
        $this->transactionService = $transactionService;
    }

    /**
     * @param string $noFilterString
     * @return array
     */
    public function getCategoryNames(string $noFilterString): array
    {
        return $this->categoryService->getCategoryNames(false, $noFilterString);
    }

    /**
     * @param string $dateFrom
     * @param string $dateTo
     * @param int $categoryId
     * @return ChartDataDto
     * @throws \Exception
     */
    public function getChartData(string $dateFrom, string $dateTo, int $categoryId): ChartDataDto
    {
        $result = new ChartDataDto();
        $result->periods = $this->getChartPeriods($dateFrom, $dateTo);
        $result->categories = $this->getChartTotals($categoryId, $dateFrom, $dateTo, $result->periods);

        return $result;
    }

    /**
     * @param $dateFrom
     * @param $dateTo
     * @return array
     * @throws \Exception
     */
    protected function getChartPeriods(string $dateFrom, string $dateTo)
    {
        return Date::getRangeMonthTitles($dateFrom . '-01', $dateTo . '-01');
    }

    /**
     * @param int $categoryId
     * @param string $dateFrom
     * @param string $dateTo
     * @param array $periods
     * @return CategoryDto[]
     * @throws NotFoundException
     */
    protected function getChartTotals(int $categoryId, string $dateFrom, string $dateTo, array $periods): array
    {
        $categories = $this->findCategories($categoryId);
        $totalsByCategory = $this->transactionService->getCategoryTotalsByDate(array_keys($categories), $dateFrom, $dateTo);

        $categoryAverage = $this->initCategoryInfo(current($categories), $periods);
        $categoryAverage->name .= ' (average)';

        $result = [];
        foreach ($totalsByCategory as $categoryId => $totals) {
            if (!isset($result[$categoryId])) {
                $result[$categoryId] = $this->initCategoryInfo($categories[$categoryId], $periods);
            }
            foreach ($totals as $date => $amount) {
                $periodKey = $this->findPeriodKey($date, self::PERIOD_TYPE_MONTH);

                $result[$categoryId]->totals[$periodKey] = Price::add($result[$categoryId]->totals[$periodKey], $amount);
                $categoryAverage->totals[$periodKey] = Price::add($categoryAverage->totals[$periodKey], $amount);
            }
        }

        $categoriesCount = count($result);
        if ($categoriesCount > 0) {
            foreach ($categoryAverage->totals as $key => $amount) {
                $categoryAverage->totals[$key] = Price::round($amount / $categoriesCount);
            }
        }
        $result[] = $categoryAverage;

        return array_values($result);
    }

    /**
     * @param Category $category
     * @param array $periods
     * @return CategoryDto
     */
    protected function initCategoryInfo(Category $category, $periods): CategoryDto
    {
        $categoryDto = new CategoryDto();
        $categoryDto->name = $category->getName();
        $categoryDto->totals = [];

        foreach ($periods as $periodKey => $periodName) {
            $categoryDto->totals[$periodKey] = 0;
        }

        return $categoryDto;
    }

    /**
     * @param string $date
     * @param string $periodType
     * @return string
     */
    protected function findPeriodKey(string $date, string $periodType): string
    {
        switch ($periodType) {
            case self::PERIOD_TYPE_MONTH:
                return substr($date, 0, 7);

            default:
                return $date;
        }
    }

    /**
     * @param int $categoryId
     * @return Category[]
     * @throws NotFoundException
     */
    protected function findCategories(int $categoryId): array
    {
        $parent = $this->categoryService->find($categoryId);
        if (!$parent) {
            throw new NotFoundException('Category not found');
        }

        $subCategories = $this->categoryService->getSubCategories($categoryId);
        $result[$parent->getId()] =  $parent;
        foreach ($subCategories as $subCategory) {
            $result[$subCategory->getId()] = $subCategory;
        }

        return $result;
    }
}