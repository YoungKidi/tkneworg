<?php
/**
 * Class MongodbClient
 * 封装php7 mongod操作类
 *
 */
class MongodbClient{
   
   protected $link;
   protected $mongodb;
   protected $dbname;
   protected $collection;
   protected $bulk;
   protected $writeConcern;
   public function __construct($config)
   {
      //连接mongodb
      if(config('mongodb.Mongodb_user') == ''){
        $this->link = 'mongodb://'.config('mongodb.Mongodb_server').':'.config('mongodb.Mongodb_port');
      }else{
        $this->link = 'mongodb://'.config('mongodb.Mongodb_user').':'.config('mongodb.Mongodb_pwd').'@'.config('mongodb.Mongodb_server').':'.config('mongodb.Mongodb_port');
      }
      //校验必填参数
      if (!$config['dbname'] || !$config['collection']) {
         # code...
         return_format('', 50000, lang('link_mongodb_fail'));
      }
      $this->mongodb = new MongoDB\Driver\Manager($this->link);
      // var_dump($this->mongodb);
      // die;
      //选择数据库(见配置文件)
      $this->dbname = $config['dbname'];
      //选择集合名
      $this->collection = $config['collection'];
      $this->bulk = new MongoDB\Driver\BulkWrite();
      $this->writeConcern   = new MongoDB\Driver\WriteConcern(MongoDB\Driver\WriteConcern::MAJORITY, 100);
   }
 
    /**
     * Created by PhpStorm.
     * function: query
     * Description:查询方法
     * User: zzq
     * Email 461464213@qq.com
     * @param array $where
     * @param array $option
     * @return string
      */
   public function query($where=[],$option=[])
   {
      $query = new MongoDB\Driver\Query($where,$option);
      $result = $this->mongodb->executeQuery("$this->dbname.$this->collection", $query);
      // var_dump($result);
      // die;
      $data = [];
      if ($result) {
         # code...
         foreach ($result as $key => $value) {
            # code...
            array_push($data, $value);
         }
      }
 
      return json_encode($data);
   }
 
    /**
     * Created by PhpStorm.
     * function: getCount
     * Description:获取统计数
     * User: zzq
     * Email 461464213@qq.com
     * @param array $where
     * @return int
     *
     */
   public function getCount($where=[])
   {
      $command = new MongoDB\Driver\Command(['count' => $this->collection,'query'=>$where]);
      $result = $this->mongodb->executeCommand($this->dbname,$command);
      $res = $result->toArray();
      $cnt = 0;
      if ($res) {
         # code...
         $cnt = $res[0]->n;
      }
 
      return $cnt;
   }
 
    /**
     * Created by PhpStorm.
     * function: page
     * Description:分页数据
     * User: zzq
     * Email 461464213@qq.com
     * @param array $where  查询条件
     * @param int $page     页码数
     * @param int $limit    每页数目
     * @param array $projection  排除掉的字段 ['_id'=>0]//表示排除_id字段
     * @param array $sort        排序['字段名'=>1表示升序 -1代表降序]    
     * @return data->page表示页数,count表示
     *
     */
   public function page($where=[],$page=1,$limit=10,$projection=[],$sort=[])
   {
      
      $count = $this->getCount($where);
      //获取目标的总页数
      $endpage = ceil($count/$limit);
      if($endpage < 1){
        //说明暂无数据
        $data = [];
        return $data;
      }
      if ($page>$endpage) {
         //表示参数page大于总的页数
         $data = [];
         return $data;
      }elseif ($page <1) {
         $page = 1;
      }
      $skip = ($page-1)*$limit;
      $options['skip'] = $skip;
      $options['limit'] = $limit;
      if(!empty($projection)){
        $options['projection'] = $projection;
      }
      if(!empty($sort)){
        $options['sort'] = $sort;
      }
      $data = $this->query($where,$options);
      //json转成数组
      $data = json_decode($data,true);
      return $data;
   }

    /**
     * Created by PhpStorm.
     * function: page
     * Description:根据条件直接查询所需要的数据
     * User: zzq
     * Email 461464213@qq.com
     * @param array $where  查询条件
     * @param int $page     页码数
     * @return data
     *
     */
   public function select($where=[],$options=[]){
      $data = $this->query($where,$options);
      //json转成数组
      $data = json_decode($data,true);
      return $data;    
   }
 
    /**
     * Created by PhpStorm.
     * function: update
     * Description:更新操作
     * User: zzq
     * Email 461464213@qq.com
     * @param array $where 如['id'=>1]
     * @param array $update  ['name'=>'张三','age'=>'12']
     * @param bool $multi //未知按默认
     * @param bool $upsert //未知按默认
     * @return int|null
     *
     */
   public function update($where=[],$update=[],$multi=true,$upsert=false)
   {
      $this->bulk->update($where,['$set' => $update], ['multi' => $multi, 'upsert' => $upsert]);
      $result = $this->mongodb->executeBulkWrite("$this->dbname.$this->collection", $this->bulk, $this->writeConcern);
      return $result->getModifiedCount();
   }
 
    /**
     * Created by PhpStorm.
     * function: insert
     * Description:插入
     * User: zzq
     * Email 461464213@qq.com
     * @param array $data
     * @return int 插入的数目
     *
     */
   public function insert($data=[])
   {
      // 插入数据
      $obj = new MongoDB\Driver\BulkWrite();
      //$this->bulk->insert($data);
      $obj->insert($data);
      //获取结果
      //$result = $this->mongodb->executeBulkWrite($this->dbname.'.'.$this->collection, $this->bulk,$this->writeConcern);
      $result = $this->mongodb->executeBulkWrite($this->dbname.'.'.$this->collection, $obj,$this->writeConcern);
      return $result->getInsertedCount();
   }
 
    /**
     * Created by PhpStorm.
     * function: delete
     * Description:删除
     * User: zzq
     * Email 461464213@qq.com
     * @param array $where
     * @param int $limit  limit为1删除符合条件的第一条数据,为0的时候表示删除所有符合条件的数据
     * @return int 删除的数目
     *
     */
   public function delete($where=[],$limit=1)
   {
      // 删除数据
      $this->bulk->delete($where,['limit'=>$limit]);
      //获取结果
      $result = $this->mongodb->executeBulkWrite($this->dbname.'.'.$this->collection, $this->bulk,$this->writeConcern);
      return $result->getDeletedCount();
   }
   
}

