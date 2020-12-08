<?php
/**
 * 错误码统一维护
 * @since   2017/02/28 创建
 * @author  zhaoxiang <zhaoxiang051405@gmail.com>
 */

namespace app\util;

class ReturnCode {

    const SUCCESS = 1;
    const INVALID = -1;
    const DB_SAVE_ERROR = -2;
    const DB_READ_ERROR = -3;
    const CACHE_SAVE_ERROR = -4;
    const CACHE_READ_ERROR = -5;
    const FILE_SAVE_ERROR = -6;
    const LOGIN_ERROR = -7;
    const NOT_EXISTS = -8;
    const JSON_PARSE_FAIL = -9;
    const TYPE_ERROR = -10;
    const NUMBER_MATCH_ERROR = -11;
    const EMPTY_PARAMS = -12;
    const DATA_EXISTS = -13;
    const AUTH_ERROR = -14;

    const OTHER_LOGIN = -16;
    const VERSION_INVALID = -17;

    const CURL_ERROR = -18;

    const RECORD_NOT_FOUND = -19; // 记录未找到
    const DELETE_FAILED = -20; // 删除失败
    const ADD_FAILED = -21; // 添加记录失败
    const UPDATE_FAILED = -22; // 添加记录失败
    const EDIT_FAILED = -23; // 修改记录失败

    const PARAM_INVALID = -995; // 参数无效
    const ACCESS_TOKEN_TIMEOUT = -996;
    const SESSION_TIMEOUT = -997;
    const UNKNOWN = -998;
    const EXCEPTION = -999;

    const SEND_SMS_FREQUENT_ERROR = -1200;//发送短信太频繁

    const GET_QINIU_TOKEN_FAILED=-1200;//获取七牛token失败
    const GET_WX_UNIONID_FAILED=-1300;//获取微信unionid失败
    const ADD_USER_FAILED=-1301;//获取微信unionid失败

    const NOT_OWNER= -1400;//不是当前数据的拥有者
    static public function getConstants() {
        $oClass = new \ReflectionClass(__CLASS__);
        return $oClass->getConstants();
    }

}