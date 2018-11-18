<?php
/**
 * Created by mvc_project.
 * User: yuhengfeng
 * Date: 2018/10/31
 * Time: 10:13
 * Email: henry.hyu1175@gmail.com
 */

namespace henryphp;

use henryphp\exception\ClassNotFoundException;

class Loader
{
    /**
     * 类名映射信息
     * @var array
     */
    protected static $classMap = [];

    /**
     * 类库别名
     * @var array
     */
    protected static $classAlias = [];

    // 注册自动加载机制
    public static function register($autoload = '')
    {
        //系统自动加载
        spl_autoload_register($autoload ?: array(self::class, 'autoload'),true,true);

        //加载composer类库
        $composerAutoFile = ROOT_PATH.'vendor'.DIRECTORY_SEPARATOR.'autoload.php';

        if ($composerAutoFile)
        {
            include_file($composerAutoFile);
        }
    }

    /**
     * @param $class
     * @return bool
     * 自动加载执行的方法
     */
    public static function autoload($class)
    {
        if (isset(self::$classAlias[$class]))
        {
            return class_alias(self::$classAlias[$class],$class);
        }

        if ($file = self::findFile($class))
        {
            // Win环境严格区分大小写
            if (strpos(PHP_OS, 'WIN') !== false && pathinfo($file, PATHINFO_FILENAME) != pathinfo(realpath($file), PATHINFO_FILENAME))
            {
                return false;
            }

            include_file($file);

            return true;
        }
    }

    /**
     * @param $class
     * @return mixed|string
     * 查找文件
     */
    public static function findFile($class)
    {
        if (strpos($class,'henryphp') !== false)
        {
            $class = str_replace('henryphp','henryphp\\library',$class);
        }

        if (!empty(self::$classMap[$class])) {
            // 类库映射
            return self::$classMap[$class];
        }

        $file = strtr($class, '\\', DIRECTORY_SEPARATOR) . '.php';

        return ROOT_PATH.$file;
    }

    /**
     * 默认的系统映射数组
     */
    public static function defaultClassMap()
    {
        self::$classMap = [
            'henryphp\\' => 'henryphp/library'
        ];
    }

    /**
     * 创建工厂对象实例
     * @access public
     * @param  string $name         工厂类名
     * @param  string $namespace    默认命名空间
     * @return mixed
     */
    public static function factory($name, $namespace = '', ...$args)
    {
        $class = false !== strpos($name, '\\') ? $name : $namespace . ucwords($name);

        if (class_exists($class)) {
            return Container::getInstance()->invokeClass($class, $args);
        } else {
            throw new ClassNotFoundException('class not exists:' . $class, $class);
        }
    }
}

/**
 * 作用范围隔离
 *
 * @param $file
 * @return mixed
 */
function include_file($file)
{
    return include $file;
}