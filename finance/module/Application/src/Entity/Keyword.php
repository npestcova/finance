<?php
/**
 * Created by PhpStorm.
 * User: npestcova
 * Date: 1/3/2018
 * Time: 1:24 PM
 */

namespace Application\Entity;

use Application\Dto\Keyword\ViewInfoDto;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="keyword")
 *
 * @ORM\Entity(repositoryClass="\Application\Repository\KeywordRepository")
 */
class Keyword extends AbstractEntity
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
     * @var string
     *
     * @ORM\Column(name="keyword", type="string", nullable=false)
     */
    protected $keyword;

    /**
     * @var integer
     *
     * @ORM\Column(name="category_id", type="integer", nullable=false)
     */
    protected $categoryId;

    /**
     * @var Category
     *
     * @ORM\ManyToOne(targetEntity="\Application\Entity\Category")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id")
     */
    protected $category;

    /**
     * @return ViewInfoDto
     */
    public function getViewInfo()
    {
        $info = new ViewInfoDto();
        $info->id = $this->id;
        $info->keyword = $this->keyword;
        $info->categoryId = $this->categoryId;
        $info->categoryName = $this->category
        ? $this->category->getFullName()
        : Category::DEFAULT_NAME;

        return $info;
    }

    /**
     * @param string $string
     * @return bool
     */
    public function matches($string)
    {
        return stripos($string, $this->keyword) !== false;
    }

    /**
     * @return int
     */
    public function getCategoryId()
    {
        return $this->categoryId;
    }

    /**
     * @param Category $category
     */
    public function setCategory($category)
    {
        $this->category = $category;
    }


}