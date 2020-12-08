# jz_api

#### 介绍
云雀记账后端程序

#### 使用说明
本项目为云雀记账小程序后端程序，[云雀记账](https://gitee.com/chniccs/jizhang)

#### 软件架构
后端程序基础框架使用的是[apiAdmin](https://gitee.com/apiadmin/ApiAdmin)
整体环境需求与apiAdmin一致，使用前可参考apiAdmin的使用教程。 

此外，项目还使用了第三方框架 
- easeWechat
- qiniu/php-sdk
- aliyuncs/oss-sdk-php   
...

因为程序之前为本人在apiAdmin基础上做过一些变动，添加了云存储、消息队列、短信及邮件发送等功能（ **本项目并未使用到** ），     

 **若只关注记账小程序的部分功能，只需要查看application/miniProgram目录下的程序即可** 

#### 安装教程

1.  克隆项目到本地

```
git clone https://gitee.com/chniccs/jz_api.git
```
2.  安装依赖

```
composer install
```
3.  导入数据库
数据库文件是在根目录下的jz.sql文件

4.  服务器配置
参考apiAdmin服务器配置，其中主要就是nginx配置
```
location / {
	if (!-e $request_filename){
		rewrite  ^(.*)$  /index.php?s=$1  last;   break;
	}
}
```
5. 部分配置文件
因目前后端服务还在线上运行，所以部分较为隐私的配置信息并未上传，获取源码后请自行在config目录下添加以下文件
- app.php（应用配置）
可参考以下配置

```
<?php

use think\facade\Env;
return [
    // 应用名称
    'app_name'               => '',
    // 应用地址
    'app_host'               => '',
    // 应用调试模式
    'app_debug'              => true,
    // 应用Trace
    'app_trace'              => false,
    // 是否支持多模块
    'app_multi_module'       => true,
    // 入口自动绑定模块
    'auto_bind_module'       => true,
    // 注册的根命名空间
    'root_namespace'         => [],
    // 默认输出类型
    'default_return_type'    => 'json',
    // 默认AJAX 数据返回格式,可选json xml ...
    'default_ajax_return'    => 'json',
    // 默认JSONP格式返回的处理方法
    'default_jsonp_handler'  => 'jsonpReturn',
    // 默认JSONP处理方法
    'var_jsonp_handler'      => 'callback',
    // 默认时区
    'default_timezone'       => 'Asia/Shanghai',
    // 是否开启多语言
    'lang_switch_on'         => false,
    // 默认全局过滤方法 用逗号分隔多个
    'default_filter'         => '',
    // 默认语言
    'default_lang'           => 'zh-cn',
    // 应用类库后缀
    'class_suffix'           => false,
    // 控制器类后缀
    'controller_suffix'      => false,

    // +----------------------------------------------------------------------
    // | 模块设置
    // +----------------------------------------------------------------------

    // 默认模块名
    'default_module'         => 'api',
    // 禁止访问模块
    'deny_module_list'       => ['http', 'install', 'model', 'util'],
    // 默认控制器名
    'default_controller'     => 'Index',
    // 默认操作名
    'default_action'         => 'index',
    // 默认验证器
    'default_validate'       => '',
    // 默认的空模块名
    'empty_module'           => '',
    // 默认的空控制器名
    'empty_controller'       => 'Error',
    // 操作方法前缀
    'use_action_prefix'      => false,
    // 操作方法后缀
    'action_suffix'          => '',
    // 自动搜索控制器
    'controller_auto_search' => false,

    // +----------------------------------------------------------------------
    // | URL设置
    // +----------------------------------------------------------------------

    // PATHINFO变量名 用于兼容模式
    'var_pathinfo'           => 's',
    // 兼容PATH_INFO获取
    'pathinfo_fetch'         => ['ORIG_PATH_INFO', 'REDIRECT_PATH_INFO', 'REDIRECT_URL'],
    // pathinfo分隔符
    'pathinfo_depr'          => '/',
    // HTTPS代理标识
    'https_agent_name'       => '',
    // IP代理获取标识
    'http_agent_ip'          => 'X-REAL-IP',
    // URL伪静态后缀
    'url_html_suffix'        => 'html',
    // URL普通方式参数 用于自动生成
    'url_common_param'       => false,
    // URL参数方式 0 按名称成对解析 1 按顺序解析
    'url_param_type'         => 0,
    // 是否开启路由延迟解析
    'url_lazy_route'         => false,
    // 是否强制使用路由
    'url_route_must'         => true,
    // 合并路由规则
    'route_rule_merge'       => false,
    // 路由是否完全匹配
    'route_complete_match'   => false,
    // 使用注解路由
    'route_annotation'       => false,
    // 域名根，如thinkphp.cn
    'url_domain_root'        => '',
    // 是否自动转换URL中的控制器和操作名
    'url_convert'            => true,
    // 默认的访问控制器层
    'url_controller_layer'   => 'controller',
    // 表单请求类型伪装变量
    'var_method'             => '_method',
    // 表单ajax伪装变量
    'var_ajax'               => '_ajax',
    // 表单pjax伪装变量
    'var_pjax'               => '_pjax',
    // 是否开启请求缓存 true自动缓存 支持设置请求缓存规则
    'request_cache'          => false,
    // 请求缓存有效期
    'request_cache_expire'   => null,
    // 全局请求缓存排除规则
    'request_cache_except'   => [],
    // 是否开启路由缓存
    'route_check_cache'      => false,
    // 路由缓存的Key自定义设置（闭包），默认为当前URL和请求类型的md5
    'route_check_cache_key'  => '',
    // 路由缓存类型及参数
    'route_cache_option'     => [],

    // 默认跳转页面对应的模板文件
    'dispatch_success_tmpl'  => Env::get('think_path') . 'tpl/dispatch_jump.tpl',
    'dispatch_error_tmpl'    => Env::get('think_path') . 'tpl/dispatch_jump.tpl',

    // 异常页面的模板文件
    'exception_tmpl'         => Env::get('think_path') . 'tpl/think_exception.tpl',

    // 错误显示信息,非调试模式有效
    'error_message'          => '页面错误！请稍后再试～',
    // 显示错误信息
    'show_error_msg'         => false,
    // 异常处理handle类 留空使用 \think\exception\Handle
    'exception_handle'       => '',

];

```

- cache.php（redis缓存配置）
可参考以下配置

```
<?php

// +----------------------------------------------------------------------
// | 缓存设置
// +----------------------------------------------------------------------

return [
    // 驱动方式
    'type'   => 'Redis',
    'host'   => '127.0.0.1',
    // 缓存保存目录
    'path'   => '',
    // 缓存前缀
    'prefix' => 'dev_jz_zhang_',
    // 缓存有效期 0表示永久缓存
    'expire' => 15*24*60&60,//最长15天
];

```

- database.php（数据库配置）
可参考以下配置


```
<?php

return [
    // 数据库类型
    'type'            => 'mysql',
    // 服务器地址
    'hostname'        => '127.0.0.1',
    // 数据库名
    'database'        => '你的数据库名',
    // 用户名
    'username'        => '你的数据库用户名',
    // 密码
    'password'        => '你的数据库密码',
    // 端口
    'hostport'        => '3306',
    // 连接dsn
    'dsn'             => '',
    // 数据库连接参数
    'params'          => [],
    // 数据库编码默认采用utf8
    'charset'         => 'utf8',
    // 数据库表前缀
    'prefix'          => '',
    // 数据库调试模式
    'debug'           => true,
    // 数据库部署方式:0 集中式(单一服务器),1 分布式(主从服务器)
    'deploy'          => 0,
    // 数据库读写是否分离 主从式有效
    'rw_separate'     => false,
    // 读写分离后 主服务器数量
    'master_num'      => 1,
    // 指定从服务器序号
    'slave_no'        => '',
    // 自动读取主库数据
    'read_master'     => false,
    // 是否严格检查字段是否存在
    'fields_strict'   => true,
    // 数据集返回类型
    'resultset_type'  => 'array',
    // 自动写入时间戳字段
//    'auto_timestamp'  => false,
    'auto_timestamp'  => 'datetime',
    // 时间字段取出后的默认时间格式
    'datetime_format' => 'Y-m-d H:i:s',
    // 是否需要进行SQL性能分析
    'sql_explain'     => false,
    // Builder类
    'builder'         => '',
    // Query类
    'query'           => '\\think\\db\\Query',
    // 是否需要断线重连
    'break_reconnect' => false,
    // 断线标识字符串
    'break_match_str' => [],
];

```
- wechat.php（主要为easeWechat配置）
可参考以下配置

```
<?php
/**
 * Created by PhpStorm.
 * User: chniccs
 * Date: 2019-07-22
 * Time: 16:23
 * 微信配置信息
 */

use think\facade\Env;

return [
//    公众号的
    "officialAccount" => [
        /**
         * Debug 模式，bool 值：true/false
         *
         * 当值为 false 时，所有的日志都不会记录
         */
        'debug' => true,
        /**
         * 账号基本信息，请从微信公众平台/开放平台获取
         */
        'app_id' => '15615',         // AppID
        'secret' => '',     // AppSecret
        'token' => '',          // Token
        'aes_key' => '',                    // EncodingAESKey，安全模式下请一定要填写！！！
        /**
         * 日志配置
         *
         * level: 日志级别, 可选为：
         *         debug/info/notice/warning/error/critical/alert/emergency
         * permission：日志文件权限(可选)，默认为null（若为null值,monolog会取0644）
         * file：日志文件位置(绝对路径!!!)，要求可写权限
         */
        'log' => [
            'level' => 'debug',
            'permission' => 0777,
            'file' => Env::get('root_path') . 'runtime/logo/wechat_officialAccount.log',
        ],
        /**
         * OAuth 配置
         *
         * scopes：公众平台（snsapi_userinfo / snsapi_base），开放平台：snsapi_login
         * callback：OAuth授权完成后的回调页地址
         */
        'oauth' => [
            'scopes' => ['snsapi_userinfo'],
            'callback' => 'home/oauthallback',
        ],
        /**
         * 微信支付
         */
        'payment' => [
            'merchant_id' => '', // 商户号
            'key' => '',
            'cert_path' => '', // XXX: 绝对路径！！！！
            'key_path' => '',      // XXX: 绝对路径！！！！
            // 'device_info'     => '013467007045764',
            // 'sub_app_id'      => '',
            // 'sub_merchant_id' => '',
            // ...
        ],
        /**
         * Guzzle 全局设置
         *
         * 更多请参考： http://docs.guzzlephp.org/en/latest/request-options.html
         */
        'guzzle' => [
            'timeout' => 3.0, // 超时时间（秒）
            'verify' => true, // 关掉 SSL 认证（强烈不建议！！！）
        ]

    ],
//    小程序的配置
    'miniprogram' => [
        'app_id' => '这里填入你的微信小程序appid',
        'secret' => '这里填入你的微信小程序的app_secret',

        // 下面为可选项
        // 指定 API 调用返回结果的类型：array(default)/collection/object/raw/自定义类名
        'response_type' => 'array',

        'log' => [
            'level' => 'debug',
            'file' => Env::get('root_path') . 'runtime/logo/wechat_miniProgram.log',
        ],
    ]
];
```
