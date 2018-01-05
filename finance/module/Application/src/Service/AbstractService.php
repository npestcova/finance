<?php
/**
 * Created by PhpStorm.
 * User: npestcova
 * Date: 1/2/2018
 * Time: 2:29 PM
 */

namespace Application\Service;

use Doctrine\ORM\EntityManager;

class AbstractService
{
    /**
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