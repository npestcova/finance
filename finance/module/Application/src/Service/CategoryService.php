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
     * @return string[]
     */
    public function getCategoryNames()
    {
        $categories = $this->categoryRepository->findBy([], ['name' => 'ASC']);

        $result = [
            self::NO_FILTER => '-- ALL --',
            0 => Category::DEFAULT_NAME
        ];

        //@todo: load parent-child

        /** @var Category $category */
        foreach ($categories as $category) {
            $result[$category->getId()] = $category->getName();
        }

        return $result;
    }
}