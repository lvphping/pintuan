<?php
define('IN_HHS', true);

$cat_id = isset($_REQUEST['cid']) ? intval($_REQUEST['cid']) : 0;
$size =isset($_REQUEST['page_size']) ? intval($_REQUEST['page_size']) :10;;
$type =isset($_REQUEST['act']) ? $_REQUEST['act'] : 'hot';
$site_id = $_REQUEST['site_id'];
$goodslist = get_mall_goods($size,$type,$site_id,$_REQUEST['user_id']);


$results['content'] = $goodslist;
echo $json->encode($results);
exit();


function get_mall_goods($size,$type,$site_id,$user_id=''){
	global $redirect_uri,$hhs,$db;
	
    $where   = "g.is_on_sale = 1 AND g.is_alone_sale = 1 AND g.is_delete = 0 AND g.is_hot = 1 ";
    $where  .= $cat_id ?' and g.`cat_id` = "'.$cat_id.'" ':' ';
    $where  .= $type == 'hot' ?' and g.`is_team` = "1" ':' and g.`is_mall` = "1" ';
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


    $sql = 'SELECT g.goods_id, g.goods_name, g.goods_number, g.suppliers_id, g.goods_name_style, g.market_price, g.shop_price , ' .
                'g.promote_start_date, g.promote_end_date, g.goods_brief, g.goods_thumb , g.goods_img,g.little_img ' .
            ' ,g.team_num,g.team_price '.
            'FROM ' . $GLOBALS['hhs']->table('goods') . ' AS g ' .
            "WHERE $where ORDER BY g.`sort_order`, g.goods_id DESC limit ".$size ;
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
        $arr[$idx]['little_img']    =$redirect_uri.$row['little_img'];
		
		
		if(empty($user_id)){
            $arr[$idx]['collect']        = 0;
        }else{
            $sql = "select rec_id,user_id from" . $GLOBALS['hhs']->table('collect_goods')." where user_id=" .$user_id ." and goods_id=".$row['goods_id'];
            $collectInfo = $GLOBALS['db']->getRow($sql);
            $arr[$idx]['collect'] = empty($collectInfo)?"0":"1";
        }		
		
		
		
		
		
		$goods = get_goods_info($row['goods_id']);
		$goods['collect'] =  $arr[$idx]['collect'];
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
