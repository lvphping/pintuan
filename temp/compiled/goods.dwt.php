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
<link href="<?php echo $this->_var['hhs_css_path']; ?>/flexslider.css" rel="stylesheet" />

<?php echo $this->smarty_insert_scripts(array('files'=>'jquery.js,haohaios.js,jquery.flexslider-min.js')); ?>
</head>
<body>
<div class="container">
    <div class="flexslider">
        <ul class="slides">
            <?php $_from = $this->_var['pictures']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'picture');$this->_foreach['ptab'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['ptab']['total'] > 0):
    foreach ($_from AS $this->_var['picture']):
        $this->_foreach['ptab']['iteration']++;
?>
            <li><img src="<?php echo $this->_var['picture']['img_url']; ?>"/></li>
            <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
        </ul>
        <?php if ($this->_var['share_info']): ?>
        <div class="g_fx">
            <div class="g_fx_bg"></div>
            <div class="g_fx_con"><img src="<?php echo $this->_var['share_info']['headimgurl']; ?>"/>来自"<?php echo $this->_var['share_info']['user_name']; ?>"的分享</div>
        </div>
        <?php endif; ?> </div>
    <script type="text/javascript">
        $(function() {
            $('.flexslider').flexslider({
                animation: "slide",
                slideDirection: "horizontal"
            });
        });
    </script> 
    <?php if ($this->_var['goods']['is_miao'] == 1): ?>
    <?php if ($this->_var['goods']['promote_end_date'] > $this->_var['now_time'] && $this->_var['goods']['promote_start_date'] < $this->_var['now_time']): ?>
    <?php echo $this->smarty_insert_scripts(array('files'=>'lefttime.js')); ?>
    <div class="lefttime">
        <div class="g_fx_bg"></div>
        <font id="leftTime"><?php echo $this->_var['lang']['please_waiting']; ?></font> </div>
    <?php endif; ?>
    <?php endif; ?>
    <div class="tuan_info">
        <form action="javascript:addToCart(<?php echo $this->_var['goods']['goods_id']; ?>)" method="post" name="HHS_FORMBUY" id="HHS_FORMBUY" >
            <div class="g_name"<?php if ($this->_var['goods']['allow_fenxiao']): ?> style="padding-right:70px;"<?php endif; ?>> <?php echo $this->_var['goods']['goods_name']; ?>
                <?php if ($this->_var['goods']['allow_fenxiao']): ?><a id="share_button" href="javascript:void(0);" onclick="document.getElementById('share_img').style.display='';" class="fx_btn"><em></em><i class="fa fa-sitemap"></i><font>分销</font></a><?php endif; ?> </div>
            <div class="g_brief"><?php echo $this->_var['goods']['goods_brief']; ?></div>
            <div class="blank"></div>
            <?php if ($this->_var['goods']['shipping_fee_new']): ?><div class="" style="color: #FF4370;">（满<b style="padding: 0 5px;"><?php echo $this->_var['goods']['shipping_fee_new']; ?></b>包邮）</div><?php endif; ?>
            <?php if ($this->_var['goods']['is_team']): ?>
            <div class="g_price"><font>¥<?php echo $this->_var['goods']['team_price']; ?></font> <del>¥<?php echo $this->_var['goods']['market_price']; ?></del> <span>已售：<?php echo $this->_var['buy_num']; ?><?php if ($this->_var['goods']['guige']): ?><?php echo $this->_var['goods']['guige']; ?><?php else: ?>件<?php endif; ?></span></div>
            <?php elseif ($this->_var['goods']['is_zero']): ?>
            <div class="g_price"><font>¥<?php echo $this->_var['goods']['shop_price']; ?></font> <del>市场价：¥<?php echo $this->_var['goods']['market_price']; ?></del> <span>已领取：<?php echo $this->_var['buy_num']; ?><?php if ($this->_var['goods']['guige']): ?><?php echo $this->_var['goods']['guige']; ?><?php else: ?>件<?php endif; ?></span></div>
            <div class="g_price">剩余：<?php echo $this->_var['goods']['goods_number']; ?><?php if ($this->_var['goods']['is_zero']): ?><span>运费：¥<?php echo $this->_var['goods']['shipping_fee']; ?></span><?php endif; ?></div>
			<?php elseif ($this->_var['goods']['is_miao']): ?>
            <div class="g_price"><font>¥<b><?php if ($this->_var['goods']['promote_price'] > 0): ?><?php echo $this->_var['goods']['promote_price']; ?><?php else: ?><?php echo $this->_var['goods']['shop_price']; ?><?php endif; ?></b></font> <del>市场价：¥<?php echo $this->_var['goods']['market_price']; ?></del> <span>已售：<?php echo $this->_var['buy_num']; ?><?php if ($this->_var['goods']['guige']): ?><?php echo $this->_var['goods']['guige']; ?><?php else: ?>件<?php endif; ?></span></div>
            <?php else: ?>
            <div class="g_price"><font>¥<b><?php echo $this->_var['goods']['shop_price']; ?></b></font> <del>市场价：¥<?php echo $this->_var['goods']['market_price']; ?></del> <span>已售：<?php echo $this->_var['buy_num']; ?><?php if ($this->_var['goods']['guige']): ?><?php echo $this->_var['goods']['guige']; ?><?php else: ?>件<?php endif; ?></span></div>
            <?php endif; ?>
            <div class="blank"></div>
            <div class="line"></div>
            <div class="blank"></div>
            <div class="td2_info"> 
                <?php if ($this->_var['goods']['lab_qgby']): ?>
                    <p><?php echo $this->_var['goods']['lab_qgby']; ?></p>
                <?php endif; ?>
                <?php if ($this->_var['goods']['lab_zpbz']): ?>
                    <p><?php echo $this->_var['goods']['lab_zpbz']; ?></p>
                <?php endif; ?>
                <?php if ($this->_var['goods']['lab_qtth']): ?>
                    <p><?php echo $this->_var['goods']['lab_qtth']; ?></p>
                <?php endif; ?>
                <?php if ($this->_var['goods']['lab_jkbs']): ?>
                    <p><?php echo $this->_var['goods']['lab_jkbs']; ?></p>
                <?php endif; ?> 
                <?php if ($this->_var['goods']['lab_hwzy']): ?>
                    <p><?php echo $this->_var['goods']['lab_hwzy']; ?></p>
                <?php endif; ?> 
            </div>
                
            <div id="speBg" style="display:none; z-index: 90000;"></div>
            <div id="speDiv" class="speDiv" style="bottom:50px; display:none;">
                <div id="sku-head"> <img id="sku-image" class="image" src="<?php echo $this->_var['goods']['goods_thumb']; ?>">
                    <div id="sku-detail">
                        <div class="sku-name"><?php echo $this->_var['goods']['goods_name']; ?></span></div>
                        <div class="sku-price2-depends" id="HHS_GOODS_AMOUNT"></div>
                        
                        <?php if ($this->_var['goods']['limit_buy_bumber'] != 1 || $this->_var['goods']['is_zero'] != 1): ?>
                        <div><span id="sku-msg"  class="<?php if ($this->_var['goods']['limit_buy_bumber'] == 1): ?> limit_hide <?php endif; ?>">请选择商品<?php if ($this->_var['specification']): ?>属性<?php endif; ?><?php if ($this->_var['goods']['is_zero'] != 1): ?>数量<?php endif; ?></span><?php if ($this->_var['goods']['limit_buy_bumber'] == 1): ?><b class="limit_one">该商品限购1件</b><?php endif; ?></div>
                        <?php endif; ?>
                        </div>
                        <a href="javascript:showhide();" id="sku-quit"></a>
                </div>
                <?php if ($this->_var['specification']): ?>
                <div class="sku-info"> <?php $_from = $this->_var['specification']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('spec_key', 'spec');if (count($_from)):
    foreach ($_from AS $this->_var['spec_key'] => $this->_var['spec']):
