<?php
namespace app\addons\behavior\test;
use app\addons\behavior\Addon;
class Test extends Addon
{
    public function run(&$params)
    {
        echo 'test行十三省为addons';// 行为逻辑
        return $this->fetch();
    }
    
    public function install(){
        return true;
    }
    
    public function uninstall(){
        return true;
    }
}