<?php
/**
 * 监课统计控制器
 * @author zzq
 * @date 18-07-03
 * 
 */
namespace app\admin\controller;
use think\Controller;
use think\Request;
use app\admin\business\SystemsetManage;
class Systemset extends Controller {
	//自定义初始化
	protected function _initialize() {
		parent::_initialize();
	}
	/**
	 * 转换查询列表
	 * @Author zzq
	 * ------------------------------------------------------
	 * @param  pagenum  当前页码
	 * @param  companyfullname  机构名称
	 * @param  sdate            开始日期  
	 * @param  edate            截止日期  
	 * @param  converttype  转换类型1表示录制件 | 2表示课件
	 * ------------------------------------------------------
	 * @param  recordstatus  0有效 1删除 (录制件) 
	 * @param  recordtype  原型参数 0代表转换 1代表超时 2代表失败(录制件)（这个参数待定）
	 * ------------------------------------------------------
	 * ------------------------------------------------------
	 * @param  filestatus  0未转换 1已转换 9代表已删除(课件)
	 * @param  filetype  原型参数（这个参数待定）
	 * @param  filekeyword 课件编号或者名称 
	 * ------------------------------------------------------
	 * POST | URL:/admin/Systemset/conversionlist
	 */
	public function conversionlist() {
		//
		$data = Request::instance()->POST(false);
		$data['pagenum'] = !empty($data['pagenum']) ? $data['pagenum'] : 1;
		$obj = new SystemsetManage;
		$dataReturn = $obj->conversionlist($data);
		$this->ajaxReturn($dataReturn);//

	}

	/**
	 * 日志列表
	 * @Author zzq
	 * @param  pagenum  当前页码
	 * @param  infoype  1代表操作日志 2系统日志
	 * @param  stime    infotype为1|2的时候传,表示开始时间      
	 * @param  etime    infotype为1|2的时候传,表示结束时间
	 * --------------------------------------------------      
	 * @param  ip       infotype为2的时候传     
	 * @param  pid      infotype为2的时候传      
	 * @param  data     infotype为2的时候传      
	 * @param  ------------------------------------------      
	 * @param  username infotype为1的时候传,表示用户名      
	 * @param  roleid     infotype为1的时候传,表示角色 -1的时候表示全部      
	 * POST | URL:/admin/Systemset/loginfolist
	 */
	public function loginfolist(){
		$data = Request::instance()->POST(false);
		$data['pagenum'] = !empty($data['pagenum']) ? $data['pagenum'] : 1;
		//$data['infotype'] = !empty($data['infotype']) ? $data['infotype'] : 2;
		$obj = new SystemsetManage;
		$dataReturn = $obj->loginfolist($data);
		$this->ajaxReturn($dataReturn);
	}

	/**
	 * 添加操作日志
	 * @Author    
	 * @param  useraccount 账户名      
	 * @param  userroleid  角色id      
	 * @param  content     操作内容 
	 * @param  userid      用户id 
	 * @param    角色id      
	 * POST | URL:/admin/Systemset/addoperatelog
	 */
	public function addoperatelog(){
		$data = Request::instance()->POST(false);
		$obj = new SystemsetManage;
		$dataReturn = $obj->addoperatelog($data);
		$this->ajaxReturn($dataReturn);
	}
	/**
	 * 存储列表
	 * @Author zzq
	 * @param  pagenum  当前页码
	 * @param  companykeyword   企业ID或者企业名称      
	 * @param  sdate    开始日期，默认为6个月前2018-02      
	 * @param  edate    结束日期.默认为当前日期       
	 * POST | URL:/admin/Systemset/storage
	 */
	public function storage(){
		$data = Request::instance()->POST(false);
		$data['pagenum'] = !empty($data['pagenum']) ? $data['pagenum'] : 1;
		$obj = new SystemsetManage;
		$dataReturn = $obj->storage($data);
		$this->ajaxReturn($dataReturn);
	}

/*****************************************实时在线***************************************/	
	/**
	 * //z1实时在线->企业并发->获取在线的机构的列表
	 * @Author zzq
	 * @param  pagenum  当前页码
	 * @param  companykeyword  机构名称或者机构id(搜索)        
	 * @param  roomtype  教室类型(1表示小班课[一对一小班课(roomtype->0),一对多小班课(roomtype->3)] 2->大班课(直播课roomtype->10) 3不限 搜索)        
	 * @return  array  
	 * 包含:总计的在线课堂数 总计的在线人数 在线机构信息(array:机构名  机构id 域名 在线课堂数 在线人数)          
	 * POST | URL:/admin/Systemset/getOnlineCompanyList
	 */
	public function getOnlineCompanyList(){
		$data = Request::instance()->POST(false);
		$data['pagenum'] = !empty($data['pagenum']) ? $data['pagenum'] : 1;
		$obj = new SystemsetManage;
		$dataReturn = $obj->getOnlineCompanyList($data);
		$this->ajaxReturn($dataReturn);	
	}

