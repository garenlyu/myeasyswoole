<?php

namespace App\RpcServices;

use EasySwoole\Rpc\Protocol\Request;
use EasySwoole\Rpc\Service\AbstractService;

class ExampleRpcService extends AbstractService
{
    /**
     *  重写onRequest(比如可以对方法做ip拦截或其它前置操作)
     *
     * @param Request $request
     * @return bool
     */
    protected function onRequest(Request $request): bool
    {
        return true;
    }

    function serviceName(): string
    {
        return 'Example';
    }
}