<?php
define('IN_HHS', true);

$hangye_id = isset($_REQUEST['id'])  ? intval($_REQUEST['id']) : 0;
if($action =='store_list')
{
	
	$store_list = store_list($hangye_id);
	$results['content'] = $store_list;
}
elseif($action =='category_list')
{
	$hangye = getHangye();
	$results['content'] = $hangye;
}
    
echo $json->encode($results);
exit();

function store_list($hangye_id=0)
{
	global $redirect_uri,$hhs,$db; 
	$where = " where is_check=1  ";
	if($hangye_id>0){
		$children = getHangye_children($hangye_id);
		$str="".$hangye_id;
		if(!empty($children)){
			$str.= ",".implode(',',$children);
			
		}
		$where .=" and hangye_id in (".$str .")" ;
	}
    $sql = "select suppliers_id,suppliers_name,supp_logo,suppliers_desc,hangye_id,province_id,city_id from ".$GLOBALS['hhs']->table('suppliers').$where." order by sort_order ";
	$res = $GLOBALS['db']->getAll($sql);
	

	foreach ($res AS $k=>$row)
	{
		$res[$k]['province_id'] = get_region_name($row['province_id']);
		$res[$k]['city_id'] = get_region_name($row['city_id']);
		
		$res[$k]['supp_logo'] = $redirect_uri.$row['supp_logo'];
		$sql = "SELECT count(*) FROM ".$GLOBALS['hhs']->table('goods')." WHERE is_on_sale = 1 AND is_alone_sale = 1 AND is_delete = 0 and  `suppliers_id` = " . $row['suppliers_id'];
		$res[$k]['goods_num'] = $GLOBALS['db']->getOne($sql);
		$sql = "SELECT sum(`sales_num`) FROM ".$GLOBALS['hhs']->table('goods')." WHERE `suppliers_id` = " .$row['suppliers_id'];
		$res[$k]['sales_num'] = $GLOBALS['db']->getOne($sql);
		$sql = "SELECT count(*) FROM  ".$GLOBALS['hhs']->table('order_goods')." as o,".$GLOBALS['hhs']->table('goods')." as g WHERE g.`goods_id` = o.`goods_id` and g.`suppliers_id` = " .$row['suppliers_id'];
		$res[$k]['sales_num'] += $GLOBALS['db']->getOne($sql);
	
		$suppliers_id = $row['suppliers_id'];
		$stores_info = get_suppliers_info($row['suppliers_id']);
		$sql = "SELECT count(*) FROM ".$hhs->table('goods')." as g WHERE is_on_sale = 1 AND is_alone_sale = 1 AND is_delete = 0 and  `suppliers_id` = " . $suppliers_id;
		$stores_info['goods_num'] = $db->getOne($sql);
		$sql = "SELECT sum(`sales_num`) FROM ".$hhs->table('goods')." as g WHERE `suppliers_id` = " .$suppliers_id ;
		$stores_info['sales_num'] = $db->getOne($sql);
		$sql = "SELECT count(*) FROM  ".$hhs->table('order_goods')." as o,".$hhs->table('goods')." as g WHERE g.`goods_id` = o.`goods_id` and g.`suppliers_id` = " .$suppliers_id;
		$stores_info['sales_num'] += $db->getOne($sql);
		$stores_info['supp_logo'] = $redirect_uri.$stores_info['supp_logo'];
		
		
		$stores_info['qrcode'] = $redirect_uri."qrcode.php?act=store&id=".$suppliers_id;


		$sql = "select * from ".$hhs->table('supp_photo')." where is_check = 1 AND supp_id = ".$suppliers_id;
		$supp_photo = $db->getAll($sql);
		foreach($supp_photo as $idx=>$v)
		{
			$supp_photo[$idx]['photo_file'] = $redirect_uri.$v['photo_file'];
		}
		
		$res[$k]['supp_photo'] = $supp_photo;
		$res[$k]['stores_info'] = $stores_info;
	
	}
    return $res;
}
function getHangye($pid=0)
{
	return $GLOBALS['db']->getAll("select a.*,(SELECT COUNT(*) FROM ".$GLOBALS['hhs']->table('suppliers')." AS b WHERE b.hangye_id=a.id) AS num from ".$GLOBALS['hhs']->table('hangye')." as a where a.pid= ".$pid);
}
function getHangye_children($pid=0)
{
	return $GLOBALS['db']->getCol("select id from ".$GLOBALS['hhs']->table('hangye')." as a where a.pid= ".$pid);
}


?>