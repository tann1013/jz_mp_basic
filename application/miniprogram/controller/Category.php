<?php
/**
 * Created by PhpStorm.
 * User: chniccs
 * Date: 2019-07-29
 * Time: 10:57
 */

namespace app\miniprogram\controller;

//记账中的事项分类
use app\miniprogram\model\JzCategory;
use app\util\ReturnCode;
use think\exception\DbException;

class Category extends Base
{
    public function getList(){
        try {
            $model = (new JzCategory());
            $defaultZc = $model->where(['is_custom'=> 0,'del'=>0,'type'=>0])->field('name,id,icon,is_custom')->select()->toArray();
            $defaultSr = $model->where(['is_custom'=> 0,'del'=>0,'type'=>1])->field('name,id,icon,is_custom')->select()->toArray();
            return $this->buildSuccess(['zcCategory'=>$defaultZc,'srCategory'=>$defaultSr]);
        }  catch (DbException $e) {
            return $this->buildFailed(ReturnCode::INVALID,'获取分类失败');
        }
    }

    /** 获取不区分类型的数组，主要为记录列表页中的数据提供显示图标的作用
     * @return array
     */
    public function getUnTypeList(){
        try {
            $model = (new JzCategory());
            $default = $model->where(['is_custom'=> 0,'del'=>0])->field('id,icon,name')->select()->toArray();
            return $this->buildSuccess($default);
        }  catch (DbException $e) {
            return $this->buildFailed(ReturnCode::INVALID,'获取分类失败');
        }
    }

}