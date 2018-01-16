<?php
/**
 * Created by PhpStorm.
 * User: npestcova
 * Date: 1/4/2018
 * Time: 4:19 PM
 */

namespace Application\Service;


use Application\Dto\Transaction\BulkChangeTransactionsDto;
use Application\Dto\Transaction\TransactionSearchDto;
use Application\Dto\Transaction\TransactionSearchResultDto;
use Application\Dto\Transaction\ViewInfoDto;
use Application\Entity\Transaction;
use Application\Repository\TransactionRepository;
use Doctrine\ORM\EntityManager;

class TransactionService extends AbstractService
{
    /**
     * @var TransactionRepository
     */
    protected $transactionRepository;

    /**
     * TransactionService constructor.
     * @param EntityManager $entityManager
     */
    public function __construct($entityManager)
    {
        parent::__construct($entityManager);

        $this->transactionRepository = $entityManager->getRepository(Transaction::class);
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
}