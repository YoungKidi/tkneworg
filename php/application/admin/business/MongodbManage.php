<?php
/**mongodb连接类**/
namespace app\admin\business;
class MongodbManage{

   protected $link;
   protected $mongodb;
   public $collection;
   //构造方法
   public function __construct($config)
   {
      //连接mongodb
      $this->link = 'mongodb://'.config('mongodb.Mongodb_server').':'.config('mongodb.Mongodb_port');
      //创建实例化对象
      $this->mongodb = new \MongoDB\Client($this->link);
      //选择数据库 文档
      //校验必填参数
      if (!$config['dbname'] || !$config['collection']) {
         # code...
         return_format('', 50000, lang('link_mongodb_fail'));
      }
      $this->collection = $this->mongodb->selectCollection($config['dbname'], $config['collection']);
   }


    //mongodb查询多条记录
    /**
     * 
     * @date   2018-08-07
     * @Author zzq
     * @param  where      过滤条件[字段条件 array]
     * @param  option     其他条条件[skip(从第几条) limit(取出几条) projection(不取出哪些字段)]             
     * @return  array          
     */
    public function find($where=[],$option=[]){         
        $cursor = $this->collection->find($where,$option);
        $data = [];
        if ($cursor) {
           foreach ($cursor as $key => $value) {
            array_push($data, $value);
           }
        }
        $str = json_encode($data);
        $arr = json_decode($str,true);
        return $arr;        
    }

    //mongodb查询单条记录
    /**
     * 
     * @date   2018-08-07
     * @Author zzq
     * @param  where      过滤条件[字段条件 array]
     * @param  option     其他条条件[skip(从第几条) limit(取出几条) projection(不取出哪些字段)]             
     * @return  array          
     */
    public function findOne($where=[],$option=[]){         
        $cursor = $this->collection->findOne($where,$option);
        $str = json_encode($cursor);
        $arr = json_decode($str,true);
        return $arr;        
    }

    //mongodb插入单条记录
    /**
     * 
     * @date   2018-08-07
     * @Author zzq
     * @param  where      插入的数组[ array]
     * @return  int       插入的数目          
     */
    public function insertOne($data){

        $insertOneResult = $this->collection->insertOne($data);
        return $insertOneResult->getInsertedCount();
    }
}   
