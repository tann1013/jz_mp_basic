<?php


namespace app\miniprogram\controller;


use app\constant\RecrodConst;
use app\miniprogram\helper\UserAccountHelper;
use app\miniprogram\model\JzCategory;
use app\miniprogram\model\JzRecord;
use app\model\AdminUser;
use app\util\ReturnCode;
use think\Db;
use think\Exception;
use think\exception\DbException;
use think\facade\Config;

class Record extends Base
{
    //记账
    public function add()
    {
        $uid = $this->userInfo['id'];
        $params = $this->request->param();
        $params['uid'] = $uid;
        $params['account_book_id'] = $params['id'];
        unset($params['id']);
        Db::startTrans();
        try {
            $res = JzRecord::create($params);
            if (!isset($res) && !$res) {
                return $this->buildFailed(ReturnCode::ADD_FAILED, '添加失败');
            }
            $done = UserAccountHelper::update($uid, $params['account_type'], $params['type'], $params['money']);
            if ($done && isset($res) && $res) {
                Db::commit();
                return $this->buildSuccess(null);
            }
            Db::rollback();
        } catch (Exception $e) {
            Db::rollback();
        }
        return $this->buildFailed(ReturnCode::ADD_FAILED, '添加失败');
    }

    //编辑
    public function edit()
    {
        $uid = $this->userInfo['id'];
        $params = $this->request->param();
        $params['uid'] = $uid;
        $params['account_book_id'] = $params['id'];
        unset($params['id']);
        $params['id'] = $params['record_id'];
        unset($params['record_id']);

        Db::startTrans();
        try {
            $model = JzRecord::get($params['id']);
            $data = $model->getData();
            $res = $model->save($params);
            if (!isset($res) && !$res) {
                return $this->buildFailed(ReturnCode::EDIT_FAILED, '修改失败');
            }
            //原先的
            $orgMoney = $data['money'];
            $orgAccountType = $data['account_type'];
            $orgType = $data['type'];
            $doneOrg = UserAccountHelper::reStore($uid, $orgAccountType, $orgType, $orgMoney);
            //现在的
            $money = $params['money'];
            $accountType = $params['account_type'];
            $type = $params['type'];
            $done = UserAccountHelper::update($uid, $accountType, $type, $money);
            if ($done && $doneOrg) {
                Db::commit();
                return $this->buildSuccess(null);
            }
            Db::rollback();
        } catch (Exception $e) {
            Db::rollback();
        }
        if (isset($res)) {
            return $this->buildSuccess(null);
        }
        return $this->buildFailed(ReturnCode::EDIT_FAILED, '修改失败');
    }

    //删除
    public function del()
    {

        $id = $this->request->param('record_id');
        Db::startTrans();
        try {
            $model = JzRecord::get($id);
            $data = $model->getData();
            $num = $data['money'];
            $accountType = $data['account_type'];
            $type = $data['type'];
            $uid = $this->userInfo['id'];
            $res = $model->delete();
            if (!isset($res) && !$res) {
                return $this->buildFailed(ReturnCode::DELETE_FAILED, '修改失败');
            }
            $done = UserAccountHelper::reStore($uid, $accountType, $type, $num);
            if ($done && isset($res) && $res) {
                Db::commit();
                return $this->buildSuccess(null);
            }
            Db::rollback();
        } catch (Exception $e) {
            Db::rollback();
        }

        return $this->buildFailed(ReturnCode::DELETE_FAILED, '删除失败');
    }

    public function getById()
    {
        $id = $this->request->param("id");
        $result = JzRecord::get($id,null,true);

        if (isset($result)) {
            $category = JzCategory::get($result['category_id'], null, true)->getData();
            $result['category'] = ['name' => $category['name'], 'icon' => $category['icon']];
            $userInfo = AdminUser::get($result['uid'], null, true)->getData();
            $result['nickname'] = $userInfo['nickname'];
            return $this->buildSuccess($result);
        }
        return $this->buildFailed(ReturnCode::INVALID, '获取失败');
    }

    //获取账单记录列表
    public function getList()
    {
        $model = (new JzRecord());
        $accountBookId = $this->request->param("id");
        $limit = $this->request->param('size', config('apiAdmin.ADMIN_LIST_DEFAULT'));
        $start = $this->request->param('page', 1);
        try {
            $listResult = $model->where('account_book_id', $accountBookId)->field('id,money,type,time,category_id,remark,create_time')
                ->order('time', 'DESC')
                ->paginate($limit, false, ['page' => $start])->toArray();
            $times = [];
            foreach ($listResult['data'] as $item) {
                if (count($times) > 0) {
                    if ($item['time'] != $times[count($times) - 1]) {
                        array_push($times, $item['time']);
                    }
                } else {
                    array_push($times, $item['time']);
                }
            }
            $days = implode(",", $times);
            $days = '\'' . str_replace(',', '\',\'', $days) . '\'';

            $tableName = Config::get('database.prefix') . 'jz_record';
            $sqlStr = "select SUM(IF(type=" . RecrodConst::TYPEZ_ZC . ",money,0)) as zc,SUM(IF(type=" . RecrodConst::TYPEZ_SR . ",money,0)) as sr,time from $tableName where account_book_id = $accountBookId AND time IN ($days) GROUP BY time";
            $countResult = Db::query($sqlStr);
            $listResult['days'] = $countResult;
            return $this->buildSuccess($listResult);
        } catch (DbException $e) {
        }
        return $this->buildFailed(ReturnCode::INVALID, '');
    }

    //获取指定月的统计
    public function getTotalWithMonth()
    {
        $date = $this->request->param("date");
        $id = $this->request->param("id");
        if (isset($date)) {
            $dateStr = (str_replace('-', '/', $date)) . '/01';
            $firstday = date('Y-m-01', strtotime($dateStr));
            $lastday = date('Y-m-d', strtotime("$firstday +1 month -1 day"));
            $tableName = Config::get('database.prefix') . 'jz_record';
            $sqlStr = "select SUM(IF(type=" . RecrodConst::TYPEZ_ZC . ",money,0)) as zc,SUM(IF(type=" . RecrodConst::TYPEZ_SR . ",money,0)) as sr FROM $tableName WHERE account_book_id = $id AND time BETWEEN '$firstday' AND '$lastday'";
            $countResult = Db::query($sqlStr);
            return $this->buildSuccess($countResult);
        }
        return $this->buildFailed(ReturnCode::INVALID, '缺少参数');

    }


}