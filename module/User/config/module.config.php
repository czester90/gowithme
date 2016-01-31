<?php

return array(
    'view_manager' => array(
        'strategies' => array(
            'ViewJsonStrategy',
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'user/user' => 'User\Controller\UserController'
        ),
    ),
    'service_manager' => array(
        'aliases' => array(
            'user_zend_db_adapter' => 'Zend\Db\Adapter\Adapter',
        ),
    ),
    'router' => array(
        'routes' => array(
            'login' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/api/user/login/',
                    'defaults' => array(
                        'controller' => 'user/user',
                        'action' => 'login',
                    ),
                ),
            ),
            'register' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/api/user/register/',
                    'defaults' => array(
                        'controller' => 'user/user',
                        'action' => 'register',
                    ),
                ),
            ),
            'token' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/api/user/token/:token',
                    'defaults' => array(
                        'controller' => 'user/user',
                        'action' => 'token',
                    ),
                ),
            )
        ),
    ),
);
