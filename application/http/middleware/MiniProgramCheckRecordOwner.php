<?php

namespace app\http\middleware;

use app\miniprogram\model\JzRecord;
use app\util\ReturnCode;

class MiniProgramCheckRecordOwner {

    /**
     * 用于防止越权访问
     * @param \think\facade\Request $request
     * @param \Closure $next
     * @return mixed|\think\response\Json
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function handle($request, \Closure $next) {
        $header = config('apiadmin.CROSS_DOMAIN');
        $userInfo = $request->API_ADMIN_USER_INFO;
        $uid = $userInfo['id'];
        if ($uid) {
            $id = $request->param('record_id');
            $done = JzRecord::get(['uid'=>$uid,'id'=>$id]);
            if (!$done) {
                return json([
                    'code' => ReturnCode::NOT_OWNER,
                    'msg'  => '越权访问',
                    'data' => []
                ])->header($header);
            }
            return $next($request);
        } else {
            return json([
                'code' => ReturnCode::AUTH_ERROR,
                'msg'  => '缺少用户信息',
                'data' => []
            ])->header($header);
        }
    }
}
