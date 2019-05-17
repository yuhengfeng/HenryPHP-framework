<?php
/**
 * Created by mvc_project.
 * User: yuhengfeng
 * Date: 2019/5/17
 * Time: 2:08 PM
 * Email: henry.hyu1175@gmail.com
 */
namespace henryphp\Providers;

use henryphp\Container;
use Illuminate\Database\Capsule\Manager as Capsule;

class DatabaseServiceProvider extends ServiceProvider
{
    protected function register()
    {
        // TODO: Implement register() method.
        $capsule = new Capsule();
        //设置henryphp 数据库配置
        if ($capsule->getContainer()->bound('config')) {
            $capsule->getContainer()->instance('config', Container::get('app')->config);
        }

        $capsule->setAsGlobal();

        $capsule->bootEloquent();
    }
}