	/**
	 * //z2实时在线->企业并发->获取某个机构的在线教室的列表
	 * @Author zzq
	 * @param  pagenum  当前页码
	 * @param  companyid  机构id(必填)        
	 * @param  roomkeyword  教室名称或者教室id(搜索)        
	 * @return  array  
	 * 包含:教室名称  某个机构的所有教室的信息(array:教室名称 教室编号 教室类型 教室人数)          
	 * POST | URL:/admin/Systemset/getOnlineRoomListByCom
	 */
	public function getOnlineRoomListByCom(){
		$data = Request::instance()->POST(false);
		$data['pagenum'] = !empty($data['pagenum']) ? $data['pagenum'] : 1;
		$obj = new SystemsetManage;
		$dataReturn = $obj->getOnlineRoomListByCom($data);
		$this->ajaxReturn($dataReturn);	
	}

	
	/**
	 * //z3实时在线->企业并发->在线教室列表
	 * @Author zzq
	 * @param  pagenum  当前页码
	 * @param  companyname  机构名称(搜索)
	 * @param  roomname  教室名称(搜索)          
	 * @return  array  
	 * 包含:机构名  教室名称 教师编号 教室类型 在线人数          
	 * POST | URL:/admin/Systemset/getOnlineRoomList
	 */
	public function getOnlineRoomList(){
		$data = Request::instance()->POST(false);
		$data['pagenum'] = !empty($data['pagenum']) ? $data['pagenum'] : 1;
		$obj = new SystemsetManage;
		$dataReturn = $obj->getOnlineRoomList($data);
		$this->ajaxReturn($dataReturn);		
	}

	/**
	 * //获取在线预警人员列表
	 * @Author zzq 2018-08-03
	 * @param  pagenum  当前页码
	 * @param  companyname  机构名称(搜索)
	 * @param  roomname  教室名称(搜索)          
	 * @return  array  
	 * 包含:机构名  教室名称           
	 * POST | URL:/admin/Systemset/getGuardOnlineUserList
	 */
	public function getGuardOnlineUserList(){
		$data = Request::instance()->POST(false);
		$data['pagenum'] = !empty($data['pagenum']) ? $data['pagenum'] : 1;
		$obj = new SystemsetManage;
		$dataReturn = $obj->getGuardOnlineUserList($data);
		$this->ajaxReturn($dataReturn);		
	}

	/**
	 * //z4在线教室详情
	 * @Author zzq
	 * @param  companyid   在线教室的id       
	 * @param  roomid   在线教室的id       
	 * @return  array  
	 * array包含:教室名称 教室编号 教室类型 开始时间 教师名称 在线人数           
	 * POST | URL:/admin/Systemset/getOnlineRoomDetail
	 */
	public function getOnlineRoomDetail(){
		$data = Request::instance()->POST(false);
		$obj = new SystemsetManage;
		$dataReturn = $obj->getOnlineRoomDetail($data);
		$this->ajaxReturn($dataReturn);		
	}
	/**
	 * //z5获取某个在线教室人员进出列表
	 * @Author zzq
	 * @param  companyid 当前的机构id
	 * @param  pagenum  当前页码(表示人员进出记录的列表)
	 * @param  roomid   在线教室的id       
	 * @return  array  
	 * array包含:(array:用户名 角色 状态 登录信息 服务器 时间记录)          
	 * POST | URL:/admin/Systemset/getOnlineRoomDetail
	 */
    public function getOnlineRoomRecordList(){
    	$data = Request::instance()->POST(false);
    	$data['pagenum'] = !empty($data['pagenum']) ? $data['pagenum'] : 1;
		$obj = new SystemsetManage;
		$dataReturn = $obj->getOnlineRoomRecordList($data);
		$this->ajaxReturn($dataReturn);	
    }

    
	/**
	 * //获取某个在线教室的某个离在线人员的基本详情
	 * @Author zzq
	 * @param  companyid  当前机构id
	 * @param  roomid   在线教室的id       
	 * @param  userid   在线用户id      
	 * @return  array  
	 * array包含:(array:用户名 角色 状态 登录信息 服务器 时间记录)          
	 * POST | URL:/admin/Systemset/getOnlineRoomRecordDetail
	 */
    public function getOnlineRoomRecordDetail(){
    	$data = Request::instance()->POST(false);
		$obj = new SystemsetManage;
		$dataReturn = $obj->getOnlineRoomRecordDetail($data);
		$this->ajaxReturn($dataReturn);
    }
    
    
	/**
	 * //获取某个在线教室的某个离在线人员的网络上行曲线
	 * @Author zzq
	 * @param  companyid  当前机构id
	 * @param  roomid   在线教室的id       
	 * @param  roomtype   在线教室的类型       
	 * @param  userid   用户id      
	 * @return  array          
	 * POST | URL:/admin/Systemset/getUpNetworkByOnOrOffline
	 */
    public function getUpNetworkByOnOrOffline(){
    	$data = Request::instance()->POST(false);
		$obj = new SystemsetManage;
		$dataReturn = $obj->getUpNetworkByOnOrOffline($data);
		$this->ajaxReturn($dataReturn);
    }

