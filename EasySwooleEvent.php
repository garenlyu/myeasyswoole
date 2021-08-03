<?php


namespace EasySwoole\EasySwoole;

use App\Util\Registry;
use EasySwoole\EasySwoole\AbstractInterface\Event;
use EasySwoole\EasySwoole\Swoole\EventRegister;

class EasySwooleEvent implements Event
{
    public static function initialize()
    {
        date_default_timezone_set('Asia/Shanghai');

        Registry::regitserOrm();

        Registry::regitserRedisPool();
    }

    public static function mainServerCreate(EventRegister $register)
    {
        // Registry::regitserRpcService();

        Registry::regitserRender();

        //开发模式下使用热重载
        if (\EasySwoole\EasySwoole\Core::getInstance()->runMode() === 'dev') {
            Registry::regitsterFileWatcher();
        }

        
    }
}