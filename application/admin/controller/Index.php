<?php
namespace app\admin\controller;

class Index extends Admin
{
    public function index()
    {
        return $this->fetch();
    }
    
    public function test() {
        return '行为测试';;
    }
}
