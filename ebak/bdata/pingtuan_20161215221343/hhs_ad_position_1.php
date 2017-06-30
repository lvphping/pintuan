<?php
require("../../inc/header.php");

/*
		SoftName : EmpireBak Version 2010
		Author   : wm_chief
		Copyright: Powered by www.phome.net
*/

DoSetDbChar('utf8');
E_D("DROP TABLE IF EXISTS `hhs_ad_position`;");
E_C("CREATE TABLE `hhs_ad_position` (
  `position_id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `position_name` varchar(60) NOT NULL DEFAULT '',
  `ad_width` smallint(5) unsigned NOT NULL DEFAULT '0',
  `ad_height` smallint(5) unsigned NOT NULL DEFAULT '0',
  `position_desc` varchar(255) NOT NULL DEFAULT '',
  `position_style` text NOT NULL,
  PRIMARY KEY (`position_id`)
) ENGINE=MyISAM AUTO_INCREMENT=27 DEFAULT CHARSET=utf8");
E_D("replace into `hhs_ad_position` values('1','首页轮播广告位','600','400','首页轮播专区展示广告','');");
E_D("replace into `hhs_ad_position` values('2','新人专属banner','600','400','新人专区展示广告','');");
E_D("replace into `hhs_ad_position` values('3','夺宝专区展示广告','500','300','夺宝专区展示广告','');");
E_D("replace into `hhs_ad_position` values('8','积分兑换广告位','640','300','','');");
E_D("replace into `hhs_ad_position` values('9','111','11','11','11','');");
E_D("replace into `hhs_ad_position` values('10','app欢迎页','640','960','app启动页','');");
E_D("replace into `hhs_ad_position` values('17','app启动页','640','960','app启动页','');");
E_D("replace into `hhs_ad_position` values('24','首页5图_1元夺宝','220','326','','');");
E_D("replace into `hhs_ad_position` values('25','首页5图_4小图','220','162','','');");
E_D("replace into `hhs_ad_position` values('26','首页抽奖广告','640','165','','');");

require("../../inc/footer.php");
?>