<?php
/**
 * Created by PhpStorm.
 * User: npestcova
 * Date: 1/2/2018
 * Time: 2:30 PM
 */

namespace Application\Service\Factory;

use Application\Service\ImportService;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class ImportServiceFactory extends AbstractServiceFactory
    implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return ImportService|object
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container,
                             $requestedName, array $options = null)
    {
        $entityManager = $this->getEntityManager($container);
        $service = new ImportService($entityManager);

        return $service;
    }
}