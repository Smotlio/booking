<?php
/**
 * Created by PhpStorm.
 * User: stanislav.yordanov
 * Date: 15-9-14
 * Time: 13:53
 */

namespace App\Storage;


use Zend\Authentication\Storage\Session;

class Auth extends Session {

    public function setRememberMe($rememberMe = 0, $time = 1209600) {

        if($rememberMe == 1) {
            $this->session->getManager()->rememberMe($time);
        }
    }

    public function forgetMe() {

        $this->session->getManager()->forgetMe();
    }
    
} 