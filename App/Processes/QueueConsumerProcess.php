<?php
namespace App\Processes;

use App\Queues\TestQueue;
use EasySwoole\Queue\Job;
use EasySwoole\Component\Process\AbstractProcess;

class QueueConsumerProcess extends AbstractProcess
{
    protected $appConfig;

    protected function run($arg)
    {
        echo 'QueueConsumer 进程启动'.PHP_EOL;

        $this->appConfig = \EasySwoole\EasySwoole\Config::getInstance()->getConf('APP');

        //【订单创建队列】队列消费
        go(function (){
            TestQueue::getInstance()->consumer()->listen(function (Job $job){
                $orderData = $job->getJobData();
                var_dump($orderData);

                TestQueue::getInstance()->consumer()->confirm($job); //任务确认

                //记录
                \EasySwoole\EasySwoole\Logger::getInstance()->info(json_encode([
                    'input' => $orderData,
                    'output' => null
                ], JSON_UNESCAPED_UNICODE), 'QueueConsumerProcess.testQueue');
            });
        });
    }
}