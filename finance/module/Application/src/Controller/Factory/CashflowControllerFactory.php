<?php
/**
 * Created by PhpStorm.
 * User: npestcova
 * Date: 2/4/2018
 * Time: 1:35 PM
 */

namespace Application\Controller\Factory;


use Application\Controller\CashflowController;
use Application\Service\TransactionService;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class CashflowControllerFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return CashflowController|object
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $transactionService = $container->get(TransactionService::class);

        // Instantiate the controller and inject dependencies
        return new CashflowController(
            $transactionService
        );
    }
}