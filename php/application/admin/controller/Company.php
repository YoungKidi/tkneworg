<?php
namespace app\admin\controller;
use think\Request;
use think\File;
use think\Controller;
use app\admin\controller\Common;
use app\admin\business\CompanyManage;
use app\admin\business\TemplateinfoMange;

/**
 * @name 企业管理
 * author 胡博森
 */
class Company extends Controller{

    private $companyid;//企业id
    private $uid;//登录用户id
    private $identity;//登录用户的身份
    private $roleid;//角色id
    public function __construct()
    {
        parent::__construct();
        $this->companyid = 10033;
        $this->uid = 102120;
        $this->identity = 0;
        $this->roleid = 10;
    }

    /**
	 * 获取企业列表
     * @auther 胡博森
	 * @param   string $token 用户token
	 * @param	int	$p	当前页
	 * @param	string $company_name	机构名称
	 * @param	string $company_state	机构状态 0:试用 1:正式 2:正常到期 3:试用到期 4:冻结 10:全部
     * @param   int $sale_id    销售id
     * @param  int $chk_month 是否查询当月过期
	 * @param	int	$page	当前页
	 * @return json
	 */
	public function getCompanyList(){
		$arr_data = [];
		//获取用户在企业的角色,测试时写死
		$arr_data['user_role_id'] = $this->roleid;
		//获取当前登录人id
		$arr_data['user_id'] = $this->uid;
		$arr_data['chk_month'] = input('post.chk_month',0,'trim');
		//接收机构名称
		$arr_data['company_name'] = input('post.company_name','','trim');
		//接收机构状态
		$arr_data['company_state'] = input('post.company_state',10,'trim');
		//接收销售id
		$arr_data['saleid'] = input('post.sale_id',0,'trim');
		//接收当前页
		$arr_data['page'] = input('post.page',1,'trim');
		$obj_company = new CompanyManage;
        $arr_return_data = $obj_company->getCompanyList($arr_data);
        return $this->ajaxReturn($arr_return_data);
	}

    /**
	 * 获取销售员列表
     * @auther 胡博森
	 * @return array
	 */
	public function getSaleList(){
        $obj_company = new CompanyManage;
        $arr_data = $obj_company->getSaleList();
        return $this->ajaxReturn($arr_data);
	}

    /**
	 * 添加企业
     * @auther 胡博森
	 * @param string $company_full_name	企业名称
	 * @param string $company_domain 企业域名
	 * @param string $company_state 企业状态
	 * @param string $admin_account 管理员账号
	 * @param string $admin_name 管理员姓名
	 * @param string $admin_pwd 管理员密码
	 * @param string $admin_pwd_again 管理员确认密码
	 * @param int	$sale_id 所属销售
	 * @param string $token
	 * @return json
	 */
	public function setCompanyAdd(){
		$arr_data = [];
		//获取当前登录的企业id，这里测试，企业id是1
		$arr_where['companyid'] = $this->companyid;
        //获取当前登录人的id，这里测试，userid是1
        $arr_data['user_id'] = $this->uid;
        //接受input数据
        $arr_data['companyname'] = trim(input('post.company_full_name','','trim'));//企业简称
        $arr_data['companyfullname'] = trim(input('post.company_full_name','','trim'));//企业全称
        $arr_data['companystate'] = input('post.company_state','','trim');//企业状态 0:试用 1:正式 2:正常到期 3:试用到期 4:冻结
        $arr_data['account'] = trim(input('post.admin_account','','trim'));//登陆账号
        $arr_data['firstname'] = trim(input('post.admin_name','','trim'));//管理员名字 table：usercompany
        $arr_data['seconddomain'] = trim(input('post.company_domain','','trim'));//企业域名 table：company
        $arr_data['pwd'] = trim(input('post.admin_pwd','','trim'));//登录密码
        $arr_data['pwd_again'] = trim(input('post.admin_pwd_again','','trim'));//再次输入密码
        $arr_data['saleid'] = trim(input('post.sale_id','','trim'));//再次输入密码
        $arr_data['parentid'] = $this->companyid;
        $arr_data['createuserid'] = $this->uid;//创建账号的用户id
        $obj_company = new CompanyManage();
        $arr_return = $obj_company->setCompanyAdd($arr_where,$arr_data);
        return $this->ajaxReturn($arr_return);
	}

