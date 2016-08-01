<?php
namespace app\admin\behavior;

class CheckAuth
{
    public function run(&$params)
    {
        echo 'CheckAuth行为'.$params;// 行为逻辑
    }
}