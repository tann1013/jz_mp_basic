<?php

namespace app\http\middleware;

use app\constant\UserTypeConst;
use app\miniprogram\model\JzUserAccountBook;
use app\util\ReturnCode;

class MiniProgramCheckAbAdmin {

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
            $id = $request->param('id');
            $done = JzUserAccountBook::get(['uid'=>$uid,'abid'=>$id,'is_owner'=>UserTypeConst::AB_OWNER_USER],null,true);
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
