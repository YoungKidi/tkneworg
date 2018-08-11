<?php
namespace app\admin\model;
use think\Model;
use think\Db;
/*
 * 公司信息表Model
 *
*/
class Companyskin extends Model
{
    protected  $table = 'companyskin';
    /**
     * @param $arr_where
     * @param $arr_field
     */
    public function getSkinList($arr_where,$arr_field){
        return Db::table($this->table)->field($arr_field)->where($arr_where)->select();
    }

    /**
     * 删除企业皮肤
     * @param $arr_where
     * @return false|int
     */
    public function delCompanySkin($arr_where){
        return Db::table($this->table)->where($arr_where)->delete();
    }

    /**
     * 添加企业皮肤
     */
    public function addCompanySkin($arr_data){
        return Db::table($this->table)->insertAll($arr_data);
    }
}