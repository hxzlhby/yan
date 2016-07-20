<?php
// +----------------------------------------------------------------------
// | Author: yil 
// +----------------------------------------------------------------------
namespace app\user\model;
use think\Model;
use think\Request;
/**
 * 会员模型层
 */
class UcenterMember extends Model{

    /**
     * 数据库连接
     * @var string
     */
    protected $connection = DATABASE;
    
	//类型转换
	protected $type = [
	    'status'    =>  'integer',
	    'reg_ip'    =>  'integer',
	    'reg_time'  =>  'timestamp',
	    'last_login_time'  =>  'timestamp',
	    'update_time'  =>  'timestamp',
	];
	
	// password属性修改器
	protected function setPasswordAttr($value)
	{
	    $key = \think\Config::get('uc_auth_key');
	    return think_ucenter_md5($value,$key);
	}
	
	// reg_ip属性修改器
	protected function setRegIpAttr()
	{
	    return Request::instance()->ip(1);
	}
	
	/* 自动完成 */
	//自动
	protected $auto = [
	    'update_time'=>NOW_TIME,
	];
	//新增
	protected $insert = [
	    'status' => 1,
	    'reg_time'=>NOW_TIME,
	    'reg_ip',
	];
	//更新
	protected $update = [];

}
