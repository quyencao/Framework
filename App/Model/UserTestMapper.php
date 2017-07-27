<?php
/**
 * Created by PhpStorm.
 * User: LaptopTCC
 * Date: 7/27/2017
 * Time: 9:58 AM
 */

namespace App\Model;

use Lib\SQL\Mapper;

class UserTestMapper extends Mapper
{

    function makeEntity($rawData)
    {
        return new UserTestEntity($rawData);
    }

    function tableName()
    {
        return 'cores_user_test';
    }

    function tableAlias()
    {
        return 'user_test';
    }

    function deleteUsers($id) {
        if (!is_array($id)) {
            $id = array($id);
        }
        foreach ($id as $i) {
            $this->db->Execute("UPDATE cores_user_test SET deleted=1 WHERE id=?", array($i));
        }
    }

    function updateUser($id, $data) {
        $update['fullName'] = arrData($data, 'fullName');
        $update['jobTitle'] = arrData($data, 'jobTitle');
        $update['depID'] = (int)arrData($data, 'depID');
        $update['account'] = arrData($data, 'account');
        $update['pass'] = arrData($data, 'pass');
        $update['deleted'] = (int)arrData($data, 'deleted');

        $this->db->StartTrans();
        $id = $this->replace($id, $update);

        if (!$id) {
            return false;
        }

        $this->db->CompleteTrans();

        return $id;
    }
}