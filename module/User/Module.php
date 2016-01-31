<?php

namespace User;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\ServiceProviderInterface;

class Module implements
    AutoloaderProviderInterface,
    ConfigProviderInterface,
    ServiceProviderInterface
{
    public function onBootstrap($e)
    {
        $app     = $e->getParam('application');
        $sm      = $app->getServiceManager();
        $options = $sm->get('user_module_options');

        if ($options->getEnableDefaultEntities()) {
            $chain = $sm->get('doctrine.driver.odm_default');
        }
    }
  
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function getConfig($env = null)
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getControllerPluginConfig()
    {
        return array(
            'factories' => array(
                'UserAuthentication' => function ($sm) {
                    $serviceLocator = $sm->getServiceLocator();
                    $authService = $serviceLocator->get('user_auth_service');
                    $authAdapter = $serviceLocator->get('User\Authentication\Adapter\AdapterChain');
                    $controllerPlugin = new Controller\Plugin\UserAuthentication;
                    $controllerPlugin->setAuthService($authService);
                    $controllerPlugin->setAuthAdapter($authAdapter);
                    return $controllerPlugin;
                },
            ),
        );
    }

    public function getViewHelperConfig()
    {
        return array(
            'factories' => array(
                'UserIdentity' => function ($sm) {
                    $locator = $sm->getServiceLocator();
                    $viewHelper = new View\Helper\UserIdentity;
                    $viewHelper->setAuthService($locator->get('user_auth_service'));
                    return $viewHelper;
                }
            ),
        );

    }

    public function getServiceConfig()
    {
        return array(
            'aliases' => array(
                'user_doctrine_em' => 'doctrine.documentmanager.odm_default',
            ),
            'invokables' => array(
                'User\Authentication\Adapter\Db' => 'User\Authentication\Adapter\Db',
                'User\Authentication\Storage\Db' => 'User\Authentication\Storage\Db',
                'User\Form\Login'                => 'User\Form\Login',
                'user_user_service'              => 'User\Service\User',
                'user_register_form_hydrator'    => 'Zend\Stdlib\Hydrator\ClassMethods',
            ),
            'factories' => array(
              
                'user_remember' => function($sm){
                    return new \User\Authentication\Adapter\RememberMe('user_remember'); 
                },

                'user_module_options' => function ($sm) {
                    $config = $sm->get('Config');
                    return new Options\ModuleOptions(isset($config['user']) ? $config['user'] : array());
                },
                // We alias this one because it's User's instance of
                // Zend\Authentication\AuthenticationService. We don't want to
                // hog the FQCN service alias for a Zend\* class.
                'user_auth_service' => function ($sm) {
                    return new \Zend\Authentication\AuthenticationService(
                        $sm->get('User\Authentication\Storage\Db'),
                        $sm->get('User\Authentication\Adapter\AdapterChain')
                    );
                },

                'User\Authentication\Adapter\AdapterChain' => 'User\Authentication\Adapter\AdapterChainServiceFactory',

                'user_login_form' => function($sm) {
                    $options = $sm->get('user_module_options');
                    $form = new Form\Login(null, $options);
                    $form->setInputFilter(new Form\LoginFilter($options));
                    return $form;
                },

                'user_register_form' => function ($sm) {
                    $options = $sm->get('user_module_options');
                    $form = new Form\Register(null, $options);
                    $form->setInputFilter(new Form\RegisterFilter(
                        new Validator\NoRecordExists(array(
                            'mapper' => $sm->get('user_user_mapper'),
                            'key'    => 'email'
                        )),
                        new Validator\NoRecordExists(array(
                            'mapper' => $sm->get('user_user_mapper'),
                            'key'    => 'username'
                        )),
                        $options
                    ));
                    return $form;
                },

                'user_change_password_form' => function($sm) {
                    $options = $sm->get('user_module_options');
                    $form = new Form\ChangePassword(null, $sm->get('user_module_options'));
                    $form->setInputFilter(new Form\ChangePasswordFilter($options));
                    return $form;
                },

                'user_change_email_form' => function($sm) {
                    $options = $sm->get('user_module_options');
                    $form = new Form\ChangeEmail(null, $sm->get('user_module_options'));
                    $form->setInputFilter(new Form\ChangeEmailFilter(
                        $options,
                        new Validator\NoRecordExists(array(
                            'mapper' => $sm->get('user_user_mapper'),
                            'key'    => 'email'
                        ))
                    ));
                    return $form;
                },

                'user_user_hydrator' => function ($sm) {
                    $hydrator = new \Zend\Stdlib\Hydrator\ClassMethods();
                    return $hydrator;
                },

                'user_user_mapper' => function ($sm) {
                    return new \User\Mapper\UserMongoDB(
                        $sm->get('user_doctrine_em'),
                        $sm->get('user_module_options')
                    );
                },
            ),
        );
    }
}
