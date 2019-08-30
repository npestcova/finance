<?php
/**
 * Created by PhpStorm.
 * User: npestcova
 * Date: 1/7/2018
 * Time: 12:54 PM
 */

namespace Application\Service;


use Application\Entity\Category;
use Application\Repository\CategoryRepository;
use Doctrine\ORM\EntityManager;

class CategoryService extends AbstractService
{
    const NO_FILTER = -1;

    /**
     * @var CategoryRepository
     */
    protected $categoryRepository;

    /**
     * TransactionService constructor.
     * @param EntityManager $entityManager
     */
    public function __construct($entityManager)
    {
        parent::__construct($entityManager);

        $this->categoryRepository = $entityManager->getRepository(Category::class);
    }

    /**
     * @param bool $includeSubcategories
     * @param string $noFilter
     * @return array
     */
    public function getCategoryNames($includeSubcategories = true, $noFilter = '-- ALL --')
    {
        $categories = $this->categoryRepository->findByParent(null, ['name' => 'ASC']);

        $result = [
            self::NO_FILTER => $noFilter,
            0 => Category::DEFAULT_NAME
        ];

        //@todo: load parent-child

        /** @var Category $category */
        foreach ($categories as $category) {
            $result[$category->getId()] = $category->getName();
            if ($includeSubcategories) {
                $subCategories = $category->getSubcategoryNames('-- ');
                if (!empty($subCategories)) {
                    $result = $result + $subCategories;
                }
            }
        }

        return $result;
    }

    /**
     * @param int $categoryId
     * @return Category[]
     */
    public function getSubCategories(int $categoryId): array
    {
        $rowset = $this->categoryRepository->findByParent($categoryId, ['name' => 'ASC']);
        return $rowset;
    }

    /**
     * @param int $categoryId
     * @return Category|null
     */
    public function find(int $categoryId): ?Category
    {
        return $this->categoryRepository->find($categoryId);
    }
}