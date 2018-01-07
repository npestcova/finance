<?php
/**
 * Created by PhpStorm.
 * User: npestcova
 * Date: 1/4/2018
 * Time: 4:18 PM
 */

namespace Application\Controller\Factory;

use Application\Controller\TransactionController;
use Application\Service\AccountService;
use Application\Service\CategoryService;
use Application\Service\TransactionService;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class TransactionControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $transactionService = $container->get(TransactionService::class);
        $categoryService = $container->get(CategoryService::class);
        $accountService = $container->get(AccountService::class);

        // Instantiate the controller and inject dependencies
        return new TransactionController(
            $transactionService,
            $categoryService,
            $accountService
        );
    }
}