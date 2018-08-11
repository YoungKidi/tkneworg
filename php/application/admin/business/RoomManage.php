<?php
/**
 * 对接网课端 对接业务逻辑
 */
namespace app\admin\business;
use app\admin\model\Room;
class RoomManage {
	/**
	 *	获取 教室列表 分页信息从 配置文件中读取
	 *	@author wyx
	 *	@param  $data array 
	 *	@return array 
	 */
	public function getRoomList($data){
		$pagesize = config('pagesize.admin_roomlist');//每页行数
		$pagenum  = $data['pagenum'] ;//当前页码
		$offset   = $data['pagenum'] > 0 ? ($data['pagenum'] - 1) * $pagesize : 0 ;// 计算起始位置

		$roomobj  = new Room ;
		$data     = $roomobj->getRoomList($offset,$pagesize);
		$total    = $roomobj->getRoomListCount();

		//返回数组组装
		$result = [
			 	'data'=>$data,// 内容结果集
			 	'pageinfo'=>[
			 		'pagesize'=> $pagesize ,// 每页多少条记录
			 		'pagenum' => $pagenum ,//当前页码
			 		'total'   => $total // 符合条件总的记录数
			 	]
			] ;
		return return_format($result,0) ;

	}


}

?>