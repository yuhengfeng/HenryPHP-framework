<?php
/**
 * Created by PhpStorm.
 * User: henryu
 * Date: 9/7/18
 * Time: 下午2:08
 */

namespace henryphp\base;

use henryphp\App;
use henryphp\Container;

class Controller
{
    /**
     * 应用实例
     * @var
     */
    protected $app;
    /**
     * 视图实例
     * @var View
     */
    protected $view;

    /**
     * 请求实例
     * @var
     */
    protected $request;

    /**
     * 控制器中间件
     * @var array
     */
    protected $middleware = [];


    public function __construct(App $app = null)
    {
        $this->app     = $app ?: Container::get('app');
        $this->request = $this->app['request'];
        $this->view    = $this->app['view'];

        // 控制器初始化
        $this->initialize();

        // 控制器中间件

    }

    // 初始化
    protected function initialize()
    {}

    /**
     * 全局参数
     * @param array $array
     */
    public function setGlobalParams($array = [])
    {
        return $this->view->setGlobalParams($array);
    }
    /**
     * 渲染试图
     */
    public function view($template = '',$data = [])
    {
        $this->view->view($template,$data);
    }
}