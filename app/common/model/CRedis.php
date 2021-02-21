<?php

/**
 * Redis工具类
 * User: orginly
 * Date: 2021/2/10
 * Time: 15:57
 */

namespace app\common\model;

class CRedis
{
    private $_pro; //统一前缀
    private $_redis;
    static private $_instance;

    public function __construct(){
        $this->_redis = new \Redis();
        $this->_redis->connect(env_custom('redis.host'), env_custom('redis.port'));
        $this->_redis->auth(env_custom('redis.password'));
        $this->_redis->select(env_custom('redis.select'));
        $this->_pro = env_custom('redis.pre');
    }

    /**
     * Notes:获取Redis实例
     * @return CRedis
     */
    public static function getInstance(){
        if (is_null(self::$_instance) || !isset(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * 删除redis
     * @param string $key [键名/表名]
     * @return int /-1异常
     */
    public function del(string $key){
        try {
            $key = $this->_pro . $key;
            return $this->_redis->del($key);
        } catch (\Exception $ex) {
            setErrLog('CRedis > del ', $ex);
            return -1;
        }
    }

    /**
     * 设置redis设置值和过期时间
     * @param $key [键]
     * @param $value [值]
     * @param $expire [过期时间]
     * @return bool [成功返回true,失败false]
     */
    public function setex($key, $value, $expire){
        $key = $this->_pro . $key;
        return $this->_redis->setex($key, $expire, $value);
    }

    /**
     * 获取单个redis值
     * @param $key [键名]
     * @return bool|string|int /-1异常
     */
    public function get($key){
        try {
            $key = $this->_pro . $key;
            return $this->_redis->get($key);
        } catch (\Exception $ex) {
            setErrLog('CRedis > get', $ex);
            return -1;
        }
    }

    /**
     * 向redis对应表设置值
     * @param string $key [表名参数]
     * @param string $field [主键值]
     * @param string $value [数据值]
     * @return bool|int /-1异常
     */
    public function hSet($key, $field, $value){
        try {
            $key = $this->_pro . $key;
            return $this->_redis->hSet($key, $field, $value);
        } catch (\Exception $ex) {
            setErrLog('CRedis > hSet', $ex);
            return -1;
        }
    }

    /**
     * 获取redis对应表指定主键值
     * @param string $key [表名参数]
     * @param string $field [主键值]
     * @return string|int /-1异常
     */
    public function hGet($key, $field){
        try {
            $key = $this->_pro . $key;
            return $this->_redis->hGet($key, $field);
        } catch (\Exception $ex) {
            setErrLog('CRedis > hGet', $ex);
            return -1;
        }
    }

    /**
     * 获取redis对应表所有数据
     * @param $key [表名参数]
     * @return array|int /-1异常
     */
    public function hGetAll($key){
        try {
            $key = $this->_pro . $key;
            return $this->_redis->hGetAll($key);
        } catch (\Exception $ex) {
            setErrLog('CRedis > hGetAll ', $ex);
            return -1;
        }
    }

    /**
     * 获取redis对应表的所有主键值
     * @param $key [表名参数]
     * @return array|int /-1异常
     */
    public function hKeys($key){
        try {
            $key = $this->_pro . $key;
            return $this->_redis->hKeys($key);
        } catch (\Exception $ex) {
            setErrLog('CRedis > hKeys ', $ex);
            return -1;
        }
    }

    /**
     * 向redis对应表设置一条数组数据
     * @param string $key [表名参数]
     * @param string $field [主键值]
     * @param array $value [一条数组数据]
     * @return bool|int /-1异常
     */
    public function shSet($key, $field, $value){
        try {
            $key = $this->_pro . $key;
            return $this->_redis->hSet($key, $field, serialize($value));
        } catch (\Exception $ex) {
            setErrLog('CRedis > shSet ', $ex);
            return -1;
        }
    }

    /**
     * 获取redis对应表指定主键的一条数据
     * @param string $key [表名参数]
     * @param $field [主键值]
     * @return array|int|mixed /-1异常
     */
    public function shGet($key, $field){
        try {
            $key    = $this->_pro . $key;
            $result = $this->_redis->hGet($key, $field);
            return $result ? unserialize($result) : [];
        } catch (\Exception $ex) {
            setErrLog('CRedis > shGet ', $ex);
            return -1;
        }
    }

    /**
     * 获取redis对应表所有数据(已unserialize处理)
     * @param $key [表名参数]
     * @return array|int /-1异常
     */
    public function shGetAll($key){
        try {
            $key = $this->_pro . $key;
            $all = $this->_redis->hGetAll($key);
            $arr = array();
            foreach ($all as $key => $item) {
                $arr[$key] = unserialize($item);
            }
            return $arr;
        } catch (\Exception $ex) {
            setErrLog('CRedis > shGetAll ', $ex);
            return -1;
        }
    }

    /**
     * 删除redis对应表指定主键的一条/多条数据
     * @param string $key [表名参数]
     * @param $field [主键值（单个/多个）]
     * @return int [影响行数] /-1异常
     */
    public function hDel($key, $field){
        try {
            $key    = $this->_pro . $key;
            $delNum = 0;
            if (is_array($field)) {
                foreach ($field as $f) {
                    $delNum += $this->_redis->hDel($key, $f);
                }
            } else {
                $this->_redis->hDel($key, $field);
                $delNum = 1;
            }
            return $delNum;
        } catch (\Exception $ex) {
            setErrLog('CRedis > hDel ', $ex);
            return -1;
        }
    }

    /**
     * 向列表左边加入一条数据
     * @param $key
     * @param $value
     * @return bool|int /-1异常
     */
    public function lPush($key, $value){
        try {
            $key = $this->_pro . $key;
            return $this->_redis->lPush($key, $value);
        } catch (\Exception $ex) {
            setErrLog('CRedis > lPush ', $ex);
            return -1;
        }
    }

    /**
     * 向列表右边加入一条数据
     * @param $key
     * @param $value
     * @return bool|int /-1异常
     */
    public function rPush($key, $value){
        try {
            $key = $this->_pro . $key;
            return $this->_redis->rPush($key, $value);
        } catch (\Exception $ex) {
            setErrLog('CRedis > rPush ', $ex);
            return -1;
        }
    }

    /**
     * 设置列表里index位置的值
     * @param $key
     * @param $index
     * @param $value
     * @return bool|int /-1异常
     */
    public function lSet($key, $index, $value){
        try {
            $key = $this->_pro . $key;
            return $this->_redis->lSet($key, $index, $value);
        } catch (\Exception $ex) {
            setErrLog('CRedis > lSet ', $ex);
            return -1;
        }
    }

    /**
     * 返回列表里index位置的值
     * @param $key
     * @param $index
     * @return String|int /-1异常
     */
    public function lIndex($key, $index){
        try {
            $key = $this->_pro . $key;
            return $this->_redis->lIndex($key, $index);
        } catch (\Exception $ex) {
            setErrLog('CRedis > lIndex ', $ex);
            return -1;
        }
    }

    /**
     * 返回并删除列表的第一个元素
     * @param $key
     * @return string|int /-1异常
     */
    public function lPop($key){
        try {
            $key = $this->_pro . $key;
            return $this->_redis->lPop($key);
        } catch (\Exception $ex) {
            setErrLog('CRedis > lPop ', $ex);
            return -1;
        }
    }

    /**
     * 返回并删除列表的最后一个元素
     * @param $key
     * @return string|int /-1异常
     */
    public function rPop($key){
        try {
            $key = $this->_pro . $key;
            return $this->_redis->rPop($key);
        } catch (\Exception $ex) {
            setErrLog('CRedis > rPop', $ex);
            return -1;
        }
    }

    /**
     * 获取列表的长度
     * @param $key
     * @return int /-1异常
     */
    public function lLen($key){
        try {
            $key = $this->_pro . $key;
            return $this->_redis->lLen($key);
        } catch (\Exception $ex) {
            setErrLog('CRedis > lLen ', $ex);
            return -1;
        }
    }

    /**
     * 获取列表中从start到end的数据
     * @param $key /表名
     * @param int $start /起始
     * @param int $end /结束(-1为所有)
     * @return array|int /-1异常
     */
    public function lRange($key, $start = 0, $end = -1){
        try {
            $key  = $this->_pro . $key;
            $list = $this->_redis->lRange($key, $start, $end);
            return $list;
        } catch (\Exception $ex) {
            setErrLog('CRedis > lRange', $ex);
            return -1;
        }
    }

    /**
     * 往集合里添加元素
     * @param $key
     * @param $value
     * @return int /-1异常
     */
    public function sAdd($key, $value){
        try {
            $key = $this->_pro . $key;
            return $this->_redis->sAdd($key, $value);
        } catch (\Exception $ex) {
            setErrLog('CRedis > sAdd', $ex);
            return -1;
        }
    }

    /**
     * 返回集合中所有元素(自动排序)
     * @param $key
     * @return array|int /-1异常
     */
    public function sMembers($key){
        try {
            $key = $this->_pro . $key;
            return $this->_redis->sMembers($key);
        } catch (\Exception $ex) {
            setErrLog('CRedis > sMembers', $ex);
            return -1;
        }
    }

    /**
     * 返回集合中一个或多个随机数
     * @param $key [集合名]
     * @param $count [数量]
     * @return array|string|int /-1异常
     */
    public function sRandMember($key, $count){
        try {
            $key = $this->_pro . $key;
            return $this->_redis->sRandMember($key, $count);
        } catch (\Exception $ex) {
            setErrLog('CRedis > sRandMember', $ex);
            return -1;
        }
    }

    /**
     * 从集合中删除一个元素
     * @param $key
     * @param $value
     * @return int /-1异常
     */
    public function sRem($key, $value){
        try {
            $key = $this->_pro . $key;
            return $this->_redis->sRem($key, $value);
        } catch (\Exception $ex) {
            setErrLog('CRedis > sRem', $ex);
            return -1;
        }
    }

    /**
     * 写入集合
     * @param $key
     * @param $score1 /排序1
     * @param $value1 /值1 (可存json格式)
     * @param null $score2
     * @param null $value2
     * @param null $scoreN
     * @param null $valueN
     * @return int /记录总数，-1异常
     */
    public function zAdd($key, $score1, $value1, $score2 = null, $value2 = null, $scoreN = null, $valueN = null){
        try {
            $key = $this->_pro . $key;
            return $this->_redis->zAdd($key, $score1, $value1, $score2, $value2, $scoreN, $valueN);
        } catch (\Exception $ex) {
            setErrLog('CRedis > zAdd', $ex);
            return -1;
        }
    }

    /**
     * 顺序查询集合
     * @param $key
     * @param $start /开始记录数
     * @param $end /结束记录数,-1查所有
     * @param null $withScore /按score排序查询
     * @return array|int /-1异常
     */
    public function zRange($key, $start, $end, $withScore = null){
        try {
            $key = $this->_pro . $key;
            return $this->_redis->zRange($key, $start, $end, $withScore);
        } catch (\Exception $ex) {
            setErrLog('CRedis > zRange ', $ex);
            return -1;
        }
    }

    /**
     * 逆序查询集合
     * @param $key
     * @param $start /开始记录数
     * @param $end /结束记录数,-1查所有
     * @param null $withScore /按score排序查询
     * @return array|int /-1异常
     */
    public function zRevRange($key, $start, $end, $withScore = null){
        try {
            $key = $this->_pro . $key;
            return $this->_redis->zRevRange($key, $start, $end, $withScore);
        } catch (\Exception $ex) {
            setErrLog('CRedis > zRevRange ', $ex);
            return -1;
        }
    }

    /**
     * 移除集合
     * @param $key
     * @param $member1 /成员1
     * @param null $member2 /成员2
     * @param null $memberN /成员N
     * @return int /-1异常
     */
    public function zRem($key, $member1, $member2 = null, $memberN = null){
        try {
            $key = $this->_pro . $key;
            return $this->_redis->zRem($key, $member1, $member2, $memberN);
        } catch (\Exception $ex) {
            setErrLog('CRedis > zRem ', $ex);
            return -1;
        }
    }

    /**
     * 订阅频道
     * @param array $patterns [需要订阅的频道]
     * @param $callback [回调函数]
     * @return int
     */
    public function psubscribe($patterns, $callback){
        try {
            $this->_redis->psubscribe($patterns, $callback);
        } catch (\Exception $ex) {
            setErrLog('CRedis > p ', $ex);
            return -1;
        }
    }

    /**
     * 设置Redis配置
     * @param $operation [SET|GET]
     * @param $key [键]
     * @param $value [值]
     * @return array|int
     */
    public function config($operation, $key, $value){
        try {
            return $this->_redis->config($operation, $key, $value);
        } catch (\Exception $ex) {
            setErrLog('CRedis > config ', $ex);
            return -1;
        }
    }

    /**
     * 配置Redis选项
     * @param $option [例如\Redis::OPT_READ_TIMEOUT Int类型]
     * @param $value [值]
     * @return bool|null
     */
    public function setOption($option, $value){
        try {
            return $this->_redis->setOption($option, $value);
        } catch (\Exception $ex) {
            setErrLog('CRedis > setOption ', $ex);
            return null;
        }
    }

}
