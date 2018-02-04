<?php
/**
 * Created by PhpStorm.
 * User: npestcova
 * Date: 2/4/2018
 * Time: 1:34 PM
 */

namespace Application\Controller;


use Application\Service\TransactionService;
use Finance\Date;
use Zend\Mvc\Controller\AbstractActionController;
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
}