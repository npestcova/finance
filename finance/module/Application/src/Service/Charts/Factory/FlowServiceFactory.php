<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: npestcova
 * Date: 8/9/2019
 * Time: 10:54 PM
 */

namespace Application\Service\Charts\Factory;


use Application\Service\CategoryService;
use Application\Service\Charts\FlowService;
use Application\Service\Factory\AbstractServiceFactory;
use Application\Service\TransactionService;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class FlowServiceFactory extends AbstractServiceFactory
    implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return FlowService|object
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container,
                             $requestedName, array $options = null)
    {
        $entityManager = $this->getEntityManager($container);
        $categoryService = $container->get(CategoryService::class);
        $transactionService = $container->get(TransactionService::class);
        $service = new FlowService(
            $entityManager,
            $categoryService,
            $transactionService
        );

        return $service;
    }
}
{

}