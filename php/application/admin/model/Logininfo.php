<?php
/**
*
*记录人员进入房间的信息
**/
namespace app\admin\model;
use think\Model;
use think\Db;
use think\Log;
use app\admin\business\MongodbManage;
class Logininfo extends Model
{
	protected $table = 'logininfo';

	/**
	 * [getAnalysisList  统计查询->统计查询历史在线课堂和人员列表]
	 * @author zzq
	 * @DateTime 2018-07-06
	 * @param    [array]                 $where     [筛选条件]
	 * @param    [int]                   $pagenum   [页码数]
	 * @param    [int]                   $pagesize  [每页条数]
	 * @param    [int]                   $ispage    [1表示执行分页,0表示不执行分页]
	 * @return   [array]                            [查询结果]
	 */
	public function getAnalysisListToUser($where,$pagenum,$pagesize,$ispage = 1){ 
		Log::write("----getAnalysisListToUser,之行开始时间：".time()."----");
		/******************************获取统计用户数******************************/
		//第一步,构建子查询,生成sql语句,但是不查询(获取按如期，公司id,房间类型的用户数)
		$fieldone = "DATE(a.entertime) as historydate,a.companyid as companyid,roomtype,count((a.userid)) as userstr" ;
		$subQueryOne = Db::table($this->table)
		    	->alias('a')
		       	->field($fieldone)
		       	->where($where)
		       	//->join('company b','a.companyid=b.companyid','LEFT')//数据量过大,现在不去连表了
		       	->group('historydate,companyid,roomtype')
		       	->order('historydate asc')
		       	->buildSql();
		//var_dump($this->getLastSql());
		//var_dump($subQueryOne);
		//die;
		//第二步,构建子查询,生成sql语句,但是不查询(获取按如期，公司id,房间类型的房间数)
		$fieldTwo = "mm.historydate,mm.roomtype,sum(mm.userstr) as usernum" ;
		$subQueryTwo = Db::table($subQueryOne.' mm' )
		       	->field($fieldTwo)
		       	->group('mm.historydate,mm.roomtype')
		       	->order('mm.historydate','asc')
		       	->buildSql();
		//var_dump($subQueryTwo);
		//die;

		//第三步 获取最后的数据
		$fieldthree = "lq.historydate,group_concat(lq.roomtype SEPARATOR '|') as roomtypestrbygroup,group_concat(lq.usernum SEPARATOR '|') as userstrbygroup";
		if($ispage == 1){
			//执行分页
			$resPeopleData = Db::table($subQueryTwo.' lq')
				   	->field($fieldthree)
					->order('lq.historydate','asc')
					->group('lq.historydate')
					->page($pagenum,$pagesize)
					->select();
		}elseif($ispage == 0){
			//获取全部
			$resPeopleData = Db::table($subQueryTwo.' lq')
			   	->field($fieldthree)
				->order('lq.historydate','asc')
				->group('lq.historydate')
				->select();
		}
		//var_dump($this->getLastSql());
		//var_dump($resPeopleData);
		//var_dump(count($resPeopleData));
		//die;
		Log::write("----getAnalysisListToUser,执行结束时间：".time()."----");
		/******************************获取统计用户数******************************/



		return $resPeopleData;
	}

