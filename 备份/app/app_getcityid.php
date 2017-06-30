<?php
define('IN_HHS', true);

$name = $_REQUEST['city_name'];
$city_id = get_str_replace_region_name(2,$name);

$id = $GLOBALS['db']->getOne('select id from ' . $GLOBALS['hhs']->table('site') . " where city_id='{$city_id}' and close=1");
if ($id) {
	
	$results['city_id'] = $id;
} else {
	$results['city_id'] = 1;
	$results['content'] = '城市暂未开通';
}
echo $json->encode($results);
exit();
?>