<?php
/**
 * 服务器管理
 * @author yr
 * @date 18-07-05
 *
 */
namespace app\admin\controller;
use think\Controller;
use think\Request;
use app\admin\business\SetupManage;
use app\admin\business\ServerManage;
class Server extends Controller {
    //自定义初始化
    protected function _initialize() {
        parent::_initialize();
    }
    /**
     * 服务器管理列表
     * @Author yr
     * @param serverid  服务器id 可选
     * @param serverdomain  IP地址或域名 可选
     * POST | URL:/admin/Server/getServerList
     */
    public function getServerList(){
        $condition = '';
        $serverid = Request::instance()->POST('serverid');
        $serverdomain = Request::instance()->POST('serverdomain');
        if($serverid){
            $condition['serverid'] = $serverid;
        }
        if($serverdomain){
            $condition['serverdomain'] = array('like', '%'.$serverdomain.'%');
        }
        $serverobj = new ServerManage;
        $result =  $serverobj ->getServerList($condition);
        $this->ajaxReturn($result);
    }
    /**
     * 服务器管理添加
     * @Author yr
     * @param serverid  服务器id
     * @param servername 服务器名字
     * @param serverdomain 服务器域名(ip)
     * @param serverport  服务器端口
     * @param totalpoint  服务器总点数
     * POST | URL:/admin/Server/addServer
     */
    public function addServer(){
        $post = Request::instance()->POST(false);
        //获取当前公司基本信息
        $serverobj = new ServerManage;
        $result = $serverobj->addServer($post);
        $this->ajaxReturn($result);
    }
    /**
     * 修改服务器状态
     * @Author yr
     * @param isactive 服务器状态1激活0停用
     * @param serverid 服务器id
     * POST | URL:/admin/Server/updateServerStatus
     */
    public function updateServerStatus(){
        $isactive  = Request::instance()->POST('isactive');
        $serverid  = Request::instance()->POST('serverid');
        //获取当前公司基本信息
        $serverobj = new ServerManage;
        $result = $serverobj->updateServerStatus($serverid,$isactive);
        $this->ajaxReturn($result);
    }
    
    /**
     * 获取资源统计
     * @Author yr
     * @param isactive []
     * @return array
     * POST | URL:/admin/Server/getResourceinfo
     */
    public function getResourceinfo(){
        //获取当前的资源统计
        $serverobj = new ServerManage;
        $result = $serverobj->getResourceinfo();
        $this->ajaxReturn($result);        
    }    

    /**********************************************smtp服务器***********************************/
     /**
     * 获取Smtp服务器数据
     * @Author Wangchen
     * @param int $companyid 企业id
     * @return array
     * POST | URL:/admin/Server/getSmtpinfo
     */
    public function getSmtpinfo(){
        $companyid = input('post.companyid');
        //获取当前的smtp信息
        $server_obj = new ServerManage;
        $result = $server_obj->getSmtpinfo($companyid);
        $this->ajaxReturn($result);        
    }    

    /**
    * 修改Smtp服务器数据
    * @Author Wangchen
    * @param  smtpserver SMTP服务器
    * @param  smtpport   SMTP服务器端口
    * @param  smtpusername SMTP用户
    * @param  smtppassword SMTP密码
    * @param  isssl 通过ssl协议发送邮件（1-是,0-否）
    * @return json
    * POST | URL:/admin/Server/updateSmtpinfo
    */
    public function updateSmtpinfo(){
        $post = Request::instance()->POST(false);
        //修改smtp信息
        $server_obj = new ServerManage;
        $result = $server_obj->updateSmtpinfo($post);
        $this->ajaxReturn($result);
           
    }    
    /**********************************************更新文件管理***********************************/
    /**
    * 获取更新文件列表信息
    * @Author Wangchen
    * @param  int $page   访问的页面
    * @param  int $companyid   企业ID
    * @return json
    * POST | URL:/admin/Server/getUpdatefileList
    */
    public function getUpdatefileList(){
        $arr_data['companyid'] = input('post.companyid')?:1;
        $arr_data['page'] = input('post.page')?:1;
        //获取列表
        $server_obj = new ServerManage;
        $result = $server_obj->getUpdatefileList($arr_data);
        $this->ajaxReturn($result);
           
    }    


