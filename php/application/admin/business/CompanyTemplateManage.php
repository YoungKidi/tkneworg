<?php
/**企业模板皮肤**/
namespace app\admin\business;

use think\Db;
use app\admin\model\Companytemplate;

class CompanyTemplateManage{

    /**
     * 查询2.0企业对应的模板列表
     * @param array $arr_where 查询条件
     * return array
     */
    public function getCompanyTemplate($arr_where){
        $arr_field = ['roomtype','pclayout','padlayout','mobilelayout'];
        $obj_template = new Companytemplate;
        $arr_data =  $obj_template->getCompanyTemplate($arr_where,$arr_field);
        return $arr_data;
    }

    /**
     * 修改2.0模板
     * @param $arr_where
     * @param $arr_data
     */
    public function updCompanySkin($arr_where,$arr_data){
        foreach($arr_data as $k => $v){
            $arr_new_data[] = [
                'companyid' => $arr_where['companyid'],
                'roomtype'  =>  $k,
                'pclayout'  =>  $v['pc'],
                'mobilelayout'  =>  $v['android'],
                'padlayout'  =>  $v['pad'],
            ];
        }
        $obj_template = new Companytemplate;
        Db::startTrans();
        try{
            $int_template_del = $obj_template->delCompanyTemplate($arr_where);
            $int_template_add = $obj_template->addCompanyTemplate($arr_new_data);
            if(!$int_template_add ) throw new \Exception("error", 1);
            Db::commit();
            return true;
        } catch(\Exception $e){
            Db::rollback();
            return false;
        }
    }

    /**
     * 同步更新子企业皮肤
     */
    public function synchronizationTemplate($arr_where,$arr_son){
        //查询父企业皮肤
        $arr_field = ['roomtype','pclayout','padlayout','mobilelayout'];
        $obj_template = new Companytemplate;
        $arr_data =  $obj_template->getCompanyTemplate($arr_where,$arr_field);
        $arr_new_data = [];
        foreach($arr_son as $k => $v){
            foreach($arr_data as $ks => $vs){
                $vs['companyid'] = $v;
                $arr_new_data[] = $vs;
            }
        }
        $arr_template_where['companyid'] = ['in',$arr_son];
        $int_del = $obj_template->delCompanyTemplate($arr_template_where);
        $int_add = $obj_template->addCompanyTemplate($arr_new_data);
        return $int_add;
    }
}