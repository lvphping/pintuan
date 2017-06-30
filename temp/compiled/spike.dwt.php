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
<link href="<?php echo $this->_var['hhs_css_path']; ?>/spike.css" rel="stylesheet" />
<?php echo $this->smarty_insert_scripts(array('files'=>'jquery.js,haohaios.js,jquery.lazyload.js')); ?>
</head>
<body>
<div class="spike_container">
    <div class="nav_fixed nav_spike">
        <a href="javascript:;" class="fixed_nav_item cur"><span>全部</span></a>
        <a href="javascript:;" class="fixed_nav_item"><span>正在进行</span></a>
        <a href="javascript:;" class="fixed_nav_item"><span>即将开始</span></a>
        <a href="javascript:;" class="fixed_nav_item"><span>已售罄</span></a>
    </div>
    <?php $_from = $this->_var['goods_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'goods');$this->_foreach['goods_list'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['goods_list']['total'] > 0):
    foreach ($_from AS $this->_var['goods']):
        $this->_foreach['goods_list']['iteration']++;
?>
    <div class="spike_goods <?php if ($this->_var['goods']['promote_start_date'] > $this->_var['timestamp']): ?>op2<?php elseif ($this->_var['goods']['promote_end_date'] > $this->_var['timestamp']): ?>op1<?php else: ?>op3<?php endif; ?>">				
        <a href="<?php echo $this->_var['goods']['url']; ?>"><img data-original="<?php echo $this->_var['goods']['goods_img']; ?>" src="themes/haohainew/images/loading.gif" class="lazy"></a>
        <div class="spike_info">
            <div class="spike_left_time">
                <?php if ($this->_var['goods']['promote_start_date1'] > $this->_var['now_time']): ?>开始时间：<?php echo $this->_var['goods']['start_date']; ?><?php else: ?><font class="endtime" data-endtime="<?php echo $this->_var['goods']['promote_end_date']; ?>"></font><?php endif; ?>
            </div>
            <a href="<?php echo $this->_var['goods']['url']; ?>" class="spike_goods_name"><?php echo $this->_var['goods']['goods_name']; ?></a>
            <div class="spike_buy">
                <div class="spike_price_all">
                    <div class="spike_sale_price">¥<?php echo $this->_var['goods']['promote_price']; ?></div>
                </div>
                <?php if ($this->_var['goods']['promote_start_date1'] > $this->_var['now_time']): ?>
                <div class="spike_buy_button_come"><a href="<?php echo $this->_var['goods']['url']; ?>">即将开始</a></div>
                <?php elseif ($this->_var['goods']['promote_end_date1'] > $this->_var['now_time']): ?>
                <div class="spike_buy_button_on"><a href="<?php echo $this->_var['goods']['url']; ?>">立即抢购</a></div>
                <?php else: ?>
                <div class="spike_buy_button_off"><a href="<?php echo $this->_var['goods']['url']; ?>">已售罄</a></div>
                <?php endif; ?>
            </div>
        </div>
    </div>
	<?php endforeach; else: ?>
	<div class="nothing">
        <i class="iconfont icon-shangpin"></i>
        <p>暂无商品</p>
    </div>
    <?php endif; unset($_from); ?><?php $this->pop_vars();; ?>
</div>
<?php echo $this->fetch('library/footer.lbi'); ?>
<script>
var timestamp = <?php echo $this->_var['timestamp']; ?>;
var serverTime = <?php echo $this->_var['timestamp']; ?> * 1000;
$(function(){
    var dateTime = new Date();
    var difference = dateTime.getTime() - serverTime;
    setInterval(function(){
      $(".endtime").each(function(){
        var obj = $(this);
        var endTime = new Date(parseInt(obj.data('endtime')) * 1000);
        var nowTime = new Date();
        var nMS=endTime.getTime() - nowTime.getTime() + difference;
        var myD=Math.floor(nMS/(1000 * 60 * 60 * 24));
        var myH=Math.floor(nMS/(1000*60*60)) % 24;
        var myM=Math.floor(nMS/(1000*60)) % 60;
        var myS=Math.floor(nMS/1000) % 60;
        var myMS=Math.floor(nMS/100) % 10;
        if(myD>= 0){
        	var str = "<span>剩余</span><b>"+myD+"</b><span>天</span><b>"+myH+"</b><span>小时</span><b>"+myM+"</b><span>分</span><b>"+myS+"</b><span>秒结束</span>";
        }else{
			var str = "已结束！";	
			obj.prev('div').text('');
		}
		obj.html(str);
      });
    }, 100);
    $(".fixed_nav_item").click(function(event) {
    	var i = $(this).index();
    	if(i>0){
    		$(".spike_goods").hide();
    		$(".op"+i).show();
    	}
    	else{
    		$(".spike_goods").show();
    	}
    	$(".fixed_nav_item").removeClass('cur');
    	$(this).addClass('cur');    	
    });
});

window.onload=function(){
    $("img.lazy").lazyload({
        effect: "fadeIn",
        threshold : 200
    });
    $("img.lazy:eq(0)").attr('src',$("img.lazy:eq(0)").attr('data-original'));
}
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
	        'onMenuShareWeibo'
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
</body>
</html>
