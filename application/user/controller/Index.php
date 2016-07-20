<?php
namespace app\user\controller;
use app\user\api\UserApi;
class Index
{
    public function index($username, $password, $email, $mobile='')
    {
//         $User = new UserApi;
//         $uid = $User->login($username, $password);
//         dump($uid);
        $ip = \think\Request::instance()->ip(1);
        dump($ip);
        $model = \think\Loader::model('UcenterMember','logic');
        $res = $model->register($username, $password, $email, $mobile);
        dump($res);
        dump($model->getError());
    }
    
    public function login($username, $password)
    {
        $ip = \think\Request::instance()->ip(1);
        dump($ip);
        $model = \think\Loader::model('UcenterMember','logic');
        $res = $model->login($username, $password);
        dump($res);
        dump($model->getError());
    }
    
    public function info($uid)
    {
        \think\Config::load('config.php','','admin');
        dump(\think\Config::get('uc_app_id'));
        $model = \think\Loader::model('UcenterMember','logic');
        $res = $model->checkField($uid);
        return $res;
    }
}
