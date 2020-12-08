<?php
/**
 * Created by PhpStorm.
 * User: chniccs
 * Date: 2019-07-20
 * Time: 10:05
 */

namespace app\admin\controller;


use app\helper\OssHelper;
use app\util\ReturnCode;

class Oss extends Base
{
    /**
     * 通过服务器中转上传阿里云oss
     * @return array|string
     *
     */
    public function uploadFile()
    {
        $result = OssHelper::uploadFile();
        if(!$result['done']){
            return $this->buildFailed(ReturnCode::INVALID,'上传失败',$result);
        }else{
            return $this->buildSuccess($result,'上传成功');
        }
    }
    //删除文件
    function deleteFile()
    {
        $result = OssHelper::deleteOssFile();
        if (isset($result)&&$result['done']) {
            return $this->buildSuccess(null, '删除成功', ReturnCode::SUCCESS);
        } else {
            return $this->buildFailed(ReturnCode::DELETE_FAILED, '删除失败',$result );
        }
    }
}