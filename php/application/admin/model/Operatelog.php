<?php
namespace app\admin\model;
use think\Model;
use think\Db;
class Operatelog extends Model
{
    //channel 暂且不了解业务
    protected $table = 'operatelog';
    /**
     * [getOperateLogList  获取操作日志列表]
     * @author zzq
     * @DateTime 2018-08-10
     * @param    [array]                   $where    [查询条件]
     * @param    [int]                   $offset    [分页起始位置]
     * @param    [int]                   $pagesize  [每页条数]
     * @return   [array]                            [查询结果]
     */
    public function getOperateLogList($where,$pagenum,$pagesize){
        $field = '*' ;
        $data = Db::table($this->table)
                ->where($where)
                ->page($pagenum,$pagesize)
                ->select();
        // var_dump($this->getLastSql());
        // die;
        return $data;
    }

    /**
     * [getOperateLogListCount  获取操作日志数目]
     * @author zzq
     * @DateTime 2018-08-10
     * @param    [array]                   $where    [查询条件]
     * @return   [array]                            [查询结果]
     */
    public function getOperateLogListCount($where){
        $field = '*' ;
        $count = Db::table($this->table)
                ->where($where)
                ->count();
        //var_dump($this->getLastSql());
        return $count;
    }

    /**
     * [addOperateLog 添加操作日志]
     * @author yr
     * @DateTime 2018-08-10
     */
    public function addOperateLog($data)
    {
        $result = Db:: table($this->table)->insert($data);
        return $result;
    }
}