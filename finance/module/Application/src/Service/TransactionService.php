<?php
/**
 * Created by PhpStorm.
 * User: npestcova
 * Date: 1/4/2018
 * Time: 4:19 PM
 */

namespace Application\Service;


use Application\Dto\Transaction\BulkChangeTransactionsDto;
use Application\Dto\Transaction\CategoryTotalDto;
use Application\Dto\Transaction\GetTotalsByCategoryInputDto;
use Application\Dto\Transaction\TransactionSearchDto;
use Application\Dto\Transaction\TransactionSearchResultDto;
use Application\Dto\Transaction\ViewInfoDto;
use Application\Entity\Category;
use Application\Entity\Transaction;
use Application\Repository\CategoryRepository;
use Application\Repository\TransactionRepository;
use Doctrine\ORM\EntityManager;
use Finance\Date;

class TransactionService extends AbstractService
{
    /**
     * @var TransactionRepository
     */
    protected $transactionRepository;

    /**
     * @var CategoryRepository
     */
    protected $categoryRepository;

    /**
     * TransactionService constructor.
     * @param EntityManager $entityManager
     */
    public function __construct($entityManager)
    {
        parent::__construct($entityManager);

        $this->transactionRepository = $entityManager->getRepository(Transaction::class);
        $this->categoryRepository = $entityManager->getRepository(Category::class);
    }

    /**
     * @param TransactionSearchDto $filter
     * @return TransactionSearchResultDto
     */
    public function findTransactions(TransactionSearchDto $filter)
    {
        $transactions = $this->transactionRepository->findTransactions($filter);

        $result = new TransactionSearchResultDto();
        $result->transactions = [];
        $result->total = 0;

        /** @var Transaction $transaction */
        foreach ($transactions as $transaction) {
            $viewInfo = $transaction->getViewInfo();
            $result->transactions[] = $viewInfo;
            $result->total += $viewInfo->amount;
        }

        $result->total = round($result->total, 2);
        return $result;
    }

    public function getTransactions()
    {
        $repository = $this->entityManager->getRepository(Transaction::class);
        $records = $repository->findBy(['date' => '2017-11-01']);

        return $records;
    }

    public function bulkChangeTransactions(BulkChangeTransactionsDto $inputDto)
    {
        $ids = [];
        foreach ($inputDto->ids as $id) {
            $id = (int) $id;
            if ($id > 0) {
                $ids[$id] = $id;
            }
        }
        if (empty($ids)) {
            return;
        }

        $inputDto->ids = $ids;
        $inputDto->categoryId = (int) $inputDto->categoryId;

        $this->transactionRepository->bulkChangeTransactions($inputDto);
    }

    /**
     * @param GetTotalsByCategoryInputDto $inputDto
     * @return CategoryTotalDto[]
     */
    public function getTotalsByCategory(GetTotalsByCategoryInputDto $inputDto)
    {
        $totals = $this->transactionRepository->findTotalsByCategory($inputDto);

        /** @var Category[] $categories */
        $categories = $inputDto->categoryIds
            ? $this->categoryRepository->findBy(['id' => $inputDto->categoryIds])
            : $this->categoryRepository->findAll();

        /** @var Category[] $categoriesById */
        $categoriesById = [];
        foreach ($categories as $category) {
            $categoriesById[$category->getId()] = $category;
        }

        $parentCategories = [];
        foreach ($totals as $row) {
            $categoryId = $row['category_id'];
            $amount = $row['total'];

            $mainCategoryId = $categoryId;
            $subCategoryId = 0;

            $parent = null;
            $subCategory = null;

            if (isset($categoriesById[$categoryId])) {
                $parent = $inputDto->showHierarchy
                    ? $categoriesById[$categoryId]->getParent()
                    : null;
                if ($parent) {
                    $mainCategoryId = $parent->getId();
                    $subCategoryId = $categoryId;
                    $subCategory = $categoriesById[$categoryId];
                } else {
                    $parent = $categoriesById[$categoryId];
                }
            }

            if (!isset($parentCategories[$mainCategoryId])) {
                $parentTotal = new CategoryTotalDto();
                $parentTotal->categoryId = $mainCategoryId;
                $parentTotal->categoryName = $parent->getName();
                $parentTotal->startDate = $inputDto->startDate;
                $parentTotal->endDate = $inputDto->endDate;
                $parentTotal->amount = 0;
                $parentTotal->subCategories = [];

                $parentCategories[$mainCategoryId] = $parentTotal;
            }

            $parentCategories[$mainCategoryId]->amount += $amount;

            if (!$subCategoryId) {
                continue;
            }

            if (!isset($parentCategories[$mainCategoryId]->subCategories[$subCategoryId])) {
                $subCategoryTotal = new CategoryTotalDto();
                $subCategoryTotal->categoryId = $mainCategoryId;
                $subCategoryTotal->categoryName = $parent->getName();
                $subCategoryTotal->startDate = $inputDto->startDate;
                $subCategoryTotal->endDate = $inputDto->endDate;
                $subCategoryTotal->amount = 0;

                $parentCategories[$mainCategoryId]->subCategories[$subCategoryId] = $subCategoryTotal;
            }
            $parentCategories[$mainCategoryId]->subCategories[$subCategoryId]->amount += $amount;
        }

        return $parentCategories;
    }

    public function getPocketMoneyBalance()
    {
        $categories = [
            'Nick' => 97,
            'Home' => 98,
            'Nata' => 99,
            'Alex' => 100,
            'Baby' => 107
        ];
        $categoryNames = array_flip($categories);

        $inputDto = new GetTotalsByCategoryInputDto();
        $inputDto->categoryIds = array_values($categories);
        $inputDto->showHierarchy = false;
        $inputDto->startDate = '2016-10-01';
        $inputDto->endDate = Date::getDbDate('now');

        $rows = $this->getTotalsByCategory($inputDto);
        foreach ($rows as &$row) {
            if (!isset($categoryNames[$row->categoryId])) {
                continue;
            }
            $row->categoryName = $categoryNames[$row->categoryId];
        }
        return array_values($rows);]
    }
}