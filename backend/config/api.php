<?php
return [
    //公共接口
    'common' => [
        'prefix' => env('API_ROUTE_PREFIX', 'api'),
        'name' => env('API_ROUTE_NAME', 'api.'),
        'namespace' => 'App\\Api\\Controllers\\Common',
        'middleware' => [],
    ],

    'admin' => [
        'prefix' => env('API_ROUTE_PREFIX', 'api'),
        'name' => env('API_ROUTE_NAME', 'admin.'),
        'namespace' => 'App\\Api\\Controllers\\Admin',
        'middleware' => [],
    ],
];
