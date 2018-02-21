<?php
/**
 * Created by PhpStorm.
 * User: npestcova
 * Date: 2/4/2018
 * Time: 1:34 PM
 */

namespace Application\Controller;


use Application\Dto\Transaction\GetTotalsByCategoryInputDto;
use Application\Dto\Transaction\GetMonthlyTotalsDto;
use Application\Service\TransactionService;
use Finance\Date;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class CashflowController extends AbstractActionController
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
        $years = range(date('Y'), 2012, -1);
        $selectedYear = date("Y");
        $months = Date::getMonthsNames();
        $selectedMonth = date('m');

        return new ViewModel([
            'years' => $years,
            'selectedYear' => $selectedYear,
            'months' => $months,
            'selectedMonth' => $selectedMonth,
        ]);
    }

    public function getAction()
    {
        $categoryTotalsDto = new GetTotalsByCategoryInputDto();
        $categoryTotalsDto->showHierarchy = true;
        $startDate = $this->params()->fromQuery('period', date("Y-m")) . '-01';
        $categoryTotalsDto->startDate = $startDate;
        $categoryTotalsDto->endDate = date("Y-m-t", strtotime($startDate));
        $categoryTotalsDto->excludeCashFlow = $this->params()->fromQuery('excludeTransfers', 0);
        $categoryTotalsDto->type = $this->params()->fromQuery('type', '');
        $rows = $this->transactionService->getTotalsByCategory($categoryTotalsDto);

        return new JsonModel([
            'rows' => array_values($rows),
        ]);
    }

    public function monthlyTotalsAction()
    {
        $totalsDto = new GetMonthlyTotalsDto();
        $totalsDto->startDate = date("Y-m-01", strtotime('-1 year'));
        $totalsDto->endDate = date("Y-m-t");

        $rows = $this->transactionService->getMonthlyTotals($totalsDto);

        return new JsonModel([
            'rows' => array_values($rows),
        ]);
    }
}