    /**
     * 修改企业备注
     * @auther 胡博森
     * @param stirng $company_remark 输入的企业备注
     */
	public function setCompanyRemark(){
        $arr_data['remark'] = input('post.company_remark','','trim');
        $arr_where['companyid'] = input('post.company_id','','trim');
        $obj_company = new CompanyManage;
        $arr_return = $obj_company->setCompanyRemark($arr_where,$arr_data);
        return $this->ajaxReturn($arr_return);
    }

    /**
	 * 删除企业接口
     * @auther 胡博森
	 * @param string $company_id 企业id
     * @param int $chk_month 查询当月过期
     * @param string|int $company_name 查询的企业id或名字
     * @param int $company_state 查询的状态
     * @param int $sale_id 查询的销售id
     * @param int $page 查询页
	 * @param string $token
	 * @return json
	 */
	public function setCompanyDel(){
		//token
        //获取用户在企业的角色
        $arr_data['user_role_id'] = $this->roleid;
        $arr_data['user_id'] = $this->uid;//获取当前登录人id
        $arr_data['chk_month'] = input('post.chk_month',0,'trim');
        //接收机构名称
        $arr_data['company_name'] = input('post.company_name','','trim');
        //接收机构状态
        $arr_data['company_state'] = input('post.company_state',10,'trim');
        //接收销售id
        $arr_data['saleid'] = input('post.sale_id',0,'trim');
        //接收当前页
        $arr_data['page'] = input('post.page',1,'trim');
		$arr_where['companyid'] = input('post.company_id');//要删除的企业id
		$obj_company = new CompanyManage;
		$arr_return = $obj_company->setCompanyDel($arr_where,$arr_data);
        return $this->ajaxReturn($arr_return);
	}

    /**
	 * 企业详情
     * @auther 胡博森
	 * @param string token
	 * @param string company_name 企业名称
	 * @return json
	 */
	public function getCompanyDetails(){
		$arr_data['company_name'] = input('post.company_name');
		$obj_company = new CompanyManage;
	    return $this->ajaxReturn($obj_company->getCompanyDetails($arr_data));
	}

