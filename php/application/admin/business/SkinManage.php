<?php
/**模板列表3.0**/
namespace app\admin\business;

use app\admin\model\Skin;

class SkinManage
{
    /**
     * 获取企业3.0模板
     * @param $company_id
     * @return array
     */
    public function getSkinList($company_id=0){
        if($company_id){
            $arr_where['t.companyId'] = ['in',[$company_id,0]];
            $arr_where['s.companyId'] = ['in',[$company_id,0]];
        }else{
            $arr_where['s.companyId'] = 0;
            $arr_where['t.companyId'] = 0;
        }
        $arr_field = ['s.id'=>'sid','t.id'=>'tid','s.name'=>'sname','s.tplId','s.clientType','t.name'=>'tname','t.roomType'];
        $obj_skin = new Skin;
        $arr_data = $obj_skin->getSkinLists($arr_where,$arr_field);
        $arr_new_data = [];

        foreach($arr_data as $k => $v) {
            $arr_type = explode(',', $v['roomType']);
            foreach ($arr_type as $tk => $tv) {
                if (empty($v)) continue;
                $arr_new_data[$tv][$v['tid']]['id'] = $v['tid'];
                $arr_new_data[$tv][$v['tid']]['name'] = $v['tname'];
                if ($v['clientType'] == 1) {
                    $arr_new_data[$tv][$v['tid']]['data']['pc'][] = ['id' => $v['sid'], 'name' => $v['sname']];
                }
                if ($v['clientType'] == 2) {
                    $arr_new_data[$tv][$v['tid']]['data']['android'][] = ['id' => $v['sid'], 'name' => $v['sname']];
                }
                if ($v['clientType'] == 3) {
                    $arr_new_data[$tv][$v['tid']]['data']['pad'][] = ['id' => $v['sid'], 'name' => $v['sname']];
                }
            }
        }
        return $arr_new_data;
    }
}