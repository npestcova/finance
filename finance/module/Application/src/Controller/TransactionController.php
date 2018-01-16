<?php
/**
 * Created by PhpStorm.
 * User: npestcova
 * Date: 1/4/2018
 * Time: 4:17 PM
 */

namespace Application\Controller;


use Application\Dto\Transaction\TransactionSearchDto;
use Application\Dto\Transaction\BulkChangeTransactionsDto;
use Application\Service\AccountService;
use Application\Service\CategoryService;
use Application\Service\TransactionService;
use Finance\Date;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class TransactionController extends AbstractActionController
{
    /**
     * @var TransactionService
     */
    protected $transactionService;
    /**
     * @var CategoryService
     */
    protected $categoryService;
    /**
     * @var AccountService
     */
    protected $accountService;

    public function __construct($transactionService, $categoryService, $accountService)
    {
        $this->transactionService = $transactionService;
        $this->categoryService = $categoryService;
        $this->accountService = $accountService;
    }

    public function indexAction()
    {
        $categoriesList = $this->categoryService->getCategoryNames();
        $accountsList = $this->accountService->getAccountNames();
        $dateFrom = Date::getViewDateFromTimestamp(strtotime('-1 month'));
        $dateTo = Date::getViewDateFromTimestamp(time());

        return new ViewModel([
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'categories' => $categoriesList,
            'accounts' => $accountsList,
        ]);
    }

    public function loadAction()
    {
        $filter = new TransactionSearchDto();
        $filter->dateFrom = Date::getDbDate($this->params()->fromPost('date_from', ''));
        $filter->dateTo = Date::getDbDate($this->params()->fromPost('date_to', ''));
        $filter->categoryId = (int) $this->params()->fromPost('category_id', CategoryService::NO_FILTER);
        $filter->accountId = (int) $this->params()->fromPost('account_id', AccountService::NO_FILTER);
        $filter->description = trim(strip_tags($this->params()->fromPost('description', '')));

        $result = $this->transactionService->findTransactions($filter);

        return new JsonModel([
            'transactions' => $result->transactions,
            'total' => '$' . number_format($result->total, 2),
        ]);
    }
	
	public function saveAction()
	{
		$inputDto = new BulkChangeTransactionsDto();
		$inputDto->ids = array_keys(array_filter($this->params()->fromPost('id', [])));
		$inputDto->categoryId = (int) $this->params()->fromPost('new_category_id', CategoryService::NO_FILTER);
		
        $this->transactionService->bulkChangeTransactions($inputDto);

        return $this->forward()->dispatch('Application\Controller\TransactionController', [
            'action' => 'load',
        ] + $this->params()->fromPost());
	}
}