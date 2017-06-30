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
<?php echo $this->smarty_insert_scripts(array('files'=>'jquery.js,haohaios.js,jquery.lazyload.js')); ?>
</head>
<body id="mall">
<div class="container">
    <div class="top_search">
        <form id="searchForm" name="searchForm" method="get" action="search.php" onSubmit="return checkSearchForm()">
            <input name="keywords" id="keyword" type="text" class="text" value="<?php echo htmlspecialchars($this->_var['search_keywords']); ?>" placeholder="商品搜索">
            <input type="submit" value="搜索" class="submit" />
            <input type="hidden" name="search_type" value="is_mall">
        </form>
    </div>
    <div class="top_nav" style="margin: 0 10px;">
		<ul id="top_nav">
            <?php if ($this->_var['sub_cat']): ?>
				<?php if ($this->_var['pid']): ?>
					<li<?php if ($this->_var['getCid'] == ''): ?> class="cur"<?php endif; ?>>
						<a href="mall.php?cid=<?php echo $this->_var['cat_id']; ?>&sort=sort_order&uid=<?php echo $this->_var['uid']; ?>"><span>全部</span></a>
					</li>
				<?php else: ?>
					<li<?php if ($this->_var['getCid'] == ''): ?> class="cur"<?php endif; ?>>
						<a href="mall.php?cid=<?php echo $this->_var['cat_id']; ?>&sort=sort_order&uid=<?php echo $this->_var['uid']; ?>"><span>全部</span></a>
					</li>
				<?php endif; ?>
			<?php else: ?>
				<?php if ($this->_var['pid']): ?>
					<li>
						<a href="mall.php?cid=<?php echo $this->_var['pid']; ?>&sort=sort_order&uid=<?php echo $this->_var['uid']; ?>">
						    <span>全部</span>
						</a>
					</li>
				<?php else: ?>
					<li<?php if ($this->_var['getCid'] == ''): ?> class="cur"<?php endif; ?>>
						<a href="mall.php?sort=sort_order&uid=<?php echo $this->_var['uid']; ?>">
						    <span>全部</span>
						</a>
					</li>
				<?php endif; ?>
			<?php endif; ?>
			
			<?php $_from = $this->_var['cat_children']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'child_data');if (count($_from)):
    foreach ($_from AS $this->_var['child_data']):
?>
				<li<?php if ($this->_var['cat_id'] == $this->_var['child_data']['id']): ?> class="cur"<?php endif; ?>>
					<a href="mall.php?cid=<?php echo $this->_var['child_data']['id']; ?>&sort=<?php echo $this->_var['sort']; ?>&uid=<?php echo $this->_var['uid']; ?>"><span><?php echo $this->_var['child_data']['name']; ?></span></a>
				</li>
			<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
				
            <?php if (0): ?>
            	<li<?php if ($this->_var['cat_id'] == 0): ?> class="cur"<?php endif; ?>>
            		<a href="mall.php?sort=sort_order&uid=<?php echo $this->_var['uid']; ?>">全部分类</a>
            	</li>
	            <?php $_from = $this->_var['categories']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'cat');if (count($_from)):
    foreach ($_from AS $this->_var['cat']):
