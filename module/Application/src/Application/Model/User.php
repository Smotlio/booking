<?php
/**
 * Created by PhpStorm.
 * User: stanislav.yordanov
 * Date: 15-9-9
 * Time: 16:35
 */

namespace Application\Model;


class User {

    public $id;
    public $email;
    public $password;
    public $rememberme = 0;
    public $active = 0;

    public function exchangeArray($data) {

        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->email = (!empty($data['email'])) ? $data['email'] : null;
        $this->password = (!empty($data['password'])) ? $data['password'] : null;
        $this->rememberme = (!empty($data['rememberme'])) ? $data['rememberme'] : $this->rememberme;
        $this->active = (!empty($data['active'])) ? $data['active'] : $this->active;
    }

} 