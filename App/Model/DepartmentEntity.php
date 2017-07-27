<?php

namespace App\Model;

use Lib\SQL\Entity;

class DepartmentEntity extends Entity {

    /** @var DepartmentEntity */
    public $parent;

    /** @var UserEntity */
    public $users = array();

    /** @var DepartmentEntity */
    public $deps = array();

    /** @var DepartmentEntity */
    public $ancestors = array();

    function __construct($rawData = null) {
        parent::__construct($rawData);
        $this->stt = (bool) $this->stt;
    }

}
