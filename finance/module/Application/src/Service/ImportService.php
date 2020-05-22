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
use Finance\Date;

class ImportService extends AbstractService
{
    const MAPPING_TYPE_CHASE = 'chase';
    const MAPPING_TYPE_CHASE_CC = 'chase-cc';
    const MAPPING_TYPE_BA = 'ba';
    const MAPPING_TYPE_CAPITAL_ONE = 'cap';
    const MAPPING_TYPE_CAPITAL_ONE_MARKET = 'cap-market';
    const MAPPING_TYPE_BCU = 'bcu';
    const MAPPING_TYPE_FIDELITY = 'fidelity';

    const COLUMN_DATE = 'Date';
    const COLUMN_DESCRIPTION = 'Description';
    const COLUMN_AMOUNT = 'Amount';
    const COLUMN_ACCOUNT = 'Account';
    const COLUMN_CATEGORY = 'Category';
    const COLUMN_CREDIT = 'Credit';
    const COLUMN_DEBIT = 'Debit';

    /**
     * @var CategoryRepository
     */
    private $categoryRepository;

    /**
     * @var AccountRepository
     */
    private $accountRepository;

    /**
     * @var KeywordService
     */
    private $keywordService;

    /**
     * ImportService constructor.
     * @param $entityManager
     * @param KeywordService $keywordService
     */
    public function __construct($entityManager, $keywordService)
    {
        parent::__construct($entityManager);
        $this->categoryRepository = $this->entityManager->getRepository(Category::class);
        $this->accountRepository = $this->entityManager->getRepository(Account::class);
        $this->keywordService = $keywordService;
    }

    /**
     * @param $fileName
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function importFile($fileName, $mapping, $accountName)
    {
        $handle = fopen($fileName, "r");

        if (!$handle) {
            echo 'Cannot open file ' . $fileName . '<br/>';
            return;
        }

        $row = 1;

        $columnDate = isset($mapping[self::COLUMN_DATE]) ? $mapping[self::COLUMN_DATE] : null;
        $columnAccountName = isset($mapping[self::COLUMN_ACCOUNT]) ? $mapping[self::COLUMN_ACCOUNT] : null;
        $columnDescription = isset($mapping[self::COLUMN_DESCRIPTION]) ? $mapping[self::COLUMN_DESCRIPTION] : null;
        $columnCategoryName = isset($mapping[self::COLUMN_CATEGORY]) ? $mapping[self::COLUMN_CATEGORY] : null;
        $columnAmount = isset($mapping[self::COLUMN_AMOUNT]) ? $mapping[self::COLUMN_AMOUNT] : null;
        $columnDebit = isset($mapping[self::COLUMN_DEBIT]) ? $mapping[self::COLUMN_DEBIT] : null;
        $columnCredit = isset($mapping[self::COLUMN_CREDIT]) ? $mapping[self::COLUMN_CREDIT] : null;

        fgetcsv($handle, 1000, ",");     // skip the titles
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            if (empty($data)) {
                continue;
            }
            
            $transactionInfo = new TransactionInfoDto();
            $transactionInfo->date = Date::getDbDate((string) $data[$columnDate]);
            $transactionInfo->description = (string) $data[$columnDescription];

            if ($columnAmount) {
                $transactionInfo->amount = (float) $data[$columnAmount];
            } else {
                if ((float) $data[$columnDebit] != 0) {
                    $transactionInfo->amount = 0 - (float) $data[$columnDebit];
                } elseif ((float) $data[$columnCredit] != 0) {
                    $transactionInfo->amount = (float) $data[$columnCredit];
                }
            }
            $transactionInfo->account = $columnAccountName
                ? $this->getAccountByName($data[$columnAccountName])
                : $this->getAccountByName($accountName);
            $transactionInfo->category = $columnCategoryName
                ? $this->getCategoryByName($data[$columnCategoryName])
                : $this->getCategoryByDescription($transactionInfo->description);

            $transaction = new Transaction($transactionInfo);

            $this->entityManager->persist($transaction);
        }
        $this->entityManager->flush();
        fclose($handle);
    }

    public function importCsvFiles($dirName)
    {
        $files = scandir($dirName);

        foreach ($files as $fileName) {
            $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            if ($fileExtension != 'csv') {
                continue;
            }

            list($mappingType, $accountType, $tmp) = explode('_', $fileName);
            $mapping = $this->getCsvMapping($mappingType);
            if (!$mapping) {
                echo 'file ' . $fileName . ' mapping not found';
                continue;
            }
            $accountName = $this->getAccountNameByType($accountType);

            $this->importFile($dirName . $fileName, $mapping, $accountName);
            echo $fileName . ' done <br/>';
        }
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

    /**
     * @param string $description
     * @return Category|null
     */
    protected function getCategoryByDescription($description)
    {
        if (empty($description)) {
            return null;
        }

        $keywords = $this->keywordService->getAllKeywords();
        foreach ($keywords as $keyword) {
            if ($keyword->matches($description)) {
                return $keyword->getCategory();
            }
        }

        return null;
    }

