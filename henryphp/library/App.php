<?php
/**
 * Created by PhpStorm.
 * User: henryu
 * Date: 11/7/18
 * Time: 下午2:57
 */

namespace henryphp;

use Dotenv\Dotenv;
use Exception;

class App extends Container
{
    const VERSION = '1.0.1';

    /**
     * @var
     * 应用程序目录
     */
    protected $frameName;
    /**
     * @var
     * 应用程序目录
     */
    protected $appPath;

    /**
     * @var
     * 模块目录
     */
    protected $modulePath;

    /**
     * @var
     * 框架应用目录
     */
    protected $henryPath;

    /**
     * @var
     * 项目根目录
     */
    protected $rootPath;

    /**
     * @var
     * 日志根目录
     */
    protected $storagePath;

    /**
     * @var
     * 配置目录
     */
    protected $configPath;

    /**
     * @var
     * 应用实例
     */
    protected $dispatch;

    //是否开启debug
    protected $appDebug = true;

    /**
     * 应用开始时间
     * @var float
     */
    protected $beginTime;

    /**
     * 应用内存初始占用
     * @var integer
     */
    protected $beginMem;

    //初始化
    protected $initialized = false;

    /**
     * App constructor.
     * @param string $appPath
     */
    public function __construct($appPath = '')
    {
        $this->appPath = $appPath ? realpath($appPath) . DIRECTORY_SEPARATOR : $this->getAppPath();

        $this->henryPath = dirname(__DIR__) . DIRECTORY_SEPARATOR;

        $this->rootPath = dirname($this->appPath).DIRECTORY_SEPARATOR;

        $this->storagePath = $this->rootPath . 'storage' . DIRECTORY_SEPARATOR;

        $this->configPath = $this->rootPath.'config'.DIRECTORY_SEPARATOR;
    }

    //执行应用程序
    public function run()
    {
        // 初始化应用
        $this->initialized = true;

        $this->initialize();

        //2、检测开发环境，根据环境读取错误信息

        //3、日志记录

        //4、检测敏感字符并删除

        //5、数据库配置
        $this->database();

        //6、路由配置，读取路由
        $this->route();
    }

    //初始化应用
    public function initialize()
    {
        if ($this->initialized == false)
        {
            return;
        }
        $this->initialized = true;

        $this->beginTime   = microtime(true);

        $this->beginMem    = memory_get_usage();

        // 初始化应用
        $this->init();

        // 应用调试模式
        $this->setAppDebug();

        //配置env文件值

        // 注册异常处理类

        // 注册类库别名

        // 设置系统时区
        date_default_timezone_set($this->config('app.default_timezone'));

        // 读取语言包

        // 路由初始化

    }

    /**
     * 初始化应用或模块
     * @access public
     * @param  string $module 模块名
     * @return void
     */
    public function init($module = '')
    {
        // 定位模块目录
        $module = $module ? $module . DIRECTORY_SEPARATOR : '';

        $path = $this->appPath . $module;

        //加载公共函数
        if (is_file($path . 'common.php'))
        {
            include_once $path . 'common.php';
        }

        // 加载系统助手函数
        if ('' == $module)
        {
            include $this->henryPath . 'helpers.php';
        }

        // 加载中间件

        // 注册服务的容器对象实例
        if (is_file($this->appPath.DIRECTORY_SEPARATOR.'service.php')){
            $services = include $this->appPath.DIRECTORY_SEPARATOR.'service.php';
            if (is_array($services)){
                $this->bindTo($services);
            }
        }
        //解析.env文件
        $dotenv = Dotenv::create(ROOT_PATH);

        $dotenv->load();
        // 自动读取配置文件
        if (is_dir($this->configPath.$module))
        {
            $dir = $this->configPath.$module;
        }

        $files = isset($dir) ? scandir($dir) : [];

        foreach ($files as $file) {
            if ('.' . pathinfo($file, PATHINFO_EXTENSION) === '.php') {

                $filename = $dir . $file;

                $this->config->load($filename, pathinfo($file, PATHINFO_FILENAME));
            }
        }
        
        if ($module) {
            // 对容器中的对象实例进行配置更新
            $this->containerConfigUpdate($module);
        }
    }

