<?php
/**
 * Created by mvc_project.
 * User: yuhengfeng
 * Date: 2019/5/17
 * Time: 2:19 PM
 * Email: henry.hyu1175@gmail.com
 */
namespace henryphp\Providers;

abstract class ServiceProvider
{
    /**
     * @return mixed
     */
    abstract protected function register();

    /**
     * 执行
     */
    public function boot()
    {
        $this->register();
    }

    public function getClass()
    {
        return get_class();
    }
}