<?php
namespace app\admin\model;
use think\Model;
use think\Db;
/*
 * 模板表Model
 *
*/
class Templateinfo extends Model
{
	protected $table = 'templateinfo';

	/**
	 * 查询所有模板
	 * @param  array $arr_where 查询条件
	 * @param  array $arr_field 查询字段
	 * @return  array 模板列表
	 */
	public function getTemplateList($arr_where,$arr_field = ['*']){
		return Db::table($this->table)->field($arr_field)->where($arr_where)->select();
	}
}