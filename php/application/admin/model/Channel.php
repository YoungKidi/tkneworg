<?php
namespace app\admin\model;
use think\Model;
use think\Db;
class Channel extends Model
{
    //channel 暂且不了解业务
    protected $table = 'channel';
    /**
     * [addUserInfo 添加用户]
     * @author yr
     * @DateTime 2018-07-03
     * @param    [int]                   $companyid   [当前登录 的公司id]
     */
    public function addChannel($info){
        //先从表里删除
        Db::table($this->table)->where('type','eq',$info['type'])->where('channelid','eq',$info['channelid'])->where('userid','eq',$info['userid'])->delete();
        //拼装插入条件
        $data['type'] = $info['type'];
        $data['channelid'] = $info['channelid'];
        $data['userid'] = $info['userid'];
        $data['version'] = $info['version'];
        $result = Db::table($this->table)->insert($data);
        return $result;
    }

    /**
     * 添加信息
     */
    public function setChannelAdd($arr_data){
        return Db::table($this->table)->insert($arr_data);
    }
}