     /**
     * 获取更新文件详细信息
     * @Author Wangchen
     * @param  int fileid   文件ID
     * @return json
     * POST | URL:/admin/Server/getUpdatefileInfo
     */
    public function getUpdatefileInfo(){
        //详细信息id
        $fileid = input('post.fileid');
        //获取详细信息
        $server_obj = new ServerManage;
        $result = $server_obj->getUpdatefileInfo($fileid);
        $this->ajaxReturn($result);
    }    

    /**
     * 获取更新文件文件类别
     * @Author Wangchen
     * POST | URL:/admin/Server/getUpdateFiletype
     */
    public function getUpdateFiletype(){
        $server_obj = new ServerManage;
        $result = $server_obj->getUpdateFiletype();
        $this->ajaxReturn($result);
    }    
    
    /**
    * 添加更新文件
    * @Author Wangchen
    * @param [int] $companyid [公司id]
    * @param [string] $companyname [公司名称]
    * @param [string] $version [客户端版本]
    * @param [int] $filetype [文件类型（0:IM   1:PC Conference   4:Android mobile platform   5:IOS mobile platform）]
    * @param [int] $isupdate [操作类型(1：升级包   0：安装包)]
    * @param [int] $updateflag [升级标志(0，不升级，1，强制升级，2不强制)]
    * @param array $_FILES 上传的文件
    * @param string $int_type 上传的类型 1 企业LOGO 2 企业数据区缺省图片 3 更新文件（升级包）
    * @return json
    * POST | URL:/admin/Server/setUpdatefileAdd
    */
    pubLic function setUpdatefileAdd(){
        //获取基本信息
        $post = Request::instance()->POST();
        // //默认企业编号为1(测试)
        $post['companyid'] = 1;
        //文件信息
        $data['files'] = $_FILES;
        $int_type = 3;
        $data['allpathnode'] = [$int_type,'',2];
        $server_obj = new ServerManage;
        $result = $server_obj->setUpdatefileAdd($data,$post);
        return $this->ajaxReturn($result);
    }



    /**
    * 编辑更新文件信息
    * @Author Wangchen
    * @param [int] $id [文件ID]
    * @param [int] $companyid [公司id]
    * @param [string] $companyname [公司名称]
    * @param [string] $version [版本信息]
    * @param [int] $filetype [文件类型（0:IM   1:PC Conference   4:Android mobile platform   5:IOS mobile platform）]
    * @param [int] $isupdate [操作类型(1：升级包   0：安装包)]
    * @param [int] $updateflag [升级标志(0，不升级，1，强制升级，2不强制)]
    * @return json
    * POST | URL:/admin/Server/setUpdatefileUpdate
    */
    pubLic function setUpdatefileUpdate(){
        //获取基本信息
        $post = Request::instance()->POST();
        $server_obj = new ServerManage;
        $result = $server_obj->setUpdatefileUpdate($post);
        return $this->ajaxReturn($result);
    }

    /**
    * 删除更新文件信息
    * @Author Wangchen
    * @param  int id   文件Id
    * @return json
    * POST | URL:/admin/Server/setUpdatefileDel
    */
    public function setUpdatefileDel(){
        //文件id
        $id = input('post.id');
        $server_obj = new ServerManage;
        $result = $server_obj->setUpdatefileDel($id);
        $this->ajaxReturn($result);
           
    }    
    /************************************模板皮肤管理***************************************/
    
    /**
    * 获取模板列表
    * @Author Wangchen
    * @return json
    * GET | URL:/admin/Server/getTemplateList
    */
    public function getTemplateList(){
        $server_obj = new ServerManage;
        $result = $server_obj->getTemplateList();
        $this->ajaxReturn($result);
    }    

