<?php
/**
 * Created by PhpStorm.
 * User: henryu
 * Date: 10/7/18
 * Time: 下午1:14
 */
use \henryphp\Container;


if (!function_exists('config'))
{
    /**
     * 获取配置
     * @param null $name
     * @param null $default
     * @return mixed
     */
    function config($name = null, $default = null)
    {
        $obj = Container::get('app')->config;

        return $obj->get($name,$default);
    }
}
if (!function_exists('app')) {
    /**
     * 快速获取容器中的实例 支持依赖注入
     * @param string    $name 类名或标识 默认获取当前应用实例
     * @param array     $args 参数
     * @param bool      $newInstance    是否每次创建新的实例
     */
    function app($name = 'henryphp\App', $args = [], $newInstance = false)
    {
        return Container::get($name, $args, $newInstance);
    }
}

if (!function_exists('request')) {
    /**
     * 视图展示与传参
     * @param string    $template 模版名称
     * @param array     $args 参数
     */
    function request()
    {
        return app('request');
    }
}

if (!function_exists('asset')) {
    /**
     * url
     * @param string    $str 资源路径
     */
    function asset($str = '')
    {
        $domain = request()->domain().DIRECTORY_SEPARATOR;

        if ($str){
            return $domain.$str;
        }
        return $domain;
    }
}

if (!function_exists('view')) {
    /**
     * 视图展示与传参
     * @param string    $template 模版名称
     * @param array     $args 参数
     */
    function view($template = '',$arr = [])
    {
        return Container::get('view')->view($template,$arr);
    }
}

if (!function_exists('traverse'))
{
    function traverse($path = '.') {
        $current_dir = opendir($path);    //opendir()返回一个目录句柄,失败返回false
        while(($file = readdir($current_dir)) !== false) {    //readdir()返回打开目录句柄中的一个条目
            $sub_dir = $path . DIRECTORY_SEPARATOR . $file;    //构建子目录路径
            if($file == '.' || $file == '..') {
                continue;
                    } else if(is_dir($sub_dir)) {    //如果是目录,进行递归
                                        echo 'Directory ' . $file . ':<br>';
                        traverse($sub_dir);
                    } else {    //如果是文件,直接输出
                                        echo 'File in Directory ' . $path . ': ' . $file . '<br>';
                   }
                 }
    }
}