<?php
/**
 * 房间管理
 * @author wyx
 * @date 18-06-27
 * 
 */
namespace app\admin\controller;
use think\Controller;
use think\Request;
use app\admin\business\RoomManage;
class Room extends Controller {
	//自定义初始化
	protected function _initialize() {
		parent::_initialize();
	}
    /**
     * 测试接口
     * @Author wyx
     * @param  coursename 课程名称
     * POST | URL:/admin/Room/test
     */
    public function test() {
        //实例化课程逻辑层
        echo 1234455;

    }
	/**
	 * 房间列表接口
	 * @Author wyx
	 * @param  pagenum  当前页码
	 * @param  coursename 课程名称
	 * POST | URL:/admin/Room/getRoomList
	 */
	public function getRoomList() {
		//实例化课程逻辑层
		$data = Request::instance()->POST(false);// 前端 直接传送 对象情况下，助手函数 和 $_POST 方式都无法接收
		$data['pagenum'] = isset($data['pagenum']) ? $data['pagenum'] : 1;
		
		$curriculum = new RoomManage;
		$dataReturn = $curriculum->getRoomList($data);
		$this->ajaxReturn($dataReturn);// 处理接口跨域问题，代码执行过程中不能有任何输出

	}

}
