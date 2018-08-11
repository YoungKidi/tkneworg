<?php

namespace app\enterprise\business;
use app\enterprise\model\Room;
use app\enterprise\model\Roomfile;
class RoomManage {
	 /**
     *获取小班课房间列表
     * @Author WangChen
     * @param  $arr_where 搜索条件
     * @param  $arr_field 查询字段
     * @param  $arr_data['page'] 访问的页面
     * @return array
     * 
     */
    public function getRoomList($arr_data){
        $arr_where = [];
        if (!empty($arr_data['roomname'])) {
                $arr_where['roomname'] = $arr_data['roomname'];
        }
        if (!empty($arr_data['serial'])) {
                $arr_where['serial'] = $arr_data['serial'];
        }
        //教室类型 （0：普通教室   1：缺省唯一教室  2:即时教室 3:日周期性教室  4:周的   5:两周的   6:月的 11:直播教室 ppt+音频  12: 直播 ppt+音视频  13: 直播 音视频）
        $arr_where['roomtype'] =array('LT','10');

        $arr_where['companyid'] = $arr_data['companyid'];
        //获取分页信息
        $arr_return_data['pageinfo'] = $this->getRoomPage($arr_data['page'],$arr_where);

        //当前页面
        $page = $arr_return_data['pageinfo']['now_page'];
        // 计算起始位置
        $arr_page['page'] = $page>0?($page-1) * $arr_return_data['pageinfo']['size']:0;
        $arr_page['size'] = $arr_return_data['pageinfo']['size'];
        //查询字段
        $arr_field = "serial,roomname,roomtype,starttime,endtime,roomstate";//返回时间戳格式
        // $arr_field = "serial,roomname,roomtype,FROM_UNIXTIME(starttime) as starttime,FROM_UNIXTIME(endtime) as  endtime,roomstate";//返回Y-m-d H:i:s 时间格式
        $room_model = new Room;
        $arr_return_data['data'] = $room_model->getRoomList($arr_where,$arr_field,$arr_page);
        return return_format($arr_return_data,0,lang('success'));
    }

    /**
    * 获取方便列表列表分页信息
    * @Author WangChen
    * @param  int $page 
    * @param  int $arr_where  搜索条件
    * @return array
    */
    public function getRoomPage($page,$arr_where){
        $room_model = new Room;
        //获取总数据数
        $int_room_number = $room_model->getRoomCount($arr_where);
        //总数据量

        $arr_page['sum_data'] = $int_room_number;
        //获取每页显示条数
        $int_size = $arr_page['size'] = config('pagesize.enterprise_roomlist');
        //计算总页数
        $arr_page['sum_page'] = ceil($int_room_number/$int_size);
        //计算上一页
        $arr_page['prev_page'] = $page-1<0?1:$page-1;
        //计算下一页
        $arr_page['next_page'] = $page+1>$arr_page['sum_page']?$arr_page['sum_page']:$page+1;
        if($page < $arr_page['prev_page']){
            $arr_page['now_page'] = (int)$arr_page['prev_page'];
        } else if($page > $arr_page['next_page']){
            $arr_page['now_page'] = (int)$arr_page['next_page'];
        } else {
            $arr_page['now_page'] = (int)$page;
        }
        return $arr_page;
    }

    /**
    * 获取教室详细信息
    * @Author WangChen
    * @param  $arr_where  搜索条件
    */
   
    public function getRoomInfo($arr_where){
        $arr_field = 'serial,roomname,roomtype,starttime,endtime,chairmanpwd,confuserpwd,assistantpwd,patrolpwd,sidelineuserpwd,videotype,videoframerate,livebypass,passwordrequired';
        $room_model = new Room;
        $result = $room_model->getRoomInfo($arr_where,$arr_field);
        //教师、学员地址
        $host = GetServerAddr();
            $temppos = strpos($host, '.');
            if( $temppos!==false )
            {
                $host = config('config.ServerConf')['urlhead'].substr($host, $temppos);
            }
        //老师地址
        $result['teacher_url']=$host.$arr_where['serial'].'/'.$arr_where['companyid'].'/1/0';
        //学生地址
        $result['confuser_url']=$host.$arr_where['serial'].'/'.$arr_where['companyid'].'/'.$result['passwordrequired'].'/2';
        //旁听地址（直播地址）
        if($result['livebypass']==1&&$result['sidelineuserpwd']!=''){ $param = 1; }else{ $param = 1; }
        $result['livebypass_url']=$host.$arr_where['serial'].'/'.$arr_where['companyid'].'/'.$param.'/99';
        unset($result['passwordrequired']);
        unset($result['livebypass']);
        if($result){
            return return_format($result,0,lang('success'));
        }else{
            return return_format('',70016,lang('data_select_error'));
        }
    }   


    /**
    * 获取教室关联id
    * @Author WangChen
    * @param  $arr_where  搜索条件
    */
   
    public function getRoomRelationFile($arr_where){
        if(empty($arr_where['rf.serial'])){
            return return_format('',70017,lang('select_param_error'));
        }
        $roomfile_model = new Roomfile;
        $result = $roomfile_model->getRoomRelationFile($arr_where);
        if($result){
            return return_format($result,0,lang('success'));
        }else{
            return return_format('',70016,lang('data_select_error'));
        }
    }

} 