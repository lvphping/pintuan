<?php
define('IN_HHS', true);

$position_name = $_REQUEST['position_name'];
$num = $_REQUEST['num'];
$ad_width =@$_REQUEST['ad_width']; 
$list = app_get_advlist_position_name($position_name,$num,$ad_width);
$results['content'] = $list;
echo $json->encode($results);
exit();

?>