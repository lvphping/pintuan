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
<link href="<?php echo $this->_var['hhs_css_path']; ?>/order.css" rel="stylesheet" />
<link href="<?php echo $this->_var['hhs_css_path']; ?>/font-awesome.min.css" rel="stylesheet" />
<?php echo $this->smarty_insert_scripts(array('files'=>'jquery.js,haohaios.js,user.js,shopping_flow.js,region.js')); ?>
<script type="text/javascript">
	//调用微信JS api 支付
	function jsApiCall(code,returnrul,is_check,team_sign){
		if(is_check==1){
			$.ajax({
				type:"post",//请求类型
				url:"team_info.php",//服务器页面地址
				data:"team_sign="+team_sign,
				dataType:"json",
				error:function(){
					//alert("ajax出错啦");
				},
				success:function(data){
					if(data.error==0){
						WeixinJSBridge.invoke('getBrandWCPayRequest',code,function(res){
							WeixinJSBridge.log(res.err_msg);
							if(res.err_msg.indexOf('ok')>0){
								window.location.href=returnrul;
							}
						});
					}else{
					   window.location=data.url;
					}
				}
			});
		}else{
			WeixinJSBridge.invoke('getBrandWCPayRequest',code,function(res){
				WeixinJSBridge.log(res.err_msg);
				// alert(res.err_code+'调试信息：'+res.err_desc+res.err_msg);
				if(res.err_msg.indexOf('ok')>0){
					window.location.href=returnrul;
				}
			});
		}
	}

	function callpay(code,returnrul,is_check,team_sign)
	{
		 if (typeof WeixinJSBridge == "undefined"){
			if( document.addEventListener ){
				document.addEventListener('WeixinJSBridgeReady', jsApiCall, false);
			}else if (document.attachEvent){
				document.attachEvent('WeixinJSBridgeReady', jsApiCall); 
				document.attachEvent('onWeixinJSBridgeReady', jsApiCall);
			}
		}else{
			jsApiCall(code,returnrul,is_check,team_sign);
		} 
	}
</script>
</head>
<body>
<div class="container"> 
    <?php if ($this->_var['action'] == 'order_list'): ?>
    <div class="nav_fixed nav_order">
        <a href="user.php?act=order_list"<?php if ($this->_var['composite_status'] == ''): ?> class="cur"<?php endif; ?>><span>全部</span></a>
        <a href="user.php?act=order_list&composite_status=100"<?php if ($this->_var['composite_status'] == 100): ?> class="cur"<?php endif; ?>><span>待付款</span></a>
		<a href="user.php?act=order_list&composite_status=180"<?php if ($this->_var['composite_status'] == 180): ?> class="cur"<?php endif; ?>><span>待发货</span></a>
        <a href="user.php?act=order_list&composite_status=120"<?php if ($this->_var['composite_status'] == 120): ?> class="cur"<?php endif; ?>><span>待收货</span></a>
        <a href="user.php?act=order_list&composite_status=999"<?php if ($this->_var['composite_status'] == 999): ?> class="cur"<?php endif; ?>><span>已完成</span></a>
    </div>
    <div id="dealliststatus1" style="padding-top:50px;"> 
        <?php $_from = $this->_var['orders']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'item');if (count($_from)):
    foreach ($_from AS $this->_var['item']):
