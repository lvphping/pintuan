<!doctype html>
<html lang="zh-CN">
<head>
<meta name="Generator" content="haohaipt v6.0" />
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=no">
<meta name="Keywords" content="<?php echo $this->_var['keywords']; ?>" />
<meta name="Description" content="<?php echo $this->_var['description']; ?>" />
<meta name="format-detection" content="telephone=no">
<title><?php echo $this->_var['page_title']; ?></title>
<link rel="shortcut icon" href="favicon.ico" />
<link href="<?php echo $this->_var['hhs_css_path']; ?>/style.css" rel="stylesheet" />
<link href="<?php echo $this->_var['hhs_css_path']; ?>/font-awesome.min.css" rel="stylesheet" />
<?php echo $this->smarty_insert_scripts(array('files'=>'jquery.js,haohaios.js,user.js')); ?>
</head>
<body>
<div class="container">
    <?php if ($this->_var['action'] == "account_raply" || $this->_var['action'] == "account_log" || $this->_var['action'] == "account_deposit" || $this->_var['action'] == "account_detail" || $this->_var['action'] == "integral_details"): ?> 
    <script type="text/javascript">
          <?php $_from = $this->_var['lang']['account_js']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'item');if (count($_from)):
    foreach ($_from AS $this->_var['key'] => $this->_var['item']):
?>
            var <?php echo $this->_var['key']; ?> = "<?php echo $this->_var['item']; ?>";
          <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
    </script>
    <div class="account_top">
        <p><a href="user.php?act=account_log">可用资金：<?php echo $this->_var['surplus_amount']; ?></a> <span><a href="user.php?act=integral_details">可用积分：<?php echo $this->_var['points']; ?></span></a></p>
    </div>
    <div class="nav_fixed nav_spike" style="top:40px;"> 
        <a href="user.php?act=account_detail"<?php if ($this->_var['action'] == 'account_detail'): ?> class="cur"<?php endif; ?>><span>账目明细</span></a>
        <a href="user.php?act=account_raply"<?php if ($this->_var['action'] == 'account_raply'): ?> class="cur"<?php endif; ?>><span>提现</span></a>
		<a href="user.php?act=account_deposit"<?php if ($this->_var['action'] == 'account_deposit'): ?> class="cur"<?php endif; ?>><span>充值</span></a>
        <a href="user.php?act=account_log"<?php if ($this->_var['action'] == 'account_log'): ?> class="cur"<?php endif; ?>><span>申请记录</span></a>
		
    </div>
    <?php endif; ?>
    <div class="account_box"> 
        <?php if ($this->_var['action'] == "account_raply"): ?>
        <form name="formSurplus" method="post" action="user.php" onSubmit="return submitSurplus()">
            <div class="account_deposit">
                <h3>每次提现金额在￥1～￥200以内</h3>
                <ul>
                    <li>
                        <input type="text" name="amount" value="<?php echo htmlspecialchars($this->_var['order']['amount']); ?>" class="inp" placeholder="<?php echo $this->_var['lang']['repay_money']; ?>" />
                    </li>
                    <li>
                        <textarea name="user_note" class="tex" placeholder="<?php echo $this->_var['lang']['process_notic']; ?>"><?php echo htmlspecialchars($this->_var['order']['user_note']); ?></textarea>
                    </li>
                    <li>
                        <input type="hidden" name="surplus_type" value="1" />
                        <input type="hidden" name="act" value="act_account" />
                        <input type="submit" name="submit" onclick="done();"  class="bnt" value="<?php echo $this->_var['lang']['submit_request']; ?>" />
                    </li>
                </ul>
            </div>
        </form>
        <?php endif; ?> 
        <?php if ($this->_var['action'] == "account_deposit"): ?>
        <!--<form name="formSurplus" method="post" action="user.php" onSubmit="return submitSurplus()">-->
            <div class="account_deposit">
                <ul>
                    <li>
                        <input type="text" name="amount" id="inp_pay" class="inp" value="<?php echo htmlspecialchars($this->_var['order']['amount']); ?>" placeholder="<?php echo $this->_var['lang']['deposit_money']; ?>" />
                    </li>
                    <li>
                        <textarea name="user_note" id="tex_pay" class="tex" placeholder="<?php echo $this->_var['lang']['process_notic']; ?>"><?php echo htmlspecialchars($this->_var['order']['user_note']); ?></textarea>
                    </li>
                    <li>
                        <?php $_from = $this->_var['payment']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'list');if (count($_from)):
    foreach ($_from AS $this->_var['list']):
