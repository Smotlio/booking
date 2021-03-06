<?php
/**
 * Created by PhpStorm.
 * User: stanislav.yordanov
 * Date: 15-9-9
 * Time: 16:41
 */

namespace App\Model;


use Zend\Db\TableGateway\TableGateway;

class UserTable {

    protected $tableGateway;

    public function __construct(TableGateway $tableGateway) {

        $this->tableGateway = $tableGateway;
    }

    public function fetchAll() {

        $resultSet = $this->tableGateway->select();
        return $resultSet;
    }

    public function getUser($id) {

        $id = (int)$id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if(!$row) {
            throw new \Exception("Could not find row $id");
        }

        return $row;
    }

    public function saveUser(User $user) {

        $data = array(
            'email' => $user->email,
            'password' => $user->password,
            'rememberme' => $user->rememberme,
            'active' => $user->active,
        );

        $id = (int)$user->id;
        if($id == 0) {

            $data['password'] = md5($user->password);
            $this->tableGateway->insert($data);
        } else {
            if($this->getUser($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('User id does not exist');
            }
        }
    }

    public function deleteUser($id) {

        $this->tableGateway->delete(array('id' => (int)$id));
    }

    public function hasUser($column) {

        $rowset = $this->tableGateway->select($column);
        $count = $rowset->count();

        if($count == 0) {
            return false;
        } else {
            return true;
        }
    }

}