<?php
/**
 * Created by PhpStorm.
 * User: npestcova
 * Date: 1/9/2018
 * Time: 9:16 PM
 */

namespace Application\Controller;


use Application\Service\CategoryService;
use Application\Service\KeywordService;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\JsonModel;
use Laminas\View\Model\ViewModel;

class KeywordController extends AbstractActionController
{
    /**
     * @var KeywordService
     */
    protected $keywordService;

    /**
     * @var CategoryService
     */
    protected $categoryService;

    public function __construct($keywordService, $categoryService)
    {
        $this->keywordService = $keywordService;
        $this->categoryService = $categoryService;
    }

    public function indexAction()
    {
        return new ViewModel();
    }

    public function loadAction()
    {
        $keywords = $this->keywordService->getAllKeywordsInfo();

        return new JsonModel([
            'keywords' => $keywords
        ]);
    }

    public function newAction()
    {
        $categoriesList = $this->categoryService->getCategoryNames();

        return new ViewModel([
            'categories' => $categoriesList,
        ]);
    }

    public function createAction()
    {
        $newKeywords = $this->params()->fromPost('new', []);
        $this->keywordService->addNewKeywords($newKeywords);

        $this->redirect()->toUrl('/keyword');
    }
}