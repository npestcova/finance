<?php
/**
 * Created by PhpStorm.
 * User: npestcova
 * Date: 1/3/2018
 * Time: 2:30 PM
 */

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="\Application\Repository\AccountRepository")
 * @ORM\Table(name="account")
 */
class Account extends AbstractEntity
{
    const DEFAULT_NAME = 'Unknown';

    /**
     * @var integer
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(name="id", type="integer", nullable=false)
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
    protected $name;

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}