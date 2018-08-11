<?php
/**
 * 房间管理
 * @author wangchen
 * @date 18-08-09
 * 
 */
namespace app\enterprise\controller;
use think\Controller;
use think\Request;
use app\enterprise\business\RoomManage;
class Room extends Controller {
	// 公司登陆id
	private $companyid = '';
	//自定义初始化
	protected function _initialize() {
		parent::_initialize();
		// 测试id
		$this->companyid = 10626;
	}


	//测试接口
	public function test(){
		
	}	

	/**
	 * [getRoomList 获取企业所创建教室列表信息]
	 * @param  $companyid 企业编号
	 * @param  $page  所需跳转页面 默认第一页 20每页
	 * @return [array] [分页列表数据]
	 * POST | URL:/enterprise/room/getRoomList
	 */
	public function getRoomList(){
		$arr_data['serial'] = input('serial')?:'';
		$arr_data['roomname'] = input('roomname')?:'';
		$arr_data['companyid'] = $this->companyid;
		$arr_data['page'] = input('page')?: 1;
		$room_obj = new RoomManage;
		$result = $room_obj->getRoomList($arr_data);
		$this->ajaxReturn($result);
	}

	/**
	 * [getRoomInfo 获取教室详细信息]
	 * @param  $serial 教室ID
	 * @return [array] [返回数据信息]
	 * POST | URL:/enterprise/room/getRoomInfo
	 */
	public function getRoomInfo(){
		$arr_data['serial'] = input('serial')?:'';
		$arr_data['companyid'] = $this->companyid;
		$room_obj = new RoomManage;
		$result = $room_obj->getRoomInfo($arr_data);
		$this->ajaxReturn($result);
	}

	/**
	 * [getRoomRelationFile 查看教室关联课件]
	 * @param  $serial 教室ID
	 * @return [array] [返回数据信息]
	 * POST | URL:/enterprise/room/getRoomRelationFile
	 */
	public function getRoomRelationFile(){
		$arr_data['rf.serial'] = input('serial')?:'';
		$room_obj = new RoomManage;
		$result = $room_obj->getRoomRelationFile($arr_data);
		$this->ajaxReturn($result);
	}

	/**
	 * [getRoomRelationFile 查看教室回放]
	 * @param  $serial 教室ID
	 * @return [array] [返回数据信息]
	 * POST | URL:/enterprise/room/getRoomRelationFile
	 */
	public function getRoomRelationFile(){
		$arr_data['rf.serial'] = input('serial')?:'';
		$room_obj = new RoomManage;
		$result = $room_obj->getRoomRelationFile($arr_data);
		$this->ajaxReturn($result);
	}
}