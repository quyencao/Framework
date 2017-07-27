<?php

namespace App\Model;

use Lib\SQL\Entity;

class UserEntity extends Entity {

    /** @var GroupEntity */
    public $groups = array();

    /** @var String[] */
    public $permissions = array();

    function __construct($rawData = null) {
        parent::__construct($rawData);
        $this->stt = $this->stt ? true : false;
        $this->isAdmin = $this->isAdmin ? true : false;

        if (isset($rawData['groups'])) {
            if (isset($rawData['groups']['var'])) {
                $rawData['groups'] = $rawData['groups']['var'];
            }
            foreach ($rawData['groups'] as $group) {
                $this->groups[] = new GroupEntity($group);
            }
        }

        if (isset($rawData['permissions'])) {
            foreach ($rawData['permissions'] as $pem) {
                $this->permissions[] = $pem;
            }
        }
    }

    function hasPermission($pem) {
        return in_array($pem, $this->permissions);
    }

    function inGroup($groupCode) {
        foreach ($this->groups as $group) {
            if ($groupCode == $group->groupCode) {
                return true;
            }
        }
        return false;
    }

}
