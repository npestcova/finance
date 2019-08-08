<?php
namespace Application\Service;


/**
 * This service is responsible for determining which items should be in the main menu.
 * The items may be different depending on whether the user is authenticated or not.
 */
class NavManager
{
    /**
     * Auth service.
     * @var Zend\Authentication\Authentication
     */
    private $authService;
    
    /**
     * Constructs the service.
     */
    public function __construct($authService) 
    {
        $this->authService = $authService;
    }
    
    /**
     * This method returns menu items depending on whether user has logged in or not.
     */
    public function getMenuItems() 
    {
        $items = [];

        if ($this->authService->hasIdentity()) {
            $items[] = [
                'id' => 'home',
                'label' => 'Home',
                'link'  => 'home'
            ];

            $items[] = [
                'id' => 'transaction',
                'label' => 'Transactions',
                'link'  => 'transaction'
            ];

            $items[] = [
                'id' => 'cashflow',
                'label' => 'CashFlow',
                'link'  => 'cashflow'
            ];

            $items[] = [
                'id' => 'charts',
                'label' => 'Charts',
                'dropdown' => [
                    [
                        'id' => 'chart_flow',
                        'label' => 'Flow Chart',
                        'link' => 'chart_flow'
                    ],
                ]
            ];

            $items[] = [
                'id' => 'budget',
                'label' => 'Budget',
                'link'  => 'budget'
            ];

            $items[] = [
                'id' => 'keywords',
                'label' => 'Keywords',
                'link'  => 'keywords'
            ];

            $items[] = [
                'id' => 'import',
                'label' => 'Import',
                'link' => '',
                'dropdown'  => [
//                    'id' => 'csv-files',
//                    'label' => 'From Folder',
//                    'link'  => 'csv-files'
                ]
            ];

            $items[] = [
                'id' => 'logout',
                'label' => $this->authService->getIdentity(),
                'float' => 'right',
                'dropdown' => [
//                    [
//                        'id' => 'set',
//                        'label' => 'Settings',
//                        'link' => $url('application', ['action'=>'settings'])
//                    ],
                    [
                        'id' => 'logout',
                        'label' => 'Sign out',
                        'link' => 'logout'
                    ],
                ]
            ];

//            $items[] = [
//                'id' => 'admin',
//                'label' => 'Admin',
//                'dropdown' => [
//                    [
//                        'id' => 'users',
//                        'label' => 'Manage Users',
//                        'link' => $url('users')
//                    ]
//                ]
//            ];
        } else {
            $items[] = [
                'id' => 'login',
                'label' => 'Sign in',
                'link'  => 'login',
                'float' => 'right'
            ];
        }

        return $items;
    }
}


