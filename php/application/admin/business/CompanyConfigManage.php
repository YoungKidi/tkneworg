<?php
/**企业管理设置**/
namespace app\admin\business;
use think\Validate;
class CompanyConfigManage{

    /**
     * 根据传入的数组取出配置项字符串中的配置项
     * @param $str_function
     * @param $arr_config
     * @return array
     */
    public function getConfigArr($str_function,$arr_config){
        $arr_new_data = [];
        foreach($arr_config as $k => $v){
            $config_key = array_search($v,$this->Config());
            $arr_new_data[$v] = (int)$str_function[$config_key];
        }
        return $arr_new_data;
    }

    /**
     * 根据传入的value获取配置项在配置字符串中的位置并替换
     * @param $str_function
     * @param $arr_config
     * @return bool
     */
    public function getConfigKey($str_function,$arr_config){
        foreach ($arr_config as $k => $v) {
                  //查询v在$this->Config中的下标
                  $config_key = array_search($k,$this->Config());
                  if(!$config_key)return false;
                  //修改str_function对应下标的值
                  $str_function[$config_key] = $v;
            }
            return $str_function;

    }

    private function Config(){
        return [
            '0'   =>    'chk_voice_frequency',		//音频
            '1'   =>    'chk_video',	            //视频
            '2'    =>   'chk_white_board',		//白板
            '3'    =>   'chk_procedure_share',		//程序共享
            '4'    =>   'chk_invite',			//邀请
            '5'    =>   'chk_classroom_transcribe',	//教室录制
            '6'    =>   'chk_share_video',		//分享影音
            '7'    =>   'chk_quit_classroom',		//教室结束自动退出教室
            '8'    =>    'chk_vote',			//投票
            '9'    =>    'chk_file_transfer',		//文件传输
            '10'    =>    'chk_high_definition',	//高清
            '11'    =>    'chk_questions_answers',	//问答
            '12'    =>    'chk_hide_chairman',		//隐藏主席
            '13'    =>    'chk_hide_teacher',		//隐藏老师
            '14'    =>    'chk_text_chat',		//文本聊天
            '15'    =>    'chk_student_list',		//学员列表
            '16'    =>    'chk_courseware_list',	//课件列表
            '17'    =>    'chk_cut_figure',		//是否切图
            '18'    =>    'chk_web_share',		//网页共享
            '19'    =>    'chk_automatic_entry_classroom',		//自动进入教室
            '20'    =>    'chk_setup_wizard',				//音视频设置向导
            '21'    =>    'chk_sip_phone',				//sip电话
            '22'    =>    'chk_h323_mcu',					//呼叫H323终端或MCU
            '23'    =>    'chk_automatically_open_video',		//自动开启音视频
            '24'    =>    'chk_open_classroom',				//公开教室
            '26'    =>    'chk_hide_close_button',			//隐藏视频窗口关闭按钮
            '27'    =>    'chk_hide_username',				//隐藏视频窗口用户名
            '28'    =>    'chk_prioritize',				//等分视频优先排列
            '29'    =>    'chk_play_video',				//多线程播放视频
            '30'    =>    'chk_server_recorde',				//服务器录制
            '31'    =>    'chk_automatic_recorde',			//自动录制
            '32'    =>    'chk_automatic_class',			//自动上课
            '33'    =>    'chk_students_close',				//学生可自行关闭音视频
            '34'    =>    'chk_hide_button',				//隐藏上下课按钮
            '35'    =>    'chk_h5_file',					//H5文档
            '36'    =>    'chk_assistant_open',				//助教是否开启音视频
            '37'    =>    'chk_strokes_jurisdiction',			//画笔权限
            '38'    =>    'chk_file_paging',			      //允许学生操作翻页
            '39'    =>    'chk_screen',					//双屏显示
            '40'    =>    'chk_raise_hands',				//上台后允许举手
            '41'    =>    'chk_before_class',				//上课前发布音视频
            '42'    =>    'chk_show_answer',				//答题结束后自动显示答案
            '43'    =>    'chk_ppt_remark',				//启用PPT备注
            '44'    =>    'chk_trophy_voice',				//自定义奖杯声音
            //'45'    =>    'chk_trophy_voice',				//教室视频混屏录制 -----暂未使用
            //'46'    =>    'chk_trophy_voice',				//教室音频混音录制 -----暂未使用
            '47'    =>    'chk_leave_classroom',			//下课后不离开教室
            '48'    =>    'chk_video_annotation',			//视频标注
            '49'    =>    'chk_local',					//本地录制
            '50'    =>    'chk_courseware_sync',			//课件全屏同步
            '51'    =>    'chk_picture_in_picture',			//画中画
            '52'    =>    'chk_close_mp4',				//MP4播放完自动关闭
            '53'    =>    'chk_upload_pictures',			//聊天区上传图片
            '54'    =>    'chk_close_client',			      //下课后自动关闭客户端
            '55'    =>    'chk_image_reversal',				//自己的视频进行镜像反转
            '56'    =>    'chk_file_classification',			//文件分类
            '57'    =>    'chk_foreground_picture',			//前景图
            '58'    =>    'chk_strokes_name',				//画笔落笔显示名字
            '59'    =>    'chk_screenshot',				//截图
            '60'    =>    'chk_rtmp',				      //RTMP推流
            '61'    =>    'chk_answer',					//答题器
            '62'    =>    'chk_turntable',				//转盘
            '63'    =>    'chk_timer',					//计时器
            '64'    =>    'chk_responder',				//抢答器
            '65'    =>    'chk_white_board',				//小白板
            '66'    =>    'chk_screen_share',				//屏幕共享
            '67'    =>    'chk_file_definition',			//文档转换清晰度高清
            '68'    =>    'chk_brush_operate',			      //按用户区别落笔笔迹
            '69'    =>    'chk_QR_code',					//二维码拍照上传
            '70'    =>    'chk_local_video',				//播放本地媒体文件
            '71'    =>    'chk_end_class',				//按下课时间结束课堂
            '72'    =>    'chk_bothway_share',				//双向共享
            '73'    =>    'chk_pointer',					//教鞭
            '74'    =>    'chk_regional_exchange',			//本地区域交换
            '75'    =>    'chk_grouping',					//分组
            '76'    =>    'chk_open_headmaster',			//开启班主任
            '77'    =>    'chk_headmaster_monitoring',		//班主任监控
            '78'    =>    'chk_tour_class',				//巡课取消点击下课
            '79'    =>    'chk_resolution_consistency',	      //用户分辨率一致
            '80'    =>    'chk_voice',					//切换为纯音频教室
            '81'    =>    'chk_whiteboard_impression',		//自定义白板底色
            '82'    =>    'chk_account_login',				//交互教室用账号登录
            '83'    =>    'chk_live_account_login',		      //直播教室用账号登录
            '84'    =>    'chk_associated_courseware',		//上课时关联课件免刷新
            '85'    =>    'chk_client',					//强起客户端
            '86'    =>    'chk_upload_file',				//下课后上传本地录制文件
            '87'    =>    'chk_advertising_position',		      //广告位
            '88'    =>    'chk_tenminutes_invalid',		      //接口进入房间地址10分钟失效
            '89'    =>    'chk_local_distinguishability',	      //本地录制分辨率
            '90'    =>    'chk_assistant_enter',			//不提示助教进入
            '91'    =>    'chk_hd_audio',					//高清音频
            '92'    =>    'chk_264_code',					//H264编码

            //'93'    =>    'chk_264_code',				      //教室内报警 	-----暂未使用
            //'94'    =>    'chk_264_code',				      //允许使用父企业课件	-----暂未使用
            //'95'    =>    'chk_264_code',				      //共享本地高清电影	-----暂未使用
            //'96'    =>    'chk_264_code',				      //合并录制件	-----暂未使用
            //'97'    =>    'chk_264_code',				      //共享时展示鼠标	-----暂未使用
            //'98'    =>    'chk_264_code',				      //是否压缩	-----暂未使用

        ];
    }

