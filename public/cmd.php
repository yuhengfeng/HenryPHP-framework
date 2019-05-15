<?php
/**
 * henryphp - A PHP MVC Framework For test
 *
 * @author henry.yu <henry.yu1175@gmail.com>
 */

if ($argc < 2) {
    echo "命令格式错误，请按下面格式运行：\n";
    echo "{$_SERVER['_']} {$argv[0]} url [env]\n";
    exit();
}
date_default_timezone_set('Asia/Shanghai');

$aUrl = parse_url($argv[1]);

$sRouteUri = isset($aUrl['path']) ? $aUrl['path'] : '/';
$sRouteUri .= isset($aUrl['query']) ? '?' . $aUrl['query'] : '';
//定义脚本host
define('CMD_HOST',$aUrl['host']);
//定义脚本uri
define('CMD_URI',$sRouteUri);

define('HENRYPHP_START', microtime(true));

//定义应用目录常量
define('ROOT_PATH',dirname(__DIR__).DIRECTORY_SEPARATOR);

//定义异常目录
define('EXCEPTION_PATH',ROOT_PATH.'henryphp/library/tpl');


//获取相关的应用实例
require_once ROOT_PATH . 'core/app.php';
