<?php
/**
 * Created by PhpStorm.
 * User: stanislav.yordanov
 * Date: 15-9-9
 * Time: 13:41
 */

namespace Auth\Controller;


use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class SuccessController extends AbstractActionController {

    public function indexAction() {

        if(!$this->getServiceLocator()
            ->get('AuthService')->hasIdentity()
        ) {
            return $this->redirect()->toRoute('login');
        }

        return new ViewModel();
    }
}