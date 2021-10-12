<?php
/**
* api权限和中间件
* @author   garenlyu
*/ 
namespace App\HttpController;

use App\HttpMiddleware\Base;
use EasySwoole\Template\Render;

class Index extends Base
{
    public function index()
    {
        $this->display['content'] = '你好';
        return $this->response()->write(Render::getInstance()->render('home', $this->display));
    }
}