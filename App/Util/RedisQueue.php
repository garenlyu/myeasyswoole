<?php

namespace App\Util;

use EasySwoole\Queue\Job;
use EasySwoole\Queue\Queue;

class RedisQueue
{
    // 生产可信任务
    public static function produceTrustedTask(Queue $queue, $data)
    {
        $job = new Job();
        $job->setJobData($data);
        
        // 设置任务重试次数为 3 次。任务如果没有确认，则会执行三次
        $job->setRetryTimes(3);

        // 如果5秒内没确认任务，会重新回到队列。默认为3秒
        $job->setWaitConfirmTime(5);

        // 投递任务
        $queue->producer()->push($job);

        // 确认一个任务
        // $queue->consumer()->confirm($job);
    }

    // 生产可信延时任务
    public static function produceTrustedDelayedTask(Queue $queue, $data)
    {
        $job = new Job();
        $job->setJobData($data);
        
        // 设置任务重试次数为 3 次。任务如果没有确认，则会执行三次
        $job->setRetryTimes(3);

        // 如果5秒内没确认任务，会重新回到队列。默认为3秒
        $job->setWaitConfirmTime(5);

        // 设置任务延后执行时间
        $job->setDelayTime(60);

        // 投递任务
        $queue->producer()->push($job);

        // 确认一个任务
        // $queue->consumer()->confirm($job);
    }
}