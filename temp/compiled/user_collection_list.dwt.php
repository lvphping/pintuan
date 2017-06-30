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
  <?php echo $this->smarty_insert_scripts(array('files'=>'utils.js')); ?>
  <?php if ($this->_var['action'] == 'collection_list'): ?>
    <div class="nav_fixed rank_nav">
        <a href="user.php?act=collection_list" <?php if ($this->_var['action'] == 'collection_list'): ?>class="cur"<?php endif; ?>><span>商品收藏</span></a>
        <a href="user.php?act=collect_store_list" <?php if ($this->_var['action'] == 'collect_store_list'): ?>class="cur"<?php endif; ?>><span>店铺收藏</span></a>
    </div>
    <div class="collection">
        <ul>
            <?php $_from = $this->_var['goods_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'goods');if (count($_from)):
    foreach ($_from AS $this->_var['goods']):
?>
            <li>
                <a href="<?php echo $this->_var['goods']['url']; ?>"><img src="<?php echo $this->_var['goods']['goods_thumb']; ?>"></a>
                <div class="info">
                    <h3><a href="<?php echo $this->_var['goods']['url']; ?>"><?php echo htmlspecialchars($this->_var['goods']['goods_name']); ?></a></h3>
                    <p>¥<?php if ($this->_var['goods']['promote_price'] != ""): ?><?php echo $this->_var['goods']['promote_price']; ?><?php else: ?><?php echo $this->_var['goods']['shop_price']; ?><?php endif; ?></p>
                </div>
                <a href="javascript:location.href='user.php?act=delete_collection&collection_id=<?php echo $this->_var['goods']['rec_id']; ?>'" class="del"></a>
            </li>
			<?php endforeach; else: ?>
        <div class="nothing">
            <i class="iconfont icon-shoucang"></i>
            <p>您还没有收藏商品哦</p>
        </div>
            <?php endif; unset($_from); ?><?php $this->pop_vars();; ?>
        </ul>
    </div>
	<?php endif; ?>
	
	<?php if ($this->_var['action'] == 'collect_store_list'): ?>
	<div class="nav_fixed rank_nav">
        <a href="user.php?act=collection_list" <?php if ($this->_var['action'] == 'collection_list'): ?>class="cur"<?php endif; ?>><span>商品收藏</span></a>
        <a href="user.php?act=collect_store_list" <?php if ($this->_var['action'] == 'collect_store_list'): ?>class="cur"<?php endif; ?>><span>店铺收藏</span></a>
    </div>
	<div class="collection_store">
        <ul>
            <?php $_from = $this->_var['store_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'store');if (count($_from)):
    foreach ($_from AS $this->_var['store']):
?>
            <li>
                <a href="store.php?id=<?php echo $this->_var['store']['suppliers_id']; ?>"><img src="<?php echo $this->_var['store']['logo']; ?>"></a>
                <div class="info">
                    <a href="store.php?id=<?php echo $this->_var['store']['suppliers_id']; ?>"><?php echo htmlspecialchars($this->_var['store']['suppliers_name']); ?></a>
                </div>
                <a href="javascript:location.href='user.php?act=del_collect_store&id=<?php echo $this->_var['store']['suppliers_id']; ?>'" class="del"></a>
            </li>
			<?php endforeach; else: ?>
        <div class="nothing">
            <i class="iconfont icon-shoucang"></i>
            <p>您还没有收藏店铺哦</p>
        </div>
            <?php endif; unset($_from); ?><?php $this->pop_vars();; ?>
        </ul>
	</div>	
	<?php endif; ?>
	
    <?php echo $this->fetch('library/pages.lbi'); ?>
</div>
<div class="blank"></div>
<?php echo $this->fetch('library/footer.lbi'); ?>
</body>
</html>

