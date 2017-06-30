<!doctype html>
<html lang="zh-CN">
<head>
<meta name="Generator" content="haohaipt v6.0" />
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=no">
<meta name="Keywords" content="<?php echo $this->_var['keywords']; ?>" />
<meta name="Description" content="<?php echo $this->_var['description']; ?>" />
<meta name="format-detection" content="telephone=no">
<title>全部品牌</title>
<link rel="shortcut icon" href="favicon.ico" />
<link href="<?php echo $this->_var['hhs_css_path']; ?>/style.css" rel="stylesheet" />
<link href="<?php echo $this->_var['hhs_css_path']; ?>/font-awesome.min.css" rel="stylesheet" />
<?php echo $this->smarty_insert_scripts(array('files'=>'jquery.js,haohaios.js,jquery.lazyload.js')); ?>
<script type="text/javascript">
    var process_request = "<?php echo $this->_var['lang']['process_request']; ?>";
    function checkSearchForm()
    {
        if(document.getElementById('keyword').value)
        {
            return true;
        }
        else
        {
            alert("<?php echo $this->_var['lang']['no_keywords']; ?>");
            return false;
        }
    }
</script>
</head>
<body>
<div class="container">
    <div class="brand_info" style="display: none;">
        <dl>
            <?php if ($this->_var['brand']['brand_logo']): ?>
            <dt><img src="data/brandlogo/<?php echo $this->_var['brand']['brand_logo']; ?>" /></dt>
            <?php endif; ?>
            <dd>
                <h3><?php echo $this->_var['brand']['brand_name']; ?></h3>
                <p><?php echo nl2br($this->_var['brand']['brand_desc']); ?></p>
            </dd>
        </dl>
    </div>
	<?php if ($this->_var['goods_list']): ?>
    <div class="good_list">
        <ul class="list_B">
            <?php $_from = $this->_var['goods_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'goods');if (count($_from)):
    foreach ($_from AS $this->_var['goods']):
?>
            <li>
                <a href="<?php echo $this->_var['goods']['url']; ?>&uid=<?php echo $this->_var['uid']; ?>"><img goods_id="<?php echo $this->_var['goods']['goods_id']; ?>" data-original="<?php echo $this->_var['goods']['goods_thumb']; ?>" src="themes/haohainew/images/loading.gif" class="lazy"></a>
                <p class="tit"><a href="<?php echo $this->_var['goods']['url']; ?>&uid=<?php echo $this->_var['uid']; ?>"><?php echo $this->_var['goods']['goods_name']; ?></a></p>
                <?php if ($this->_var['goods']['goods_brief']): ?>
                <p class="brief"><?php echo $this->_var['goods']['goods_brief']; ?></p>
                <?php endif; ?>
                <p><del>¥<?php echo $this->_var['goods']['market_price']; ?></del><font class="price">¥<b><?php echo $this->_var['goods']['shop_price']; ?></b></font></p>
            </li>
            <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
        </ul>
    </div>
	<?php else: ?>
			<div class="nothing">
            <i class="iconfont icon-shangpin"></i>
		    <p>此品牌下暂无商品</p>
            </div>
	<?php endif; ?>
    <?php echo $this->fetch('library/pages.lbi'); ?>
</div>
<?php echo $this->fetch('library/footer.lbi'); ?>
<script>
	window.onload=function(){
		$("img.lazy").lazyload({
                effect: "fadeIn",
                threshold : 200
        });
        $("img.lazy:eq(0)").attr('src',$("img.lazy:eq(0)").attr('data-original'));
	}
</script>
</body>
</html>
