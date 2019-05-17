<?php
/**
 * Created by PhpStorm.
 * User: henryu
 * Date: 9/7/18
 * Time: 上午11:43
 */

return [
    //是否开启debug模式
    'app_debug' => true,

    // 错误显示信息,非调试模式有效
    'error_message'  => env('ERROR_MESSAGE','页面出现错误！请稍后再试呗～'),

    //异常显示页面
    'exception_tmpl' => EXCEPTION_PATH.'/henryPHP_exception.tpl',

    'default_timezone' => env('DEFAULT_TIMEZONE','Asia/Shanghai'),

    //默认模块配置
    'modules' => [
        'default' => env('DEFAULT_MODULES','Web'),
        'default_controller' => env('DEFAULT_CONTROLLER','Index'),
        'default_action' => env('DEFAULT_ACTION','index'),
    ]
];