	public function getAnalysisListToRoom($where,$pagenum,$pagesize,$ispage = 1){
		/******************************获取统计房间数******************************/
		Log::write("----getAnalysisListToRoom,执行开始时间：".time()."----");
		//第一步,构建子查询,先按日期，公司名，房间类型分组
		$fieldFour = "DATE(a.entertime) as historydate,a.companyid as companyid,roomtype,a.userroleid as userroleid,count(distinct(a.serial)) as serialstr" ;
		$subQueryFour = Db::table($this->table)
		    	->alias('a')
		       	->field($fieldFour)
		       	->where($where)
		       	->group('historydate,companyid,roomtype')
		       	->order('historydate asc')
		       	->buildSql();
		//var_dump($this->getLastSql());
		//var_dump($subQueryFour);
		//die;
		//第二步,构建子查询,生成sql语句,但是不查询(获取按如期，公司id,房间类型的房间数)
		$fieldFive = "mm.historydate,mm.roomtype,sum(mm.serialstr) as serialnum" ;
		$subQueryFive = Db::table($subQueryFour.' mm' )
		       	->field($fieldFive)
		       	->group('mm.historydate,mm.roomtype')
		       	->order('mm.historydate','asc')
		       	->buildSql();
		//var_dump($subQueryFive);
		//die;

		//第三步 获取最后的数据
		$fieldSix = "lq.historydate,group_concat(lq.roomtype SEPARATOR '|') as roomtypestrbygroup,group_concat(lq.serialnum SEPARATOR '|') as serialstrbygroup";
		if($ispage == 1){
			$resRoomData = Db::table($subQueryFive.' lq')
				   	->field($fieldSix)
					->order('lq.historydate','asc')
					->group('lq.historydate')
					->page($pagenum,$pagesize)
					->select();
		}elseif($ispage == 0){
			$resRoomData = Db::table($subQueryFive.' lq')
				   	->field($fieldSix)
					->order('lq.historydate','asc')
					->group('lq.historydate')
					->select();
		}
		//var_dump($this->getLastSql());
		//var_dump($resRoomData);
		// var_dump(count($resRoomData));
		// echo time();//结束时间
		// die;
		Log::write("----getAnalysisListToRoom,执行结束时间：".time()."----");
		return $resRoomData;
		/******************************获取统计房间数******************************/		
	}

	/**
	 * [getAnalysisList  统计查询->统计查询历史在线课堂和人员列表的数目]
	 * @author zzq
	 * @DateTime 2018-07-06
	 * @param    [array]                 $where     [筛选条件]
	 * @param    [int]                   $pagenum   [页码数]
	 * @param    [int]                   $pagesize  [每页条数]
	 * @return   [array]                            [查询结果]
	 */
	public function getAnalysisListCount($where){
		Log::write("----getAnalysisListCount,执行开始时间：".time()."----");
		//第一步,构建子查询,生成sql语句,但是不查询(获取按如期，公司id,房间类型的用户数)
		$fieldone = "DATE(a.entertime) as historydate,a.companyid as companyid,roomtype,count((a.userid)) as userstr" ;
		$subQueryOne = Db::table($this->table)
		    	->alias('a')
		       	->field($fieldone)
		       	->where($where)
		       	->group('historydate,companyid,roomtype')
		       	->order('historydate asc')
		       	->buildSql();
		//var_dump($this->getLastSql());
		//var_dump($subQueryOne);
		//die;
		//第二步,构建子查询,生成sql语句,但是不查询(获取按如期，公司id,房间类型的房间数)
		$fieldTwo = "mm.historydate,mm.roomtype,sum(mm.userstr) as usernum" ;
		$subQueryTwo = Db::table($subQueryOne.' mm' )
		       	->field($fieldTwo)
		       	->group('mm.historydate,mm.roomtype')
		       	->order('mm.historydate','asc')
		       	->buildSql();
		//var_dump($subQueryTwo);
		//die;

		//第三步 获取最后的数据
		$fieldthree = "lq.historydate,group_concat(lq.roomtype SEPARATOR '|') as roomtypestrbygroup,group_concat(lq.usernum SEPARATOR '|') as userstrbygroup";
			//获取全部
		$count = Db::table($subQueryTwo.' lq')
			   	->field($fieldthree)
				->order('lq.historydate','asc')
				->group('lq.historydate')
				->count();
		// var_dump($this->getLastSql());
		// die;
		Log::write("----getAnalysisListCount,执行结束时间：".time()."----");
		return $count;
	}


	
	/**
	 * [getAnalysisChartByDateRoomSum  //统计查询->获取某段时间内的各个课堂的课堂数曲线]
	 * @author zzq
	 * @DateTime 2018-08-09
	 * @param    [array]                 $where     [筛选条件]
	 * @return   [array]                            [查询结果]
	 * where [companyid date roomtype]
	 */
	public function getAnalysisChartByDateRoomSum($where){
		// var_dump($where);
		// die;
		$fieldOne = "DATE(starttime) as historydate,a.roomtype,count(a.serial) as roomnum";   
		$subQueryOne = Db::table($this->table)
				->alias('a')
			   	->field($fieldOne)
			   	->where($where)
			   	->where('a.userroleid','0') //老师开课
			   	->where('a.entertime','not null') //进入时间
			   	->where('a.outtime','not null') //离开时间
			   	->where('a.starttime','not null') //老师开课
			   	->where('a.endtime','not null') //老师开课
				->group('historydate,a.roomtype') //按照时间 课堂类型分组
				->order('historydate','asc')
				->buildSql();
		$fieldTwo = "lq.historydate,group_concat(lq.roomtype) as roomtypestr,group_concat(lq.roomnum) as roomnumstr";
		$res = Db::table($subQueryOne.' lq')
				->field($fieldTwo)
				->group('lq.historydate')
				->order('lq.historydate','asc')
				->select();
		// var_dump($this->getLastSql());
		// die;
		return $res;		
	}

