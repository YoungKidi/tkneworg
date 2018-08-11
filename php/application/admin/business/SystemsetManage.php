<?php
/*
*监课统计的业务逻辑处理
*/
namespace app\admin\business;
use RedisClient;
use MongodbClient;
use app\admin\business\MongodbManage;
use app\admin\model\Companystorage;
use app\admin\model\Company;
use app\admin\model\Roomusepoint;
use app\admin\model\Logininfo;
use app\admin\model\Userinfo;
use app\admin\model\Serverinfo;
use app\admin\model\Room;
use app\admin\model\Fileinfo;
use app\admin\model\Recordinfo;
use app\admin\model\Operatelog;
use Exportexcel;

//record 和 record:timeout
/*{
  	"Instance": "879534994",
  	"Name": 1531449542930,
  	"Path": "http://192.168.1.9:8000/dfd2f888-e200-4143-8085-6b709395f5bc-879534994record.json",
  	"Createtime": 1531449543,
  	"Duration": 32012,
  	"convert": 1,
  	"recordtype": 0
}
{
	"Instance":"1871526416","Name":1531712921386,"Path":"http://recorddemo-1253417915.cosgz.myqcloud.com/2637e9f5-9609-4235-844c-2ca3b6c34288-1871526416/record.json","Createtime":1531712921,"Duration":11447852,"convert":1,"recordtype":0,"curpath":"2637e9f5-9609-4235-844c-2ca3b6c34288-1871526416/","k":"record:timeout","retry":60,"timeoutCount":5}

*/
class SystemsetManage {
	/**
	 *	转换列表
	 *  Redis list数据类型 暂时添加不了搜索关键字????
	 *	@author zzq
	 *	@param  $data array 
	 *	@return array 
	 */
	public function conversionlist($data){
		$pagesize = config('pagesize.admin_conversionlist');//每页行数
		//$pagesize = 5;
		$pagenum  = $data['pagenum'] ;//当前页码
		//校验converttype
		$arr = [1,2];
		if(empty($data['converttype'])){
			return return_format('', 50011, lang('convert_error'));
		}
		$converttype = $data['converttype'];
		if(!in_array($converttype,$arr)){
			return return_format('', 50011, lang('convert_error'));
		}
   		//校验
		if($pagenum < 0 ){
			return return_format('', 50003, lang('pagenum_error'));
		}
		//判断类型
		if($data['converttype'] == 1){
			//1代表录制件
			if(!isset($data['recordtype'])){
				return return_format('', 50013, lang('recordconverttype_error'));
			}
			$tip = [0,1,2];
			if(!in_array($data['recordtype'],$tip)){
				return return_format('', 50013, lang('recordconverttype_error'));
			}
			/***************redis获取录制件转换状态***************/
			//计算起始位置
			//$start = ($pagenum-1)*$pagesize;
			//$end = $pagenum*$pagesize-1;
			switch ($data['recordtype']) {
				case 0:
					$key = 'record';
					break;
				case 1:
					$key = 'record:timeout';
					break;	
				case 2:
					$key = 'convert:failed';
					break;
				default:
					$key = 'record';
					break;
			}

			$redis  = RedisClient::getInstance();
			//$data     = $redis->lrange($key,$start,$end);
			//$total    = $redis->lsize($key);
			//获取键中所有的值
			$list = $redis->lrange($key,0,-1);
			//获取所有的list中的所有的录制件的recordtitle->Name
			//Name就是recordtitile
			if($list){
				foreach($list as $k => $v){
					$single = json_decode($v,true);
					$recordtitleArr = $single['Name'];
				}
			}
			// var_dump($recordtitleArr);
			// die;
			/***************redis获取录制件转换状态***************/
			//构造搜索条件
			$where = [];
			//Redis的条件 
			if($recordtitleArr){
				$where['a.recordtitle'] = ['IN',$recordtitleArr];
			}
			//状态条件
			if(!isset($data['recordstatus'])){
				return return_format('', 50002, lang('recordstatus_error'));
			}
			$recordstatus  = $data['recordstatus'] ;//当前录制件状态
			$ret = [0,1];//0代表有效  1代表删除
			if(!in_array($recordstatus,$ret)){
				return return_format('', 50002, lang('recordstatus_error'));
			}
			$where['a.state'] = ['EQ',$recordstatus];
			//匹配机构名称
	        if (!empty($data['companykeyword'])) {
				$where['b.companyfullname'] = ['like','%'.$data['companykeyword'].'%'] ;
	        }
	        //匹配日期   
	        if(!empty($data['edate'])){
		        //获取截止日期
		        $ConvertEndtime = strtotime($data['edate']);
		        //截止日期加上一天，表示包含当天的数据
		        $ConvertEndtime = $ConvertEndtime + 60*60*24;

		        $ConvertEnddate = Date('Y-m-d H:i:s',$ConvertEndtime);
	        }
	        if(empty($data['edate']) && !empty($data['sdate'])){
	            //大于某个时间
	            $where['a.starttime'] = ['>= time', $data['sdate']];
	        }elseif(!empty($data['edate']) && empty($data['sdate'])){
	            //小于某个时间
	            $where['a.starttime'] = ['<= time', $ConvertEnddate];
	        }elseif(!empty($data['sdate']) && !empty($data['edate'])){
	            $where['a.starttime'] = ['between time', [$data['sdate'],$ConvertEnddate] ];
	        }
	        $recordinfo = new Recordinfo();
	        // var_dump($where);
	        // die;
	        $res = $recordinfo->getRecordinfoList($where,$pagenum,$pagesize);
	        foreach($res as $k => $v){
	        	//转换时间格式
	        	$res[$k]['starttime'] = Date('Y-m-d H:i:s',$v['starttime']);
	        	//转换文件格式(看下是B还是Kb)
	        	$res[$k]['size'] = formatSize($v['size'],$times=0);
	        	//转换录制件时长格式
	        	$res[$k]['duration'] = showTime($v['duration']/1000);
	        	//转换录制件状态为文字
	        	if($v['state'] == 0){
	        		$res[$k]['state'] = "有效";
	        	}elseif($v['state'] == 1){
	        		$res[$k]['state'] = "删除";
	        	}
	        	if($v['recordtype'] == 0){
	        		$res[$k]['recordtype'] = "课堂录制件";
	        	}elseif($v['recordtype'] == 1){
	        		$res[$k]['recordtype'] = "直播录制件";
	        	}
	        }
	        $count = $recordinfo->getRecordinfoListCount($where);
	        // var_dump($res);
	        // var_dump($count);                    
			//返回数组组装
			$result = [
				 	'data'=>$res,// 内容结果集
				 	'pageinfo'=>[
				 		'pagesize'=> $pagesize ,// 每页多少条记录
				 		'pagenum' => $pagenum ,//当前页码
				 		'count'   => $count // 符合条件总的记录数
				 	]
				] ;
			return return_format($result,0) ;	
		}elseif($data['converttype'] == 2){
			//2代表课件
			//构造搜索条件
			$where = [];
			//匹配机构名称
	        if (!empty($data['companykeyword'])) {
				$where['b.companyfullname'] = ['like','%'.$data['companykeyword'].'%'] ;
	        }
	        //匹配日期   
	        if(!empty($data['edate'])){
		        //获取截止日期
		        $ConvertEndtime = strtotime($data['edate']);
		        //截止日期加上一天，表示包含当天的数据
		        $ConvertEndtime = $ConvertEndtime + 60*60*24;

		        $ConvertEnddate = Date('Y-m-d H:i:s',$ConvertEndtime);
	        }
	        if(empty($data['edate']) && !empty($data['sdate'])){
	            //大于某个时间
	            $where['a.uploadtime'] = ['>= time', $data['sdate']];
	        }elseif(!empty($data['edate']) && empty($data['sdate'])){
	            //小于某个时间
	            $where['a.uploadtime'] = ['<= time', $ConvertEnddate];
	        }elseif(!empty($data['sdate']) && !empty($data['edate'])){
	            $where['a.uploadtime'] = ['between time', [$data['sdate'],$ConvertEnddate] ];
	        }
	        //匹配文件编号或者文件名称
	        if (!empty($data['filekeyword'])) {
				$where['a.fileid|a.filename'] = ['like','%'.$data['filekeyword'].'%'] ;
	        }    
			//filestatus  0未转换 1已转换 9代表已删除(课件)
			if(!isset($data['filestatus'])){
				return return_format('', 50012, lang('filestatus_error'));
			}
			$filestatus  = $data['filestatus'] ;//当前类型
			$ret = [0,1,9];
			if(!in_array($filestatus,$ret)){
				return return_format('', 50012, lang('filestatus_error'));
			}
			//状态条件
			$where['a.status'] = ['EQ',$filestatus];
	        $fileinfo = new Fileinfo();
	        $res = $fileinfo->getFileinfoList($where,$pagenum,$pagesize);
	        foreach($res as $k => $v){
	        	$res[$k]['size'] = formatSize($v['size'],$times=0);
	        	//转换课件状态为文字
	        	if($v['status'] == 0){
	        		$res[$k]['status'] = "不可见";
	        	}elseif($v['status'] == 1){
	        		$res[$k]['status'] = "可见";
	        	}elseif($v['status'] == 9){
	        		$res[$k]['status'] = "删除";
	        	}
	        }
	        $count = $fileinfo->getFileinfoListCount($where);
	        // var_dump($res);
	        // var_dump($count);                    
			//返回数组组装
			$result = [
				 	'data'=>$res,// 内容结果集
				 	'pageinfo'=>[
				 		'pagesize'=> $pagesize ,// 每页多少条记录
				 		'pagenum' => $pagenum ,//当前页码
				 		'count'   => $count // 符合条件总的记录数
				 	]
				] ;
			return return_format($result,0) ;			
		}
	}

