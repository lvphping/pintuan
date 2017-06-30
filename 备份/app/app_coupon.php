<?php
define('IN_HHS', true);

$quanList = quanList();
$results['content'] = $quanList;

echo $json->encode($results);
exit();


function quanList(){
	global $db,$hhs;

	$sql = "select b.`type_id`,b.`type_name`,b.`type_money`,b.`min_goods_amount`,b.`use_start_date`,b.`use_end_date`,s.`suppliers_name`
	 		from ".$hhs->table('bonus_type')." as b 
	 		left join ".$hhs->table('suppliers')."  as s ON s.`suppliers_id` = b.`suppliers_id`
	 		where b.`is_online` = 1 and b.`send_end_date` > " . time() ;
	$rows = $db->getAll($sql);
	
	foreach ($rows as $key => $row) {
		$rows[$key]['use_start_date'] = date("Y-m-d",$row['use_start_date']);
		$rows[$key]['use_end_date']   = date("Y-m-d",$row['use_end_date']);
		$rows[$key]['stamp']   = rand(1,4);
		if(empty($row['suppliers_name']))
		$rows[$key]['suppliers_name']   = '自营店';
	}
	return $rows;
}


