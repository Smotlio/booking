<?php
namespace Auth\Controller;

use Application\Model\User;
use Application\Model\UserTable;
use Auth\Form\UserForm;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

//use Zend\Form\Annotation\AnnotationBuilder;
//use SanAuth\Model\User;

class AuthController extends AbstractActionController {

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
            $this->userTable = $sm->get('Application\Model\UserTable');
        }
        return $this->userTable;
    }

    public function getAuthService() {

        if(!$this->authService) {
            $this->authService = $this->getServiceLocator()
                ->get('AuthService');
        }

        return $this->authService;
    }

    public function getSessionStorage() {

        if(!$this->storage) {
            $this->storage = $this->getServiceLocator()
                ->get('MyAuthStorage');
        }

        return $this->storage;
    }

    public function getForm() {

        if(!$this->form) {

            $this->form = new UserForm();
        }

        return $this->form;
    }


    public function loginAction() {

        //if already login, redirect to success page
        if($this->getAuthService()->hasIdentity()) {
            return $this->redirect()->toRoute('success');
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
                //check authentication...
                $this->getAuthService()->getAdapter()
                    ->setIdentity($request->getPost('email'))
                    ->setCredential($request->getPost('password'));

                $authentication = $this->getAuthService()->authenticate();
                $authErrors = $authentication->getMessages();

                if($authentication->isValid()) {

                    //check if it has rememberMe :
                    if($request->getPost('rememberme') == 1) {
                        $this->getSessionStorage()
                            ->setRememberMe(1);
                        //set storage again
                        $this->getAuthService()->setStorage($this->getSessionStorage());
                    }
                    $this->getAuthService()->getStorage()->write($request->getPost('email'));

                    $this->redirect()->toRoute('success');
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
            return $this->redirect()->toRoute('success');
        }

        $form = $this->getForm();
        $form->get('submit')->setValue('Register');

        /**
         * @var $request \Zend\Http\Request
         */
        $request = $this->getRequest();
        if($request->isPost()) {
            $form->setData($request->getPost());

            if($form->isValid()) {

                $user = new User();
                $user->exchangeArray($form->getData());

                $this->getUserTable()->saveUser($user);

                $this->redirect()->toRoute('success');
//                //check authentication...
//                $this->getAuthService()->getAdapter()
//                    ->setIdentity($request->getPost('email'))
//                    ->setCredential($request->getPost('password'));
//
//                $result = $this->getAuthService()->authenticate();
//                $authErrors = $result->getMessages();
//
//                if($result->isValid()) {
//
//                    //check if it has rememberMe :
//                    if($request->getPost('rememberme') == 1) {
//                        $this->getSessionStorage()
//                            ->setRememberMe(1);
//                        //set storage again
//                        $this->getAuthService()->setStorage($this->getSessionStorage());
//                    }
//                    $this->getAuthService()->getStorage()->write($request->getPost('email'));
//
//                    $this->redirect()->toRoute('success');
//                }
            }
        }

        return new ViewModel(array(
            'form' => $form,
        ));
    }

    public function logoutAction() {

        $this->getSessionStorage()->forgetMe();
        $this->getAuthService()->clearIdentity();

        $this->flashmessenger()->addMessage("You've been logged out");
        return $this->redirect()->toRoute('login');
    }
}
