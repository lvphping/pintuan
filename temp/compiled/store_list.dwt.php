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
<link href="<?php echo $this->_var['hhs_css_path']; ?>/store_list.css" rel="stylesheet" />
<?php echo $this->smarty_insert_scripts(array('files'=>'jquery.js,store_list.js')); ?>
</head>
<body>
<div class="container" style="padding:49px 0 60px 0;">
    <div class="nav_fixed_wrap">
        <ul class="nav_fixed_catgoods" id="top_nav">
            <li class="fixed_nav_item_catgoods fixed_nav_item_catgoods_first">
                <a href="store_list.php"><span<?php if ($this->_var['hangye_id'] == 0): ?> class="nav_cur_cat"<?php endif; ?>>全部</span></a>
            </li>
            <?php $_from = $this->_var['hangye']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'item');$this->_foreach['name'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['name']['total'] > 0):
    foreach ($_from AS $this->_var['item']):
        $this->_foreach['name']['iteration']++;
?>
            <li class="fixed_nav_item_catgoods">
                <a href="store_list.php?id=<?php echo $this->_var['item']['id']; ?>">
                <span<?php if ($this->_var['hangye_id'] == $this->_var['item']['id']): ?> class="nav_cur_cat"<?php endif; ?>><?php echo $this->_var['item']['name']; ?></span>
                </a>
            </li>
            <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
        </ul>
    </div>
    <div class="store_list">
        <?php $_from = $this->_var['store_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'suppliers');$this->_foreach['suppliers'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['suppliers']['total'] > 0):
    foreach ($_from AS $this->_var['key'] => $this->_var['suppliers']):
        $this->_foreach['suppliers']['iteration']++;
?>
        <div class="grst-block hangye hangye-<?php echo $this->_var['suppliers']['hangye_id']; ?>">
            <a href="store.php?id=<?php echo $this->_var['suppliers']['suppliers_id']; ?>"><img class="grst-logo" src="<?php echo $this->_var['suppliers']['supp_logo']; ?>"></a>
            <div class="grst-detail">
                <h3><?php echo $this->_var['suppliers']['suppliers_name']; ?></h3>
                <p><?php echo $this->_var['suppliers']['province_id']; ?> <?php echo $this->_var['suppliers']['city_id']; ?></p>
                <p>商品：<?php echo $this->_var['suppliers']['goods_num']; ?>　销量：<?php echo $this->_var['suppliers']['sales_num']; ?></p>
                <a href="store.php?id=<?php echo $this->_var['suppliers']['suppliers_id']; ?>" class="in">进入店铺</a>
            </div>
        </div>
		<?php endforeach; else: ?>
			<div class="nothing">
            <i class="iconfont icon-shangpin"></i>
		    <p>此分类下暂无店铺</p>
            </div>
        <?php endif; unset($_from); ?><?php $this->pop_vars();; ?>
    </div>
</div>
<div class="blank"></div>
<div class="blank"></div>
<div class="blank"></div>
<div class="blank"></div>
<div class="blank"></div>
<div class="blank"></div>
<?php echo $this->fetch('library/footer.lbi'); ?>

<div class="back-top" style=""><a href="javascript:;" uigs="wap_to_top"></a></div>
<script type="text/javascript">
var wa = $(window).width(); //获取浏览器显示区域（可视区域）的宽度
var wb = $("#top_nav .nav_cur_cat").width(); //获取或设置元素的宽度
var wc = $("#top_nav .nav_cur_cat").offset().left; //当前元素左侧距离
if(wc+wb > wa){
	$("#top_nav").scrollLeft(wc-wa+wb+wb); //设置滚动条到左边的宽度
}
$(function(){   
	$('.choosebox li a').click(function(){
		var thisToggle = $(this).is('.size_radioToggle') ? $(this) : $(this).prev();
		var checkBox = thisToggle.prev();
		checkBox.trigger('click');
		$('.size_radioToggle').removeClass('current');
		thisToggle.addClass('current');
		return false;
	});		
});

$(".choosebox li a").click(function(){
	var text = $(this).html();
	$(".choosetext span").html(text);
	$("#result").html("" + getSelectedValue("dress-size"));
});
			
function getSelectedValue(id){
	return 
	$("#" + id).find(".choosetext span.value").html();
}
</script>
</body>
</html>