    /**
    * 获取模板皮肤列表
    * @Author Wangchen
    * @return json
    * GET | URL:/admin/Server/getTemplateSkinList
    */
    public function getTemplateSkinList(){
        $server_obj = new ServerManage;
        $result = $server_obj->getTemplateSkinList();
        $this->ajaxReturn($result);
    }    


    /**
     * 获取模板管理=>添加模板=>适用教室(数据集合)
     * @Author Wangchen
     * POST | URL:/admin/Server/getTemplateRoomType
     */
    public function getTemplateRoomType(){
        $server_obj = new ServerManage;
        $result = $server_obj->getTemplateRoomType();
        $this->ajaxReturn($result);
    }    
    


    /**
    * 创建模板信息
    * @Author Wangchen
    * @param string $name [模板名称]
    * @param string $sign [模板标识]
    * @param array $roomType[] [适用教室（ 0:一对一 3:一对多 10:大班课）]
    * @return json
    * POST | URL:/admin/Server/setTemplateAdd
    */

    public function setTemplateAdd(){
        $post = Request::instance()->POST(false);
        $server_obj = new ServerManage;
        $result = $server_obj->setTemplateAdd($post);
        $this->ajaxReturn($result);
    }


    /**
    * 编辑模板信息
    * @Author Wangchen
    * @param int $id [模板ID]
    * @param string $name [模板名称]
    * @param string $sign [模板标识]
    * @return json
    * POST | URL:/admin/Server/setTemplateUpdate
    */

    public function setTemplateUpdate(){
        $post = Request::instance()->POST(false);
        $server_obj = new ServerManage;
        $result = $server_obj->setTemplateUpdate($post);
        $this->ajaxReturn($result);
    }

    /**
     * 删除模板接口
     * @author wangchen
     * @param int $id 模板id
     * @return json
     * POST | URL:/admin/Server/setTemplateDel
     */
    public function setTemplateDel(){
        $id = input('post.id');//要删除的模板id
        $server_obj = new ServerManage;
        $result = $server_obj->setTemplateDel($id);
        $this->ajaxReturn($result);

    }

    /**
     * 删除皮肤接口
     * @author wangchen
     * @param int $id 皮肤id
     * @return json
     * POST | URL:/admin/Server/setSkinDel
     */
    public function setSkinDel(){
        $id = input('post.id');//要删除的皮肤id
        $server_obj = new ServerManage;
        $result = $server_obj->setSkinDel($id);
        $this->ajaxReturn($result);
    }


    /**
     * 获取模板管理=>添加皮肤=>适用终端(数据集合)
     * @Author Wangchen
     * POST | URL:/admin/Server/getSkinClientType
     */
    public function getSkinClientType(){
        $server_obj = new ServerManage;
        $result = $server_obj->getSkinClientType();
        $this->ajaxReturn($result);
    }    
    

    /**
    * 添加皮肤资源文件
    * @Author Wangchen
    * @param array $_FILES 上传的文件
    * @return json
    * POST | URL:/admin/Server/setSkinFileAdd
    */
    pubLic function setSkinFileAdd(){
        //默认企业编号为1(测试)
        $data['companyid'] = 1;
        //文件信息
        $data['files'] = $_FILES;
        $int_type = 4; //上传的类型 1 企业LOGO 2 企业数据区缺省图片 3 更新文件（升级包） 4皮肤资源文件
        $data['allpathnode'] = [$int_type,$data['companyid'],2];
        $server_obj = new ServerManage;
        $result = $server_obj->setSkinFileAdd($data);
        return $this->ajaxReturn($result);
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
    * @return json
    * POST | URL:/admin/Server/setSkinAdd
    */

    public function setSkinAdd(){
        $post = Request::instance()->POST(false);
        $server_obj = new ServerManage;
        $result = $server_obj->setSkinAdd($post);
        $this->ajaxReturn($result);
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
    * @param string $resource [文件资源地址]
    * @return json
    * POST | URL:/admin/Server/setSkinUpdate
    */

     public function setSkinUpdate(){
        $post = Request::instance()->POST(false);
        $server_obj = new ServerManage;
        $result = $server_obj->setSkinUpdate($post);
        $this->ajaxReturn($result);
    }




}
