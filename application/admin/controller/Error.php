<?php
namespace app\admin\controller;
use think\Request;
class Error extends Admin
{
    public function index(Request $request)
    {
        return '空控制器:'.$request;
    }
}
