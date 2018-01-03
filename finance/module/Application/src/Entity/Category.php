<?php
/**
 * Created by PhpStorm.
 * User: npestcova
 * Date: 1/3/2018
 * Time: 2:37 PM
 */

namespace Application\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="category")
 */
class Category extends AbstractEntityEntity
{
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
     * @var Category[]
     *
     * @OneToMany(targetEntity="\Application\Entity\Category", mappedBy="parent")
     */
    protected $children;

    /**
     * @var Category
     *
     * @ManyToOne(targetEntity="\Application\Entity\Category", inversedBy="children")
     * @JoinColumn(name="parent_id", referencedColumnName="id")
     */
    protected $parent;

    public function __construct($inputDto)
    {
        parent::__construct($inputDto);

        $this->children = new ArrayCollection();
    }

}