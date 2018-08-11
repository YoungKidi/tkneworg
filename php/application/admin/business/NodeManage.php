<?php
/**企业管理设置**/
namespace app\admin\business;
use think\Validate;
use app\admin\model\Adminaccessnode;
class NodeManage{

    /**
     * 导入节点数据
     * @auther 胡博森
     * @param $arr_data
     */
    public function autoNodeAdd($arr_data){
        $arr_add = [];
        foreach($arr_data as $k => $v){
            list(,$module,$controller,$function) = explode('/',$v['url']);
            $arr_declare = explode('=>',$v['name']);
            if(isset($arr_declare[3])){
                $str_declare = $arr_declare[1].'('.$arr_declare[2].'('.$arr_declare[3].'))';
            }else{
                if(isset($arr_declare[2])){
                    $str_declare = $arr_declare[1].'('.$arr_declare[2].')';
                }else{
                    $str_declare = $arr_declare[1];
                }
            }
            $str_apiexplain = $arr_declare[0];
            $arr_add[] = [
                'module' => $module,
                'controller' => $controller,
                'function' => $function,
                'apiexplain' => $str_apiexplain,
                'particular' => $str_declare,
                'ctime' => time(),
            ];
        }
        $obj_node = new Adminaccessnode();
        $int_add = $obj_node->setNodeAddAll($arr_add);
        if(!$int_add){
            return return_format('',60521,lang('error'));
        }
        return return_format('',0,lang('success'));
    }

    /**
     * 添加节点
     * @auther 胡博森
     * @param array $arr_data 添加的节点数据
     * @return array
     */
    public function setNodeAdd($arr_data){
        $bool_check = $this->verifyData($arr_data);
        if(!$bool_check){
            return return_format('',60411,lang('node_data_error'));
        }

        $obj_node = new Adminaccessnode();
        $arr_where = ['function'=>$arr_data['function'],'controller'=>$arr_data['controller'],'module'=>$arr_data['module']];
        //查询节点是否存在
        $arr_node = $obj_node->getNodeOne($arr_where,['id']);
        if($arr_node){
            return return_format('',60411,lang('node_exist'));
        }
        $arr_data['ctime'] = time();
        $int_add = $obj_node->setNodeAddOne($arr_data);
        if(!$int_add){
            return return_format('',60521,lang('node_add_error'));
        }
        return return_format('',0,lang('success'));
    }

    /**
     * 删除节点
     * @auther 胡博森
     * @param $arr_data
     */
    public function setNodeDel($arr_data){
        $arr_where = [];
        if($arr_data['id']){
            $arr_where['id'] = $arr_data['id'];
        }else{
            $arr_rule = [
                'module' => 'require',
                'controller' => 'require',
                'function' => 'require',
            ];
            $obj_validate = new Validate($arr_rule);
            $bool_check = $obj_validate->check($arr_data);
            if(!$bool_check){
                return return_format('',60411,lang('node_data_del_error'));
            }
            $arr_where = ['function'=>$arr_data['function'],'controller'=>$arr_data['controller'],'module'=>$arr_data['module']];
        }
        $obj_node  = new Adminaccessnode();
        $int_del = $obj_node->setNodeDel($arr_where);
        if(!$int_del){
            return return_format('',60521,lang('node_del_error'));
        }
        return return_format('',0,lang('success'));
    }
    /**
     * 查询节点数据
     * @auther 胡博森
     */
    public function getNodeList($arr_data){
        $arr_where = [];
        if($arr_data['id'])$arr_where['id'] = $arr_data['id'];
        if($arr_data['module'])$arr_where['module'] = $arr_data['module'];
        if($arr_data['controller'])$arr_where['controller'] = $arr_data['controller'];
        if($arr_data['function'])$arr_where['function'] = $arr_data['function'];
        $arr_field = ['id','module','controller','function','apiexplain','particular','ctime','state'];
        $obj_node = new Adminaccessnode();
        $arr_list = $obj_node->getNodeList($arr_where,$arr_field);
        return return_format($arr_list,0,lang('success'));
    }

    /**
     * 修改企业节点
     */
    public function setNodeUpd($arr_where,$arr_data){
        if(!$arr_where['id']){
            return return_format('',60411,lang('node_data_upd_error'));
        }
        $arr_new_data = [];
        if($arr_data['module'])$arr_new_data['module'] = $arr_data['module'];
        if($arr_data['controller'])$arr_new_data['controller'] = $arr_data['controller'];
        if($arr_data['function'])$arr_new_data['function'] = $arr_data['function'];
        if($arr_data['apiexplain'])$arr_new_data['apiexplain'] = $arr_data['apiexplain'];
        if($arr_data['particular'])$arr_new_data['module'] = $arr_data['particular'];
        if($arr_data['state'])$arr_new_data['state'] = $arr_data['state'];
        if(empty($arr_new_data)) return return_format('',60411,lang('node_data_upd_error'));
        $obj_node = new Adminaccessnode();
        $int_upd = $obj_node->setNodeUpd($arr_where,$arr_data);
        if(!$int_upd){
            return return_format('',60521,lang('node_upd_error'));
        }
        return return_format('',0,lang('success'));
    }




    /**
     * 验证节点数据
     * @auther 胡博森
     */
    private function verifyData($arr_data){
        $arr_rule = [
            'module' => 'require',
            'controller' => 'require',
            'function' => 'require',
            'apiexplain' => 'require',
            'particular' => 'require',
        ];
        $obj_validate = new Validate($arr_rule);
        return $bool_check = $obj_validate->check($arr_data);
    }
}