?>
                    <div class="sku-type"><?php echo $this->_var['spec']['name']; ?></div>
                    <?php $_from = $this->_var['spec']['values']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'value');if (count($_from)):
    foreach ($_from AS $this->_var['key'] => $this->_var['value']):
?>
                    <input class="goods" id="spec_value_<?php echo $this->_var['value']['id']; ?>" type="radio" name="spec_<?php echo $this->_var['spec_key']; ?>" value="<?php echo $this->_var['value']['id']; ?>" <?php if ($this->_var['key'] == 0): ?>checked<?php endif; ?> onclick="changePrice()" />
                    <label for="spec_value_<?php echo $this->_var['value']['id']; ?>"><?php echo $this->_var['value']['label']; ?></label>
                    <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                    <input type="hidden" name="spec_list" value="<?php echo $this->_var['key']; ?>" />
                    <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?> </div>
                <?php endif; ?>
                <?php if ($this->_var['goods']['limit_buy_bumber'] == 1 || $this->_var['goods']['is_zero'] == 1): ?>
                <input name="number"  type="hidden" id="number" class="text" value="1"/>

                <?php elseif ($this->_var['goods']['limit_buy_bumber'] == 0): ?>
                <div class="sku-amount">
                    <div class="sku-text"> <a>购买数量</a>
                        <div class="nbox">
                            <i class="fa fa-minus hui" onclick="goods_cut();changePrice()"></i>
                            <input name="number" type="text" id="number" class="num" value="1" size="4" onblur="changePrice();"/>
                            <i class="fa fa-plus" onclick="goods_add();changePrice()"></i>
                        </div>
                        <!--div class="sku-buy-amount-increase" onclick="goods_add();changePrice()"><span>+</span></div>
                        <input name="number" type="text" id="number" class="sku-input-text" value="1" size="4" onblur="changePrice();"/>
                        <div class="sku-buy-amount-reduce" onclick="goods_cut();changePrice()"><span>−</span></div-->
                    </div>
                </div>
                <?php else: ?>
                <div class="sku-amount">
                    <div class="sku-text"> <a>购买数量</a>
                        <div class="sku-buy-amount-increase" onclick="goods_add();changePrice()"><span>+</span></div>
                        <input name="number" type="text" id="number" class="sku-input-text" value="1" size="4" onblur="changePrice();"/>
                        <div class="sku-buy-amount-reduce" onclick="goods_cut();changePrice()"><span>−</span></div>
                        <span>限购<?php echo $this->_var['goods']['limit_buy_bumber']; ?><?php if ($this->_var['goods']['guige']): ?><?php echo $this->_var['goods']['guige']; ?><?php else: ?>件<?php endif; ?></span> </div>
                </div>
                <?php endif; ?> </div>
            <div class="ftbuy"> <a href="index.php" class="ftbuy_index">
                <div class="ftbuy_index_img"> <img src="themes/haohainew/images/index-38d3d45c2c.png"> </div>
                <div class="ftbuy_index_text">首页</div>
                </a>
				<a href="javascript:;" class="ftbuy_like">
                <div class="ftbuy_index_img">
				    <?php if ($this->_var['rec_id'] > 0): ?>
                    <div class="ftbuy_index_img_bg" data-id="<?php echo $this->_var['goods']['goods_id']; ?>"><img src="themes/haohainew/images/is_liked2.png" data-isLiked="1"></div>
					<?php else: ?>
					<div class="ftbuy_index_img_bg" data-id="<?php echo $this->_var['goods']['goods_id']; ?>"><img src="themes/haohainew/images/no_liked2.png" data-isLiked="0"></div>
					<?php endif; ?>
                </div>
                <div class="ftbuy_index_text">收藏</div>
                </a> 
				<a class="ftbuy_message" target="_blank" href="tel:<?php echo $this->_var['service_phone']; ?>">
                <div class="ftbuy_message_img"> </div>
                <div class="ftbuy_message_text">客服</div>
                </a> 
                
                <?php if ($this->_var['goods']['is_zero'] == 1 || $this->_var['goods']['is_team'] == 0): ?> 
                	<?php if ($this->_var['goods']['is_miao'] == 1): ?>
                		<?php if ($this->_var['goods']['promote_start_date'] > $this->_var['now_time']): ?>
                         <a href="javascript:;" id="btn-pre-buy" class="ftbuy_item out hui" style="width:60%; line-height:50px; font-size:16px;"> 尚未开始 </a> 
						<?php elseif ($this->_var['goods']['promote_end_date'] > $this->_var['now_time']): ?>
                        <a href="javascript:showhide(1);" id="btn-pre-buy1" class="ftbuy_item out" style="width:60%; line-height:50px; font-size:16px;">马上购买 </a> 
                        <a href="javascript:addToCart(<?php echo $this->_var['goods']['goods_id']; ?>)" id="btn-pre-buy1" class="ftbuy_item out" style="width:60%; line-height:50px; font-size:16px;"> 确定 </a> 
						<?php else: ?>
                        <a href="javascript:;" id="btn-pre-buy1" class="ftbuy_item out hui" style="width:60%; line-height:50px; font-size:16px;"> 已经结束 </a> 
                        <?php endif; ?>
                
               		<?php else: ?>
               			 <?php if ($this->_var['goods']['is_zero']): ?>
               				  <a href="javascript:showhide();" id="btn-pre-buy" class="ftbuy_item out" style="width:60%; line-height:50px; font-size:16px;"> 立即领取 </a> <a href="javascript:addToCart(<?php echo $this->_var['goods']['goods_id']; ?>,0,0,5)" id="btn-buy" class="ftbuy_item out" style="width:60%; line-height:50px; font-size:16px;"> 立即领取 </a>
                         <?php else: ?>
                          <a href="javascript:showhide();" id="btn-pre-buy" class="ftbuy_item out" style="width:60%; line-height:50px; font-size:16px;"> 马上购买 </a> 
                          <a href="javascript:addToCart(<?php echo $this->_var['goods']['goods_id']; ?>)" id="btn-buy" class="ftbuy_item out" style="width:60%; line-height:50px; font-size:16px;"> 确定 </a> 
                          <?php endif; ?> 
                    <?php endif; ?>
              <?php else: ?> 
                <?php if ($this->_var['bonus_free_all'] > 0): ?>
                <?php if ($this->_var['goods']['bonus_free_all'] > 0): ?>
                 <a href="javascript:addToCart(<?php echo $this->_var['goods']['goods_id']; ?>,0,0,5)" class="ftbuy_item out" style="width:62%; line-height:50px; font-size:16px;"> 团长免单 </a> <?php endif; ?>
                <?php else: ?> 
                <a href="javascript:showhide(1);" id="btn-pre-buy1" class="ftbuy_item out">
                <div class="ftbuy_price"><b id="tuan_more_price">¥&nbsp;<?php echo $this->_var['goods']['team_price']; ?></b><i>/</i><?php if ($this->_var['goods']['guige']): ?><?php echo $this->_var['goods']['guige']; ?><?php else: ?>件<?php endif; ?></div>
                <div class="ftbuy_btn"><b id="tuan_more_number"><?php echo $this->_var['goods']['team_num']; ?>人团</b></div>
                </a> <a href="javascript:addToCart(<?php echo $this->_var['goods']['goods_id']; ?>,0,0,5)" id="btn-buy1" class="ftbuy_item out" style="display:none;">
                <div class="ftbuy_btn" id="tuan_one_number" style="height:50px;top: 0;line-height:50px; font-size:16px;">确定</div>
                </a> 
                <?php endif; ?>
                <a href="javascript:showhide();" id="btn-pre-buy" class="ftbuy_item ftbuy_item_buy"<?php if ($this->_var['bonus_free_all'] > 0): ?><?php if ($this->_var['goods']['bonus_free_all'] > 0): ?> style="display:none;"<?php endif; ?><?php endif; ?>>
                <div class="ftbuy_price">
                    <div><b id="tuan_one_price">¥&nbsp;<?php echo $this->_var['goods']['shop_price']; ?></b><i>/</i><?php if ($this->_var['goods']['guige']): ?><?php echo $this->_var['goods']['guige']; ?><?php else: ?>件<?php endif; ?></div>
                </div>
                <div class="ftbuy_btn" id="tuan_one_number">单独购买</div>
                </a> <a href="javascript:addToCart(<?php echo $this->_var['goods']['goods_id']; ?>);" id="btn-buy" class="ftbuy_item ftbuy_item_buy" style="display:none;">
                <div class="ftbuy_btn" id="tuan_one_number" style="height:50px;top: 0;line-height:50px; font-size:16px;">确定</div>
                </a>
                </li>
              <?php endif; ?> 
            </div>
        </form>
    </div>
    <?php if ($this->_var['goods']['suppliers_id']): ?>
    <div class="blank"></div>
    <div class="mall_goods"> <a href="store.php?id=<?php echo $this->_var['goods']['suppliers_id']; ?>">
        <div class="mall_img"><img src="<?php echo $this->_var['stores_info']['supp_logo']; ?>"></div>
        <div class="mall_sub">
            <h3><?php echo $this->_var['stores_info']['suppliers_name']; ?></h3>
            <p>商品:<?php echo $this->_var['stores_info']['goods_num']; ?>  销量:<?php echo $this->_var['stores_info']['sales_num']; ?></p>
            </span> </div>
        <div class="enter_button"> <span><img src="themes/haohainew/images/mall_icon.png"></span> <em>进入店铺</em> </div>
        </a> </div>
    <?php endif; ?>        
    
    <?php if ($this->_var['goods']['is_team']): ?>
    <div class="g_tip">支付开团并邀请好友参团，未成团自动退款<a href="tuan_rule.php">开团介绍</a></div>
    <?php endif; ?> 
    <?php if ($this->_var['goods']['is_nearby']): ?> 
    <?php if ($this->_var['group_list']): ?>