	/**
	 *	获取 日志列表 
	 *  mongodb数据类型
	 *	@author zzq
	 *	@param  $data array 
	 *	@return array 
	 */
	public function loginfolist($data){
		$pagesize = config('pagesize.admin_loginfolist');//每页行数
		//$pagesize = 1;
		$pagenum  = $data['pagenum'] ;//当前页码
		//验证
		if($pagenum < 0 ){
			return return_format('', 50003, lang('pagenum_error'));
		}
		if(!isset($data['infotype'])){
			return return_format('', 50004, lang('infotype_error'));
		}
		$ret = [1,2];
		if(!in_array($data['infotype'],$ret)){
			return return_format('', 50004, lang('infotype_error'));
		}
		if($data['infotype'] == 1){
			$where = [];

			//用户名
			if(!empty($data['useraccount'])){
				$where['useraccount'] = ['like',"%".$data['useraccount']."%"];
			}

			//角色id
			if(isset($data['userroleid'])){
				if($data['userroleid'] != ''){
					$where['userroleid'] = ['EQ',$data['userroleid']];
				}
			}
			//die;
	        //对日期进行匹配
	        if( !empty($data['stime']) && !empty($data['etime']) ){
	            $where['addtime'] = ['between', [Date('Y-m-d',strtotime($data['stime'])),Date('Y-m-d',strtotime($data['etime']))] ];
	        }else if( !empty($data['stime']) ){
	            $where['addtime'] = ['>=', Date('Y-m-d',strtotime($data['stime'])) ];
	        }else if( !empty($data['edate']) ){
	            $where['adddtime'] = ['<=', Date('Y-m-d',strtotime($data['etime']))  ];
	        }else{
	        	//默认匹配当前月份到之前的六个月
	        	$etime = strtotime("-6 day");
			    $edate = date('Y-m-d',time());
			    $sdate = date('Y-m-d',$etime); 
	        	$where['addtime'] = ['between', [$sdate,$edate] ];
	        }
	        //操作日志
	        $operatelog = new Operatelog();
	        $res = $operatelog->getOperateLogList($where,$pagenum,$pagesize);
	        foreach ($res as $k => $v) {
	        	$res[$k]['userrolename'] = getRoleNameByRoleId($v['userroleid']);
	        }
	        $count = $operatelog->getOperateLogListCount($where);
			//表示登录日志
			//返回数组组装
			$result = [
				 	'data'=>$res,// 内容结果集
				 	'pageinfo'=>[
				 		'pagesize'=> $pagesize ,// 每页多少条记录
				 		'pagenum' => $pagenum ,//当前页码
				 		'infotype' => $data['infotype'] ,//当前类型
				 		'count'   => $count // 符合条件总的记录数
				 	]
				] ;
			return return_format($result,0) ;
		}elseif ($data['infotype'] == 2) {
			//表示系统日志
			$where = [];
			//对ip进行筛选
	        if (!empty($data['ip'])) {
	            $where['ip'] = $data['ip'];
	        }
	        //对内容进行模糊筛选
	        if (!empty($data['data'])) {
	        	$key = trim($data['data']);
	            $where['data'] = new \MongoDB\BSON\Regex(".*{$key}.*", '');;
	        }
	        //对pid进行筛选
	        if (!empty($data['pid'])) {
	            $where['pid'] = intval($data['pid']);
	        }
	        //时间筛选
	        if( !empty($data['stime']) && !empty($data['etime']) ){
	        	date_default_timezone_set("PRC");
	            $stime = convertToUtcDate($data['stime']);
	            //多加一天
	            date_default_timezone_set("PRC");
	            $str = Date('Y-m-d H:i:s',strtotime($data['etime'])+60*60*24);
	            $etime = convertToUtcDate($str);
	            $where['timestamp'] = ['$gte'=>$stime,'$lte'=>$etime];
	        }else if( !empty($data['stime']) ){
	        	date_default_timezone_set("PRC");
	            $stime = convertToUtcDate($data['stime']);
	            $where['timestamp'] = ['$gte'=>$stime];
	        }else if( !empty($data['etime']) ){
	            date_default_timezone_set("PRC");
	            $str = Date('Y-m-d H:i:s',strtotime($data['etime'])+60*60*24);
	            $etime = convertToUtcDate($str);
	            $where['timestamp'] = ['$lte'=>$etime];
	        }
	        date_default_timezone_set("PRC");
	        $config=[
	        	'dbname'=>config('mongodb.MongoDB_useDB2'),//选择数据库
	        	'collection'=>'log'//选择集合
	        ];
	        $mongodb = new MongodbClient($config);
	       // var_dump($mongodb);
	        //die;
	        $projection = ['_id'=>0];//过滤掉的字段
	        $sort = ['timestamp'=>-1];//过滤掉的字段
	        $res = $mongodb->page($where,$pagenum,$pagesize,$projection,$sort);
	        //utc时间转成北京时间
	        foreach($res as $k => $v){
	        	date_default_timezone_set('PRC');
                $timeStr = strtotime($v['timestamp']);
                $timeDate = Date('Y-m-d H:i:s',$timeStr);
	        	$res[$k]['timestamp'] = $timeDate;
	        }
	        //var_dump($res);
	        //die;
	        $count = $mongodb->getCount($where);
			//返回数组组装
			$result = [
				 	'data'=>$res,// 内容结果集
				 	'pageinfo'=>[
				 		'pagesize'=> $pagesize ,// 每页多少条记录
				 		'pagenum' => $pagenum ,//当前页码
				 		'infotype' => $data['infotype'] ,//当前类型
				 		'count'   => $count // 符合条件总的记录数
				 	]
				] ;
			return return_format($result,0) ;
		}

	}

	/**
	 *	添加操作日志
	 *  mysql数据类型
	 *	@author zzq
	 *	@param  $data array 
	 *	@return array 
	 */
	public function addoperatelog($data){
		if( empty($data['useraccount']) || empty($data['userid']) || empty($data['userroleid']) || empty($data['content'])  ){
			return return_format('', 50003, lang('param_empty'));
		}
		$obj = new Operatelog();
		$res = $obj->addOperateLog($data);
	}
	/**
	 *	获取 存储列表 
	 *  mysql数据类型
	 *	@author zzq
	 *	@param  $data array 
	 *	@return array 
	 */
	public function storage($data){
		$where = [];
		$pagesize = config('pagesize.admin_storagelist');//每页行数
		//$pagesize = 100;
		//www域能查看全部的信息,其他的只能查看相应机构的数据
		$companyid = !empty(session('curcompanyid')) ? session('curcompanyid') : 1;
		//$companyid =  10289;
		if($companyid != 1){
			$where['a.companyid'] =  ['EQ',$companyid];
		}
		$pagenum  = $data['pagenum'] ;//当前页码
		//验证
		if($pagenum < 0 ){
			return return_format('', 50003, lang('pagenum_error'));
		}
		//对companykeyword进行筛选
        if (!empty($data['companykeyword'])) {
            $where['a.companyid|b.companyfullname'] = ['like','%'.$data['companykeyword'].'%'];
        }

        //对日期进行匹配
        if( !empty($data['sdate']) && !empty($data['edate']) ){
            $where['a.datemonth'] = ['between', [Date('Y-m',strtotime($data['sdate'])),Date('Y-m',strtotime($data['edate']))] ];
        }else if( !empty($data['sdate']) ){
            $where['a.datemonth'] = ['>=', Date('Y-m',strtotime($data['sdate'])) ];
        }else if( !empty($data['edate']) ){
            $where['a.datemonth'] = ['<=', Date('Y-m',strtotime($data['edate']))  ];
        }else{
        	//默认匹配当前月份到之前的六个月
        	$etime = strtotime("-5 month");
		    $edate = date('Y-m',time());
		    $sdate = date('Y-m',$etime); 
        	$where['a.datemonth'] = ['between', [$sdate,$edate] ];
        }
        $companystorage = new Companystorage();
        $res = $companystorage->getcompanystarageList($where,$pagenum,$pagesize);
        $count = $companystorage->getcompanystarageListCount($where);
        // var_dump($res);
        // var_dump($count);
		//返回数组组装
		$result = [
			 	'data'=>$res,// 内容结果集
			 	'pageinfo'=>[
			 		'pagesize'=> $pagesize ,// 每页多少条记录
			 		'pagenum' => $pagenum ,//当前页码
			 		'count'   => $count // 符合条件总的记录数
			 	]
			] ;
		return return_format($result,0) ;
        //die;
	}

/*****************************************实时在线***************************************/	
	/**
	 * //z1实时在线->企业并发->获取在线的机构的列表
	 * @Author zzq
	 * @param  array $data      
	 * @return  array  
	 * 包含:总计的在线课堂数 总计的在线人数 在线机构信息(array:机构名  机构id 域名 在线课堂数 在线人数)          
	 */
	public function getOnlineCompanyList($data){
		#todo
		$pagenum  = $data['pagenum'] ;
		$pagesize = config('pagesize.admin_onlinecompanylist');//每页行数
		$where = [];
		//companyid要对于0
		$where['a.companyid'] = ['>','0'];
		//对companykeyword进行筛选
        if (!empty($data['companykeyword'])) {
            $where['a.companyid|b.companyfullname'] = ['like','%'.$data['companykeyword'].'%'];
        }
        //根据课的类型进行筛选
        if(empty($data['roomtype'])){
        	//默认为全部取出   
        	$data['roomtype'] = 3;
        }
        $ret = [1,2,3];
        if(!in_array($data['roomtype'],$ret)){
        	return return_format('', 50006, lang('roomtype_error'));
        }
        switch ($data['roomtype']) {
        	//小班课(一对一->0,一对多->3)
        	case 1:
        		$where['a.roomtype'] = ['IN',[0,3]];
        		break;
        	//大班课(直播课)
        	case 2:
        		$where['a.roomtype'] = ['EQ',10];
        		break;
        	//无限制        	
        	case 3:
        		$where['a.roomtype'] = ['IN',[0,3,10]];
        		break;
        	default:
       			$where['a.roomtype'] = ['IN',[0,3,10]];
        		break;
        }
        $roomusepoint = new Roomusepoint();
        //获取时时在线企业列表
        $resList = $roomusepoint->getOnlineCompanyList($where,$pagenum,$pagesize);
        //var_dump($resList);
        //获取时时在线企业列表总数目
        $resCount =$roomusepoint->getOnlineCompanyListCount($where);
        //var_dump($resCount);
        //获取时时在线企业列表的总课堂数和总在线人数
        $resNum = $roomusepoint->getOnlineComTotalDataByCondition($where);
        // var_dump($resNum);
        // die;
		//返回数组组装
		$result = [
			 	'data'=>$resList,// 内容结果集
			 	'pageinfo'=>[
			 		'pagesize'=> $pagesize ,// 每页多少条记录
			 		'pagenum' => $pagenum ,//当前页码
			 		'count'   => $resCount // 符合条件总的记录数
			 	],
			 	'totalRoomNum' => $resNum[0]['totalroomnum'],//总计的课堂数目
			 	'totalUserNum' => $resNum[0]['totalusernum'],//总计的在线人数
			] ;
		return return_format($result,0) ;
        //die;
	}

	/**
	 * //z2实时在线->企业并发->获取某个机构的在线教室的列表
	 * @Author zzq
	 * @param  $data   
	 * @return  array  
	 * 包含:教室名称  某个机构的所有教室的信息(array:教室名称 教室编号 教室类型 教室人数)          
	 */
	public function getOnlineRoomListByCom($data){
		$pagenum  = $data['pagenum'] ;
		$pagesize = config('pagesize.admin_onlineroomlistbycom');//每页行数
		$where = [];
		//判断companyid
		if(empty($data['companyid'])){
			return return_format('', 50007, lang('companyid_error'));
		}
		$where['a.companyid'] = ['EQ',$data['companyid']];
		//对companykeyword进行筛选
        if (!empty($data['roomkeyword'])) {
            $where['a.serial|c.roomname'] = ['like','%'.$data['roomkeyword'].'%'];
        }
        $roomusepoint = new Roomusepoint();
        //获取某个机构下的教室列表
        $resList = $roomusepoint->getOnlineRoomListByCom($where,$pagenum,$pagesize);
        foreach($resList as $k=>$v){
        	$resList[$k]['roomtypename'] = getRoomTypeName($v['roomtype']);
        }
        //获取该企业机构的名称
        $company = new Company();
        $resCom = $company->getCompanyInfoById($data['companyid']);
        //获取某个机构下的教室列表的数目
        $resCount =$roomusepoint->getOnlineRoomListByComCount($where);
		//返回数组组装
		$result = [
			 	'data'=>$resList,// 内容结果集
			 	'pageinfo'=>[
			 		'pagesize'=> $pagesize ,// 每页多少条记录
			 		'pagenum' => $pagenum ,//当前页码
			 		'count'   => $resCount , // 符合条件总的记录数
			 		'companyfullname' => $resCom['companyfullname'] //该企业的名称
			 	],
			] ;
		return return_format($result,0) ;
	}


