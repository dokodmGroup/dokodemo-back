<?php

namespace Redis;

/**
 * redis操作类
 */
class AbstractModel {

    /**
     * 表名和键的分割符号
     */
    const DELIMITER = '-';

    /**
     * 连接的库
     * 
     * @var int 
     */
    protected $_db = 0;

    /**
     * 获取redis连接
     * 
     * @staticvar null $redis
     * @return \Redis
     * @throws \Exception
     */
    public function getRedis() {
        static $redis = null;

        if (!$redis) {
            $conf = \Yaf\Registry::get('config')->get('redis.database.params');
            if (!$conf) {
                throw new \Exception('redis连接必须设置');
            }

            $redis = new \Redis();
            $redis->connect($conf['host'], $conf['port']);
            $redis->select($this->_db);
        }

        return $redis;
    }

    /**
     * 删除key
     * 
     * @param string $key
     * @return 
     */
    public function del($key) {
        return $this->getRedis()->del($key);
    }

    /**
     * 获取keys
     * 
     * @param string $pattern
     */
    public function keys($pattern) {
        return $this->getRedis()->keys($pattern);
    }

    /**
     * 增加缓存
     * 
     * @param string $key
     * @param mix $value
     */
    public function set($key, $value) {
        return $this->getRedis()->set($key, $value);
    }

    /**
     * 根据key值获取缓存数据
     * 
     * @param string $key
     * @return mixed
     */
    public function get($key) {
        return $this->getRedis()->get($key);
    }

    /**
     * redis自增1
     * 
     * @param string $key
     * @return int
     */
    public function incr($key) {
        return $this->getRedis()->incr($key);
    }

    /**
     * redis自减1
     * 
     * @param string $key
     * @return int
     */
    public function decr($key) {
        return $this->getRedis()->decr($key);
    }

    /**
     * redis自减1
     * 
     * @param string $key
     * @return int
     */
    public function decrby($key, $decrement) {
        return $this->getRedis()->decrby($key, $decrement);
    }

    /**
     * 增加列表内的元素
     * 
     * @param string $key
     * @param mix $value
     * @return int
     */
    public function lpush($key, $value) {
        return $this->getRedis()->lpush($key, $value);
    }

    /**
     * 获取列表内的元素
     * 
     * @param string $key
     * @param int $start
     * @param int $stop
     * @return mix
     */
    public function lrange($key, $start, $stop) {
        return $this->getRedis()->lrange($key, $start, $stop);
    }

    /**
     * 增加集合内的元素
     * 
     * @param string $key
     * @param mix $value
     * @return int
     */
    public function sadd($key, $value) {
        return $this->getRedis()->sadd($key, $value);
    }

    /**
     * 列出集合内的元素
     * 
     * @param int $key
     * @return mix
     */
    public function smembers($key) {
        return $this->getRedis()->smembers($key);
    }

}
