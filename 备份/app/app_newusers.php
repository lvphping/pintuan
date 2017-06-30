<?php
define('IN_HHS', true);


	$page  = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
	$size  = isset($_REQUEST['page_size']) ? intval($_REQUEST['page_size']) :8;
	$page  = $page > 0 ? $page : 1;
	$sort  = isset($_REQUEST['sort']) ? trim($_REQUEST['sort']) : 'sort_order';
	$site_id = $_REQUEST['site_id'];
	$order = '';
	
    $count = get_mall_goods_count($cat_id);
    $max_page = ($count> 0) ? ceil($count / $size) : 1;
    if ($page > $max_page)
    {
        $page = $max_page;
    }
    $goodslist = get_mall_goods($cat_id, $size, $page, $sort, $order,$site_id);
	$results['content'] = $goodslist; 
	
	echo $json->encode($results);
	exit();

/**
 * 获取商品
 * @param  [type] $cat_id [description]
 * @param  [type] $size   [description]
 * @param  [type] $page   [description]
 * @param  [type] $sort   [description]
 * @param  [type] $order  [description]
 * @return [type]         [description]
 */
function get_mall_goods($cat_id, $size, $page, $sort, $order,$site_id){
	global $redirect_uri,$hhs,$db;
    $where   = "g.is_on_sale = 1 AND g.is_alone_sale = 1 AND g.is_delete = 0 AND g.is_fresh = 1 ";
    $where  .= $cat_id ?' and g.`cat_id` = "'.$cat_id.'" ':' ';
    //获得区域级别
	if($site_id)
	{
		$current_region_type=get_region_type($site_id); 
		if($current_region_type<=2){
			 $where.=" and (g.city_id='".$site_id . "' or g.city_id=1) ";
		}elseif($current_region_type==3){
			$where.=" and (g.district_id='".$site_id . "' or g.city_id=1) ";
		}
	}

    $skip     = ($page - 1) * $size;

    $limit = " limit " . $skip . "," . $size;

    $sql = 'SELECT g.goods_id, g.goods_name, g.goods_number, g.suppliers_id, g.goods_name_style, g.market_price, g.shop_price , ' .
                'g.promote_start_date, g.promote_end_date, g.goods_brief, g.goods_thumb , g.goods_img,g.little_img ' .
            ' ,g.team_num,g.team_price '.
            'FROM ' . $GLOBALS['hhs']->table('goods') . ' AS g ' .
            "WHERE $where ORDER BY g.`".$sort."`, g.goods_id DESC" . $limit;
    $res = $GLOBALS['db']->getAll($sql);

    $arr = array();
    foreach ($res AS $idx => $row)
    {
        $arr[$idx]['goods_id']      = $row['goods_id'];
        $arr[$idx]['goods_name']    = $row['goods_name'];
        $arr[$idx]['goods_brief']   = $row['goods_brief'];
        $arr[$idx]['goods_number']  = $row['goods_number'];
        
        $arr[$idx]['market_price']  = app_price_format($row['market_price'],false);
        $arr[$idx]['shop_price']    = app_price_format($row['shop_price'],false);
        
        $arr[$idx]['goods_thumb']   = $redirect_uri.get_image_path($row['goods_id'], $row['goods_thumb'], true);
        $arr[$idx]['goods_img']     = $redirect_uri.get_image_path($row['goods_id'], $row['goods_img']);
        $arr[$idx]['url']           = build_uri('goods', array('gid'=>$row['goods_id']), $row['goods_name']);
        $arr[$idx]['team_num']      = $row['team_num'];
        $arr[$idx]['team_price']    = app_price_format($row['team_price'],false);
        $arr[$idx]['team_discount'] = number_format($row['team_price']/$row['market_price']*10,1);
        $arr[$idx]['little_img']    = $redirect_uri.$row['little_img'];
		
		
		
		
		
		
		$goods = get_goods_info($row['goods_id']);
		$goods['goods_thumb'] = $redirect_uri.$goods['goods_thumb'];
		$goods['goods_img'] = $redirect_uri.$goods['goods_img'];
		$goods['little_img'] = $redirect_uri.$goods['little_img'];
		$goods['goods_desc'] =$redirect_uri.'app/app.php?op=goods_desc&id='.$goods['goods_id'];
		$store_id = $goods['suppliers_id'];
		$sql = "SELECT sum(`sales_num`) FROM ".$hhs->table('goods')." WHERE `suppliers_id` = " .$store_id;
		$sales_num = $db->getOne($sql);
		$sql = "SELECT count(*) FROM  ".$hhs->table('order_goods')." as o,".$hhs->table('goods')." as g WHERE g.`goods_id` = o.`goods_id` and g.`suppliers_id` = " .$store_id;
		$sales_num += $db->getOne($sql);
		$goods['sales_num'] = $sales_num;
		
   		$stores_info = get_suppliers_info($goods['suppliers_id']);
   		$sql = "SELECT count(*) FROM ".$hhs->table('goods')." as g WHERE is_on_sale = 1 AND is_alone_sale = 1 AND is_delete = 0 and  `suppliers_id` = " . $goods['suppliers_id'];
   		$stores_info['goods_num'] = $db->getOne($sql);
    	$sql = "SELECT sum(`sales_num`) FROM ".$hhs->table('goods')." as g WHERE `suppliers_id` = " .$goods['suppliers_id'];
    	$stores_info['sales_num'] = $db->getOne($sql);
    	$sql = "SELECT count(*) FROM  ".$hhs->table('order_goods')." as o,".$hhs->table('goods')." as g WHERE g.`goods_id` = o.`goods_id` and g.`suppliers_id` = " .$goods['suppliers_id'];
   		$stores_info['sales_num'] += $db->getOne($sql);
	    $stores_info['supp_logo'] = $redirect_uri.$stores_info['supp_logo'];
		
		
		
		$list = get_goods_gallery($row['goods_id']);
		foreach($list as $ix=>$v)
		{
			$list[$ix]['img_url'] = $redirect_uri.$v['img_url'];
			$list[$ix]['thumb_url'] = $redirect_uri.$v['thumb_url'];
		}
		$properties = get_goods_properties($row['goods_id']);  // 获得商品的规格和属性
		$comments = assign_comment($row['goods_id'],0,1);
		$goods_info['stores_info'] = $stores_info;
 		$goods_info['info'] = $goods;
		$goods_info['goods_gallery'] = $list;
		$goods_info['properties'] = $properties;
		$goods_info['comments'] = $comments;
		
		
 		$arr[$idx]['info']  = $goods_info;		
		
		
		
		
		
		
		
        unset($row);
    }

    return $arr;
}
function get_mall_goods_count($cat_id,$site_id){
    $where   = "g.is_on_sale = 1 AND g.is_alone_sale = 1 AND g.is_delete = 0 AND g.is_fresh = 1 ";
    $where  .= $cat_id ?' and g.`cat_id` = "'.$cat_id.'" ':' ';
    //获得区域级别
	if($site_id)
	{
		$current_region_type=get_region_type($site_id); 
		if($current_region_type<=2){
			 $where.=" and (g.city_id='".$site_id . "' or g.city_id=1) ";
		}elseif($current_region_type==3){
			$where.=" and (g.district_id='".$site_id . "' or g.city_id=1) ";
		}
	}

    $sql     = "select count(*) FROM ".$GLOBALS['hhs']->table('goods')." as g WHERE " . $where;
    return $GLOBALS['db']->getOne($sql);    
}
?>