?>
        <div class="order">
            <div class="order_hd"> <?php if ($this->_var['item']['suppliers_name']): ?><img src="<?php echo $this->_var['item']['supp_logo']; ?>"><?php echo $this->_var['item']['suppliers_name']; ?><?php else: ?><img src="themes/haohainew/images/logo.gif"><?php echo $this->_var['shop_name']; ?>自营店<?php endif; ?> <span><?php echo $this->_var['item']['order_status']; ?></span></div>
            <div class="order_bd">
                <div class="order_glist"> <?php $_from = $this->_var['item']['goods_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'goods');$this->_foreach['foreach_goods_list'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['foreach_goods_list']['total'] > 0):
    foreach ($_from AS $this->_var['goods']):
        $this->_foreach['foreach_goods_list']['iteration']++;
?>
                    <div class="order_goods">
                        <div class="order_goods_img" onclick="window.location='user.php?act=order_detail&order_id=<?php echo $this->_var['item']['order_id']; ?>';"> <img alt="<?php echo $this->_var['goods']['goods_name']; ?>" src="<?php echo $this->_var['goods']['goods_thumb']; ?>"> </div>
                        <div class="order_goods_info">
                            <div class="order_goods_name" onclick="window.location='user.php?act=order_detail&order_id=<?php echo $this->_var['item']['order_id']; ?>';"><?php echo $this->_var['goods']['goods_name']; ?></div>

                            <div class="order_goods_attr">
                                <p class="order_goods_attr_item"><?php echo $this->_var['goods']['goods_attr']; ?></p>
                                <p class="order_goods_attr_item">
                                    <font class="order_goods_price"><?php if ($this->_var['item']['integral']): ?>积分兑换<?php else: ?><i>¥</i><?php echo $this->_var['goods']['goods_price_fmt']; ?><?php endif; ?> <small> X<?php echo $this->_var['goods']['goods_number']; ?></small></font>
									<span class="order_btn2">
									<?php if (! $this->_var['goods']['comment'] && ( $this->_var['item']['order_status2'] == 1 || $this->_var['item']['order_status2'] == 5 ) && $this->_var['item']['shipping_status'] == 2): ?>
									<a class="order_btn_buy" href="javascript:void(0);" data-id="<?php echo $this->_var['goods']['goods_id']; ?>" data-order_id="<?php echo $this->_var['item']['order_id']; ?>">去评价</a>
									<?php endif; ?>
									</span>
                                </p>

                                

                            </div>

                        </div>

                    </div>

                    <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>

                    <div class="order_ft">

                        <div class="order_total"> <span class="order_total_info">共<?php echo $this->_var['goods']['goods_number']; ?>件商品</span> <span class="order_price">总金额：<b>¥<?php echo $this->_var['item']['total_fee']; ?></b></span> <span class="coupon_icon" ms-if="order.coupons.length>0"></span> </div>

                        <div class="order_opt">

                            <div class="order_btn"> 

                                <?php echo $this->_var['item']['handler']; ?> 

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>
		<?php endforeach; else: ?>
        <div class="nothing">
            <i class="iconfont icon-order"></i>
		    <p>您还没有任何相关订单哦</p>
        </div>
        <div class="blank"></div>
        <?php endif; unset($_from); ?><?php $this->pop_vars();; ?> 
        
<div class="common">
    <a href="javascript:void(0);" class="close"></a>
    <div class="common-title">提交评价</p>
    </div>
    <div class="common-content">
        <ul>
            <li>
                <label>评分</label>
                <div id="QuacorGrading">
                <input name="1" type="button" />
                <input name="2" type="button" />
                <input name="3" type="button" />
                <input name="4" type="button" />
                <input name="5" type="button" />
                </div>
            </li>
            <li>
                <label>内容</label>
                <textarea name="content" id="comment"></textarea>
            </li>
            <li>
                <label>　</label>
                <input id="btn-send" type="submit"  value="提交评论">
            </li>
        </ul>
    </div>
</div>
<div class="common-bg"></div>
<script>
var stars = 0;
var id_value = 0;
var order_id = 0;
$(function () {
    H_login = {};
    H_login.openLogin = function(){
        $('.order_btn_buy').click(function(){
            id_value = $(this).data('id');
            order_id = $(this).data('order_id');
            $("#comment").val('');
            $('.common').show();
            $('.common-bg').show();
        });
    };
    H_login.closeLogin = function(){
        $('.close').click(function(){
            $('.common').hide();
            $('.common-bg').hide();
        });
    };
    H_login.run = function () {
        this.closeLogin();
        this.openLogin();
    };
    H_login.run();

    $("#btn-send").click(function(event) {
       var comment = $.trim($("#comment").val());
       if (comment.length>0) {
        $.ajax({
            url: 'comment.php?act=create',
            type: 'POST',
            dataType: 'json',
            data: {comment: comment, stars: stars,id_value: id_value,order_id: order_id},
            success: function (res) {
                if(res.isError == 1){
                    //alert(res.message)
					layer.open({
		                content: res.message,
			            btn: ['嗯']
		           });
                }
                else{
                    location.href='user.php?act=order_list&composite_status=999';
                }
            }
        })
        
       }
    });
});

var GradList = document.getElementById("QuacorGrading").getElementsByTagName("input");
/*
for(var di=0;di<parseInt(document.getElementById("QuacorGradingValue").getElementsByTagName("font")[0].innerHTML);di++){
	GradList[di].style.backgroundPosition = 'left center';
}
*/
for(var i=0;i < GradList.length;i++){
	GradList[i].onmouseover = function(){
		for(var Qi=0;Qi<GradList.length;Qi++){
			GradList[Qi].style.backgroundPosition = 'right center';
		}
		for(var Qii=0;Qii<this.name;Qii++){
			GradList[Qii].style.backgroundPosition = 'left center';
		}
        stars = this.name;
		//document.getElementById("QuacorGradingValue").innerHTML = '<b><font size="5" color="#fd7d28">'+this.name+'</font></b>分';
	}
}
</script>

        <?php echo $this->fetch('library/pages.lbi'); ?> 

    </div>

    <div class="express_box" id="invoice" style="display:none;"> </div>

     

    

    <div id="qr_code" class="qr_code" style="display:none;" onclick="document.getElementById('qr_code').style.display='none';">

        <div class="qrcode" id="qrcode"></div>

    </div>
    <?php echo $this->smarty_insert_scripts(array('files'=>'qrcode.js')); ?>
    <script>
    var root = 'http://<?php echo $this->_var['root']; ?>';
    var qrcode = new QRCode(document.getElementById("qrcode"), '');
    function showCode(order_id){
        document.getElementById('qr_code').style.display='';
        qrcode.clear();
        qrcode.makeCode(root+"/handle.php?order_id="+parseInt(order_id));
    }

    </script> 

     
    <?php endif; ?>
    
    <?php if ($this->_var['action'] == 'team_list'): ?>

    <div class="nav_fixed nav_spike">
        <a href="user.php?act=team_list"<?php if ($this->_var['composite_status'] == ''): ?> class="cur"<?php endif; ?>><span>全部</span></a>
        <a href="user.php?act=team_list&composite_status=100"<?php if ($this->_var['composite_status'] == 100): ?> class="cur"<?php endif; ?>><span>组团中</span></a>
        <a href="user.php?act=team_list&composite_status=120"<?php if ($this->_var['composite_status'] == 120): ?> class="cur"<?php endif; ?>><span>已成团</span></a>
        <a href="user.php?act=team_list&composite_status=999"<?php if ($this->_var['composite_status'] == 999): ?> class="cur"<?php endif; ?>><span>组团失败</span></a>
    </div>
        <?php if ($this->_var['orders']): ?> 
		<div class="mt_order" style="padding-top:50px;">
        <?php $_from = $this->_var['orders']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'item');if (count($_from)):
    foreach ($_from AS $this->_var['item']):
