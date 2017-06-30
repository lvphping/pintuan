<?php
require("../../inc/header.php");

/*
		SoftName : EmpireBak Version 2010
		Author   : wm_chief
		Copyright: Powered by www.phome.net
*/

DoSetDbChar('utf8');
E_D("DROP TABLE IF EXISTS `hhs_settlement_action`;");
E_C("CREATE TABLE `hhs_settlement_action` (
  `action_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `settlement_id` int(10) unsigned NOT NULL DEFAULT '0',
  `action_user` varchar(30) NOT NULL DEFAULT '',
  `status` tinyint(4) unsigned NOT NULL DEFAULT '0',
  `action_note` text NOT NULL,
  `log_time` int(11) unsigned NOT NULL DEFAULT '0',
  `settlement_sn` varchar(30) DEFAULT NULL COMMENT '结算编号',
  PRIMARY KEY (`action_id`),
  KEY `settlement_id` (`settlement_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8");

require("../../inc/footer.php");
?>