<?php
namespace app\admin\controller;
use think\Request;
use app\user\api\UserApi;
use think\Controller;
class User extends Controller
{
    public function index()
    {
        $map = array();
        $map['status']  =   ['egt',0];
        if (Request::instance()->has('nickname','param')){
            $nickname = Request::instance()->param('nickname');
            if(is_numeric($nickname)){
                $map['uid|nickname']=   array(['eq',intval($nickname)],array('like','%'.$nickname.'%'),'or');
            }else{
                $map['nickname']    =   array('like', '%'.(string)$nickname.'%');
            }
        }
        $list = $this->lists('Member',$map);
        $this->assign('_list', $list);
        return $this->fetch();
    }
    
    public function login($username = null, $password = null, $captcha = null) 
    {
        if (Request::instance()->isPost()){
//             if(!captcha_check($captcha)){
//                 $this->error('验证码错误!');
//             }
            
            $User = new UserApi;
            $uid = $User->login($username, $password);
            if(0 < $uid){ //UC登录成功
                /* 登录用户 */
                $Member = \think\Loader::model('Member','logic');
                if($Member->login($uid)){ //登录用户
                    //TODO:跳转到登录前页面
                    return $this->success('登录成功！', url('Index/index'));
                } else {
                    return $this->error($Member->getError());
                }
            
            } else { //登录失败
                switch($uid) {
                    case -1: $error = '用户不存在或被禁用！'; break; //系统级别禁用
                    case -2: $error = '密码错误！'; break;
                    default: $error = '未知错误！'; break; // 0-接口参数错误（调试阶段使用）
                }
                return $this->error($error);
            }
        }else{
            if(is_login()){
                $this->redirect('Index/index');
            }else{
                /* 读取数据库中的配置 */
//                 $config	=	S('DB_CONFIG_DATA');
//                 if(!$config){
//                     $config	=	D('Config')->lists();
//                     S('DB_CONFIG_DATA',$config);
//                 }
//                 C($config); //添加配置
            
                return $this->fetch();
            }
            
        }
    }
    
    /* 退出登录 */
    public function logout(){
        if(is_login()){
            $Member = \think\Loader::model('Member','logic');
            $Member->logout();
            session('[destroy]');
            return $this->success('退出成功！', url('user/login'));
        } else {
            $this->redirect('user/login');
        }
    }
    
   
}
