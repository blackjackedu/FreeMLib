<?php
namespace JYLibrary\Model;

use Zend\Session\Container;


class LoginModel
{
	public $username;
	public $id;
	public $password;
	public $auth;
	public $loginDate;	
	public $sid;
	
	public static function getLoginModel()
	{
		$container = new Container('namespace');
		$lm=$lm=new LoginModel();
		if($container->auth)
		{			
			$lm->username=$container->username;
			$lm->password=$container->password;
			$lm->id=$container->id;
			$lm->auth=true;
			$lm->sid=session_id();
		}else
		{
			$lm->auth=false;
		}
		return $lm;
	}
	public static function setLoginModel($u,$i,$p,$a,$d)
	{
		$container = new Container('namespace');
		$container->auth=true;
		$container->id=$i;
		$container->password=$p;
		$container->loginDate=$d;
		$container->username=$u;
	}
	
	public static function logout()
	{
		$container = new Container('namespace');
		$container->auth=false;
		$container->id=null;
		$container->password=null;
		$container->loginDate=null;
		$container->username=null;
		session_destroy();
		$_SESSION = array();
	}
}

?>