    public function containerConfigUpdate($module = '')
    {
        dd($this->config);
    }

    /**
     * 数据库配置
     */
    public function database()
    {
        if ($this->has('database')){
            call_user_func_array(array($this->get('database'),'boot'),[]);
        }
    }

    // 路由处理
    public function route()
    {
        $moduleName = $this->request->getModuleName();
        $controllerName = $this->request->getControllerName();
        $actionName = $this->request->getActionName();
        $param = $this->request->getCurUriParam();

        // 判断控制器和操作是否存在
        $controller = 'app\\Controllers\\'.$moduleName.'\\'. $controllerName . 'Controller';

        $moduleDir = ROOT_PATH.'app\\Controllers\\'.$moduleName;

        if (!is_dir(str_replace('\\', '/', $moduleDir)))
        {
            throw new Exception($moduleName . '模块不存在');
        }
        if (!class_exists($controller)) {
            throw new Exception($controller . '控制器不存在');
        }
        if (!method_exists($controller, $actionName)) {
            throw new Exception($actionName . '方法不存在');
        }

        //请求分发
        $dispatch = $this->$controller;

        call_user_func_array(array($dispatch, $actionName), $param);
    }


    /**
     * 获取配置参数 为空则获取所有配置
     * @access public
     * @param  string    $name 配置参数名（支持二级配置 .号分割）
     * @return mixed
     */
    public function config($name = '')
    {
        return $this->config->get($name);
    }

    /**
     * @return string
     * 获取应用目录
     */
    public function getAppPath()
    {
        if (is_null($this->appPath))
        {
            return ROOT_PATH.'app'.DIRECTORY_SEPARATOR;
        }

        return $this->appPath;
    }

    /**
     * @return mixed
     * 获取模块路径
     */
    public function getModulePath()
    {
        return $this->modulePath;
    }

    /**
     * @param string $path
     * @return $this
     * 设置模块路径
     */
    public function setModulePath($path = '')
    {
        if (is_null($path))
        {
            $this->modulePath = $path;

            return $this;
        }

        return $this->modulePath;
    }

    /**
     * 获取框架版本
     * @access public
     * @return string
     */
    public function version()
    {
        return static::VERSION;
    }

    /**
     * 获取日志目录
     * @return mixed
     */
    public function getStoragePath()
    {
        return $this->storagePath;
    }

    /**
     * 获取框架名称
     * @return string
     */
    public function getFrameName()
    {
        return $this->frameName = 'henryphp';
    }
    /**
     * 应用调试模式
     */
    public function setAppDebug()
    {
        $this->appDebug = $this->config('app.app_debug');

        if ($this->appDebug) {
            error_reporting(E_ALL);
            ini_set('display_errors','On');
        }
        elseif (PHP_SAPI != 'cli')
        {
            //重新申请一块比较大的buffer
            if (ob_get_level() > 0) {
                $output = ob_get_clean();
            }
            ob_start();
            if (!empty($output)) {
                echo $output;
            }
        }
        else
        {
            error_reporting(E_ALL);
            ini_set('display_errors','Off');
            ini_set('log_errors', 'On');
        }
    }

    /**
     * 是否为调试模式
     * @return bool
     */
    public function isDebug()
    {
        return $this->appDebug;
    }
    /**
     * 获取应用开启时间
     * @access public
     * @return float
     */
    public function getBeginTime()
    {
        return $this->beginTime;
    }

    /**
     * 获取应用初始内存占用
     * @access public
     * @return integer
     */
    public function getBeginMem()
    {
        return $this->beginMem;
    }

}