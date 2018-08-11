<?php
/**模板列表**/
namespace app\admin\business;

use app\admin\model\Templateinfo;

class TemplateinfoManage{

	/**
	 * 查询模板列表
	 */
	public function getTemplateList(){
		$arr_where = [];
		$arr_field = ['templateid','templatename','templateeffect','pc','pad','phone'];
		$obj_template = new Templateinfo;
		$arr_template = $obj_template->getTemplateList($arr_where,$arr_field);
		if(empty($arr_template)){
			return return_format('',0,lang('Success'));
		}
		$arr_template = $this->classTemplage($arr_template);
		return $arr_template;

	}

	/**
	 * 对模板分类
	 * @param  array $arr_templage 需要处理的模板数组
	 * @return array
	 */
	public function classTemplage($arr_template){
		$arr_new_template = [];

		foreach($arr_template as $k => $v){
            $arr_new_temp['templateid'] = $v['templateid'];
            $arr_new_temp['templatename'] = $v['templatename'];
            if($v['pc'] == 1){ //支持pc
				$arr_new_template[$v['templateeffect']]['pc'][] = $arr_new_temp;
			}
			if($v['pad'] == 1){ //支持pad
                $arr_new_template[$v['templateeffect']]['pad'][] = $arr_new_temp;
			}
			if($v['phone'] == 1){ //支持移动端
                $arr_new_template[$v['templateeffect']]['phone'][] = $arr_new_temp;
			}
		}
		return $arr_new_template;
	}

}