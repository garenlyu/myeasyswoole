<?php

namespace App\Crontabs;

use EasySwoole\EasySwoole\Crontab\AbstractCronTask;
use EasySwoole\EasySwoole\Task\TaskManager;

class TestCrontab extends AbstractCronTask
{
    public static function getRule(): string
    {
        // 定义执行规则 根据Crontab来定义
        return '*/1 * * * *';
    }

    public static function getTaskName(): string
    {
        // 定时任务的名称
        return 'CustomCrontab';
    }

    public function run(int $taskId, int $workerIndex)
    {
        // 定时任务的执行逻辑

        // 开发者可投递给task异步处理
        TaskManager::getInstance()->async(function (){
            // todo some thing
        });
    }

    public function onException(\Throwable $throwable, int $taskId, int $workerIndex)
    {
        // 捕获run方法内所抛出的异常
    }
}