<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: npestcova
 * Date: 8/7/2019
 * Time: 9:39 PM
 */

namespace Application\Controller;


use Application\Service\Charts\FlowService;
use Finance\Date;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class ChartFlowController extends AbstractActionController
{
    /**
     * @var FlowService
     */
    protected $flowService;

    /**
     * ChartFlowController constructor.
     * @param $flowService
     */
    public function __construct($flowService)
    {
        $this->flowService = $flowService;
    }

    public function indexAction()
    {
        $periods = Date::getMonthYearArray(5);
        $categoriesList = $this->flowService->getCategoryNames('Please select a category');

        return new ViewModel([
            'periods' => $periods,
            'fromMonth' => date("Y-m", strtotime('-1 year')),
            'toMonth' => date("Y-m"),
            'categories' => $categoriesList,
        ]);
    }

    public function getAction()
    {
        $dateFrom = $this->params()->fromQuery('date_from', '');
        $dateTo = $this->params()->fromQuery('date_to', '');
        $periods = Date::getRangeMonthTitles($dateFrom . '-01', $dateTo . '-01');

        return new JsonModel([
            'periods' => $periods,
            'categories' => [
                [
                    'name' => 'Name 1',
                    'totals' => [
                        '2018-09' => 100,
                        '2018-10' => 200,
                        '2018-11' => 100,
                        '2018-12' => 400,
                        '2019-01' => 100,
                        '2019-02' => 200,
                        '2019-03' => 100,
                        '2019-04' => 200,
                        '2019-05' => 100,
                        '2019-06' => 500,
                        '2019-07' => 100,
                        '2019-08' => -100,
                    ]
                ]
            ]
        ]);
    }
}