<?php
define('IN_HHS', true);


$last_time = trim($_REQUEST['serv_time']);
$sql = "select u.uname,u.headimgurl,o.pay_time from ".$hhs->table('order_info')." as o,".$hhs->table('users')." as u where o.pay_time > '".$last_time."' and o.user_id = u.user_id order by order_id desc";
$order = $db->getRow($sql);
$serv_time = gmtime();

$html['headimgurl'] = $order['headimgurl'];
$html['uname'] = $order['uname'];
$html['serv_time'] = $order['serv_time'];
$result = array('error' => 0, 'message' => '', 'content' => $html, 'serv_time' => $serv_time);
die($json->encode($result));

?>