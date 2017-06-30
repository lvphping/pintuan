<?php
require("../../inc/header.php");

/*
		SoftName : EmpireBak Version 2010
		Author   : wm_chief
		Copyright: Powered by www.phome.net
*/

DoSetDbChar('utf8');
E_D("DROP TABLE IF EXISTS `hhs_ad`;");
E_C("CREATE TABLE `hhs_ad` (
  `ad_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `position_id` smallint(5) unsigned NOT NULL DEFAULT '0',
  `media_type` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `ad_name` varchar(60) NOT NULL DEFAULT '',
  `ad_link` varchar(255) NOT NULL DEFAULT '',
  `ad_code` text NOT NULL,
  `start_time` int(11) NOT NULL DEFAULT '0',
  `end_time` int(11) NOT NULL DEFAULT '0',
  `link_man` varchar(60) NOT NULL DEFAULT '',
  `link_email` varchar(60) NOT NULL DEFAULT '',
  `link_phone` varchar(60) NOT NULL DEFAULT '',
  `click_count` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `enabled` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `city_id` int(10) NOT NULL,
  `order_sort` int(10) NOT NULL DEFAULT '0',
  `ad_width` int(30) NOT NULL COMMENT 'app用',
  PRIMARY KEY (`ad_id`),
  KEY `position_id` (`position_id`),
  KEY `enabled` (`enabled`)
) ENGINE=MyISAM AUTO_INCREMENT=57 DEFAULT CHARSET=utf8");
E_D("replace into `hhs_ad` values('19','1','0','4','http://wfx.hostadmin.com.cn/goods.php?id=392','1466565037602683903.jpg','-28800','-28800','','','','0','1','311','0','0');");
E_D("replace into `hhs_ad` values('20','1','0','2','http://wfx.hostadmin.com.cn/goods.php?id=460','1466565046946455376.jpg','-28800','-28800','','','','0','1','220','3','0');");
E_D("replace into `hhs_ad` values('17','1','0','1','','1466565015994465549.jpg','-28800','-28800','','','','0','1','0','2','0');");
E_D("replace into `hhs_ad` values('18','1','0','3','','1466565028385085066.jpg','-28800','-28800','','','','0','1','53','0','0');");
E_D("replace into `hhs_ad` values('6','3','0','夺宝','','1462991657961063481.jpg','-28800','-28800','','','','0','1','0','2','0');");
E_D("replace into `hhs_ad` values('22','8','0','积分兑换广告','','1467072734197248971.jpg','-28800','-28800','','','','0','1','0','0','0');");
E_D("replace into `hhs_ad` values('10','3','0','夺宝测试','http://wfx.hostadmin.com.cn/goods.php?id=403','1462991613184644685.png','-28800','-28800','','','','0','1','0','0','0');");
E_D("replace into `hhs_ad` values('24','10','0','start_1_640×1136','','1471023657527615129.jpg','-28800','-28800','','','','0','1','0','1','0');");
E_D("replace into `hhs_ad` values('25','10','0','start_2_640×1136','','1471023634723120409.jpg','-28800','-28800','','','','0','1','0','2','0');");
E_D("replace into `hhs_ad` values('26','10','0','start_3_640×1136','','1471023621677615792.jpg','-28800','-28800','','','','0','1','0','3','0');");
E_D("replace into `hhs_ad` values('27','17','0','启动页_640×1136','','1471023807766380580.jpg','-28800','-28800','','','','0','1','0','0','0');");
E_D("replace into `hhs_ad` values('29','17','0','启动页_640×960','','1471024065292909690.jpg','-28800','-28800','','','','0','1','0','0','0');");
E_D("replace into `hhs_ad` values('30','17','0','启动页_1080×1920','','1471024423126472094.jpg','-28800','-28800','','','','0','1','0','0','0');");
E_D("replace into `hhs_ad` values('31','17','0','启动页_750×1334','','1471024444997054118.jpg','-28800','-28800','','','','0','1','0','0','1334');");
E_D("replace into `hhs_ad` values('32','17','0','启动页_720×1280','','1471024476617203749.jpg','-28800','-28800','','','','0','1','0','0','960');");
E_D("replace into `hhs_ad` values('33','17','0','启动页_480×800','','1471024507254801078.jpg','-28800','-28800','','','','0','1','0','0','0');");
E_D("replace into `hhs_ad` values('34','17','0','启动页_1242×2208','','1471025594549159817.jpg','-28800','-28800','','','','0','1','0','0','0');");
E_D("replace into `hhs_ad` values('35','10','0','start_1_640×960','','1471025772070466486.jpg','-28800','-28800','','','','0','1','0','1','960');");
E_D("replace into `hhs_ad` values('36','10','0','start_2_640×960','','1471025800253721785.jpg','-28800','-28800','','','','0','1','0','2','960');");
E_D("replace into `hhs_ad` values('37','10','0','start_3_640×960','','1471025816751148029.jpg','-28800','-28800','','','','0','1','0','3','960');");
E_D("replace into `hhs_ad` values('38','10','0','start_1_1080x1920','','1471026250693046746.jpg','-28800','-28800','','','','0','1','0','1','0');");
E_D("replace into `hhs_ad` values('39','10','0','start_2_1080x1920','','1471026267067453880.jpg','-28800','-28800','','','','0','1','0','2','0');");
E_D("replace into `hhs_ad` values('40','10','0','start_3_1080x1920','','1471026283948583516.jpg','-28800','-28800','','','','0','1','0','3','0');");
E_D("replace into `hhs_ad` values('41','10','0','start_1_1242x2208','','1471026312305167148.jpg','-28800','-28800','','','','0','1','0','1','0');");
E_D("replace into `hhs_ad` values('42','10','0','start_2_1242x2208','','1471026329567818289.jpg','-28800','-28800','','','','0','1','0','2','0');");
E_D("replace into `hhs_ad` values('43','10','0','start_3_1242x2208','','1471026358967663689.jpg','-28800','-28800','','','','0','1','0','3','0');");
E_D("replace into `hhs_ad` values('45','24','0','首页5图-1元夺宝','lottery.php','1472437544137395787.png','-28800','-28800','','','','0','1','0','0','0');");
E_D("replace into `hhs_ad` values('46','25','0','首页5图-0元购物','zero.php','1472437220120043762.png','-28800','-28800','','','','0','1','0','0','0');");
E_D("replace into `hhs_ad` values('47','25','0','首页5图-店铺街','store_list.php','1472437197707930297.png','-28800','-28800','','','','0','1','55','0','0');");
E_D("replace into `hhs_ad` values('48','25','0','首页5图-新人专享','newusers.php','1472437167322487876.png','-28800','-28800','','','','0','1','0','0','0');");
E_D("replace into `hhs_ad` values('49','25','0','首页5图-热卖榜单','rank.php','1473700145108419128.png','-28800','-28800','','','','0','1','0','0','0');");
E_D("replace into `hhs_ad` values('50','2','0','新人专属banner','','1472086812282164314.jpg','-28800','-28800','','','','0','1','0','0','0');");
E_D("replace into `hhs_ad` values('51','1','0','7','http://wfx.hostadmin.com.cn/goods.php?id=465','1472423541791535487.jpg','-28800','-28800','','','','0','1','52','0','0');");
E_D("replace into `hhs_ad` values('52','1','0','8','http://wfx.hostadmin.com.cn/goods.php?id=463','1472423553217467490.jpg','-28800','-28800','','','','0','1','52','0','0');");
E_D("replace into `hhs_ad` values('53','1','0','6','http://wfx.hostadmin.com.cn/goods.php?id=461','1472423564976946378.jpg','-28800','-28800','','','','0','1','0','0','0');");
E_D("replace into `hhs_ad` values('54','1','0','5','http://wfx.hostadmin.com.cn/goods.php?id=485','1472423574475044173.jpg','-28800','-28800','','','','0','1','0','0','0');");
E_D("replace into `hhs_ad` values('56','26','0','首页抽奖广告','luckdraw.php','1476815985172795511.jpg','-28800','-28800','','','','0','1','52','0','0');");

require("../../inc/footer.php");
?>