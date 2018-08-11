<?php
namespace app\admin\model;
use think\Model;
use think\Db;
/*
 * 公司状态日志表Model
 *
*/
class Companystatelog extends Model
{
    protected $table = 'companystatelog';

    /**
     * 添加日志
     * @param $arr_data
     * @return int|string
     */
    public function setState($arr_data){
        return Db::table($this->table)->insert($arr_data);
    }

    /**
     * 查询日志信息
     * @param $arr_where
     * @param $arr_data
     * @return array|false|mixed|\PDOStatement|string|Model
     */
    public function getSate($arr_where,$arr_field){
        return Db::table($this->table)->field($arr_field)->where($arr_where)->limit(1)->order('ctime desc')->find();
    }
}