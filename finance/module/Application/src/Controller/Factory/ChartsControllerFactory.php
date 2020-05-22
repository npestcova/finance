<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: npestcova
 * Date: 8/7/2019
 * Time: 9:40 PM
 */

namespace Application\Controller\Factory;


use Application\Controller\ChartFlowController;
use Application\Service\Charts\FlowService;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class ChartsControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $flowService = $container->get(FlowService::class);

        // Instantiate the controller and inject dependencies
        return new ChartFlowController(
            $flowService
        );
    }
}