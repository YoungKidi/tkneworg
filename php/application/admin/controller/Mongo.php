<?php
/**
 * 房间管理
 * @author wyx
 * @date 18-06-27
 * 
 */
namespace app\admin\controller;
use think\Controller;
use think\Request;
//use MongodbClient;
use app\admin\business\MongodbManage;
use Particle;
class Mongo extends Controller {
	//自定义初始化
	protected function _initialize() {
		parent::_initialize();
	}

        //插入设备测试数据
        public function insertEquipment($dbname,$collection,$userid,$serial,$companyid,$starttime,$endtime,$gaptime){

                if( empty($dbname) || empty($collection) || empty($userid) || empty($serial) || empty($companyid) || empty($starttime) ||  empty($endtime) || empty($gaptime) ){
                        return ;
                }
                $config = ['dbname'=>$dbname,'collection'=>$collection];
                $obj = new MongodbManage($config);

                $starttime = strtotime($starttime);
                $endtime = strtotime($endtime);
                $num = floor(($endtime - $starttime)/$gaptime);
                $total = 0;
                for($i=0;$i<$num;$i++){
                        $timestamp = $starttime + $i*$gaptime;
                        //过滤掉已经插入的数据
                        $where = [];
                        $options = [];
                        $where['peerId'] = $userid;
                        $where['serial'] = $serial;
                        $where['companyid'] = $companyid;
                        $where['statistical.time'] = $timestamp;
                        $option = [];
                        $res = $obj->find($where,$option);
                        // var_dump($res);
                        // die;
                        if($res){
                                continue;
                        }
                        $str="QWERTYUIOPASDFGHJKLZXCVBNM1234567890qwertyuiopasdfghjklzxcvbnm";
                        str_shuffle($str);
                        $peerName=substr(str_shuffle($str),26,10);
                        $data = [
                                'devicetype' => 'iPad6,11',
                                'peerName' => $peerName,
                                'version' => '3.0.1',
                                'serial' => $serial,
                                'peerId' => $userid,
                                'appType' => 'iPad',
                                'deviceName' => 'iPad',
                                'ip' => '192.168.1.216',
                                'OSVersion' => 'iOS 11.2.1',
                                'systemversion' => 'iOS 11.2.1',
                                'companyid' => $companyid,
                                'sdkVersion' => 'TKRoomSDK-2.2.9',
                                'cpuArchitecture' => 'ARM64',
                                'statistical' => [
                                        'time' => $timestamp
                                ]
                        ];
                        $insertRes = $obj->insertOne($data); 
                        if($insertRes){
                                $total += 1;
                        }                               
                }
                $res =  return_format("机构id:".$companyid.",教室id:".$serial.",userid:".$userid.",已经插入了".$total."条数据",0) ;
                $this->ajaxReturn($res);               

        }
        //插入网络设备信息
        public function insertNetworkEquipment($dbname,$collection,$userid,$otheruserid,$serial,$companyid,$starttime,$endtime,$gaptime){
                $config = ['dbname'=>$dbname,'collection'=>$collection];
                $obj = new MongodbManage($config);

                $starttime = strtotime($starttime);
                $endtime = strtotime($endtime);
                $num = floor(($endtime - $starttime)/$gaptime);
                $quality = ['1','2','3','4','5'];
                $total = 0;
                
                for($i=0;$i<$num;$i++){
                        $flag = true;
                        $timestamp = $starttime + $i*$gaptime;
                        //过滤掉已经插入的数据
                        $where = [];
                        $options = [];
                        $where['myPeerId'] = $userid;
                        $where['serial'] = $serial;
                        $where['companyid'] = $companyid;
                        $where['statistical']['0']['peerId'] = $otheruserid;
                        $where['statistical']['time'] = $timestamp;
                        $option = [];
                        $res = $obj->find($where,$options);
                        if($res){
                                continue;
                        }

                        $video_bitsPerSecond = (string)(rand(0,100));
                        $video_packetsLost = (string)(rand(0,100));
                        $video_totalPackets = (string)(rand(0,100000));
                        $video_currentdelay = (string)(rand(0,100));
                        $video_netquality = (string)($quality[array_rand($quality)]);

                        $audio_bitsPerSecond = (string)(rand(0,100));
                        $audio_packetsLost = (string)(rand(0,100));
                        $audio_totalPackets = (string)(rand(0,100000));
                        $audio_currentdelay = (string)(rand(0,100));
                        $audio_netquality = (string)(array_rand($quality));

                        $cpuOccupancy = (string)(rand(0,100));
                        $streamId = (string)(rand(10000000,99999990));

                        $str="QWERTYUIOPASDFGHJKLZXCVBNM1234567890qwertyuiopasdfghjklzxcvbnm";
                        str_shuffle($str);
                        $peerName=substr(str_shuffle($str),26,10);

                        $data = [
                        'companyid'=>$companyid,
                        "serial"=>$serial,
                        'myPeerId'=>$userid,
                        'statistical'=>[
                                "0"=>[
                                        "video"=>[
                                                "bitsPerSecond"=>$video_bitsPerSecond,
                                                "packetsLost"=>$video_packetsLost,
                                                "totalPackets"=>$video_totalPackets,
                                                "currentDelay"=>$video_currentdelay,
                                                "frameRate"=>"10",
                                                "frameWidth"=>"320",
                                                "frameHeight"=>"240",
                                                "timestamp"=>(string)($timestamp),
                                                "netquality"=>$video_netquality                                        
                                        ],
                                        "audio"=>[
                                                "bitsPerSecond"=>$audio_bitsPerSecond,
                                                "packetsLost"=>$audio_packetsLost,
                                                "totalPackets"=>$audio_totalPackets,
                                                "currentDelay"=>$audio_currentdelay,
                                                "frameRate"=>"10",
                                                "frameWidth"=>"320",
                                                "frameHeight"=>"240",
                                                "timestamp"=>(string)($timestamp),
                                                "netquality"=>$audio_netquality                                        
                                        ],
                                        "cpuOccupancy"=>$cpuOccupancy,
                                        "peerId"=> $otheruserid,
                                        "peerName"=>$peerName,
                                        "streamId"=>$streamId
                                ],
                                "time"=>$timestamp
                            ],
                        ]; 
                        $insertRes = $obj->insertOne($data); 
                        if($insertRes){
                                $total += 1;
                        }                      
                }
                $res =  return_format("机构id:".$companyid.",教室id:".$serial.",userid:".$userid.",otheruserid:".$otheruserid."已经插入了".$total."条数据",0) ;
                $this->ajaxReturn($res); 
        }
        //测试查询
        public function select(){
                // $client = new \MongoDB\Client();
                // //$collection = (new \MongoDB\Client)->local->networkequipment;
                // $collection = $client->selectCollection('local', 'networkequipment');
                // $cursor = $collection->find(
                //     [
                //         'companyid' => '10618'
                //         //'statistical.0.peerName' => 'uhe0oVjGYd',
                //     ],
                //     [ 
                //         'limit' => 1000000,
                //         'projection' => [
                //         ],
                //     ]
                // );
                // //var_dump($cursor);
                // //die;
                // $data = [];
                // if ($cursor) {
                //    foreach ($cursor as $key => $value) {
                //     array_push($data, $value);
                //    }
                // }
                // // var_dump($data);
                // // die;
                // $str = json_encode($data);
                // $arr = json_decode($str,true);
                // var_dump($arr);
                // die;
                $dbname = "local";
                $collection = "equipment";
                $config = ['dbname'=>$dbname,'collection'=>$collection];
                $obj = new MongodbManage($config);
                // var_dump($obj->collection);
                // die;
                $where['companyid'] = '10618';
                $where['statistical.time'] = ['$lte'=>1533630012];
                $option['skip'] = 0;
                $option['limit'] = 10;
                $option['projection'] = [];
                $res = $obj->find($where,$option);
                var_dump($res);
                die;

                
        }
}
