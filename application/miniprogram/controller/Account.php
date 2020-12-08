<?php
/**
 * Created by PhpStorm.
 * User: chniccs
 * Date: 2019-07-29
 * Time: 9:54
 */

namespace app\miniprogram\controller;

//资产账户管理控制器
use app\miniprogram\model\JzUserAccountType;
use app\util\ReturnCode;
use app\util\Tools;
use think\exception\DbException;

class Account extends Base
{
    public function getBalance(){
        $uid = $this->userInfo['id'];
        try {
            $res = (new JzUserAccountType())->where(['uid' => $uid])->field('account_type,balance')->select()->toArray();
            if($res){
                $formatData = Tools::buildArrByNewKey($res,'account_type');
                return $this->buildSuccess($formatData);
            }
        }catch (DbException $e) {
        }

        return $this->buildFailed(ReturnCode::INVALID,'获取资产信息失败');
    }
    public function balanceEdit(){
        $type = $this->request->param("type");
        $balance = $this->request->param("balance");
        $uid = $this->userInfo['id'];
        $model = JzUserAccountType::update(['balance'=>$balance],['uid'=>$uid,"account_type"=>$type]);
        if(isset($model)&&$model){
            return $this->buildSuccess(null);
        }
        return $this->buildFailed(ReturnCode::EDIT_FAILED,'更新失败');
    }
}