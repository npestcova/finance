<?php

namespace Application\Controller;


use Application\Service\TransactionService;
use Finance\Date;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\JsonModel;
use Laminas\View\Model\ViewModel;

class BudgetController extends AbstractActionController
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
}