    /**
     * @param $mappingType
     * @return null
     */
    public function getCsvMapping($mappingType)
    {
        $mapping[self::MAPPING_TYPE_CHASE] = [
            self::COLUMN_DATE => 1,
            self::COLUMN_DESCRIPTION => 2,
            self::COLUMN_AMOUNT => 3,
        ];
        $mapping[self::MAPPING_TYPE_CHASE_CC] = [
            self::COLUMN_DATE => 0,
            self::COLUMN_DESCRIPTION => 2,
            self::COLUMN_AMOUNT => 5,
        ];
        $mapping[self::MAPPING_TYPE_BA] = [		
            self::COLUMN_DATE => 0,
            self::COLUMN_DESCRIPTION => 2,
            self::COLUMN_AMOUNT => 4,
		];
        $mapping[self::MAPPING_TYPE_BCU] = [
            self::COLUMN_DATE => 1,
            self::COLUMN_DESCRIPTION => 7,
            self::COLUMN_AMOUNT => 4,		
		];
        $mapping[self::MAPPING_TYPE_CAPITAL_ONE] = [
            self::COLUMN_DATE => 0,
            self::COLUMN_DESCRIPTION => 3,
            self::COLUMN_DEBIT => 5,
            self::COLUMN_CREDIT => 6,
		];
		//$mapping[self::MAPPING_TYPE_CAPITAL_ONE] = [
        //    self::COLUMN_DATE => 2,
        //    self::COLUMN_DESCRIPTION => 4,
       //     self::COLUMN_DEBIT => 6,
        //    self::COLUMN_CREDIT => 7,
		//];
        $mapping[self::MAPPING_TYPE_CAPITAL_ONE_MARKET] = [
            self::COLUMN_DATE => 1,
            self::COLUMN_DESCRIPTION => 4,
            self::COLUMN_AMOUNT => 2,
        ];
//        $mapping[self::MAPPING_TYPE_CAPITAL_ONE_MARKET] = [
//            self::COLUMN_DATE => 4,
//            self::COLUMN_DESCRIPTION => 3,
//            self::COLUMN_DEBIT => 1,
//            self::COLUMN_CREDIT => 2,
//        ];
       // $mapping[self::MAPPING_TYPE_CAPITAL_ONE_MARKET] = [
       //     self::COLUMN_DATE => 7,
       //     self::COLUMN_DESCRIPTION => 10,
      //      self::COLUMN_AMOUNT => 8,
      //  ];
        $mapping[self::MAPPING_TYPE_FIDELITY] = [
            self::COLUMN_DATE => 0,
            self::COLUMN_DESCRIPTION => 2,
            self::COLUMN_AMOUNT => 4,
        ];

        if (isset($mapping[$mappingType])) {
            return $mapping[$mappingType];
        }

        return null;
    }

    /**
     * @param $type
     * @return mixed|string
     */
    public function getAccountNameByType($type)
    {
        $accountNames = [
            'checking' => 'Checking Account',
            'saving' => 'Saving',
            'cap' => 'Capital One',
            'money' => 'Capital Money Market',
            'amazon' => 'Amazon',
            'bcu' => 'BCU-checking',
            'bcu-rainy' => 'BCU-Rainy Day',
            'nick-bcu' => 'Nick BCU Checking',
            'nick-rainy' => 'Nick BCU RainyDay',
            'bankof' => 'Bank Of America',
            'fidelity' => 'Fidelity Credit Card',
        ];

        return isset($accountNames[$type])
            ? $accountNames[$type]
            : '';
    }
}