<?php
// +----------------------------------------------------------------------
// | Author: yil 
// +----------------------------------------------------------------------
namespace app\admin\logic;
use app\admin\model\Member as MemberModel;
use think\Model;
/**
 * 会员逻辑层
 */
class Member extends Model{
    /**
     * 登录指定用户
     * @param  integer $uid 用户ID
     * @return boolean      ture-登录成功，false-登录失败
     */
    public function login($uid){
        $model = new MemberModel();
        /* 检测是否在当前应用注册 */
        $user = $model::get($uid);
        if(!$user || 1 != $user->status) {
            $this->error = '用户不存在或已被禁用！'; //应用级别禁用
            return false;
        }
    
        //记录行为
//         action_log('user_login', 'member', $uid, $uid);
    
        /* 登录用户 */
        $this->autoLogin($user);
        return true;
    }
    
    /**
     * 自动登录用户
     * @param  integer $user 用户信息数组
     */
    private function autoLogin($user){
        /* 更新登录信息 */
        $data = array(
            'uid'             => $user->uid,
            'login'           => array('exp', '`login`+1'),
            'last_login_time' => NOW_TIME,
            'last_login_ip'   => \think\Request::instance()->ip(1)
        );
        $model = new MemberModel();
        $model->update($data);
    
        /* 记录登录SESSION和COOKIES */
        $auth = array(
            'uid'             => $user->uid,
            'username'        => $user->nickname,
            'last_login_time' => $user->last_login_time,
        );
    
        session('user_auth', $auth);
        session('user_auth_sign', data_auth_sign($auth));
        
    }
    
    /**
     * 注销当前用户
     * @return void
     */
    public function logout(){
        session('user_auth', null);
        session('user_auth_sign', null);
    }
    

}
