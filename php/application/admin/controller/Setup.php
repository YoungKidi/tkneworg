<?php
/**
 * 设置
 * @author yr
 * @date 18-06-27
 *
 */
namespace app\admin\controller;
use think\Controller;
use think\Request;
use app\admin\business\SetupManage;
class Setup extends Controller {
    //自定义初始化
    protected function _initialize() {
        parent::_initialize();
    }
    /**
     * 查看企业基本信息接口
     * @Author yr
     * POST | URL:/admin/Setup/getCompanyInfo
     */
    public function getCompanyInfo(){
        //获取当前登录的公司id
        $currcompanyid = 1;
        //获取当前公司基本信息
        $companymodel = new SetupManage;
        $result = $companymodel->getcompanyInfo($currcompanyid);
        $this->ajaxReturn($result);
    }

    /**
     * 查看企业设置信息接口
     * @Author yr
     * POST | URL:/admin/Setup/getCompanyInfo
     */
    public function getCompanySetInfo(){
        //获取当前登录的公司id
        $currcompanyid = 1;
        //获取当前公司基本信息
        $companymodel = new SetupManage;
        $result = $companymodel->getcompanySetInfo($currcompanyid);
        $this->ajaxReturn($result);
    }
    /**
     * 企业设置基本信息修改接口
     * @Author yr
     * @param  seconddomain ww域名
     * @param  authkey authkey
     * @param  companyfullname 企业名称
     * @param  companytitle 企业页面标题
     * @param  roomstartcallbackurl 上课回调地址
     * @param  callbackurl 下课回调地址
     * @param  logincallbackurl 登入登出回调地址
     * @param  recordcallback 录制完成回调地址
     * @param  filenotifyurl 文档转换完回调地址
     * @param  helpcallbackurl 帮助跳转地址
     * @param  ico 企业Logo
     * @param  dataregionimgl 数据区域缺省图片
     * POST | URL:/admin/Setup/editCompanyInfo
     * return
     */
    public function editCompanyInfo() {
        $currcompanyid = 1;
        //实例化课程逻辑层
        $post = Request::instance()->POST(false);
       /* //www域名
        $seconddomain = Request::instance()->POST('seconddomain');
        //authkey
        $authkey = Request::instance()->POST('authkey');
        //企业名称
        $companyfullname = Request::instance()->POST('companyfullname');
        //企业页面标题
        $companytitle = Request::instance()->POST('companytitle');
        //上课回调地址
        $roomstartcallbackurl = Request::instance()->POST('roomstartcallbackurl');
        //下课回调地址
        $callbackurl = Request::instance()->POST('callbackurl');
        //登入登出回调地址
        $logincallbackurl = Request::instance()->POST('logincallbackurl');
        //录制完成回调地址
        $recordcallback = Request::instance()->POST('recordcallback');
        //文档转换完回调地址
        $filenotifyurl = Request::instance()->POST('filenotifyurl');
        //帮助跳转地址
        $helpcallbackurl = Request::instance()->POST('helpcallbackurl');
        //企业Logo
        $ico = Request::instance()->POST('ico');
        //数据区域缺省图片
        $dataregionimgl = Request::instance()->POST('dataregionimg');*/
        //获取企业id
        $setupobj = new SetupManage;
        $result = $setupobj ->editCompanyInfo($currcompanyid,$post);
        $this->ajaxReturn($result);// 处理接口跨域问题，代码执行过程中不能有任何输出

    }
    /**
     * 权限管理 新增人员
     * @Author yr
     * @param  companyid  www域填1
     * @param  userid  添加的时候天0 修改时候必填，填大于0 用户userid
     * @param  userroleid 用户角色id
     * @param  account 账号名称
     * @param  firstname 名字
     * @param  userpwd 密码
     * @param  againpwd 确认密码
     * @param  mobile 手机号
     * @param  email 邮箱
     * @param  description 简介
     * @param  logo 头像logo
     * @param  method 1执行添加2执行修改
     * @param  oldsortid 原来的排序id,添加的时候填写0,编辑的时候不为0
     * POST | URL:/admin/Setup/addDepartment
     * return
     */
    public function addOrEditUserinfo(){
        //获取post数据
        $post = Request::instance()->POST(false);
        $setupobj  = new SetupManage;
        $result = $setupobj->addOrEditUserinfo($post);
        $this->ajaxReturn($result);
    }


