<?php
/**
* api权限和中间件
* @author   garenlyu
*/ 
namespace App\HttpMiddleware;

use EasySwoole\Http\AbstractInterface\Controller;

abstract class Base extends Controller
{
    //应用配置
    protected $appConfig;

    //运行模式
    protected $runMode;

    //post json 数据
    protected $raw;

    //验证器
    protected $validate;

    protected function onRequest(?string $action): ?bool
    {
        //应用配置
        $this->appConfig = \EasySwoole\EasySwoole\Config::getInstance()->getConf('APP');

        //运行模式
        $this->runMode = \EasySwoole\EasySwoole\Core::getInstance()->runMode();

        //post json 数据
        $content = $this->request()->getBody()->__toString();
        $this->raw = json_decode($content, true)??[];
        
        //验证器
        $this->validate = new \EasySwoole\Validate\Validate();

        return true;
    }
}