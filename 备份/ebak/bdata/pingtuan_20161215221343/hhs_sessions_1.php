<?php
require("../../inc/header.php");

/*
		SoftName : EmpireBak Version 2010
		Author   : wm_chief
		Copyright: Powered by www.phome.net
*/

DoSetDbChar('utf8');
E_D("DROP TABLE IF EXISTS `hhs_sessions`;");
E_C("CREATE TABLE `hhs_sessions` (
  `sesskey` char(32) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '',
  `expiry` int(10) unsigned NOT NULL DEFAULT '0',
  `userid` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `adminid` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `ip` char(15) NOT NULL DEFAULT '',
  `user_name` varchar(60) NOT NULL,
  `user_rank` tinyint(3) NOT NULL,
  `discount` decimal(3,2) NOT NULL,
  `email` varchar(60) NOT NULL,
  `data` char(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`sesskey`),
  KEY `expiry` (`expiry`)
) ENGINE=MEMORY DEFAULT CHARSET=utf8");
E_D("replace into `hhs_sessions` values('f229cf46b65b89dbb2f747aaf8ba75e7','1481811184','0','1','127.0.0.1','0','0','0.00','0','a:4:{s:10:\"admin_name\";s:5:\"admin\";s:11:\"action_list\";s:3:\"all\";s:10:\"last_check\";i:1481782239;s:12:\"suppliers_id\";s:1:\"0\";}');");
E_D("replace into `hhs_sessions` values('82b5e7650c7836f44adade736a360bed','1481811012','0','0','127.0.0.1','0','0','1.00','0','a:4:{s:10:\"login_fail\";i:0;s:7:\"site_id\";i:1;s:12:\"access_token\";N;s:17:\"access_token_time\";i:1481811000;}');");

require("../../inc/footer.php");
?>