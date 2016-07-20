<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace app\user\api;
use think\Exception;


//载入配置文件
require_cache(APP_PATH . '/user/config.php');

//载入函数库文件
require_cache(APP_PATH . '/user/common.php');

/**
 * UC API调用控制器层
 */
abstract class Api{

	/**
	 * API调用模型实例
	 * @access  protected
	 * @var object
	 */
	protected $model;

	/**
	 * 构造方法，检测相关配置
	 */
	public function __construct(){
		//相关配置检测
		if (!defined('UC_APP_ID')){
		    throw new Exception('UC配置错误：缺少UC_APP_ID');
		}
		if (!defined('UC_API_TYPE')){
		    throw new Exception('UC配置错误：缺少UC_API_TYPE');
		}
		if (!defined('UC_AUTH_KEY')){
		    throw new Exception('UC配置错误：缺少UC_AUTH_KEY');
		}
		if (!defined('DATABASE')){
		    throw new Exception('UC配置错误：缺少数据库database');
		}
		if(UC_API_TYPE != 'Model' && UC_API_TYPE != 'Service'){
			throw new Exception('UC配置错误：UC_API_TYPE只能为 Model 或 Service');
		}
		if(UC_API_TYPE == 'Service' && defined('UC_AUTH_KEY') == ''){
			throw new Exception('UC配置错误：Service方式调用Api时UC_AUTH_KEY不能为空');
		}
		if(UC_API_TYPE == 'Model' && defined('DATABASE') == ''){
			throw new Exception('UC配置错误：Model方式调用Api时UC_DB_DSN不能为空');
		}
		

		$this->_init();
	}

	/**
	 * 抽象方法，用于设置模型实例
	 */
	abstract protected function _init();

}
