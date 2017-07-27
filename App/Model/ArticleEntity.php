<?php
/**
 * Created by PhpStorm.
 * User: LaptopTCC
 * Date: 7/26/2017
 * Time: 11:45 AM
 */

namespace App\Model;

use Lib\SQL\Entity;


class ArticleEntity extends Entity
{
    public function getUrl() {
        return url('/article/' . $this->pk);
    }
}