	/**
	 * //z3在线教室列表
	 * @Author zzq
	 * @param  array  $data          
	 * @return  array  
	 * 包含:机构名  教室名称 教师编号 教室类型 在线人数          
	 */
	public function getOnlineRoomList($data){
		#todo
		$pagenum  = $data['pagenum'] ;
		$pagesize = config('pagesize.admin_onlineroomlist');//每页行数
		$where = [];
		//其中的companyid必须大于0
		$where['a.companyid'] = ['>',0];
		//对companyname进行筛选
        if (!empty($data['companyname'])) {
            $where['b.companyname'] = ['like','%'.$data['companyname'].'%'];
        }
		//对roomname进行筛选
        if (!empty($data['roomname'])) {
            $where['c.roomname'] = ['like','%'.$data['roomname'].'%'];
        }
        $roomusepoint = new Roomusepoint();
        //获取所有的教室列表
        $resList = $roomusepoint->getOnlineRoomList($where,$pagenum,$pagesize);
        foreach($resList as $k=>$v){
        	$resList[$k]['roomtypename'] = getRoomTypeName($v['roomtype']);
        }
        //获取某个机构下的教室列表的数目
        $resCount =$roomusepoint->getOnlineRoomListCount($where);
		//返回数组组装
		$result = [
			 	'data'=>$resList,// 内容结果集
			 	'pageinfo'=>[
			 		'pagesize'=> $pagesize ,// 每页多少条记录
			 		'pagenum' => $pagenum ,//当前页码
			 		'count'   => $resCount , // 符合条件总的记录数
			 	],
			] ;
		return return_format($result,0) ;
	}

	/**
	 * //新增在线人员列表
	 * @Author zzq 2018-08-03
	 * @param  $data    
	 * @return  array  
	 * 包含:机构名  教室名称  页码         
	 */
	public function getGuardOnlineUserList($data){

		$pagenum  = $data['pagenum'] ;
		$pagesize = config('pagesize.admin_onlineguarduserlist');//每页行数
		//获取搜索参数
		

		//在mongodb中筛选出需要报警的在线userid 2min内
		$logininfo = new Logininfo();
		$totime = time();
		$fromtime = $totime - config('mongodb.select_time');		
		$resOne = $logininfo->getNetworkInfo($fromtime,$totime);
		//var_dump($resOne);
		//1筛选出在线的用户
		//2网络有问题的用户
		$finalUseridArr = [];
		if($resOne){
			foreach($resOne as $k1 => $v1){
				$where = [];
				$obj = new Roomusepoint();
				$where['a.companyid'] = ['EQ',$v1['companyid']];
				$where['a.serial'] = ['EQ',$v1['serial']];
				$where['a.buddyid'] = ['EQ',$v1['myPeerId']];
				$resTwo = $obj->getOnlineUser($where);
				//表示在线
				if($resTwo){
					//var_dump($resTwo);
					if(!in_array($v1['myPeerId'],$finalUseridArr)){
						$finalUseridArr[] = $v1['myPeerId'];
					}
				} 
			}
		}else{
			//表示现在找不到最近时间内的网络信息
			$resList = [];
			$resCount = 0;
		}
		//die;
		// var_dump($finalUseridArr);
		// die;
		//最后重新获取报警人员的列表
		if($finalUseridArr){
			$_where = [];
			$_where['a.buddyid'] = ['IN',$finalUseridArr];
			if(!empty($data['companyname'])){
				$companyfullname =  $data['companyname'];
			}else{
				$companyfullname =  '';
			}
			if(!empty($data['roomname'])){
				$roomname = $data['roomname'];
			}else{
				$roomname = '';
			}
			$roomusepoint = new Roomusepoint();
			$resList =  $roomusepoint->getOnlineUserList($_where,$pagenum,$pagesize,$companyfullname,$roomname);
			foreach ($resList as $key => $value) {
				$resList[$key]['userrolename'] = getRoleNameByRoleId($value['usertype']);
			}
			$resCount =  $roomusepoint->getOnlineUserListCount($_where,$companyfullname,$roomname);
		}else{
			//表示没有不需要预警的在线人员
			$resList = [];
			$resCount = 0;
		}

		//返回数组组装
		$result = [
			 	'data'=>$resList,// 内容结果集
			 	'pageinfo'=>[
			 		'pagesize'=> $pagesize ,// 每页多少条记录
			 		'pagenum' => $pagenum ,//当前页码
			 		'count'   => $resCount , // 符合条件总的记录数
			 	],
			] ;
		return return_format($result,0) ;
		//die;

	}

	/**
	 * //z4在线教室详情
	 * @Author zzq
	 * @param  $data   
	 * @return  array  
	 * array包含:教室名称 教室编号 教室类型 开始时间 教师名称 在线学生数目        
	 */
	public function getOnlineRoomDetail($data){
		#todo操作room表和
		//(room)教室名称 教室编号 教室类型 开始时间 ||(roomusepoint)教师名称 在线学生数目
		//获取进出教室记录
		if(empty($data['roomid'])){
			return return_format('', 50008, lang('roomid_error'));
		}
		if(empty($data['companyid'])){
			return return_format('', 50007, lang('companyid_error'));
		}
		//判断该教室是否是在线教室(测试的时候注释)
		$_where = [];
		$_where['companyid'] = ['EQ',$data['companyid']];
		$_where['serial'] = ['EQ',$data['roomid']];
		$roomusepoint = new Roomusepoint();
		$flag = $roomusepoint->hasOnlineRoomBySerial($_where);
		if(!$flag){
			return return_format('', 50009, lang('room_not_online'));
		}
		//(room)教室名称 教室编号 教室类型 开始时间
		$roomid = $data['roomid'];
		$res = [];
		$room = new Room();
		$roomData = $room->getRoomDetail($roomid);
		$res['companyid'] = $data['companyid'];
		$res['roomname'] = $roomData['roomname'] ? $roomData['roomname'] : '';
		$res['serial'] = $roomData['serial'] ? $roomData['serial'] : '';
		$res['roomtypestr'] = $roomData['roomtype'] ? getRoomTypeName($roomData['roomtype']) : '';
		//查询useroompoint的老师的信息
		$roomusepoint = new Roomusepoint();
		$oneWhere = [];
		$oneWhere['companyid'] = ['EQ',$data['companyid']];
		$oneWhere['serial'] = ['EQ',$data['roomid']];
		$oneWhere['usertype'] = ['EQ','0'];
		$roomusepointInfo = $roomusepoint->getOnlineUserDetail($oneWhere);
		if($roomusepointInfo){
			$teacherBuddyid = $roomusepointInfo['buddyid'];
			$teacherIdentification = $roomusepointInfo['identification'];
		}
		//获取这节课的开始时间starttime(教师的logininfo)
		//查询logininfo中相应的数据starttime
		$logininfo = new Logininfo();
		$teacher = [];
		$teacher['companyid'] = ['EQ',$data['companyid']];
		$teacher['serial'] = ['EQ',$data['roomid']];
		$teacher['userid'] = ['EQ',$teacherBuddyid];
		$teacher['identification'] = ['EQ',$teacherIdentification];
		$teacher['userroleid'] = ['EQ','0'];
		$teacherLoginInfo = $logininfo->getOnlineUserInfo($teacher);
		$res['starttime'] = $teacherLoginInfo['starttime'];
		//(roomusepoint)教师名称
		if($roomusepointInfo){
			$res['teachername'] = $roomusepointInfo['buddyname'];
		}else{
			$res['teachername'] = '';
		}
		//(roomusepoint) 在线学生数目
		$roomusepoint = new Roomusepoint();
		$twoWhere = [];
		$twoWhere['companyid'] = ['EQ',$data['companyid']];
		$twoWhere['serial'] = ['EQ',$data['roomid']];
		$twoWhere['usertype'] = ['EQ','2'];
		$count = $roomusepoint->getOnlineRoomStudentCountBySerial($twoWhere);
		$res['studentcount'] = $count ? $count : 0;		
		return return_format($res,0) ;
	}

