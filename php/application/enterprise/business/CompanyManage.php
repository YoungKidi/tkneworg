<?php
namespace app\enterprise\business;
use think\Validate;
use RedisClient;
use think\Db;
use app\enterprise\model\Company;
use app\enterprise\business\UserloginManage;
class CompanyManage{

	/**
     *	企业配置
     *	@author wangchen
     *	@param  $companyid 公司id
     *	@return array
     */
	public function getCompanyInfo($companyid){
		// 获取企业信息
        $company_model = new Company;
        $company_info = $company_model->getcompanyInfo($companyid);
        return return_format($company_info,0,lang('success'));
    }
    /**
     *  修改企业名称
     *  @author 汪子龙
     *  @param  $companyid 公司id
     */
    public function editCompanyName($companyid,$post){
        //获取公司id 公司id暂且写死
        $companymodel = new Company;
        $update_result = $companymodel->updateInfo($companyid,$post);
        if($update_result>=0){
            return return_format('',0,lang('success'));
        }else{
            return return_format('',30000,lang('error'));
        }
    }
    /**
     *  查看企业开发配置
     *  @author 汪子龙
     *  @param  $companyid 公司id
     */
    public function getCompanySetInfo($companyid){
        $companymodel = new Company;
        //获取企业开发配置信息
        $field = "authkey,companytitle,functionitem,roomstartcallbackurl,callbackurl,logincallbackurl,recordcallback,filenotifyurl,helpcallbackurl";
        $company_setinfo = $companymodel->getCompanySetInfo($companyid,$field);
        //取出functionitem的100字段中第31个字段并删除functionitem字段
        $company_setinfo['chk_automatic_recorde'] = $company_setinfo['functionitem'][31];
        unset($company_setinfo['functionitem']);
        return return_format($company_setinfo,0,lang('success'));
    }
    /**
     *  编辑企业开发配置
     *  @author 汪子龙
     *  @param  $companyid 公司id
     */
    function editCompanySetInfo($companyid,$post){
        //获取当前公司id 暂且写死
        $companymodel = new Company;
        
        $info = $companymodel->getcompanySetInfo($companyid,'functionitem');
        //查到字段functionitem所有的值
        $info['functionitem'][31] = $post['chk_automatic_recorde'];
        //接收的chk_automatic_recorde替换成functionitem的第31个配置
        $post['functionitem'] = $info['functionitem'];
        //换成post的
        $update_result = $companymodel->editCompanySetInfo($companyid,$post);
        //修改
        if ($update_result>0) {
            return return_format('',0,lang('success'));
        } else {
            return return_format('',30000,lang('error'));
        }
        
    }
}
?>