<script>
var Tday = new Array();
var daysms = 24 * 60 * 60 * 1000
var hoursms = 60 * 60 * 1000
var Secondms = 60 * 1000
var microsecond = 1000
var DifferHour = -1
var DifferMinute = -1
var DifferSecond = -1
function clock(key)
  {
   var time = new Date()
   var hour = time.getHours()
   var minute = time.getMinutes()
   var second = time.getSeconds()
   var timevalue = ""+((hour > 12) ? hour-12:hour)
   timevalue +=((minute < 10) ? ":0":":")+minute
   timevalue +=((second < 10) ? ":0":":")+second
   timevalue +=((hour >12 ) ? " PM":" AM")
   var convertHour = DifferHour
   var convertMinute = DifferMinute
   var convertSecond = DifferSecond
   var Diffms = Tday[key].getTime() - time.getTime()
   DifferHour = Math.floor(Diffms / daysms)
   Diffms -= DifferHour * daysms
   DifferMinute = Math.floor(Diffms / hoursms)
   Diffms -= DifferMinute * hoursms
   DifferSecond = Math.floor(Diffms / Secondms)
   Diffms -= DifferSecond * Secondms
   var dSecs = Math.floor(Diffms / microsecond)
  
   if(convertHour != DifferHour) a="<b>"+DifferHour+"</b>天";
   if(convertMinute != DifferMinute) b="<b>"+DifferMinute+"</b>时";
   if(convertSecond != DifferSecond) c="<b>"+DifferSecond+"</b>分"
     d="<b>"+dSecs+"</b>秒"
     if (DifferHour>0) {a=a}
     else {a=''}
   document.getElementById("leftTime"+key).innerHTML =a + b + c + d; //显示倒计时信息
 
  }
