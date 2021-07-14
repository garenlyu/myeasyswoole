<?php


namespace App\HttpController;

class User extends ApiBase
{
    public function getUserInfo()
    {
        $this->writeJson(200, ['name' => 'easyswoole'], 'SUCCESS');
    }
}