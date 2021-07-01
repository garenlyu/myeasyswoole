<?php

namespace App\RpcServices;

use EasySwoole\Rpc\Service\AbstractServiceModule;

class ExampleModule extends AbstractServiceModule
{
    function moduleName(): string
    {
        return 'ExampleModule';
    }

    function list()
    {
        $data = $this->request()->getArg();
        var_dump($data);
        $name = $data['name'];
        $age = $data['age'];
        $this->response()->setResult('用户名：'.$name.'|年龄：'.$age);
        $this->response()->setMsg('get example list success');
    }

    function exception()
    {
        throw new \Exception('the ExampleModule exception');

    }

    protected function onException(\Throwable $throwable)
    {
        $this->response()->setStatus(-1)->setMsg($throwable->getMessage());
    }
}