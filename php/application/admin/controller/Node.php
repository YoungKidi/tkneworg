<?php
namespace app\admin\controller;

use think\Controller;
use app\admin\business\NodeManage;

/**
 * @name 公共控制器
 */
class Node extends Controller
{
    /**
     * 批量导入节点
     * @auther 胡博森
     */
    public function autoNodeAdd(){
        require  "./../application/admin/api/ZhaoZQ.php";
        require  "./../application/admin/api/HuBS.php";
        require  "./../application/admin/api/WangC.php";

        $arr_api = array_merge($ZhaoZQ,$HuBS,$WangC);
        $obj_node = new NodeManage();
        $arr_return = $obj_node->autoNodeAdd($arr_api);
        return $this->ajaxReturn($arr_return);
    }

    /**
     * 添加节点
     * @auther 胡博森
     * @param string $module 模块名称
     * @param string $controller 控制器名称
     * @param string $function 方法名称
     * @param string $apiexplain 接口分类说明
     * @param string $particular 接口详细说明
     */
    public function setNodeAdd(){
        $arr_data['module'] = input('post.module','',['trim','strtolower']);
        $arr_data['controller'] = input('post.controller','',['trim','ucfirst']);
        $arr_data['function'] = input('post.function','','trim');
        $arr_data['apiexplain'] = input('post.apiexplain','','trim');
        $arr_data['particular'] = input('post.particular','','trim');
        $obj_node = new NodeManage();
        $arr_return = $obj_node->setNodeAdd($arr_data);
        return $this->ajaxReturn($arr_return);
    }

    /**
     * 删除节点数据 优先根据节点id删除
     * @auther 胡博森
     * @param int $node_id 要删除的节点id
     * @param string $module 模块名称
     * @param string $controller 控制器名称
     * @param string $function 方法名称
     */
    public function setNodeDel(){
        $arr_data['id'] = input('post.node_id','','trim');
        $arr_data['module'] = input('post.module','',['trim','strtolower']);
        $arr_data['controller'] = input('post.controller','',['trim','ucfirst']);
        $arr_data['function'] = input('post.function','','trim');
        $obj_node = new NodeManage();
        $arr_return = $obj_node->setNodeDel($arr_data);
        return $this->ajaxReturn($arr_return);
    }
    /**
     * 查询节点列表
     * @auther 胡博森
     */
    public function getNodeList(){
        $arr_data['id'] = input('post.node_id','','trim');
        $arr_data['module'] = input('post.module','',['trim','strtolower']);
        $arr_data['controller'] = input('post.controller','',['trim','ucfirst']);
        $arr_data['function'] = input('post.function','','trim');
        $obj_node = new NodeManage();
        $arr_return = $obj_node->getNodeList($arr_data);
        return $this->ajaxReturn($arr_return);
    }

    /**
     * 修改企业节点
     * @auther 胡博森
     */
    public function setNodeUpd(){
        $arr_where['id'] = input('post.node_id','','trim');
        $arr_data['module'] = input('post.module','',['trim','strtolower']);
        $arr_data['controller'] = input('post.controller','',['trim','ucfirst']);
        $arr_data['function'] = input('post.function','','trim');
        $arr_data['apiexplain'] = input('post.apiexplain','','trim');
        $arr_data['particular'] = input('post.particular','','trim');
        $arr_data['state'] = input('post.state','','trim');
        $obj_node = new NodeManage();
        $arr_return = $obj_node->setNodeUpd($arr_where,$arr_data);
        return $this->ajaxReturn($arr_return);
    }
}