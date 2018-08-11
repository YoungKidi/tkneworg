<?php
namespace app\admin\model;
use think\Model;
use think\Db;
class Room extends Model
{
	protected $table = 'room';
	/**
	 * [getRoomList  获取教室列表]
	 * @author wyx
	 * @DateTime 2018-06-27
	 * @param    [int]                   $offset    [分页起始位置]
	 * @param    [int]                   $pagesize  [每页条数]
	 * @return   [array]                            [查询结果]
	 */
	public function getRoomList($offset,$pagesize){
		$field = 'serial,roomname' ;
		return Db::table($this->table)->field($field)->limit($offset.','.$pagesize)->select();
	}
	/**
	 * [getRoomListCount  获取教室列表记录总数]
	 * @Author wyx
	 * @DateTime 2018-06-27
	 * @param    无
	 * @return   [int]                 返回符合的记录数目
	 */
	public function getRoomListCount(){
		$field = 'serial' ;
		return Db::table($this->table)->field($field)->count();
	}

	/**
	 * [getRoomDetail  获取教室详情]
	 * @Author  zzq
	 * @DateTime 2018-07-10
	 * @param    [int]roomid                教室id
	 * @return   [array]                 返回数据
	 */
	public function getRoomDetail($roomid){
		$field = 'serial,roomname,roomtype,starttime,endtime' ;
		$data = Db::table($this->table)->field($field)->where('serial','EQ',$roomid)->find();
		return $data;
	}

    
	/**
	 * [where条件查询教室列表]
	 * @author zzq
	 * @DateTime 2018-07-30
	 * @param    [array]                 $data     [查询条件]
	 * @param    [int]                   $pagenum   [页码数]
	 * @param    [int]                   $pagesize  [每页条数]
	 * @return   [array]                            [查询结果]
	 */
	public function getConditionRoomList($data,$pagenum,$pagesize){
		$where = [];
		$where['a.serial'] = [ 'IN',$data['serialArr'] ];
		$field = 'a.serial,a.roomname,a.roomtype,a.starttime,a.endtime,a.companyid,b.companyfullname' ;
		$res = Db::table($this->table)
		->alias('a')
		->field($field)
		->where($where)
		->join('company b','a.companyid=b.companyid','LEFT')
		->page($pagenum,$pagesize)
		->select();
		// var_dump($this->getLastSql());
		// die;
		return $res;
	}
	/**
	 * [where条件查询教室列表的数目]
	 * @author zzq
	 * @DateTime 2018-07-30
	 * @param    [array]                 $data     [查询条件]
	 * @return   [array]                            [查询结果]
	 */
	public function getConditionRoomListCount($data){
		$where = [];
		$where['a.serial'] = [ 'IN',$data['serialArr'] ];
		$field = 'a.serial,a.roomname,a.roomtype,a.starttime,a.endtime,b.companyfullname' ;
		$count = Db::table($this->table)
		->alias('a')
		->field($field)
		->where($where)
		->join('company b','a.companyid=b.companyid','LEFT')
		->count();
		return $count;
	}

	/**
	 * [//获取某个教室的详情]
	 * @author zzq
	 * @DateTime 2018-07-30
	 * @param    [array]                 $data     [查询条件]
	 * @return   [array]                            [查询结果]
	 */
	public function getRoomDetailBySerial($where){
		$field = "a.serial,a.roomname,a.roomtype,a.starttime,a.endtime,a.companyid,b.companyfullname";
		$res = Db::table($this->table)
		->alias('a')
		->field($field)
		->where($where)
		->join('company b','a.companyid=b.companyid','LEFT')
		->find();
		return $res;		
	}	
}	