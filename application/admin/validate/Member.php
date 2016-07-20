<?php
// +----------------------------------------------------------------------
// | Author: yil 
// +----------------------------------------------------------------------
namespace app\admin\validate;
use think\validate;
/**
 * 自动验证类
 */
class Member extends Validate{

    protected $rule = [
        'nickname'   => 'length:1,16|unique:ucenter_member',
    ];

    protected $message = [
        'nickname.unique'  =>  '昵称被占用',
        'nickname.length' =>  '昵称长度为1-16个字符',
    ];

    protected $scene = [
        'add'   =>  ['nickname'],
        'edit'  =>  ['nickname'],
    ];
    
}
