<?php
/**
 * Created by PhpStorm.
 * User: npestcova
 * Date: 1/3/2018
 * Time: 4:36 PM
 */

namespace Application\Controller;

use Application\Service\ImportService;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;

class ImportController extends AbstractActionController
{
    /**
     * @var ImportService
     */
    protected $importService;

    public function __construct($importService)
    {
        $this->importService = $importService;
    }

    public function indexAction()
    {
    }

    public function csvFilesAction()
    {
        $directory = 'C:/www/finance/import/';
        $this->importService->importCsvFiles($directory);
        die('ok');
    }
}