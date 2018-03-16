<?php

namespace Application\Controller\Factory;

use Application\Controller\BudgetController;
use Application\Service\TransactionService;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class BudgetControllerFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return BudgetController|object
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $transactionService = $container->get(TransactionService::class);

        // Instantiate the controller and inject dependencies
        return new BudgetController(
            $transactionService
        );
    }
}