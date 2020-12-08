<?php
/**
 * Created by PhpStorm.
 * User: chniccs
 * Date: 2019-08-01
 * Time: 16:14
 */

namespace app\miniprogram\helper;


use app\constant\RecordTypeConst;
use app\miniprogram\model\JzUserAccountType;

/**
 * 用户余额操作的管理类
 * Class UserAccountHelper
 * @package app\miniprogram\helper
 */
class UserAccountHelper
{
    /**
     * @param $uid
     * @param $accountType 3不选帐户 2微信  1支付宝 0现金 @LINK(AccountTypeConst)
     * @param $type 0减 1加
     * @param  $num  数值
     * @return bool 是否成功
     */
    public static function update($uid,$accountType,$type,$num){
        $data = JzUserAccountType::get(['uid'=>$uid,'account_type'=>$accountType]);
        if(!$data){
            return false;
        }
        $balance = ($data->getData())['balance'];
        //如果是收入的话，这里就应该去减掉余额，所以乘以-1
        if($type==RecordTypeConst::TYPE_ZC){
            $num = $num* -1;
        }
        $data->setAttr('balance',$balance+$num);
        //4.更新表中记录
        $affected = $data -> allowField(true) -> save();
        if (!empty($affected)) {
            return true;
        }else{
            return false;
        }
    }
    /**
     * @param $uid
     * @param $accountType 3不选帐户 2微信  1支付宝 0现金 @LINK(AccountTypeConst)
     * @param $type 原来的类型,0减 1加
     * @param  $num 数值
     * @return bool 是否成功
     */
    public static function reStore($uid,$accountType,$type,$num){
        if($type==RecordTypeConst::TYPE_ZC){
            $type = RecordTypeConst::TYPE_SR;
        }else{
            $type = RecordTypeConst::TYPE_ZC;
        }
        return UserAccountHelper::update($uid,$accountType,$type,$num);
    }
}