<?php
/**
 * Created by PhpStorm.
 * User: henryu
 * Date: 9/7/18
 * Time: 下午2:08
 */
namespace henryphp\base;

use \henryphp\db\Sql;

class Model extends Sql
{
    protected $model;

    public function __construct()
    {
        // 获取数据库表名
        if (!$this->table) {

            // 获取模型类名称
            $this->model = get_class($this);

            $this->model = explode('\\',$this->model);
            // 删除类名最后的 Model 字符
            $this->model = end($this->model);
            // 数据库表名与类名一致
            $this->table = strtolower($this->model);
        }
    }
}