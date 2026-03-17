<?php
return [
    //路由相关配置
    'route' => [
        'prefix' => env('ADMIN_ROUTE_PREFIX', 'admin'),
        'name' => env('ADMIN_ROUTE_NAME', 'admin.'),
        'namespace' => 'App\\Admin\\Controllers',
        'middleware' => [],
    ],
];
