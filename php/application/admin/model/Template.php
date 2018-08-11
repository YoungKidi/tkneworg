<?php
namespace app\admin\model;
use think\Model;
use think\Db;
/*
 * 模板表Model
 * 
*/
class Template extends Model{

    protected $table = 'template';

    /**
     * 获取模板列表
     * @param  [array] $arr_where [查询条件]
     * @param  [array] $arr_field [查找字段]
     * @return [array]            
     */
    public function getTemplateList($arr_where,$arr_field=['*']){
    	return Db::table($this->table)->field($arr_field)->where($arr_where)->select();
    }

    /**
     * 修改模板类型
     * @Author Wangchen
     * @DateTime 2018-08-01
     * @param  [array] $data [修改数据]
     * @return [int]            []
     */
    public function setTemplateUpdate($data){
        return Db::table($this->table)->update($data);
    }

    /**
     * 添加模板类型
     * @Author Wangchen
     * @DateTime 2018-08-02
     * @param  [array] $data [添加数据]
     * @return [int]            []
     */
    public function setTemplateAdd($data){
        $result = Db::table($this->table)->insert($data);
        return $result;
    }

    /**
     * 删除模板
     * @Author Wangchen
     * @DateTime 2018-08-02
     * @param  [array] $arr_where [搜索条件]
     */
    public function setTemplateDel($arr_where){
        $result = Db::table($this->table)->where($arr_where)->delete();
        return $result;
    }


}