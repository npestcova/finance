<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: npestcova
 * Date: 8/7/2019
 * Time: 9:39 PM
 */

namespace Application\Controller;


use Application\Dto\Charts\Flow\ChartDataDto;
use Application\Service\Charts\FlowService;
use Finance\Date;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\JsonModel;
use Laminas\View\Model\ViewModel;

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
        $monthFrom = $this->params()->fromQuery('date_from', '');
        $monthTo = $this->params()->fromQuery('date_to', '');
        $categoryId = (int) $this->params()->fromQuery('category_id', 0);
        $error = '';

        try {
            $dateFrom = $monthFrom ? Date::getDbDate($monthFrom): Date::getDbDate('now');
            $dateTo = $monthTo ? Date::getLastDateOfMonth($monthTo): Date::getDbDate('now');
            $data = $this->flowService->getChartData($dateFrom, $dateTo, $categoryId);
        } catch (\Exception $e) {
            $data = new ChartDataDto();
            $error = $e->getMessage();
        }

        return new JsonModel([
            'periods' => $data->periods,
            'categories' => $data->categories,
            'error' => $error,
        ]);
    }
}