	/**
	 * //获取某个在线教室的某个离在线人员的网络下行曲线
	 * @Author zzq
	 * @param  companyid  当前机构id
	 * @param  roomid   在线教室的id 
	 * @param  roomtype   在线教室的类型       
	 * @param  userid   用户id      
	 * @return  array          
	 * POST | URL:/admin/Systemset/getUpNetworkByOnOrOffline
	 */
    public function getDownNetworkByOnOrOffline(){
    	$data = Request::instance()->POST(false);
		$obj = new SystemsetManage;
		$dataReturn = $obj->getDownNetworkByOnOrOffline($data);
		$this->ajaxReturn($dataReturn);
    }

/*****************************************实时在线***************************************/	
/*****************************************统计查询***************************************/	
	/**
	 * //统计查询图表
	 * @Author zzq
	 * @param  companykeyword   企业名称或者企业id(搜索，此搜索是精准搜索)      
	 * @param  sdate(2018-01-01)  开始日期(搜索)      
	 * @param  edate(2018-08-01)  结束日期(搜索)  
	 * @param  roomtype  教室类型(0-》1对1  3=> 1对多 10 => 直播课)  
	 * @param  showtype  显示类型(1=》课堂数 2=>人数) 
	 * @return  array  
	 * array包含:日期 机构名称 小班课一对一教室数量 小班课一对多教室数量 大班课教室数量 小班课一对一在线人数 小班课一对多在线人数 大班课在线人数           
	 * POST | URL:/admin/Systemset/getAnalysisChart
	 */
	public function getAnalysisChart(){
		$data = Request::instance()->POST(false);
		$data['pagenum'] = isset($data['pagenum']) ? $data['pagenum'] : 1;
		$obj = new SystemsetManage;
		$dataReturn = $obj->getAnalysisChart($data);
		$this->ajaxReturn($dataReturn);				
	}	

	//统计查询->获取某企业某日期课堂数与人员数
	/**
	 * 
	 * @Author zzq
	 * @date 2018-07-30
	 * @param  companyid   企业id(0表示所有企业 其他代表指定企业)      
	 * @param  date(2018-01-01)  日期(搜索)             
	 * @return  array  
	 * array包含:日期 机构名称 小班课一对一教室数量 小班课一对多教室数量 大班课教室数量 小班课一对一在线人数 小班课一对多在线人数 大班课在线人数          
	 * POST | URL:/admin/Systemset/getAnalysisSumByComAndDate
	 */	
	public function getAnalysisSumByComAndDate(){
		$data = Request::instance()->POST(false);
		$obj = new SystemsetManage;
		$dataReturn = $obj->getAnalysisSumByComAndDate($data);
		$this->ajaxReturn($dataReturn);			
	}

	//统计查询->指定企业[获取某一个日期内 三班的课堂列表（全部 1对1 1对多 大班课）]
	/**
	 * 
	 * @Author zzq
	 * @date 2018-07-30
	 * @param  companyid   企业id(0表示所有企业 其他代表指定企业)      
	 * @param  date(2018-01-01)  日期            
	 * @param  roomid  教室号（搜索关键字）             
	 * @param  roomtype  教室类型（搜索关键字） 
	 * @param  pagenum  当前页码            
	 * @return  array  
	 * array包含:教室名称  教室号 教室类型 机构名称 开始时间 结束时间       
	 * POST | URL:/admin/Systemset/getAnalysisSumByComAndDate
	 */	
	public function getHisRoomListByComAndDate(){
		$data = Request::instance()->POST(false);
		$data['pagenum'] = isset($data['pagenum']) ? $data['pagenum'] : 1;
		$obj = new SystemsetManage;
		$dataReturn = $obj->getHisRoomListByComAndDate($data);
		$this->ajaxReturn($dataReturn);	
	}	

	//统计查询->课堂报告->获取某天某机构某个课堂的详情
	/**
	 * 
	 * @Author zzq
	 * @date 2018-07-31    
	 * @param  companyid   企业id    
	 * @param  date(2018-01-01)  日期        
	 * @param  roomid  教室号                         
	 * @param  roomtype  教室类型                         
	 * @param  starttime  开始时间                         
	 * @param  endtime  结束时间                         
	 * @return  array  
	 * array包含:教室名称  教室号 教室类型 机构名称 机构id 开始时间 结束时间 并发数       
	 * POST | URL:/admin/Systemset/getHisRoomDetailByComAndDateAndRoom
	 */	
	public function getHisRoomDetailByComAndDateAndRoom(){
		$data = Request::instance()->POST(false);
		$obj = new SystemsetManage;
		$dataReturn = $obj->getHisRoomDetailByComAndDateAndRoom($data);
		$this->ajaxReturn($dataReturn);	
	}	

