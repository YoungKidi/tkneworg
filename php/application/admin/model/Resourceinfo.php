<?php
namespace app\admin\model;
use think\Model;
use think\Db;
/*
 * 资源统计Model
 *
*/
class Resourceinfo extends Model
{
    protected $table = 'resourceinfo';

    public function getResourceinfo(){
        $field = "resourceid,startdate,expirydate,normalmaxpoint,sidelinemaxpoint,maxaudiofeeds,maxvideonum";
        $datainfo  = Db::table($this->table)
            ->field($field)
            ->find();
        return $datainfo;        
    }
}	
