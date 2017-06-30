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
<?php echo $this->smarty_insert_scripts(array('files'=>'jquery.js,haohaios.js,shopping_flow.js,region.js')); ?>
</head>
<body>
 
<?php echo $this->smarty_insert_scripts(array('files'=>'region.js,utils.js')); ?> 
<script type="text/javascript">
    region.isAdmin = false;
    <?php $_from = $this->_var['lang']['flow_js']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'item');if (count($_from)):
    foreach ($_from AS $this->_var['key'] => $this->_var['item']):
?>
    var <?php echo $this->_var['key']; ?> = "<?php echo $this->_var['item']; ?>";
    <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
	
	onload = function() {
		if (!document.all)
		{
			 document.forms['theForm'].reset();
		}
	}
	
</script>
<div class="container">
<form action="<?php echo $this->_var['back_url']; ?>" method="post" name="theForm" id="theForm" onsubmit="return checkConsignee(this)">
    <?php echo $this->smarty_insert_scripts(array('files'=>'utils.js')); ?>
    <div class="consignee">
        <dl>
            <dt>收货人</dt>
            <dd>
                <input name="consignee" type="text" class="inputBg" id="consignee_<?php echo $this->_var['sn']; ?>" value="<?php echo htmlspecialchars($this->_var['consignee']['consignee']); ?>" placeholder="姓名"/>
                <div id="updateTip1" class="operate_tips" style="display:none;">
                    <span class="operate_content">请填写姓名</span>
                    <span class="down_row"></span>
                </div>       
            </dd>
        </dl>
        <dl>
            <dt>手机号码</dt>
            <dd>
                <input name="mobile" type="text" class="inputBg"  id="mobile_<?php echo $this->_var['sn']; ?>" value="<?php echo htmlspecialchars($this->_var['consignee']['mobile']); ?>" placeholder="电话"/>
                <div id="updateTip2" class="operate_tips" style="display:none;">
                    <span class="operate_content">请填写正确的电话</span>
                    <span class="down_row"></span>
                </div>
            </dd>
        </dl>
        <dl>
            <dd class="diqu">
                <select name="province" id="selProvinces" onchange="region.changed(this, 2, 'selCities')">
                    <option value="0">请选择省</option>
                    <?php $_from = $this->_var['province_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'province');if (count($_from)):
    foreach ($_from AS $this->_var['province']):
?>
                    <option value="<?php echo $this->_var['province']['region_id']; ?>" <?php if ($this->_var['consignee']['province'] == $this->_var['province']['region_id']): ?>selected<?php endif; ?>><?php echo $this->_var['province']['region_name']; ?></option>
                    <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                </select>
                <div id="updateTip3" class="operate_tips" style="display:none;">
                    <span class="operate_content">请选择省</span>
                    <span class="down_row"></span>
                </div>
				
				
                <select name="city" id="selCities" onchange="region.changed(this, 3, 'selDistricts')">
                    <option value="0">请选择市</option>
                    <?php $_from = $this->_var['city_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'city');if (count($_from)):
    foreach ($_from AS $this->_var['city']):
?>
                    <option value="<?php echo $this->_var['city']['region_id']; ?>" <?php if ($this->_var['consignee']['city'] == $this->_var['city']['region_id']): ?>selected<?php endif; ?>><?php echo $this->_var['city']['region_name']; ?></option>
                    <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                </select>
                <div id="updateTip4" class="operate_tips" style="display:none;">
                    <span class="operate_content">请选择市</span>
                    <span class="down_row"></span>
                </div>
				
				
				<select name="district" id="selDistricts">
                    <option value="0">请选择区/县</option>
                    <?php $_from = $this->_var['district_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'district');if (count($_from)):
    foreach ($_from AS $this->_var['district']):
?>
                    <option value="<?php echo $this->_var['district']['region_id']; ?>" <?php if ($this->_var['consignee']['district'] == $this->_var['district']['region_id']): ?>selected<?php endif; ?>><?php echo $this->_var['district']['region_name']; ?></option>
                    <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                </select>
                <div id="updateTip5" class="operate_tips" style="display:none;">
                    <span class="operate_content">请选择区/县</span>
                    <span class="down_row"></span>
                </div>
				
				
            </dd>
        </dl>
        <!--dl>
            <dt>地址类别</dt>
            <dd>
                <select name="address_type" id="address_type">
                    <option value="0">-选择家庭/公司-</option>
                    <option value="1" <?php if ($this->_var['consignee']['address_type'] == 1): ?>selected<?php endif; ?>>家庭</option>
                    <option value="2" <?php if ($this->_var['consignee']['address_type'] == 2): ?>selected<?php endif; ?>>公司</option>
                </select>
            </dd>
        </dl-->
        <dl>
            <dt>详细地址</dt>
            <dd>
                <input name="address" type="text" class="inputBg"  id="address_<?php echo $this->_var['sn']; ?>" value="<?php echo htmlspecialchars($this->_var['consignee']['address']); ?>" placeholder="详细地址" />
                <div id="updateTip6" class="operate_tips" style="display:none;">
                    <span class="operate_content">请填写详细地址</span>
                    <span class="down_row"></span>
                </div>
            </dd>
        </dl>
        <dl>
            <div style="padding:10px 0;overflow:hidden;">
                <input name="address_id" type="hidden" value="<?php echo $this->_var['consignee']['address_id']; ?>" />
                <input type="hidden" name="act" value="act_edit_consignee" />
                <input type="hidden" name="luckdraw_id" value="<?php echo $this->_var['luckdraw_id']; ?>"/>
                <?php if ($this->_var['consignee']['address_id']): ?>
                <button class="submit t1" id="add">确认</button>
                <button class="submit t2" type="button" onclick="window.location='<?php echo $this->_var['back_url']; ?>?act=drop_consignee&id=<?php echo $this->_var['consignee']['address_id']; ?>';" id="deletes">删除</button>
                <?php else: ?>
                <button class="submit" id="add">确认</button>
                <?php endif; ?>
            </div>
        </dl>
    </div>
</form>
</div>
<div class="blank"></div>
<?php echo $this->fetch('library/footer.lbi'); ?>
</body>
<script type="text/javascript">
var process_request = "<?php echo $this->_var['lang']['process_request']; ?>";
<?php $_from = $this->_var['lang']['passport_js']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'item');if (count($_from)):
    foreach ($_from AS $this->_var['key'] => $this->_var['item']):
?>
var <?php echo $this->_var['key']; ?> = "<?php echo $this->_var['item']; ?>";
<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
var country_not_null='国家不能为空';
var province_not_null='省份不能为空';
var city_not_null='城市不能为空';
var district_not_null='区域不能为空';
var consignee_not_null='收货人不能为空';
var address_not_null='详细地址不能为空';

var username_exist = "<?php echo $this->_var['lang']['username_exist']; ?>";
var compare_no_goods = "<?php echo $this->_var['lang']['compare_no_goods']; ?>";
var btn_buy = "<?php echo $this->_var['lang']['btn_buy']; ?>";
var is_cancel = "<?php echo $this->_var['lang']['is_cancel']; ?>";
var select_spe = "<?php echo $this->_var['lang']['select_spe']; ?>";
</script>
</html>
