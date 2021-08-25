<?php

namespace App\Util;

class Register
{

    public static function regitserOrm()
    {
        $dbConfig = new \EasySwoole\ORM\Db\Config(\EasySwoole\EasySwoole\Config::getInstance()->getConf('MYSQL'));

        \EasySwoole\ORM\DbManager::getInstance()->addConnection(new \EasySwoole\ORM\Db\Connection($dbConfig));
    }

    public static function regitserRedisPool()
    {
        $redisConfig = \EasySwoole\EasySwoole\Config::getInstance()->getConf('REDIS');
        $redisPoolConfig = \EasySwoole\RedisPool\RedisPool::getInstance()->register(new \EasySwoole\Redis\Config\RedisConfig($redisConfig));
        //配置连接池连接数
        $redisPoolConfig->setMinObjectNum(5);
        $redisPoolConfig->setMaxObjectNum(20);
    }

    public static function regitserRender()
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

    public static function regitsterFileWatcher()
    {
        # code...
        $watcher = new \EasySwoole\FileWatcher\FileWatcher();
        $rule = new \EasySwoole\FileWatcher\WatchRule(EASYSWOOLE_ROOT . "/App"); // 设置监控规则和监控目录
        $watcher->addRule($rule);
        $watcher->setOnChange(function () {
            \EasySwoole\EasySwoole\Logger::getInstance()->info('file change ,reload!!!');
            \EasySwoole\EasySwoole\ServerManager::getInstance()->getSwooleServer()->reload();
        });
        $watcher->attachServer(\EasySwoole\EasySwoole\ServerManager::getInstance()->getSwooleServer());
    }

    public static function regitserCrontabProcess()
    {
        $processConfig = new \EasySwoole\Component\Process\Config([
            'processName' => 'CrontabProcess', // 设置 进程名称为 CrontabProcess
            'processGroup' => 'Crontab', // 设置 进程组名称为 Crontab
            'enableCoroutine' => true, // 设置 自定义进程自动开启协程环境
        ]);

        // 【推荐】使用 \EasySwoole\Component\Process\Manager 类注册自定义进程
        $crontabProcess = (new \App\Processes\CrontabProcess($processConfig));
        // 注册进程
        \EasySwoole\Component\Process\Manager::getInstance()->addProcess($crontabProcess);
    }

    public static function regitserTimerProcess()
    {
        $processConfig = new \EasySwoole\Component\Process\Config([
            'processName' => 'TimerProcess', // 设置 进程名称为 TimerProcess
            'processGroup' => 'Timer', // 设置 进程组名称为 Tick
            'enableCoroutine' => true, // 设置 自定义进程自动开启协程环境
        ]);

        // 【推荐】使用 \EasySwoole\Component\Process\Manager 类注册自定义进程
        $timerProcess = (new \App\Processes\TimerProcess($processConfig));
        // 注册进程
        \EasySwoole\Component\Process\Manager::getInstance()->addProcess($timerProcess);
    }

    public static function regitserQueueConsumerProcess()
    {
        // 注册一个消费进程
        $processConfig = new \EasySwoole\Component\Process\Config([
            'processName' => 'QueueConsummerProcess', // 设置 自定义进程名称
            'processGroup' => 'Queue', // 设置 自定义进程组名称
            'enableCoroutine' => true, // 设置 自定义进程自动开启协程
        ]);
        $queueConsumerProcess = new \App\Processes\QueueConsumerProcess($processConfig);
        \EasySwoole\Component\Process\Manager::getInstance()->addProcess($queueConsumerProcess);
    }

    public static function regitserQueueDriver($queueName, $queueClass)
    {
        $redisConfig = \EasySwoole\EasySwoole\Config::getInstance()->getConf('REDIS');
        // 配置 队列驱动器
        $driver = new \EasySwoole\Queue\Driver\RedisQueue(new \EasySwoole\Redis\Config\RedisConfig($redisConfig), $queueName);
        $queueClass::getInstance($driver);
    }
}

