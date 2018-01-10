<?php
/**
 * Created by PhpStorm.
 * User: npestcova
 * Date: 1/9/2018
 * Time: 9:22 PM
 */

namespace Application\Service;


use Application\Dto\Keyword\ViewInfoDto;
use Application\Entity\Category;
use Application\Entity\Keyword;
use Application\Repository\CategoryRepository;
use Application\Repository\KeywordRepository;
use Doctrine\ORM\EntityManager;

class KeywordService extends AbstractService
{
    /**
     * @var KeywordRepository
     */
    protected $keywordRepository;

    /** @var CategoryRepository */
    protected $categoryRepository;

    /**
     * TransactionService constructor.
     * @param EntityManager $entityManager
     */
    public function __construct($entityManager)
    {
        parent::__construct($entityManager);

        $this->keywordRepository = $entityManager->getRepository(Keyword::class);
        $this->categoryRepository = $entityManager->getRepository(Category::class);
    }

    /**
     * @return ViewInfoDto[]
     */
    public function getAllKeywordsInfo()
    {
        $keywords = $this->keywordRepository->findAll();

        $result = [];
        /** @var Keyword $keyword */
        foreach ($keywords as $keyword) {
            $result[] = $keyword->getViewInfo();
        }

        return $result;
    }

    /**
     * @param $keywords
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function addNewKeywords($keywords)
    {
        $records = $this->categoryRepository->findAll();
        $categories = [];
        /** @var Category $category */
        foreach ($records as $category) {
            $categories[$category->getId()] = $category;
        }

        foreach ($keywords as $keywordData) {
            $string = trim($keywordData['keyword']);
            $categoryId = (int) $keywordData['category'];

            if (empty($string) || $categoryId <= 0) {
                continue;
            }

            if (empty($categories[$categoryId])) {
                continue;
            }

            $keywordDto = new ViewInfoDto();
            $keywordDto->keyword = $string;

            $keyword = new Keyword($keywordDto);
            $keyword->setCategory($categories[$categoryId]);

            $this->entityManager->persist($keyword);
        }

        $this->entityManager->flush();
    }
}