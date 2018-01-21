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
 * @ORM\Entity(repositoryClass="\Application\Repository\CategoryRepository")
 * @ORM\Table(name="category")
 */
class Category extends AbstractEntity
{
    const DEFAULT_NAME = 'Uncategorized';
    const NAME_DELIMITER = '. ';

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
     * @var string
     *
     * @ORM\Column(name="exclude_from_cashflow", type="integer", length=1, nullable=false)
     */
    protected $excludeFromCashflow;

    /**
     * @var Category[]
     *
     * @ORM\OneToMany(targetEntity="\Application\Entity\Category", mappedBy="parent")
     */
    protected $children;

    /**
     * @var Category
     *
     * @ORM\ManyToOne(targetEntity="\Application\Entity\Category", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", nullable=true)
     */
    protected $parent;

    public function __construct($inputDto)
    {
        parent::__construct($inputDto);

        $this->children = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function getFullName()
    {
        $name = '';
        if ($this->parent) {
            $name = $this->parent->getFullName() . self::NAME_DELIMITER;
        }
        $name .= $this->name;

        return $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    public function getSubcategoryNames($prefix = '')
    {
        $result = [];
        foreach ($this->children as $subCategory) {
            $result[$subCategory->getId()] = $prefix . $subCategory->getName();
        }

        return $result;
    }

    /**
     * @return Category
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @return bool
     */
    public function needExcludeFromCashFlow()
    {
        return $this->excludeFromCashflow ? true : false;
    }
}