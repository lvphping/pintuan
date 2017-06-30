<?php
require("../../inc/header.php");

/*
		SoftName : EmpireBak Version 2010
		Author   : wm_chief
		Copyright: Powered by www.phome.net
*/

DoSetDbChar('utf8');
E_D("DROP TABLE IF EXISTS `hhs_supp_action`;");
E_C("CREATE TABLE `hhs_supp_action` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `action` varchar(100) NOT NULL,
  `action_name` varchar(30) NOT NULL,
  `action_link` varchar(30) NOT NULL,
  `action_act` varchar(30) NOT NULL,
  `parent_id` varchar(30) NOT NULL,
  `sort` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=43 DEFAULT CHARSET=utf8");
E_D("replace into `hhs_supp_action` values('1','goods_manage','商品管理','','','0','1');");
E_D("replace into `hhs_supp_action` values('2','goods_list','商品列表','my_goods','','1','0');");
E_D("replace into `hhs_supp_action` values('3','goods_add','新增商品','add_goods','','1','0');");
E_D("replace into `hhs_supp_action` values('5','order_manage','订单管理','','','0','2');");
E_D("replace into `hhs_supp_action` values('6','order_list','发货订单','goods_order','','5','0');");
E_D("replace into `hhs_supp_action` values('8','accounts_manage','结算管理','','','','3');");
E_D("replace into `hhs_supp_action` values('9','supp_manage','商家管理','','','','4');");
E_D("replace into `hhs_supp_action` values('11','supp_config','系统设置','','','','7');");
E_D("replace into `hhs_supp_action` values('14','supp_info','店铺设置','supp_info','','9','0');");
E_D("replace into `hhs_supp_action` values('15','edit_password','密码修改','edit_password','','11','0');");
E_D("replace into `hhs_supp_action` values('16','supp_account_list','子账号列表','supp_account_list','','10','0');");
E_D("replace into `hhs_supp_action` values('17','add_supp_account','新建账号','add_supp_account','','10','0');");
E_D("replace into `hhs_supp_action` values('18','delivery_list','提货管理','delivery_list','','5','4');");
E_D("replace into `hhs_supp_action` values('19','bank_config','开户行设置','bank_config','','8','0');");
E_D("replace into `hhs_supp_action` values('20','my_order','订单结算','my_order','','8','0');");
E_D("replace into `hhs_supp_action` values('28','statistical','统计报表','statistical','','0','8');");
E_D("replace into `hhs_supp_action` values('29','order_stats','订单统计','order_stats','order_stats','28','0');");
E_D("replace into `hhs_supp_action` values('31','sale_list','销售明细','sale_list','sale_list','28','0');");
E_D("replace into `hhs_supp_action` values('32','sale_order','销售排行','sale_order','sale_order','28','0');");
E_D("replace into `hhs_supp_action` values('35','factoryauthorized','厂家授权','factoryauthorized','factoryauthorized','38','0');");
E_D("replace into `hhs_supp_action` values('36','goods_trash','商品回收站','goods_trash','goods_trash','1','0');");
E_D("replace into `hhs_supp_action` values('37','bunus','优惠券管理','bunus','bunus','9','0');");
E_D("replace into `hhs_supp_action` values('39','trademark','商标注册','trademark','trademark','38','0');");
E_D("replace into `hhs_supp_action` values('40','my_qualification','我的资质','my_qualification','my_qualification','38','0');");
E_D("replace into `hhs_supp_action` values('41','ad','广告轮播','ad','ad','9','0');");
E_D("replace into `hhs_supp_action` values('42','supp_shipping','配送方式','supp_shipping','supp_shipping','9','0');");

require("../../inc/footer.php");
?>