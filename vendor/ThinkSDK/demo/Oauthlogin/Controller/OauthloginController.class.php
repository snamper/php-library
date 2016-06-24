<?php
namespace Addons\Oauthlogin\Controller;
use Home\Controller\AddonsController; 
use Think\ThinkSDK\ThinkOauth;
use User\Api\UserApi as UserApi;
class OauthloginController extends AddonsController{
	//  登陆
	public function login(){
        $type = I('get.type');
	    if (!empty($type)) {
	    $sns  = ThinkOauth::getInstance($type);
		//跳转到授权页面
		redirect($sns->getRequestCodeURL());
	 }
	 }
	  	//sdk授权回调地址
	public function callback($type = null, $code = null){
	   $code = I('get.code');
	   $type = I('get.type');
		(empty($type) || empty($code)) && $this->error('参数错误');
		
		//加载ThinkOauth类并实例化一个对象
		$sns  = ThinkOauth::getInstance($type);

		//腾讯微博需传递的额外参数
		$extend = null;
		if($type == 'tencent'){
			$extend = array('openid' => $this->_get('openid'), 'openkey' => $this->_get('openkey'));
		}
		//请妥善保管这里获取到的Token信息，方便以后API调用
		//调用方法，实例化SDK对象的时候直接作为构造函数的第二个参数传入
		//如： $qq = ThinkOauth::getInstance('qq', $token);
		$token = $sns->getAccessToken($code , $extend);

		//获取当前登录用户信息
		if(is_array($token)){
			//$user_info = A('Type', 'Model')->$type($token);
			//$user_info=D('Type')->$type($token);
			$user_info=D('Addons://Oauthlogin/Oauthlogin')->$type($token);
			//echo("<h1>恭喜！使用 {$type} 用户登录成功</h1><br>");
			//echo("授权信息为：<br>");
			//dump($token);
			//echo("当前登录用户信息为：<br>");
			//dump($user_info);
		$sdkinfo  = M('member_sdk')->where(array('oauth_token'=>$token['access_token']))->field('type_uid,uid')->find();
               if($sdkinfo) 
			   {
			   $uid= $sdkinfo['uid'];
			   /* 调用UC登录接口登录 */
			   $user = new UserApi;
			  /* 登录用户 */
				$Member = D('Member');
				if($Member->login($uid)){ //登录用户
					//TODO:跳转到登录前页面
					$this->success('登录成功！',U('Home/Index/index'));
				}
			    }
				else 
				{
			/* 调用注册接口注册用户 */
            $User = new UserApi;
			$username=$user_info['name']."_".$user_info['nick'];
			$password="11111111111111111";
			$email="xxxxx@qq.com";
			$uid = $User->register($username, $password, $email);
			if(0 < $uid){
				//TODO: 发送验证邮件
			  $data['uid'] = $uid;
              $data['type_uid'] = $token['openid'];
              $data['type'] = $type;
              $data['oauth_token'] = $token['access_token'];
              M('member_sdk')->add($data);
				 /* 登录用户 */
				$Member = D('Member');
				if($Member->login($uid)){ //登录用户
					//TODO:跳转到登录前页面
					$this->success('登录成功！',U('Home/Index/index'));
				}
			} else { //注册失败，显示错误信息
				//$this->error($this->showRegError($uid));
				//echo $uid.$token['access_token'];
			}
				}
			  
		}
	}
	}
?>