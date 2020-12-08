<?php
/**
 * Created by PhpStorm.
 * User: chniccs
 * Date: 2019-07-22
 * Time: 17:05
 */

namespace app\miniprogram\controller;


use app\constant\CacheTagConst;
use app\constant\UserTypeConst;
use app\miniprogram\model\JzAccountType;
use app\miniprogram\model\JzUserAccountType;
use app\model\AdminAuthGroupAccess;
use app\model\AdminOss;
use app\model\AdminUser;
use app\util\ReturnCode;
use app\util\Tools;
use EasyWeChat\Kernel\Exceptions\DecryptException;
use EasyWeChat\Kernel\Exceptions\InvalidConfigException;
use think\Db;
use think\facade\Cache;
use think\facade\Request;

class Index extends Base
{


    public function wxlogin()
    {
        $code = Request::param('code');
        $encryptedData = Request::param('encryptedData');
        $iv = Request::param('iv');
        try {
            $result = $this->getWx()->auth->session($code);
            //这里是对用户未关注公众号或者没使用过微信登陆的情况下，直接获取不到unionid，要解密数据获取
            if ($iv!=null && $encryptedData!=null && isset($iv) && isset($encryptedData)) {
                try {
                    $data = $this->getWx()->encryptor->decryptData($result['session_key'], $iv, $encryptedData);
                    $result['unionid'] = $data['unionId'];
                } catch (DecryptException $e) {
                    return $this->buildFailed(ReturnCode::GET_WX_UNIONID_FAILED, '获取授权失败');
                }
            }

            $userInfo = AdminUser::get(['unionid' => $result['unionid']]);
            $rep['unionid'] = $result['unionid'];
            $rep['openid'] = $result['openid'];
            //没有查询到用户就创建用户
            if (!$userInfo) {
                $addResult = $this->add($result['openid'], $result['unionid'], $result['session_key']);
                if ($addResult['done']) {
                    $rep['uid'] = $addResult['res']['id'];
                    $userInfo = $addResult['res'];
                } else {
                    return $this->buildFailed(ReturnCode::ADD_USER_FAILED, '获取授权失败');
                }
            } else {
                $rep['uid'] = $userInfo->id;
            }
            $apiAuth = md5(uniqid() . $result['unionid'] . time());
            cache('Login:' . $apiAuth, json_encode($userInfo), config('apiadmin.WX_TOKEN_TIME_OUT'));
            //设置之前先清除掉之前的token
            if (Cache::has('Login:' . $userInfo['id'])) {
                $tokenName = Cache::get('Login:' . $userInfo['id']);
                cache('Login:' . $tokenName, null);
            }
            cache('Login:' . $userInfo['id'], $apiAuth, config('apiadmin.WX_TOKEN_TIME_OUT'));
            $rep['token'] = $apiAuth;
            return $this->buildSuccess($rep);
        } catch (InvalidConfigException $e) {
            return $this->buildFailed(ReturnCode::GET_WX_UNIONID_FAILED, '获取授权失败');
        }
    }

    /**
     * 新增用户
     * @param $openid string openid
     * @param $unionid string unionid
     * @param $sessionKey string 密钥
     * @return array
     */
    private function add($openid, $unionid, $sessionKey)
    {
        // 启动事务
        Db::startTrans();
        try {
            $post = $this->request->param();
            //默认为普通用户
            $groups = UserTypeConst::NORMAL_USER;
            $user = [];
            $user['create_ip'] = request()->ip(1);
            $user['openid'] = $openid;
            $user['unionid'] = $unionid;
            $user['session_key'] = $sessionKey;
            $user['head_img'] = $post['headImg'];
            $user['nickname'] = $post['nickName'];

            $res = AdminUser::create($user);
            if ($res === false) {
                return ['done' => false, 'id' => null];
            } else {
                //插入权限数据
                AdminAuthGroupAccess::create([
                    'uid' => $res->id,
                    'group_id' => $groups
                ]);
                //插入余额数据
                $accountTypes = JzAccountType::all();
                $data = [];
                foreach (array_column(Tools::buildArrFromObj($accountTypes), 'type') as $value) {
                    array_push($data, ['account_type' => $value, 'uid' => $res->id, 'balance' => 0]);
                }
                (new JzUserAccountType())->insertAll($data);
            }
            // 提交事务
            Db::commit();
            return ['done' => true, 'res' => $res];
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            return ['done' => false, 'res' => null];
        }

    }
}