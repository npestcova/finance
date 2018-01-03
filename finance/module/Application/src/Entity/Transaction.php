<?php
/**
 * Created by PhpStorm.
 * User: npestcova
 * Date: 1/3/2018
 * Time: 1:24 PM
 */

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="transaction")
 *
 * @ORM\Entity(repositoryClass="Application\Repository\TransactionRepository")
 */
class Transaction extends AbstractEntityEntity
{
    /**
     * @var integer
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(name="id")
     */
    protected $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="date", nullable=true)
     */
    protected $date;

    /**
     * @var Account
     *
     * @ORM\ManyToOne(targetEntity="\Application\Entity\Account")
     * @ORM\JoinColumn(name="account_id", referencedColumnName="id")
     */
    protected $account;

    /**
     * @var Category
     *
     * @ORM\ManyToOne(targetEntity="\Application\Entity\Category")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id")
     */
    protected $category;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255, nullable=false)
     */
    protected $description;

    /**
     * @var float
     *
     * @ORM\Column(name="amount", type="decimal", precision=6, scale=2, nullable=false)
     */
    protected $amount = 0;
}