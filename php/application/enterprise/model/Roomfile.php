<?php
namespace app\enterprise\model;
use think\Model;
use think\Db;
/*
 * 教室文件关联表
 *
*/
class Roomfile extends Model
{
    protected $table = 'roomfile';
    /**
     * [getRoomRelationFile  获取教室关联文件]
     * @author wangchen
     * @param  $arr_where where搜索 * 
     * @DateTime 2018-08-11
     * @return array
     */
    public function getRoomRelationFile($arr_where){
        $field='rf.serial,rf.fileid,f.filename,f.status';
        $result = Db::table($this->table .' rf')->field($field)->join('fileinfo f','f.fileid = rf.fileid','LEFT')->where($arr_where)->select();
        return $result;

    }
}