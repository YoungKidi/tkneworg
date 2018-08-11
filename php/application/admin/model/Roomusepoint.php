<?php
/**
*
*房间room实时并发在线情况模型(该表为数据为动态数据)
**/
namespace app\admin\model;
use think\Model;
use think\Db;
class Roomusepoint extends Model
{
	protected $table = 'roomusepoint';

	/**
	 * [getOnlineCompanyList  实时在线->企业并发->获取在线的机构的列表]
	 * @author zzq
	 * @DateTime 2018-07-04
	 * @param    [array]                 $where     [筛选条件]
	 * @param    [int]                   $pagenum   [页码数]
	 * @param    [int]                   $pagesize  [每页条数]
	 * @return   [array]                            [查询结果]
	 */
	public function getOnlineCompanyList($where,$pagenum,$pagesize){
		$field = 'b.companyfullname,b.seconddomain,a.companyid,count(distinct(a.serial)) as roomnum,count(a.buddyid) as usernum' ;
		$res = Db::table($this->table)
		       ->alias('a')
		       ->field($field)
		       ->where($where)
		       ->join('company b','a.companyid=b.companyid','LEFT')
		       ->group('a.companyid')
		       ->page($pagenum,$pagesize)
		       ->select(); 
		//var_dump($this->getLastSql());
		//var_dump($res);
		return $res;
	}

	/**
	 * [getOnlineCompanyListCount  实时在线->企业并发->获取在线的机构的列表的总数]
	 * @author zzq
	 * @DateTime 2018-07-04
	 * @param    [array]                 $where     [筛选条件]
	 * @return   [int]                            [查询结果]
	 */
	public function getOnlineCompanyListCount($where){
		$field = 'b.companyfullname,b.seconddomain,a.companyid,count(distinct(a.serial)) as roomnum,count(a.buddyid) as usernum' ;
		$res = Db::table($this->table)
		       ->alias('a')
		       ->field($field)
		       ->where($where)
		       ->join('company b','a.companyid=b.companyid','LEFT')
		       ->group('a.companyid')
		       ->count(); 
		//var_dump($this->getLastSql());
		// var_dump($res);
		return $res;
	}


	/**
	 * [getOnlineComTotalDataByCondition  实时在线->企业并发->获取在线的机构的列表->根据筛选条件获取总计的课堂数和在线人数]
	 * @author zzq
	 * @DateTime 2018-07-05
	 * @param    [array]                 $where     [筛选条件]
	 * @return   [array]                            [查询结果]
	 */	
	public function getOnlineComTotalDataByCondition($where){
		$field = 'count(distinct(a.serial)) as totalroomnum,count(a.buddyid) as totalusernum';
		$res = Db::table($this->table)
		       ->alias('a')
		       ->field($field)
		       ->where($where)
		       ->join('company b','a.companyid=b.companyid','LEFT')
		       ->select();
		//var_dump($this->getLastSql());
		// var_dump($res);
		// die;  
		return $res;
	}

	/**
	 * //实时在线->企业并发->获取某个机构的在线教室的列表
	 * @author zzq
	 * @DateTime 2018-07-05
	 * @param    [array]                 $where     [筛选条件]
	 * @param    [int]                   $pagenum   [页码数]
	 * @param    [int]                   $pagesize  [每页条数]
	 * @return   [array]                            [查询结果]
	 */
	public function getOnlineRoomListByCom($where,$pagenum,$pagesize){
		$field = 'b.companyfullname,c.roomname,a.companyid,a.roomtype,a.serial as roomnum,count(a.buddyid) as usernum' ;
		$res = Db::table($this->table)
		       ->alias('a')
		       ->field($field)
		       ->where($where)
		       ->join('company b','a.companyid=b.companyid','LEFT')
		       ->join('room c','a.serial=c.serial','LEFT')
		       ->group('a.serial')
		       ->page($pagenum,$pagesize)
		       ->select(); 
		// var_dump($this->getLastSql());
		// var_dump($res);
		// die;
		return $res;		
	}
	/**
	 * //实时在线->企业并发->获取某个机构的在线教室的列表的数目
	 * @author zzq
	 * @DateTime 2018-07-05
	 * @param    [array]                 $where     [筛选条件]
	 * @return   [int]                            [查询结果]
	 */
    public function getOnlineRoomListByComCount($where){
		$field = 'b.companyfullname,c.roomname,a.companyid,a.roomtype,a.serial as roomnum,count(a.buddyid) as usernum' ;
		$res = Db::table($this->table)
		       ->alias('a')
		       ->field($field)
		       ->where($where)
		       ->join('company b','a.companyid=b.companyid','LEFT')
		       ->join('room c','a.serial=c.serial','LEFT')
		       ->group('a.serial')
		       ->count(); 
		// var_dump($this->getLastSql());
		// var_dump($res);
		// die;
		return $res;	    	
    }

