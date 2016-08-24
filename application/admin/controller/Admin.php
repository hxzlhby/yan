<?php
namespace app\admin\controller;
use think\Controller;
use think\Request;
use think\Config;
use think\Cache;
class Admin extends Controller
{
    public function _initialize()
    {
        // 获取当前用户ID
        if(defined('UID')) return ;
        define('UID',is_login());
        if( !UID ){// 还没登录 跳转到登录页面
            $this->redirect('user/login');
        }
        
        /* 读取数据库中的配置 */
        $config =   Cache::get('DB_CONFIG_DATA');
        if(!$config){
            $config =   api('Config/lists');
            Cache::set('DB_CONFIG_DATA',$config);
        }
        Config::set($config); //添加配置
        
        // 是否是超级管理员
        define('IS_ROOT',   is_administrator());
//         if(!IS_ROOT && Config::get('ADMIN_ALLOW_IP')){
//             // 检查IP地址访问
//             if(!in_array(get_client_ip(),explode(',',Config::get('ADMIN_ALLOW_IP')))){
//                 $this->error('403:禁止访问');
//             }
//         }
        
        // 检测系统权限
//         if(!IS_ROOT){
//             $access =   $this->accessControl();
//             if ( false === $access ) {
//                 $this->error('403:禁止访问');
//             }elseif(null === $access ){
//                 //检测访问权限
//                 $rule  = strtolower(Request::instance()->module().'/'.Request::instance()->controller().'/'.Request::instance()->action());
//                 if ( !$this->checkRule($rule,array('in','1,2')) ){
//                     $this->error('未授权访问!');
//                 }else{
//                     // 检测分类及内容有关的各项动态权限
//                     $dynamic    =   $this->checkDynamic();
//                     if( false === $dynamic ){
//                         $this->error('未授权访问!');
//                     }
//                 }
//             }
//         }
        $this->assign('__MENU__', $this->getMenus());
    }
    /**
     * 检测是否是需要动态判断的权限
     * @return boolean|null
     *      返回true则表示当前访问有权限
     *      返回false则表示当前访问无权限
     *      返回null，则表示权限不明
     *
     * @author 朱亚杰  <xcoolcc@gmail.com>
     */
    protected function checkDynamic(){}
    /**
     * 权限检测
     * @param string  $rule    检测的规则
     * @param string  $mode    check模式
     * @return boolean
     * @author 朱亚杰  <xcoolcc@gmail.com>
     */
    final protected function checkRule($rule, $type=\app\admin\model\AuthRule::RULE_URL, $mode='url'){
        static $Auth    =   null;
        if (!$Auth) {
            $Auth       =   new \com\Auth();
        }
        if(!$Auth->check($rule,UID,$type,$mode)){
            return false;
        }
        return true;
    }
    /**
     * action访问控制,在 **登陆成功** 后执行的第一项权限检测任务
     *
     * @return boolean|null  返回值必须使用 `===` 进行判断
     *
     *   返回 **false**, 不允许任何人访问(超管除外)
     *   返回 **true**, 允许任何管理员访问,无需执行节点权限检测
     *   返回 **null**, 需要继续执行节点权限检测决定是否允许访问
     * @author 朱亚杰  <xcoolcc@gmail.com>
     */
    final protected function accessControl(){
        $allow = Config::get('ALLOW_VISIT');
        $deny  = Config::get('DENY_VISIT');
        $check = strtolower(Request::instance()->controller().'/'.Request::instance()->action());
        dump($check);
        if ( !empty($deny)  && in_array_case($check,$deny) ) {
            return false;//非超管禁止访问deny中的方法
        }
        if ( !empty($allow) && in_array_case($check,$allow) ) {
            return true;
        }
        return null;//需要检测节点权限
    }
    /**
     * 获取控制器菜单数组,二级菜单元素位于一级菜单的'_child'元素中
     * @author 朱亚杰  <xcoolcc@gmail.com>
     */
    final public function getMenus($controller=null){
        if (is_null($controller)){
            $controller = Request::instance()->controller();
        }
//         $menus  =   session('ADMIN_MENU_LIST.'.$controller);
        if(empty($menus)){
            // 获取主菜单
            $where['pid']   =   0;
            $where['hide']  =   0;
            if(!Config::get('DEVELOP_MODE')){ // 是否开发者模式
                $where['is_dev']    =   0;
            }
            $menuModel = new \app\admin\model\Menu();
            $menus['main']  =   $menuModel->where($where)->order('sort asc')->field('id,title,url')->select();
            $menus['child'] =   array(); //设置子节点
            foreach ($menus['main'] as $key => $item) {
                // 判断主菜单权限
                if ( !IS_ROOT && !$this->checkRule(strtolower(Request::instance()->module().'/'.$item['url']),\app\admin\model\AuthRule::RULE_MAIN,null) ) {
                    unset($menus['main'][$key]);
                    continue;//继续循环
                }
                if(strtolower(Request::instance()->controller().'/'.Request::instance()->action())  == strtolower($item['url'])){
                    $menus['main'][$key]['class']='current';
                }
            }
    
            // 查找当前子菜单
            $pid = $menuModel->where("pid !=0 AND url like '%{$controller}/".Request::instance()->action()."%'")->column('pid');
            if($pid){
                // 查找当前主菜单
                $nav =  $menuModel->find($pid);
                if($nav['pid']){
                    $nav    =   $menuModel->find($nav['pid']);
                }
                foreach ($menus['main'] as $key => $item) {
                    // 获取当前主菜单的子菜单项
                    if($item['id'] == $nav['id']){
                        $menus['main'][$key]['class']='current';
                        //生成child树
                        $groups = $menuModel->where(array('group'=>array('neq',''),'pid' =>$item['id']))->distinct(true)->column('group');
                        //获取二级分类的合法url
                        $where          =   array();
                        $where['pid']   =   $item['id'];
                        $where['hide']  =   0;
                        if(!Config::get('DEVELOP_MODE')){ // 是否开发者模式
                            $where['is_dev']    =   0;
                        }
                        $second_urls = $menuModel->where($where)->column('id,url');
    
                        if(!IS_ROOT){
                            // 检测菜单权限
                            $to_check_urls = array();
                            foreach ($second_urls as $key=>$to_check_url) {
                                if( stripos($to_check_url,Request::instance()->module())!==0 ){
                                    $rule = Request::instance()->module().'/'.$to_check_url;
                                }else{
                                    $rule = $to_check_url;
                                }
                                if($this->checkRule($rule, \app\admin\model\AuthRule::RULE_URL,null))
                                    $to_check_urls[] = $to_check_url;
                            }
                        }
                        // 按照分组生成子菜单树
                        foreach ($groups as $g) {
                            $map = array('group'=>$g);
                            if(isset($to_check_urls)){
                                if(empty($to_check_urls)){
                                    // 没有任何权限
                                    continue;
                                }else{
                                    $map['url'] = array('in', $to_check_urls);
                                }
                            }
                            $map['pid']     =   $item['id'];
                            $map['hide']    =   0;
                            if(!Config::get('DEVELOP_MODE')){ // 是否开发者模式
                                $map['is_dev']  =   0;
                            }
                            $menuList = $menuModel->where($map)->field('id,pid,title,url,tip')->order('sort asc')->select();
                            $menus['child'][$g] = list_to_tree($menuList, 'id', 'pid', 'operater', $item['id']);
                        }
                    }
                }
            }
//             session('ADMIN_MENU_LIST.'.$controller,$menus);
        }
        return $menus;
    }
    
    
    /**
     * 获取列表
     * @param unknown $model
     * @param array $where
     * @param string $order
     * @param string $field
     */
    protected function lists ($model,$where=array(),$order='',$field=true)
    {
        $options    =   array();
        $REQUEST    =   (array)Request::instance()->param();
        
        if(is_string($model)){
            $class = "app\\admin\\model\\".ucfirst($model);
            $model  =   new $class();
        }
        $pk         =   $model->getPk();
        if($order===null){
            //order置空
        }else if ( isset($REQUEST['_order']) && isset($REQUEST['_field']) && in_array(strtolower($REQUEST['_order']),array('desc','asc')) ) {
            $options['order'] = '`'.$REQUEST['_field'].'` '.$REQUEST['_order'];
        }elseif( $order==='' && empty($options['order']) && !empty($pk) ){
            $options['order'] = $pk.' desc';
        }elseif($order){
            $options['order'] = $order;
        }
        unset($REQUEST['_order'],$REQUEST['_field']);
    
        if(empty($where)){
            $where  =   array('status'=>array('egt',0));
        }
        if( !empty($where)){
            $options['where']   =   $where;
        }
    
        if( isset($REQUEST['r']) ){
            $listRows = (int)$REQUEST['r'];
        }else{
            $listRows = Config::get('list_rows') > 0 ? Config::get('list_rows') : 10;
        }
        return $model::where($options['where'])->order($options['order'])->paginate($listRows);
    }
    
    public function _empty($name)
    {
        return '空操作:'.$name;
    }
}