    /**
     * 修改企业基本信息
     * @auther 胡博森
     * @param string $company_id 企业编号
     * @param string $company_state 是否冻结
     * @param string $company_colony 集群
     * @param string $company_name 企业名称
     * @param int $silentpoint 大班课
     * @param int $userpoint 小班课
     * @param string $admin_pwd 管理员登录密码
     * @param string $admin_pwd_again 管理员确认登录密码
     * @param string $smallcharge 小班课计费模式
     * @param string $bigcharge 大班课计费模式
     * @param string $paystype 预付费方式
     * @param string $usetype 应用类型
     * @param string $industry 所在行业
     * @param string $starttime 开始时间
     * @param string $endtime 截止时间
     * @param string $remark 备注
     */
    public function setCompanyDetails(){
        //根据token获取企业id
        $arr_where['companyid'] = input('post.company_id','','trim');
        //接收企业修改信息
        $arr_data = [
            'companyname'=> input('post.company_name','','trim'),
            'companystate'=> input('post.company_state','','trim'),
            'colony'=> input('post.company_colony','','trim'),
            'companyfullname'=> input('post.company_name','','trim'),
            'silentpoint'=> input('post.silentpoint','','trim'),
            'userpoint'=> input('post.userpoint','','trim'),
            'admin_pwd'=> input('post.admin_pwd','','trim'),
            'admin_pwd_again'=> input('post.admin_pwd_again','','trim'),
            'smallcharge'=> input('post.smallcharge','','trim'),
            'bigcharge'=> input('post.bigcharge','','trim'),
            'paystype'=> input('post.paystype','','trim'),
            'usetype'=> input('post.usetype','','trim'),
            'industry'=> input('post.industry','','trim'),
            'starttime'=> input('post.starttime','','trim'),
            'endtime'=> input('post.endtime','','trim'),
            'remark'=> input('post.remark','','trim'),
        ];
        $obj_company = new CompanyManage;
        $arr_company = $obj_company->setCompanyDetails($arr_where,$arr_data);
        return $this->ajaxReturn($arr_company);
    }
    /**
     * 查询企业更多配置
     * @auther 胡博森
     * @param string $company_id 查询的企业id
     * @param string $type 需要查询的数据 1 界面显示 2 **企业配置 3 回调跳转 4 子企业
     */
    public function getCompanyConfig(){
        $int_companyid = input('post.company_id','','trim')?input('post.company_id','','trim'):1;
        $arr_data['type'] = input('post.type',1,'trim');
        $arr_data['page'] = input('post.page',1,'trim');
        $arr_where = ['companyid'=>$int_companyid];
        //查询企业的配置信息
        $obj_company = new CompanyManage;
        $arr_data = $obj_company->getCompanyConfig($arr_where,$arr_data);
        return $this->ajaxReturn($arr_data);
    }

    /**
     * 查询新增企业名称是否重复
     * @auther 胡博森
     * @param string company_name 企业名称
     */
    public function getCompanyRegister(){
        $company_name = input('post.company_name','','trim');
        $obj_company = new CompanyManage;
        $arr_company = $obj_company->isCompanyRegister($company_name);
        return $this->ajaxReturn($arr_company);
    }

    /**
     * 检测域名和账号是否被注册
     * @auther 胡博森
     * @param string company_domain 域名
     * @param string company_account 账号
     */
    public function getDomainRegister(){
        $company_domain = input('post.company_domain','','trim');
        $company_account = input('post.company_account','','trim');
        $obj_company = new CompanyManage;
        $json = $obj_company->isDomainRegister($company_domain,$company_account);
        return $this->ajaxReturn($json);
    }

    /**
     * 文件上传
     * @auther 胡博森
     * @param array $_FILES 上传的文件
     * @param string $company_id 上传的企业编号
     * @param string $file_type 上传的类型 1 企业LOGO 2 企业数据区缺省图片
     */
    public function setCompanyFile(){
        $data['organid'] = input('post.company_id',0,'trim');
        $data['files'] = $_FILES;
        $int_type = input('post.file_type',0,'trim');
        $data['allpathnode'] = [$int_type,$data['organid'],1];
        $obj_company = new CompanyManage;
        $arr_file_info = $obj_company->setCompanyFile($data);
        return $this->ajaxReturn($arr_file_info);
    }

