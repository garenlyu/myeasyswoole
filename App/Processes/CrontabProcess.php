<?php
namespace App\Processes;

use EasySwoole\Component\Process\AbstractProcess;

class CrontabProcess extends AbstractProcess
{
    protected function run($arg)
    {
        echo 'Crontab 进程启动'.PHP_EOL;
    }
}