	/**
	 * //z5获取某个在线教室人员进出列表
	 * @Author zzq
	 * @param  $data array
	 * @return  array  
	 * array包含:(array:用户名 角色 状态 登录信息 服务器 时间记录)          
	 */
    public function getOnlineRoomRecordList($data){

		//获取进出教室记录
		if(empty($data['roomid'])){
			return return_format('', 50008, lang('roomid_error'));
		}
		if(empty($data['companyid'])){
			return return_format('', 50007, lang('companyid_error'));
		}
		//检测房间类型
		if( isset($data['roomtype']) && ($data['roomtype'] != '') ){
			$ret = ['0','3','10'];
			if(!in_array($data['roomtype'],$ret)){
				return return_format('', 50006, lang('roomtype_error'));
			}
			$where['roomtype'] = ['EQ',$data['roomtype']];
			$roomtype = $data['roomtype'];
		}else{
			return return_format('', 50006, lang('roomtype_error'));
		}
		//判断该教室是否是在线教室(测试的时候注释)
		$roomusepoint = new Roomusepoint();
		$_where = [];
		$_where['companyid'] = ['EQ',$data['companyid']];
		$_where['serial'] = ['EQ',$data['roomid']];
		$_where['roomtype'] = ['EQ',$data['roomtype']];
		$flag = $roomusepoint->hasOnlineRoomBySerial($_where);
		if(!$flag){
			return return_format('', 50009, lang('room_not_online'));
		}
		$roomid = $data['roomid'];
		$companyid = $data['companyid'];
		$pagenum  = $data['pagenum'] ;
		$pagesize = config('pagesize.admin_onlineuserlist');//每页行数
		//连表查出当前机构当前课堂的在线人员和离线人员	

		//查询useroompoint的老师的信息
		$roomusepoint = new Roomusepoint();
		$oneWhere = [];
		$oneWhere['companyid'] = ['EQ',$companyid];
		$oneWhere['serial'] = ['EQ',$roomid];
		$oneWhere['usertype'] = ['EQ','0'];
		$roomusepointInfo = $roomusepoint->getOnlineUserDetail($oneWhere);
		if($roomusepointInfo){
			$teacherBuddyid = $roomusepointInfo['buddyid'];
			$teacherIdentification = $roomusepointInfo['identification'];
		}else{
			//在线课堂老师不存在
			return return_format('', 50017, lang('teacher_notexist'));
			
		}
		//获取这节课的开始时间starttime(教师的logininfo)
		//查询logininfo中相应的数据starttime
		$logininfo = new Logininfo();
		$teacher = [];
		$teacher['companyid'] = ['EQ',$companyid];
		$teacher['serial'] = ['EQ',$roomid];
		$teacher['userid'] = ['EQ',$teacherBuddyid];
		$teacher['identification'] = ['EQ',$teacherIdentification];
		$teacher['userroleid'] = ['EQ','0'];
		$teacherLoginInfo = $logininfo->getOnlineUserInfo($teacher);
		//获取这节课的开始时间
		$starttime = $teacherLoginInfo['starttime'];
		// var_dump($starttime);
		// die;
	    $logininfo = new Logininfo();
	    $resList = $logininfo->getOnlineUserRecordList($companyid,$roomid,$pagenum,$pagesize,$starttime);
	    //对于设备情况 获取companyid serial userid最新的一个
        //设备类型：Windows PC 系统版本：Windows 10 浏览器版本：Chrome x.x.x CPU占有率：30%
        // var_dump($resList);
        // die;
        foreach($resList as $k => $v){
        	if(!$v['outtime']){
		        //对于在线人员 获取当前时间往前2分钟的设备数据,取出最近的
		        $endtime = time();
		        $fromtime = $endtime - config('mongodb.select_time');
        	}else{
		        //对于离线人员 获取离开时间前1分钟的设备数据,取出最近的
		        $endtime = strtotime($v['outtime']);
		        $fromtime = $endtime - config('mongodb.select_time');        		
        	}
		    //获取设备信息
		    $mongodb = new Logininfo();
		    $deviceInfo = $mongodb->getDeviceInfoByComSerialUser($companyid,$roomid,$v['userid'],$fromtime,$endtime,0);
		    //var_dump($deviceInfo);
		    // die;
		    if($deviceInfo){
		    	$resList[$k]['devicetype'] = $deviceInfo[0]['devicetype'];
		    	$resList[$k]['version'] = $deviceInfo[0]['version'];
		    	$resList[$k]['deviceName'] = $deviceInfo[0]['deviceName'];
		    	$resList[$k]['ip'] = $deviceInfo[0]['ip'];
		    	$resList[$k]['systemversion'] = $deviceInfo[0]['systemversion'];
		    	$resList[$k]['OSVersion'] = $deviceInfo[0]['OSVersion'];
		    }else{
		    	$resList[$k]['devicetype'] = "";
		    	$resList[$k]['version'] = "";
		    	$resList[$k]['deviceName'] = "";
		    	$resList[$k]['ip'] = "";
		    	$resList[$k]['systemversion'] = "";
		    	$resList[$k]['OSVersion'] = "";
		    }
		    //获取在线人员的网络上行的信息
		    if(!$v['outtime']){
			    $mongodb = new Logininfo();
			    //获取某个在线人员的网络上行的数据(可能有多条,取出最新的一条)
			    $upNetworkInfo = $mongodb->getNetworkInfoByComSerialUser($companyid,$roomid,$v['userid'],$v['userid'],$fromtime,$endtime,0,1);
			    //获取某个在线人员所有的下行(多条，取出各个其他用户的其他的一条)
			    $_allNetworkInfo = $mongodb->getNetworkInfoByComSerialUser($companyid,$roomid,$v['userid'],$v['userid'],$fromtime,$endtime,0,2);	
			    $allNetworkInfo = [];
			    $otherPeeridArr = [];
			    foreach($_allNetworkInfo as $kk => $vv){
			    	if(!in_array($vv['statistical']['0']['peerId'],$otherPeeridArr)){
			    		$otherPeeridArr[] = $vv['statistical']['0']['peerId']; 
			    		$allNetworkInfo[$kk] = $vv;
			    	}
			    }
			    // if($v['userid'] == '9840287b-5edc-72bf-de8a-5073312bc016'){
			    // 	var_dump($allNetworkInfo);
			    // 	die;			    	
			    // }
			    //设置网络情况存贮到数组
			    $allNetArr = []; 
			    //获取网络上行的情况输出
			    if($upNetworkInfo){
			    	$_ret = [];
			    	$resList[$k]['cpuOccupancy'] = $upNetworkInfo[0]['statistical']['0']['cpuOccupancy'];
			    	$resList[$k]['upNetworkVideoQuality'] = $upNetworkInfo[0]['statistical']['0']['video']['netquality'];
			    	$_ret[] = $upNetworkInfo[0]['statistical']['0']['video']['netquality'];
			    	$allNetArr[] = $upNetworkInfo[0]['statistical']['0']['video']['netquality'];
			    	$resList[$k]['upNetworkAudioQuality'] = $upNetworkInfo[0]['statistical']['0']['audio']['netquality'];
			    	$_ret[] = $upNetworkInfo[0]['statistical']['0']['audio']['netquality'];
			    	$allNetArr[] = $upNetworkInfo[0]['statistical']['0']['audio']['netquality'];
			    	$resList[$k]['upNetworkQualityName'] = getNetworkQuality(max($_ret));
			    }else{
			    	$resList[$k]['cpuOccupancy'] = "";
			    	$resList[$k]['upNetworkQualityName'] = "";
			    }
			    //var_dump($allNetArr);

			    //获取网络下行的情况输出
			    if($allNetworkInfo){
			    	$_let = [];
			    	foreach($allNetworkInfo as $k2 => $v2){
				    	$_let[] = $v2['statistical']['0']['video']['netquality'];
				    	$_let[] = $v2['statistical']['0']['audio']['netquality'];
				    	$allNetArr[] = $v2['statistical']['0']['video']['netquality'];
				    	$allNetArr[] = $v2['statistical']['0']['audio']['netquality'];
			    	}
			    	//所有的下行视音频存到数组中$_let中
			    	$resList[$k]['DownNetworkQualityStr'] = implode(',',$_let);
			    	//合并上下行信息判断报警信息
				     // if($v['userid'] == '9840287b-5edc-72bf-de8a-5073312bc016'){
				     // 	var_dump($_ret);
				     // 	var_dump($_let);
				     // 	die;			    	
				     // }
			  
			    }

			    if($allNetArr){
			    	$resList[$k]['allNetwork'] = $allNetArr;
			    	$resList[$k]['GuardNetwork'] = getNetworkQuality(max($allNetArr));
			    }else{
			    	$resList[$k]['allNetwork'] = [];
			    	$resList[$k]['GuardNetwork'] = '未知';
			    }
			    //评定报警信息

		    }else{
		    	//所有的离线人员不返回cpu和网络上行的信息
			    $resList[$k]['cpuOccupancy'] = "";
			    $resList[$k]['upNetworkVideoQuality'] = "";
			    $resList[$k]['upNetworkAudioQuality'] = ""; 
			    $resList[$k]['upNetworkQualityName'] = "";
			    $resList[$k]['DownNetworkQualityStr'] = '';
			    $resList[$k]['GuardNetwork'] = '';

		    }    
		    //获取其他信息
	    	$resList[$k]['rolename'] = getRoleNameByRoleId($v['userroleid']);
	    	//在离线状态
	    	if(!empty($v['outtime'])){
	    		//表示已经离线
	    		$resList[$k]['onlinestatus'] = '离开';
	    	}else{
	    		//表示在线
	    		$resList[$k]['onlinestatus'] = '上课中';
	    	}
	    	//计算离线人员的停留时间
	    	if( $v['entertime'] && $v['outtime'] ){
	    		//获取时间间隔
	    		$gaptime = strtotime($v['outtime']) - strtotime($v['entertime']);
	    		//友好的显示停留时间
	    		$resList[$k]['gaptime'] = showTime($gaptime);
	    	}else{
	    		//在线状态
	    		$resList[$k]['gaptime'] = showTime(0);
	    	}


        }
	    // var_dump($resList);
	    // die;
	    //获取数量
	    $resCount = $logininfo->getOnlineUserRecordListCount($companyid,$roomid,$starttime);
	    // var_dump($resCount);
	    // die;
		//返回数组组装
		$result = [
			 	'data'=>$resList,// 内容结果集
			 	'pageinfo'=>[
			 		'pagesize'=> $pagesize ,// 每页多少条记录
			 		'pagenum' => $pagenum ,//当前页码
			 		'count'   => $resCount , // 符合条件总的记录数
			 	],
			] ;
		return return_format($result,0) ;	    	
    }


	/**
	 * //获取某个在线教室的某个离在线人员的基本详情
	 * @Author zzq
	 * @param  $data   
	 * @return  array  
	 */
    public function getOnlineRoomRecordDetail($data){
		//构造查询条件
		$where = [];
		if(!empty($data['companyid'])){
			$where['companyid'] = ['EQ',$data['companyid']];
			$companyid = $data['companyid'];
		}else{
			//companyid_error
			return return_format('', 50007, lang('companyid_error'));
		}
		//检测房间号
		if(!empty($data['roomid'])){
			$where['serial'] = ['EQ',$data['roomid'] ];
			$roomid = $data['roomid'];
		}else{
			return return_format('', 50008, lang('roomid_error'));
		}
		//检测房间类型
		if( isset($data['roomtype']) && ($data['roomtype'] != '') ){
			$ret = ['0','3','10'];
			if(!in_array($data['roomtype'],$ret)){
				return return_format('', 50006, lang('roomtype_error'));
			}
			$where['roomtype'] = ['EQ',$data['roomtype']];
			$roomtype = $data['roomtype'];
		}else{
			return return_format('', 50006, lang('roomtype_error'));
		}
		$userid = $data['userid'];
		//判断该教室是否是在线教室(测试的时候注释)
		$_where = [];
		$_where['companyid'] = $companyid;
		$_where['serial'] = $roomid;
		$_where['roomtype'] = $roomtype;
		$_where['buddyid'] = $userid;
		$roomusepoint = new Roomusepoint();
		$flag = $roomusepoint->hasOnlineRoomBySerial($_where);
		// var_dump($flag);
		// die;
		if(!$flag){
			return return_format('', 50009, lang('room_not_online'));
		}
		//mongodb中获取设备信息
		$totime = time();
		$fromtime = $totime - config('mongodb.select_time');
		$logininfo = new Logininfo();
		$deviceInfo = $logininfo->getDeviceInfoByComSerialUser($companyid,$roomid,$userid,$fromtime,$totime,0);
		// var_dump($result);
		// die;
		$res = [];
		$res['usertype'] = $flag['usertype'];
		$res['usertypename'] = getRoleNameByRoleId($flag['usertype']);
	    if($deviceInfo){
	    	$res['devicetype'] = $deviceInfo[0]['devicetype'];
	    	$res['version'] = $deviceInfo[0]['version'];
	    	$res['deviceName'] = $deviceInfo[0]['deviceName'];
	    	$res['ip'] = $deviceInfo[0]['ip'];
	    	$res['systemversion'] = $deviceInfo[0]['systemversion'];
	    	$res['OSVersion'] = $deviceInfo[0]['OSVersion'];
	    	$res['sdkVersion'] = $deviceInfo[0]['sdkVersion'];
	    	$res['cpuArchitecture'] = $deviceInfo[0]['cpuArchitecture'];
	    }else{
	    	$res['devicetype'] = "";
	    	$res['version'] = "";
	    	$res['deviceName'] = "";
	    	$res['ip'] = "";
	    	$res['systemversion'] = "";
	    	$res['OSVersion'] = "";
	    	$res['sdkVersion'] = "";
	    	$res['cpuArchitecture'] = "";
	    }
		//获取当前userid的在logininfo的用户名
		$whereOne = [];
		$whereOne['companyid'] = ['EQ',$companyid];
		$whereOne['serial'] = ['EQ',$roomid];
		$whereOne['userid'] = ['EQ',$userid];
		$whereOne['identification'] = ['EQ',$flag['identification']];
		$whereOne['roomtype'] = ['EQ',$roomtype];
		// var_dump($whereOne);
		// die;
		$_res = $logininfo->getOnlineUserInfo($whereOne);
		// var_dump($_res);
		// die;
		$res['companyid'] = $companyid;
		$res['serial'] = $roomid;
		$res['userid'] = $userid;
		if($_res){
			$res['username'] = $_res['username'];
		}else{
			$res['username'] = '';
		}
		return return_format($res,0) ;		    		
    }

