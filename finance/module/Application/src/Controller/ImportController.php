<?php
/**
 * Created by PhpStorm.
 * User: npestcova
 * Date: 1/3/2018
 * Time: 4:36 PM
 */

namespace Application\Controller;

use Application\Service\ImportService;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

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
        $directory = 'C:/Home/finance/PowerWallet/';
        $this->importService->importFilesFromDirectory($directory);
        die('ok');
    }
}