?>
						<div class="pay-box">
                        <input type="radio" name="payment_id" id="payment_<?php echo $this->_var['list']['pay_id']; ?>" value="<?php echo $this->_var['list']['pay_id']; ?>">
                        <label for="payment_<?php echo $this->_var['list']['pay_id']; ?>" class="label-btn"></label>
                        <label for="payment_<?php echo $this->_var['list']['pay_id']; ?>"><i class="ico_<?php echo $this->_var['list']['pay_code']; ?>"></i><?php echo $this->_var['list']['pay_name']; ?></label>
                        </div>
                        <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
					</li>
                    <li>
                        <input type="hidden" name="surplus_type" value="0" />
                        <input type="hidden" name="rec_id" value="<?php echo $this->_var['order']['id']; ?>" />
                        <input type="hidden" name="act" value="act_account" />
                        <input type="submit" id="J_btn" class="bnt" name="submit" value="<?php echo $this->_var['lang']['submit_request']; ?>" />
                    </li>
                </ul>
            </div>
        <!--</form>-->
        <?php endif; ?> 
        
        <?php if ($this->_var['action'] == "act_account"): ?>
        <table width="100%" class="list_table" border="0" cellspacing="0" cellpadding="0">
            <tr>
                <td width="25%" align="right"><?php echo $this->_var['lang']['surplus_amount']; ?></td>
                <td width="80%"><?php echo $this->_var['amount']; ?></td>
            </tr>
            <tr>
                <td align="right"><?php echo $this->_var['lang']['payment_name']; ?></td>
                <td><?php echo $this->_var['payment']['pay_name']; ?></td>
            </tr>
            <tr>
                <td align="right"><?php echo $this->_var['lang']['payment_fee']; ?></td>
                <td><?php echo $this->_var['pay_fee']; ?></td>
            </tr>
            <tr>
                <td align="right"><?php echo $this->_var['lang']['payment_desc']; ?></td>
                <td bgcolor="#ffffff"><?php echo $this->_var['payment']['pay_desc']; ?></td>
            </tr>
            <tr>
                <td colspan="2"><?php echo $this->_var['payment']['pay_button']; ?></td>
            </tr>
        </table>
        <?php endif; ?> 
        
        <?php if ($this->_var['action'] == "account_detail"): ?>
        <div class="account_detail">
            <table width="100%" class="list_table" border="0" cellspacing="0" cellpadding="0">
                <tr align="center">
                    <td width="80"><?php echo $this->_var['lang']['process_time']; ?></td>
                    <td><?php echo $this->_var['lang']['surplus_pro_type']; ?></td>
                    <td><?php echo $this->_var['lang']['money']; ?></td>
                    <td width="160"><?php echo $this->_var['lang']['change_desc']; ?></td>
                </tr>
                
                <?php $_from = $this->_var['account_log']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'item');if (count($_from)):
    foreach ($_from AS $this->_var['item']):
?>
                
                <tr>
                    <td align="center"><?php echo $this->_var['item']['change_time']; ?></td>
                    <td align="center"><?php echo $this->_var['item']['type']; ?></td>
                    <td align="center"><?php echo $this->_var['item']['amount']; ?></td>
                    <td title="<?php echo $this->_var['item']['change_desc']; ?>">&nbsp;&nbsp;<?php echo $this->_var['item']['short_change_desc']; ?></td>
                </tr>
                
                <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                
            </table>
        </div>
        <?php echo $this->fetch('library/pages.lbi'); ?>
        <?php endif; ?>
		
		
		<?php if ($this->_var['action'] == "integral_details"): ?>
        <div class="account_detail">
            <table width="100%" class="list_table" border="0" cellspacing="0" cellpadding="0">
                <tr align="center">
                    <td>日期</td>
                    <td>积分</td>
                    <td>变化原因</td>
                </tr>
                
                <?php $_from = $this->_var['my_points']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'item');if (count($_from)):
    foreach ($_from AS $this->_var['item']):
?>
                
                <tr>
                    <td align="center"><?php echo $this->_var['item']['change_time']; ?></td>
                    <td align="center"><?php echo $this->_var['item']['pay_points']; ?></td>
                    <td align="center"><?php echo $this->_var['item']['change_desc']; ?></td>
                </tr>
                
                <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                
            </table>
        </div>
        <?php echo $this->fetch('library/pages.lbi'); ?>
        <?php endif; ?> 
        
        <?php if ($this->_var['action'] == "account_log"): ?>
        <div class="account_log">
            <table width="100%" class="list_table" border="0" cellspacing="0" cellpadding="0">
            
                <tr align="center">
                    <td width="80"><?php echo $this->_var['lang']['process_time']; ?></td>
                    <td><?php echo $this->_var['lang']['surplus_pro_type']; ?></td>
                    <td><?php echo $this->_var['lang']['money']; ?></td>
                    <td><?php echo $this->_var['lang']['is_paid']; ?></td>
                    <td width="90"><?php echo $this->_var['lang']['handle']; ?></td>
                </tr>
                
                <?php $_from = $this->_var['account_log']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'item');if (count($_from)):
    foreach ($_from AS $this->_var['item']):