	/**
	 * //获取某个在线教室的某个离在线人员的网络上行的情况
	 * @Author zzq
	 * @param  $data   
	 * @return  array  
	 */
    public function getUpNetworkByOnOrOffline($data){
		if(!empty($data['companyid'])){
			$companyid = $data['companyid'];
		}else{
			//companyid_error
			return return_format('', 50007, lang('companyid_error'));
		}
		//检测房间号
		if(!empty($data['roomid'])){
			$roomid = $data['roomid'];
		}else{
			return return_format('', 50008, lang('roomid_error'));
		}
		//检测房间类型
		if( isset($data['roomtype']) && ($data['roomtype'] != '') ){
			$ret = ['0','3','10'];
			if(!in_array($data['roomtype'],$ret)){
				return return_format('', 50006, lang('roomtype_error'));
			}
			$roomtype = $data['roomtype'];
			//判断这个房间是否在roomusepoint中
			#todo
		}else{
			return return_format('', 50006, lang('roomtype_error'));
		}
		$userid = $data['userid'];
	    $mongodb = new Logininfo();
	    $endtime = time();
		$fromtime = $endtime - 30*60;
	    $result = $mongodb->getNetworkInfoByComSerialUser($companyid,$roomid,$userid,$userid,$fromtime,$endtime,0,1);
	    $res = [];
	    // var_dump($res);
	    // die;
	    if($result){
		    foreach ($result as $k => $v) {
		    	//var_dump($v);
		    	// die;
		    	$res[$k]['datetime'] = Date('Y-m-d H:i:s',$v['statistical']['time']);
		    	$res[$k]['cpuOccupancy'] = $v['statistical']['0']['cpuOccupancy'];
		    	$res[$k]['video']['bitsPerSecond'] = $v['statistical']['0']['video']['bitsPerSecond'];
		    	$res[$k]['video']['packetsLost'] = $v['statistical']['0']['video']['packetsLost'];
		    	$res[$k]['video']['currentDelay'] = $v['statistical']['0']['video']['currentDelay'];
		    	$res[$k]['video']['netquality'] = $v['statistical']['0']['video']['netquality'];
		    	$res[$k]['audio']['bitsPerSecond'] = $v['statistical']['0']['audio']['bitsPerSecond'];
		    	$res[$k]['audio']['packetsLost'] = $v['statistical']['0']['audio']['packetsLost'];
		    	$res[$k]['audio']['currentDelay'] = $v['statistical']['0']['audio']['currentDelay'];
		    	$res[$k]['audio']['netquality'] = $v['statistical']['0']['audio']['netquality'];
		    }
		    $_data['NowCpuOccupancy'] = $result[0]['statistical']['0']['cpuOccupancy'];	    	
	    }else{
	    	$_data['data'] = [];
	    	$_data['NowCpuOccupancy'] = '';
	    }
	    $_data['data'] = $res;
	    $_data['companyid'] = $companyid;
	    $_data['serial'] = $roomid;
	    $_data['userid'] = $userid;
	    return return_format($_data,0) ;    	
    }

	/**
	 * //获取某个在线教室的某个在线人员的网络下行的情况
	 * @Author zzq
	 * @param  $data   
	 * @return  array  
	 */
    public function getDownNetworkByOnOrOffline($data){
		if(!empty($data['companyid'])){
			$companyid = $data['companyid'];
		}else{
			//companyid_error
			return return_format('', 50007, lang('companyid_error'));
		}
		//检测房间号
		if(!empty($data['roomid'])){
			$roomid = $data['roomid'];
		}else{
			return return_format('', 50008, lang('roomid_error'));
		}
		//检测房间类型
		if( isset($data['roomtype']) && ($data['roomtype'] != '') ){
			$ret = ['0','3','10'];
			if(!in_array($data['roomtype'],$ret)){
				return return_format('', 50006, lang('roomtype_error'));
			}
			$roomtype = $data['roomtype'];
			//判断这个房间是否在roomusepoint中,否则不是在线人员
			#todo
		}else{
			return return_format('', 50006, lang('roomtype_error'));
		}
		$userid = $data['userid'];
		//1 查roomusepoint表查取所有的下行的其他的人员
		$roomusepoint = new Roomusepoint();
		$whereOne = [];
		$whereOne['a.companyid'] = ['EQ',$companyid];
		$whereOne['a.serial'] = ['EQ',$roomid];
		$whereOne['a.buddyid'] = ['NEQ',$userid];
		$otherUseridRes = $roomusepoint->getOnlineOtherUserByUserid($whereOne);
		// var_dump($otherUseridRes);
		// die;
	    $endtime = time();
		$fromtime = $endtime - 30*60;
		$ret=[];
		if($otherUseridRes){
			foreach($otherUseridRes as $k => $v){
				$_ret = [];
				$logininfo = new Logininfo();
				$downNetworkRes = $logininfo->getNetworkInfoByComSerialUser($companyid,$roomid,$userid,$v['buddyid'],$fromtime,$endtime,0,1);
				// var_dump($downNetworkRes);
				// die;
				foreach($downNetworkRes as $k1 => $v1){
				    $_ret[$k1]['datetime'] = Date('Y-m-d H:i:s',$v1['statistical']['time']);
				    $_ret[$k1]['userid'] = $v1['statistical']['0']['peerId'];
				    $_ret[$k1]['usertype'] = $v['usertype'];
				    $_ret[$k1]['usertypename'] = getRoleNameByRoleId($v['usertype']);
				    $_ret[$k1]['username'] = $v['username'];
			    	$_ret[$k1]['cpuOccupancy'] = $v1['statistical']['0']['cpuOccupancy'];
			    	$_ret[$k1]['video']['bitsPerSecond'] = $v1['statistical']['0']['video']['bitsPerSecond'];
			    	$_ret[$k1]['video']['packetsLost'] = $v1['statistical']['0']['video']['packetsLost'];
			    	$_ret[$k1]['video']['currentDelay'] = $v1['statistical']['0']['video']['currentDelay'];
			    	$_ret[$k1]['video']['netquality'] = $v1['statistical']['0']['video']['netquality'];
			    	$_ret[$k1]['audio']['bitsPerSecond'] = $v1['statistical']['0']['audio']['bitsPerSecond'];
			    	$_ret[$k1]['audio']['packetsLost'] = $v1['statistical']['0']['audio']['packetsLost'];
			    	$_ret[$k1]['audio']['currentDelay'] = $v1['statistical']['0']['audio']['currentDelay'];
			    	$_ret[$k1]['audio']['netquality'] = $v1['statistical']['0']['audio']['netquality'];
				}
				// var_dump($_ret);
				// die;
				if($_ret){
					$ret[$k] = $_ret;
				}
				
				// var_dump($ret);
				// die;
			}
		}

		// var_dump($ret);
		// die;
		//2 查出现在的userid的这些人的,循环查询
	    return return_format($ret,0) ;    	
    }
/*****************************************实时在线***************************************/	
/*****************************************统计查询***************************************/	

	/**
	 * //统计查询图表
	 * @Author zzq
	 * @param  companykey   企业名称或者企业id(搜索)      
	 * @return  array  
	 * array包含:日期 机构名称 小班课一对一教室数量 小班课一对多教室数量 大班课教室数量 小班课一对一在线人数 小班课一对多在线人数 大班课在线人数           
	 */
	public function getAnalysisChart($data){
		//获取where条件
		$where = $this->getWhere($data);
		//判断显示类型
		if(empty($data['showtype'])){
			$showType = 1;
		}else{
			$showType = $data['showtype'];
		}
		$logininfo = new Logininfo();
		$arr = explode(',',$data['roomtype']);
		//$needRoomTypeArr = explode(',',);
		//表示课堂数
		if($showType == 1){
			//表示统计课堂数的曲线
        	$resList = $logininfo->getAnalysisChartByDateRoomSum($where);
        	// var_dump($resList);
        	// die;
        	//遍历获取每天的三种课堂的数目
        	$res = [];
        	if($resList){
				foreach($resList as $k => $v){
					$res[$k]['historydate'] = $v['historydate'];
					$roomtypearr = [];
					$roomnumarr = [];
					$roomtypearr = explode(',',$resList[$k]['roomtypestr']);
					$roomnumarr = explode(',',$resList[$k]['roomnumstr']);
					//判断房间的类型
					//一对一的类型
					//房间数
					if(in_array(0,$arr)){
						if(in_array(0,$roomtypearr)){
							//如果1对1教室存在的话
							$onetoonekey = array_search(0,$roomtypearr);
							$res[$k]['onotoone_roomnum'] = $roomnumarr[$onetoonekey];
						}else{
							$res[$k]['onotoone_roomnum'] = '0';
						}
					}
					//一对多的类型
					//房间数
					if(in_array(3,$arr)){
						if(in_array(3,$roomtypearr)){
							//如果1对多教室存在的话
							$onetomorekey = array_search(3,$roomtypearr);
							$res[$k]['onotomore_roomnum'] = $roomnumarr[$onetomorekey];
						}else{
							$res[$k]['onotomore_roomnum'] = '0';
						}
				    }
					//直播课的类型
					//房间数
					if(in_array(10,$arr)){
						if(in_array(10,$roomtypearr)){
							//如果直播教室存在的话
							$livekey = array_search(10,$roomtypearr);
							$res[$k]['live_roomnum'] = $roomnumarr[$livekey];	
						}else{
							$res[$k]['live_roomnum'] = '0';
						}
					}
				}
        	}

		}elseif($showType == 2){
			//表示统计人数的曲线
			$resList = $logininfo->getAnalysisChartByDateUserSum($where);
        	$res = [];
        	if($resList){
				foreach($resList as $k => $v){
					$res[$k]['historydate'] = $v['historydate'];
					$roomtypearr = [];
					$usernumarr = [];
					$roomtypearr = explode(',',$resList[$k]['roomtypestr']);
					$usernumarr = explode(',',$resList[$k]['usernumstr']);
					//判断房间的类型
					//一对一的类型
					//房间数
					if(in_array(0,$arr)){
						if(in_array(0,$roomtypearr)){
							//如果1对1教室存在的话
							$onetoonekey = array_search(0,$roomtypearr);
							$res[$k]['onotoone_usernum'] = $usernumarr[$onetoonekey];
						}else{
							$res[$k]['onotoone_usernum'] = '0';
						}
					}

					//一对多的类型
					//房间数
					if(in_array(3,$arr)){
						if(in_array(3,$roomtypearr)){
							//如果1对多教室存在的话
							$onetomorekey = array_search(3,$roomtypearr);
							$res[$k]['onotomore_usernum'] = $usernumarr[$onetomorekey];
						}else{
							$res[$k]['onotomore_usernum'] = '0';
						}
					}
					//直播课的类型
					//房间数
					if(in_array(10,$arr)){
						if(in_array(10,$roomtypearr)){
							//如果直播教室存在的话
							$livekey = array_search(10,$roomtypearr);
							$res[$k]['live_usernum'] = $usernumarr[$livekey];	
						}else{
							$res[$k]['live_usernum'] = '0';
						}
					}
				}        		
        	}
		}
		//返回数组组装
		$result = [
			 	'data'=>$res,// 内容结果集
			] ;
		return return_format($result,0) ;
	}	

