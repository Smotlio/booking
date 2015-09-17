<?php
/**
 * Created by PhpStorm.
 * User: stanislav.yordanov
 * Date: 15-9-14
 * Time: 13:53
 */

namespace App\Storage;


use Zend\Authentication\Storage\Session;
use Zend\Session\Container;
use Zend\Session\ManagerInterface as SessionManager;

class Auth extends Session {

    const SESSION_CONTAINER_NAME = 'appAuth';
    const SESSION_VARIABLE_NAME = 'authContainerVariable';

    private $allowedIdleTimeInSeconds = 1800;

    public function setRememberMe($rememberMe = 0, $time = 1209600) {

        if($rememberMe == 1) {
            $this->session->getManager()->rememberMe($time);
        }
    }

    public function forgetMe() {

        $authSession = new Container(self::SESSION_CONTAINER_NAME);
        $authSession->getManager()->forgetMe();
    }

    /**
     * this method gets current time, adds timeout for authentication and stores value in session variable
     */
    public function setAuthenticationExpirationTime() {

        $expirationTime = time() + $this->allowedIdleTimeInSeconds;

        $authSession = new Container(self::SESSION_CONTAINER_NAME);

        if($authSession->offsetExists(self::SESSION_VARIABLE_NAME)) {
            $authSession->offsetUnset(self::SESSION_VARIABLE_NAME);
        }

        $authSession->offsetSet(self::SESSION_VARIABLE_NAME, $expirationTime);
    }

    /**
     * checks if authentication should expire or not
     * @return bool
     */
    public function isExpiredAuthenticationTime() {

        $authSession = new Container(self::SESSION_CONTAINER_NAME);

        if($authSession->offsetExists(self::SESSION_VARIABLE_NAME)) {
            $expirationTime = $authSession->offsetGet(self::SESSION_VARIABLE_NAME);
            return $expirationTime < time();
        }
        return false;
    }

    /**
     * removes expiration time from session. This method is called when isExpiredAuthenticationTime will return true or on user logout action
     */
    public function clearAuthenticationExpirationTime() {

        $authSession = new Container(self::SESSION_CONTAINER_NAME);
        $authSession->offsetUnset(self::SESSION_VARIABLE_NAME);
    }

    /**
     * returns authentication expiration time (therefor we can present to user when he will be logged out
     * @return mixed
     */
    public function getAuthenticationExpirationTime() {

        $authSession = new Container(self::SESSION_CONTAINER_NAME);
        return $authSession->offsetGet(self::SESSION_VARIABLE_NAME);
    }

    /**
     * setter for idle time with value get ie. from config
     * @param int $allowedIdleTimeInSeconds
     */
    public function setAllowedIdleTimeInSeconds($allowedIdleTimeInSeconds) {

        $this->allowedIdleTimeInSeconds = $allowedIdleTimeInSeconds;
    }

} 