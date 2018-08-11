<?php
namespace app\admin\model;

use think\Model;
use think\Db;

class Department extends Model
{
    protected  $table = 'department';

    public function setDepartAdds($arr_data){
         return Db::table($this->table)->insert($arr_data);
    }

}