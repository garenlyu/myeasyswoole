<?php

namespace App\Util;

/**Redis*/
class Redis
{
    use \EasySwoole\Component\CoroutineSingleTon;

    public function getRedisPool()
    {
        $redisPool = \EasySwoole\RedisPool\RedisPool::defer();

        return $redisPool;
    }

    //仅限string 类型储存
    public function remember(string $key, int $seconds, callable $call)
    {
        $cacheVal = $this->getRedisPool()->get($key);
        if(!$cacheVal){
            $data = call_user_func($call);
            if(is_array($data)){
                $data = json_encode($data);
            }
            $this->getRedisPool()->set($key, $data, ['NX','EX' => $seconds]);
            $cacheVal = $this->getRedisPool()->get($key);
        }
        
        $cacheValArr = json_decode($cacheVal, true);

        return $cacheValArr;
    }
}