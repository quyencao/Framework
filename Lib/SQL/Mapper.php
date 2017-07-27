<?php

namespace Lib\SQL;

abstract class Mapper extends Query {

    /** @var \ADOConnection */
    public $db;
    protected $params = array();
    protected $pageSize = 20;
    protected $mapperSet;

    function __construct() {
        $this->db = DB::getInstance();
        $this->select($this->tableAlias() . '.*')
                ->from($this->tableName() . ' ' . $this->tableAlias());
    }

    static function makeInstance() {
        return new static();
    }

    abstract function tableName();

    abstract function tableAlias();

    function makeEntity($rawData) {
        return new Entity($rawData);
    }

    function setParam($value, $key) {
        $this->params[$key] = $value;
        return $this;
    }

    function setParams($arr) {
        if (!is_array($arr)) {
            $arr = array($arr);
        }
        $this->params += $arr;
        return $this;
    }

    function setPage($pageNo, $pageSize = null) {
        $pageSize = $pageSize ? $pageSize : $this->pageSize;
        $offset = ($pageNo - 1) * $pageSize;

        $this->limit($pageSize, $offset);
        return $this;
    }

    function count(&$totalRecord) {
        $mapper = clone $this;
        $mapper->select('COUNT(*)')
                ->limit(1)
                ->offset(0)
                ->orderBy(null);
        $totalRecord = $this->db->GetOne($mapper->__toString(), $this->params);
        return $this;
    }

    function getEntity($callback = null) {
        $this->limit(1);
        $row = $this->db->GetRow($this->__toString(), $this->params);
        $entity = $this->makeEntity($row);
        if (is_callable($callback)) {
            call_user_func($callback, $row, $entity);
        }
        return $entity;
    }

    /** @return Entity_Iterator */
    function getAll($callback = null) {
        $rows = $this->db->GetAll($this->__toString(), $this->params);
        $rows = $rows ? $rows : array();
        $entities = array();

        foreach ($rows as $row) {
            $entity = $this->makeEntity($row);
            if (is_callable($callback)) {
                call_user_func($callback, $row, $entity);
            }
            $entities[] = $entity;
        }

        return $entities;
    }

    function getOne() {
        $this->limit(1);
        return $this->db->GetOne($this->__toString(), $this->params);
    }

    function getCol() {
        $this->limit(1);
        return $this->db->GetCol($this->__toString(), $this->params);
    }

    function getAssoc() {
        return $this->db->GetAssoc($this->__toString(), $this->params);
    }

    function filterID($id) {
        $this->where($this->tableAlias() . '.id=?', __FUNCTION__)->setParam($id, __FUNCTION__);
        return $this;
    }

    function rebuildPath($parentCol, $pathCol, $srcCol, $fromNode = 0) {
        $this->db->startTrans();

        $table = $this->tableName();

        $node = $this->db->GetRow("SELECT * FROM $table WHERE id=?", array($fromNode));
        $parentNode = $this->db->GetRow("SELECT * FROM $table WHERE id=?", array(arrData($node, $parentCol)));
        $childNodes = $this->db->GetAll("SELECT * FROM $table WHERE $parentCol=?", array($fromNode));

        if (!empty($node)) {
            $path = arrData($parentNode, $pathCol, '/');
            $this->db->update(
                    $table
                    , array($pathCol => $path . $node[$srcCol] . '/')
                    , 'id=?'
                    , array($node['id'])
            );
        }

        foreach ($childNodes as $node) {
            $this->rebuildPath($parentCol, $pathCol, $srcCol, $node['id']);
        }

        $this->db->completeTrans();
    }

    /**
     * 
     * @param type $id
     * @param type $updateData
     * @return type
     */
    function update($id, $updateData) {
        return $this->db->update($this->tableName(), $updateData, 'id=?', array($id));
    }

    /**
     * 
     * @param type $updateData
     * @return type
     */
    function insert($updateData) {
        return $this->db->insert($this->tableName(), $updateData);
    }

    function copy() {
        return clone $this;
    }

    function union() {
        $arr = func_get_args();
        $ret = [];
        foreach ($arr as $rs) {
            foreach ($rs as $row) {
                $ret[$row->id] = $row;
            }
        }

        return array_values($ret);
    }

    /**
     * id = 0 insert; id <> 0 update
     * @param type $id
     * @param type $updateData
     * @return int id
     */
    function replace($id, $updateData) {
        if ($id) {
            $this->update($id, $updateData);
        } else {
            $id = $this->insert($updateData);
        }
        return $id;
    }

}