	/**
	 * //在线教室列表
	 * @Author zzq
	 * @param    [array]                 $where     [筛选条件]
	 * @param    [int]                   $pagenum   [页码数]
	 * @param    [int]                   $pagesize  [每页条数]      
	 * @return  array  
	 * 包含:机构名  教室名称 教师编号 教室类型 在线人数          
	 */
    public function getOnlineRoomList($where,$pagenum,$pagesize){
		$field = 'b.companyfullname,c.roomname,a.companyid,a.roomtype,a.serial as roomnum,count(a.buddyid) as usernum' ;
		$res = Db::table($this->table)
		       ->alias('a')
		       ->field($field)
		       ->where($where)
		       ->join('company b','a.companyid=b.companyid','LEFT')
		       ->join('room c','a.serial=c.serial','LEFT')
		       ->group('a.serial')
		       ->page($pagenum,$pagesize)
		       ->select(); 
		// var_dump($this->getLastSql());
		// var_dump($res);
		// die;
		return $res;
    }

	/**
	 * //在线教室列表的数目
	 * @author zzq
	 * @DateTime 2018-07-05
	 * @param    [array]                 $where     [筛选条件]
	 * @return   [int]                            [查询结果]
	 */
    public function getOnlineRoomListCount($where){
		$field = 'b.companyfullname,c.roomname,a.companyid,a.roomtype,a.serial as roomnum,count(a.buddyid) as usernum' ;
		$res = Db::table($this->table)
		       ->alias('a')
		       ->field($field)
		       ->where($where)
		       ->join('company b','a.companyid=b.companyid','LEFT')
		       ->join('room c','a.serial=c.serial','LEFT')
		       ->group('a.serial')
		       ->count(); 
		// var_dump($this->getLastSql());
		// var_dump($res);
		// die;
		return $res;	    	
    }

	/**
	 * //根据教室号判断该教室是否是在线教室
	 * @author zzq
	 * @DateTime 2018-07-10
	 * @param    [array]                           包括$companyid $serial     [教室编号]
	 * @return   [bool]                            [查询结果]
	 */
    public function hasOnlineRoomBySerial($where){
		$field = 'buddyid,identification,companyid,serial,roomtype,usertype' ;
		$res = Db::table($this->table)
		       ->field($field)
		       ->where($where)
		       ->find();
		if($res){
			return $res;
		}else{
			return false;
		}     	
    }

	/**
	 * //获取在线教室的老师姓名
	 * @author zzq
	 * @DateTime 2018-07-10
	 * @param    [string]                 $serial     [教室编号]
	 * @return   [array]                            [查询结果]
	 */
    public function getOnlineRoomTeacherNameBySerial($serial){
		$field = 'buddyname' ;
		$res = Db::table($this->table)
		       ->field($field)
		       ->where('usertype','EQ','0')
		       ->where('serial','EQ',$serial)
		       ->find();
		return $res;  	
    }

	/**
	 * //获取在线教室的学生数量
	 * @author zzq
	 * @DateTime 2018-07-10
	 * @param    [array]                 $comanyid serial usertype     []
	 * @return   [bool]                            [查询结果]
	 */
    public function getOnlineRoomStudentCountBySerial($where){
		$count = Db::table($this->table)
		       ->where($where)
		       ->count();
		return $count; 
    }


	/**
	 * //获取在线教室的人员的详细信息
	 * @author zzq
	 * @DateTime 2018-07-10
	 * @param    [array]               $where     [查询条件]
	 * @return   [array]                            [查询结果]
	 */
    public function getOnlineUserDetail($where){
    	$field = "buddyid,identification,serial,companyid,buddyname,usertype,roomtype,logintime";
		$res = Db::table($this->table)
		       ->field($field)
		       ->where($where)
		       ->find();
		return $res; 
    }

    
	/**
	 * //查询在线的某用户的所有的下行用户
	 * @author zzq
	 * @DateTime 2018-08-08
	 * @param    [array]               $where     [查询条件]
	 * @return   [array]                            [查询结果]
	 */
    public function getOnlineOtherUserByUserid($where){
    	$field = "a.buddyid,a.identification,a.serial,a.companyid,a.buddyname,a.usertype,a.roomtype,b.userid,b.identification,b.username,b.entertime,b.outtime";
		$res = Db::table($this->table)
		       ->alias('a')
		       ->field($field)
		       ->where($where)
		       ->where('b.entertime','not null')
		       ->where('b.outtime','null')
		       ->join('logininfo b','a.buddyid=b.userid and a.identification = b.identification and a.companyid = b.companyid and a.serial = b.serial','left')
		       ->order('a.usertype asc')
		       ->select();
		return $res;     	
    }

