<?php

namespace App\Util;

/**Redis*/
class Redis
{
    public static function getRedisPool()
    {
        $redisPool = \EasySwoole\RedisPool\RedisPool::defer();

        return $redisPool;
    }

    //仅限string 类型储存
    public static function remember(string $key, int $seconds, callable $call)
    {
        $cacheVal = self::getRedisPool()->get($key);
        if(!$cacheVal){
            $data = call_user_func($call);
            if(is_array($data)){
                $data = json_encode($data);
            }
            self::getRedisPool()->set($key, $data, ['NX','EX' => $seconds]);
            $cacheVal = self::getRedisPool()->get($key);
        }
        
        $cacheValArr = json_decode($cacheVal, true);

        return $cacheValArr;
    }
}