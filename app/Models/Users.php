<?php
/**
 * Created by PhpStorm.
 * User: henryu
 * Date: 10/7/18
 * Time: 下午1:51
 */

namespace app\Models;

class Users extends Model
{
    public function getAll()
    {
        return $this->fetchAll();
    }
}