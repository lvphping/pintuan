<form name="selectPageForm" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get">
<?php if ($this->_var['pager']['page_count'] != 1): ?>
 <div class="pagebar">
  <?php if ($this->_var['pager']['page_prev']): ?><a class="prev" href="<?php echo $this->_var['pager']['page_prev']; ?>"><?php echo $this->_var['lang']['page_prev']; ?></a><?php else: ?><span><?php echo $this->_var['lang']['page_prev']; ?></span><?php endif; ?>
  <?php if ($this->_var['pager']['page_next']): ?><a class="next" href="<?php echo $this->_var['pager']['page_next']; ?>"><?php echo $this->_var['lang']['page_next']; ?></a><?php else: ?><span><?php echo $this->_var['lang']['page_next']; ?></span><?php endif; ?>
</div>
<?php endif; ?>
</form>

