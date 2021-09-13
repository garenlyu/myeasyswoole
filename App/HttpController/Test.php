<?php
/**
* api权限和中间件
* @author   garenlyu
*/ 
namespace App\HttpController;

use App\HttpMiddleware\Base;
use App\Tasks\LogTask;
use App\Util\RedisQueue;
use EasySwoole\Http\Message\Status;

class Test extends Base
{
    public function index()
    {
        $data = ['test' => '1111'];

        $log = [
            'input' => $this->request()->getRequestParam(),
            'output' => $data,
            'logName' => 'Test.index'
        ];

        //生产日志队列
        RedisQueue::produceTrustedTask(\App\Queues\LogQueue::getInstance(), $log);
        
        return $this->writeJson(Status::CODE_OK, $data, 'Success');
    }
}