<?php
define('IN_HHS', true);

$goods_id = isset($_REQUEST['id'])  ? intval($_REQUEST['id']) : 0;

$goods = get_goods_info($goods_id);
$goods['goods_desc'] = img_url_replace($goods['goods_desc']);
echo $goods['goods_desc'];
?>
