<?php

namespace App\Providers;

use App\Util\Common;
use EasySwoole\Component\CoroutineSingleTon;

class AppProvider
{
    use CoroutineSingleTon;

    public function regitserOrm()
    {
        $dbConfig = new \EasySwoole\ORM\Db\Config(\EasySwoole\EasySwoole\Config::getInstance()->getConf('MYSQL'));

        \EasySwoole\ORM\DbManager::getInstance()->addConnection(new \EasySwoole\ORM\Db\Connection($dbConfig));
    }

    public function regitserRedisPool()
    {
        $redisConfig = \EasySwoole\EasySwoole\Config::getInstance()->getConf('REDIS');
        $redisPoolConfig = \EasySwoole\RedisPool\RedisPool::getInstance()->register(new \EasySwoole\Redis\Config\RedisConfig($redisConfig));
        //配置连接池连接数
        $redisPoolConfig->setMinObjectNum(5);
        $redisPoolConfig->setMaxObjectNum(20);
    }

    public function getRedis()
    {
        $redisConfig = \EasySwoole\EasySwoole\Config::getInstance()->getConf('REDIS');
        $redisPoolConfig = new \EasySwoole\Redis\Redis($redisConfig);

        return $redisPoolConfig;
    }

    public function getRedisPool()
    {
        $redisPool = \EasySwoole\RedisPool\RedisPool::defer();

        return $redisPool;
    }

    public function getRpcConfig()
    {
        $redisConfig = \EasySwoole\EasySwoole\Config::getInstance()->getConf('REDIS');
        $redisPool = new \EasySwoole\RedisPool\Pool(new \EasySwoole\Redis\Config\RedisConfig($redisConfig));
        $redisManager = new \App\RpcServices\NodeManager\RedisManager($redisPool);
        $rpcConfig = new \EasySwoole\Rpc\Config($redisManager);

        return $rpcConfig;
    }

    public function getRpc()
    {
        $redisConfig = \EasySwoole\EasySwoole\Config::getInstance()->getConf('REDIS');
        $redisPool = new \EasySwoole\RedisPool\Pool(new \EasySwoole\Redis\Config\RedisConfig($redisConfig));
        $redisManager = new \App\RpcServices\NodeManager\RedisManager($redisPool);
        $rpcConfig = new \EasySwoole\Rpc\Config($redisManager);
        $rpc = new \EasySwoole\Rpc\Rpc($rpcConfig);

        return $rpc;
    }

    public function regitserRpcService()
    {
        ###### 注册 rpc 服务 ######
        /** rpc 服务端配置 */
        // 采用了redis 节点管理器 可以关闭udp 广播了。
        $rpcConfig = $this->getRpcConfig();
        $rpcConfig->setNodeId('EasySwooleRpcNode1');
        $rpcConfig->setServerName('EasySwoole'); // 默认 EasySwoole
        $rpcConfig->setOnException(function (\Throwable $throwable) {

        });

        $serverConfig = $rpcConfig->getServer();
        $serverConfig->setServerIp('127.0.0.1');

        // rpc 具体配置请看配置章节
        $rpc = new \EasySwoole\Rpc\Rpc($rpcConfig);

        // 创建 Goods 服务
        $exampleService = new \App\RpcServices\ExampleRpcService();
        // 添加 GoodsModule 模块到 Goods 服务中
        $exampleService->addModule(new \App\RpcServices\ExampleModule());
        // 添加 Goods 服务到服务管理器中
        $rpc->serviceManager()->addService($exampleService);

        // 此刻的rpc实例需要保存下来 或者采用单例模式继承整个Rpc类进行注册 或者使用Di
        // \EasySwoole\Component\Di::getInstance()->set('rpc', $rpc);

        // 注册 rpc 服务
        $rpc->attachServer(\EasySwoole\EasySwoole\ServerManager::getInstance()->getSwooleServer());
    }

    public function regitserRender()
    {
        ###### 注册 render 服务 ######
        $renderConfig = \EasySwoole\Template\Render::getInstance()->getConfig();

        // [可选配置]
        /*
        $renderConfig->setTimeout(3); // 设置 超时时间，默认为 3s，不建议修改
        $renderConfig->setServerName('EasySwoole'); // 设置 渲染引擎驱动服务名称，不建议修改
        $renderConfig->setWorkerNum(3); // 设置 渲染引擎服务工作进程数，默认为 3，不建议修改
        */

        // 设置 渲染引擎模板驱动
        $renderConfig->setRender(new \App\RenderDriver\Blade());

        // 设置 渲染引擎进程 Socket 存放目录，默认为 getcwd()
        $renderConfig->setTempDir('/Temp');

        // 注册进程到 EasySwoole 主服务

        \EasySwoole\Template\Render::getInstance()->attachServer(\EasySwoole\EasySwoole\ServerManager::getInstance()->getSwooleServer());
    }
}