?>
        <div class="mt_g">
            <?php $_from = $this->_var['item']['goods_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'goods');$this->_foreach['foreach_goods_list'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['foreach_goods_list']['total'] > 0):
    foreach ($_from AS $this->_var['goods']):
        $this->_foreach['foreach_goods_list']['iteration']++;
?>
            <div class="mt_g_img" ><a href="goods.php?id=<?php echo $this->_var['goods']['goods_id']; ?>"><img src="<?php echo $this->_var['goods']['goods_thumb']; ?>"></a></div>
            <div class="mt_g_info" >
                <p class="mt_g_name"><?php echo $this->_var['goods']['goods_name']; ?></p>
                <p class="mt_g_price"><b>¥<?php echo $this->_var['goods']['goods_price_fmt']; ?></b>/<?php echo $this->_var['item']['team_num']; ?>人团<span><?php echo $this->_var['item']['team_status']; ?></span></p>
            </div>
            <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
        </div>
        <div class="mt_status">
            <a href="user.php?act=order_detail&order_id=<?php echo $this->_var['item']['order_id']; ?>"> 订单详情 </a>
            <?php if ($this->_var['item']['luckdraw_id']): ?>
            	<a href="share.php?team_sign=<?php echo $this->_var['item']['team_sign']; ?>&uid=<?php echo $this->_var['uid']; ?>&luckdraw_id=<?php echo $this->_var['item']['luckdraw_id']; ?>"> 团详情 </a>
            <?php else: ?>
           		<a href="share.php?team_sign=<?php echo $this->_var['item']['team_sign']; ?>&uid=<?php echo $this->_var['uid']; ?>"> 团详情 </a>
            <?php endif; ?>
            
			<?php if ($this->_var['item']['square'] == 1): ?>
            <a href="javascript:void(0);" class="mt_status_lk1 marg_right qfabu" data-id="<?php echo $this->_var['goods']['goods_id']; ?>" data-luckdraw_id="<?php echo $this->_var['item']['luckdraw_id']; ?>" data-order_id="<?php echo $this->_var['item']['order_id']; ?>">发布</a>
            <?php endif; ?>
        </div>
        
        <div class="blank"></div>
        <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?> 
		</div>
        <?php echo $this->fetch('library/pages.lbi'); ?> 
        <?php else: ?> 
        <div class="nothing">
            <i class="iconfont icon-pintuan"></i>
            <p><a href="tuan.php">您还没有参加过任何团哦，赶快去火拼吧！</a></p>
        </div>
        <?php endif; ?> 
<div class="common">
    <a href="javascript:void(0);" class="close"></a>
    <div class="common-title">发布广场</div>
    <div class="common-content">
        <p><textarea id="square"></textarea></p>
        <p><input id="btn-square" type="submit"  value="发布"></p>
    </div>
</div>
<div class="common-bg"></div>
<script>
var stars = 0;
var goods_id = 0;
var order_id = 0;
var luckdraw_id = 0;
$(function () {
    H_login = {};
    H_login.openLogin = function(){
        $('.qfabu').click(function(){
            goods_id = $(this).data('id');
            order_id = $(this).data('order_id');
            luckdraw_id = $(this).data('luckdraw_id');
            $("#comment").val('');
            $('.common').show();
            $('.common-bg').show();
        });
    };
    H_login.closeLogin = function(){
        $('.close').click(function(){
            $('.common').hide();
            $('.common-bg').hide();
        });
    };
    H_login.run = function () {
        this.closeLogin();
        this.openLogin();
    };
    H_login.run();

    $("#btn-square").click(function(event) {
       var square = $.trim($("#square").val());
       console.info(square)
       if (square.length>0) {
        $.ajax({
            url: 'square.php?act=create',
            type: 'POST',
            dataType: 'json',
            data: {square: square,order_id: order_id,luckdraw_id: luckdraw_id},
            success: function (res) {
                if(res.isError == 1){
                    //alert(res.message)
					layer.open({
		                content: res.message,
			            btn: ['嗯']
		            });
                }
                else{
                    //alert('发布成功！')
					layer.open({
		                content: '发布成功！',
			            btn: ['嗯']
		            });
                    location.reload();
                }
            }
        })
        
       }
    });
});

</script>
    <?php endif; ?> 

    <?php if ($this->_var['action'] == order_detail): ?>
    <div class="<?php if ($this->_var['order']['shipping_status_cy'] == 2): ?>state state_3<?php elseif ($this->_var['order']['shipping_status_cy'] == 1): ?>state state_2<?php elseif ($this->_var['order']['pay_status_cy'] == 2): ?>state state_1<?php endif; ?>">
        <div class="state_step">
            <ul>
                <li class="state_step_1"></li>
                <li class="state_step_2"></li>
                <li class="state_step_3"></li>
            </ul>
            <span class="state_arrow"> <i class="state_arrow_i"></i> <i class="state_arrow_o"></i> </span>
        </div>
        <div class="address">
            <div class="address_row">
                <div class="address_tit">订单状态：</div>
                <div class="address_cnt"> <b><?php echo $this->_var['order']['order_status']; ?></b> </div>
            </div>
            <div class="address_row">
                <div class="address_tit">订单总额：</div>
                <div class="address_cnt"> <span class="address_price"><?php echo $this->_var['order']['formated_total_fee']; ?></span> （<?php echo $this->_var['order']['pay_name']; ?>）</div>
            </div>
            <div class="address_row">
                <div class="address_tit">订单编号：</div>
                <div class="address_cnt"><?php echo $this->_var['order']['order_sn']; ?></div>
            </div>
            <div class="address_row">
                <div class="address_tit">下单时间：</div>
                <div class="address_cnt"><?php echo $this->_var['order']['add_time']; ?></div>
            </div>
            <?php if ($this->_var['order']['shipping_id'] != $this->_var['notExpress']): ?>
            <div class="address_row">
                <div class="address_tit">收货地址：</div>
                <div class="address_cnt"><?php echo htmlspecialchars($this->_var['order']['province']); ?> <?php echo htmlspecialchars($this->_var['order']['city']); ?> <?php echo htmlspecialchars($this->_var['order']['district']); ?> <?php echo $this->_var['order']['address']; ?></div>
            </div>
            <div class="address_row">
                <div class="address_tit">收 货 人：</div>
                <div class="address_cnt"><?php echo htmlspecialchars($this->_var['order']['consignee']); ?> <?php echo htmlspecialchars($this->_var['order']['mobile']); ?></div>
            </div>
            <div class="address_row">
                <div class="address_tit">配送方式：</div>
                <div class="address_cnt"><?php echo $this->_var['order']['shipping_name']; ?><br><?php echo $this->_var['order']['invoice_no']; ?></div>
            </div>
            <?php endif; ?> 
            <?php if ($this->_var['order']['shipping_id'] == $this->_var['notExpress']): ?>
            <div class="address_row">
                <div class="address_tit">取货地址：</div>
                <div class="address_cnt"><?php echo $this->_var['order']['shipping_point']; ?></div>
            </div>
            <?php endif; ?>
        </div>
        <?php if (1): ?>
        <div class="state_btn"> <?php echo $this->_var['order']['handler']; ?> </div>
        <?php endif; ?>
        <div class="ptit">商品信息 </div>
        <div class="order order_height">
            <div class="order_bd">
                <div class="order_glist">
                    <div class="order_item"> 
                        <?php $_from = $this->_var['goods_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'goods');if (count($_from)):
    foreach ($_from AS $this->_var['goods']):
?> 
                        <div class="order_goods">
                            <div class="order_goods_img"><a href="goods.php?id=<?php echo $this->_var['goods']['goods_id']; ?>"><img src="<?php echo $this->_var['goods']['goods_thumb']; ?>"></a></div>
                            <div class="order_goods_info"> 
                                <div class="order_goods_name"><a href="goods.php?id=<?php echo $this->_var['goods']['goods_id']; ?>"><?php echo $this->_var['goods']['goods_name']; ?></a></div>
                                <div class="order_goods_attr">
                                    <div class="order_goods_attr_item">
									    <span>数量：<?php echo $this->_var['goods']['goods_number']; ?></span>
										<div class="order_goods_price"><?php if ($this->_var['order']['integral']): ?>积分兑换<?php else: ?><i>¥</i><?php echo $this->_var['goods']['goods_price']; ?><?php endif; ?></div>
                                    </div>
                                </div>
							</div>
						</div>
                        <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?> 
                    </div>
                    <?php if ($this->_var['order']['is_luck']): ?>
                    <div>夺宝号码： 
                        <?php $_from = $this->_var['luck_rows']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'luck');if (count($_from)):
    foreach ($_from AS $this->_var['luck']):
?>
                        <?php if ($this->_var['luck']['is_lucker'] == 1): ?> <font color="red">幸运号：<?php echo $this->_var['luck']['id']; ?></font> <?php else: ?> <font style="margin-left: 10px;"><?php echo $this->_var['luck']['id']; ?></font> <?php endif; ?>
                        <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                    </div>
                    <?php endif; ?>
                </div>
				<?php if ($this->_var['order']['team_sign'] && $this->_var['order']['team_status'] != 0 && $this->_var['order']['is_luck'] == 0): ?> 
                <div class="mt_status">
                	<?php if ($this->_var['order']['luckdraw_id']): ?>
                		<a href="share.php?team_sign=<?php echo $this->_var['order']['team_sign']; ?>&uid=<?php echo $this->_var['uid']; ?>&luckdraw_id=<?php echo $this->_var['order']['luckdraw_id']; ?>">查看团详情 </a>
                	<?php else: ?>
                    	<a href="share.php?team_sign=<?php echo $this->_var['order']['team_sign']; ?>&uid=<?php echo $this->_var['uid']; ?>">查看团详情 </a> 
                	<?php endif; ?>
                </div>
				<?php endif; ?> 
            </div>
        </div>
    </div>
    <div class="express_box" id="invoice" style="display:none;"> 
    </div>
    <?php endif; ?> 

    <?php if ($this->_var['action'] == team_detail): ?>

    <div class="mod_container">

        <div id="detailCon" class="wx_wrap">

            <div class="ptit">商品信息 </div>

            <div class="order order_height">

                <div class="order_bd">

                    <div class="order_glist">

                        <div class="order_item"> 

                            <?php $_from = $this->_var['goods_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'goods');if (count($_from)):
    foreach ($_from AS $this->_var['goods']):
?> 

                            <a href="goods.php?id=<?php echo $this->_var['goods']['goods_id']; ?>" class="order_goods" style="float:left;border-bottom:none;">

                            <div class="order_goods_img"> <img src="<?php echo $this->_var['goods']['goods_thumb']; ?>"> </div>

                            </a>

                            <div class="order_goods_info"> <a class="order_goods" href="goods.php?id=<?php echo $this->_var['goods']['goods_id']; ?>">

                                <div class="order_goods_name"><?php echo $this->_var['goods']['goods_name']; ?></div>

                                <div class="order_goods_attr"> <br>

                                    <div class="order_goods_attr_item">数量：<?php echo $this->_var['goods']['goods_number']; ?>

                                        <div class="order_goods_price"><i>¥</i><?php echo $this->_var['goods']['goods_price']; ?><i>/件</i></div>

                                    </div>

                                </div>

                                </a> </div>

                            <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?> 

                        </div>

                    </div>

                    <div> 

                        <?php $_from = $this->_var['team_mem']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'item');if (count($_from)):
    foreach ($_from AS $this->_var['item']):
?>

                        <div class="my_head" style="float:left;">

                            <div class="my_head_pic"> <img class="my_head_img" width="130" height="130" src="<?php echo $this->_var['item']['headimgurl']; ?>"> </div>

                        </div>

                        <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?> 

                    </div>

                    <div style="clear:both;"></div>

                    <div> <span id="time"></span> </div>

                    <div> 

                        <?php $_from = $this->_var['team_mem']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'item');if (count($_from)):
    foreach ($_from AS $this->_var['item']):
