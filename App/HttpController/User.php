<?php
/**
* api权限和中间件
* @author   garenlyu
*/ 
namespace App\HttpController;

use App\HttpMiddleware\Base;
use EasySwoole\Http\Message\Status;

class User extends Base
{
    public function getUserDetails()
    {
        return $this->writeJson(Status::CODE_OK, null, 'Success');
    }
}