<?php
/**
 * Created by PhpStorm.
 * User: chniccs
 * Date: 2019-01-04
 * Time: 15:41
 */
namespace app\queue;
class QueueTypes
{
    const PREFIX = 'QianYu';
// 1-关闭超时订单 2-发送消息
    const CLOSE_ORDER = 1;
    const SEND_MSG = 2;

    const CLASS_PATH = 'app\queue\job';
    static public function getQueue($type)
    {
        $queue = '';
        switch ($type) {
            case self::CLOSE_ORDER:
                $queue = self::PREFIX . 'Test';
                break;
        }
        return $queue;
    }

    static public function getQueueClass($type)
    {
        $class = '';
        switch ($type) {
            case self::CLOSE_ORDER:
                $class = self::CLASS_PATH . '\JobTest';
                break;
        }
        return $class;
    }
}