</script>
    <div id="more_tuan">
        <div class="ht">
            <div class="ht_tit">小伙伴正在发起的团购，您可以直接参与</div>
            <div class="ht_list" id="near_team"> 
                <?php $_from = $this->_var['group_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'group');$this->_foreach['name'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['name']['total'] > 0):
    foreach ($_from AS $this->_var['key'] => $this->_var['group']):
        $this->_foreach['name']['iteration']++;
?>
                <div class="ht_item" onclick="location.href='share.php?team_sign=<?php echo $this->_var['group']['team_sign']; ?>';">
                    <div class="ht_avatar"> <img src="<?php echo $this->_var['group']['headimgurl']; ?>" alt="团长头像"> </div>
                    <div class="ht_info">
                        <div class="ht_name"><?php echo $this->_var['group']['uname']; ?></div>
                        <!--div class="ht_time"><?php echo $this->_var['group']['finish_str']; ?></div-->
						<div class="ht_time">剩余 <font id="leftTime<?php echo $this->_var['key']; ?>"><?php echo $this->_var['lang']['please_waiting']; ?></font></div>
                    </div>
                    <a href="javascript:;" class="ht_btn"> <span class="ht_price"><i>￥</i><?php echo $this->_var['goods']['team_price']; ?> / <?php if ($this->_var['goods']['guige']): ?><?php echo $this->_var['goods']['guige']; ?><?php else: ?>件<?php endif; ?></span> <span class="ht_btn_go">还差<?php echo $this->_var['group']['progress']; ?>人成团，去参团</span> </a>
				</div>
<script>
Tday[<?php echo $this->_var['key']; ?>] = new Date("<?php echo $this->_var['group']['times']; ?>");  
window.setInterval(function()    
{clock(<?php echo $this->_var['key']; ?>);}, 1000);    
</script>
                <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?> 
            </div>
        </div>
    </div>
    <?php endif; ?> 
    <?php endif; ?> 
    
    <?php if ($this->_var['comments']): ?>
    <div class="detail-comments-container">
        <div class="detail-comments-title">
            <div class="detail-comments-all">用户评价</div>
            <img class="detail-comments-arrow" src="themes/haohainew/images/personal_arrow-dd13467d78.png">
            <div class="detail-comments-amount"> <a href="comments.php?id=<?php echo $this->_var['goods']['goods_id']; ?>">共<span><?php echo $this->_var['comments_nums']; ?></span>条评论</a> </div>
        </div>
        <div class="goods-comments-list"> <?php $_from = $this->_var['comments']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'item');if (count($_from)):
    foreach ($_from AS $this->_var['item']):
