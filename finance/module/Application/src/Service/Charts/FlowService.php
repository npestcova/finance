<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: npestcova
 * Date: 8/9/2019
 * Time: 10:53 PM
 */

namespace Application\Service\Charts;


use Application\Service\AbstractService;
use Application\Service\CategoryService;
use Application\Service\TransactionService;
use Doctrine\ORM\EntityManager;

class FlowService extends AbstractService
{
    /**
     * @var CategoryService
     */
    protected $categoryService;

    /**
     * @var TransactionService
     */
    protected $transactionService;

    public function __construct(
        EntityManager $entityManager,
        CategoryService $categoryService,
        TransactionService $transactionService
    ) {
        parent::__construct($entityManager);

        $this->categoryService = $categoryService;
        $this->transactionService = $transactionService;
    }

    /**
     * @param string $noFilterString
     * @return array
     */
    public function getCategoryNames(string $noFilterString): array
    {
        return $this->categoryService->getCategoryNames(false, $noFilterString);
    }
}