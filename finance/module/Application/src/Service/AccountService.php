<?php
/**
 * Created by PhpStorm.
 * User: npestcova
 * Date: 1/7/2018
 * Time: 12:54 PM
 */

namespace Application\Service;


use Application\Entity\Account;
use Application\Repository\AccountRepository;
use Doctrine\ORM\EntityManager;

class AccountService extends AbstractService
{
    const NO_FILTER = -1;

    /**
     * @var AccountRepository
     */
    protected $accountRepository;

    /**
     * TransactionService constructor.
     * @param EntityManager $entityManager
     */
    public function __construct($entityManager)
    {
        parent::__construct($entityManager);

        $this->accountRepository = $entityManager->getRepository(Account::class);
    }


    /**
     * @return string[]
     */
    public function getAccountNames()
    {
        $accounts = $this->accountRepository->findBy([], ['name' => 'ASC']);

        $result = [
            self::NO_FILTER => '-- ALL --',
        ];

        /** @var Account $account */
        foreach ($accounts as $account) {
            $result[$account->getId()] = $account->getName();
        }

        return $result;
    }
}