?>
            <div class="goods-comments-detail"> <img class="goods-comments-avatar" src="<?php echo $this->_var['item']['headimgurl']; ?>">
                <div class="goods-comments-name"><?php echo $this->_var['item']['username']; ?></div>
                <div class="goods-comments-time"><?php echo $this->_var['item']['add_time']; ?></div>
                <div class="goods-comments-stars"> <?php if ($this->_var['item']['rank'] == 1): ?>
                    <div class="goods-comments-stars-show"></div>
                    <?php elseif ($this->_var['item']['rank'] == 2): ?>
                    <div class="goods-comments-stars-show"></div>
                    <div class="goods-comments-stars-show"></div>
                    <?php elseif ($this->_var['item']['rank'] == 3): ?>
                    <div class="goods-comments-stars-show"></div>
                    <div class="goods-comments-stars-show"></div>
                    <div class="goods-comments-stars-show"></div>
                    <?php elseif ($this->_var['item']['rank'] == 4): ?>
                    <div class="goods-comments-stars-show"></div>
                    <div class="goods-comments-stars-show"></div>
                    <div class="goods-comments-stars-show"></div>
                    <div class="goods-comments-stars-show"></div>
                    <?php elseif ($this->_var['item']['rank'] == 5): ?>
                    <div class="goods-comments-stars-show"></div>
                    <div class="goods-comments-stars-show"></div>
                    <div class="goods-comments-stars-show"></div>
                    <div class="goods-comments-stars-show"></div>
                    <div class="goods-comments-stars-show"></div>
                    <?php endif; ?> </div>
                <div class="goods-comments-content"><em></em><p><?php echo $this->_var['item']['content']; ?></p><?php if ($this->_var['item']['re_content']): ?><p class="re_content"><?php echo $this->_var['item']['re_username']; ?>回复：<?php echo $this->_var['item']['re_content']; ?></p><?php endif; ?></div>
            </div>
            <?php endforeach; else: ?>
            暂无评论
            <?php endif; unset($_from); ?><?php $this->pop_vars();; ?> </div>
    </div>
    <?php endif; ?>
    <div class="blank"></div>
    <div class="pro_detial">
        <div class="pro_con"> <?php echo $this->_var['goods']['goods_desc']; ?> </div>
    </div>
    <div class="recommend_grid_wrap">
        <div id="recommend" class="grid">
            <div class="recommend_head">你可能还喜欢</div>
            <div class="bd">
                <ul>
                    <?php $_from = $this->_var['rands_goods']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'goods_0_01753800_1498473730');if (count($_from)):
    foreach ($_from AS $this->_var['goods_0_01753800_1498473730']):
