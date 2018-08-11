<?php
/**企业模板皮肤3.0**/
namespace app\admin\business;

use app\admin\model\Companyskin;
use think\Db;

class CompanySkinManage{

    /**
     * 获取企业的皮肤
     * @param $arr_where
     * @return array
     */
    public function getCompanySkin($arr_where){
        $arr_field = ['id','roomType','clientType','skinId','tplId'];
        $obj_skin = new Companyskin;
        $arr_skin = $obj_skin->getSkinList($arr_where,$arr_field);
        return $this->disposeSkin($arr_skin);
    }

    /**
     * 修改企业皮肤
     * @param $arr_where
     */
    public function updCompanySkin($arr_where,$arr_data){
        if(empty($arr_data)) return false;
        foreach($arr_data as $k => $v) {
            foreach($v as $ks=> $vs) {
                if($ks == 'mid') continue;
                if($ks == 'pc') {
                    $clientType = 1;
                    $skin_id = $vs;
                }
                if($ks == 'android'){
                    $clientType = 2;
                    $skin_id = $vs;
                }
                if($ks == 'pad'){
                    $clientType = 3;
                    $skin_id = $vs;
                }
                $arr_new_templates[] = [
                    'companyId'=>$arr_where['companyid'],
                    'roomType' => $k,
                    'tplId' => $v['mid'],
                    'createTime'=>time(),
                    'skinId'=>$skin_id,
                    'clientType'=>$clientType,
                ];
            }
        }
        $obj_skin = new CompanySkin;
        Db::startTrans();
        try{
            $int_skin_del = $obj_skin->delCompanySkin($arr_where);
            $int_skin_add = $obj_skin->addCompanySkin($arr_new_templates);
            if(!$int_skin_add ) throw new \Exception("error", 1);
            Db::commit();
            return true;
        } catch(\Exception $e){
            Db::rollback();
            return false;
        }

    }

    /**
     * 处理皮肤列表
     * @param $arr_data
     * @return array
     */
    private function disposeSkin($arr_data){
        $arr_new_data = [];
        foreach($arr_data as $k => $v){
            if($v['clientType'] == 1){
                $arr_new_data[$v['roomType']][$v['tplId']]['pc'] = $v['skinId'];
            }else if($v['clientType'] ==2){
                $arr_new_data[$v['roomType']][$v['tplId']]['android'] = $v['skinId'];
            }else if($v['clientType'] == 3){
                $arr_new_data[$v['roomType']][$v['tplId']]['pad'] = $v['skinId'];

            }
        }
        return $arr_new_data;
    }

    /**
     * 同步子企业3.0皮肤
     * @param $arr_where
     * @param $arr_data
     */
    public function synchronizationSkin($arr_where,$arr_son){

            //查询企业皮肤信息
            $obj_skin = new CompanySkin;
            $arr_field = ['companyId','roomType','clientType','skinId','tplId'];
            $arr_skin = $obj_skin->getSkinList($arr_where,$arr_field);
            $arr_new_skin = [];
            $arr_skin_where['companyId'] = ['in',$arr_son];
            foreach($arr_son as $ks => $vs){
                foreach($arr_skin as $k => $v){
                    $v['companyId'] = $vs;
                    $arr_new_skin[] = $v;
                }
            }
            $int_skin_del = $obj_skin->delCompanySkin($arr_skin_where);
            $int_skin_add = $obj_skin->addCompanySkin($arr_new_skin);
            return true;
    }

}