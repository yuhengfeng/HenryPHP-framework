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
    'error_message'  => '页面出现错误！请稍后再试呗～',

    //异常显示页面
    'exception_tmpl' => EXCEPTION_PATH.'/henryPHP_exception.tpl',

    'default_timezone' => 'Asia/Shanghai',

    //模块配置
    'modules' => [
        'default' => 'Web',
        'default_controller' => 'Index',
        'default_action' => 'index',
    ]
];