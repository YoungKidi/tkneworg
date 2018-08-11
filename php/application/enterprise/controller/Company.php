<?php
namespace app\enterprise\controller;
use app\enterprise\business\CompanyManage;
use think\Request;
use think\Controller;
/**
 * 企业模块
 * author wangchen
 */

class Company extends Controller{
	//自定义初始化
    protected function _initialize() {
        parent::_initialize();
    }

    /**
     * 查看企业基本信息接口
     * @Author wangchen
     * @param $companyid 公司登陆id
     * POST | URL:/enterprise/company/getCompanyInfo
     */
    public function getCompanyInfo(){
        //获取当前登录的公司id 测试id=1 存储session
        $companyid = 1;
        //获取当前公司基本信息
        $company_obj = new CompanyManage;
        $result = $company_obj->getcompanyInfo($companyid);
        $this->ajaxReturn($result);
    }
    /**
     * 修改企业名称
     * @Author 汪子龙
     * @param $companyid 公司登陆id
     * POST | URL:/enterprise/company/editCompanyName
     */
    public function editCompanyName(){
        //获取当前登录的公司id 测试写死id=1
        $companyid = 1;
        $post = Request::instance()->POST(false);
        $obj_setup = new CompanyManage;
        $result = $obj_setup -> editCompanyName($companyid,$post);
        $this->ajaxReturn($result);
    }
    /**
     * 查看企业开发配置接口
     * @Author 汪子龙
     * POST | URL:/enterprise/Company/getCompanySetInfo
     */
    public function getCompanySetInfo(){
        //获取当前登录的公司id
        $companyid = 1;
        //获取当前公司基本信息
        $obj_setinfo = new CompanyManage;
        $result = $obj_setinfo -> getCompanySetInfo($companyid);
        $this->ajaxReturn($result);
    }
    /**
     * 修改企业开发配置接口
     * @Author 汪子龙
     * POST | URL:/enterprise/Company/editCompanySetInfo
     */
    public function editCompanySetInfo()
    {
        //获取当前登录的公司id
        $companyid = 1;
        $post = Request::instance()->POST(false);
        $obj_editinfo = new CompanyManage;
        $result = $obj_editinfo -> editCompanySetInfo($companyid,$post);
        $this->ajaxReturn($result);
    }
}
?>