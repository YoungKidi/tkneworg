<?php
/**企业管理设置**/
namespace app\admin\business;

use app\admin\model\Department;

class DepartmentManage{

    /**
     * 添加部分信息表
     * @param $arr_data
     */
    public function setDepartAdd($arr_data){
        $obj_department = new Department();
        $int_department = $obj_department->setDepartAdds($arr_data);
        return $int_department;
    }
}