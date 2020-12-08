<?php
/**
 * Created by PhpStorm.
 * User: chniccs
 * Date: 2019-07-22
 * Time: 16:59
 */

namespace app\miniprogram\controller;


use EasyWeChat\Factory;
use think\facade\Config;

class Base extends \app\admin\controller\Base
{
    private $wx = null;
    private $config = null;
    public function __construct()
    {
        parent::__construct();

    }
    public function index(){

    }

    /**
     * 懒加载获取小程序配置
     * @return mixed|null
     */
    public function getConfig(){
        if($this->config == null){
            $this->config = Config::get('wechat.miniprogram');
        }
        return $this->config;
    }

    /**
     * 懒加载获取easywechat操作的工厂类
     * @return \EasyWeChat\MiniProgram\Application|null
     */
    public function getWx(){
        if($this->wx == null){
            $this->wx = Factory::miniProgram($this->getConfig());;
        }
        return $this->wx;
    }
}