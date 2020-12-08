<?php
/**
 * Created by PhpStorm.
 * User: chniccs
 * Date: 2019-07-20
 * Time: 9:54
 */

namespace app\helper;


use app\constant\UploadConst;
use app\model\AdminOss;
use OSS\Core\OssException;
use OSS\OssClient;
use think\facade\Request;

class OssHelper
{
    /**
     * 通过服务器中转上传阿里云oss
     * @return array|string
     *
     */
    public static function uploadFile()
    {
        $file = request()->file('file');  //获取到上传的文件

        $arr = null;
        // 尝试执行
        try {
            $type = CommonHelper::getFileExName($file->getInfo()['name']);
            $config = self::getOssConfig(UploadConst::OSS);
            if (isset($file)) {
                $accessKeyId = $config['accesskey'];
                $accessKeySecret = $config['secretkey'];
                $endpoint = $config['endpoint'];
                $bucket = $config['bucket'];
                //实例化对象 将配置传入
                $ossClient = new OssClient($accessKeyId, $accessKeySecret, $endpoint);
                //这里是有sha1加密 生成文件名 之后连接上后缀
                $fileName = date('Ymd', time())."/" . uniqid() . $type;
                //执行阿里云上传
                $result = $ossClient->uploadFile($bucket, $fileName, $file->getInfo()['tmp_name']);
                /**
                 * 这个只是为了展示
                 * 可以删除或者保留下做后面的操作
                 */
                $arr = [
                    'done' => true,
                    'url' => $result['info']['url'],
                    'name' => $fileName
                ];
            }
        } catch (OssException $e) {
            return $arr = [
                'done' => false,
                'msg' => $e->getMessage()
            ];
        }
        return $arr;
    }

    //删除文件
    public static function deleteOssFile()
    {
        $config = self::getOssConfig(UploadConst::OSS);
        $key = Request::param('key');
        $result = null;
        if (isset($key)) {
            $accessKeyId = $config['accesskey'];
            $accessKeySecret = $config['secretkey'];
            $endpoint = $config['endpoint'];
            $bucket = $config['bucket'];
            try {
                $ossClient = new OssClient($accessKeyId, $accessKeySecret, $endpoint);
                $ossClient->deleteObject($bucket, $key);
                $result = [
                    'done' => true,
                    'msg' => '删除成功'
                ];
                return $result;
            } catch (OssException $e) {
                printf(__FUNCTION__ . ": FAILED\n");
                printf($e->getMessage() . "\n");
                $result = [
                    'done' => false,
                    'msg' => '删除失败' . $e->getMessage()
                ];
                return $result;
            }
        } else {
            $result = [
                'done' => false,
                'msg' => '删除失败,缺少参数key'
            ];
            return $result;
        }
    }

    public static function getOssConfig($type)
    {
        return AdminOss::get(['type' => $type], '', false);
    }
}