?>
	            	<li<?php if ($this->_var['cat_id'] == $this->_var['cat']['id']): ?> class="cur"<?php endif; ?>>
	            		<a href="mall.php?sort=<?php echo $this->_var['sort']; ?>&cid=<?php echo $this->_var['cat']['id']; ?>&uid=<?php echo $this->_var['uid']; ?>"><?php echo $this->_var['cat']['name']; ?></a>
	            	</li>
	            <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
            <?php endif; ?>
		</ul>
    </div>
    <div class="blank"></div>
    <div class="good_list" style="margin: 0 10px;">
        <ul class="list_B">
            <?php $_from = $this->_var['goods_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'goods');if (count($_from)):
    foreach ($_from AS $this->_var['goods']):
?>
            <li>
                <a href="<?php if ($this->_var['goods']['goods_number'] > 0): ?><?php echo $this->_var['goods']['url']; ?>&uid=<?php echo $this->_var['uid']; ?><?php else: ?>javascript:void(0);<?php endif; ?>"><img goods_id="<?php echo $this->_var['goods']['goods_id']; ?>" data-original="<?php echo $this->_var['goods']['goods_thumb']; ?>" src="themes/haohainew/images/loading.gif" class="lazy"></a>
                <p class="tit"><a href="<?php echo $this->_var['goods']['url']; ?>&uid=<?php echo $this->_var['uid']; ?>"><?php echo $this->_var['goods']['goods_name']; ?></a></p>
                <p>
                    <font class="price">¥<b><?php echo $this->_var['goods']['shop_price']; ?></b></font>
                    <?php if ($this->_var['goods']['goods_number'] > 0): ?>
                     <?php if ($this->_var['goods']['attr']): ?>
                     <a class="mai iproduct_<?php echo $this->_var['goods']['goods_id']; ?>" id="iproduct_<?php echo $this->_var['goods']['goods_id']; ?>" href="javascript:addToCart(<?php echo $this->_var['goods']['goods_id']; ?>,0,1,0,0,1)">买</a>
                     <?php else: ?>
                     <a class="mai" id="iproduct_<?php echo $this->_var['goods']['goods_id']; ?>" href="javascript:addToCart(<?php echo $this->_var['goods']['goods_id']; ?>,0,1,0,0,1)">买</a>
                     <?php endif; ?>
                     <?php else: ?>
                     <a class="mai hui" href="javascript:;">缺货</a>
                   <?php endif; ?>

                </p>
            </li>
			<?php endforeach; else: ?>
			<div class="nothing">
            <i class="iconfont icon-shangpin"></i>
		    <p>此分类下暂无商品</p>
            </div>
            <?php endif; unset($_from); ?><?php $this->pop_vars();; ?>
        </ul>
    </div>
    <?php echo $this->fetch('library/pages.lbi'); ?>
</div>
<div class="blank"></div>
<?php echo $this->fetch('library/footer.lbi'); ?>
<script>
var wa = $(window).width(); //获取浏览器显示区域（可视区域）的宽度
var wb = $("#top_nav .cur").width(); //获取或设置元素的宽度
var wc = $("#top_nav .cur").offset().left; //当前元素左侧距离
if(wc+wb > wa){
	$("#top_nav").scrollLeft(wc-wa+wb+wb); //设置滚动条到左边的宽度
}
window.onload=function(){
	$("img.lazy").lazyload({
			effect: "fadeIn",
			threshold : 200
	});
	$("img.lazy:eq(0)").attr('src',$("img.lazy:eq(0)").attr('data-original'));
}
var btn_buy = "<?php echo $this->_var['lang']['btn_buy']; ?>";
var btn_add_to_cart = "<?php echo $this->_var['lang']['btn_add_to_cart']; ?>";
var is_cancel = "<?php echo $this->_var['lang']['is_cancel']; ?>";
var select_spe = "<?php echo $this->_var['lang']['select_spe']; ?>";
</script>

<script type="text/javascript">
function getElementLeft(element){
　　　　var actualLeft = element.offsetLeft;
　　　　var current = element.offsetParent;
        
　　　　while ( current !== null ){
　　　　　　actualLeft += current.offsetLeft;
　　　　　　current = current.offsetParent;

　　　　}

　　　　return actualLeft;
　　}

function getElementTop(element){
　　　　var actualTop = element.offsetTop;
　　　　var current = element.offsetParent;

　　　　while (current !== null){
　　　　　　actualTop += current.offsetTop;
　　　　　　current = current.offsetParent;
　　　　}

　　　　return actualTop;
　　}　　

    var Cart = {
      id: 'cat',
      addProduct: function(cpid, num, t ) {
        //添加商品
        var cat =document.getElementById('cat');  
        var catLeft=getElementLeft(cat);
        var catTop=getElementTop(cat);
        var sTop=document.body.scrollTop+document.documentElement.scrollTop;


        var op = $("[id=iproduct_"+cpid+"]").parents("li").find("img");
        var goods_id = $(op).attr("goods_id");

        if(op.length>0) {
            var np = op.clone().css({"position":"absolute", "top": op.offset().top, "left": op.offset().left, width: 50, height:50, "z-index": 999999999}).show();
            np.appendTo("body").animate({top:  catTop + sTop , left: $("#cat").offset().left +30 , width: 20, height:20}, {duration: 1000,
                    callback:function(){}, complete: function(){np.remove();addToCart(goods_id,0,1 ,0,0,1 );} });
        }
       }
    }

    $(function() {
        $('[id^=iproduct_]').click(function() {
            var id = $(this).attr("id");
            var tmp = id.split('_');
            var goods_id = tmp[1];

            //var cpid = this.id.replace('iproduct_'+goods_id,goods_id);

             Cart.addProduct(goods_id, 1, 0  );

            return false;
        });
     });
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

