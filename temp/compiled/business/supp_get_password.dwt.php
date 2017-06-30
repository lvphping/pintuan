<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta name="Generator" content="haohaipt v6.0" />
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>商家管理平台</title>
	<link href="templates/css/get_password.css" rel="stylesheet" type="text/css" />
</head>
<body>
<div class="get_pwd">
<?php if ($this->_var['form_act'] == "forget_pwd"): ?>
<form action="index.php?op=get_password" method="post" name="submitAdmin" onsubmit="return validate()">
  <table cellspacing="0" cellpadding="0" align="center">
  <tr>
    
    <td >
      <table>
        <tr>
          <td colspan="2"><h3>商家密码找回</h3></td>
        </tr>
        <tr>
          <td>商家用户名</td>
          <td>
            <input type="text" name="user_name" maxlength="20" size="30"/><span class="require-field">*</span>
          </td>
        </tr>
        <tr>
          <td><?php echo $this->_var['lang']['email']; ?></td>
          <td>
            <input type="text" name="email" size="30" /><span class="require-field">*</span>
         </td>
        </tr>
       <tr>
         <td>&nbsp;</td>
         <td>
           <input type="hidden" name="action" value="get_pwd" />
           <input type="hidden" name="act" value="forget_pwd" />
           <input type="submit" value="确定" class="button" />
           <input type="reset" value="重置" class="button" />
         </td>
       </tr>
      </table>
    </td>
  </tr>
  </table>
</form>
<?php endif; ?>
<?php if ($this->_var['form_act'] == "reset_pwd"): ?>
<form action="index.php?op=get_password" method="post" name="submitPwd" onsubmit="return validate2()">
  <table cellspacing="0" cellpadding="0" align="center">
  <tr>
    <td >
      <table>
        <tr>
          <td colspan="2"><h3>商家密码找回</h3></td>
        </tr>
        <tr>
          <td>商家新密码</td>
          <td>
            <input type="password" name="password" size="30"/><span class="require-field">*</span>
          </td>
        </tr>
        <tr>
          <td>商家确认密码</td>
          <td>
            <input type="password" name="confirm_pwd" size="30" /><span class="require-field">*</span>
         </td>
        </tr>
       <tr>
         <td>&nbsp;</td>
         <td>
           <input type="hidden" name="action" value="reset_pwd" />
           <input type="hidden" name="act" value="forget_pwd" />
           <input type="hidden" name="adminid" value="<?php echo $this->_var['adminid']; ?>" />
           <input type="hidden" name="code" value="<?php echo $this->_var['code']; ?>" />
           <input type="submit" value="确定" class="button" />
           <input type="reset" value="重置" class="button" />
         </td>
       </tr>
      </table>
    </td>
  </tr>
  </table>
</form>
<?php endif; ?>
</div>
</body>
</html>