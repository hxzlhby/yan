<?php
// +----------------------------------------------------------------------
// | Author: yil 
// +----------------------------------------------------------------------
namespace app\user\logic;
use app\user\model\UcenterMember as UcenterMemberModel;
use think\Model;
/**
 * 会员逻辑层
 */
class UcenterMember extends Model{

	/**
	 * XXX
	 * 注册一个新用户
	 * @param  string $username 用户名
	 * @param  string $password 用户密码
	 * @param  string $email    用户邮箱
	 * @param  string $mobile   用户手机号码
	 * @return integer          注册成功-用户信息，注册失败-错误编号
	 */
	public function register($username, $password, $email, $mobile=''){
		$data = array(
			'username' => $username,
			'password' => $password,
			'email'    => $email,
			'mobile'   => $mobile,
		);
		if(empty($data['mobile'])) unset($data['mobile']);
		$model = new UcenterMemberModel();
		$result = $model->validate(true)->save($data);
		if(false === $result){
		    // 验证失败 输出错误信息
		    $this->error = $model->getError();
		    return false;
		}
		return true;
	}

	/**
	 * XXX
	 * 用户登录认证
	 * @param  string  $username 用户名
	 * @param  string  $password 用户密码
	 * @param  integer $type     用户名类型 （1-用户名，2-邮箱，3-手机，4-UID）
	 * @return integer           登录成功-用户ID，登录失败-错误编号
	 */
	public function login($username, $password, $type = 1){
		$map = array();
		switch ($type) {
			case 1:
				$map['username'] = $username;
				break;
			case 2:
				$map['email'] = $username;
				break;
			case 3:
				$map['mobile'] = $username;
				break;
			case 4:
				$map['id'] = $username;
				break;
			default:
				return 0; //参数错误
		}
		$model = new UcenterMemberModel();
		/* 获取用户数据 */
		$user = $model->where($map)->find();
		if(is_object($user) && $user->status){
			/* 验证用户密码 */
			if(think_ucenter_md5($password, UC_AUTH_KEY) === $user->password){
				$this->updateLogin($user->id); //更新用户登录信息
				return $user->id; //登录成功，返回用户ID
			} else {
				return -2; //密码错误
			}
		} else {
			return -1; //用户不存在或被禁用
		}
	}

	/**
	 * XXX
	 * 获取用户信息
	 * @param  string  $uid         用户ID或用户名
	 * @param  boolean $is_username 是否使用用户名查询
	 * @return array                用户信息
	 */
	public function info($uid, $is_username = false){
		$map = array();
		if($is_username){ //通过用户名获取
			$map['username'] = $uid;
		} else {
			$map['id'] = $uid;
		}
		$model = new UcenterMemberModel();
		$user = $model->where($map)->field('id,username,email,mobile,status')->find();
		if(is_object($user) && $user->status = 1){
			return $user;
		} else {
			return -1; //用户不存在或被禁用
		}
	}

	/**
	 * XXX
	 * 检测用户信息
	 * @param  string  $field  用户名
	 * @param  integer $type   用户名类型 1-用户名，2-用户邮箱，3-用户电话
	 * @return integer         错误编号
	 */
	public function checkField($field, $type = 1){
		$data = array();
		switch ($type) {
			case 1:
				$data['username'] = $field;
				break;
			case 2:
				$data['email'] = $field;
				break;
			case 3:
				$data['mobile'] = $field;
				break;
			default:
				return 0; //参数错误
		}
		$validate = \think\Loader::validate('UcenterMember');
		return $validate->check($data) ? 1 : $validate->getError();
	}

	/**
	 * XXX
	 * 更新用户登录信息
	 * @param  integer $uid 用户ID
	 */
	protected function updateLogin($uid){
		$data = array(
			'id'              => $uid,
			'last_login_time' => NOW_TIME,
			'last_login_ip'   => \think\Request::instance()->ip(1),
		);
		$model = new UcenterMemberModel();
		$model->update($data);
	}

	/**
	 * 更新用户信息
	 * @param int $uid 用户id
	 * @param string $password 密码，用来验证
	 * @param array $data 修改的字段数组
	 * @return true 修改成功，false 修改失败
	 * @author huajie <banhuajie@163.com>
	 */
	public function updateUserFields($uid, $password, $data){
		if(empty($uid) || empty($password) || empty($data)){
			$this->error = '参数错误！';
			return false;
		}

		//更新前检查用户密码
		if(!$this->verifyUser($uid, $password)){
			$this->error = '验证出错：密码不正确！';
			return false;
		}
		$data['id'] = $uid;
		$model = new UcenterMemberModel();
		$result = $model->validate(true)->update($data);
		if(false === $result){
		    // 验证失败 输出错误信息
		    $this->error = $model->getError();
		    return false;
		}
        return true;
	}

	/**
	 * 验证用户密码
	 * @param int $uid 用户id
	 * @param string $password_in 密码
	 * @return true 验证成功，false 验证失败
	 * @author huajie <banhuajie@163.com>
	 */
	protected function verifyUser($uid, $password_in){
		$model = new UcenterMemberModel();
		$password = $model::where('id',$uid)->value('password');
		if(think_ucenter_md5($password_in, UC_AUTH_KEY) === $password){
			return true;
		}
		return false;
	}

}
