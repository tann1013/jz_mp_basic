<?php


namespace app\miniprogram\controller;


use app\constant\AccountBookType;
use app\constant\UserTypeConst;
use app\helper\CommonHelper;
use app\miniprogram\model\JzAccountBook;
use app\miniprogram\model\JzUserAccountBook;
use app\model\AdminUser;
use app\util\ReturnCode;
use think\Db;
use think\db\exception\DataNotFoundException;
use think\db\exception\ModelNotFoundException;
use think\db\Where;
use think\Exception;
use think\exception\DbException;
use think\facade\Cache;

class AccountBooks extends Base
{

    /**
     * 创建账单
     */
    public function addNormal()
    {
        $post = $this->request->param();
        $name = $post['name'];
        if (CommonHelper::checkStrIsInvalid($name)) {
            return $this->buildFailed(ReturnCode::ADD_FAILED, '缺少账单名称');
        }
        $entity = [];
        $entity['name'] = $name;
        $entity['uid'] = $this->userInfo['id'];
        $entity['type'] = AccountBookType::NORMAL_ACCOUNT_BOOK;
        Db::startTrans();
        try {
            $res = JzAccountBook::create($entity);
            if ($res) {
                $done = JzUserAccountBook::create([
                    'uid' => $this->userInfo['id'],
                    'abid' => $res->id,
                    'is_owner' => UserTypeConst::AB_OWNER_USER
                ]);
                if ($done) {
                    Db::commit();
                    return $this->buildSuccess($res->id);
                }
            } else {
                return $this->buildFailed(ReturnCode::ADD_FAILED, '创建失败');
            }
        } catch (Exception $e) {
            Db::rollback();
            return $this->buildFailed(ReturnCode::ADD_FAILED, '创建失败');
        }
        return $this->buildFailed(ReturnCode::ADD_FAILED, '创建失败');
    }

    /**
     * 获取单个账单信息
     */
    public function getById()
    {
        $id = $this->request->param('id');
        $abInfo = JzAccountBook::get($id, true);
        $count = JzUserAccountBook::where('abid', '=', $id)->count();
        $abInfo['count'] = $count;
        if ($abInfo) {
            return $this->buildSuccess($abInfo);
        } else {
            return $this->buildFailed(ReturnCode::INVALID, '获取失败');
        }
    }

    public function getList()
    {
        $uid = $this->userInfo['id'];
        try {
            $abids = (new JzUserAccountBook())->where('uid', $uid)->field('abid')->select()->toArray();
            $abs = (new JzAccountBook())->whereIn('id', array_column($abids, 'abid'))->order('update_time DESC')->select()->toArray();
            return $this->buildSuccess($abs);
        } catch (DbException $e) {
            return $this->buildFailed(ReturnCode::INVALID, '查询数据失败');
        }
    }

    /**
     * 获取当前账单的用户列表
     */
    public function getUsers()
    {
        $abid = $this->request->param('id');
        try {
            $uids = (new JzUserAccountBook)->where(['abid' => $abid])->select()->toArray();
            $result = null;

            if ($uids) {
                $result['data'] = (new AdminUser())->whereIn('id', array_column($uids, 'uid'))->select()->toArray();
                $result['user_roles'] = $uids;
            }
            return $this->buildSuccess($result);
        } catch (DbException $e) {
        }
        return $this->buildFailed(ReturnCode::INVALID, '获取失败');
    }

    /**
     * 生成一个分享的临时key
     * @return array
     */
    public function getShareKey()
    {
        $abid = $this->request->param('id');
        $key = md5(uniqid() . $abid . time());
        Cache::set(config('apiadmin.WX_AB_SHARE_PRE') . $key, $abid, config('apiadmin.WX_AB_SHARE_TIME_OUT'));
        return $this->buildSuccess($key);
    }

    /**
     * 将用户添加到账单
     */
    public function addUserToAb()
    {
        $key = $this->request->param('key');
        $abid = Cache::get(config('apiadmin.WX_AB_SHARE_PRE') . $key, '');
        if ($abid !== '') {
            $has = JzUserAccountBook::get([
                'uid' => $this->userInfo['id'],
                'abid' => $abid
            ]);
            if (isset($has)) {
                return $this->buildFailed(ReturnCode::INVALID, '您已经加入此账单了');
            }
            $res = JzUserAccountBook::create([
                'uid' => $this->userInfo['id'],
                'abid' => $abid,
                'is_owner' => UserTypeConst::AB_NORMAL_USER
            ]);
            if (isset($res)) {
                return $this->buildSuccess($abid);
            }
        }
        return $this->buildFailed(ReturnCode::INVALID, '添加失败', null);
    }

    /**
     * 将用户移除账单
     */
    public function removeUserByUid()
    {
        $uid = $this->request->param('uid');
        $abid = $this->request->param('id');
        if($uid == $this->userInfo['id']){
            return $this->buildFailed(ReturnCode::DELETE_FAILED,'不能将自己移除');
        }
        try {
            $result = (new JzUserAccountBook())->where(['uid' => $uid, 'abid' => $abid])->delete();
            if ($result > 0 && isset($result)) {
                return $this->buildSuccess(null);
            }
        } catch (\Exception $e) {
        }
        return $this->buildFailed(ReturnCode::DELETE_FAILED, '操作失败');
    }


    /**
     * 用户主动退出
     */
    public function out()
    {
        $uid = $this->userInfo['id'];
        $abid = $this->request->param('id');
        try {
            $model = JzUserAccountBook::get(['uid' => $uid, 'abid' => $abid]);
            $data = $model->getData();
            if ($data['is_owner'] == UserTypeConst::AB_OWNER_USER) {
                return $this->buildFailed(ReturnCode::DELETE_FAILED, '管理员不可退出');
            }
            $done = $model->delete();
            if ($done) {
                return $this->buildSuccess(null);
            }
        } catch (\Exception $e) {
        }
        return $this->buildFailed(ReturnCode::DELETE_FAILED, '操作失败');
    }

    /**
     * 移交管理员
     */
    public function changeAdmin()
    {
        $myuid = $this->userInfo['id'];
        $abid = $this->request->param('id');
        $uid = $this->request->param('uid');
        Db::startTrans();
        try {
            $data['uid'] = $myuid;  //更新条件自动识别
            $data['abid'] = $abid;
            $model = JzUserAccountBook::get($data);
            $model->setAttr('is_owner', UserTypeConst::AB_NORMAL_USER);
            $affected = $model->allowField(true)->save();
            if (empty($affected)) {
                return $this->buildFailed(ReturnCode::EDIT_FAILED, "操作失败");
            }
            $cdata['uid'] = $uid;  //更新条件自动识别
            $cdata['abid'] = $abid;
            $model = JzUserAccountBook::get($cdata);
            $model->setAttr('is_owner', UserTypeConst::AB_OWNER_USER);
            $affected = $model->allowField(true)->save();
            if (empty($affected)) {
                return $this->buildFailed(ReturnCode::EDIT_FAILED, "操作失败");
            }
            Db::commit();
            return $this->buildSuccess(null);
        } catch (Exception $e) {
        }
        Db::rollback();
        return $this->buildFailed(ReturnCode::EDIT_FAILED, "操作失败");
    }
}