?>
                    <li>
                        <div class="recommend_img"><a href="goods.php?id=<?php echo $this->_var['goods_0_01753800_1498473730']['goods_id']; ?>"><img  src="<?php if ($this->_var['goods_0_01753800_1498473730']['goods_img']): ?><?php echo $this->_var['goods_0_01753800_1498473730']['goods_img']; ?><?php else: ?>images/no_picture.gif<?php endif; ?>"></a></div>
                        <div class="recommend_title"><a href="goods.php?id=<?php echo $this->_var['goods_0_01753800_1498473730']['goods_id']; ?>"><?php echo $this->_var['goods_0_01753800_1498473730']['goods_name']; ?></a></div>
                        <div class="recommend_price">￥<span><?php echo $this->_var['goods_0_01753800_1498473730']['goods_price']; ?></span></div>
                        <?php if ($this->_var['goods_0_01753800_1498473730']['rec_id'] > 0): ?>
                        <div class="like_click" data-id="<?php echo $this->_var['goods_0_01753800_1498473730']['goods_id']; ?>"><img src="themes/haohainew/images/is_liked2.png" data-isLiked="1"></div>
                        <?php else: ?>
                        <div class="like_click" data-id="<?php echo $this->_var['goods_0_01753800_1498473730']['goods_id']; ?>"> <img src="themes/haohainew/images/no_liked2.png" data-isLiked="0"></div>
                        <?php endif; ?>
                    </li>
                    <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                </ul>
            </div>
        </div>
        <div class="recommend_bottom">
            <div class="line"></div>
            <p>已经到底部了</p>
        </div>
    </div>
    <div class="blank"></div>
    <div id="share_img" class="share_img" style="display:none;" onclick="document.getElementById('share_img').style.display='none';">
        <p><img class="arrow" src="themes/haohainew/images/share.png" ></p>
        <p style="margin-top:30px; margin-right:50px;">点击右上角，</p>
        <p style="margin-right:50px;">将此商品分享给好友</p>
    </div>
</div>
<a href="flows.php?step=cart" class="gw-cart-p" id="cat"><i class="iconfont-cart"></i> <span id="HHS_CARTINFO" class="gw-cart"><?php 
$k = array (
  'name' => 'cart_num',
);
echo $this->_echash . $k['name'] . '|' . serialize($k) . $this->_echash;
?></span> </a>
<div class="back-top"><span uigs="wap_to_top">顶部</span></div>
</body>
<script type="text/javascript">
var fuck_team = true;
var goods_id = <?php echo $this->_var['goods_id']; ?>;
var goodsattr_style = <?php echo empty($this->_var['cfg']['goodsattr_style']) ? '1' : $this->_var['cfg']['goodsattr_style']; ?>;
var gmt_end_time = <?php echo empty($this->_var['promote_end_time']) ? '0' : $this->_var['promote_end_time']; ?>;
<?php $_from = $this->_var['lang']['goods_js']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'item');if (count($_from)):
    foreach ($_from AS $this->_var['key'] => $this->_var['item']):