	/**
	 * //构造where条件
	 * @Author zzq     
	 * @param  $data     
	 * @return  array            
	 */
	public function getWhere($data){
		$where = [];
		//图表统计选择的
		if( isset($data['roomtype']) ){
			if($data['roomtype'] != ''){
				$arr = explode(',',$data['roomtype']);
				$where['a.roomtype'] = ['IN',$arr];
			}else{
				$where['a.roomtype'] = ['LT','0'];
			}
		}else{
			$where['a.roomtype'] = ['LT','0'];
		}
		
		//其中的companyid必须大于0
		$where['a.companyid'] = ['>',0];
		//对companykeyword进行筛选( `logininfo`.`company` LIKE '%$companykeyword%' OR `company`.`companyfullname` LIKE '%$companykeyword%' )
		//获取按照企业名称匹配的时候获取的企业的id的集合
        if (!empty($data['companykeyword'])) {
		    $company = new Company();
		    $companyid = $company->getCompanyIdsbyKeyword($data['companykeyword']);
			$where['a.companyid'] = ['EQ',$companyid] ;
        }      
        if(!empty($data['edate'])){
	        //获取截止日期
	        $ConvertEndtime = strtotime($data['edate']);
	        //截止日期加上一天，表示包含当天的数据
	        $ConvertEndtime = $ConvertEndtime + 60*60*24;

	        $ConvertEnddate = Date('Y-m-d H:i:s',$ConvertEndtime);
        }
        if(empty($data['edate']) && !empty($data['sdate'])){
            //大于某个时间
            $where['a.starttime'] = ['>= time', $data['sdate']];
        }elseif(!empty($data['edate']) && empty($data['sdate'])){
            //小于某个时间
            $where['a.starttime'] = ['<= time', $ConvertEnddate];
        }elseif(!empty($data['sdate']) && !empty($data['edate'])){
            $where['a.starttime'] = ['between time', [$data['sdate'],$ConvertEnddate] ];
        }elseif(empty($data['sdate']) && empty($data['edate'])){
        	$etime = time();
        	$edate = date('Y-m-d',$etime);
        	$stime = strtotime($edate) - 29*60*60*24;
        	$sdate = Date('Y-m-d',$stime);
	        $ConvertEndtime = $etime + 60*60*24;
	        $ConvertEnddate = Date('Y-m-d',$ConvertEndtime);
        	$where['a.starttime'] = ['between time', [$sdate,$ConvertEnddate] ];
        }
        //2018-08-08添加where条件
        //userroleid =0 教师 
        //$where['a.userroleid'] = ['EQ','0'];
        //$where['a.outtime']  = ['exp','is not null'];
        //entertime outtime starttime endtime 都有值
        //再按照 starttime分组
        return $where;
	}
	
	/************************************20180730新增***************************************/
	//统计查询->获取某企业某日期课堂数与人员数
	/**
	 * 
	 * @Author zzq
	 * @date 2018-07-30
	 * @param   $data            
	 * @return  array  
	 * array包含:日期 机构名称 小班课一对一教室数量 小班课一对多教室数量 大班课教室数量 小班课一对一在线人数 小班课一对多在线人数 大班课在线人数          
	 */	
	public function getAnalysisSumByComAndDate($data){
		
	
		$where = [];
		//判断date的格式
		if(!empty($data['date'])){
			if(checkDateFormat($data['date'])){
				$where['date'] = $data['date'];
			}else{
				return return_format('', 50014, lang('datetime_error'));
			}
		}else{
			return return_format('', 50014, lang('datetime_error'));
		}
		if(!empty($data['companyid'])){
			$where['companyid'] = ['EQ',$data['companyid']];
			//查出该机构的名称
			$company = new Company();
			$comInfo = $company->getCompanyInfoById($data['companyid']);
			$companyfullname = $comInfo['companyfullname'];
		}else{
			$companyfullname = "全部机构";
		}
		$where['roomtype'] = ['IN',['0','3','10'] ];
		$obj = new Logininfo();
		// var_dump($where);
		// die;
		$roomRes = $obj->getAnalysisRoomSumByComAndDate($where);
		//var_dump($roomRes);
		//die;
		$userRes = $obj->getAnalysisUserSumByComAndDate($where);
		//var_dump($userRes);
		//die;
		//然后转成需要的格式
		$result = [];
		$result['date'] = $data['date'];
		$result['companyfullname'] = $companyfullname;
		//课堂数
		if($roomRes){
			$roomtypestr=$roomRes[0]['roomtypestr'];
			$roomtypearr = explode(',',$roomtypestr);
			$roomnumstr=$roomRes[0]['roomnumstr'];
			$roomnumarr=explode(',',$roomnumstr);
			if(in_array(0,$roomtypearr)){
				//如果1对1教室存在的话
				$onetomorekey = array_search(0,$roomtypearr);
				$result['onotoone_roomnum'] = $roomnumarr[$onetomorekey];
			}else{
				$result['onotoone_roomnum'] = '0';
			}
			if(in_array(3,$roomtypearr)){
				//如果1对多教室存在的话
				$onetomorekey = array_search(3,$roomtypearr);
				$result['onotomore_roomnum'] = $roomnumarr[$onetomorekey];
			}else{
				$result['onotomore_roomnum'] = '0';
			}
			if(in_array(10,$roomtypearr)){
				//如果直播教室存在的话
				$onetomorekey = array_search(10,$roomtypearr);
				$result['live_roomnum'] = $roomnumarr[$onetomorekey];
			}else{
				$result['live_roomnum'] = '0';
			}			
		}else{
			$result['onotoone_roomnum'] = '0';
			$result['onotomore_roomnum'] = '0';
			$result['live_roomnum'] = '0';
		}
		//人数
		if($userRes){
			$roomtypestr=$userRes[0]['roomtypestr'];
			$roomtypearr = explode(',',$roomtypestr);
			$usernumstr=$userRes[0]['usernumstr'];
			$usernumarr=explode(',',$usernumstr);
			if(in_array(0,$roomtypearr)){
				//如果1对1教室存在的话
				$onetomorekey = array_search(0,$roomtypearr);
				$result['onotoone_usernum'] = $usernumarr[$onetomorekey];
			}else{
				$result['onotoone_usernum'] = '0';
			}
			if(in_array(3,$roomtypearr)){
				//如果1对多教室存在的话
				$onetomorekey = array_search(3,$roomtypearr);
				$result['onotomore_usernum'] = $usernumarr[$onetomorekey];
			}else{
				$result['onotomore_usernum'] = '0';
			}
			if(in_array(10,$roomtypearr)){
				//如果直播教室存在的话
				$onetomorekey = array_search(10,$roomtypearr);
				$result['live_usernum'] = $usernumarr[$onetomorekey];
			}else{
				$result['live_usernum'] = '0';
			}			
		}else{
			$result['onotoone_usernum'] = '0';
			$result['onotomore_usernum'] = '0';
			$result['live_usernum'] = '0';
		}		
		// var_dump($result);
		// die;
		return return_format($result,0) ;				
	}	

	//统计查询->获取某企业某日期课堂列表
	/**
	 * 
	 * @Author zzq
	 * @date 2018-07-30
	 * @param   $data            
	 * @return  array         
	 */	
	public function getHisRoomListByComAndDate($data){
		$pagenum = $data['pagenum'];
		$pagesize = config('pagesize.admin_roomlistbycomanddate');//每页行数
		//校验参数
		//构造查询条件
		$where = [];
		if(!empty($data['companyid'])){
			$where['companyid'] = ['EQ',$data['companyid']];
		}
		if(!empty($data['roomid'])){
			$where['serial'] = ['like','%'.$data['roomid'].'%' ];
		}
		if( isset($data['roomtype']) ){
			if($data['roomtype'] != ''){
				$ret = ['0','3','10'];
				if(!in_array($data['roomtype'],$ret)){
					return return_format('', 50006, lang('roomtype_error'));
				}
				$where['roomtype'] = ['EQ',$data['roomtype']];
			}else{
				$where['roomtype'] = ['IN',['0','3','10']];
			}
		}else{
			$where['roomtype'] = ['IN',['0','3','10']];
		}
		//检验日期格式
		if(!empty($data['date'])){
			if(checkDateFormat($data['date'])){
				$date = $data['date'];
			}else{
				return return_format('', 50014, lang('datetime_error'));
			}
		}else{
			return return_format('', 50014, lang('datetime_error'));
		}			
		//查看当前日期内符合条件的serialArr
		$logininfo = new Logininfo();
		//获取(全部)某机构某天的课堂列表(存在同一个serial更改roomtype的情况)
		$resList = $logininfo->getSerialListByComAndDate($where,$pagenum,$pagesize,$date);
		//die;
		$resCount = $logininfo->getSerialListCountByComAndDate($where,$date);
		//var_dump($resList);
		// var_dump($resCount);
		//die;
		foreach($resList as $k => $v){
			$resList[$k]['roomtypename'] = getRoomTypeName($v['roomtype']);
			$_where = [];
			$_where['serial'] = ['EQ',$v['serial']];
			$room = new Room();
			$ret = $room->getRoomDetailBySerial($_where);
			if($ret){
				$resList[$k]['roomname'] = $ret['roomname'];
				$resList[$k]['companyfullname'] = $ret['companyfullname'];			
			}else{
				$resList[$k]['roomname'] = '';
				$resList[$k]['companyfullname'] = '';					
			}

		}
		//返回数组组装
		$result = [
			 	'data'=>$resList,// 内容结果集
			 	'pageinfo'=>[
			 		'pagesize'=> $pagesize ,// 每页多少条记录
			 		'pagenum' => $pagenum ,//当前页码
			 		'count'   => $resCount , // 符合条件总的记录数
			 	],
			] ;
		return return_format($result,0) ;					
	}	

	//统计查询->获取某企业某日期某课堂的详情
	/**
	 * 
	 * @Author zzq
	 * @date 2018-07-30
	 * @param   $data            
	 * @return  array         
	 */	
	public function getHisRoomDetailByComAndDateAndRoom($data){
		//构造查询条件
		$where = [];
		if(!empty($data['companyid'])){
			$where['companyid'] = ['EQ',$data['companyid']];
			$companyid = $data['companyid'];
		}else{
			//companyid_error
			return return_format('', 50007, lang('companyid_error'));
		}
		if(!empty($data['roomid'])){
			$where['serial'] = ['EQ',$data['roomid'] ];
			$roomid = $data['roomid'];
		}else{
			return return_format('', 50008, lang('roomid_error'));
		}
		if( isset($data['roomtype']) && ($data['roomtype'] != '') ){
			$ret = ['0','3','10'];
			if(!in_array($data['roomtype'],$ret)){
				return return_format('', 50006, lang('roomtype_error'));
			}
			$where['roomtype'] = ['EQ',$data['roomtype']];
			$roomtype = $data['roomtype'];
		}else{
			return return_format('', 50006, lang('roomtype_error'));
		}
		//检验日期格式
		if(!empty($data['date'])){
			if(checkDateFormat($data['date'])){
				$date = $data['date'];
			}else{
				return return_format('', 50014, lang('datetime_error'));
			}
		}else{
			return return_format('', 50014, lang('datetime_error'));
		}
		//检验开始结束时间	
		if(empty($data['starttime'])){
			return return_format('', 50019, lang('datetime_error'));
		}
		if(empty($data['endtime'])){
			return return_format('', 50019, lang('datetime_error'));
		}
		$starttime = $data['starttime'];
		$endtime = $data['endtime'];
		$logininfo = new Logininfo();
		$_data = [];
		$_data['companyid'] = ['EQ',$companyid];
		$_data['serial'] = ['EQ',$roomid];
		$_data['userroleid'] = ['EQ','0'];
		$_data['roomtype'] = ['EQ',$roomtype];
		$_data['starttime'] = ['EQ',$starttime];
		$_data['endtime'] = ['EQ',$endtime];
		$res = $logininfo->getHisClass($_data);
		// var_dump($res);
		// die;
		if(!$res){
			//历史课堂不存在
			return return_format('', 50018, lang('hisclass_notexist'));
		}
		$res['roomtype'] = $roomtype;
		$res['roomtypename'] = getRoomTypeName($roomtype);
		//获取教室名称
		$room = new Room();
		$roomData = $room->getRoomDetail($roomid);
		$res['roomname'] = $roomData['roomname'];
		//获取机构名称
		$company = new company();
		$companyRes = $company->getCompanyInfoById($companyid);
		if($companyRes){
			$res['companyfullname'] = $companyRes['companyfullname'];
		}else{
			$res['companyfullname'] = '';
		}
		$res['starttime'] = $starttime;
	    $res['endtime'] = $endtime;		
	    $_where = [];
	    $_where['companyid'] = $companyid;
	    $_where['serial'] = $roomid;
	    $_where['roomtype'] = $roomtype;
	    $_where['starttime'] = $starttime;
	    $_where['endtime'] = $endtime;
	    $obj = new Logininfo();
	    $currency = $obj->getUserCurrencyByComAndDateAndRoom($_where);
	    if($currency){
	    	$res['currency'] = $currency;
	    }else{
	    	$res['currency'] = 0;
	    }

		// var_dump($currency);
		// die;
		return return_format($res,0) ;
	}

