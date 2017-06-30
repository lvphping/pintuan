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
<link href="<?php echo $this->_var['hhs_css_path']; ?>/suggestion.css" rel="stylesheet" />
<link href="<?php echo $this->_var['hhs_css_path']; ?>/font-awesome.min.css" rel="stylesheet" />
<?php echo $this->smarty_insert_scripts(array('files'=>'jquery.js,haohaios.js')); ?>
</head>
<body id="user">
<div class="container">
    <div class="suggestion-wrap">
        <div class="suggestion-header">常见问题</div>
        <div style="height: 45px; visibility: hidden;"></div>
        <div class="suggestion-body">
            <div class="suggestion-list">
                <?php $_from = $this->_var['suggestion']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'suggestion_0_26876500_1498558329');if (count($_from)):
    foreach ($_from AS $this->_var['suggestion_0_26876500_1498558329']):
?>
                <div class="suggestion-one">
                    <a href="faq.php?id=<?php echo $this->_var['suggestion_0_26876500_1498558329']['article_id']; ?>">
                    <div class="suggestion-title"><?php echo $this->_var['suggestion_0_26876500_1498558329']['title']; ?></div>
                    <div class="suggestion-arrow">
                        <img src="themes/haohainew/images/home_arrow.png">
                    </div>
                    </a>
                </div>
                <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>  
            </div>
        </div>
        <div style="height: 50px; visibility: hidden;"></div>
    </div>
</div>
<?php echo $this->fetch('library/footer.lbi'); ?>
</body>
</html>
