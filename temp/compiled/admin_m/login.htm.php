<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo $this->_var['lang']['cp_home']; ?><?php if ($this->_var['ur_here']): ?> - <?php echo $this->_var['ur_here']; ?><?php endif; ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="css/mobile.css" rel="stylesheet" type="text/css" />
<?php echo $this->smarty_insert_scripts(array('files'=>'../js/utils.js,validator.js')); ?>
<script language="JavaScript">
<!--
// 这里把JS用到的所有语言都赋值到这里
<?php $_from = $this->_var['lang']['js_languages']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'item');if (count($_from)):
    foreach ($_from AS $this->_var['key'] => $this->_var['item']):
?>
var <?php echo $this->_var['key']; ?> = "<?php echo $this->_var['item']; ?>";
<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>

if (window.parent != window)
{
  window.top.location.href = location.href;
}

//-->
</script>
</head>
<body>
<div class="container">	
	<div class="logintop">
		<h1 class="loginlogo" style="background-image:url(images/login_logo.png);" title="管理平台">管理平台</h1>
	</div>
	<div class="loginbox">
    <form method="post" action="privilege.php" name='theForm' onsubmit="return validate()">
		<ul class="logincon">
			<li class="title">管理中心登录</li>
			<li>
				<i class="username"></i>
				<div><input type="text" title="用户名" name="username" value="" class="inputname" onfocus="this.className='inputname act'" onblur="this.className='inputname'"></div>
			</li>
			<li>
				<i class="password"></i>
				<div><input type="password" title="密码" name="password" value="" class="inputpwd" onfocus="this.className='inputpwd act'" onblur="this.className='inputpwd'"></div>
			</li>
			<li>
			 <input type="hidden" value="1" name="remember" id="remember" />
             <input type="hidden" name="act" value="signin" />
			<div><input type="submit" title="登录" value="登 录" class="loginbtn" /></div>
			</li>
		</ul>
        </form>
	</div>
</div>
<script language="JavaScript">
<!--
  document.forms['theForm'].elements['username'].focus();
  
  /**
   * 检查表单输入的内容
   */
  function validate()
  {
    var validator = new Validator('theForm');
    validator.required('username', user_name_empty);
    //validator.required('password', password_empty);
    if (document.forms['theForm'].elements['captcha'])
    {
      validator.required('captcha', captcha_empty);
    }
    return validator.passed();
  }
  
//-->
</script>
</body>