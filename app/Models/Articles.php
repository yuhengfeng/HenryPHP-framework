<?php
/**
 * Created by mvc_project.
 * User: yuhengfeng
 * Date: 2018/11/7
 * Time: 4:29 PM
 * Email: henry.hyu1175@gmail.com
 */

namespace app\Models;

class Articles extends Model
{
    public function getAll()
    {
        return $this->fetchAll();
    }
}