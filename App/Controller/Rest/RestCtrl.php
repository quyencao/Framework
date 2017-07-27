<?php

namespace App\Controller\Rest;

use App\Model\UserEntity;
use App\Model\UserMapper;

class RestCtrl extends \Lib\RestCtrl {

    /**
     * Không dùng trực tiếp biến này, phải dùng ->user()
     * @var \App\Model\UserEntity 
     */
    private $user;
    private $userSeed = array();

    protected function init() {
        parent::init();
        $this->userSeed = $this->session->get('user');
    }

    protected function requireLogin() {
        if (!$this->user() || !$this->user()->id) {
            http_response_code(401);
            echo 'requireLogin';
            die;
        }
    }

    /** @var \App\Model\UserEntity */
    protected function user() {
        if (!$this->userSeed) {
            return new UserEntity;
        }

        if (!$this->user) {
            $user = UserMapper::makeInstance()
                    ->filterID($this->userSeed['id'])
                    ->getEntity();
            if ($user->pass != $this->userSeed['pass']) {
                return new UserEntity;
            }

            $this->user = $user;
        }

        return $this->user;
    }

    protected function requireAdmin() {
        $this->requireLogin();

        if (!$this->user()->isAdmin) {
            http_response_code(403);
            echo 'requireAdmin';
            die;
        }
    }

}
