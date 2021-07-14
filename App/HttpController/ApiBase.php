<?php
/**
* api权限和中间件
* @author   garenlyu
*/ 
namespace App\HttpController;

use App\Util\Common;
use EasySwoole\Http\AbstractInterface\Controller;
use EasySwoole\Http\Message\Status;

abstract class ApiBase extends Controller
{
    protected function onRequest(?string $action): ?bool
    {
        var_dump($this->request()->getRequestParam());
        //参数检查
        if(!$this->request()->getRequestParam('api_token') || !$this->request()->getRequestParam('timestamp') || !$this->request()->getRequestParam('sign')){
            $this->writeJson(Status::CODE_BAD_REQUEST, 'Parameter error', 'FAIL');
            return false;
        }

        $checkSignRes = $this->checkSign($this->request()->getRequestParam());
        if(!$checkSignRes){
            $this->writeJson(Status::CODE_UNAUTHORIZED, 'Signature error', 'FAIL');
            return false;
        }

        return true;
    }
    
    protected function checkToken($api_token)
    {

    }

    protected function checkSign($params)
    {
        //获取用户apiKey
        $apiKey = Common::getInstance()->getRedisPool()->hGet('api_token_'.$params['api_token'], 'api_key');
        $signature = Common::getInstance()->generateSignature($params, $apiKey);
        var_dump($signature);
        if ($signature !== $params['sign']) {
            return false;
        }

        return true;
    }
}