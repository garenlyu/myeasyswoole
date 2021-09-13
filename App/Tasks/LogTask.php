<?php

namespace App\Tasks;

use EasySwoole\Task\AbstractInterface\TaskInterface;

class LogTask implements TaskInterface
{
    protected $input;

    protected $output;

    protected $logName;

    public function __construct($input = null, $output = null, $logName='system')
    {
        // 保存投递过来的数据
        $this->input = $input;
        $this->output = $output;
        $this->logName = $logName;
    }

    public function run(int $taskId, int $workerIndex)
    {
        // 执行逻辑
        //纪录日志
        \EasySwoole\EasySwoole\Logger::getInstance()->info(json_encode([
            'input' => $this->input,
            'output' => $this->output,
        ], JSON_UNESCAPED_UNICODE), $this->logName);
    }

    public function onException(\Throwable $throwable, int $taskId, int $workerIndex)
    {
        // 异常处理
    }
}