<?php


namespace App\HttpController;

use App\Util\Common;
use EasySwoole\Http\AbstractInterface\Controller;

class Oauth extends Controller
{
    public function index()
    {
        $api_token = \EasySwoole\Utility\Random::character(60);
        $redisPool = Common::getInstance()->getRedisPool();
        $redisPool->hSet('oauth_'.$api_token, 'api_token', $api_token);
        $redisPool->expire('oauth_'.$api_token, 7200);
        $oauthInfo = $redisPool->hGetAll('oauth_'.$api_token);

        return $this->writeJson(200, $oauthInfo, 'SUCCESS');
    }
}