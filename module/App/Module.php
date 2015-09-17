<?php
namespace App;

use App\Storage\Auth;
use Zend\Authentication\Adapter\DbTable\CredentialTreatmentAdapter;
use Zend\Authentication\AuthenticationService;
use Zend\ModuleManager\ModuleManager;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;


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
                'AppAuthSessionStorage' => function($sm) {

                    $config = $sm->get('config');

                    $authSessionStorage = new Auth(Auth::SESSION_CONTAINER_NAME);
                    $authSessionStorage->setAllowedIdleTimeInSeconds($config['session']['config']['authentication_expiration_time']);

                    /**
                     * @var $authSessionStorage Auth
                     */
                    return $authSessionStorage;
                },
                'AppAuthService' => function($sm) {

                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $dbTableAuthAdapter = new CredentialTreatmentAdapter($dbAdapter,
                        'user', 'email', 'password', 'MD5(?)'); //'MD5(?) AND active = 1'

                    $authService = new AuthenticationService();
                    $authService->setAdapter($dbTableAuthAdapter);
                    $authService->setStorage($sm->get('AppAuthSessionStorage'));

                    /**
                     * @var $authService AuthenticationService
                     */
                    return $authService;
                },

//                'AppStorageAuth' => function ($sm) {
//
//                    return new Auth('user_auth');
//                },
//                'AuthService' => function ($sm) {
//
//                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
//                    $dbTableAuthAdapter = new CredentialTreatmentAdapter($dbAdapter,
//                        'user', 'email', 'password', 'MD5(?)'); //'MD5(?) AND active = 1'
//
//                    $authService = new AuthenticationService();
//                    $authService->setAdapter($dbTableAuthAdapter);
//                    $authService->setStorage($sm->get('AppStorageAuth'));
//
//                    /**
//                     * @var $authService AuthenticationService
//                     */
//                    return $authService;
//                },
            ),
        );
    }

    protected $whitelist = array(
        'App\Controller\Auth'
    );

    public function onBootstrap(MvcEvent $e)
    {

        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);

        // add event
        $eventManager->attach('dispatch', array($this, 'checkLogin'));

    }

    public function checkLogin($e)
    {

        $auth   = $e->getApplication()->getServiceManager()->get("Zend\Authentication\AuthenticationService");
        $target = $e->getTarget();
        $match  = $e->getRouteMatch();

        $controller = $match->getParam('controller');

        if( !in_array($controller, $this->whitelist)){

            if($auth->getStorage()->isExpiredAuthenticationTime()) {

                $auth->getStorage()->clearAuthenticationExpirationTime();
                $auth->getStorage()->forgetMe();
                $auth->clearIdentity();

                return $target->redirect()->toUrl('/login');
            } else {

                $auth->getStorage()->setAuthenticationExpirationTime();
            }

//            if( !$auth->hasIdentity() ){
//                return $target->redirect()->toUrl('/login');
//            }
        }

    }

    public function init(ModuleManager $mm)
    {
        $mm->getEventManager()->getSharedManager()->attach(__NAMESPACE__, 'dispatch', function($e) {
            $e->getTarget()->layout('app/layout');
        });
    }

}
