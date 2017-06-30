<?php
define('IN_HHS', true);

require(dirname(__FILE__) . '/includes/init2.php');

include_once('includes/cls_json.php');

$last_time = trim($_REQUEST['serv_time']);
$sql = "select u.uname,u.headimgurl,o.pay_time from ".$hhs->table('order_info')." as o,".$hhs->table('users')." as u where o.pay_time > '".$last_time."' and o.user_id = u.user_id order by order_id desc";
$order = $db->getRow($sql);
$serv_time = gmtime();

$html = empty($order)?'':'<div class="ws-for-push ws-for-push-show">
    <img src="'.$order['headimgurl'].'">
    <span>最新订单来自'.$order['uname'].'，1秒前</span>
</div>';
$result = array('error' => 0, 'message' => '', 'content' => $html, 'serv_time' => $serv_time);
$json  = new JSON;
die($json->encode($result));