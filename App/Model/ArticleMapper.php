<?php
/**
 * Created by PhpStorm.
 * User: LaptopTCC
 * Date: 7/26/2017
 * Time: 11:53 AM
 */

namespace App\Model;

use Lib\SQL\Mapper;

class ArticleMapper extends Mapper
{
    function makeEntity($rawData)
    {
        return new ArticleEntity($rawData);// TODO: Change the autogenerated stub
    }

    function tableName()
    {
        return 'article';
    }

    function tableAlias()
    {
        return 'a';
    }

    function filterTitle($title) {
        $this->where($this->tableAlias().'.title LIKE ?', __FUNCTION__)->setParam("%$title%", __FUNCTION__);
        return $this;
    }

    function filterStatus($status) {
        $this->where($this->tableAlias().'.stt = ?', 'a')->setParam((int)$status, 'a');

        return $this;
    }

    public function countArticleInCategory() {
        return $this->db->GetAll("
            SELECT category_id, COUNT(category_id) as `Count`
            FROM article
            GROUP BY category_id
        ");
    }

    function filterID($id) {
        $this->where($this->tableAlias() . '.id=?', __FUNCTION__)->setParam($id, __FUNCTION__);
        return $this;
    }
}