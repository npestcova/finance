<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: npestcova
 * Date: 8/7/2019
 * Time: 9:40 PM
 */

namespace Application\Controller\Factory;


use Application\Controller\ChartsController;
use Application\Service\CategoryService;
use Application\Service\TransactionService;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class ChartsControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $categoryService = $container->get(CategoryService::class);
        $transactionService = $container->get(TransactionService::class);

        // Instantiate the controller and inject dependencies
        return new ChartsController(
            $transactionService,
            $categoryService
        );
    }
}