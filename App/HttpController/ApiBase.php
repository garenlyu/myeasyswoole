<?php


namespace App\HttpController;

use App\Providers\AppProvider;
use EasySwoole\Http\AbstractInterface\Controller;

class ApiBase extends Controller
{
    // protected $rpc;

    // protected function onRequest(?string $action): ?bool
    // {
    //     $this->rpc = AppProvider::getInstance()->getRpc();
        
    //     return true;
    // }
}