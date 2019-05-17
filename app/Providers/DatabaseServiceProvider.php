<?php
/**
 * Created by mvc_project.
 * User: yuhengfeng
 * Date: 2019/5/17
 * Time: 2:08 PM
 * Email: henry.hyu1175@gmail.com
 */
namespace app\Providers;

use Illuminate\Support\ServiceProvider;

class DatabaseServiceProvider extends ServiceProvider
{
    public function __construct(\Illuminate\Contracts\Foundation\Application $app)
    {
        dd($app);
        parent::__construct($app);
    }
}