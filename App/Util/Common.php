<?php

namespace App\Util;

use EasySwoole\Component\CoroutineSingleTon;

/**公共函数*/
class Common
{
    use CoroutineSingleTon;

    public function getRedisPool()
    {
        $redisPool = \EasySwoole\RedisPool\RedisPool::defer();

        return $redisPool;
    }

    public function remember(string $key, int $seconds, callable $call)
    {
        $cacheVal = $this->getRedisPool()->get($key);
        if(!$cacheVal){
            $data = call_user_func($call);
            $this->getRedisPool()->set($key, json_encode($data), ['NX','EX' => $seconds]);
            $cacheVal = $this->getRedisPool()->get($key);
        }
        
        $cacheValArr = json_decode($cacheVal, true);

        return $cacheValArr;
    }

    public function generateSignature($params, $secretKey)
    {
        ksort($params);
        unset($params['sign']);
        $signStr = '';
        foreach ($params as $key => $value) {
            $signStr .= "{$key}={$value}&"; 
        }
        $signStr .= 'key='.$secretKey;
        
        return md5($signStr);
    }
}