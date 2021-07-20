<?php

namespace App\Util;

/**公共类静态方法*/
class Common
{
    public static function generateSignature($params, $secretKey)
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

    //生成uuid
    public static function generateUuid($prefix = '')
    {
        $chars = md5(uniqid(mt_rand(), true));
        $uuid = substr ( $chars, 0, 8 ) . '-'
            . substr ( $chars, 8, 4 ) . '-'
            . substr ( $chars, 12, 4 ) . '-'
            . substr ( $chars, 16, 4 ) . '-'
            . substr ( $chars, 20, 12 );

        return $prefix.$uuid;
    }

    //生成用户token
    public static function generateUserToken()
    {
        $snowFlake = \EasySwoole\Utility\SnowFlake::make();
        $userToken = sha1($snowFlake);

        return $userToken;
    }
}