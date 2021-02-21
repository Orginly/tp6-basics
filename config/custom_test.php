<?php

/**
 * 测试环境全局自定义参数
 * User: orginly
 * Date: 2021/2/10
 * Time: 15:00
 */

return [
    'user_redis'      => 'user_info', //用户信息的redis缓存
    'api_login_redis' => 'api_token', //登录记录redis缓存
    'api_out_auth'    => ['user/login',], //接口无需鉴权的路由(与路由文件里对应,区分大小写)
    'payment_type'    => [0 => '系统币种', 1 => '银行卡支付', 2 => '微信支付', 3 => '支付宝支付'], //订单支付方式
    'first'           => 'U', //编号首字母
    'pageSize'        => 10,  //分页大小
    'redis'           => [
        'host'     => env('redis.host'),
        'port'     => env('redis.port'),
        'password' => env('redis.password'),
        'select'   => env('redis.select'),
        'timeout'  => env('redis.timeout'),
        'pre'      => env('redis.pre') //redis记录头部标识(按项目更改)
    ],
];