?>
var <?php echo $this->_var['key']; ?> = "<?php echo $this->_var['item']; ?>";
<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
var goodsId = <?php echo $this->_var['goods_id']; ?>;
var now_time = <?php echo $this->_var['now_time']; ?>;

onload = function(){
  changePrice();
  try {onload_leftTime();}
  catch (e) {}
}
function goods_cut(){
	var num_val=document.getElementById('number');
	var new_num=num_val.value;
	 if(isNaN(new_num)){alert('请输入数字');return false}
	var Num = parseInt(new_num);
	if(Num>1)Num=Num-1;
	if(Num==1){
	    $(".fa-minus").addClass("hui");
	}
	num_val.value=Num;
}
function goods_add(){
	var num_val=document.getElementById('number');
	var new_num=num_val.value;
	 if(isNaN(new_num)){alert('请输入数字');return false}
	var Num = parseInt(new_num);
	Num=Num+1;
	if(Num>1){
	    $(".fa-minus").removeClass("hui");
	}
	num_val.value=Num;
}
function changeAtt(t) {
t.lastChild.checked='checked';
for (var i = 0; i<t.parentNode.childNodes.length;i++) {
        if (t.parentNode.childNodes[i].className == 'cattsel') {
            t.parentNode.childNodes[i].className = '';
        }
    }
t.className = "cattsel";
changePrice();
}
function changePrice()
{
  var attr = getSelectedAttributes(document.forms['HHS_FORMBUY']);
 
  var qty = document.forms['HHS_FORMBUY'].elements['number'].value;
  Ajax.call('goods.php', 'act=price&id=' + goodsId + '&attr=' + attr + '&number=' + qty, changePriceResponse, 'GET', 'JSON');
}

function changePriceResponse(res)
{
  if (res.err_msg.length > 0)
  {
    //alert(res.err_msg);
	layer.open({
		    content: res.err_msg,
			btn: ['嗯']
		});
	document.forms['HHS_FORMBUY'].elements['number'].value = res.number;
  }
  else
  {
	 
	 if(res.team_price ==undefined)
	 {
		 res.team_price='';
	 }
	
    document.forms['HHS_FORMBUY'].elements['number'].value = res.qty;
    var fuck_price = fuck_team !== true ? res.result : res.team_price;
    if (document.getElementById('HHS_GOODS_AMOUNT')){
      document.getElementById('HHS_GOODS_AMOUNT').innerHTML = '￥'+fuck_price;
    }
  }
}



		var user_id = <?php echo $this->_var['uid']; ?>;
		
        $(".ftbuy_index_img_bg").on("click", function(e) {
        e.preventDefault();
        var goodsId = $(this).attr("data-id");
        var img = $(this).find("img");
        if (img.attr("data-isLiked") == 1) {
            $.get('user.php', {
                act: "del_collection",
                collection_id: goodsId,
                user_id: user_id
            }).done(function(e) {
                img.attr("src", "themes/haohainew/images/no_liked2.png");
                img.attr("data-isLiked", 0)
            })
        } else {
            $.get('user.php', {
                act: "collect",
                id: goodsId,
                user_id: user_id
            }).done(function(e) {
                img.attr("src", "themes/haohainew/images/is_liked2.png");
                img.attr("data-isLiked", 1)
            })

        }
        })	

        $(".like_click").on("click", function(e) {
        e.preventDefault();
        var goodsId = $(this).attr("data-id");
        var img = $(this).find("img");
        if (img.attr("data-isLiked") == 1) {
            $.get('user.php', {
                act: "del_collection",
                collection_id: goodsId,
                user_id: user_id
            }).done(function(e) {
                img.attr("src", "themes/haohainew/images/no_liked2.png");
                img.attr("data-isLiked", 0)
            })
        } else {
            $.get('user.php', {
                act: "collect",
                id: goodsId,
                user_id: user_id
            }).done(function(e) {
                img.attr("src", "themes/haohainew/images/is_liked2.png");
                img.attr("data-isLiked", 1)
            })

        }
        })		
		
