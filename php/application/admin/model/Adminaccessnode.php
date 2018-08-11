<?php
namespace app\admin\model;
use think\Model;
use think\Db;
class Adminaccessnode extends Model
{
    protected $table = 'adminaccessnode';

    /**
     * 批量添加节点数据
     * @auther 胡博森
     * @param 批量添加 $arr_data 添加的数据
     */
    public function setNodeAddAll($arr_data){
        return Db::table($this->table)->insertAll($arr_data);
    }

    /**
     * 添加单个节点数据
     * @auther 胡博森
     * @param $arr_data
     */
    public function setNodeAddOne($arr_data){
        return Db::table($this->table)->insert($arr_data);
    }

    /**
     * 查询单个节点数据
     * @auther 胡博森
     * @param array $arr_where 查询条件
     * @param array $arr_field 查询字段
     */
    public function getNodeOne($arr_where,$arr_field){
        return Db::table($this->table)->where($arr_where)->field($arr_field)->find();
    }

    /**
     * 删除节点
     * @auther 胡博森
     * @param array $arr_where 删除条件
     */
    public function setNodeDel($arr_where){
        return Db::table($this->table)->where($arr_where)->delete();
    }
    /**
     * 修改节点
     * @auther 胡博森
     * @param array $arr_where 修改条件
     * @param array $arr_data 修改的数据
     */
    public function setNodeUpd($arr_where,$arr_data){
        return Db::table($this->table)->where($arr_where)->data($arr_data)->update();
    }

    /**
     * 查询节点列表
     * @auther 胡博森
     * @param $arr_where
     * @param $arr_field
     * @return false|mixed|\PDOStatement|string|\think\Collection
     */
    public function getNodeList($arr_where,$arr_field){
        return Db::table($this->table)->field($arr_field)->where($arr_where)->order('id desc')->select();
    }

}