<?php

namespace App\HttpController;

use App\Models\OauthModel;
use App\Util\Redis;
use EasySwoole\Http\AbstractInterface\Controller;

class Oauth extends Controller
{
    public function create()
    {
        $appId = \EasySwoole\Utility\SnowFlake::make(); //雪花算法生成appid
        $appSecret = md5($appId);

        $oauthData = [
            'app_id' => $appId,
            'app_secret' => $appSecret,
            'created_at' => date('Y-m-d H:i:s'),
        ];
        OauthModel::create($oauthData)->save();
        
        $redisPool = Redis::getRedisPool();
        $redisPool->hMSet('oauth_'.$appId, $oauthData);
        $redisPool->expire('oauth_'.$appId, 7200);
        $oauthInfo = $redisPool->hGetAll('oauth_'.$appId);

        return $this->writeJson(200, $oauthInfo, 'Success');
    }
}