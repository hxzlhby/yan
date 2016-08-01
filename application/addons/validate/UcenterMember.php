<?php
// +----------------------------------------------------------------------
// | Author: yil 
// +----------------------------------------------------------------------
namespace app\user\validate;
use think\validate;
/**
 * 自动验证类
 */
class UcenterMember extends Validate{

    protected $rule = [
        'username'   => 'unique:ucenter_member|length:1,30',
        'password'  =>  'length:6,30',
        'email' =>  'email|length:6,30|unique:ucenter_member',
    ];

    protected $message = [
        'username.unique'  =>  '用户名唯一',
        'email.email' =>  '邮箱格式错误',
    ];

    protected $scene = [
        'add'   =>  ['username','password','email'],
        'edit'  =>  ['password','email'],
    ];
    
}
