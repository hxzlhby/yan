<?php
// +----------------------------------------------------------------------
// | Author: hxz
// +----------------------------------------------------------------------

namespace app\addons\behavior;
use think\Exception;
use think\Request;
use think\Config;
abstract class Addon{
    protected $view = null;
    
    public $info                =   array();
    public $addon_path          =   '';//配置文件
    public $config_file         =   '';
    public $custom_config       =   '';
    public $admin_list          =   array();
    public $custom_adminlist    =   '';
    public $access_url          =   array();

    public function __construct(){
        $this->view         =   new \think\View();
        $this->addon_path   =   APP_PATH.Config::get('addon.path').'/'.$this->getName().'/';//配置文件
        if(is_file($this->addon_path.'config.php')){
            $this->config_file = $this->addon_path.'config.php';
        }
    }

    /**
     * 模板变量赋值
     * @access protected
     * @param mixed $name 要显示的模板变量
     * @param mixed $value 变量的值
     * @return Action
     */
    final protected function assign($name,$value='') {
        $this->view->assign($name,$value);
        return $this;
    }


    //用于显示模板的方法
    final protected function fetch($templateFile = ''){
        if (empty($templateFile)){
            $templateFile = Request::instance()->action();
        }
        if(!is_file($templateFile)){
            $templateFile = $this->addon_path.$templateFile.'.'.Config::get('template.view_suffix');
            if(!is_file($templateFile)){
                throw new Exception("模板不存在:$templateFile");
            }
        }
        echo $this->view->fetch($templateFile);
    }

    final public function getName(){
        $class = get_class($this);
        return lcfirst(substr($class,strrpos($class, '\\')+1));
    }

    final public function checkInfo(){
        $info_check_keys = array('name','title','description','status','author','version');
        foreach ($info_check_keys as $value) {
            if(!array_key_exists($value, $this->info))
                return FALSE;
        }
        return TRUE;
    }

    /**
     * 获取插件的配置数组
     */
    final public function getConfig($name=''){
        static $_config = array();
        if(empty($name)){
            $name = $this->getName();
        }
        if(isset($_config[$name])){
            return $_config[$name];
        }
        $config =   array();
        $map['name']    =   $name;
        $map['status']  =   1;
        $config  =   \think\Db::table('Addons')->where($map)->value('config');
        if($config){
            $config   =   json_decode($config, true);
        }else{
            $temp_arr = include $this->config_file;
            foreach ($temp_arr as $key => $value) {
                if($value['type'] == 'group'){
                    foreach ($value['options'] as $gkey => $gvalue) {
                        foreach ($gvalue['options'] as $ikey => $ivalue) {
                            $config[$ikey] = $ivalue['value'];
                        }
                    }
                }else{
                    $config[$key] = $temp_arr[$key]['value'];
                }
            }
        }
        $_config[$name]     =   $config;
        return $config;
    }

    //必须实现安装
    abstract public function install();

    //必须卸载插件方法
    abstract public function uninstall();
}