	/**
	 * [getAnalysisChartByDateUserSum  //统计查询->获取某段时间内的各个课堂的用户数曲线]
	 * @author zzq
	 * @DateTime 2018-08-09
	 * @param    [array]                 $where     [筛选条件]
	 * @return   [array]                            [查询结果]
	 * where [companyid date roomtype]
	 */
	public function getAnalysisChartByDateUserSum($where){
		$fieldOne = "DATE(starttime) as historydate,a.roomtype,count(a.userid) as usernum";   
		$subQueryOne = Db::table($this->table)
				->alias('a')
			   	->field($fieldOne)
			   	->where($where)
			   	->where('a.entertime','not null') //进入时间
			   	->where('a.outtime','not null') //离开时间
			   	->where('a.starttime','not null') //老师开课
			   	->where('a.endtime','not null') //老师开课
				->group('historydate,a.roomtype') //按照时间 课堂类型分组
				->order('historydate','asc')
				->buildSql();
		$fieldTwo = "lq.historydate,group_concat(lq.roomtype) as roomtypestr,group_concat(lq.usernum) as usernumstr";
		$res = Db::table($subQueryOne.' lq')
				->field($fieldTwo)
				->group('lq.historydate')
				->order('lq.historydate','asc')
				->select();
		// var_dump($this->getLastSql());
		// die;
		return $res;	
	}

	//获取某个在线教室的进出人员记录列表
	//0：主讲  1：助教    2: 学员
	//第一:取出在线人员roomusepoint与logininfo表left join   
	/**
	 * [getUserRecordListBySeria  //获取某个教室的进出人员记录列表]
	 * @author zzq
	 * @DateTime 2018-08-07
	 * @param    [companyid]                 机构id     [筛选条件]
	 * @param    [roomid]                 $where     [筛选条件]
	 * @param    [int]                   $pagenum   [页码数]
	 * @param    [int]                   $pagesize  [每页条数]
	 * @return   [array]                            [查询结果]
	 */
	public function getOnlineUserRecordList($companyid,$roomid,$pagenum,$pagesize,$starttime){
		//1 roomusepoint连接logininfo获取在线人的列表 userid=userid identification=identification comapnyid serial
		$sql1 = " select a.userid,a.identification,a.entertime,a.outtime,a.userroleid,a.serial,a.companyid,a.username FROM logininfo a RIGHT JOIN roomusepoint b ON a.userid=b.buddyid and a.identification = b.identification WHERE a.serial = $roomid AND a.companyid = $companyid and a.outtime is null";
		//2 logininfo获取当前离线的人数列表 starttime>当前在线教师的starttime
		$sql2 = " select userid,identification,entertime,outtime,userroleid,serial,companyid,username from logininfo where starttime >= '".$starttime."' and outtime is not null and serial = $roomid AND companyid = $companyid ";
		//$pagesize = 3;
		$limit = ($pagenum-1)*$pagesize;
		//做联合查询在线的在最前面
		$sql = "".$sql1."  UNION ALL  ".$sql2."order by outtime asc,userroleid asc,entertime asc  limit $limit ".","."$pagesize ";
		//$sql ="select userid from ( select userid from logininfo union all select userid from logininfo) as t order by userid limit 0,1";
		//$data1 = Db::query($sql1);
		//$data2 = Db::query($sql2);
		// var_dump($sql);
		// die;
	    $data = Db::query($sql);
		//var_dump($data1);
		//var_dump($data2);
		// var_dump($data);
		// die;
		return $data;
	}

