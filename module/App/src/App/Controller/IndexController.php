<?php
/**
 * Created by PhpStorm.
 * User: stanislav.yordanov
 * Date: 15-9-14
 * Time: 10:05
 */

namespace App\Controller;


use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class IndexController extends AbstractActionController {

    private $storage, $authService;
    /**
     * @return Auth
     */
    public function getAuthStorage() {

        if(!$this->storage) {
            $this->storage = $this->getServiceLocator()
                ->get('AppAuthSessionStorage');
        }

        return $this->storage;
    }

    /**
     * @return AuthenticationService
     */
    public function getAuthService() {

        if(!$this->authService) {
            $this->authService = $this->getServiceLocator()
                ->get('AppAuthService');
        }

        return $this->authService;
    }

    public function indexAction() {

//var_dump($this->getServiceLocator()->get('AppAuthService')->getStorage());

        var_dump($this->getServiceLocator()->get('AppAuthService')->getStorage()->getAuthenticationExpirationTime());
        var_dump(time());
//        die;
//
//        $authStorage = $this->getAuthStorage();
//        var_dump($authStorage->getAuthenticationExpirationTime());
//
//        if($this->getAuthStorage()->isExpiredAuthenticationTime()) {
//            $this->getAuthStorage()->clearAuthenticationExpirationTime();
//            $this->getAuthStorage()->forgetMe();
//            $this->getAuthService()->clearIdentity();
//        }




        print_r($this->identity());
//        print_r($this->getServiceLocator()->get('AuthService')->getStorage()->read()); die;
        return new ViewModel();
    }

    public function testAction() {

        return new ViewModel();
    }
} 