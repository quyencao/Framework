<?php

namespace App\Model;

use Lib\SQL\Mapper;

class DepartmentMapper extends Mapper {

    protected $loadUsers;
    protected $loadUsersCallback;
    protected $loadChildDeps;
    protected $loadChildDepsRecusiveLy; //load cả cây
    protected $loadAncestors;
    protected $not = array();

    protected function userMapper() {
        return UserMapper::makeInstance();
    }

    function __construct() {
        parent::__construct();
        $this->orderBy('dep.path');
        $this->filterDeleted(false);
    }

    public function makeEntity($rawData) {
        $entity = new DepartmentEntity($rawData);
        $entity->id = (int) $entity->id;

        if ($this->loadUsers) {
            $entity->users = $this->loadUsers($entity->id, $this->loadUsersCallback);
        }
        if ($this->loadChildDeps) {
            $entity->deps = $this->loadChildDeps($entity->id, $this->loadChildDepsRecusiveLy);
        }
        if ($this->loadAncestors) {
            $entity->ancestors = $this->loadAncestors($entity);
        }

        return $entity;
    }

    public function tableAlias() {
        return 'dep';
    }

    public function tableName() {
        return 'cores_department';
    }

    function filterID($depID) {
        $this->where('dep.id=?', __FUNCTION__)->setParam($depID, __FUNCTION__);
        return $this;
    }

    function filterParent($depID) {
        $this->where('dep.depID=?', __FUNCTION__)->setParam($depID, __FUNCTION__);
        return $this;
    }

    /**
     * Tự động load user trực thuộc
     */
    function setLoadUsers($bool = true, $callback = null) {
        $this->loadUsers = $bool;
        $this->loadUsersCallback = $callback;
        return $this;
    }

    /** Tự động load đơn vị trực thuộc */
    function setLoadChildDeps($bool = true, $rescusively = false) {
        $this->loadChildDeps = $bool;
        $this->loadChildDepsRecusiveLy = $rescusively;
        return $this;
    }

    /** Tự động load đơn vị tổ tiên */
    function setLoadAncestors($bool = true) {
        $this->loadAncestors = $bool;
        return $this;
    }

    /** @return UserEntity */
    function loadUsers($depID, $callback = null) {
        return $this->userMapper()
                        ->filterDeleted(false)
                        ->filterParent($depID)
                        ->getAll($callback);
    }

    /** @return DepartmentEntity */
    function loadChildDeps($depID, $rescusively = false) {
        $mapper = $this;
        return $this->makeInstance()
                        ->filterParent($depID)
                        ->filterDeleted(false)
                        ->filterNot($this->not)
                        ->getAll(function($rawData, $entity) use($mapper, $rescusively) {
                            $entity->deps = $mapper->loadChildDeps($entity->id, $rescusively);
                        });
    }

    /**
     * 
     * @param DepartmentEntity $dep
     * @return DepartmentEntity 
     */
    function loadAncestors(DepartmentEntity $dep) {
        $ids = explode('/', trim($dep->path, '/'));
        //remove this dep
        array_pop($ids);

        if (count($ids)) {
            $ids = implode(',', $ids);
            return $this->makeInstance()
                            ->where("dep.id IN($ids)")
                            ->getAll();
        } else {
            return [];
        }
    }

    function filterNot($arr) {
        if (!is_array($arr)) {
            $arr = array($arr);
        }
        $this->not = $arr;

        $where = array();
        foreach ($arr as &$id) {
            $id = (int) $id;
            $where[] = "dep.id <> $id";
        }
        $this->where("(" . implode(' AND ', $where) . ")", __FUNCTION__);

        return $this;
    }

    function updateDep($depID, $parentID, $code, $name, $stt) {
        if (!$code || !$name) {
            return;
        }
        $data = array(
            'depID'   => (int) $parentID,
            'depCode' => $code,
            'depName' => $name,
            'stt'     => $stt ? 1 : 0
        );


        if ($depID) {
            $this->db->update('cores_department', $data, 'id=?', array($depID));
        } else {
            $depID = $this->db->insert('cores_department', $data);
        }
        //re-index path
        $this->rebuildDepPath();

        return $depID;
    }

    protected function rebuildDepPath() {
        $this->rebuildPath('depID', 'path', 'id');
    }

    function deleteDepartments($arrId) {
        if (!is_array($arrId))
            return;
        $this->db->StartTrans();
        foreach ($arrId as $id) {
            $this->db->Execute("UPDATE cores_department SET deleted=1, depCode=CONCAT(depCode, ?) WHERE id=?", array('|' . uniqid($id), $id));
            //chuyển đơn vị, tk vè thư mục gốc
            $this->db->update('cores_department', array('depID' => 0), 'depID=?', array($id));
            $this->db->update('cores_user', array('depID' => 0), 'depID=?', array($id));
        }
        $this->rebuildDepPath();
        $this->db->CompleteTrans();
    }

    function filterDeleted($bool) {
        $this->where('dep.deleted=?', __FUNCTION__)->setParam($bool ? 1 : 0, __FUNCTION__);
        return $this;
    }

    function moveDepartments($arrId, $depID) {
        if (!is_array($arrId))
            return;
        $this->db->StartTrans();

        foreach ($arrId as $id) {
            $this->db->update('cores_department', array('depID' => $depID), 'id=?', array($id));
        }
        $this->rebuildDepPath();
        $this->db->CompleteTrans();
    }

    function filterSearch($search) {
        $this->where('(dep.depName LIKE ? OR dep.depCode LIKE ?)', __FUNCTION__)
                ->setParams(array(
                    __FUNCTION__ . 1 => "%$search%",
                    __FUNCTION__ . 2 => "%$search%"
        ));
        return $this;
    }

    function filterStatus($bool) {
        if ($bool != -1) {
            $this->where('dep.stt=?', __FUNCTION__)->setParam($bool ? 1 : 0, __FUNCTION__);
        }
        return $this;
    }

}
