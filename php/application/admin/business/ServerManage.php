<?php
/**
 * 服务器管理业务逻辑
 */
namespace app\admin\business;
use app\admin\model\Serverinfo;
use app\admin\model\Resourceinfo;
use app\admin\model\Smtpinfo;
use app\admin\model\Updatefile;
use app\admin\model\Template;
use app\admin\model\Skin;
use think\Validate;
use RedisClient;
use think\db;
class ServerManage{
    /**
     *	查询服务器信息
     *	@author yr
     * @param serverid  服务器id 可选
     * @param serverdomain  IP地址或域名 可选
     *	@return array
     */
    public function getServerList($condition){
        //定义查询条件 默认空
        $servermodel = new Serverinfo;
        $result = $servermodel->getServerList($condition);
        return return_format($result,0,lang('success'));
    }
    /**
     *	修改企业配置
     *	@author yr
     * @param serverid  服务器id
     * @param servername 服务器名字
     * @param serverdomain 服务器域名(ip)
     * @param serverport  服务器端口
     * @param totalpoint  服务器总点数
     *	@param  $data array
     *	@return array
     */
    public function addServer($post){
        //字段验证

        $rule = [
            'serverid' => 'require',
            'servername' => 'require',
            'serverdomain' => 'require',
            'serverport' => 'require|number',
            'totalpoint' => 'require|number',
        ];
        $msg = [
            'serverid.require' => '服务器id必须填写',
            'servername.require' => '服务器名字必须填写',
            'serverdomain.require' => '服务器域名(ip)必须填写',
            'serverport.require' => '服务器端口必须填写',
            'serverport.number' => '服务器端口类型必须为整数类型',
            'totalpoint.require' => '服务器总点数必须填写',
            'totalpoint.number' => '服务器总点数必须为整数类型',
        ];
        $validate = new Validate($rule, $msg);
        $result = $validate->check($post);
        if (true !== $result) {
            return return_format('', 39010, $validate->getError());
        }
        $servermodel = new Serverinfo;
        //判断数据库中是否含有添加的服务器id (不能重复添加服务器ID)
        $res= $servermodel->getServerInfoByServerid($post['serverid']);
        if($res){
            return return_format('', 70018, lang('server_id_repeat'));
        }
        $add_result = $servermodel->addServer($post);
        if($add_result>=0){
            return return_format('',0,lang('success'));
        }else{
            return return_format('',30000,lang('error'));
        }
    }
    /**
     * 修改服务器状态
     * @Author yr
     * @param isactive 服务器状态1激活0停用
     * @param serverid 服务器id
     */
    public function updateServerStatus($serverid,$isactive){
        //如果是激活状态改为禁用,反之
        switch ($isactive){
            case 0:
                $status = 1;
                break;
            case 1:
                $status = 0;
                break;
            default:
                return return_format('',30005,lang('param_error'));
        }
        $serverinfomodel = new Serverinfo;
        $update_res = $serverinfomodel->updateServerStatus($serverid,$status);
        if($update_res){
            return return_format(array('isactive'=>$status),0,lang('操作成功'));
        }else{
            return return_format('',0,lang('error'));

        }
    }

    /**
     * 获取资源统计
     * @Author yr
     * @param isactive []
     * @return array
     */
    public function getResourceinfo(){
        $resourceinfomodel = new Resourceinfo;
        $result = $resourceinfomodel->getResourceinfo();
        if(strtotime($result['expirydate']) > time()+60*60*24*365*5){
            $result['expirydate'] = '无限制';
        }
        return return_format($result,0) ;             
    }

    /**********************************************smtp服务器***********************************/
    /**
     * 获取SMTP服务器信息
     * @Author WangChen
     * @param int $companyid [公司id]
     * @return array
     */
    public function getSmtpinfo($companyid){
        //测试companyid
        $companyid = 1;
        $smtpinfo_model = new Smtpinfo;
        $arr_where['companyid']=$companyid;
        $result = $smtpinfo_model->getSmtpinfo($arr_where);
        return return_format($result,0,lang('success'));       
    }


