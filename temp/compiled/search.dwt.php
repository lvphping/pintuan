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
<?php echo $this->smarty_insert_scripts(array('files'=>'jquery.js,haohaios.js,utils.js')); ?>
</head>
<body>
<div class="container">
    <div class="top_search">
        <form id="searchForm" name="searchForm" method="get" action="search.php" onSubmit="return checkSearchForm()">
            <input name="keywords" id="keyword" type="text" class="text" value="<?php echo htmlspecialchars($this->_var['search_keywords']); ?>" placeholder="商品搜索">
            <input type="submit" value="搜索" class="submit" />
        </form>
    </div>
    <div class="blank" style="height: 0;"></div>
    <div class="good_list" style="padding: 0 10px;">
        <?php if ($this->_var['goods_list']): ?>
        <h3>有关<b><?php echo htmlspecialchars($this->_var['search_keywords']); ?></b>的商品有<b><?php echo $this->_var['count']; ?></b>种</h3>
        <ul class="list_B">
            <?php $_from = $this->_var['goods_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'goods');if (count($_from)):
    foreach ($_from AS $this->_var['goods']):
?>
         
                <?php if ($this->_var['goods']['is_team'] == 1): ?>
				<li>
                    <a href="<?php if ($this->_var['goods']['goods_number'] > 0): ?><?php echo $this->_var['goods']['url']; ?><?php else: ?>javascript:void(0);<?php endif; ?>"><img src="<?php echo $this->_var['goods']['goods_thumb']; ?>"></a>
					<?php if ($this->_var['goods']['goods_number'] < 1): ?>
					<span class="sell_f"></span>
					<?php endif; ?>
                    <p class="tit"><a href="<?php if ($this->_var['goods']['goods_number'] > 0): ?><?php echo $this->_var['goods']['url']; ?><?php else: ?>javascript:void(0);<?php endif; ?>"><?php echo $this->_var['goods']['goods_name']; ?></a></p>
                    <?php if ($this->_var['goods']['goods_brief']): ?>
                    <p class="brief"><?php echo $this->_var['goods']['goods_brief']; ?></p>
                    <?php endif; ?>
                    <p><del>¥<?php echo $this->_var['goods']['market_price']; ?></del><font class="price">¥<b><?php echo $this->_var['goods']['shop_price']; ?></b></font></p>
				</li>
                <?php endif; ?>
                <?php if ($this->_var['goods']['is_mall'] == 1 || $this->_var['goods']['is_zero'] == 1): ?>
				<li>
                    <a href="<?php if ($this->_var['goods']['goods_number'] > 0): ?><?php echo $this->_var['goods']['url']; ?><?php else: ?>javascript:void(0);<?php endif; ?>"><img goods_id="<?php echo $this->_var['goods']['goods_id']; ?>" src="<?php echo $this->_var['goods']['goods_thumb']; ?>"></a>
                    <p class="tit"><a href="<?php if ($this->_var['goods']['goods_number'] > 0): ?><?php echo $this->_var['goods']['url']; ?><?php else: ?>javascript:void(0);<?php endif; ?>"><?php echo $this->_var['goods']['goods_name']; ?></a></p>
                    <?php if ($this->_var['goods']['goods_brief']): ?>
                    <p class="brief"><?php echo $this->_var['goods']['goods_brief']; ?></p>
                    <?php endif; ?>
                    <p><del>¥<?php echo $this->_var['goods']['market_price']; ?></del><font class="price">¥<b><?php echo $this->_var['goods']['shop_price']; ?></b></font></p>
				</li>
                <?php endif; ?>
            
            <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
        </ul>
        <?php else: ?>
		
		<div class="nothing" style="position:static; margin:50px auto ;">
            <i class="iconfont icon-shangpin"></i>
		    <p>没有找到<b><?php echo htmlspecialchars($this->_var['search_keywords']); ?></b>相关的商品</p>
        </div>

        <div class="recommend_grid_wrap" >
            <div id="recommend" class="grid">
                <div class="recommend_head">你可能还喜欢</div>
                <div class="bd">
                    <ul>
                        <?php $_from = $this->_var['rands_goods']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'goods');if (count($_from)):
    foreach ($_from AS $this->_var['goods']):
?>
                        <li>
                            <div class="recommend_img"><a href="goods.php?id=<?php echo $this->_var['goods']['goods_id']; ?>"><img  src="<?php if ($this->_var['goods']['goods_img']): ?><?php echo $this->_var['goods']['goods_img']; ?><?php else: ?>images/no_picture.gif<?php endif; ?>"></a></div>
                            <div class="recommend_title"><a href="goods.php?id=<?php echo $this->_var['goods']['goods_id']; ?>"><?php echo $this->_var['goods']['goods_name']; ?></a></div>
                            <div class="recommend_price">￥<span><?php echo $this->_var['goods']['goods_price']; ?></span></div>
                            <?php if ($this->_var['goods']['rec_id'] > 0): ?>
                            <div class="like_click" data-id="<?php echo $this->_var['goods']['goods_id']; ?>"><img src="themes/haohainew/images/is_liked2.png" data-isLiked="1"></div>
                            <?php else: ?>
                            <div class="like_click" data-id="<?php echo $this->_var['goods']['goods_id']; ?>"> <img src="themes/haohainew/images/no_liked2.png" data-isLiked="0"></div>
                            <?php endif; ?>
                        </li>
                        <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                    </ul>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>
<a href="flows.php?step=cart" class="gw-cart-p" id="cat"><i class="iconfont-cart"></i>
    <span id="HHS_CARTINFO" class="gw-cart"><?php 
$k = array (
  'name' => 'cart_num',
);
echo $this->_echash . $k['name'] . '|' . serialize($k) . $this->_echash;
?></span>
</a>
<?php echo $this->fetch('library/footer.lbi'); ?>
</body>
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



        var user_id = <?php echo $this->_var['uid']; ?>;
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
                });
            } else {
                $.get('user.php', {
                    act: "collect",
                    id: goodsId,
                    user_id: user_id
                }).done(function(e) {
                    img.attr("src", "themes/haohainew/images/is_liked2.png");
                    img.attr("data-isLiked", 1)
                });

            }
        });   
     });
</script>
</html>