    /**
     * 接收更多配置——企业配置项
     * @auther 胡博森
     *
     * @param string $company_title 企业页面标题
     * @param string $ico  企业Logo
     * @param string $dataregionimg 数据区缺省图片
     * @param string $whiteboards 自定义白色板底
     * @param int $videotype 视频分辨率
     * @param int $maxvideonum 最大视频数
     * @param string $company_title 跳转地址
     * @param string $company_title 直播跳转地址
     * @param string $roomstartcallbackurl 上课回调地址
     * @param string $callbackurl 下课回调地址
     * @param string $logincallbackurl 登入登出回调地址
     * @param string $recordcallback 录制完成回调地址
     * @param string $filenotifyurl 文档转换完成回调地址
     * @param string $helpcallbackurl 帮助跳转地址
     * @param string $recorduploadaddr 本地录制上传地址
     *
     * @param int type 修改的配置项类型
     *  ---全局配置项---21---
     * @param string $videotype 视频分辨率
     * @param string $maxvideonum 最大视频数
     * @param int $chk_allow_video 允许七路以上视频
     * @param string $numpages 课件最大页数
     * @param string $coursemaxsize 大课件上限
     * @param string $chk_mp mp3或mp4
     * @param int $chk_upd_child 子企业同步更新配置项
     * @param int $chk_video 视频
     * @param int $chk_screen 双屏显示
     * @param int $chk_local 本地录制
     * @param int $chk_image_reversal 自己的视频进行镜像反转
     * @param int $chk_foreground_picture 前景图
     * @param int $chk_rtmp RTMP推流
     * @param int $chk_voice 切换为纯音频教室
     * @param int $chk_resolution_consistency 用户分辨率一致
     * @param int $chk_client 强起客户端
     * @param int $chk_upload_file 下课后上传本地录制文件
     * @param int $chk_tenminutes_invalid 接口进入房间地址10分钟失效
     * @param int $chk_local_distinguishability 本地录制分辨率
     * @param int $chk_assistant_enter 不提示助教进入
     * @param int $chk_hd_audio 高清音频
     * @param int $1 H264编码
     *
     *  ---上课流程相关---22---
     * @param int $chk_automatic_class 自动上课
     * @param int $chk_students_close 学生可自行关闭音视频
     * @param int $chk_hide_button 隐藏上下课按钮
     * @param int $chk_assistant_open 助教是否开启音视频
     * @param int $chk_before_class 上课前发布音视频
     * @param int $chk_leave_classroom 下课后不离开教室
     * @param int $chk_courseware_sync 课件全屏同步
     * @param int $chk_picture_in_picture 画中画
     * @param int $chk_close_mp4 MP4播放完自动关闭
     * @param int $chk_close_client 下课后自动关闭客户端
     * @param int $chk_end_class 按下课时间结束课堂
     * @param int $chk_regional_exchange 本地区域交换
     * @param int $chk_tour_class 巡课取消点击下课
     * @param int $chk_account_login 交互教室用账号登录
     *
     *  ---课堂工具---23---
     * @param int $chk_procedure_share 程序共享
     * @param int $chk_h5_file H5文档
     * @param int $chk_strokes_jurisdiction 画笔权限
     * @param int $chk_file_paging 允许学生操作翻页
     * @param int $chk_show_answer 答题结束后自动显示答案
     * @param int $chk_ppt_remark 启用PPT备注
     * @param int $chk_trophy_voice 自定义奖杯声音
     * @param int $chk_video_annotation 视频标注
     * @param int $chk_upload_pictures 聊天区上传图片
     * @param int $chk_file_classification 文件分类
     * @param int $chk_strokes_name 画笔落笔显示名字
     * @param int $chk_screenshot 截图
     * @param int $chk_answer 答题器
     * @param int $chk_turntable 转盘
     * @param int $chk_timer 计时器
     * @param int $chk_responder 抢答器
     * @param int $chk_white_board 白板
     * @param int $chk_screen_share 屏幕共享
     * @param int $chk_file_definition 文档转换清晰度高清
     * @param int $chk_brush_operate 按用户区别落笔笔迹
     * @param int $chk_QR_code 二维码拍照上传
     * @param int $chk_local_video 播放本地媒体文件
     * @param int $chk_bothway_share 双向共享
     * @param int $chk_associated_courseware 上课时关联课件免刷新
     *
     *  ---版本相关---24---
     * @param int $chk_pointer 教鞭
     * @param int $chk_grouping 分组
     * @param int $chk_open_headmaster 开启班主任
     * @param int $chk_headmaster_monitoring 班主任监控
     *
     *  ---保留项---25---
     * @param int $chk_voice_frequency
     * @param int $chk_white_board
     * @param int $chk_invite
     * @param int $chk_classroom_transcribe
     * @param int $chk_share_video
     * @param int $chk_quit_classroom
     * @param int $chk_vote
     * @param int $chk_file_transfer
     * @param int $chk_high_definition
     * @param int $chk_questions_answers
     * @param int $chk_hide_chairman
     * @param int $chk_hide_teacher
     * @param int $chk_text_chat
     * @param int $chk_student_list
     * @param int $chk_courseware_list
     * @param int $chk_cut_figure
     * @param int $chk_web_share
     * @param int $chk_automatic_entry_classroom
     * @param int $chk_setup_wizard
     * @param int $chk_sip_phone
     * @param int $chk_h323_mcu
     * @param int $chk_automatically_open_video
     * @param int $chk_open_classroom
     * @param int $chk_hide_close_button
     * @param int $chk_hide_username
     * @param int $chk_prioritize
     * @param int $chk_play_video
     * @param int $chk_server_recorde
     * @param int $chk_automatic_recorde
     * @param int $chk_raise_hands
     *
     *  ---大班课相关---26---
     * @param int $chk_live 是否直播
     * @param int $chk_advertising_position 广告位
     * @param int $chk_live_account_login 直播教室用账号登录
     *
     * */
    public function setCompanyConfigs(){
        //接受type
        $int_type = input('post.type',0,'trim');
        $arr_data = input('post.');
        $obj_company = new CompanyManage;
        $arr_data = $obj_company->setCompanyConfig($int_type,$arr_data);
        return $this->AjaxReturn($arr_data);
    }

