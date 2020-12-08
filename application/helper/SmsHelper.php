<?php
/**
 * Created by PhpStorm.
 * User: PVer
 * Date: 2018-12-26
 * Time: 10:34
 */

namespace app\helper;

use Aliyun\Core\Config as AliyunConfig;
use Aliyun\Core\Profile\DefaultProfile;
use Aliyun\Core\DefaultAcsClient;
use Aliyun\Api\Sms\Request\V20170525\SendSmsRequest;
use think\facade\Cache;

class SmsHelper
{
    /**
     * @param $type String 短信类型
     * @param $phone String 手机号
     * @param $signName String 签名
     * @param $template_code String 模板code
     * @param $param array 短信参数对象
     * @return bool 是否发送成功
     */
    public static function sendSms($type, $phone, $signName, $template_code, $param)
    {


        // 阿里云Access Key ID和Access Key Secret 从 https://ak-console.aliyun.com 获取
        $appKey = 'LTAIPigZNTm98LOK';
        $appSecret = 'D19AQH5KxjLcjTVoQimBmQLdt9yufs';
        // 短信中的替换变量json字符串
        $json_string_param = json_encode($param);
        // 初始化阿里云config
        AliyunConfig::load();
        // 初始化用户Profile实例
        $profile = DefaultProfile::getProfile("cn-hangzhou", $appKey, $appSecret);
        DefaultProfile::addEndpoint("cn-hangzhou", "cn-hangzhou", "Dysmsapi", "dysmsapi.aliyuncs.com");
        $acsClient = new DefaultAcsClient($profile);
        // 初始化SendSmsRequest实例用于设置发送短信的参数
        $request = new SendSmsRequest();
        // 必填，设置短信接收号码
        $request->setPhoneNumbers($phone);
        // 必填，设置签名名称
        $request->setSignName($signName);
        // 必填，设置模板CODE
        $request->setTemplateCode($template_code);

        // 可选，设置模板参数
        if (!empty($json_string_param)) {
            $request->setTemplateParam($json_string_param);
        }


        // 可选，设置流水号
        // if($outId) {
        //     $request->setOutId($outId);
        // }
        // 发起请求
        $acsResponse = $acsClient->getAcsResponse($request);
        // 默认返回stdClass，通过返回值的Code属性来判断发送成功与否
        if ($acsResponse && strtolower($acsResponse->Code) == 'ok') {
            //记录当前ip操作的时间

            try {
                //记录操作的Ip及时间，防止刷短信
                $date = new \DateTime();
                $date->add(new \DateInterval('PT1M'));
                $ip = request()->ip();
                Cache::set('sms_' . $ip, $phone, $date);
                if (isset($param['code'])) {
                    $date = new \DateTime();
                    //设置有效期为10分钟
                    $date->add(new \DateInterval('PT10M'));
                    Cache::set($param['code'] . '_sms_' . $type . $phone, true, $date);
                }
            } catch (\Exception $e) {
                return false;
            }
            return true;
        }
        return false;
    }

    /**
     * @param $type String 短信类型
     * @param $phone String 手机号
     * @param $code String 验证码
     * @return mixed
     */
    public static function checkSms($type, $phone, $code)
    {
        $checkResult = Cache::get($code . '_sms_' . $type . $phone);
        if ($checkResult) {
            Cache::rm($code . '_sms_' . $type . $phone);
            return true;
        } else {
            return false;
        }
    }

}