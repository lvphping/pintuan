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
<link href="<?php echo $this->_var['hhs_css_path']; ?>/user.css" rel="stylesheet" />
<link href="<?php echo $this->_var['hhs_css_path']; ?>/font-awesome.min.css" rel="stylesheet" />
<?php echo $this->smarty_insert_scripts(array('files'=>'jquery.js,haohaios.js,user.js')); ?>
</head>
<body id="user">
<?php if ($this->_var['action'] == 'default'): ?>
<div class="container">
    <div class="my">
            <div class="my_head_pic">
                <img class="my_head_img" width="130" height="130" src="<?php echo $this->_var['info']['headimgurl']; ?>">
            </div>
            <div class="my_head_info">
                <h3><?php if ($this->_var['info']['uname']): ?><?php echo $this->_var['info']['uname']; ?><?php else: ?><?php echo $this->_var['info']['username']; ?><?php endif; ?></h4>
                <p>账户余额：<?php echo $this->_var['surplus_amount']; ?>元</p>
                <p>积分：<?php echo $this->_var['points']; ?>分</p>
            </div>
    </div>
    <div class="my_nav">
        <ul>
            <li><a href="user.php?act=collection_list" class="fx_sc"><i></i>收藏</a></li>
            <li><a href="user.php?act=account_log" class="fx_qb"><i></i>钱包</a></li>
            <li><a href="javascript:registration(<?php echo $_SESSION['user_id']; ?>)" class="fx_qd"><i></i>签到</a></li>
        </ul>
    </div>
    <div class="blank"></div>
    <div class="nav">
        <div class="nav_item nav_order">
            <div class="nav_item_order_hd"> <a href="user.php?act=order_list">
                <div class="nav_item_order">我的订单</div>
                <div class="nav_item_order_bd">查看全部订单 <img class="nav_item_order_bd_arrow" src="themes/haohainew/images/personal_arrow.png"> </div>
                </a> </div>
            <div class="nav_item_bd">
                <a href="user.php?act=order_list&composite_status=100">
                <div class="nav_item_order_img order_unpay"><b><?php echo $this->_var['daifukuan']; ?></b></div>
                <span class="nav_item_txt">待付款</span> </a> <a href="user.php?act=order_list&composite_status=180">
                <div class="nav_item_order_img order_unsend"><b><?php echo $this->_var['daifahuo']; ?></b></div>
                <span class="nav_item_txt">待发货</span> </a> <a href="user.php?act=order_list&composite_status=120">
                <div class="nav_item_order_img order_unreceived"><b><?php echo $this->_var['daishouhuo']; ?></b></div>
                <span class="nav_item_txt">待收货</span> </a> <a href="user.php?act=order_list&composite_status=999">
                <div class="nav_item_order_img order_unevaluated"><b><?php echo $this->_var['daipingjia']; ?></b></div>
                <span class="nav_item_txt">已完成</span> </a> </div>
        </div>
    </div>
    <div class="nav">
        <ul class="nav_list">
            <li class="nav_team"><a href="user.php?act=team_list">我的团</a></li>
			<li class="nav_lottory"><a href="user.php?act=luckdraw">抽奖记录</a></li>
            <li class="nav_duobao" style="display:none"><a href="user.php?act=lottery_list">夺宝记录</a></li>
            <li class="nav_bonus"><a href="user.php?act=bonus">我的优惠券</a></li>
            <li class="nav_adress"><a href="user.php?act=address_list">收货地址</a></li>
            <li class="nav_fenxiao" style="display:none"><a href="user.php?act=fenxiao">我的分销</a></li>
            <li class="nav_store" style="display:none"> 
                <?php if ($this->_var['is_check'] == 1): ?> 
                <a href="store.php?id=<?php echo $this->_var['suppliers_id']; ?>">我的小店</a> 
                <?php elseif ($this->_var['is_check'] == 2): ?>
                审核中
                <?php elseif ($this->_var['is_check'] == 3): ?>
                审核未过
                <?php else: ?> 
                <a href="enter.php">申请入驻</a> 
                <?php endif; ?> 
            </li>
            <li class="nav_sug"><a href="suggestion.php">常见问题</a></li>
            <li class="nav_exc"><a href="exchange.php">积分商城</a></li>
        </ul>
    </div>
    <?php endif; ?> 
</div>
<div class="blank"></div>
<?php echo $this->fetch('library/footer.lbi'); ?> 
<script language="javascript">
    	//会员签到
        function registration(user_id)
		{
			Ajax.call('./user_registration.php', 'act=registration&user_id='+user_id, registration_res, 'GET', 'JSON');
		}
			   
		function registration_res(result)
		{	 
			  		if(result.error==3)
					{
						layer.open({
						    content: '签到活动已关闭，敬请期待。',
							btn: ['确定']
						});

						return false;
					}
					else if(result.error==1)
					{
						//alert('签到成功，获得'+result.qiandao_integral+'积分。');
						layer.open({
						    content: '签到成功，获得'+result.qiandao_integral+'积分。',
							btn: ['确定']
						});
						
						document.getElementById('pay_points').innerHTML  = result.pay_points;
						//alert(result.content);
						return false;
					}
					else if(result.error==2)
					{
						layer.open({
						    content: '已签到，请明天再来吧!',
							btn: ['确定']
						});
						return false;
					}
					else if(result.error==4)
					{
						location.href = './user.php';
					}
			}
			
    </script>
</body>
</html>