	/**
	 * [getUserRecordListBySeria  //获取某个在线教室的进出人员记录的总数目]
	 * @author zzq
	 * @DateTime 2018-08-07
	 * @param    [companyid]                 机构id     [筛选条件]
	 * @param    [roomid]                 $where     [筛选条件]
	 * @param    [int]                   $pagenum   [页码数]
	 * @param    [int]                   $pagesize  [每页条数]
	 * @return   [array]                            [查询结果]
	 */
	public function getOnlineUserRecordListCount($companyid,$roomid,$starttime){
		//1 roomusepoint连接logininfo获取在线人的列表 userid=userid identification=identification comapnyid serial
		$sql1 = " select count(*) as countOne FROM logininfo a RIGHT JOIN roomusepoint b ON a.userid=b.buddyid and a.identification = b.identification WHERE a.serial = $roomid AND a.companyid = $companyid  and a.outtime is null";
		//2 logininfo获取当前离线的人数列表 starttime>当前在线教师的starttime
		$sql2 = " select count(*) as countOne from logininfo where starttime >= '".$starttime."' and outtime is not null and serial = $roomid AND companyid = $companyid ";
		//$pagesize = 3;
		//做联合查询在线的在最前面
		$sql = "select sum(t.countOne) as num from  ( ".$sql1."   UNION ALL   ".$sql2." ) as t";
		// var_dump($sql);
		// die;
	    $data = Db::query($sql);
		return $data[0]['num'];
	}



	//获取某个教室的进出人员记录列表的记录数
	public function getUserRecordListCountBySerial($roomid){
		$field = "serial";
		$count = Db::table($this->table)
		->field($field)
		->where('serial','EQ',$roomid)
		->count();
		return $count;		
	}

	/**
	 * [getAnalysisRoomSumByComAndDate  //获取某天的全部机构或者某机构总课堂数和总人数]
	 * @author zzq
	 * @DateTime 2018-08-09
	 * @param    [array]                 $data     [筛选条件]
	 * @return   [array]                            [查询结果]
	 * where [companyid date roomtype]
	 */
	public function getAnalysisRoomSumByComAndDate($data){
		$where = [];
		if(!empty($data['companyid'])){
			$where['companyid'] = $data['companyid'];
		}
		$where['roomtype'] = $data['roomtype'];
		$fieldOne = "DATE(starttime) as certaindate,a.roomtype,count(a.userid) as roomnum";   
		$subQueryOne = Db::table($this->table)
				->alias('a')
			   	->field($fieldOne)
			   	->where($where)
			   	->where('a.userroleid','0') //老师开课
			   	->where('a.entertime','not null') //进入时间
			   	->where('a.outtime','not null') //离开时间
			   	->where('a.starttime','not null') //老师开课
			   	->where('a.endtime','not null') //老师开课
				->group('certaindate,a.roomtype') //按照时间 课堂类型分组
				->having("certaindate = '".$data['date']."'")
				->order('certaindate','asc')
				->buildSql();
		// var_dump($subQueryOne);
		// die;
		$fieldTwo = "lq.certaindate,group_concat(lq.roomtype) as roomtypestr,group_concat(lq.roomnum) as roomnumstr";
		$res = Db::table($subQueryOne.' lq')
				->field($fieldTwo)
				->group('lq.certaindate')
				->order('lq.certaindate','asc')
				->select();
		// var_dump($this->getLastSql());
		// die;
		return $res;	
	}

