<?php
namespace App\Processes;

use App\Queues\LogQueue;
use EasySwoole\Queue\Job;
use EasySwoole\Component\Process\AbstractProcess;
use App\Tasks\LogTask;

class QueueConsumerProcess extends AbstractProcess
{
    protected $appConfig;

    protected function run($arg)
    {
        echo 'QueueConsumer 进程启动'.PHP_EOL;

        //【日志队列】队列消费
        go(function (){
            LogQueue::getInstance()->consumer()->listen(function (Job $job){
                $log = $job->getJobData();

                //投递日志任务
                \EasySwoole\EasySwoole\Task\TaskManager::getInstance()->async(new LogTask($log['input'], $log['output'], $log['logName']));

                LogQueue::getInstance()->consumer()->confirm($job); //任务确认
            });
        });
    }
}