	//统计查询->获取某企业某日期某课堂的人员进出列表
	/**
	 * 
	 * @Author zzq
	 * @date 2018-08-01
	 * @param   $data            
	 * @return  array         
	 */	
	public function getHisUserListByComAndDateAndRoom($data){
		$pagenum = $data['pagenum'];
		$pagesize = config('pagesize.admin_roomuserlistbycomanddate');//每页行数
		//构造查询条件
		//构造查询条件
		$where = [];
		if(!empty($data['companyid'])){
			$companyid = $data['companyid'];
		}else{
			//companyid_error
			return return_format('', 50007, lang('companyid_error'));
		}
		if(!empty($data['roomid'])){
			$roomid = $data['roomid'];
		}else{
			return return_format('', 50008, lang('roomid_error'));
		}
		if( isset($data['roomtype']) && ($data['roomtype'] != '') ){
			$ret = ['0','3','10'];
			if(!in_array($data['roomtype'],$ret)){
				return return_format('', 50006, lang('roomtype_error'));
			}
			$roomtype = $data['roomtype'];
		}else{
			return return_format('', 50006, lang('roomtype_error'));
		}
		//检验日期格式
		if(!empty($data['date'])){
			if(checkDateFormat($data['date'])){
				$date = $data['date'];
			}else{
				return return_format('', 50014, lang('datetime_error'));
			}
		}else{
			return return_format('', 50014, lang('datetime_error'));
		}
		//检验开始结束时间	
		if(empty($data['starttime'])){
			return return_format('', 50019, lang('datetime_error'));
		}
		if(empty($data['endtime'])){
			return return_format('', 50019, lang('datetime_error'));
		}
		$starttime = $data['starttime'];
		$endtime = $data['endtime'];

		$logininfo = new Logininfo();
		$_data = [];
		$_data['companyid'] = ['EQ',$companyid];
		$_data['serial'] = ['EQ',$roomid];
		$_data['roomtype'] = ['EQ',$roomtype];
		$_data['starttime'] = ['EQ',$starttime];
		$_data['endtime'] = ['EQ',$endtime];
		$res = $logininfo->getHisClass($_data);
		// var_dump($res);
		// die;
		if(!$res){
			//历史课堂不存在
			return return_format('', 50018, lang('hisclass_notexist'));
		}
		$where = [];
		$where['companyid'] = $companyid;
		$where['serial'] = $roomid;
		$where['roomtype'] = $roomtype;
		$where['starttime'] = $starttime;
		$where['endtime'] = $endtime;
		$logininfo = new Logininfo();
		$resList = $logininfo->getHisUserListByComAndDateAndRoom($where,$pagenum,$pagesize);
		// var_dump($resList);
		// die;
		if($resList){
			foreach($resList as $k => $v){

			    //获取设备信息
			    $mongodb = new Logininfo();
			    $deviceInfo = $mongodb->getDeviceInfoByComSerialUser($companyid,$roomid,$v['userid'],strtotime($v['entertime']),strtotime($v['outtime']),0);
			    // var_dump($deviceInfo);
			    // die;
			    if($deviceInfo){
			    	$resList[$k]['devicetype'] = $deviceInfo[0]['devicetype'];
			    	$resList[$k]['deviceName'] = $deviceInfo[0]['deviceName'];
			    	$resList[$k]['ip'] = $deviceInfo[0]['ip'];
			    }else{
			    	$resList[$k]['devicetype'] = "";
			    	$resList[$k]['deviceName'] = "";
			    	$resList[$k]['ip'] = "";
			    }
			    //网络上行报警时间段集合
				$guardtimeArr = [];
				$mongodb = new Logininfo();
			    $upNetworkInfo = $mongodb->getNetworkInfoByComSerialUser($companyid,$roomid,$v['userid'],$v['userid'],strtotime($v['entertime']),strtotime($v['outtime']),0,1);	
			    // var_dump($upNetworkInfo);
			    // die;	
			    if($upNetworkInfo){
			    	foreach($upNetworkInfo as $kk => $vv){
			    		$guardtimeArr[] = Date('Y-m-d H:i:s',$vv['statistical']['time']);
			    	}
			    }	
				//人员角色
				$resList[$k]['userrolename'] = getRoleNameByRoleId($v['userroleid']);
				//在线时长
		    	//计算离线人员的停留时间
		    	if( $v['starttime'] && $v['endtime'] ){
		    		//获取时间间隔
		    		$gaptime = strtotime($v['endtime']) - strtotime($v['starttime']);
		    		//友好的显示停留时间
		    		$resList[$k]['gaptime'] = showTime($gaptime);
		    	}else{
		    		//在线状态
		    		$resList[$k]['gaptime'] = showTime(0);
		    	}
		    	$resList[$k]['guardtimeArr'] = $guardtimeArr;		
			}			
		}
		// var_dump($resList);
		// die;
		$resCount = $logininfo->getHisUserListCountByComAndDateAndRoom($where);
		//返回数组组装
		$result = [
			 	'data'=>$resList,// 内容结果集
			 	'pageinfo'=>[
			 		'pagesize'=> $pagesize ,// 每页多少条记录
			 		'pagenum' => $pagenum ,//当前页码
			 		'count'   => $resCount , // 符合条件总的记录数
			 	],
			] ;
		return return_format($result,0) ;
	}

	//获取设备情况
	/**
	 * 
	 * @Author zzq
	 * @date 2018-08-09
	 * @param   $data            
	 * @return  array         
	 */	
	public function getHisUserDeviceByComDateRoomUser($data){
		//构造查询条件
		$where = [];
		if(!empty($data['companyid'])){
			$companyid = $data['companyid'];
		}else{
			//companyid_error
			return return_format('', 50007, lang('companyid_error'));
		}
		if(!empty($data['roomid'])){
			$roomid = $data['roomid'];
		}else{
			return return_format('', 50008, lang('roomid_error'));
		}
		if( isset($data['roomtype']) && ($data['roomtype'] != '') ){
			$ret = ['0','3','10'];
			if(!in_array($data['roomtype'],$ret)){
				return return_format('', 50006, lang('roomtype_error'));
			}
			$roomtype = $data['roomtype'];
		}else{
			return return_format('', 50006, lang('roomtype_error'));
		}
		//检验日期格式
		if(!empty($data['date'])){
			if(checkDateFormat($data['date'])){
				$date = $data['date'];
			}else{
				return return_format('', 50014, lang('datetime_error'));
			}
		}else{
			return return_format('', 50014, lang('datetime_error'));
		}
		//检验开始结束时间	
		if(empty($data['starttime'])){
			return return_format('', 50019, lang('datetime_error'));
		}
		if(empty($data['endtime'])){
			return return_format('', 50019, lang('datetime_error'));
		}
		if(empty($data['entertime'])){
			return return_format('', 50019, lang('datetime_error'));
		}
		if(empty($data['outtime'])){
			return return_format('', 50019, lang('datetime_error'));
		}
		//判断userid
		if(empty($data['userid'])){
			return return_format('', 50016, lang('userid_error'));
		}
		$userid = $data['userid'];		
		$starttime = $data['starttime'];
		$endtime = $data['endtime'];
		$entertime = $data['entertime'];
		$outtime = $data['outtime'];
		$logininfo = new Logininfo();
		$_data = [];
		$_data['companyid'] = ['EQ',$companyid];
		$_data['serial'] = ['EQ',$roomid];
		$_data['userroleid'] = ['EQ','0'];
		$_data['roomtype'] = ['EQ',$roomtype];
		$_data['starttime'] = ['EQ',$starttime];
		$_data['endtime'] = ['EQ',$endtime];
		// var_dump($_data);
		// die;
		$res = $logininfo->getHisClass($_data);
		// var_dump($res);
		// die;
		if(!$res){
			//历史课堂不存在
			return return_format('', 50018, lang('hisclass_notexist'));
		}

		//查看这个用户是否上过这个课
		$logininfo = new Logininfo();
		//查的是学员的数目
		$where = [];
		$where['companyid'] = $companyid;
		$where['userid'] = $userid;
		$where['serial'] = $roomid;
		$where['roomtype'] = $roomtype;
		$where['entertime'] = $entertime;
		$where['outtime'] = $outtime;
		$userInfo = $logininfo->getHisUserToHisRoom($where);
		if(!$userInfo){
			//这个用户没有上过该课
			return return_format('', 50020, lang('user_noenterroom'));
		}
		$res['entertime'] = $entertime;
		$res['outtime'] = $outtime;
	    //获取设备信息
	    $mongodb = new Logininfo();
	    $deviceInfo = $mongodb->getDeviceInfoByComSerialUser($companyid,$roomid,$userid,strtotime($entertime),strtotime($outtime),0);
	    if($deviceInfo){
	    	$res['devicetype'] = $deviceInfo[0]['devicetype'];
	    	$res['deviceName'] = $deviceInfo[0]['deviceName'];
	    	$res['ip'] = $deviceInfo[0]['ip'];	    	
	    	$res['systemversion'] = $deviceInfo[0]['systemversion'];
	    	$res['sdkVersion'] = $deviceInfo[0]['sdkVersion'];
	    	$res['cpuArchitecture'] = $deviceInfo[0]['cpuArchitecture'];    	
	    }else{
	    	$res['devicetype'] = '';
	    	$res['deviceName'] = '';
	    	$res['ip'] = '';	    	
	    	$res['systemversion'] = '';
	    	$res['sdkVersion'] = '';
	    	$res['cpuArchitecture'] = ''; 	    	
	    }
		$res['roomtype'] = $roomtype;
		$res['roomtypename'] = getRoomTypeName($roomtype);
		//获取教室名称
		$room = new Room();
		$roomData = $room->getRoomDetail($roomid);
		$res['roomname'] = $roomData['roomname'];
		//获取机构名称
		$company = new company();
		$companyRes = $company->getCompanyInfoById($companyid);
		if($companyRes){
			$res['companyfullname'] = $companyRes['companyfullname'];
		}else{
			$res['companyfullname'] = '';
		}
		//该课的上下课时间
		$res['starttime'] = $starttime;
	    $res['endtime'] = $endtime;
		//获取课时时长
		$classtime = strtotime($endtime) - strtotime($starttime);
		//友好的显示
		$res['classtime'] = showTime($classtime);

		//该课的停留时间
		$lastingtime = strtotime($outtime) - strtotime($entertime);
		//友好的显示
		$res['lastingtime'] = showTime($lastingtime);	   	
		// var_dump($currency);
		// die;
		return return_format($res,0) ;
	}

