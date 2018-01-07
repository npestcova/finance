<?php
/**
 * Created by PhpStorm.
 * User: npestcova
 * Date: 1/4/2018
 * Time: 4:19 PM
 */

namespace Application\Service;


use Application\Dto\Transaction\TransactionSearchDto;
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
     * @return ViewInfoDto[]
     */
    public function findTransactions(TransactionSearchDto $filter)
    {
        $transactions = $this->transactionRepository->findTransactions($filter);

        $result = [];
        /** @var Transaction $transaction */
        foreach ($transactions as $transaction) {
            $viewInfo = $transaction->getViewInfo();
            $result[] = $viewInfo;
        }

        return $result;
    }

    public function getTransactions()
    {
        $repository = $this->entityManager->getRepository(Transaction::class);
        $records = $repository->findBy(['date' => '2017-11-01']);

        return $records;
    }
}