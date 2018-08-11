<?php
namespace app\admin\model;
use think\Model;
use think\Db;
/**
*更新文件数据表
**/

class Updatefile extends Model
{
	protected $table = 'updatefile';
	/**
	 * [getUpdatefilePage  获取更新文件数据（分页）]
	 * @author wangchen
	 * @DateTime 2018-07-31
     * @param  $arr_where where搜索
     * @param  $arr_field 查找字段 
     * @param  $arr_page['page'] 当前页
     * @param  $arr_page['size'] 每页数量
	 */

	 public function getUpdatefilePage($arr_where,$arr_field=['*'],$arr_page){
        $result  = $this
            ->field($arr_field)
            ->where($arr_where)
            ->limit($arr_page['page'],$arr_page['size'])
            ->select();
        return $result;        
    }


    /**
     * [getUpdatefileCount  获取更新文件总数]
     * 获取更新文件总数
	 * @author wangchen
     * @param  $arr_where where搜索 * 
	 * @DateTime 2018-07-31
     * @return int
     */
    public function getUpdatefileCount($arr_where=''){
        $result = $this->where($arr_where)->count();
        return $result;
    }

    /**
     * [getUpdatefilePage  获取更新文件数据]
     * @author wangchen
     * @DateTime 2018-07-31
     * @param  $id 更新文件id
     * @return array
     */
    public function getUpdatefileInfo($arr_where,$field){
        $result = $this->field($field)->where($arr_where)->find();
        return $result;
    }

    /**
     * [setUpdatefileAdd  添加文件信息]
     * @author wangchen
     * @DateTime 2018-08-02
     * @param  $data 添加数据
     * @return array
     */
    public function setUpdatefileAdd($data){
        $result = $this->save($data);
        return $result;
    }

    /**
     * [setUpdatefileUpdate  修改文件信息]
     * @author wangchen
     * @DateTime 2018-08-02
     * @param  $data 修改数据
     * @return array
     */
    public function setUpdatefileUpdate($data,$arr_where){
        $result = $this->where($arr_where)->Update($data);
        return $result;
    }

    /**
     * [setUpdatefileDel  删除文件信息]
     * @author wangchen
     * @DateTime 2018-08-02
     * @param  $id 文件Id
     * @return array
     */
    public function setUpdatefileDel($id){
        $result = $this->where(['id'=>$id])->delete();
        return $result;
    }
}