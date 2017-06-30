<?php echo $this->smarty_insert_scripts(array('files'=>'utils.js')); ?>
<div class="total">运费：¥<span class="shipping_fee"><?php echo $this->_var['total']['shipping_fee']; ?></span> 合计：¥<span class="amount_fee"><?php echo $this->_var['total']['amount_fee']; ?></span>
