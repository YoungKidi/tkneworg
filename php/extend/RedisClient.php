<?php
/**
 * Created by PhpStorm.
 * User: zzq
 * Date: 18/07/03
 * Time: 上午09:52
 */
class RedisClient {
    public $redis = "";
    /**
     * 定义单例模式的变量
     * @var null
     */
    private static $_instance = null;

    public static function getInstance() {
        if(empty(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    private function __construct() {
        $this->redis = new \Redis();
        $result = $this->redis->connect(config('redis.redis_host'), config('redis.redis_port'), config('redis.redis_timeout'));
        //校验连接
        if($result === false) {
            return_format('', 50001, lang('link_redis_fail'));
        }
        //if( config('redis.redis_pwd') !=='' ){
            //检验密码
            //$res = $this->redis->auth(config('redis.redis_pwd')); //验证密码
            //if($res === false) {
                //return_format('', 50005, lang('redispass_error'));
            //}
        //}
    }

    /**
     * set 设置某个键的值
     * @param $key
     * @param $value
     * @param int $time
     * @return bool|string
     */
    public function set($key, $value, $time = 0 ) {
        if(!$key) {
            return '';
        }
        if(is_array($value)) {
            $value = json_encode($value);
        }
        if(!$time) {
            return $this->redis->set($key, $value);
        }

        return $this->redis->setex($key, $time, $value);
    }

    /**
     * get 获取某个键的值
     * @param $key
     * @return bool|string
     */
    public function get($key) {
        if(!$key) {
            return '';
        }

        return $this->redis->get($key);
    }
    /**
     * incr 添加某个键的值
     * @param $key
     * @return int
     */
    public function incr($key){
        return $this->redis->incr($key);
    } 
    /**
     * incr 获取集合的所有成员
     * @param $key
     * @return array
     */
    public function sMembers($key) {
        return $this->redis->sMembers($key);
    }

    /**
     * @param $name
     * @param $arguments
     * @return array
     */
    public function __call($name, $arguments) {
        //echo $name.PHP_EOL;
        //print_r($arguments);
        if(count($arguments) != 2) {
            return '';
        }
        $this->redis->$name($arguments[0], $arguments[1]);
    }

    /**
     * 删除rediskey
     * @param $key
     * @return int
     */
    public function del($key){
        return $this->redis->del($key);
    }



    /**
     * 获取list队列的数据
     * @param $key  //list的键名
     * @param $key  //开始位置
     * @param $key  //结束位置
     * @return array
     */
    public function lrange($key,$start,$end) {
        $data = $this->redis->lrange($key,$start,$end);
        if(!$data){
            $data = [];
        }
        return $data;
    }    

    /**
     * * 获取list队列的长度
     * @param $key  //list的键名=
     * @return int
     */
    public function lsize($key){
        $size = $this->redis->lsize($key);
        return $size;
    }


    ###############哈希操作函数类型################
    //返回值说明
    //如果key不存在  返回false
    //如果key存在 不是哈希 返回false
    //key存在 不管有没有该field 成功返回1
    //例如$flag = $redis->hset('user','name','zs');
    /**
     * * //hset  设置某key的某个字段值
     * @param $key  //哈希的键名
     * @param $name  //哈希的字段名
     * @return mixed
     */
    public function hset($key,$name,$value){
        $res = $this->redis->hset($key,$name,$value);
        return $res;
    }
     
    //hget返回值说明
    //如果key不存在  返回false
    //如果key存在 不是哈希 返回false
    //key存在 没有field  返回false 
    //成功返回相应的信息
    //例如$flag = $redis->hget('user','name');
    /**
     * * //hset  获取某key的某个字段值
     * @param $key  //哈希的键名
     * @param $name  //哈希的字段名
     * @return mixed
     */
    public function hget($key,$name){
        $res = $this->redis->hget($key,$name);
        return $res;
    }

    //hdel
    //如果key不存在  返回false
    //如果key存在 不是哈希 返回false
    //成功返回 1
    //$flag = $redis->hdel('user','name');
    /**
     * * //hdel  删除某key的某个字段值
     * @param $key  //哈希的键名
     * @param $name  //哈希的字段名
     * @return bool|int  
     */
    public function hdel($key,$name){
        $res = $this->redis->hdel($key,$name);
        return $res;
    }    
     
    //hexists  //返回某个键的字段是否存在
    //如果key不存在  返回false
    //如果key存在 不是哈希 返回false
    //成功返回  true
    //$flag = $redis->hexists('user','name');
    /**
     * * //hexists  查看某key的某个字段是否存在
     * @param $key  //哈希的键名
     * @param $name  //哈希的字段名
     * @return bool 
     */
    public function hexists($key,$name){
        $res = $this->redis->hexists($key,$name);
        return $res;
    }      
     
    //hgetall  //返回某个键的所有字段以及所有的值
    //如果key不存在  返回[]
    //如果key存在 不是哈希 返回[]
    //成功返回  数组['field'=>'value']
    //$flag = $redis->hgetall('user');
    /**
     * * //hgetall  //返回某个键的所有字段以及所有的值
     * @param $key  //哈希的键名
     * @param $name  //哈希的字段名
     * @return array 
     */
    public function hgetall($key){
        $res = $this->redis->hexists($key);
        return $res;
    }  
     
    //hincrby
    //如果该key不是哈希 返回false
    //如果该key是哈希  field有没有都会被运算
    //该key不存在，将会创建key 和 field
    // 增量也可以为负数，相当于对指定字段进行减法操作。
    // 如果哈希表的 key 不存在，一个新的哈希表被创建并执行 HINCRBY 命令。
    // 如果指定的字段不存在，那么在执行命令前，字段的值被初始化为 0 
    //成功返回结果值为该key该字段的结果
    //$flag = $redis->hincrby('user','age','-1');

    /**
     * * //hincrby    改变改键的某个字段的值
     * @param $key  哈希的键名
     * @param $name  字段名
     * @param $value  改变量
     * @return $res bool|int  
     */  
    public function hincrby($key,$name,$limit){
        $res = $this->redis->hincrby($key,$name,$limit);
        return $res;
    }  
     
    //hincrbyfloat
    //如果该key不是哈希 返回false
    //如果该key是哈希  field有没有都会被运算
    //该key不存在，将会创建key 和 field
    // 增量也可以为负数，相当于对指定字段进行减法操作。
    // 如果哈希表的 key 不存在，一个新的哈希表被创建并执行 HINCRBY 命令。
    // 如果指定的字段不存在，那么在执行命令前，字段的值被初始化为 0 
    //成功返回结果值为该key该字段的结果
    //$flag = $redis->hincrbyfloat('user','age','-0.1');
    /**
     * * //hincrbyfloat    改变改键的某个字段的值
     * @param $key  哈希的键名
     * @param $name  字段名
     * @param $value  改变量
     * @return $res bool|int  
     */  
    public function hincrbyfloat($key,$name,$limit){
        $res = $this->redis->hincrbyfloat($key,$name,$limit);
        return $res;
    } 
     
    //hllen 返回field的数目
    //key不存在 返回0
    //key存在 不是哈希 返回false
    //$flag = $redis->hlen('user');
    /**
     * * //hlen    查看某个键的长度
     * @param $key  哈希的键名
     * @return $res bool|int  
     */   
    public function hlen($key){
        $res = $this->redis->hlen($key);
        return $res;
    } 

    //hmset
    //此命令会覆盖哈希表中已存在的字段。
    // 如果哈希表不存在，会创建一个空哈希表，并执行 HMSET 操作。
    //以上两条执行返回true
    //成功返回true
    //如果该key不是hash 返回false
    //$flag = $redis->hmset('user2',['height'=>'198','city'=>'Tokyo']); 
    /**
     * * //hset    设置某个键某些字段的值
     * @param $key  哈希的键名
     * @param $data  字段以及字段值的数组
     * @return $res bool  
     */    
    public function hmset($key,$data){
        $res = $this->redis->hmset($key,$data);
        return $res;
    } 
    //hmget
    //比如key存在 字段a不存在或者key不存在 
    //返回类似于以下的信息
    //array(1) { ["a"]=> bool(false) }      
    //key存在 不是哈希 返回false
    //$flag = $redis->hmget('user',['name','age']);
    /**
     * * //hmget    获取某个键某些字段的值
     * @param $key  哈希的键名
     * @param $data  字段的数组
     * @return $res bool|array  
     */
    public function hmget($key,$data){
        $res = $this->redis->hmget($key,$data);
        return $res;
    }     
     
     
    //hkeys 返回field的数组
    //key不存在 返回0
    //key存在 不是哈希 返回false
    //key存在 field存在 返回数组
    //$flag = $redis->hkeys('user');
    /**
     * * //hkeys  返回field的数组
     * @param $key  哈希的键名
     * @return $res int|bool|array  
     */
    public function hkeys($key){
        $res = $this->redis->hkeys($key);
        return $res;
    } 

    //hvals 返回field的值的数组
    //key不存在 返回0
    //key存在 不是哈希 返回false
    //key存在 field存在 返回{}
    //$flag = $redis->hvals('user');
    /**
     * * //hvals  返回field的值的数组
     * @param $key  哈希的键名
     * @return $res int|bool|array  
     */
    public function hvals($key){
        $res = $this->redis->hvals($key);
        return $res;
    } 

    ###############哈希操作函数类型################

    /**
     * * //redis操作lua脚本
     * @param $script  string
     * @param $args  array
     * @param $int  int
     * @return mixed  
     */    
    public function eval($script,$args,$int){
        return $this->redis->eval($script, $args, $int);
    }
}