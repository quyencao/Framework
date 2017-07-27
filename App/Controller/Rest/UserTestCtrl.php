<?php
/**
 * Created by PhpStorm.
 * User: LaptopTCC
 * Date: 7/27/2017
 * Time: 9:55 AM
 */

namespace App\Controller\Rest;

use App\Model\UserTestMapper;
use Lib\Json;

class UserTestCtrl extends RestCtrl
{
    protected $userTestMapper;

    protected function init()
    {
        parent::init();
        $this->userTestMapper = UserTestMapper::makeInstance();
    }

    function getUsersTest() {
        $this->requireLogin();

        $usersTest = $this->userTestMapper->makeInstance()
                ->getAll();

        $this->resp->setBody(Json::encode($usersTest));
    }

    function deleteUsersTest() {
        $this->requireLogin();

        $arrID = $this->restInput('id', array());
        $this->userTestMapper->deleteUsers($arrID);
        $this->resp->setBody(Json::encode(array(
            'status' => true
        )));
    }

    function updateUsersTest($id) {
        $this->requireLogin();
        $user = $this->restInput();

//        $this->resp->setBody(Json::encode($user['deleted']));

        $id = $this->userTestMapper->updateUser($id, $user);

        $this->resp->setBody(Json::encode(array(
            'status' => true,
            'id'     => $id
        )));
    }
}