?> 

                        <?php if (($this->_foreach['name']['iteration'] - 1) == 0): ?> 

                        团长：<?php echo $this->_var['item']['user_name']; ?> 	开团时间：<?php echo $this->_var['item']['date']; ?> 

                        <?php else: ?> 

                        <?php echo $this->_var['item']['user_name']; ?> 	参团时间：<?php echo $this->_var['item']['date']; ?> 

                        <?php endif; ?> 

                        <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?> 

                    </div>

                    <?php if ($this->_var['order']['team_status'] == 1): ?> 

                    正在进行 

                    <?php elseif ($this->_var['order']['team_status'] == 2): ?> 

                    成功 

                    <?php elseif ($this->_var['order']['team_status'] == 3): ?> 

                    失败待退款 

                    <?php elseif ($this->_var['order']['team_status'] == 4): ?> 

                    失败退款成功 

                    <?php endif; ?>

                    <div id="handler"> 

                        <?php if ($this->_var['order']['team_status'] == 1): ?>

                        <button onclick="window.location='share.php?team_sign=<?php echo $this->_var['order']['team_sign']; ?>';">分享给朋友</button>

                        <?php else: ?>

                        <button type="button" onclick="window.location='index.php';">我也要开个团，点此到商品列表</button>

                        <?php endif; ?> 

                    </div>

                </div>

            </div>

        </div>

    </div>

    <script>

    var daysms = 24 * 60 * 60 * 1000;

    var hoursms = 60 * 60 * 1000;

    var Secondms = 60 * 1000;

    var microsecond = 1000;

    var DifferHour = 0;

    var DifferMinute = 0;

    var DifferSecond = 0;

    var systime=<?php echo $this->_var['systime']; ?>;

    var team_start=<?php echo $this->_var['team_start']; ?>*1000;

    var team_end=<?php echo $this->_var['team_start']; ?>*1000+<?php echo $this->_var['team_suc_time']; ?>*24*3600*1000;

    setInterval("systime_clock()",1000);

    function systime_clock(){

    	systime++;

    }

    function clock()

    {	

    	//当前时间

      var time = new Date();

      time.setTime(systime*1000);

      var Diffms = team_end - time.getTime();

      var Diffms1=Diffms;

      var a='';

      var b='';

      var c='';

      var d='';

      DifferHour = Math.floor(Diffms / daysms);

      Diffms -= DifferHour * daysms;

      DifferMinute = Math.floor(Diffms / hoursms);

      Diffms -= DifferMinute * hoursms;

      DifferSecond = Math.floor(Diffms / Secondms);

      Diffms -= DifferSecond * Secondms;

      var dShhs = Math.floor(Diffms / microsecond);

      if(Diffms1>=0){

    	   //a="还剩<strong class='tcd-h'>"+DifferHour+"</strong>天;";

    	   b="还剩<strong >"+DifferMinute+"</strong>时";

    	   c="<strong >"+DifferSecond+"</strong>分";

    	   d="<strong >"+dShhs+"</strong>秒";

    	  document.getElementById('time').innerHTML =b+c+d;

      }else{//已结束

    	  document.getElementById('handler').innerHTML="<button type='button' onclick='window.location=\'index.php\';'>我也要开个团，点此到商品列表</button>";

    	   document.getElementById('time').innerHTML ="<strong style='color:#999;'>已结束</strong>"

      }

    }

    window.setInterval("clock()", 1000);

    </script> 

    <?php endif; ?> 

