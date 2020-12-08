<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

return [
    'APP_VERSION'           => 'v4.0',
    'APP_NAME'              => 'ApiAdmin',

    //鉴权相关
    'USER_ADMINISTRATOR'    => [1],

    //安全秘钥
    'AUTH_KEY'              => '25fe3c00-4436-89f7-66e7-c1de1678f811',

    //后台登录状态维持时间[目前只有登录和解锁会重置登录时间]
    'ONLINE_TIME'           => 7200,
    //AccessToken失效时间
    'ACCESS_TOKEN_TIME_OUT' => 7200,
    //小程序Token失效时间
    'WX_TOKEN_TIME_OUT' => 7*24*60*60,
    //小程序Token失效时间
    'WX_AB_SHARE_TIME_OUT' => 2*24*60*60,
    //小程序Token失效时间
    'WX_AB_SHARE_PRE' => 'shareabkey_',
    'COMPANY_NAME'          => 'ApiAdmin开发维护团队',

    //跨域配置
    'CROSS_DOMAIN'          => [
        'Access-Control-Allow-Origin'      => '*',
        'Access-Control-Allow-Methods'     => 'POST,PUT,GET,DELETE',
        'Access-Control-Allow-Headers'     => 'version, access-token, user-token, apiAuth, User-Agent, Keep-Alive, Origin, No-Cache, X-Requested-With, If-Modified-Since, Pragma, Last-Modified, Cache-Control, Expires, Content-Type, X-E4M-With',
        'Access-Control-Allow-Credentials' => 'true'
    ],

    //后台列表默认一页显示数量
    'ADMIN_LIST_DEFAULT'    => 20,
];
