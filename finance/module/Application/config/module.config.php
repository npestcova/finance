<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Zend\Router\Http\Literal;
use Zend\Router\Http\Segment;
use Zend\ServiceManager\Factory\InvokableFactory;

use Doctrine\DBAL\Driver\PDOMySql\Driver as PDOMySqlDriver;

return [
    'router' => [
        'routes' => [
            'home' => [
                'type' => Literal::class,
                'options' => [
                    'route'    => '/',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
            'application' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/application[/:action]',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
            'import' => [
                'type' => Segment::class,
                'options' => [
                    'route'    => '/import[/:action]',
                    'defaults' => [
                        'controller' => Controller\ImportController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
            'transaction' => [
                'type' => Segment::class,
                'options' => [
                    'route'    => '/transaction[/:action]',
                    'defaults' => [
                        'controller' => Controller\TransactionController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
            'cashflow' => [
                'type' => Segment::class,
                'options' => [
                    'route'    => '/cashflow[/:action]',
                    'defaults' => [
                        'controller' => Controller\CashflowController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
            'budget' => [
                'type' => Segment::class,
                'options' => [
                    'route'    => '/budget[/:action]',
                    'defaults' => [
                        'controller' => Controller\BudgetController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
            'keywords' => [
                'type' => Segment::class,
                'options' => [
                    'route'    => '/keyword[/:action]',
                    'defaults' => [
                        'controller' => Controller\KeywordController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
        ],
    ],
    'controllers' => [
        'factories' => [
            Controller\IndexController::class => InvokableFactory::class,
            Controller\ImportController::class => Controller\Factory\ImportControllerFactory::class,
            Controller\TransactionController::class => Controller\Factory\TransactionControllerFactory::class,
            Controller\CashflowController::class => Controller\Factory\CashflowControllerFactory::class,
            Controller\BudgetController::class => Controller\Factory\BudgetControllerFactory::class,
            Controller\KeywordController::class => Controller\Factory\KeywordControllerFactory::class,
        ],
    ],
    'view_helpers' => [
        'factories' => [
            View\Helper\Menu::class => View\Helper\Factory\MenuFactory::class,
            View\Helper\Breadcrumbs::class => InvokableFactory::class,
        ],
        'aliases' => [
            'mainMenu' => View\Helper\Menu::class,
            'pageBreadcrumbs' => View\Helper\Breadcrumbs::class,
        ],
    ],
    'view_manager' => [
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => [
            'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
            'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
        ],
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
        'strategies' => [
            'ViewJsonStrategy',
        ],
    ],
    'doctrine' => [
        'driver' => [
            __NAMESPACE__ . '_driver' => [
                'class' => AnnotationDriver::class,
                'cache' => 'array',
                'paths' => [__DIR__ . '/../src/Entity']
            ],
            'orm_default' => [
                'drivers' => [
                    __NAMESPACE__ . '\Entity' => __NAMESPACE__ . '_driver'
                ]
            ]
        ]
    ]
];