</script>
<script type="text/javascript" src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
<script language="javascript" type="text/javascript">

	wx.config({
	    debug: false,//这里是开启测试，如果设置为true，则打开每个步骤，都会有提示，是否成功或者失败
	    appId: '<?php echo $this->_var['appid']; ?>',
	    timestamp: '<?php echo $this->_var['timestamp']; ?>',//这个一定要与上面的php代码里的一样。
	    nonceStr: '<?php echo $this->_var['timestamp']; ?>',//这个一定要与上面的php代码里的一样。
	    signature: '<?php echo $this->_var['signature']; ?>',
	    jsApiList: [
	      // 所有要调用的 API 都要加到这个列表中
	        'onMenuShareTimeline',
	        'onMenuShareAppMessage',
	        'onMenuShareQQ',
	        'onMenuShareWeibo',
	        'checkJsApi',
	        'openLocation',
	        'getLocation'
	    ]
	});
	
	var title="<?php echo $this->_var['title']; ?>";
	var link= "<?php echo $this->_var['link']; ?>";
	var imgUrl="<?php echo $this->_var['imgUrl']; ?>";
	var desc= "<?php echo $this->_var['desc']; ?>";
	wx.ready(function () {
	    wx.onMenuShareTimeline({//朋友圈
	        title: title, // 分享标题
	        link: link, // 分享链接
	        imgUrl: imgUrl, // 分享图标
	        success: function () { 
	            // 用户确认分享后执行的回调函数
	        	statis(2,1);
	        },
	        cancel: function () { 
	            // 用户取消分享后执行的回调函数
	        	statis(2,2);
	        }
	    });
	    wx.onMenuShareAppMessage({//好友
	        title: title, // 分享标题
	        desc: desc, // 
	        link: link, // 分享链接
	        imgUrl: imgUrl, // 分享图标
	        type: '', // 分享类型,music、video或link，不填默认为link
	        dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空
	        success: function () { 
	        	// 用户确认分享后执行的回调函数
	            statis(1,1);    
	        },
	        cancel: function () { 
	            // 用户取消分享后执行的回调函数
	        	statis(1,2);
	        }
	    });
	  
	    wx.onMenuShareQQ({
	        title: title, // 分享标题
	        desc: desc, // 分享描述
	        link: link, // 分享链接
	        imgUrl: imgUrl, // 分享图标
	        success: function () { 
	           // 用户确认分享后执行的回调函数
	        	statis(4,1);
	        },
	        cancel: function () { 
	           // 用户取消分享后执行的回调函数
	        	statis(4,2);
	        }
	    });
	    wx.onMenuShareWeibo({
	        title: title, // 分享标题
	        desc: desc, // 分享描述
	        link: link, // 分享链接
	        imgUrl: imgUrl, // 分享图标
	        success: function () { 
	           // 用户确认分享后执行的回调函数
	        	statis(3,1);
	        },
	        cancel: function () { 
	            // 用户取消分享后执行的回调函数
	        	statis(3,2);
	        }
	    });
	    <?php if ($this->_var['goods']['is_nearby']): ?>
	    wx.checkJsApi({
	    	
	        jsApiList: [
	            'getLocation'
	        ],
	        success: function (res) {
	             //alert(JSON.stringify(res));
	            // alert(JSON.stringify(res.checkResult.getLocation));
	            if (res.checkResult.getLocation == false) {
	                alert('你的微信版本太低，不支持微信JS接口，请升级到最新的微信版本！');
	                return;
	            }
	        }
	    });
	    wx.getLocation({
	        success: function (res) {
	            var latitude = res.latitude; // 纬度，浮点数，范围为90 ~ -90
	            var longitude = res.longitude; // 经度，浮点数，范围为180 ~ -180。
	            var speed = res.speed; // 速度，以米/每秒计
	            var accuracy = res.accuracy; // 位置精度
	            $.ajax({
	                type:"post",//请求类型
	                url:"goods.php",//服务器页面地址
	                data:"act=save_location&lat="+latitude+"&lng="+longitude,
	                dataType:"json",//服务器返回结果类型(可有可无)
	                error:function(){//错误处理函数(可有可无)
	                    //alert("ajax出错啦");
	                },
	                success:function(data){
	                    if(data.error==1){
	                        //alert('错误'+data.message);
	                    }else{
	                    	//document.getElementById('loading').style.display='none';
	                		
	                    }
	                }
	            });
	        },
	        cancel: function (res) {
	            alert('用户拒绝授权获取地理位置');
	        }
	    });
	    <?php endif; ?>	
	    
	});
	function statis(share_type,share_status){
		$.ajax({
            type:"post",//请求类型
            url:"share.php",//服务器页面地址
            data:"act=link&share_status="+share_status+"&share_type="+share_type+"&link_url=<?php echo $this->_var['link2']; ?>",
            dataType:"json",//服务器返回结果类型(可有可无)
            error:function(){//错误处理函数(可有可无)
                //alert("ajax出错啦");
            },
            success:function(data){
                
            }
        });
	}
</script>
</html>