<?php

return [

    'default' => env('DB_CONNECTION', 'mysql'),

    'connections' => [
        'mysql' => [
            'driver'    => 'mysql',
            'host'      => env('DB_HOST','127.0.0.1'),
            'database'  => env('DB_DATABASE', 'henry_blog'),
            'username'  => env('DB_USERNAME', 'root'),
            'password'  => env('DB_PASSWORD', ''),
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => env('DB_PREFIX',''),
            'strict'    => false,
        ],
//        'mysql_main' => [
//            'driver'    => 'mysql',
//            'host'      => env('DB_MAIN_HOST', '127.0.0.1'),
//            'database'  => env('DB_MAIN_DATABASE', 'test_blog'),
//            'username'  => env('DB_MAIN_USERNAME', 'root'),
//            'password'  => env('DB_MAIN_PASSWORD', ''),
//            'charset'   => 'utf8',
//            'collation' => 'utf8_unicode_ci',
//            'prefix'    => '',
//            'strict'    => false,
//        ],

    ],


    'migrations' => 'migrations',

    'redis' => [

        'client' => 'predis',

        'default' => [
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'password' => env('REDIS_PASSWORD', null),
            'port' => env('REDIS_PORT', 6379),
            'database' => 0,
        ],
        'session' => [
            'host' => env('REDIS_HOST', 'localhost'),
            'password' => env('REDIS_PASSWORD', null),
            'port' => env('REDIS_PORT', 6379),
            'database' => 1,
        ],
        'queue' => [
            'host' => env('REDIS_HOST', 'localhost'),
            'password' => env('REDIS_PASSWORD', null),
            'port' => env('REDIS_PORT', 6379),
            'database' => 2,
        ],
    ],

];
