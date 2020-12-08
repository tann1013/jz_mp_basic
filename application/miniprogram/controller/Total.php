<?php
/**
 * Created by PhpStorm.
 * User: chniccs
 * Date: 2019-08-03
 * Time: 8:37
 */

namespace app\miniprogram\controller;


use app\constant\RecrodConst;
use think\Db;
use think\facade\Config;

class Total extends Base
{
    public function getTotlaByYear()
    {
        $year = $this->request->param('year');
        $abid = $this->request->param('id');
        $tableName = Config::get('database.prefix') . 'jz_record';
        $typeZc = RecrodConst::TYPEZ_ZC;
        $typeSr = RecrodConst::TYPEZ_SR;
        $sqlStr = "SELECT sum(sr_count) as sr_count,SUM(zc_count) as zc_count,months FROM(".
            "select DATE_FORMAT(time,'%Y-%m') months,0 as sr_count,sum(money) as zc_count,type from $tableName WHERE account_book_id = ?  AND type=$typeZc AND YEAR (time) = ? GROUP BY months UNION ".//查询支出
            "select DATE_FORMAT(time,'%Y-%m') months,sum(money) as sr_count,0 as zc_count,type from $tableName WHERE account_book_id = ?  AND type=$typeSr AND YEAR (time) = ? GROUP BY months)".//查询支出
            " count GROUP BY months ORDER BY months ASC";
        $result = Db::query($sqlStr,[$abid,$year,$abid,$year]);
        return $this->buildSuccess($result);
    }
    public function getTotlaByMonth()
    {
        $month = $this->request->param('month');
        $abid = $this->request->param('id');
        $tableName = Config::get('database.prefix') . 'jz_record';
        $tableCategory = Config::get('database.prefix') . 'jz_category';
        $typeZc = RecrodConst::TYPEZ_ZC;
        $typeSr = RecrodConst::TYPEZ_SR;
        $sqlStr = "SELECT count,name,account_type,c.type FROM( "."SELECT SUM(money) count,account_type,type FROM $tableName WHERE account_book_id = ? AND type = $typeZc AND MONTH(time) = ? GROUP BY account_type ".
            "UNION ".
            "SELECT SUM(money) count,account_type,type FROM $tableName WHERE account_book_id = ? AND type = $typeSr AND MONTH(time) = ? GROUP BY account_type)c JOIN $tableCategory  ON c.account_type = $tableCategory.id ";
        $result = Db::query($sqlStr,[$abid,$month,$abid,$month]);
        return $this->buildSuccess($result);


    }
}