<?php
namespace app\enterprise\model;
use think\Model;
use think\Db;
/*
 * 会议房间记录表
 *
*/
class Room extends Model
{
	protected $table = 'room';
    /**
     * [getRoomList 获取小班课基本信息]
     * @author wangchen
     * @DateTime 2018-08-09
     * @param  $arr_where where搜索 
     * @param  $arr_field 查询字段
     * LEFT 用户帐号表  useraccount; department 部门信息表
     */
    public function getRoomList($arr_where,$arr_field=['*'],$arr_page){
         $result  = $this
            ->field($arr_field)
            ->where($arr_where)
            ->limit($arr_page['page'],$arr_page['size'])
            ->select();
        return $result;        
    }

    /**
     * [getRoomCount  获取小班课总数]
     * @author wangchen
     * @param  $arr_where where搜索 * 
     * @DateTime 2018-08-09
     * @return int
     */
    public function getRoomCount($arr_where=''){
        $result = $this->where($arr_where)->count();
        return $result;
    }

    /**
     * [getRoomInfo  获取教室详细信息]
     * @author wangchen
     * @param  $arr_where where搜索 
     * @param  $arr_field 查询字段
     * @DateTime 2018-08-10
     * @return int
     */
    public function getRoomInfo($arr_where='',$arr_field=['*']){
        $result =$this->field($arr_field)->where($arr_where)->find();
        return $result;
    }


}