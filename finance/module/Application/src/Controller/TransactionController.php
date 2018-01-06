<?php
/**
 * Created by PhpStorm.
 * User: npestcova
 * Date: 1/4/2018
 * Time: 4:17 PM
 */

namespace Application\Controller;


use Application\Dto\Transaction\TransactionSearchDto;
use Application\Service\TransactionService;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class TransactionController extends AbstractActionController
{
    /**
     * @var TransactionService
     */
    protected $transactionService;

    public function __construct($transactionService)
    {
        $this->transactionService = $transactionService;
    }

    public function indexAction()
    {
        return new ViewModel();
    }

    public function loadAction()
    {
        $filter = new TransactionSearchDto();
        $filter->dateFrom = '2017-11-01';
        $filter->dateTo = '2017-12-02';

        $transactions = $this->transactionService->findTransactions($filter);

        return new JsonModel([
            'transactions' => $transactions
        ]);
    }
}