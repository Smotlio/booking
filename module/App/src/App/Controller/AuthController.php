<?php
/**
 * Created by PhpStorm.
 * User: stanislav.yordanov
 * Date: 15-9-14
 * Time: 13:46
 */

namespace App\Controller;


use App\Model\User;
use App\Model\UserTable;
use App\Form\AuthForm;
use Zend\Authentication\AuthenticationService;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class AuthController extends AbstractActionController {

    const SUCCESS_URL = 'home';

    protected $form;
    protected $storage;
    protected $authService;
    protected $userTable;

    /**
     * @return UserTable
     */
    public function getUserTable() {

        if(!$this->userTable) {
            $sm = $this->getServiceLocator();
            $this->userTable = $sm->get('App\Model\UserTable');
        }
        return $this->userTable;
    }

    public function getAuthStorage() {

        if(!$this->storage) {
            $this->storage = $this->getServiceLocator()
                ->get('AppStorageAuth');
        }

        return $this->storage;
    }

    /**
     * @return AuthenticationService
     */
    public function getAuthService() {

        if(!$this->authService) {
            $this->authService = $this->getServiceLocator()
                ->get('AuthService');
        }

        return $this->authService;
    }

    /**
     * @return AuthForm
     */
    public function getForm() {

        if(!$this->form) {

            $this->form = new AuthForm();
        }

        return $this->form;
    }

    public function loginAction() {

        //if already login, redirect to success page
        if($this->getAuthService()->hasIdentity()) {
            return $this->redirect()->toRoute(self::SUCCESS_URL);
        }

        $form = $this->getForm();
        $form->get('submit')->setValue('Login');

        $authErrors = array();
        /**
         * @var $request \Zend\Http\Request
         */
        $request = $this->getRequest();
        if($request->isPost()) {
            $form->setData($request->getPost());

            if($form->isValid()) {

                $user = new User();
                $user->exchangeArray($form->getData());

                //check authentication...
                $this->getAuthService()->getAdapter()
                    ->setIdentity($request->getPost('email'))
                    ->setCredential($request->getPost('password'));

                $authentication = $this->getAuthService()->authenticate();
                $authErrors = $authentication->getMessages();

                if($authentication->isValid()) {
//print_r($this->getAuthService()->getIdentity()); die;
                    //check if it has rememberMe :
                    if($request->getPost('rememberme') == 1) {
                        $this->getAuthStorage()
                            ->setRememberMe(1);
                        //set storage again
                        $this->getAuthService()->setStorage($this->getAuthStorage());
                    }
                    $this->getAuthService()->getStorage()->write($user);

                    $this->redirect()->toRoute(self::SUCCESS_URL);
                }
            }
        }

        return new ViewModel(array(
            'form' => $form,
            'messages' => $authErrors
        ));
    }

    public function registerAction() {

        //if already login, redirect to success page
        if($this->getAuthService()->hasIdentity()) {
            return $this->redirect()->toRoute(self::SUCCESS_URL);
        }

        $form = $this->getForm();
        $form->get('submit')->setValue('Register');

        $authErrors = array();
        /**
         * @var $request \Zend\Http\Request
         */
        $request = $this->getRequest();
        if($request->isPost()) {
            $form->setData($request->getPost());

            if($form->isValid()) {

                $user = new User();
                $user->exchangeArray($form->getData());

                if($this->getUserTable()->hasUser(array('email' => $user->email))) {

                    $authErrors[] = 'Another user has registered with this email.';

                } else {

                    $this->getUserTable()->saveUser($user);

                    //check authentication...
                    $this->getAuthService()->getAdapter()
                        ->setIdentity($request->getPost('email'))
                        ->setCredential($request->getPost('password'));

                    $result = $this->getAuthService()->authenticate();
                    $authErrors = $result->getMessages();

                    if($result->isValid()) {

                        //check if it has rememberMe :
                        if($request->getPost('rememberme') == 1) {
                            $this->getAuthStorage()
                                ->setRememberMe(1);
                            //set storage again
                            $this->getAuthService()->setStorage($this->getAuthStorage());
                        }
                        $this->getAuthService()->getStorage()->write($request->getPost('email'));

                        $this->redirect()->toRoute(self::SUCCESS_URL);
                    }
                }

            }
        }

        return new ViewModel(array(
            'form' => $form,
            'messages' => $authErrors
        ));
    }

    public function logoutAction() {

        $this->getAuthStorage()->forgetMe();
        $this->getAuthService()->clearIdentity();

        $this->flashmessenger()->addMessage("You've been logged out");
        return $this->redirect()->toRoute('login');
    }

}