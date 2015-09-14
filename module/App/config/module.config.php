<?php
return array(

    'router' => array(
        'routes' => array(
            'home' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/',
                    'defaults' => array(
                        'controller' => 'App\Controller\Index',
                        'action'     => 'index',
                    ),
                ),
            ),
            // The following is a route to simplify getting started creating
            // new controllers and actions without needing to create a new
            // module. Simply drop new controllers in, and you can access them
            // using the path /application/:controller/:action
            'search' => array(
                'type'    => 'Literal',
                'options' => array(
                    'route'    => '/search',
                    'defaults' => array(
                        'controller' => 'App\Controller\Index',
                        'action'        => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'default' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/[:action]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                                'controller' => 'App\Controller\Index',
                                'action'        => 'index',
                            ),
                        ),
                    ),
                ),
            ),

//            Problem: kogato napisha /search tursi metod v AuthController
//            'auth' => array(
//                'type'    => 'Segment',
//                'options' => array(
//                    'route'    => '/[:action]',
//                    'constraints' => array(
//                        'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
//                    ),
//                    'defaults' => array(
//                        'controller' => 'App\Controller\Auth',
//                    ),
//                ),
//                'may_terminate' => false,
//            ),
            'login' => array(
                'type' => 'Literal',
                'options' => array(
                    'route'    => '/login',
                    'defaults' => array(
                        'controller' => 'App\Controller\Auth',
                        'action'     => 'login',
                    ),
                ),
            ),
            'register' => array(
                'type' => 'Literal',
                'options' => array(
                    'route'    => '/register',
                    'defaults' => array(
                        'controller' => 'App\Controller\Auth',
                        'action'     => 'register',
                    ),
                ),
            ),
            'logout' => array(
                'type' => 'Literal',
                'options' => array(
                    'route'    => '/logout',
                    'defaults' => array(
                        'controller' => 'App\Controller\Auth',
                        'action'     => 'logout',
                    ),
                ),
            ),

        ),
    ),

    'controllers' => array(
        'invokables' => array(
            'App\Controller\Index' => 'App\Controller\IndexController',
            'App\Controller\Auth' => 'App\Controller\AuthController'
        ),
    ),

    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => array(
            'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
//            'layout/layout'           => __DIR__ . '/../../layout/layout.phtml',
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),

    'service_manager' => array(
        'abstract_factories' => array(
            'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
            'Zend\Log\LoggerAbstractServiceFactory',
        ),
        'aliases' => array(
            'translator' => 'MvcTranslator',
        ),
    ),

    'translator' => array(
        'locale' => 'en_US',
        'translation_file_patterns' => array(
            array(
                'type'     => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern'  => '%s.mo',
            ),
        ),
    ),
    // Placeholder for console routes
    'console' => array(
        'router' => array(
            'routes' => array(
            ),
        ),
    ),
);