	/**
	 * [getAnalysisUserSumByComAndDate  //获取某天的全部机构或者某机构总课堂数和总人数]
	 * @author zzq
	 * @DateTime 2018-08-09
	 * @param    [array]                 $data     [筛选条件]
	 * @return   [array]                            [查询结果]
	 * where [companyid date roomtype]
	 */
	public function getAnalysisUserSumByComAndDate($data){
		$where = [];
		if(!empty($data['companyid'])){
			$where['companyid'] = $data['companyid'];
		}
		$where['roomtype'] = $data['roomtype'];
		$fieldOne = "DATE(starttime) as certaindate,a.roomtype,count(a.serial) as usernum";   
		$subQueryOne = Db::table($this->table)
				->alias('a')
			   	->field($fieldOne)
			   	->where($where)
			   	->where('a.entertime','not null') //进入时间
			   	->where('a.outtime','not null') //离开时间
			   	->where('a.starttime','not null') //老师开课
			   	->where('a.endtime','not null') //老师开课
				->group('certaindate,a.roomtype') //按照时间 课堂类型分组
				->having("certaindate = '".$data['date']."'")
				->order('certaindate','asc')
				->buildSql();
		// var_dump($subQueryOne);
		// die;
		$fieldTwo = "lq.certaindate,group_concat(lq.roomtype) as roomtypestr,group_concat(lq.usernum) as usernumstr";
		$res = Db::table($subQueryOne.' lq')
				->field($fieldTwo)
				->group('lq.certaindate')
				->order('lq.certaindate','asc')
				->select();
		// var_dump($this->getLastSql());
		// die;
		return $res;	
	}

	
	/**
	 * [getSerialListByComAndDate  //获取(全部)某机构某天的课堂列表(存在同一个serial更改roomtype的情况)]
	 * @author zzq
	 * @DateTime 2018-07-06
	 * @param    [array]               $where       [筛选条件]
	 * @param    [int]                 $pagenum     [页码数]
	 * @param    [int]                 $pagesize    [每页条数]
	 * @return   [array]                            [查询结果]
	 */	
	public function getSerialListByComAndDate($where,$pagenum,$pagesize,$date){

		// var_dump($where);
		// die;
		$field = "DATE(starttime) as certaindate,a.serial,a.starttime,a.endtime,a.roomtype,a.companyid";   
		$res = Db::table($this->table)
				->alias('a')
			   	->field($field)
			   	->where($where)
			   	->where('userroleid','0')
			   	->where('a.entertime','not null') //进入时间
			   	->where('a.outtime','not null') //离开时间
			   	->where('a.starttime','not null') //老师开课
			   	->where('a.endtime','not null') //老师开课
				->having("certaindate = '".$date."'")
				->order('a.serial','asc')
				->order('a.starttime','asc')		
				->page($pagenum,$pagesize)
				->select();
		//var_dump($this->getLastSql());
		// var_dump($res);
		// die;
		return $res;			
	}

	/**
	 * [getSerialListCountByComAndDate  //获取(全部)某机构某天的课堂列表的数目(存在同一serial更改roomtype)]
	 * @author zzq
	 * @DateTime 2018-07-06
	 * @param    [array]                 $where     [筛选条件]
	 * @param    [string]                $date      [日期条件]
	 * @return   [array]                            [查询结果]
	 */	
	public function getSerialListCountByComAndDate($where,$date){
		// var_dump($where);
		//var_dump($date);
		//die;
		$field = "DATE(starttime) as certaindate,a.serial,a.starttime,a.endtime,a.roomtype,a.companyid";   
		$res = Db::table($this->table)
				->alias('a')
			   	->field($field)
			   	->where($where)
			   	->where('userroleid','0')
			   	->where('a.entertime','not null') //进入时间
			   	->where('a.outtime','not null') //离开时间
			   	->where('a.starttime','not null') //老师开课
			   	->where('a.endtime','not null') //老师开课
				->having("certaindate = '".$date."'")
				->order('a.starttime','asc')
				->select();//为什么用count报错
		//var_dump($this->getLastSql());
		// var_dump($res);
		//die;
		return count($res);		
	}

	//获取历史课堂
	public function getHisClass($where){
		$field = "userid,serial,roomtype,userroleid,companyid,starttime,endtime";
		$res = Db::table($this->table)
		       	->field($field)
		       	->where($where)
		       	->find();
		 // var_dump($this->getLastSql());
		 // die;
		return $res;
	}


