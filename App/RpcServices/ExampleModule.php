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
        $this->response()->setResult([
            [
                'exampleId' => '100001',
                'exampleName' => '例1'
            ],
            [
                'exampleId' => '100002',
                'exampleName' => '例2'
            ]
        ]);
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