    /**
     * 修改SMTP服务器信息
     * @Author WangChen
     * @param  $companyid 公司id 
     * @param  smtpserver SMTP服务器
     * @param  smtpport   SMTP服务器端口
     * @param  smtpusername SMTP用户
     * @param  smtppassword SMTP密码
     * @param  isssl 通过ssl协议发送邮件（1-是,0-否）
     * @return array
     */
    public function updateSmtpinfo($post){
        //测试SMTPid等于1
        $post['companyid'] = 1;
        //字段验证

        $rule = [
            'smtpserver' => 'require',
            'smtpport' => 'require|number',
            'smtpusername' => 'require',
            'smtppassword' => 'require',
            'isssl' => 'require|number',
        ];
        $msg = [
            'smtpserver.require' => 'SMTP服务器必须填写',
            'smtpport.require' => 'SMTP服务器端口必须填写',
            'smtpport.number' => 'SMTP服务器端口必须为整数类型',
            'smtpusername.require' => 'SMTP用户名必须填写',
            'smtppassword.require' => 'SMTP密码必须填写',
            'isssl.number' => '通过ssl协议发送邮件（1-是,0-否）必须为整数类型',
            'isssl.require' => '通过ssl协议发送邮件必须选择',
        ];
        $validate = new Validate($rule, $msg);
        $result = $validate->check($post);
        if (true !== $result) {
            return return_format('', 79000, $validate->getError());
        }
        $smtpinfo_model = new Smtpinfo;
        // 判断是否含有数据，（有/修改，无/新增）
        $arr_where['companyid'] = $post['companyid'];
        $smtpinfo = $smtpinfo_model->getSmtpinfo($arr_where);
        if(!empty($smtpinfo)){
            //修改smtp信息
            $result = $smtpinfo_model->setSmtpinfoUpdate($post['companyid'],$post);
            
        }else{
            //新增一条smtp信息
            $post['companyid']=$companyid;
            $result = $smtpinfo_model->setSmtpinfoAdd($post);
           
        }
        if($result){
                return return_format('',0,lang('success'));       
            }else{
                return return_format('',70001,lang('smtp_update_error'));       
        }
    }

    /**********************************************更新文件管理***********************************/
    /**
     *更新文件管理列表信息
     * @Author WangChen
     * @param  $arr_where 搜索条件
     * @param  $arr_field 查询字段
     * @param  $arr_data['page'] 访问的页面
     * @return array
     */
    public function getUpdatefileList($arr_data){
        $arr_where['companyid'] = $arr_data['companyid'];
        //获取分页信息
        $arr_return_data['pageinfo'] = $this->getUpdatefilePage($arr_data['page'],$arr_where);

        //当前页面
        $page = $arr_return_data['pageinfo']['now_page'];
        // 计算起始位置
        $arr_page['page'] = $page>0?($page-1) * $arr_return_data['pageinfo']['size']:0;
        $arr_page['size'] = $arr_return_data['pageinfo']['size'];
        //查询字段
        $arr_field = "id,filename,filesize,filedate,isupdate,uploadtime";
        $updatefile_model = new Updatefile;
        $arr_return_data['data'] = $updatefile_model->getUpdatefilePage($arr_where,$arr_field,$arr_page);
        return return_format($arr_return_data,0,lang('success'));
    }

