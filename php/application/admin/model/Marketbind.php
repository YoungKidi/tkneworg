<?php
namespace app\admin\model;
use think\Model;
use think\Db;
use think\Validate;
class Marketbind extends Model
{
    //销售主管与销售绑定表
    protected $table = 'marketbind';
    protected $rule = [
            'marketid' => 'require|number',
            'marketleaderid'   => 'require|number',
        ];
    protected $message = [
            'marketid.require' => '销售id必须填写',
            'marketid.number' => '销售id必须是数字',
            'marketleaderid.require' => '销售主管id必须填写',
            'marketleaderid.number' => '销售主管id必须是数字',
        ];

    
    /**
     * [getAllbindMarketid //查出所有的已经被绑定的企业的companyid的数组]
     * @author zzq
     * @DateTime 2018-07-28
     * @param    [array]                   $where   [搜索条件]
     */
    public function getAllbindMarketid(){
        $res = Db::table($this->table)
            ->alias('a')
            ->column('a.marketid'); 
        return $res;        
    }
    /**
     * [getAllbindMarketidByLeader 查看某个销售主管下所有的销售的id]
     * @author zzq
     * @DateTime 2018-07-28
     * @param    [int]                   $marketleaderid   [销售主管]
     */
    public function getAllbindMarketidByLeader($marketleaderid){
        $res = Db::table($this->table)
            ->alias('a')
            ->where('a.marketleaderid','EQ',$marketleaderid)
            ->column('a.marketid'); 
        return $res;         
    }

    /**
     * [getBindByMarketidAndMarketLeaderid //查看某个销售和某个销售主管是否关联有关联的关系]
     * @author zzq
     * @DateTime 2018-07-27
     * @param    [array]                   $where   [筛选条件]
     * @param    [array]                            [查询结果]
     */
    public function getBindByMarketidAndMarketLeaderid($where){
        $field = "id,marketid,marketleaderid";              
        $res = Db::table($this->table)
        ->where($where)
        ->find();
        return $res;
    }


    //添加销售和销售主管的关联
    public function addMarketBindLeader($data){
        //校验参数
        $validate = new Validate($this->rule, $this->message);
        if( !$validate->check($data) ){
            return return_format('',30006,$validate->getError());
        }else{
            //添加数据
            $res = Db::table($this->table)->insert($data);
            return $res;
        }        
        
    }
    //删除销售和企业关联关系
    public function deleteMarketBindLeader($marketleaderid,$marketids){
        try{
            $res = Db::table($this->table)->where('marketid','IN',$marketids)->where('marketleaderid','EQ',$marketleaderid)->delete();
            if($res !== false){
                return return_format('',0,'删除成功');
            }else{	
            	return return_format('',30016,lang('delete_error'));
            }
        }catch(\Exception $e){
            return return_format('',30016,$e->getMessage());
        }
    }
}
