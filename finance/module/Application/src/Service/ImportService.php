<?php
/**
 * Created by PhpStorm.
 * User: npestcova
 * Date: 1/2/2018
 * Time: 2:28 PM
 */

namespace Application\Service;


use Application\Dto\AccountInfoDto;
use Application\Dto\CategoryInfoDto;
use Application\Dto\Transaction\TransactionInfoDto;
use Application\Entity\Account;
use Application\Entity\Category;
use Application\Entity\Transaction;
use Application\Repository\AccountRepository;
use Application\Repository\CategoryRepository;
use Doctrine\ORM\EntityManager;

class ImportService extends AbstractService
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var CategoryRepository
     */
    private $categoryRepository;

    /**
     * @var AccountRepository
     */
    private $accountRepository;

    /**
     * ImportService constructor.
     * @param $entityManager
     */
    public function __construct($entityManager)
    {
        $this->entityManager = $entityManager;
        $this->categoryRepository = $this->entityManager->getRepository(Category::class);
        $this->accountRepository = $this->entityManager->getRepository(Account::class);
    }

    /**
     * @param $dirName
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function importFilesFromDirectory($dirName)
    {
        $files = scandir($dirName);

        foreach ($files as $fileName) {
            $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
            if ($fileExtension != 'csv') {
                continue;
            }

            $this->importFile($dirName . $fileName);
            echo $fileName . ' done <br/>';
        }
    }

    /**
     * @param $fileName
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function importFile($fileName)
    {
        $handle = fopen($fileName, "r");

        if (!$handle) {
            echo 'Cannot open file ' . $fileName . '<br/>';
            return;
        }

        $row = 1;

        $columnDate = 0;
        $columnAccountName = 1;
        $columnDescription = 2;
        $columnCategoryName = 3;
        $columnAmount = 4;

        fgetcsv($handle, 1000, ",");     // skip the titles
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            $transactionInfo = new TransactionInfoDto();
            $transactionInfo->date = (string) $data[$columnDate];
            $transactionInfo->description = (string) $data[$columnDescription];
            $transactionInfo->amount = (float) $data[$columnAmount];
            $transactionInfo->account = $this->getAccountByName($data[$columnAccountName]);
            $transactionInfo->category = $this->getCategoryByName($data[$columnCategoryName]);

            $transaction = new Transaction($transactionInfo);

            $this->entityManager->persist($transaction);
            $this->entityManager->flush();
        }
        fclose($handle);
    }

    /**
     * @param $name
     * @return Account
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    protected function getAccountByName($name)
    {
        $name = trim($name);

        $account = $this->accountRepository->findOneByName($name);
        if (!$account) {
            $accountInfo = new AccountInfoDto();
            $accountInfo->name = $name;
            $account = new Account($accountInfo);

            $this->entityManager->persist($account);
            $this->entityManager->flush();
        }

        return $account;
    }

    /**
     * @param $name
     * @return Category
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    protected function getCategoryByName($name)
    {
        if ($name == 'Uncategorized') {
            return null;
        }

        $category = $this->categoryRepository->findOneByName($name);
        if (!$category) {
            $categoryInfo = new CategoryInfoDto();
            $categoryInfo->name = $name;

            $category = new Category($categoryInfo);

            $this->entityManager->persist($category);
            $this->entityManager->flush();
        }

        return $category;
    }
}