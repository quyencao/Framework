<?php

namespace App\Model;

use Lib\SQL\Entity;

class GroupEntity extends Entity {

    public $permissions = array();

    function __construct($rawData = null) {
        parent::__construct($rawData);
        $this->stt = (bool) $this->stt;
    }

}