	//统计查询->课堂报告->获取某一个日期内 某个机构 某个课堂 的进出人员列表
	/**
	 * 
	 * @Author zzq
	 * @date 2018-07-30          
	 * @param  date  日期                         
	 * @param  companyid  机构id                         
	 * @param  roomid  教室号                         
	 * @param  roomtype  教室类型                         
	 * @param  starttime  开始时间                         
	 * @param  endtime  结束时间                         
	 * @param  pagenum  页码，默认为1                         
	 * @return  array  
	 * array包含:      
	 * POST | URL:/admin//Systemset/getHisRoomDetailByComAndDateAndRoom
	 * array包含:用户名 角色  上课时长 ip地址 设备 开始时间 结束时间 进入时间  离开时间  报警时间(数组)
	 */	
	public function getHisUserListByComAndDateAndRoom(){
		$data = Request::instance()->POST(false);
		$data['pagenum'] = isset($data['pagenum']) ? $data['pagenum'] : 1;
		$obj = new SystemsetManage;
		$dataReturn = $obj->getHisUserListByComAndDateAndRoom($data);
		$this->ajaxReturn($dataReturn);			
	}

	//统计查询->个人报告->某日期/某课堂/某人的设备情况
	/**
	 * 
	 * @Author zzq
	 * @date 2018-07-30
	 * @param  companyid   企业id     
	 * @param  date(2018-01-01)  日期             
	 * @param  roomid  教室号                         
	 * @param  roomtype  教室类型                         
	 * @param  userid  用户id                        
	 * @param  starttime  上课开始时间                        
	 * @param  endtime  上课结束时间  
	 * @param  entertime  进入时间                        
	 * @param  outtime  离开时间                        
	 * @return  array  
	 * array包含:用户名  用户id 角色 教室名 教室id 上课时间 下课时间  课时长 进入时间 离开时间 上课时长 设备类型 操作系统 CPU架构 SDK版本 ip 国家 地区 城市 运营商       
	 * POST | URL:/admin/Systemset/getHisUserDevByComDateRoomUser
	 */
	public function getHisUserDeviceByComDateRoomUser(){
		$data = Request::instance()->POST(false);
		$obj = new SystemsetManage;
		$dataReturn = $obj->getHisUserDeviceByComDateRoomUser($data);
		$this->ajaxReturn($dataReturn);	
	}

	//统计查询->个人报告->某日期/某课堂/某人的网络上行变化曲线
	/**
	 * 
	 * @Author zzq
	 * @date 2018-08-02
	 * @param  companyid   企业id     
	 * @param  date(2018-01-01)  日期             
	 * @param  roomid  教室号                         
	 * @param  roomtype  教室类型                         
	 * @param  userid  用户id                        
	 * @param  starttime  上课开始时间                        
	 * @param  endtime  上课结束时间 
	 * @param  entertime  进入时间                        
	 * @param  outtime  离开时间                                               
	 * @return array  
	 * array包含:      
	 * POST | URL:/admin/Systemset/getHisUserUpNetworkByComDateRoomUser
	 */
	public function getHisUserUpNetworkByComDateRoomUser(){
		$data = Request::instance()->POST(false);
		$obj = new SystemsetManage;
		$dataReturn = $obj->getHisUserUpNetworkByComDateRoomUser($data);
		$this->ajaxReturn($dataReturn);			
	}

	//统计查询->个人报告->某日期/某课堂/某人的网络下行变化曲线
	/**
	 * 
	 * @Author zzq
	 * @date 2018-08-02
	 * @param  companyid   企业id     
	 * @param  date(2018-01-01)  日期             
	 * @param  roomid  教室号                         
	 * @param  roomtype  教室类型                         
	 * @param  userid  用户id                        
	 * @param  starttime  上课开始时间                        
	 * @param  endtime  上课结束时间 
	 * @param  entertime  进入时间                        
	 * @param  outtime  离开时间                                                  
	 * @return array  
	 * array包含:      
	 * POST | URL:/admin/Systemset/getHisUserCpuByComDateRoomUser
	 */	
	public function getHisUserDownNetworkByComDateRoomUser(){
		$data = Request::instance()->POST(false);
		$obj = new SystemsetManage;
		$dataReturn = $obj->getHisUserDownNetworkByComDateRoomUser($data);
		$this->ajaxReturn($dataReturn);		
	}

/*****************************************统计查询***************************************/			
}
