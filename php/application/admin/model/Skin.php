<?php
namespace app\admin\model;
use think\Model;
use think\Db;
/*
 * 模板表Model
 *
*/
class Skin extends Model
{
    protected $table = 'skin';

    /**
     * 获取皮肤列表
     * @param  [array] $arr_where [搜索条件]
     * @param  [array] $arr_field [查询字段]
     * @return [array]            []
     */
    public function getSkinList($arr_where,$arr_field=['*']){
        return Db::table($this->table)->field($arr_field)->where($arr_where)->select();
    }
    /**
     * 获取皮肤列表
     * @author 胡博森
     * @param  [array] $arr_where [description]
     * @param  [array] $arr_field [description]
     * @return [type]            [description]
     */
    public function getSkinLists($arr_where,$arr_field=['*']){
        return Db::table($this->table)->alias('s')
            ->field($arr_field)
            ->join('template t','t.id = s.tplId')
            ->where($arr_where)->select();
    }
    /**
     * 修改皮肤信息
     * @param  [array] $data [修改数据]
     * @return [int]            []
     */
    public function setSkinUpdate($data){
        return Db::table($this->table)->update($data);
    }

    /**
     * 添加皮肤信息
     * @Author Wangchen
     * @DateTime 2018-08-02
     * @param  [array] $data [添加数据]
     * @return [int]            []
     */
    public function setSkinAdd($data){
        $result = Db::table($this->table)->insert($data);
        return $result;
    }

    /**
     * 删除皮肤
     * @Author Wangchen
     * @DateTime 2018-08-02
     * @param  [array] $arr_where [搜索条件]
     */
    public function setSkinDel($arr_where){
        $result = Db::table($this->table)->where($arr_where)->delete();
        return $result;
    }
}