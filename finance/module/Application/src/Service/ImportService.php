<?php
/**
 * Created by PhpStorm.
 * User: npestcova
 * Date: 1/2/2018
 * Time: 2:28 PM
 */

namespace Application\Service;


use Doctrine\ORM\EntityManager;

class ImportService extends AbstractService
{
    /**
     * Менеджер сущностей.
     * @var EntityManager
     */
    private $entityManager;

    /**
     * ImportService constructor.
     * @param $entityManager
     */
    public function __construct($entityManager)
    {
        $this->entityManager = $entityManager;
    }

    
}