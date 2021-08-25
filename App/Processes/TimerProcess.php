<?php
namespace App\Processes;

use App\Timers\OrderTimer;
use EasySwoole\Component\Process\AbstractProcess;

class TimerProcess extends AbstractProcess
{
    protected function run($arg)
    {
        echo 'Timer 进程启动'.PHP_EOL;
        
        //订单支付查询
        OrderTimer::getInstance()->orderPayQuery();

        //订单充值查询
        OrderTimer::getInstance()->orderTopUpQuery();
    }
}