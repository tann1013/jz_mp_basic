<?php
/**
 * Created by PhpStorm.
 * User: PVer
 * Date: 2018-12-26
 * Time: 9:26
 */

namespace app\admin\controller;


use app\helper\SmsHelper;
use app\util\ReturnCode;
use app\util\Tools;

class Sms extends Base
{
    public function getSmsCode()
    {
        $code = Tools::generate_code(6);
        $type = request()->param("type");
        $phone= request()->param("phone");
        // 短信签名 详见：https://dysms.console.aliyun.com/dysms.htm?spm=5176.2020520001.1001.3.psXEEJ#/sign
        $signName= self::getSignName($type);
        // 短信模板Code https://dysms.console.aliyun.com/dysms.htm?spm=5176.2020520001.1001.3.psXEEJ#/template
        $template_code = self::getTemplate($type);
        // 短信中的替换变量json字符串
        $param = array('code' => $code,'product'=>'趣砍');
        // 接收短信的手机号码
        $result = SmsHelper::sendSms($type,$phone,$signName,$template_code,$param);
        if($result)
        {
            return $this->buildSuccess(null,'发送成功',ReturnCode::SUCCESS);
        }
        return $this->buildFailed(ReturnCode::INVALID,'发送失败',null);
    }
    static function getTemplate($type){
        if($type=='login'){
            return 'SMS_4920125';
        }else if($type =='register'){
            return 'SMS_4920122';
        }else if($type =='forgot'){
            return 'SMS_4920120';
        }
        return false;
    }
    static function getSignName($type){
//        if($type=='login'){
//            return '登录验证';
//        }
        return '趣砍';

    }

    public function check(){
        $type = request()->param("type");
        $phone= request()->param("phone");
        $code= request()->param("code");
        $result = SmsHelper::checkSms($type,$phone,$code);
        if($result)
        {
            return $this->buildSuccess(null,'验证成功',ReturnCode::SUCCESS);
        }
        return $this->buildFailed(ReturnCode::INVALID,'验证失败',null);
    }
}