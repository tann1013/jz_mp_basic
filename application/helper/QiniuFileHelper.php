<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/9/009
 * Time: 21:42
 */
namespace app\helper;

use app\model\AdminOss;
use Qiniu\Auth;
use Qiniu\Processing\PersistentFop;
use Qiniu\Storage\BucketManager;
use app\constant\QiniuConst;
use think\facade\Request;


// require_once QiniuConst::QNSDK_INIT;//七牛sdk

class QiniuFileHelper{
    const AUTH_KEY = 'qiniu_auth_key';
    const AUTH_TOKEN = 'qiniu_auth_token';
    static $auth = null;

    //获得授权对象
    public static function getauth(){
        if(QiniuFileHelper::$auth == null){
            $config = AdminOss::get(['type' => 0], '', true);
            QiniuFileHelper::$auth = new Auth($config['accesskey'],$config['secretkey']);
        }
        return QiniuFileHelper::$auth;
    }
    public static function getBM(){
        //初始化BucketManager
        $bucketMgr = new BucketManager(QiniuFileHelper::getauth());
        return $bucketMgr;
    }
    public static function deleteFile($bucket, $key){
        //初始化BucketManager
        $bucketMgr = QiniuFileHelper::getBM();
        //删除文件
        $result = $bucketMgr->delete($bucket,$key);
        if($result !==null ){
            return false;
        }else{
            return true;
        }
    }
    //批量删除
    public static function batch($operations){
        $bm = QiniuFileHelper::getBM();
        $resultStr = $bm -> batch($operations);
        return $resultStr;
    }
    public static function getBucket(){
        return QiniuConst::BUCKET;
    }

    //验证回调
    public static function checkIsMkzipCallback(){
        $auth = QiniuFileHelper::getauth();
        //获取回调的body信息
        $callbackBody = file_get_contents('php://input');
        //回调的contentType
        $contentType = 'application/json';
        //回调的签名信息，可以验证该回调是否来自七牛
        $authorization = Request::instance()->header('Authorization');
        $url = QiniuConst::getMkzipNotifyUrl();
        $isQiniuCallback = $auth->verifyCallback($contentType, $authorization, $url, $callbackBody);
        if ($isQiniuCallback) {
            return true;
        } else {
            return false;
        }
    }
    //检查是否是七牛的回调
    public static function checkIsUploadCallback(){
        $auth = QiniuFileHelper::getauth();
        //获取回调的body信息
        $callbackBody = file_get_contents('php://input');
        //回调的contentType
        $contentType = 'application/x-www-form-urlencoded';
        //回调的签名信息，可以验证该回调是否来自七牛
        $authorization = Request::instance()->header('Authorization');
        // $authorization = $_SERVER['HTTP_ACCEPT_ENCODING'];

        // $authorization = getallheaders()['Authorization'];//这种获取方式只用以apache作为服务器时才有用
        $url = QiniuConst::getNotifyUrl();
        $isQiniuCallback = $auth->verifyCallback($contentType, $authorization, $url, $callbackBody);
        if ($isQiniuCallback) {
            return true;
        } else {
            return false;
        }
    }
    //压缩
    public static function mkzip($own_id,$bucket,$pipeline,$notifyUrl,$host,$resourcesList,$key){
        $auth = QiniuFileHelper::getauth();
        //***获得任务队列***
        $pfop = new PersistentFop($auth, $bucket, $pipeline,$notifyUrl);
        //***开始设置压缩参数***
        //设置功能是压缩
        $fops = 'mkzip/2';
        //对地址进行base64编码
        $count = count($resourcesList);
        for ($i=0; $i < $count; $i++) {
            $fops .= '/url/' . \Qiniu\base64_urlSafeEncode($host . trim($resourcesList[$i]));
            // echo $resourcesList[$i];
        }
        //设置保存名
        $zipKey = $own_id . '.zip';
        $fops .= '|saveas/' . \Qiniu\base64_urlSafeEncode("$bucket:$zipKey");
        return $pfop->execute($key, $fops);
    }
}
?>