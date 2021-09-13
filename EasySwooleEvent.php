<?php


namespace EasySwoole\EasySwoole;

use App\Util\Register;
use EasySwoole\EasySwoole\AbstractInterface\Event;
use EasySwoole\EasySwoole\Swoole\EventRegister;

class EasySwooleEvent implements Event
{
    public static function initialize()
    {
        date_default_timezone_set('Asia/Shanghai');

        Register::regitserOrm();

        Register::regitserRedisPool();
    }

    public static function mainServerCreate(EventRegister $register)
    {
        // Register::regitserRpcService();

        //注册模板引擎
        Register::regitserRender();

        //注册队列驱动
        Register::regitserQueueDriver(\App\Config\Queue::LOG_QUEUE, \App\Queues\LogQueue::class);

        //注册消费者进程
        Register::regitserQueueConsumerProcess();

        //注册定时器进程
        Register::regitserTimerProcess();

        //注册crontab进程
        Register::regitserCrontabProcess();

        //非生产模式下使用热重载
        if (\EasySwoole\EasySwoole\Core::getInstance()->runMode() !== 'produce') {
            Register::regitsterFileWatcher();
        }

        
    }
}