    /**
    * 获取更新文件管理列表分页信息
    * @Author WangChen
    * @param  int $page 
    * @param  int $arr_where  搜索条件
    * @return array
    */
    public function getUpdatefilePage($page,$arr_where){
        $updatefile_model = new Updatefile;
        //获取总数据数
        $int_updatefile_number = $updatefile_model->getUpdatefileCount($arr_where);
        //总数据量

        $arr_page['sum_data'] = $int_updatefile_number;
        //获取每页显示条数
        $int_size = $arr_page['size'] = config('pagesize.admin_uploadfilelist');
        //计算总页数
        $arr_page['sum_page'] = ceil($int_updatefile_number/$int_size);
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
    * 获取更新文件管理详细信息
    * @Author WangChen
    * @param  int $fileid 
    */
    public function getUpdatefileInfo($fileid){
        $arr_where['id'] = $fileid;
        $field='id,version,filename,filetype,isupdate,filedate,filesize,uploadtime,updateflag,companyname';
        $updatefile_model = new updatefile;
        $updatefile_info = $updatefile_model->getUpdatefileInfo($arr_where,$field);
        if($updatefile_info){
            return return_format($updatefile_info);
        }else{
            return return_format('',70015,lang('updatefile_select_null'));       
        }
    }

    /**
    * 获取更新文件管理列表
    */
    public function getUpdateFiletype(){
        $filetype = config('server.updatefile_filetype');
        return return_format($filetype);
    }


    /**
     * 添加更新文件文件和数据信息
     * @author wangchen
     * @param array $arr_data
     */
    public function setUpdatefileAdd($arr_data,$post){
        //验证字段
        $re=$this->checkUpdatefilePostInfo($post);
        if($re){
            return return_format('',70002,$re);   
        }
        $obj_upload = new \Upload;
        $arr_file_info = $obj_upload->getUploadFiles($arr_data,1,$post['companyid']);
        if($arr_file_info['code'] != 0)return return_format('',$arr_file_info['code'],$arr_file_info['info']);
        //如果文件上传成功,返回路径插入数据库
        $post['filename'] = $arr_data['files']['uploadFile']['name'];
        $post['filesize'] = $arr_data['files']['uploadFile']['size'];
        $post['url'] = $arr_file_info['data']['data']['source_url'];
        $post['uploadtime'] = date('Y-m-d H:i:s', time());
        $updatefile_model = new updatefile;
        $result = $updatefile_model->setUpdatefileAdd($post);
        if($result){
            return return_format('',0,lang('success'));
        }else{
            return return_format('',70002,lang('updatefile_add_error'));   
        }
    }


    /**
     * 编辑更新文件数据信息
     * @author wangchen
     * @param array $post
     */
    public function setUpdatefileUpdate($post){
        //数据验证
        $re=$this->checkUpdatefilePostInfo($post);
        if($re){
            return return_format('',79002,$re);   
        }
        //更新时间
        $post['uploadtime'] = date('Y-m-d H:i:s',time());
        //修改id
        $arr_where['id']=$post['id'];
        $updatefile_model = new updatefile;
        $result = $updatefile_model->setUpdatefileUpdate($post,$arr_where);
        if($result){
            return return_format('',0,lang('success'));
        }else{
            return return_format('',70003,lang('updatefile_update_error'));
        }
    }

    /**
     * 检测更新文件数据
     * @author wangchen
     * @param array 
     *
     * 缺少版本号字段
     */    
    public function checkUpdatefilePostInfo($data){
        //字段验证

        $rule = [
            'companyid' => 'require|number',
            'companyname' => 'require',
            'version' => 'require',
            'filetype' => 'require|number',
        ];
        $msg = [
            'companyid.require' => '企业ID必须填写',
            'companyid.number' => '企业ID必须为整数类型',
            'filetype.require' => '请选择文件类型(PC/Android...)',
            'filetype.number' => '文件类型必须为整数类型',
            'version.require' => '客户端版本必须填写',
            'companyname.require' => '企业名称必须填写',
        ];
        $validate = new Validate($rule, $msg);
        $result = $validate->check($data);
        if (true !== $result) {
            return $validate->getError();
        }
    }


    /**
    * 删除更新文件信息
    * @Author WangChen
    * @param  int $id  文件Id
    */
    public function setUpdatefileDel($id){
        $updatefile_model = new updatefile;
        $result = $updatefile_model->setUpdatefileDel($id);
        if($result){
            return return_format('',0,lang('success'));
        }else{
            return return_format('',70004,lang('updatefile_del_error'));
        }
    }

    /**********************************************模板管理***********************************/
    /**
    * 获取模板列表(只查询Id和名称)
    * @Author WangChen
    * @param $filed 查询字段
    */
    public function getTemplateList(){
        $field = 'id,name';
        $template_model = new template;
        $template_info = $template_model->getTemplateList('',$field);
        return return_format($template_info);
    }


    /**
    * 获取皮肤模板列表
    * @Author WangChen
    */
    public function getTemplateSkinList(){
        $tpl_field = 'name,roomType,sign,id';
        //获取模板列表
        $template_model = new template;
        $template_info = $template_model->getTemplateList('',$tpl_field);

        $skin_field='name,clientType,sign,id,tplId';
        //获取皮肤列表
        $skin_model = new skin;
        $skin_info = $skin_model->getSkinList('',$skin_field);
        //循环将两个数组合并模板包含皮肤
        foreach ($template_info as $key => &$value) {
            //将roomType由字符串变为数组
            $value['roomType']=explode(',', $value['roomType']);
            $arr=array();
            foreach ($skin_info as $k => $v) {
                if($v['tplId']==$value['id']){
                    $arr[]=$v;
                }
            }
            $value['skin']=$arr;
        }
        //返回数据
        return return_format($template_info);
    }

    /**
    * 获取模板管理=>添加模板=>适用教室(数据集合)
    */
    public function getTemplateRoomType(){
        $filetype = config('server.template_roomType');
        return return_format($filetype);
    }

    /**
    * 获取模板管理=>添加皮肤=>适用终端(数据集合)
    */
    public function getSkinClientType(){
        $filetype = config('server.skin_clientType');
        return return_format($filetype);
    }

    /**
    * 创建模板信息
    * @Author Wangchen
    * @param string $name [模板名称]
    * @param string $sign [模板标识]
    * @param array $roomType[] [适用教室（ 0:一对一 3:一对多 10:大班课）]
    * @return array
    */
    public function setTemplateAdd($post){
        //数据验证
        $re=$this->checkTemplatePostInfo($post);
        if($re){
            return return_format('',79003,$re);   
        }
        $post['createTime']=time();
        //将获取到的教室数组修改为字符串
        $post['roomType'] = implode(',' , $post['roomType']);
        $template_model = new template;
        $result = $template_model->setTemplateAdd($post);
        if($result){
            return return_format('',0,lang('success'));
        }else{
            return return_format('',70005,lang('template_add_error'));
        }
    }

    /**
    * 编辑模板信息
    * @Author Wangchen
    * @param int $id [模板ID]
    * @param string $name [模板名称]
    * @param string $sign [模板标识]
    * @return array
    */
    public function setTemplateUpdate($post){
        //数据验证
        $re=$this->checkTemplatePostInfo($post);
        if($re){
            return return_format('',79003,$re);   
        }
        $template_model = new template;
        $result = $template_model->setTemplateUpdate($post);
        if($result){
            return return_format('',0,lang('success'));
        }else{
            return return_format('',70006,lang('template_update_error'));
        }
    }

    /**
     * 更新模板数据验证
     * @author wangchen
     * @param array 
     */    
    public function checkTemplatePostInfo($data){
        //字段验证

        $rule = [
            'name' => 'require',
            'sign' => 'require',
        ];
        $msg = [
            'name.require' => '模板名称必须填写',
            'sign.require' => '模板标识必须填写',
        ];
        $validate = new Validate($rule, $msg);
        $result = $validate->check($data);
        if (true !== $result) {
            return $validate->getError();
        }
    }


    /**
     * 删除模板接口
     * @author Wangchen
     * @param int $id 模板id
     * @param array $arr_where [查询条件]
     */
    public function setTemplateDel($id){
        if(empty(is_numeric($id))){
            return return_format('',70012,lang('templateid_param_error'));
        }
        //查询皮肤表中是否含有删除的模板ID
        $skin_where['tplId'] = $id;
        $skin_model = new skin;
        $result = $skin_model->getSkinList($skin_where);
        //如果含有不能删除需要优先删除皮肤
        if($result){
            return return_format('',70010,lang('template_del_skin_error'));
        }
        // 删除模板
        $arr_where['id']=$id;
        $template_model = new template;
        $result = $template_model->setTemplateDel($arr_where);
        if($result){
            return return_format('',0,lang('success'));
        }else{
            return return_format('',70011,lang('template_del_error'));
        }

    }

    /**
     * 删除皮肤接口
     * @author Wangchen
     * @param int $id 皮肤id
     * @param array $arr_where [查询条件]
     */
    public function setSkinDel($id){
        if(empty(is_numeric($id))){
            return return_format('',70013,lang('skinid_param_error'));
        }
        $arr_where['id'] = $id;
        $skin_model = new skin;
        $result = $skin_model->setSkinDel($arr_where);
        if($result){
            return return_format('',0,lang('success'));
        }else{
            return return_format('',70014,lang('skin_del_error'));
        }

    }
    /**
     * 皮肤资源文件上传
     * @author wangchen
     * @param array $arr_data
     */
    public function setSkinFileAdd ($arr_data){
        $obj_upload = new \Upload;
        $arr_file_info = $obj_upload->getUploadFiles($arr_data,1,$arr_data['companyid']);
        if($arr_file_info['code'] != 0)return return_format('',$arr_file_info['code'],$arr_file_info['info']);
        $result = ['resource'=>$arr_file_info['data']['data']['source_url']];
        if($result){
            return return_format($result,0,lang('success'));
        }else{
            return return_format('',70007,lang('skin_file_add_error'));
        }
    }

    /**
    * 创建皮肤信息
    * @Author Wangchen
    * @param string $name [皮肤名称]
    * @param string $sign [皮肤标识]
    * @param int $tplId [所属模板]
    * @param int $clientType [适用终端（1 PC 2Android3.IOS）(单选)]
    * @param int $type [所属类型（1 代表公用 2代表私有）(单选)]
    * @param int $companyId [企业编号（私有开启）]
    * @param string $resource [文件资源地址]
    * @return array
    */
    public function setSkinAdd($post){
        //数据验证
        $re=$this->checkSkinPostInfo($post);
        if($re){
            return return_format('',79003,$re);   
        }
        $post['createTime'] = time();
        //将获取到的教室数组修改为字符串
        $skin_model = new skin;
        $result = $skin_model->setSkinAdd($post);
        if($result){
            return return_format('',0,lang('success'));
        }else{
            return return_format('',70008,lang('skin_add_error'));
        }
    }

    /**
    * 编辑皮肤信息
    * @Author Wangchen
    * @param int $id [皮肤ID]
    * @param string $name [皮肤名称]
    * @param string $sign [皮肤标识]
    * @param int $tplId [所属模板]
    * @param int $clientType [适用终端（1 PC 2Android3.IOS）(单选)]
    * @param int $type [所属类型（1 代表公用 2代表私有）(单选)]
    * @param int $companyId [企业编号（私有开启）]
    * @return array
    */
    public function setSkinUpdate($post){
        //数据验证
        $re=$this->checkSkinPostInfo($post);
        if($re){
            return return_format('',79003,$re);   
        }
        $skin_model = new skin;
        $result = $skin_model->setSkinUpdate($post);
        if($result){
            return return_format('',0,lang('success'));
        }else{
            return return_format('',70009,lang('skin_update_error'));
        }
    }

    /**
     * 更新皮肤数据验证
     * @author wangchen
     * @param array 
     */ 
    public function checkSkinPostInfo($data){
        //字段验证

        $rule = [
            'name' => 'require',
            'sign' => 'require',
        ];
        $msg = [
            'name.require' => '皮肤名称必须填写',
            'sign.require' => '皮肤标识必须填写',
        ];
        $validate = new Validate($rule, $msg);
        $result = $validate->check($data);
        if (true !== $result) {
            return $validate->getError();
        }
    }
}

?>