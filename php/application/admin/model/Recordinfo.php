<?php
/**
*录制件模型
**/
namespace app\admin\model;
use think\Model;
use think\Db;
class Recordinfo extends Model
{
	protected $table = 'recordinfo';
	/**
	 * [getRecordinfoList  获取录制件列表]
	 * @author zzq
	 * @DateTime 2018-07-13
	 * @param    [int]                   $offset    [分页起始位置]
	 * @param    [int]                   $pagesize  [每页条数]
	 * @return   [array]                            [查询结果]
	 */
	public function getRecordinfoList($where,$pagenum,$pagesize){
		$field = 'b.companyfullname,c.roomname,a.recordid,a.serial,a.companyid,a.recordtitle,a.starttime,a.duration,a.state,a.size,a.recordname,a.recordfileurl,a.recordtype' ;
		$data = Db::table($this->table)
		        ->alias('a')
		        ->join('company b','a.companyid= b.companyid','LEFT')
		        ->join('room c','a.serial= c.serial','LEFT')
				->field($field)
				->where($where)
				->page($pagenum,$pagesize)
				->select();
		//var_dump($this->getLastSql());
	    return $data;
	}
	/**
	 * [getRecordinfoListCount  获取录制件列表记录总数]
	 * @Author zzq
	 * @DateTime 2018-07-13
	 * @param    无
	 * @return   [int]                 返回符合的记录数目
	 */
	public function getRecordinfoListCount($where){
		$field = 'b.companyfullname,c.roomname,a.recordid,a.serial,a.companyid,a.recordtitle,a.recordpath,a.starttime,a.duration,a.state,a.size,a.recordname,a.recordfileurl,a.recordtype' ;
		$count = Db::table($this->table)
		        ->alias('a')
		        ->join('company b','a.companyid= b.companyid','LEFT')
		        ->join('room c','a.serial= c.serial','LEFT')
				->field($field)
				->where($where)
				->count();
	    return $count;
	}	
}