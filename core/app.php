<?php
/**
 * Created by PhpStorm.
 * User: henryu
 * Date: 11/7/18
 * Time: 下午2:35
 */

use \henryphp\Container;
use \henryphp\Loader;
use \henryphp\Error;

// 载入Loader类
require ROOT_PATH . '/henryphp/library/Loader.php';

// 实现日志接口
if (interface_exists('Psr\Log\LoggerInterface')) {
    interface LoggerInterface extends \Psr\Log\LoggerInterface
    {}
} else {
    interface LoggerInterface
    {}
}

//自动加载核心类库
Loader::register();

// 注册错误和异常处理机制
Error::register();


Container::get('app')->run();
