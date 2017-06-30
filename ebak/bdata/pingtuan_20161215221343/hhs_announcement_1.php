<?php
require("../../inc/header.php");

/*
		SoftName : EmpireBak Version 2010
		Author   : wm_chief
		Copyright: Powered by www.phome.net
*/

DoSetDbChar('utf8');
E_D("DROP TABLE IF EXISTS `hhs_announcement`;");
E_C("CREATE TABLE `hhs_announcement` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `add_time` char(10) NOT NULL,
  `is_display` smallint(1) NOT NULL COMMENT '是否显示 1显示  2隐藏',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8");
E_D("replace into `hhs_announcement` values('1','广场功能调整中','广场功能调整中\r\n\r\n广场功能调整中广场功能调整中\r\n\r\n广场功能调整中\r\n\r\n广场功能调整中\r\n\r\n广场功能调整中\r\n\r\n广场功能调整中\r\n\r\n广场功能调整中','1477589193','1');");

require("../../inc/footer.php");
?>