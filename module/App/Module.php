<?php
namespace App;

use App\Storage\Auth;
use Zend\Authentication\Adapter\DbTable\CredentialTreatmentAdapter;
use Zend\Authentication\AuthenticationService;

class Module
{
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
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

    public function getServiceConfig() {

        return array(
            'factories' => array(
                'AppStorageAuth' => function ($sm) {

                    return new Auth('user_auth');
                },
                'AuthService' => function ($sm) {

                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $dbTableAuthAdapter = new CredentialTreatmentAdapter($dbAdapter,
                        'user', 'email', 'password', 'MD5(?)'); //'MD5(?) AND active = 1'

                    $authService = new AuthenticationService();
                    $authService->setAdapter($dbTableAuthAdapter);
                    $authService->setStorage($sm->get('AppStorageAuth'));

                    /**
                     * @var $authService AuthenticationService
                     */
                    return $authService;
                },
            ),
        );
    }

}
