<?php

namespace App\Model;

use Lib\SQL\Mapper;

class GroupMapper extends Mapper {

    protected $loadPermission;

    public function makeEntity($rawData) {
        $entity = new GroupEntity($rawData);
        if ($this->loadPermission) {
            $entity->permissions = $this->getPermissions($entity->id);
        }
        return $entity;
    }

    public function tableAlias() {
        return 'gp';
    }

    public function tableName() {
        return 'cores_group';
    }

    function __construct() {
        parent::__construct();
        $this->filterDeleted(false);
    }

    function filterDeleted($bool) {
        $this->where('gp.deleted=?', __FUNCTION__)->setParam($bool ? 1 : 0, __FUNCTION__);
        return $this;
    }

    function filterID($id) {
        $this->where('gp.id=?', __FUNCTION__)->setParam($id, __FUNCTION__);
        return $this;
    }

    function filterStatus($bool) {
        if ($bool != -1) {
            $this->where('gp.stt=?', __FUNCTION__)->setParam($bool ? 1 : 0, __FUNCTION__);
        }
        return $this;
    }

    /** @return UserEntity */
    function loadUsers($groupID) {
        return UserMapper::makeInstance()
                        ->innerJoin('cores_group_user gu ON u.id=gu.userID AND gu.groupID=' . intval($groupID))
                        ->getAll();
    }

    function updateGroup($id, $data) {
        $update['groupCode'] = arrData($data, 'groupCode');
        $update['groupName'] = arrData($data, 'groupName');
        $update['stt'] = arrData($data, 'stt') ? 1 : 0;

        if (!$update['groupCode'] || !$update['groupName']) {
            return false;
        }

        $this->db->StartTrans();
        $id = $this->replace($id, $update);

        if (!$id) {
            return false;
        }

        //user in group
        $this->db->delete('cores_group_user', 'groupID=?', array($id));
        foreach (arrData($data, 'users', array()) as $user) {
            $this->db->insert('cores_group_user', array(
                'userID'  => $user,
                'groupID' => $id
            ));
        }

        //group permissions
        $this->db->delete('cores_group_permission', 'groupID=?', array($id));
        foreach (arrData($data, 'permissions', array()) as $pem) {
            $this->db->insert('cores_group_permission', array(
                'groupID'    => $id,
                'permission' => $pem
            ));
        }
        $this->db->CompleteTrans();

        return $id;
    }

    function filterCode($code) {
        $this->where('gp.groupCode=?', __FUNCTION__)->setParam($code, __FUNCTION__);
        return $this;
    }

    function deleteGroup($id) {
        if (!is_array($id)) {
            $id = array($id);
        }
        foreach ($id as $i) {
            $this->db->Execute("UPDATE cores_group SET deleted=1, groupCode=CONCAT(groupCode, ?) WHERE id=?", array('|' . uniqid() . $i, $i));
        }
    }

    function checkCode($id, $code) {
        $inserted = $this->makeInstance()->filterCode($code)->getEntity();

        if (!$inserted->id) {
            return true;
        } else if ($inserted->id == $id) {
            return true;
        }
        return false;
    }

    function setLoadPermission($bool = true) {
        $this->loadPermission = $bool;
        return $this;
    }

    function getPermissions($groupID) {
        return $this->db->GetCol("SELECT permission FROM cores_group_permission WHERE groupID=?", array($groupID));
    }

}
