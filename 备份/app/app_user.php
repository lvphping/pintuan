<?php
define('IN_HHS', true);

if($action =='get_code')
{
	//include_once 'includes/cls_sms.php';
	include_once(ROOT_PATH . '/includes/cls_sms.php');
	$user_name =  $_REQUEST['user_name'];

	
		$code = getRandomCode(5);
		$sms = new sms();
		$msg = '尊敬的用户，您的校验证码为：' . $code . '，用于注册验证，请勿向任何人提供您收到的短信校验证码。';
		$send = $sms->send($user_name, $msg, '', 1);
		$send =1;
		$_SESSION['validate_mobile_code'] = $code;
		if ($send)
		{
			$results['content'] = '发送成功';
			$results['error'] =1;
			$results['mobile_code'] =$code;
		}
	echo $json->encode($results);
	die();
}
elseif($action =='weixin_reg')
{
	$headimgurl = $_REQUEST['headimgurl'];
	$nickname = $_REQUEST['nickname'];
	$user_name = $_REQUEST['username'];
	$unionid  = $_REQUEST['unionid'];
	$openid   = $_REQUEST['openid'];
	$rs = $db->getRow("select * from ".$hhs->table('users')." where unionid='$unionid'");
	
	if($rs)
	{
		$result['user_id'] = $rs['user_id'];
		$result['username'] = $rs['uname'];
		$result['headimgurl'] = $rs['headimgurl'];
	}
	else
	{
		include_once(ROOT_PATH . 'includes/lib_passport.php');
		$ychar="0,1,2,3,4,5,6,7,8,9,a,b,c,d,e,f,g,h,i,j,k,l,m,n,o,p,q,r,s,t,u,v,w,x,y,z";
		$list=explode(",",$ychar);
		$password='';
		for($i=0;$i<6;$i++)
		{
			$randnum=rand(0,35);
			$password.=$list[$randnum];
		}
		$sql="select user_id from ".$hhs->table('users')." order by user_id desc limit 1";
		$user_id=$db->getOne($sql)+1;
		$username = 'wx'.$user_id.mt_rand(0,100);
		$email    = '';
		$other['msn'] = '';
		$other['qq'] = '';
		$other['office_phone'] = '';
		$other['home_phone'] = '';
		$other['mobile_phone'] = '';
		$other['openid'] = '';
		$other['app_openid'] = $openid;
		$other['unionid'] = $unionid;
        $other['subscribe'] ='';
		$other['uname']=filterNickname($nickname);
		$other['headimgurl'] =$headimgurl;
		if (register($username, $password, $email, $other) !== false)
		{
			
			$result['user_id'] = $_SESSION['user_id'];
			$result['username'] = $user_name;
			$result['headimgurl'] = $headimgurl;
		}
	}
	$results['error'] =0;
	echo $json->encode($result);
	die();
}
elseif($action =='register_act')
{
	include_once ROOT_PATH . 'includes/lib_passport.php';
	include_once ROOT_PATH . 'includes/lib_transaction.php';
    $username = isset($_REQUEST['username']) ? trim($_REQUEST['username']) : '';
    $password = isset($_REQUEST['password']) ? trim($_REQUEST['password']) : '';
	$code = $_REQUEST['code'];
	$email= $username."@139.com";
	$other = array();
	
	$count = $db->getOne("select count(*) from ".$hhs->table('users') ." where user_name='$username'");
	if($count)
	{
		$results['content'] = '该手机号码已经存在';
		$results['error'] =0;
		echo $json->encode($results);
		die();
	}
	if($code!=$_SESSION['validate_mobile_code'])
	{
		$results['content'] = '输入验证码不正确';
		$results['error'] =0;
		echo $json->encode($results);
		die();
		
	}
	
	if (register($username, $password, $email, $other) !== false)
	 {
		   $sql = 'UPDATE ' . $hhs->table('users') . ' SET `reg_time`=\'' . gmtime() . '\'  WHERE `user_id`=\'' . $_SESSION['user_id'] . '\'';
		   $db->query($sql);
		  
		  /* 判断是否需要自动发送注册邮件 */
		  
		  if ($GLOBALS['_CFG']['sms_user_register'] == 1 && $other['mobile_phone'] != '') {
			  //echo "sdfsdf";exit;
			  include_once 'includes/cls_sms.php';
			  $sms = new sms();
			  $msg = '欢迎您加入'.$_CFG['shop_name'].'，用户名：' . $username . '，密码:' . $password . '，请牢记相关信息，工作人员不会向您索取，请勿泄露。';
			  $sms->send($username, $msg, '', 13, 1);
		  }
		$results['content'] = '注册成功';
		$results['error'] =1;
		$results['user_id'] =$_SESSION['user_id'];
		echo $json->encode($results);
		die();
	
	} 
	else
	 {
		$results['content'] = '注册失败';
		$results['error'] =0;
		echo $json->encode($results);
		die();
	}   																									
}
elseif($action =='login_act')
{
	include_once ROOT_PATH . 'includes/lib_passport.php';
	include_once ROOT_PATH . 'includes/lib_transaction.php';
	$username = isset($_REQUEST['username']) ? trim($_REQUEST['username']) : '';
	$password = 123456;
	$email= $username."@139.com";
	$code = $_REQUEST['code'];
	if($code!=$_SESSION['validate_mobile_code'])
	{
		$results['content'] = '输入验证码不正确';
		$results['error'] =0;
		echo $json->encode($results);
		die();
		
	}
	
	
	$count = $db->getOne("select count(*) from ".$hhs->table('users')." where user_name='$username'");
	
	if($count)
	{
	 
		  $rows = $db->getRow("select * from ".$hhs->table('users')." where user_name='$username'");
		  $results['content'] = '登录成功';
		  $results['error'] =1;
		  $results['is_login'] =1;
		 
		  $results['user_id'] =$rows['user_id'];
		  $results['uname'] =$rows['uname'];
		  echo $json->encode($results);
		  die();
		 
	}
	else
	{
		  if (register($username, $password, $email, $other) !== false)
		   {
				 $sql = 'UPDATE ' . $hhs->table('users') . ' SET `reg_time`=\'' . gmtime() . '\'  WHERE `user_id`=\'' . $_SESSION['user_id'] . '\'';
				 $db->query($sql);
				
				/* 判断是否需要自动发送注册邮件 */
			
			  $results['content'] = '登录成功';
			  $results['error'] =1;
			  $results['is_login'] =1;
			  $results['user_id'] =$_SESSION['user_id'];
			  echo $json->encode($results);
			  die();
		  
		  } 
		  else
		   {
			  $results['content'] = '登录失败';
			  $results['error'] =0;
			  echo $json->encode($results);
			  die();
		  }   																									
	}
}
elseif($action ='get_password')
{
	$user_name =  $_REQUEST['username'];
	$count = $db->getOne("select count(*) from ".$hhs->table('users')." where user_name='$user_name'");
	if(!$count)
	{
		$results['content'] = '输入的手机号码不存在';
		$results['error'] =0;
		
	}
	else
	{
		$code = getRandomCode(5);
		include_once 'includes/cls_sms.php';
		$sms = new sms();
		$msg = '尊敬的用户，您的校验证码为：' . $code . '，用于手机找回密码服务，请勿向任何人提供您收到的短信校验证码。';       #$send = $sms->code_send($mobile, $msg, '', 1);
		$send = $sms->send($mobile, $msg, '', 13, 1);
		$results['code'] = $code;
		$results['user_id'] = $user_id;
		$results['error'] =1;
		
	}
	echo $json->encode($results);
	die();

}
elseif($action ='update_password')
{
	$password =  md5($_REQUEST['password']);
	$username =  $_REQUEST['username'];
	$code =   $_REQUEST['code'];
	
	if($code!=$_SESSION['validate_mobile_code'])
	{
		$results['content'] = '输入验证码不正确';
		$results['error'] =0;
		echo $json->encode($results);
		die();
		
	}
	
	
	
	$sql="UPDATE ".$hhs->table('users'). "SET `ec_salt`='0',`password`='$password' WHERE user_name= '".$username."'";
	$db->query($sql);
	$results['error'] =1;
	echo $json->encode($results);
	die();
}