</div>

<div class="blank"></div>
<?php echo $this->fetch('library/footer.lbi'); ?>

</body>
<script language="javascript">
	function cancel_invoice(){
		document.body.style.overflow = "";
		document.getElementById('invoice').style.display='none';
		document.getElementById('dealliststatus1').style.display='';
		document.getElementById("dealliststatus1").style.backgroundColor="";
		document.getElementById("dealliststatus1").style.opacity = '';
	}
    function get_invoice(expressid,expressno){	
    	document.getElementById("invoice").style.display="";
    	document.getElementById("invoice").innerHTML="<center>正在查询物流信息，请稍后...</center>";
    	if(document.getElementById("dealliststatus1")){
    		//document.getElementById("dealliststatus1").style.display="none";
    		
    		document.body.style.overflow = "hidden";
    		document.getElementById("dealliststatus1").style.backgroundColor="#EEEEEE";
    		document.getElementById("dealliststatus1").style.opacity = 50/100;
    		/**/
    	}
    	Ajax.call('/plugins/juhe/kuaidi.php?com='+ expressid+'&nu=' + expressno,'showtest=showtest', 
    			get_invoice_reponse, 'GET', 'JSON');
    }
	function get_invoice_reponse(result){ 
		document.getElementById("invoice").innerHTML=result;
	}
</script>
</html>



