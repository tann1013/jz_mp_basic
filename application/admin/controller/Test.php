<?php
namespace app\admin\controller;

use app\queue\QueuePub;
use app\queue\QueueTypes;
use think\Controller;

class Test extends Controller
{
    public function index()
    {
//        try {
//            echo ("<info>进入判断：</info>");
//            $orderData = AdminOrder::get(['id' => '456f4ds56f4sd64f56sd', 'status' => OrderStatus::STATUS_UNPAY])->getData();
//            echo ("<info>数据查询：</info>");
//            if($orderData){
//                return true;
//            }else{
//                return false;
//            }
//        } catch (Exception $e) {
//            print("<info>出现异常：</info>");
//            return false;
//        }

        $isPushed=QueuePub::later(2,QueueTypes::getQueueClass(QueueTypes::CLOSE_ORDER),['id'	=> '456f4ds56f4sd64f56sd'],QueueTypes::getQueue(QueueTypes::CLOSE_ORDER));
        if( $isPushed !== false ){
            echo date('Y-m-d H:i:s') . " a new Hello Job is Pushed to the MQ"."<br>";
        }else{
            echo 'Oops, something went wrong.';
        }
    }

}
