<?php
/**
*课件模型
**/
namespace app\admin\model;
use think\Model;
use think\Db;
class Fileinfo extends Model
{
	protected $table = 'fileinfo';
	/**
	 * [getFileinfoList  获取课件列表]
	 * @author zzq
	 * @DateTime 2018-07-11
	 * @param    [int]                   $offset    [分页起始位置]
	 * @param    [int]                   $pagesize  [每页条数]
	 * @return   [array]                            [查询结果]
	 */
	public function getFileinfoList($where,$pagenum,$pagesize){
		$field = 'b.companyfullname,a.fileid,a.companyid,a.filename,a.newfilename,a.size,a.filetype,a.status,a.uploadtime,a.isconvert' ;
		$data = Db::table($this->table)
		        ->alias('a')
		        ->join('company b','a.companyid= b.companyid','LEFT')
				->field($field)
				->where($where)
				->page($pagenum,$pagesize)
				->select();
		//var_dump($this->getLastSql());
	    return $data;
	}
	/**
	 * [getFileListCount  获取教室列表记录总数]
	 * @Author zzq
	 * @DateTime 2018-07-11
	 * @param    无
	 * @return   [int]                 返回符合的记录数目
	 */
	public function getFileinfoListCount($where){
		$field = 'b.companyfullname,a.fileid,a.companyid,a.filename,a.newfilename,a.size,a.filetype,a.status,a.uploadtime,a.isconvert' ;
		$count = Db::table($this->table)
		        ->alias('a')
		        ->join('company b','a.companyid= b.companyid','LEFT')
				->field($field)
				->where($where)
				->count();
	    return $count;
	}

}	