?>
                
                <tr>
                    <td align="center"><?php echo $this->_var['item']['add_time']; ?></td>
                    <td align="center"><?php echo $this->_var['item']['type']; ?></td>
                    <td align="center"><?php echo $this->_var['item']['amount']; ?></td>
                    <td align="center"><?php echo $this->_var['item']['pay_status']; ?></td>
                    <td align="center"><?php echo $this->_var['item']['handle']; ?> 
                        
                        <?php if (( $this->_var['item']['is_paid'] == 0 && $this->_var['item']['process_type'] == 1 ) || $this->_var['item']['handle']): ?> 
                        
                        <a href="user.php?act=cancel&id=<?php echo $this->_var['item']['id']; ?>" onclick="if (!confirm('<?php echo $this->_var['lang']['confirm_remove_account']; ?>')) return false;"><?php echo $this->_var['lang']['is_cancel']; ?></a> 
                        
                        <?php endif; ?></td>
                </tr>
                
                <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                
            </table>
        </div>
        <?php echo $this->fetch('library/pages.lbi'); ?>
        <?php endif; ?>
    </div>
</div>
<div class="blank"></div>
<?php echo $this->fetch('library/footer.lbi'); ?>

<script type="text/javascript" src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
<script>
$(function(){
        
      $('#J_btn').click(function(){
         var money = $('#inp_pay').val();
         
         var intro = $('#tex_pay').val(); 
         var pay_type =$('[name=payment_id]:checked').val();
          if(isNaN(money) || money==''){
            alert('请填写充值金额,必须是数字');
            return false;
          }
          if(intro==''){
            alert('请填写备注信息');
            return false;
          }
          if(isNaN(pay_type)) {
             alert('请选择支付方式');
             return false;
          }     
          $(this).val('正在支付...');
           
            $.ajax({
              type: "POST",
              dataType: 'JSON',
              url: "user.php?act=pay_lib",
              data:{pay_type:pay_type,money:money,intro:intro},
              success: function(result){
                if(result.error==0){
            		if(result.pay_code=='wxpay'){
						
            			callpay(result.content.jsApiParameters,result.url);
            		}
            		else if(result.pay_code=='alipay'){
            		  
            			window.location='toalipay_chong.php?op=<?php echo $this->_var['op']; ?>&m='+result.m;
            		}
            	}
              }
           });
      })
      
      function jsApiCall(code,returnrul){
    	WeixinJSBridge.invoke('getBrandWCPayRequest',code,function(res){
    			WeixinJSBridge.log(res.err_msg);
    			//alert(res.err_code+'调试信息：'+res.err_desc+res.err_msg);		
    			if(res.err_msg.indexOf('ok')>0){
    				window.location.href=returnrul;
    			}else{
    				window.location.href=returnrul;
    			}
    		});
    }
    		function callpay(code,returnrul)
    		{
    			if (typeof WeixinJSBridge == "undefined"){
    			    if( document.addEventListener ){
    			        document.addEventListener('WeixinJSBridgeReady', jsApiCall, false);
    			    }else if (document.attachEvent){
    			        document.attachEvent('WeixinJSBridgeReady', jsApiCall);
    			        document.attachEvent('onWeixinJSBridgeReady', jsApiCall);
    			    }
    			}else{
    			    jsApiCall(code,returnrul);
    			}
    		}
    		
    function done_response(result){
        //console.log(result);return;
    	if(result.error==0){
    		if(result.pay_code=='wxpay'){
    			callpay(result.content.jsApiParameters,result.content.returnrul);
    		}
    		else if(result.pay_code=='alipay'){
    			window.location='toalipay.php?order_id='+result.order_id;
    		}
        else if(result.pay_code=='balance'){
          window.location=result.url;
        }
    	}else if(result.error==1){
        //console.log(result);return;
    	 setTimeout(function(){
            	window.location=result.url;
             },150);
    	}else if(result.error==2){
    		alert(result.message);
    		$('#btn_pay_now').val('立即支付');
    	}
    	
    }
})
     
            
     
</script>
</body>
</html>