    /**
     * 数据验证 验证企业配置项——全局配置
     * @param array $arr_data 需要验证的数据
     * @return boolean
     */
    public function chkConfig21($arr_data){
        $arr_rule=[
            'chk_video'         =>  'require|number|between:0,1',
            'chk_screen'        =>  'require|number|between:0,1',
            'chk_local'         =>  'require|number|between:0,1',
            'chk_image_reversal'        =>  'require|number|between:0,1',
            'chk_foreground_picture'    =>  'require|number|between:0,1',
            'chk_rtmp'          =>  'require|number|between:0,1',
            'chk_voice'         =>  'require|number|between:0,1',
            'chk_resolution_consistency'    =>  'require|number|between:0,1',
            'chk_client'        =>  'require|number|between:0,1',
            'chk_tenminutes_invalid'        =>  'require|number|between:0,1',
            'chk_local_distinguishability'  =>  'require|number|between:0,1',
             'chk_assistant_enter'          =>  'require|number|between:0,1',
            'chk_hd_audio'      =>  'require|number|between:0,1',
            'chk_264_code'      =>  'require|number|between:0,1',
        ];
        $obj_validate = new Validate($arr_rule);
        $bool_result = $obj_validate->check($arr_data);
        return $bool_result;
    }

    /**
     * 数据验证 验证企业配置项——上课流程相关
     * @param  [type] $arr_data [description]
     * @return [type]           [description]
     */
    public function chkConfig22($arr_data){
        $arr_rule=[
            'chk_automatic_class'         =>  'require|number|between:0,1',
            'chk_students_close'  =>  'require|number|between:0,1',
            'chk_hide_button'        =>  'require|number|between:0,1',
            'chk_assistant_open'         =>  'require|number|between:0,1',
            'chk_before_class'        =>  'require|number|between:0,1',
            'chk_leave_classroom'    =>  'require|number|between:0,1',
            'chk_courseware_sync'          =>  'require|number|between:0,1',
            'chk_picture_in_picture'         =>  'require|number|between:0,1',
            'chk_close_mp4'    =>  'require|number|between:0,1',
            'chk_close_client'        =>  'require|number|between:0,1',
            'chk_end_class'   =>  'require|number|between:0,1',
            'chk_regional_exchange'        =>  'require|number|between:0,1',
            'chk_tour_class'  =>  'require|number|between:0,1',
            'chk_account_login'      =>  'require|number|between:0,1',
        ];
        $obj_validate = new Validate($arr_rule);
        $bool_result = $obj_validate->check($arr_data);
        return $bool_result;
    }
    /**
     * 数据验证 验证企业配置项——课堂工具
     * @param  [type] $arr_data [description]
     * @return [type]           [description]
     */
    public function chkConfig23($arr_data){
        $arr_rule=[
            'chk_procedure_share'         =>  'require|number|between:0,1',
            'chk_h5_file'  =>  'require|number|between:0,1',
            'chk_strokes_jurisdiction'        =>  'require|number|between:0,1',
            'chk_file_paging'         =>  'require|number|between:0,1',
            'chk_show_answer'        =>  'require|number|between:0,1',
            'chk_ppt_remark'    =>  'require|number|between:0,1',
            'chk_trophy_voice'          =>  'require|number|between:0,1',
            'chk_video_annotation'         =>  'require|number|between:0,1',
            'chk_upload_pictures'    =>  'require|number|between:0,1',
            'chk_file_classification'        =>  'require|number|between:0,1',
            'chk_strokes_name'   =>  'require|number|between:0,1',
            'chk_screenshot'        =>  'require|number|between:0,1',
            'chk_answer'  =>  'require|number|between:0,1',
            'chk_turntable'      =>  'require|number|between:0,1',
            'chk_timer'      =>  'require|number|between:0,1',
            'chk_responder'      =>  'require|number|between:0,1',
            'chk_white_board'      =>  'require|number|between:0,1',
            'chk_screen_share'      =>  'require|number|between:0,1',
            'chk_file_definition'      =>  'require|number|between:0,1',
            'chk_brush_operate'      =>  'require|number|between:0,1',
            'chk_QR_code'      =>  'require|number|between:0,1',
            'chk_local_video'      =>  'require|number|between:0,1',
            'chk_bothway_share'      =>  'require|number|between:0,1',
            'chk_associated_courseware'      =>  'require|number|between:0,1',
        ];
        $obj_validate = new Validate($arr_rule);
        $bool_result = $obj_validate->check($arr_data);
        return $bool_result;
    }
    /**
     * 数据验证 验证企业配置项——版本相关
     * @param  [type] $arr_data [description]
     * @return [type]           [description]
     */
    public function chkConfig24($arr_data){
        $arr_rule=[
            'chk_pointer'         =>  'require|number|between:0,1',
            'chk_grouping'  =>  'require|number|between:0,1',
            'chk_open_headmaster'        =>  'require|number|between:0,1',
            'chk_headmaster_monitoring'         =>  'require|number|between:0,1',
        ];
        $obj_validate = new Validate($arr_rule);
        $bool_result = $obj_validate->check($arr_data);
        return $bool_result;
    }
    /**
     * 数据验证 验证企业配置项——保留项
     * @param  [type] $arr_data [description]
     * @return [type]           [description]
     */
    public function chkConfig25($arr_data){
        $arr_rule=[
            'chk_voice_frequency' =>    'require|number|between:0,1',
            'chk_white_board' =>    'require|number|between:0,1',
            'chk_invite' => 'require|number|between:0,1',
            'chk_classroom_transcribe' =>   'require|number|between:0,1',
            'chk_share_video' =>    'require|number|between:0,1',
            'chk_quit_classroom' => 'require|number|between:0,1',
            'chk_vote' =>   'require|number|between:0,1',
            'chk_file_transfer' =>  'require|number|between:0,1',
            'chk_high_definition' =>    'require|number|between:0,1',
            'chk_questions_answers' =>  'require|number|between:0,1',
            'chk_hide_chairman' =>  'require|number|between:0,1',
            'chk_hide_teacher' =>   'require|number|between:0,1',
            'chk_text_chat' =>  'require|number|between:0,1',
            'chk_student_list' =>   'require|number|between:0,1',
            'chk_courseware_list' =>    'require|number|between:0,1',
            'chk_cut_figure' => 'require|number|between:0,1',
            'chk_web_share' =>  'require|number|between:0,1',
            'chk_automatic_entry_classroom' =>  'require|number|between:0,1',
            'chk_setup_wizard' =>   'require|number|between:0,1',
            'chk_sip_phone' =>  'require|number|between:0,1',
            'chk_h323_mcu' =>   'require|number|between:0,1',
            'chk_automatically_open_video' =>   'require|number|between:0,1',
            'chk_open_classroom' => 'require|number|between:0,1',
            'chk_hide_close_button' =>  'require|number|between:0,1',
            'chk_hide_username' =>  'require|number|between:0,1',
            'chk_prioritize' => 'require|number|between:0,1',
            'chk_play_video' => 'require|number|between:0,1',
            'chk_server_recorde' => 'require|number|between:0,1',
            'chk_automatic_recorde' =>  'require|number|between:0,1',
            'chk_raise_hands' =>    'require|number|between:0,1',
        ];
        $obj_validate = new Validate($arr_rule);
        $bool_result = $obj_validate->check($arr_data);
        return $bool_result;
    }

