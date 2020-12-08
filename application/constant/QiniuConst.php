<?php

namespace app\constant;

class QiniuConst
{

    const  BASE_URL = 'http://api.shangyee.site/admin/';
    const DB_QINIU = 'qiniu';
    // const BUCKET = 'anchu-test';
    // const BUCKET = 'demo';
    const BUCKET = 'kanjia';

    const TB_QINIU_USER = 'qiniu_user';
    const TB_QINIU_FOLDER = 'qiniu_folder';
    const TB_QINIU_FILES = 'qiniu_files';
    const TB_QINIU_BUCKET = 'qiniu_bucket';
    const TB_QINIU_RES = 'qiniu_resources';
    const NUM_LIMIT = 50;
    //AccessKey
    const AK = 'AhjeVFPALrhk1eEq7be60YN1jrQxGSsFOuRI2a_L';
    //SecretKey
    const SK = '8rB8zD4W-9hgOkbEFeu5U-D-e3QGPKc-9oslAHIJ';
    //回调地址
    public static function getNotifyUrl(){
        return self::BASE_URL . 'QiNiu/notify';
    }
    //压缩文件（持久化处理的）回调地址
    public static function getMkzipNotifyUrl(){
        return QiniuConst::BASE_URL . 'mkzip_notify';
    }
    //数据库密码
    const PASSWORD = '334200';
    //七牛初始化入口
    const QNSDK_INIT = '/var/www/html/tp5/vendor/autoload.php';
    //Redis缓存时间
    const REDIS_CACHE_TIME = 3599;
    //redis缓存的空间host标识 
    const REDIS_HOST_KEY = 'qiniu_host_';

    //保存文件出错的错误码
    const PARENT_ID_ERROR = 10001;
    const SAVE_FAILED = 10002;
    //

    //数据库配置
    static $DB_CONF = [// 数据库类型
        'type' => 'mysql',
        // 服务器地址
        'hostname' => '127.0.0.1',
        // 数据库名
        'database' => QiniuConst::DB_QINIU,
        // 数据库用户名
        'username' => 'root',
        // 数据库密码
        'password' => QiniuConst::PASSWORD,
        // 数据库编码默认采用utf8
        'charset' => 'utf8',
        // 数据库表前缀
        'prefix' => '',
        // 数据库调试模式
        'debug' => false,];

}

?>