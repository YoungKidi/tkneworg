<?php

use think\Controller;
use think\Request;
use think\Session;
use app\teacher\business\UploadFiles;
use app\admin\business\Docking;
//该类用于所有文件上传
class Upload
{
    /**
     * 文件上传调用，上传图片等，腾讯云
     *
     * @return \think\Response
     *
     */

    public function getUploadFiles($files,$filetype,$organid){

        if (empty($files['files'])) {
          return return_format([],29001,lang('29001'));
        }
        //将三维数组转换成一维数组
        $file = $files['files']['uploadFile'];
        $filename = $file['tmp_name'];//获取用户刚刚上传的文件
        //判断是否是一个上传文件
        if (!is_uploaded_file($filename)) {
          // 如果该文件不是一个上传的文件
          return return_format('',29002,lang('29002'));
        }
        if (empty($files)) {
          // 如果传入数据为空
          return return_format('',29003,lang('29003'));
        }

        // 允许上传的文件后缀
        $allowedExts = array('xls','xlsx','ppt','pptx','doc', 'docx','txt','pdf','jpg','gif','jpeg','png','bmp', 'mp3','mp4','rmvb','avi','mov','zip','rar');
        $temp = explode(".", $file["name"]);
        $typearr = array('image/gif' ,'image/jpeg','image/jpg','image/pjpeg','image/x-png','image/png','image/bmp','application/vnd.ms-excel','application/vnd.openxmlformats-officedocument.spreadsheetml.sheet','application/vnd.ms-powerpoint','application/vnd.openxmlformats-officedocument.presentationml.presentation','application/msword','application/vnd.openxmlformats-officedocument.wordprocessingml.document','text/plain','application/pdf','audio/mp3','video/mp4','video/vnd.rn-realvideo','video/avi','video/quicktime','application/x-zip-compressed','application/octet-stream');
        //echo $file['size'];
        $extension = end($temp);//获取文件后缀名
        //判断文件扩展名和上传文件的内容mime类型
        if (in_array($extension,$allowedExts) && in_array($file['type'],$typearr)) {
            //判断文件大小,是否大于100M
            $tempfilesize = $file['size']/1024;
            if ($tempfilesize<=102400) {
                //判断
                if ($file['error']>0) {
                    return return_format([],29004,lang('29004'));
                }else{
                    $all['name'] = $file['name'];
                    $all['type'] = $file['type'];//文件类型
                    $all['size'] = $file['size']/1024;//单位kb
                    $all['tmppath'] = $file['tmp_name'];//文件临时存储位置
                    //判断当期目录下的upload目录是否存在该文件
                    //如果没有upload目录，则你需要创建它，upload目录权限未777
                    // $addtime=date("Ymd",time());
                    // $testdir="./".$addtime."/";
                    $addtime= 'upload';
                    $testdir = "./".$addtime."/";
                    //把文件名格式化，便于存储
                    $arr=explode(".", $file["name"]);
                    //$hz=$arr[count($arr)-1];
                    //$middlename = date("Y").date("m").date("d").date("H").date("i").date("s").rand(100, 999).".".$hz;
                    $middlename = $file["name"];
                    //不允许出现两个“.”
                    if (count($arr)>2) {
                        return return_format([],29005,lang('29005'));
                    }
                    //$savefilename = strtotime($file['name']);
                    if(file_exists($testdir)):
                     else:
                      mkdir($testdir,0777);
                     endif;
                    if (file_exists("./upload/".$file['name'])) {
                        # code...
//                        return return_format([],29006,lang('29006'));
						//JCR 由于图片上传后未重新命名 造成图片可能重复上传到服务器
						$all['path'] = "./upload/".$file['name'];
                    }else{
                        //如果upload目录不存在该文件则将上传文件收到upload目录下
                        move_uploaded_file($file['tmp_name'], 'upload/'.$middlename);
                        $all['path'] = './upload/'.$middlename;
                        //return return_format($all,0,'文件上传成功');
                        // return is_file($all['path']) && unlink($all['path']);
                    }
                    $file['name'] = $middlename;
                    //判断$fieltype 如果$filetype==1,则上传腾讯云
                    //如果$filetype == 2，则上传拓客云
                    if ($filetype==1) {
                        $files['allpathnode'] = is_array($files['allpathnode'])?$files['allpathnode']:explode(',', $files['allpathnode']);
                        $files['allpathnode'][1] = $organid;
                        //判断当前文件路径
                        $receive = self::checkpath($files['allpathnode']);
                        //合成最终文件所在路径
                        $dstfolder = $receive['purposename'].'/'.$files['allpathnode'][1].'/'.$receive['plane'];
                        $file['dst']=$dstfolder."/".$file['name'];

                        //上传文件
                        $teccent = self::uploadtencent($file['type'],$all['path'],$file['dst'],'');
                        //判断文件是否上传腾讯云是否成功
                        if (!empty($teccent)) {
                            //上传成功则删除本地文件
                            //is_file($all['path']) && unlink($all['path']);
                            return return_format($teccent,0,lang('success'));
                        }else{
                            return return_format([],29007,lang('29007'));
                        }
                    }elseif ($filetype==2){
                        $dock = new Docking;
                        $Docking= $dock->uploadToFiles($organid,$all['path'],$all['name'],$files['fatherid'],$files['teacherid']);
                        //判断文件是否上传拓客云是否成功
                        if (!empty($Docking)) {
                            //上传成功则删除本地文件
                            //is_file($all['path']) && unlink($all['path']);
                            return return_format($Docking,0,lang('success'));
                        }else{
                            return return_format([],29008,lang('29008'));
                        }
                    }
                }
            }else{
                return return_format([],29009,lang('29009'));
            }
        }else{
            return return_format([],29010,lang('29010'));
        }

    }

    //判断参数路径
    protected function checkpath($allpathnode){
        //plane平台 organid 机构id purposename 上传文件用途
        switch ($allpathnode[2]) {
            case 1:
                $plane = 'company';
                break;
            case 2:
                $plane = 'server';  //服务器类
                break;
        }
        //purposename 文件夹
        switch ($allpathnode[0]) {
            case 1:
                $purposename = 'logo'; //企业LOGO
                break;
            case 2:
                $purposename = 'image_data'; //企业数据区缺省图片
                break;
            case 3:
                $purposename = 'update_file'; //升级包
                break;

            case 4:

                $purposename = 'skin_resource'; //皮肤资源
        }
        if (empty($plane)||empty($purposename)) {
            return return_format([],29011,lang('29011'));
        }else{
            return ['plane'=>$plane,'purposename'=>$purposename];
        }
    }

    //src 本地路径，dst 服务器路径
    protected function uploadtencent($contenttype,$src,$dst,$bucket=''){
        $qcloud = new \QcloudManage();
        $cos = $qcloud->upload($src,$dst,$bucket);

        $bizAttr = '';
        $authority = 'eWPrivateRPublic';
        $customerHeaders = array('x-cos-acl' => 'public-read','Content-Type'=>$contenttype);
        $qcloud->updateFile($dst,$bizAttr,$authority,$customerHeaders);//更新文件控制


        if (empty($cos)) {
            $path = "./";
            //上传失败，则查询文件夹是否存在
            $array_dir=explode("/",$dst);//把多级目录分别放到数组中
            for($i=0;$i<count($array_dir);$i++){
                $path .= $array_dir[$i]."/";
                if(!file_exists($path)){
                   $mk=$qcloud->createFolder($path,$bucket);
                }else{
                  $cosa = $qcloud->upload($src,$dst,$bucket);
                  return $cosa;
                  }
            }
        }
        return $cos;
    }


}
