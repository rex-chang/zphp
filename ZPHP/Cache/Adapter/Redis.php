<?php
/**
 * User: shenzhe
 * Date: 13-6-17
 */


namespace ZPHP\Cache\Adapter;

use ZPHP\Cache\ICache,
    ZPHP\Manager;

class Redis implements ICache
{
    private $redis;

    public function __construct($config)
    {
        if (empty($this->redis)) {
            $this->redis = Manager\Redis::getInstance($config);
        }
    }

    function  __call($name, $params)
    {
//        $params[0] = $this->uKey($params[0]);
//        var_dump('params:',$params);
        if (method_exists($this->redis, $name)) ;
        return call_user_func_array(array($this->redis, $name), $params);
    }

    public function enable()
    {
        return true;
    }

    public function selectDb($db)
    {
        $this->redis->select($db);
    }

    public function add($key, $value, $expiration = 0)
    {
        return $this->redis->setNex($key, $expiration, $value);
    }

    public function set($key, $value, $expiration = 0)
    {
        if ($expiration) {
            return $this->redis->setex($key, $expiration, $value);
        } else {
            return $this->redis->set($key, $value);
        }
    }

    public function addToCache($key, $value, $expiration = 0)
    {
        return $this->set($key, $value, $expiration);
    }

    public function get($key)
    {
        return $this->redis->get($key);
    }

    public function getCache($key)
    {
        return $this->get($key);
    }

    public function delete($key)
    {
        return $this->redis->delete($key);
    }

    public function increment($key, $offset = 1)
    {
        return $this->redis->incrBy($key, $offset);
    }

    public function decrement($key, $offset = 1)
    {
        return $this->redis->decBy($key, $offset);
    }

    public function clear()
    {
        return $this->redis->flushDB();
    }

    //自加方法
    public function sAdd($key, $value)
    {
        return $this->redis->sAdd($key, $value);
    }

//    public function getKeys($key)
//    {
//        return $this->redis->getKeys($key);
//    }

    public function sRemove($key, $value)
    {
        return $this->redis->sRemove($key, $value);
    }

    public function sRandMember($key)
    {
        return $this->redis->sRandMember($key);
    }

    public function hSetNx($key, $ext, $value)
    {
        return $this->redis->hSetNx($key, $ext, $value);
    }

    public function hGetAll($key)
    {
        return $this->redis->hGetAll($key);
    }

    public function  hMset($key, array $values)
    {
        return $this->redis->hMset($key, $values);
    }

    public function hIncrBy($key, $ext, $offset = 1)
    {
        return $this->redis->hIncrBy($key, $ext, $offset);
    }

    public function hDel($key, $ext)
    {
        return $this->redis->hDel($key, $ext);
    }
}