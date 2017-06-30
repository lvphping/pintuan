<?php
require("../../inc/header.php");

/*
		SoftName : EmpireBak Version 2010
		Author   : wm_chief
		Copyright: Powered by www.phome.net
*/

DoSetDbChar('utf8');
E_D("DROP TABLE IF EXISTS `hhs_admin_log`;");
E_C("CREATE TABLE `hhs_admin_log` (
  `log_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `log_time` int(10) unsigned NOT NULL DEFAULT '0',
  `user_id` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `log_info` varchar(255) NOT NULL DEFAULT '',
  `ip_address` varchar(15) NOT NULL DEFAULT '',
  PRIMARY KEY (`log_id`),
  KEY `log_time` (`log_time`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8");
E_D("replace into `hhs_admin_log` values('1','1481671795','1','编辑权限管理: admin','127.0.0.1');");
E_D("replace into `hhs_admin_log` values('2','1481671896','1','编辑商店设置: ','127.0.0.1');");
E_D("replace into `hhs_admin_log` values('3','1481672106','1','编辑权限管理: admin','127.0.0.1');");
E_D("replace into `hhs_admin_log` values('4','1481782281','1','编辑权限管理: admin','127.0.0.1');");
E_D("replace into `hhs_admin_log` values('5','1481782333','1','编辑商店设置: ','127.0.0.1');");
E_D("replace into `hhs_admin_log` values('6','1481782374','1','编辑商店设置: ','127.0.0.1');");

require("../../inc/footer.php");
?>