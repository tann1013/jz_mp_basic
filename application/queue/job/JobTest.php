<?php
/**
 * Created by PhpStorm.
 * User: chniccs
 * Date: 2019-07-20
 * Time: 14:18
 */

namespace app\queue\job;


use app\model\AdminQueueMessage;
use app\queue\QueueTypes;
use think\Exception;
use think\facade\Log;
use think\queue\Job;

class JobTest{

    public function fire(Job $job, $data){

        //....这里执行具体的任务
        print("<info>开始执行，数据为: " . var_export($data, true) . "</info> \n");
        if ($job->attempts() > 3) {
            //通过这个方法可以检查这个任务已经重试了几次了
        }

        $this->failed($data);
        //如果任务执行成功后 记得删除任务，不然这个任务会重复执行，直到达到最大重试次数后失败后，执行failed方法
        $job->delete();

        // 也可以重新发布这个任务
//        $job->release(3); //$delay为延迟时间

    }

    public function failed($data){

        // ...任务达到最大重试次数后，失败了
        try{

            //添加了失败记录中
            AdminQueueMessage::create(["type" => QueueTypes::CLOSE_ORDER, 'level' => 6, 'content' => json_encode($data)]);
        }catch (Exception $e){
            Log::write("关闭订单队列操作失败，且任务记录失败" . json_encode($data),'error');
        }
    }

}