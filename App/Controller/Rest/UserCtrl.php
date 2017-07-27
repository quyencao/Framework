<?php

namespace App\Controller\Rest;

use Lib\Json;
use App\Model\DepartmentMapper;
use App\Model\UserMapper;
use App\Model\GroupMapper;
use App\Model\Permission;

class UserCtrl extends RestCtrl {

    protected $userMapper;
    protected $depMapper;
    protected $groupMapper;

    protected function init() {
        parent::init();
        $this->userMapper = UserMapper::makeInstance();
        $this->depMapper = DepartmentMapper::makeInstance();
        $this->groupMapper = GroupMapper::makeInstance();
    }

    function getDepartment($depID) {
        $this->requireLogin();

        $depID = (int) $depID;
        $loadUsers = $this->req->get('users');
        $loadDeps = $this->req->get('departments');
        $rescusively = $this->req->get('rescusively');
        $loadAncestors = $this->req->get('ancestors');
        $not = $this->req->get('not');

        $userMapper = $this->userMapper;

        $dep = $this->depMapper
                ->makeInstance()
                //autoload related entities
                ->setLoadAncestors($loadAncestors)
                ->setLoadChildDeps($loadDeps, $rescusively)
                ->setLoadUsers($loadUsers, function($rawData, $entity) use ($userMapper) {
                    $entity->groups = $userMapper->db->GetCol('SELECT groupID FROM cores_group_user WHERE userID=?', array($entity->id));
                    $entity->permissions = $userMapper->loadPermissions($entity->id);
                })
                ->filterNot($not)
                //query
                ->filterID($depID)
                ->getEntity();

        $this->resp->setBody(Json::encode($dep));
    }

    function search() {
        $this->requireLogin();

        $search = $this->req->get('search');
        $stt = $this->req->get('status', -1);

        $deps = $this->depMapper
                ->makeInstance()
                ->filterStatus($stt)
                ->filterDeleted(false)
                ->filterSearch($search)
                ->getAll();

        $users = $this->userMapper
                ->makeInstance()
                ->filterStatus($stt)
                ->filterDeleted(false)
                ->filterSearch($search)
                ->getAll();



        $this->resp->setBody(Json::encode(array(
                    'departments' => $deps,
                    'users'       => $users
        )));
    }

    function updateDepartment($depID) {
        $this->requireAdmin();

        $code = $this->restInput('depCode');
        $name = $this->restInput('depName');
        $stt = $this->restInput('stt');
        $parentID = $this->restInput('depID');

        $depID = $this->depMapper->updateDep($depID, $parentID, $code, $name, $stt);
        $this->resp->setBody(Json::encode(array(
                    'status'   => true,
                    'resource' => url('/rest/department/' . $depID)
        )));
    }

    function getGroups() {
        $this->requireLogin();

        $stt = $this->req->get('status', -1);
        $groups = $this->groupMapper->makeInstance()
                ->filterStatus($stt)
                ->setLoadPermission()
                ->getAll();

        $this->resp->setBody(Json::encode($groups));
    }

    function getGroupUsers($groupID) {
        $this->requireLogin();

        $users = $this->userMapper
                ->makeInstance()
                ->select('dep.depName', false)
                ->filterDeleted(false)
                ->innerJoin('cores_group_user gu ON u.id=gu.userID AND gu.groupID=' . intval($groupID))
                ->leftJoin('cores_department dep ON u.depID=dep.id')
                ->getAll(function($rawData, $entity) {
            if (!$entity->depName) {
                $entity->depName = '[Thư mục gốc]';
            }
        });

        $this->resp->setBody(Json::encode($users));
    }

    /**
     * Trả về danh sách tất cả quyền của ứng dụng
     */
    function getBasePermissions() {
        $this->requireAdmin();

        $ret = array();
        foreach (\Lib\Setting::getAllApp() as $appId) {
            $setting = new \Lib\Setting($appId);
            if ($setting->xml->attributes()->active != 'true') {
                continue;
            }
            $app = array(
                'name'   => (string) $setting->xml->attributes()->name,
                'groups' => array()
            );
            foreach ($setting->xml->permissions->group as $groupXml) {
                $group = array(
                    'name'        => (string) $groupXml->attributes()->name,
                    'permissions' => array()
                );
                foreach ($groupXml->pem as $pem) {
                    $group['permissions'][] = array(
                        'id'   => (string) $pem->attributes()->id,
                        'name' => (string) $pem->attributes()->name
                    );
                }
                $app['groups'][] = $group;
            }
            $ret[] = $app;
        }

        $this->resp->setBody(Json::encode($ret));
    }

    function checkUniqueAccount() {
        $this->requireAdmin();

        $result = $this->userMapper->checkUniqueAccount($this->req->get('id'), $this->req->get('account'));
        $this->resp->setBody(Json::encode(array(
                    'valid' => $result
        )));
    }

    function updateUser($id) {
        $this->requireAdmin();

        $data = $this->restInput();
        $id = $this->userMapper->updateUser($id, $data);

        $this->resp->setBody(Json::encode(array(
                    'status'   => true,
                    'resource' => url('/rest/user/' . $id)
        )));
    }

    function deleteUsers() {
        $this->requireAdmin();

        $users = $this->restInput();
        $this->userMapper->deleteUsers($users);

        $this->resp->setBody(Json::encode(array(
                    'status' => true
        )));
    }

    function deleteDepartments() {
        $this->requireAdmin();

        $deps = $this->restInput();
        $this->depMapper->deleteDepartments($deps);

        $this->resp->setBody(Json::encode(array(
                    'status' => true
        )));
    }

    function moveUsers() {
        $this->requireAdmin();

        $users = $this->restInput('pks');
        $dest = $this->restInput('dest');

        $this->userMapper->moveUsers($users, $dest);
        $this->resp->setBody(Json::encode(array(
                    'status' => true
        )));
    }

    function moveDepartments() {
        $this->requireAdmin();

        $deps = $this->restInput('pks');
        $dest = $this->restInput('dest');

        $this->depMapper->moveDepartments($deps, $dest);
        $this->resp->setBody(Json::encode(array(
                    'status' => true
        )));
    }

    function updateGroup($id) {
        $this->requireAdmin();

        $group = $this->restInput();
        if (!$this->groupMapper->checkCode($id, $group['groupCode'])) {
            $this->resp->setBody(Json::encode(array(
                        'status' => false,
                        'error'  => 'duplicateCode'
            )));
            return;
        }

        $id = $this->groupMapper->updateGroup($id, $group);

        $this->resp->setBody(Json::encode(array(
                    'status' => true,
                    'id'     => $id
        )));
    }

    function deleteGroups() {
        $this->requireAdmin();

        $arrID = $this->restInput('id', array());
        $this->groupMapper->deleteGroup($arrID);
        $this->resp->setBody(Json::encode(array(
                    'status' => true
        )));
    }

}