	/**
	 * //查看这个在线用户是否存在
	 * @author zzq
	 * @DateTime 2018-08-08
	 * @param    [array]               $where     [查询条件]
	 * @return   [array]                            [查询结果]
	 */
    public function getOnlineUser($where){

    	$field = "a.buddyid,a.identification,a.serial,a.companyid,a.buddyname,a.usertype,a.roomtype,b.userid,b.identification,b.username,b.entertime,b.outtime";
		$res = Db::table($this->table)
		       ->alias('a')
		       ->field($field)
		       ->where($where)
		       ->where('b.entertime','not null')
		       ->where('b.outtime','null')
		       ->join('logininfo b','a.buddyid=b.userid and a.identification = b.identification and a.companyid = b.companyid and a.serial = b.serial','left')
		       ->order('a.usertype asc')
		       ->find();
		//var_dump($this->getLastSql());
		// die;
		return $res;     	
    } 

	/**
	 * //查看这个在线用户列表
	 * @author zzq
	 * @DateTime 2018-08-08
	 * @param    [array]                 $where     [查询条件]
	 * @param    [int]                   $pagenum   [页码数]
	 * @param    [int]                   $pagesize  [每页条数]   
	 * @return   [array]                            [查询结果]
	 */
    public function getOnlineUserList($where,$pagenum,$pagesize,$companyfullname,$roomname){
    	//var_dump($where);
    	//die;
    	if(!empty($companyfullname)){
    		$where['c.companyfullname'] = ['EQ','%'.$companyfullname.'%'];    		
    	}
    	if(!empty($roomname)){
    		$where['d.roomname'] = ['EQ','%'.$roomname.'%'];    		
    	}
    	$field = "a.buddyid,a.identification,a.serial,a.companyid,a.usertype,a.roomtype,b.userid,b.username,b.entertime,b.outtime,c.companyfullname,d.roomname";
		$res = Db::table($this->table)
		       ->alias('a')
		       ->field($field)
		       ->join('logininfo b','a.buddyid=b.userid and a.identification = b.identification and a.companyid = b.companyid and a.serial = b.serial','left')
		       ->join('company c','a.companyid=c.companyid','LEFT')
		       ->join('room d','a.serial=d.serial','LEFT')
		       ->where($where)
		       ->where('b.entertime','not null')
		       ->where('b.outtime','null')
		       ->order('a.usertype asc')
		       ->page($pagenum,$pagesize)
		       ->select();
		//var_dump($this->getLastSql());
		// die;
		return $res;     	
    } 

	/**
	 * //查看这个在线用户列表的数目
	 * @author zzq
	 * @DateTime 2018-08-08
	 * @param    [array]               $where     [查询条件]
	 * @return   [array]                            [查询结果]
	 */
    public function getOnlineUserListCount($where,$companyfullname,$roomname){
    	if(!empty($companyfullname)){
    		$where['c.companyfullname'] = ['EQ','%'.$companyfullname.'%'];    		
    	}
    	if(!empty($roomname)){
    		$where['d.roomname'] = ['EQ','%'.$roomname.'%'];    		
    	}
    	$field = "a.buddyid,a.identification,a.serial,a.companyid,a.usertype,a.roomtype,b.userid,b.username,b.entertime,b.outtime";
		$res = Db::table($this->table)
		       ->alias('a')
		       ->field($field)
		       ->where($where)
		       ->where('b.entertime','not null')
		       ->where('b.outtime','null')
		       ->join('logininfo b','a.buddyid=b.userid and a.identification = b.identification and a.companyid = b.companyid and a.serial = b.serial','left')
		       ->join('company c','a.companyid=c.companyid','LEFT')
		       ->join('room d','a.serial=d.serial','LEFT')
		       ->order('a.usertype asc')
		       ->count();
		//var_dump($this->getLastSql());
		// die;
		return $res;     	
    }   
}	