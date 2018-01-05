<?php
/**
 * Created by PhpStorm.
 * User: npestcova
 * Date: 1/4/2018
 * Time: 4:17 PM
 */

namespace Application\Controller;


use Application\Service\TransactionService;
use Zend\Mvc\Controller\AbstractActionController;
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
}