    //4巡检 12管理员 13销售 14财务  15销售主管
    /**
     * 权限管理 //获取人员列表
     * @Author zzq
     * @param  companyid  www域填1
     * @param  pagenum  当前页码
     * @param  name 账号或者姓名
     * POST | URL:/admin/Setup/getUserList
     * return
     */
    public function getCompanyUserList(){
        $data = Request::instance()->POST(false);
        $data['pagenum'] = !empty($data['pagenum']) ? $data['pagenum'] : 1;
        $obj = new SetupManage;
        $dataReturn = $obj->getCompanyUserList($data);
        $this->ajaxReturn($dataReturn);//
    }

    
    /**
     * 权限管理 //获取人员信息详情
     * @Author zzq
     * @param  $userid  用户id
     * POST | URL:/admin/Setup/getUserDetail
     * return
     */
    public function getUserDetail(){
        $data = Request::instance()->POST(false);
        $obj = new SetupManage;
        $dataReturn = $obj->getUserDetail($data);
        $this->ajaxReturn($dataReturn);//
    }

    /**
     * 权限管理 //删除管理人员
     * @Author zzq
     * @param  $companyid  企业id
     * @param  $userid  用户id
     * POST | URL:/admin/Setup/delUser
     * return
     */
    public function delUser(){
        $data = Request::instance()->POST(false);
        $obj = new SetupManage;
        $dataReturn = $obj->delUser($data);
        $this->ajaxReturn($dataReturn);//
    }

    
    /**
     * 权限管理 //获取某销售人员已关联|未关联的企业
     * @Author zzq
     * @param  $companykeyword  企业名称或者ID(搜索关键字)
     * @param  $userid  销售用户id
     * @param  $bindtype  1表示已经绑定的|2表示没有被绑定的
     * POST | URL:/admin/Setup/getBindOrUnbindCompoanyList
     * return
     */
    public function getBindOrUnbindCompoanyList(){
        $data = Request::instance()->POST(false);
        $data['pagenum'] = !empty($data['pagenum']) ? $data['pagenum'] : 1;
        $obj = new SetupManage;
        $dataReturn = $obj->getBindOrUnbindCompoanyList($data);
        $this->ajaxReturn($dataReturn);//        
    }

    /**
     * 权限管理 //设置某销售人员关联某个企业
     * @Author zzq
     * @param  $companyid  需要绑定的企业的id
     * @param  $marketid  销售用户id
     * POST | URL:/admin/Setup/bindCompany
     * return
     */
    public function bindCompany(){
        $data = Request::instance()->POST(false);
        $obj = new SetupManage;
        $dataReturn = $obj->bindCompany($data);
        $this->ajaxReturn($dataReturn);//
    }

    /**
     * 权限管理 //设置取消关联某个企业(可批量操作)
     * @Author zzq
     * @param  $companyids  需要绑定的企业的id的集合,比如1,2,3
     * @param  $marketid  销售用户id
     * POST | URL:/admin/Setup/batchUnbindCompany
     * return
     */
    public function batchUnbindCompany(){
        $data = Request::instance()->POST(false);
        $obj = new SetupManage;
        $dataReturn = $obj->batchUnbindCompany($data);
        $this->ajaxReturn($dataReturn);//
    }

    
    /**
     * 权限管理 //获取某个销售主管的,关联和未关联的销售人员
     * @Author zzq
     * @param  $companyid  企业id
     * @param  $marketleaderid  销售用户id
     * @param  $bindtype  1表示已经绑定的|2表示没有被绑定的
     * POST | URL:/admin/Setup/getBindOrUnbindSaleManagerList
     * return
     */
    public function getBindOrUnbindSaleManagerList(){
        $data = Request::instance()->POST(false);
        $data['pagenum'] = !empty($data['pagenum']) ? $data['pagenum'] : 1;
        $obj = new SetupManage;
        $dataReturn = $obj->getBindOrUnbindSaleManagerList($data);
        $this->ajaxReturn($dataReturn);
    }

    /**
     * 权限管理 //修改某个销售主管与销售的关联关系(可批量处理)
     * @Author zzq
     * @param  $companyid  企业id
     * @param  $marketleaderid  销售主管的id(以此为主体)
     * @param  $bindmarketids  需要绑定的销售用户id的集合
     * @param  $unbindmarketids  需要解绑的销售用户id的集合
     * POST | URL:/admin/Setup/bindOrUnbindSaleManager
     * return
     */
    public function bindOrUnbindSaleManager(){
        //将marketids拆分
        $data = Request::instance()->POST(false);
        $obj = new SetupManage;
        $dataReturn = $obj->bindOrUnbindSaleManager($data);
        $this->ajaxReturn($dataReturn);//
    }

}
