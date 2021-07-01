<?php


namespace EasySwoole\EasySwoole;

use App\Providers\AppProvider;
use EasySwoole\EasySwoole\AbstractInterface\Event;
use EasySwoole\EasySwoole\Swoole\EventRegister;

class EasySwooleEvent implements Event
{
    public static function initialize()
    {
        date_default_timezone_set('Asia/Shanghai');

        AppProvider::getInstance()->regitserOrm();

        AppProvider::getInstance()->regitserRedisPool();
    }

    public static function mainServerCreate(EventRegister $register)
    {
        AppProvider::getInstance()->regitserRpcService();

        AppProvider::getInstance()->regitserRender();
    }
}