<?php
/**
 * Created by PhpStorm.
 * User: npestcova
 * Date: 1/3/2018
 * Time: 3:24 PM
 */

namespace Application\Service\Factory;

use Interop\Container\ContainerInterface;

class AbstractServiceFactory
{

    /**
     * @param ContainerInterface $container
     * @return mixed
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    protected function getEntityManager(ContainerInterface $container)
    {
        $entityManager = $container->get('doctrine.entitymanager.orm_default');

        return $entityManager;
    }
}