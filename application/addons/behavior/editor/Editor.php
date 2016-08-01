<?php
namespace app\addons\behavior\editor;
use app\addons\behavior\Addon;
class Editor extends Addon
{
    public function run(&$params)
    {
        echo 'test行editor省为addons';// 行为逻辑
        $this->assign('name',123);
        return $this->fetch();
    }
    
    public function install(){
        return true;
    }
    
    public function uninstall(){
        return true;
    }
}