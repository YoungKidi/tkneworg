<?php
namespace app\admin\model;
use think\Model;
use think\Db;
/*
 * 企业模板关联表Model
 *
*/
class Companytemplate extends Model
{
    protected $table = 'companytemplate';

    /**
     * 查询企业的皮肤
     * @param $arr_where
     * @param array $arr_field
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getCompanyTemplate($arr_where,$arr_field = ['*']){
        return Db::table($this->table)->field($arr_field)->where($arr_where)->limit(3)->select();
    }

    /**
     * 删除企业的皮肤
     * @param $arr_where
     * @return false|int
     */
    public function delCompanyTemplate($arr_where){
        return Db::table($this->table)->where($arr_where)->delete();
    }

    /**
     * 添加企业皮肤
     * @param $arr_data
     */
    public function addCompanyTemplate($arr_data){
        return Db::table($this->table)->insertAll($arr_data);
    }
}