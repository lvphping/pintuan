
<a class="send_address" href="flows.php?step=address_list">
    <div id="sendTo">
        <div class="address_defalut">
            <div class="bg"></div>
            <ul id="editAddBtn">
            <?php if ($this->_var['consignee']): ?>
                <li><i class="fa fa-user"></i><b><?php echo $this->_var['consignee']['consignee']; ?>　<?php echo $this->_var['consignee']['mobile']; ?></b></li>
                <li><i class="fa fa-map-marker"></i><?php echo $this->_var['consignee']['province_name']; ?><?php echo $this->_var['consignee']['city_name']; ?><?php echo $this->_var['consignee']['district_name']; ?><?php echo $this->_var['consignee']['address']; ?></li>
            <?php else: ?>
                <li>&nbsp;</li>
                <li>请填写配送地址</li>
            <?php endif; ?>
            </ul>
            <input name="address_id" type="hidden" value="<?php if ($this->_var['consignee']): ?><?php echo $this->_var['consignee']['address_id']; ?><?php else: ?>0<?php endif; ?>" />
            <div class="bg"></div>
        </div>
    </div>
</a>