    /**
     * 获取默认配置
     *
     */
    public function getDefaultSetting(){
        $str = '';
        $str_config = str_pad($str,100,0,STR_PAD_RIGHT );//填充字符串
        $arr_config = [0,1,2,5,6,14,15,16,20,23,26,30,33,36,37,40,42,55,56,61,62,63,64,65,71];
        foreach($arr_config as $k => $v){
            $str_config[$v] = 1;
        }
        return $str_config;
    }


    /**
     * 数据验证 验证企业配置项——大班课相关
     * @param  [type] $arr_data [description]
     * @return [type]           [description]
     */
    public function chkConfig26($arr_data){
        $arr_rule=[
            'chk_advertising_position'  =>  'require|number|between:0,1',
            'chk_live_account_login'    =>  'require|number|between:0,1',
        ];
        $obj_validate = new Validate($arr_rule);
        $bool_result = $obj_validate->check($arr_data);
        return $bool_result;
    }
    /**
     * 数据验证 验证回调跳转
     */
    public function chkConfig3($arr_data){
        $arr_rule=[
            'chk_upload_file'  =>  'require|number|between:0,1',
        ];
        $obj_validate = new Validate($arr_rule);
        $bool_result = $obj_validate->check($arr_data);
        return $bool_result;
    }
}   