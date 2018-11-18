<?php
/**
 * Created by PhpStorm.
 * User: henryu
 * Date: 9/7/18
 * Time: 下午2:08
 */

namespace henryphp\base;

use henryphp\Container;
use henryphp\twig\View_Twig;

class View
{
    /**
     * 请求
     * @var object
     */
    protected $request;
    /**
     * @var string
     */
    protected $moduleName;

    /**
     * @var string
     */
    protected $controller;

    /**
     * @var string
     */
    protected $action;

    /**
     * 视图目录
     * @var
     */
    protected $viewPath;

    protected $twig;
    /**
     * 传递变量
     * @var array
     */
    protected $item = [];

    public function __construct()
    {
        $this->request = Container::get('request');

        $this->moduleName = lcfirst($this->request->getModuleName());
        $this->controller = lcfirst($this->request->getControllerName());
        $this->action = lcfirst($this->request->getActionName());

        $this->init();
    }

    /**
     * 视图初始化
     */
    public function init()
    {
        $viewPath = config('view.view_path') ?: 'resources/views/';

        $this->twig = new View_Twig();

        $this->viewPath = ROOT_PATH.$viewPath;
    }

    /**
     * 默认布局
     * 遵循 控制器/方法 的视图
     */
    public function view($template = '',$arr = [])
    {

        $prefix = config('view.template_prefix');

        $data = $arr ? array_merge($arr,$this->item) : $this->item;

        $path = $this->viewPath.$this->moduleName.'/'.$this->controller.'/';

        $customLayout = $template ? $path.$template.'.'.$prefix : $path.$this->action.'.'.$prefix;

        $filename = $template ? $this->moduleName.'/'.$this->controller.'/'.$template.'.'.config('view.template_prefix') : $this->moduleName.'/'.$this->controller.'/'.$this->action.'.'.config('view.template_prefix');

        if (is_file($customLayout))
        {
            $this->twig->view($data,$this->viewPath,$filename,$this->moduleName);
        }else{
            throw new \Exception($customLayout.'文件不存在!');
        }
    }

    /**
     * @param array $array
     * @return $this
     * 设置全局参数
     */
    public function setGlobalParams($array = [])
    {
         $this->item = $array;

         return $this;
    }

    /**
     * 获取全局参数值
     * @param string $name
     * @return array|mixed
     */
    public function getGlobalParams($name = '')
    {
        if (!empty($name)){
            if (isset($this->item[$name])){
                return $this->item[$name];
            }
        }

        return $this->item;
    }
}