	//获取网络上行情况
	/**
	 * 
	 * @Author zzq
	 * @date 2018-08-09
	 * @param   $data            
	 * @return  array         
	 */	
	public function getHisUserUpNetworkByComDateRoomUser($data){
		//构造查询条件
		$where = [];
		if(!empty($data['companyid'])){
			$companyid = $data['companyid'];
		}else{
			//companyid_error
			return return_format('', 50007, lang('companyid_error'));
		}
		if(!empty($data['roomid'])){
			$roomid = $data['roomid'];
		}else{
			return return_format('', 50008, lang('roomid_error'));
		}
		if( isset($data['roomtype']) && ($data['roomtype'] != '') ){
			$ret = ['0','3','10'];
			if(!in_array($data['roomtype'],$ret)){
				return return_format('', 50006, lang('roomtype_error'));
			}
			$roomtype = $data['roomtype'];
		}else{
			return return_format('', 50006, lang('roomtype_error'));
		}
		//检验日期格式
		if(!empty($data['date'])){
			if(checkDateFormat($data['date'])){
				$date = $data['date'];
			}else{
				return return_format('', 50014, lang('datetime_error'));
			}
		}else{
			return return_format('', 50014, lang('datetime_error'));
		}
		//检验开始结束时间	
		if(empty($data['starttime'])){
			return return_format('', 50019, lang('datetime_error'));
		}
		if(empty($data['endtime'])){
			return return_format('', 50019, lang('datetime_error'));
		}
		if(empty($data['entertime'])){
			return return_format('', 50019, lang('datetime_error'));
		}
		if(empty($data['outtime'])){
			return return_format('', 50019, lang('datetime_error'));
		}
		//判断userid
		if(empty($data['userid'])){
			return return_format('', 50016, lang('userid_error'));
		}
		$userid = $data['userid'];		
		$starttime = $data['starttime'];
		$endtime = $data['endtime'];
		$entertime = $data['entertime'];
		$outtime = $data['outtime'];
		$logininfo = new Logininfo();
		$_data = [];
		$_data['companyid'] = ['EQ',$companyid];
		$_data['serial'] = ['EQ',$roomid];
		$_data['userroleid'] = ['EQ','0'];
		$_data['roomtype'] = ['EQ',$roomtype];
		$_data['starttime'] = ['EQ',$starttime];
		$_data['endtime'] = ['EQ',$endtime];
		// var_dump($_data);
		// die;
		$res = $logininfo->getHisClass($_data);
		// var_dump($res);
		// die;
		if(!$res){
			//历史课堂不存在
			return return_format('', 50018, lang('hisclass_notexist'));
		}

		//查看这个用户是否上过这个课
		$logininfo = new Logininfo();
		//查的是学员的数目
		$where = [];
		$where['companyid'] = $companyid;
		$where['userid'] = $userid;
		$where['serial'] = $roomid;
		$where['roomtype'] = $roomtype;
		$where['entertime'] = $entertime;
		$where['outtime'] = $outtime;
		$userInfo = $logininfo->getHisUserToHisRoom($where);
		if(!$userInfo){
			//这个用户没有上过该课
			return return_format('', 50020, lang('user_noenterroom'));
		}
		//die;
		//获取网络上行的情况
	    $mongodb = new Logininfo();
	    $result = $mongodb->getNetworkInfoByComSerialUser($companyid,$roomid,$userid,$userid,strtotime($entertime),strtotime($outtime),0,1);
	    // var_dump($result);
	    // die;
	    $resList = [];
	    // var_dump($res);
	    // die;
	    if($result){
		    foreach ($result as $k => $v) {
		    	//var_dump($v);
		    	// die;
		    	$resList[$k]['datetime'] = Date('Y-m-d H:i:s',$v['statistical']['time']);
		    	$resList[$k]['cpuOccupancy'] = $v['statistical']['0']['cpuOccupancy'];
		    	$resList[$k]['video']['bitsPerSecond'] = $v['statistical']['0']['video']['bitsPerSecond'];
		    	$resList[$k]['video']['packetsLost'] = $v['statistical']['0']['video']['packetsLost'];
		    	$resList[$k]['video']['currentDelay'] = $v['statistical']['0']['video']['currentDelay'];
		    	$resList[$k]['video']['netquality'] = $v['statistical']['0']['video']['netquality'];
		    	$resList[$k]['audio']['bitsPerSecond'] = $v['statistical']['0']['audio']['bitsPerSecond'];
		    	$resList[$k]['audio']['packetsLost'] = $v['statistical']['0']['audio']['packetsLost'];
		    	$resList[$k]['audio']['currentDelay'] = $v['statistical']['0']['audio']['currentDelay'];
		    	$resList[$k]['audio']['netquality'] = $v['statistical']['0']['audio']['netquality'];
		    }
		    $mydata['NowCpuOccupancy'] = $result[0]['statistical']['0']['cpuOccupancy'];	    	
	    }else{
	    	$mydata['data'] = [];
	    	$mydata['NowCpuOccupancy'] = '';
	    }
	    $mydata['data'] = $resList;
	    $mydata['companyid'] = $companyid;
	    $mydata['serial'] = $roomid;
	    $mydata['userid'] = $userid;
		return return_format($mydata,0) ;


	}

	//获取网络下行情况
	/**
	 * 
	 * @Author zzq
	 * @date 2018-08-09
	 * @param   $data            
	 * @return  array         
	 */	
	public function getHisUserDownNetworkByComDateRoomUser($data){
		//构造查询条件
		$where = [];
		if(!empty($data['companyid'])){
			$companyid = $data['companyid'];
		}else{
			//companyid_error
			return return_format('', 50007, lang('companyid_error'));
		}
		if(!empty($data['roomid'])){
			$roomid = $data['roomid'];
		}else{
			return return_format('', 50008, lang('roomid_error'));
		}
		if( isset($data['roomtype']) && ($data['roomtype'] != '') ){
			$ret = ['0','3','10'];
			if(!in_array($data['roomtype'],$ret)){
				return return_format('', 50006, lang('roomtype_error'));
			}
			$roomtype = $data['roomtype'];
		}else{
			return return_format('', 50006, lang('roomtype_error'));
		}
		//检验日期格式
		if(!empty($data['date'])){
			if(checkDateFormat($data['date'])){
				$date = $data['date'];
			}else{
				return return_format('', 50014, lang('datetime_error'));
			}
		}else{
			return return_format('', 50014, lang('datetime_error'));
		}
		//检验开始结束时间	
		if(empty($data['starttime'])){
			return return_format('', 50019, lang('datetime_error'));
		}
		if(empty($data['endtime'])){
			return return_format('', 50019, lang('datetime_error'));
		}
		if(empty($data['entertime'])){
			return return_format('', 50019, lang('datetime_error'));
		}
		if(empty($data['outtime'])){
			return return_format('', 50019, lang('datetime_error'));
		}
		//判断userid
		if(empty($data['userid'])){
			return return_format('', 50016, lang('userid_error'));
		}
		$userid = $data['userid'];		
		$starttime = $data['starttime'];
		$endtime = $data['endtime'];
		$entertime = $data['entertime'];
		$outtime = $data['outtime'];
		$logininfo = new Logininfo();
		$_data = [];
		$_data['companyid'] = ['EQ',$companyid];
		$_data['serial'] = ['EQ',$roomid];
		$_data['userroleid'] = ['EQ','0'];
		$_data['roomtype'] = ['EQ',$roomtype];
		$_data['starttime'] = ['EQ',$starttime];
		$_data['endtime'] = ['EQ',$endtime];
		// var_dump($_data);
		// die;
		$res = $logininfo->getHisClass($_data);
		// var_dump($res);
		// die;
		if(!$res){
			//历史课堂不存在
			return return_format('', 50018, lang('hisclass_notexist'));
		}

		//查看这个用户是否上过这个课
		$logininfo = new Logininfo();
		//查的是学员的数目
		$where = [];
		$where['companyid'] = $companyid;
		$where['userid'] = $userid;
		$where['serial'] = $roomid;
		$where['roomtype'] = $roomtype;
		$where['entertime'] = $entertime;
		$where['outtime'] = $outtime;
		$userInfo = $logininfo->getHisUserToHisRoom($where);
		if(!$userInfo){
			//这个用户没有上过该课
			return return_format('', 50020, lang('user_noenterroom'));
		}

		
		//1 查logininfo表查取所有的下行的其他的人员（除了自己以外的其他上过改课的人员）
		$obj = new Logininfo();
		$whereOne = [];
		$whereOne['companyid'] = $companyid;
		$whereOne['serial'] = $roomid;
		$whereOne['roomtype'] = $roomtype;
		$whereOne['userid'] = $userid;
		$whereOne['starttime'] = $starttime;
		$whereOne['endtime'] = $endtime;
		$otherUseridRes = $obj->getHisUserDownNetworkUserList($whereOne);
		// var_dump($otherUseridRes);
		// die;
		//2 查询出该用户的所有的下行的情况
	    $endtime = strtotime($outtime);
		$fromtime = strtotime($entertime);
		$ret=[];
		if($otherUseridRes){
			foreach($otherUseridRes as $k => $v){
				$_ret = [];
				$logininfo = new Logininfo();
				$downNetworkRes = $logininfo->getNetworkInfoByComSerialUser($companyid,$roomid,$userid,$v['userid'],$fromtime,$endtime,0,1);
				// var_dump($downNetworkRes);
				// die;
				foreach($downNetworkRes as $k1 => $v1){
				    $_ret[$k1]['datetime'] = Date('Y-m-d H:i:s',$v1['statistical']['time']);
				    $_ret[$k1]['userid'] = $v['userid'];
				    $_ret[$k1]['userroleid'] = $v['userroleid'];
				    $_ret[$k1]['userrolename'] = getRoleNameByRoleId($v['userroleid']);
				    $_ret[$k1]['username'] = $v['username'];
			    	$_ret[$k1]['cpuOccupancy'] = $v1['statistical']['0']['cpuOccupancy'];
			    	$_ret[$k1]['video']['bitsPerSecond'] = $v1['statistical']['0']['video']['bitsPerSecond'];
			    	$_ret[$k1]['video']['packetsLost'] = $v1['statistical']['0']['video']['packetsLost'];
			    	$_ret[$k1]['video']['currentDelay'] = $v1['statistical']['0']['video']['currentDelay'];
			    	$_ret[$k1]['video']['netquality'] = $v1['statistical']['0']['video']['netquality'];
			    	$_ret[$k1]['audio']['bitsPerSecond'] = $v1['statistical']['0']['audio']['bitsPerSecond'];
			    	$_ret[$k1]['audio']['packetsLost'] = $v1['statistical']['0']['audio']['packetsLost'];
			    	$_ret[$k1]['audio']['currentDelay'] = $v1['statistical']['0']['audio']['currentDelay'];
			    	$_ret[$k1]['audio']['netquality'] = $v1['statistical']['0']['audio']['netquality'];
				}
				// var_dump($_ret);
				// die;
				if($_ret){
					$ret[$k] = $_ret;
				}
				
				// var_dump($ret);
				// die;
			}
		}
		return return_format($ret,0) ;

	}

/*****************************************统计查询***************************************/
}


