<?php

namespace Lib\SQL;

class Entity {

    //tránh lỗi notice khi gọi biến chưa khai báo
    function __get($name) {
        return null;
    }

    function __construct($rawData = null) {
        if (is_array($rawData)) {
            foreach ($rawData as $k => $v) {
                $this->{$k} = $v;
            }
        }
    }

}
