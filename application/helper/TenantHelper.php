<?php


namespace app\helper;


/*
 * 用于处理多租户的问题，通过统一的方法来添加租户标识，便于后期的数据库分表
 */

use think\facade\Request;

class TenantHelper
{
    /**
     * @param $where
     * @return bool 如果没有tid的话就会返回false
     */
    public static function addTid($where)
    {
        $tid = self::getUserTid()['tid'];
        if ($tid) {
            $where['tid'] = $tid;
            return $where;
        } else {
            return false;
        }
    }

    /**
     * @return array 用户的信息
     */
    public static function getUserTid()
    {
        $ApiAuth = Request::header("apiAuth", "");
        $userInfo = false;
        if ($ApiAuth) {
            $userInfo = cache('Login:' . $ApiAuth);
        }
        return $userInfo;
    }

}