    /**
     * 修改企业管理员登录密码
     * @auther 胡博森
     * @param int $company_id 需要修改的企业id
     * @param string $pwd 修改的密码
     * @param string $pwd_again 确认修改的密码
     */
    public function setCompanyPwd(){
        $arr_data['companyid'] = input('post.company_id','','trim');
        $arr_data['pwd'] = input('post.admin_pwd','','trim');
        $arr_data['pwd_again'] = input('post.admin_pwd_again','','trim');
        $obj_company = new CompanyManage();
        $arr_return = $obj_company->setCompanyPwd($arr_data);
        return $this->AjaxReturn($arr_return);
    }

    /**
     * 冻结或恢复企业
     * @auther 胡博森
     */
    public function setCompanyState(){
        //接收企业id
        $arr_data['companyid'] = input('post.company_id','','trim');
        //获取用户id
        $arr_data['uid'] = $this->uid;
        //获取操作
        $arr_data['operation'] = input('post.operation');
        $obj_company = new CompanyManage;
        $arr_return = $obj_company->setCompanyState($arr_data);
        return $this->AjaxReturn($arr_return);
    }

    /**
     * 关联子企业
     * @auther 胡博森
     * @param int company_id 关联的企业id
     * @param int company_son 被关联的企业id
     */
    public function setCompanyParent(){
        //接收企业id
        $arr_data['parentid'] = input('post.company_id','','trim');
        //接收子企业id
        $arr_data['companyid'] = input('post.company_son_id','','trim');
        $obj_company = new CompanyManage();
        $arr_return = $obj_company->setCompanyParent($arr_data);
        return $this->AjaxReturn($arr_return);
    }

    /**
     * 查询子企业列表
     * @auther 胡博森
     * @param string|int company_name 查询的企业名称或编号
     * @param int page 跳转页
     */
    public function getCompanySon(){
        //接收搜索的企业名字
        $arr_data['companyfullname'] = input('post.company_name','','trim');
        $arr_data['page'] = input('post.page',1,'trim');
        $obj_company = new CompanyManage();
        $arr_return = $obj_company->getCompanySon($arr_data);
        return $this->AjaxReturn($arr_return);
    }


    /**
     * 空操作  当请求错误的方法时执行
     * @auther 胡博森
     */
    public function _empty(){
        $arr_return = ['',60404,lang('ActionInexistence')];
        return $this->AjaxReturn($arr_return);
    }
}