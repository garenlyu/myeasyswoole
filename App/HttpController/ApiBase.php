<?php
/**
* api权限和中间件
* @author   garenlyu
*/ 
namespace App\HttpController;

use App\Util\Common;
use App\Util\Redis;
use EasySwoole\Http\Message\Status;
use EasySwoole\Http\AbstractInterface\Controller;

abstract class ApiBase extends Controller
{

    protected $userInfo;

    protected function onRequest(?string $action): ?bool
    {
        //参数检查
        if(!$this->request()->getRequestParam('app_id')){
            $this->writeJson(Status::CODE_BAD_REQUEST, null, '参数错误(app_id)');
            return false;
        }
        if(!$this->request()->getRequestParam('timestamp')){
            $this->writeJson(Status::CODE_BAD_REQUEST, null, '参数错误(timestamp)');
            return false;
        }
        if(!$this->request()->getRequestParam('nonce_str')){
            $this->writeJson(Status::CODE_BAD_REQUEST, null, '参数错误(nonce_str)');
            return false;
        }
        if(!$this->request()->getRequestParam('sign')){
            $this->writeJson(Status::CODE_BAD_REQUEST, null, '参数错误(sign)');
            return false;
        }

        if (\EasySwoole\EasySwoole\Core::getInstance()->runMode() !== 'dev') {
            //时间验证
            if($this->request()->getRequestParam('timestamp') < time() - 60){
                $this->writeJson(Status::CODE_UNAUTHORIZED, null, '签名过期');
                return false;
            }

            //签名验证
            $checkSignRes = $this->checkSign($this->request()->getRequestParam());
            if(!$checkSignRes){
                $this->writeJson(Status::CODE_UNAUTHORIZED, null, '签名错误',);
                return false;
            }
        }

        if($this->request()->getRequestParam('user_token')){
            $userInfo = Redis::getRedisPool()->hGetAll('user_token_'.$this->request()->getRequestParam('user_token'));
            if(!$userInfo){
                $this->writeJson(Status::CODE_UNAUTHORIZED, null, 'user_token错误');
                return false;
            }
            $this->userInfo = $userInfo;
        }

        return true;
    }

    protected function checkSign($params)
    {
        //获取用户apiKey
        $apiKey = Redis::getRedisPool()->hGet('oauth_'.$params['appId'], 'api_key');
        $signature = Common::generateSignature($params, $apiKey);
        var_dump($signature);
        if ($signature !== $params['sign'])
            return false;

        //验签通过后，保存时间戳和随机字符串，防止重放
        Redis::getRedisPool()->setNx('sign', $signature);

        return true;
    }
}