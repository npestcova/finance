<?php

namespace Application\Service\Factory;

use Application\Service\ImportService;
use Application\Service\KeywordService;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

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
        $keywordService = $container->get(KeywordService::class);
        $service = new ImportService($entityManager, $keywordService);

        return $service;
    }
}