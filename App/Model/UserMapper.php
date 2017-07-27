<?php

namespace App\Model;

use Lib\SQL\Mapper;

class UserMapper extends Mapper {

    protected $loadPermissions = 0;
    protected $loadGroups = false;

    public function makeEntity($rawData) {
        $entity = new UserEntity($rawData);
        if ($this->loadPermissions) {
            $entity->permissions = $this->loadPermissions($entity->id, $this->loadPermissions == 2 ? true : false);
        }
        if ($this->loadGroups) {
            $entity->groups = $this->loadGroups($entity->id);
        }
        return $entity;
    }

    public function tableAlias() {
        return 'u';
    }

    public function tableName() {
        return 'cores_user';
    }

    function __construct() {
        parent::__construct();
        $this->filterDeleted(false);
    }

    function filterParent($depID) {
        $this->where('u.depID=?', __FUNCTION__)->setParam($depID, __FUNCTION__);
        return $this;
    }

    function filterAccount($acc) {
        $this->where('u.account=?', __FUNCTION__)->setParam($acc, __FUNCTION__);
        return $this;
    }

    /** @return GroupEntity */
    function loadGroups($userID) {
        return GroupMapper::makeInstance()
                        ->innerJoin('cores_group_user gu ON gu.groupID=gp.id AND gu.userID=' . intval($userID))
                        ->getAll();
    }

    function updateUser($id, $data) {
        $update['account'] = arrData($data, 'account');
        $update['fullName'] = arrData($data, 'fullName');
        $update['depID'] = (int) arrData($data, 'depID');
        $update['jobTitle'] = arrData($data, 'jobTitle');
        $update['stt'] = arrData($data, 'stt') ? 1 : 0;

        if (!$update['account'] || !$update['fullName']) {
            return;
        }
        if (!$id && !$data['newPass']) {
            return;
        }

        if (arrData($data, 'changePass') || !$id) {
            $update['pass'] = md5(arrData($data, 'newPass'));
        }

        $groups = arrData($data, 'groups', array());
        $permissions = arrData($data, 'permissions', array());

        $this->db->StartTrans();
        if ($id) {
            $this->db->update('cores_user', $update, 'id=?', array($id));
        } else {
            $id = $this->db->insert('cores_user', $update);
        }

        //group
        $this->db->delete('cores_group_user', 'userID=?', array($id));
        foreach ($groups as $groupID) {
            $this->db->insert('cores_group_user', array('userID' => $id, 'groupID' => $groupID));
        }

        //permissions
        $this->db->delete('cores_user_permission', 'userID=?', array($id));
        foreach ($permissions as $pem) {
            $this->db->insert('cores_user_permission', array('userID' => $id, 'permission' => $pem));
        }

        $this->db->CompleteTrans();
        return $id;
    }

    function checkUniqueAccount($userID, $account) {
        $inserted = $this->makeInstance()
                ->filterAccount($account)
                ->getEntity();

        if ($userID && $inserted->id == $userID) {
            return true;
        }
        if (!$inserted->id) {
            return true;
        }

        return false;
    }

    function deleteUsers($arrId) {
        if (!is_array($arrId))
            return;
        foreach ($arrId as $id) {
            if ($id == 1)
                continue;
            $this->db->Execute("UPDATE cores_user SET deleted=1, account=CONCAT(account, ?) WHERE id=?", array('|' . uniqid($id), $id));
        }
    }

    function filterDeleted($bool) {
        $this->where('u.deleted=?', __FUNCTION__)->setParam($bool ? 1 : 0, __FUNCTION__);
        return $this;
    }

    function moveUsers($arrId, $depID) {
        if (!is_array($arrId))
            return;
        foreach ($arrId as $id) {
            $this->db->update('cores_user', array('depID' => $depID), 'id=?', array($id));
        }
    }

    function filterStatus($bool) {
        if ($bool != -1) {
            $this->where('u.stt=?', __FUNCTION__)->setParam($bool ? 1 : 0, __FUNCTION__);
        }
        return $this;
    }

    function filterSearch($search) {
        $this->where('(u.fullName LIKE ? OR u.jobTitle LIKE ? OR u.account LIKE ? OR u.email LIKE ? OR u.phone LIKE ?)', __FUNCTION__);
        $this->setParams(array(
            __FUNCTION__ . 1 => "%$search%",
            __FUNCTION__ . 2 => "%$search%",
            __FUNCTION__ . 3 => "%$search%",
            __FUNCTION__ . 4 => "%$search%",
            __FUNCTION__ . 5 => "%$search%"
        ));
        return $this;
    }

    function setLoadPermissions($includeGroupPem = false) {
        $this->loadPermissions = $includeGroupPem ? 2 : 1;
        return $this;
    }

    function loadPermissions($userID, $includeGroupPem = false) {
        $userID = (int) $userID;
        if (!$userID) {
            return array();
        }

        $sql = "SELECT permission FROM cores_user_permission WHERE userID=$userID";
        if ($includeGroupPem) {
            $groups = "SELECT groupID FROM cores_group_user WHERE userID=$userID";
            $sql .= "\nUNION SELECT permission FROM cores_group_permission WHERE groupID IN($groups)";
        }

        return $this->db->GetCol($sql);
    }

    function setLoadGroups($bool = true) {
        $this->loadGroups = $bool;
        return $this;
    }

    /**
     * Lỗi array('status' => false, 'error' => '[code]')<br>
     * badRequest: chưa điền account hoặc pass<br>
     * notFound: không tìm thấy<br>
     * inactive: không kích hoạt<br>
     * wrongPassword: sai mật khẩu<Br>
     * Thành công array('status'=> true, 'user' => [UserEntity])
     * @param type $account
     * @param type $password
     * @return arra 
     */
    function authenticate($account, $password) {
        if (!$account || !$password) {
            return array(
                'status' => false,
                'error'  => 'badRequest'
            );
        }

        $user = $this->makeInstance()
                ->setLoadPermissions(true)
                ->setLoadGroups()
                ->filterAccount($account)
                ->filterDeleted(false)
                ->getEntity();

        if (!$user->id) {
            return array(
                'status' => false,
                'error'  => 'notFound'
            );
        }

        if ($user->stt == 0) {
            return array(
                'status' => false,
                'error'  => 'inactive'
            );
        }

        if ($user->pass != md5($password)) {
            return array(
                'status' => false,
                'error'  => 'wrongPassword'
            );
        }

        return array(
            'status' => true,
            'user'   => $user
        );
    }

    function changePassword($userID, $pass) {
        $this->update($userID, array('pass' => md5($pass)));
    }

}
