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

    public function indexAction() {
//        print_r($this->getServiceLocator()->get('AuthService')->getStorage()->read()); die;
        return new ViewModel();
    }

    public function testAction() {

        return new ViewModel();
    }
} 