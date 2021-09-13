<?php
namespace App\Processes;

use EasySwoole\Component\Process\AbstractProcess;
use App\Tasks\LogTask;

class TimerProcess extends AbstractProcess
{
    protected function run($arg)
    {
        echo 'Timer 进程启动'.PHP_EOL;
        
        // 每隔 10 秒执行一次
        \EasySwoole\Component\Timer::getInstance()->loop(10 * 1000, function () {
            //投递日志任务
            \EasySwoole\EasySwoole\Task\TaskManager::getInstance()->async(new LogTask());
        });
    }
}