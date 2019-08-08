<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: npestcova
 * Date: 8/7/2019
 * Time: 9:39 PM
 */

namespace Application\Controller;


use Application\Service\CategoryService;
use Application\Service\TransactionService;
use Finance\Date;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class ChartsController extends AbstractActionController
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
     * ChartsController constructor.
     * @param TransactionService $transactionService
     * @param CategoryService $categoryService
     */
    public function __construct($transactionService, $categoryService)
    {
        $this->transactionService = $transactionService;
        $this->categoryService = $categoryService;
    }

    public function flowChartAction()
    {
        $periods = Date::getMonthYearArray(5);
        $categoriesList = $this->categoryService->getCategoryNames(false, 'Please select a category');

        return new ViewModel([
            'periods' => $periods,
            'fromMonth' => date("Y-m", strtotime('-1 year')),
            'toMonth' => date("Y-m"),
            'categories' => $categoriesList,
        ]);
    }
}