<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/1/23/023
 * Time: 21:12
 */

namespace app\queue;
use think\Queue;

class QueuePub
{
    public static function later($delay,$job,$data,$queue){
        return Queue::later($delay,$job,$data,$queue);
    }
    public static function push($job,$data,$queue){
        return Queue::push($job,$data,$queue);
    }
}