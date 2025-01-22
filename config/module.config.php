<?php

return array(
    'controllers' => array(
        'invokables' => array(
            'Monevsdgs\Controller\Dashboard' => 'Monevsdgs\Controller\DashboardController',
            'Monevsdgs\Controller\Coding' => 'Monevsdgs\Controller\CodingController',
            'Monevsdgs\Controller\Output' => 'Monevsdgs\Controller\OutputController',
            'Monevsdgs\Controller\Monev' => 'Monevsdgs\Controller\MonevController',
        ),
    ),
    'router' => array(
        'routes' => array(
           'dashboard' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/monevsdgs/dashboard[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Monevsdgs\Controller\Dashboard',
                        'action' => 'index',
                    ),
                ),
            ),
           'coding' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/sdgs/coding[/:action][?:param1][=:param2]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'param1' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'param2' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Monevsdgs\Controller\Coding',
                        'action' => 'index',
                    ),
                ),
            ),
           'output' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/monevsdgs/output[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Monevsdgs\Controller\Output',
                        'action' => 'index',
                    ),
                ),
            ),
           'monev_output' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/monevsdgs/monev[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Monevsdgs\Controller\Monev',
                        'action' => 'index',
                    ),
                ),
            ),

        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'monevsdgs' => __DIR__ . '/../view'
        ),
        'strategies' => array(
            'ViewJsonStrategy',
        ),
    ),
);
