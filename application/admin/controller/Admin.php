<?php
namespace app\admin\controller;
use think\Controller;
class Admin extends Controller
{
    public function _initialize()
    {
        
    }
    
    public function _empty($name)
    {
        return '空操作:'.$name;
    }
}
