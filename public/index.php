<?php
/**
 * henryphp - A PHP MVC Framework For test
 *
 * @author henry.yu <henry.yu1175@gmail.com>
 */

define('HENRYPHP_START', microtime(true));

//定义应用目录常量
define('ROOT_PATH',dirname(__DIR__).DIRECTORY_SEPARATOR);

//定义异常目录
define('EXCEPTION_PATH',ROOT_PATH.'henryphp/library/tpl');


//获取相关的应用实例
require_once ROOT_PATH . 'core/app.php';
