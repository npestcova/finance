<?php
/**
 * Created by PhpStorm.
 * User: npestcova
 * Date: 1/9/2018
 * Time: 9:20 PM
 */

namespace Application\Controller\Factory;

use Application\Controller\KeywordController;
use Application\Service\KeywordService;
use Application\Service\CategoryService;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class KeywordControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $categoryService = $container->get(CategoryService::class);
        $keywordService = $container->get(KeywordService::class);

        // Instantiate the controller and inject dependencies
        return new KeywordController(
            $keywordService,
            $categoryService
        );
    }
}