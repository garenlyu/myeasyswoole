<?php


namespace App\HttpController;

use App\Providers\AppProvider;
use EasySwoole\Rpc\Protocol\Response;
use EasySwoole\Template\Render;

class Example extends ApiBase
{

    public function rpc()
    {
        // 如果在同server中 直接用保存的rpc实例调用即可
        // $rpc = \EasySwoole\Component\Di::getInstance()->get('rpc');
        
        // 如果不是需要重新new一个rpc 注意config的配置 节点管理器 以及所在ip是否能被其他服务广播到 如果不能请调整其他服务的广播地址
        $rpc = AppProvider::getInstance()->getRpc();
        $ret = [];
        $client = $rpc->client();
        // client 全局参数
        $client->setClientArg([1,2,3]);
        /**
         * 调用商品列表
         */
        $ctx1 = $client->addRequest('Example.ExampleModule.list');
        // 设置请求参数
        $ctx1->setArg([
            'name' => 'Garen',
            'age' => 29
        ]);
        // 设置调用成功执行回调
        $ctx1->setOnSuccess(function (Response $response) use (&$ret) {
            $ret[] = [
                'list' => [
                    'msg' => $response->getMsg(),
                    'result' => $response->getResult()
                ]
            ];
        });

        // 执行调用
        $client->exec();
        $this->writeJson(200, $ret);
    }

    public function index()
    {
        // $this->response()->write(Render::getInstance()->render('example.index', [
        //     'content' => '示例首页内容'
        // ]));
        $this->writeJson(200, ['name' => 'easyswoole'], 'SUCCESS');
    }
}