	/**
	 * [getUserCurrencyByComAndDateAndRoom  //获取某天某机构某课堂的并发数]
	 * @author zzq
	 * @DateTime 2018-08-09
	 * @param    [array]                 $where     [筛选条件]
	 * @return   [array]                            [查询结果]
	 */	
	public function getUserCurrencyByComAndDateAndRoom($where){
		// var_dump($where);
		// die;
		$field = "serial,roomtype,companyid,starttime,endtime";
		//$sql = "select * from logininfo where serial=xxx and companyid = xxx and roomtype= xx and useroleid = xx and ( (startTime > a AND startTime < b) OR (startTime < a AND endTime > b) OR (endTime > a AND endTime < b)  )    "
		//闭包获取学生的starttime-endtime与老师starttime-endtime交叉的记录
		//表示该学生上过这节课
		$starttime = $where['starttime'];
		$endtime = $where['endtime'];
		$result = Db::table($this->table)->field($field)->where(function ($query) use($where) {
		    $query->where('companyid', $where['companyid'])
		    ->where('serial', $where['serial'])
		    ->where('roomtype', $where['roomtype'])
		    ->where('userroleid', '2');
		})->where(function ($query) use($where) {
		    $query->whereOr("starttime > '".$where['starttime']."'  and starttime < '".$where['endtime']."'")
		    ->whereOr("starttime <= '".$where['starttime']."'  and endtime >=
		     '".$where['endtime']."'")
		    ->whereOr("endtime > '".$where['starttime']."'  and endtime < '".$where['endtime']."'");
		})->count();
		// var_dump($this->getLastSql());
		// var_dump($result);
		// die;
		return $result;
	}

	/**
	 * [getHisUserToHisRoom  //查看某个userid用户是否上过某次课堂]【这个需要看看?】
	 * @author zzq
	 * @DateTime 2018-08-09
	 * @param    [array]                 $where     [筛选条件]
	 * @return   [array]                            [查询结果]
	 */	
	public function getHisUserToHisRoom($where){
		// var_dump($where);
		// die;
		$field = "userid,username,serial,roomtype,companyid,entertime,outtime,starttime,endtime";
		$result = Db::table($this->table)->field($field)->where($where)->find();
		// var_dump($this->getLastSql());
		// var_dump($result);
		// die;
		return $result;
	}

	/**
	 * [getHisUserListByComAndDateAndRoom  //获取某天某机构某课堂的人员列表]
	 * @author zzq
	 * @DateTime 2018-07-06
	 * @param    [array]                 $where     [筛选条件]
	 * @param    [int]                      $pagenum     [页码]
	 * @param    [int]             $pagesize     [页数]
	 * @return   [array]                            [查询结果]
	 */	
	public function getHisUserListByComAndDateAndRoom($where,$pagenum,$pagesize){
		// var_dump($where);
		// die;
		$field = "userid,userroleid,username,serial,roomtype,companyid,entertime,outtime,starttime,endtime";
		//$sql = "select * from logininfo where serial=xxx and companyid = xxx and roomtype= xx and useroleid = xx and ( (startTime > a AND startTime < b) OR (startTime < a AND endTime > b) OR (endTime > a AND endTime < b)  )    "
		//闭包获取学生的starttime-endtime与老师starttime-endtime交叉的记录
		//表示该学生上过这节课
		$starttime = $where['starttime'];
		$endtime = $where['endtime'];
		$result = Db::table($this->table)->field($field)->where(function ($query) use($where) {
		    $query->where('companyid', $where['companyid'])
		    ->where('serial', $where['serial'])
		    ->where('roomtype', $where['roomtype']);
		})->where(function ($query) use($where) {
		    $query->whereOr("starttime > '".$where['starttime']."'  and starttime < '".$where['endtime']."'")
		    ->whereOr("starttime <= '".$where['starttime']."'  and endtime >=
		     '".$where['endtime']."'")
		    ->whereOr("endtime > '".$where['starttime']."'  and endtime < '".$where['endtime']."'");
		})->order('userroleid asc')->order('entertime asc')->page($pagenum,$pagesize)->select();
		// var_dump($this->getLastSql());
		// var_dump($result);
		// die;
		return $result;
	}

	/**
	 * [getHisUserListByComAndDateAndRoom  //获取某天某机构某课堂的人员列表的数目]
	 * @author zzq
	 * @DateTime 2018-07-06
	 * @param    [string]                $certainDate     [日期]
	 * @param    [string]                $where           [条件]
	 * @return   [array]                                  [查询结果]
	 */	            
	public function getHisUserListCountByComAndDateAndRoom($where){
		$field = "userid,userroleid,username,serial,roomtype,companyid,entertime,outtime,starttime,endtime";
		//$sql = "select * from logininfo where serial=xxx and companyid = xxx and roomtype= xx and useroleid = xx and ( (startTime > a AND startTime < b) OR (startTime < a AND endTime > b) OR (endTime > a AND endTime < b)  )    "
		//闭包获取学生的starttime-endtime与老师starttime-endtime交叉的记录
		//表示该学生上过这节课
		$starttime = $where['starttime'];
		$endtime = $where['endtime'];
		$result = Db::table($this->table)->field($field)->where(function ($query) use($where) {
		    $query->where('companyid', $where['companyid'])
		    ->where('serial', $where['serial'])
		    ->where('roomtype', $where['roomtype']);
		})->where(function ($query) use($where) {
		    $query->whereOr("starttime > '".$where['starttime']."'  and starttime < '".$where['endtime']."'")
		    ->whereOr("starttime <= '".$where['starttime']."'  and endtime >=
		     '".$where['endtime']."'")
		    ->whereOr("endtime > '".$where['starttime']."'  and endtime < '".$where['endtime']."'");
		})->order('userroleid asc')->order('entertime asc')->count();
		// var_dump($this->getLastSql());
		// var_dump($result);
		// die;
		return $result;	
	}

	/**
	 * [getHisUserDownNetworkUserList  查出历史用户所有的下行人员]
	 * @author zzq
	 * @DateTime 2018-08-10
	 * @param    [array]                 $where     [筛选条件]
	 * @return   [array]                            [查询结果]
	 */	
	public function getHisUserDownNetworkUserList($where){
		// var_dump($where);
		// die;
		$field = "userid,userroleid,username,serial,roomtype,companyid,entertime,outtime,starttime,endtime";
		//$sql = "select * from logininfo where serial=xxx and companyid = xxx and roomtype= xx and useroleid = xx and ( (startTime > a AND startTime < b) OR (startTime < a AND endTime > b) OR (endTime > a AND endTime < b)  )    "
		//闭包获取学生的starttime-endtime与老师starttime-endtime交叉的记录
		//表示该学生上过这节课
		$starttime = $where['starttime'];
		$endtime = $where['endtime'];
		$result = Db::table($this->table)->field($field)->where(function ($query) use($where) {
		    $query->where('companyid', $where['companyid'])
		    ->where('serial', $where['serial'])
		    ->where('userid','NEQ',$where['userid'])
		    ->where('roomtype', $where['roomtype']);
		})->where(function ($query) use($where) {
		    $query->whereOr("starttime > '".$where['starttime']."'  and starttime < '".$where['endtime']."'")
		    ->whereOr("starttime <= '".$where['starttime']."'  and endtime >=
		     '".$where['endtime']."'")
		    ->whereOr("endtime > '".$where['starttime']."'  and endtime < '".$where['endtime']."'");
		})->order('userroleid asc')->order('entertime asc')->select();
		// var_dump($this->getLastSql());
		// var_dump($result);
		// die;
		return $result;
	}


	/**
	 * [getUserInfoByComDateRoom  //获取某天某机构某课堂的某人的的在线信息]
	 * @author zzq
	 * @DateTime 2018-07-06
	 * @param    [string]                $certainDate     [日期]
	 * @param    [string]                $where           [条件]
	 * @return   [array]                                  [查询结果]
	 */	
	public function getUserInfoByComDateRoom($where,$date){
		$field = "Date(entertime) as certaindate,userid,serial,roomtype,companyid,userroleid,entertime,outtime,ipaddress,deviceno,devicetype,username,operatingsystem";
		$subQueryOne = Db::table($this->table)
		    	->alias('a')
		       	->field($field)
		       	->where($where)
		       	->having("certaindate = '".$date."'")
		       	->buildSql();
		// var_dump($subQueryOne);
		// die;
		$fieldTwo = "mm.certaindate,mm.userid,mm.roomtype,mm.serial,mm.companyid,mm.userroleid,mm.entertime,mm.outtime,mm.ipaddress,mm.deviceno,mm.devicetype,mm.username,mm.operatingsystem" ;
		$res = Db::table($subQueryOne.' mm' )
		       	->field($fieldTwo)
		       	->find();
		return $res;		
	}

	public function getOnlineUserInfo($where){
		$field = "userid,identification,companyid,serial,entertime,outtime,operatingsystem,deviceno,devicetype,ipaddress,starttime,endtime,username";
		//$field = "*";
		$res = Db::table($this->table)
		       	->field($field)
		       	->where($where)
		       	->find();
		// var_dump($this->getLastSql());
		// die;
		return $res;		
	}


    //获取某一段时间某个机构,某个教室,某个人员的设备信息
    //分为取出最新的一条还是取出多条？
    /**
     * 
     * @date   2018-08-06
     * @Author zzq
     * @param  companyid  当前页码(表示人员进出记录的列表)
     * @param  roomid   在线教室的id       
     * @param  userid   在线用户id      
     * @param  fromtime 开始时间      
     * @param  totime   截止时间      
     * @param  limit   0表示取出全部 1表示取出最近的一条      
     * @return  array          
     */
    public function getDeviceInfoByComSerialUser($companyid,$serial,$userid,$fromtime,$totime,$limit){

        $config=[
            'dbname'=>config('mongodb.equipment_dbname'),//选择数据库
            'collection'=>config('mongodb.equipment_collection')//选择集合
        ];
        $obj = new MongodbManage($config);
        // var_dump($obj);
        // die;
        //获取当前用户的设备信息
        $where = [];
        $options = [];
        $where['peerId'] = $userid;
        $where['serial'] = $serial;
        $where['companyid'] = $companyid;

        $option['skip'] = 0;
        //只取出一条
        if($limit > 0){

            $option['limit'] = $limit;
        }
        $where['statistical.time'] = ['$gte'=>$fromtime,'$lte'=>$totime];
        $option['skip'] = 0;
        $option['projection'] = [];
        //按照时间倒序
        $option['sort'] = ['statistical.time'=>-1];
        // var_dump($where);
        // var_dump($option);
        // die;
        $res = $obj->find($where,$option);
        // var_dump($res);
        // die;
        return $res;
    }

    //获取某一段时间某个机构,某个教室,某个人员的网络信息
    //分为取出最新的一条还是取出多条？
    /**
     * 
     * @date   2018-08-06
     * @Author zzq
     * @param  companyid  当前页码(表示人员进出记录的列表)
     * @param  roomid   在线教室的id       
     * @param  userid   在线用户id      
     * @param  otheruserid   在线用户id      
     * @param  fromtime 开始时间      
     * @param  totime   截止时间      
     * @param  limit   0表示取出全部 1表示取出最近的一条    
     * @param  showtype   0表示取出该人上行,所有下行 1表示取出上行或者某个下行 2取出该人的所有下行   
     * @return  array          
     */
    public function getNetworkInfoByComSerialUser($companyid,$serial,$userid,$otheruserid,$fromtime,$totime,$limit,$showtype){
        $config=[
            'dbname'=>config('mongodb.networkequipment_dbname'),//选择数据库
            'collection'=>config('mongodb.networkequipment_collection')//选择集合
        ];
        $obj = new MongodbManage($config);
        // var_dump($obj);
        // die;
        //获取当前用户的设备信息
        $where = [];
        $options = [];
        $where['myPeerId'] = $userid;
        $where['serial'] = $serial;
        $where['companyid'] = $companyid;
        if($showtype == 1){
        	$where['statistical.0.peerId'] = $otheruserid;
        }
        if($showtype == 2){
        	$where['statistical.0.peerId'] = ['$ne'=>$otheruserid];
        }
        $where['statistical.time'] = ['$gte'=>$fromtime,'$lte'=>$totime];
        //只取出一条
        if($limit > 0){
            $option['limit'] = $limit;
        }
        $option['skip'] = 0;
        $option['projection'] = [];
        //按照时间倒序
        $option['sort'] = ['statistical.time'=>-1];
        // var_dump($where);
        // var_dump($option);
        // die;
        $res = $obj->find($where,$option);
        // var_dump($res);
        // die;
        return $res;        
    }


    //获取一定时间内某个用户的上下行信息
    /**
     * 
     * @date   2018-08-08
     * @Author zzq           
     * @param  fromtime 开始时间      
     * @param  totime   截止时间     
     * @return  array          
     */
    public function getNetworkInfo($fromtime,$totime){
        $config=[
            'dbname'=>config('mongodb.networkequipment_dbname'),//选择数据库
            'collection'=>config('mongodb.networkequipment_collection')//选择集合
        ];
        $obj = new MongodbManage($config);
        // var_dump($obj);
        // die;
        //获取当前用户的设备信息
        $where = [];
        $options = [];
        $where['statistical.time'] = ['$gte'=>$fromtime,'$lte'=>$totime];
        $option['skip'] = 0;
        $option['projection'] = [];
        //按照时间倒序
        $option['sort'] = ['statistical.time'=>-1];
        // var_dump($where);
        // var_dump($option);
        // die;
        $res = $obj->find($where,$option);
        // var_dump($res);
        // die;
        return $res;        
    }
}	