<?php
define('IN_HHS', true);
require(dirname(__FILE__) . '/includes/init2.php');
require(dirname(__FILE__) . '/includes/lib_order.php');

$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;
$user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;
$order = order_info($order_id);
$sql = "select * from ".$hhs->table('order_luck')." where order_id = '".$order_id."'";
$rows = $db->getAll($sql);
if (empty($rows) || empty($order)) {
	
		$result['error'] = 1;
		$result['content'] = '夺宝记录为空';
		die($json->encode($result));
	
	
}
if ($order['pay_status'] != 2) {
	
		$result['error'] = 2;
		$result['content'] = '请先去支付';
		$result['order_id'] = $order_id;
		die($json->encode($result));
	//hhs_header("Location: user.php?act=order_detail&order_id=".$order_id."\n");
	//exit();
}
if($order['user_id'] != $user_id){
		$result['error'] = 3;
		$result['content'] = '请先登录';
		die($json->encode($result));
}
$sql = "select * from ".$hhs->table('order_goods')." where order_id = '".$order_id."'";
$goods = $db->getRow($sql);

$result['error'] = 0;
$result['order'] = $order;
$result['rows'] = $rows;
$result['nums'] = count($rows);
$result['goods'] = $goods;
die($json->encode($result));

