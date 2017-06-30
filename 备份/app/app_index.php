<?php
define('IN_HHS', true);

$site_name = $_REQUEST['site_id'];
$city_id = get_str_replace_region_name(2,$site_name);

$id = $GLOBALS['db']->getOne('select id from ' . $GLOBALS['hhs']->table('site') . " where city_id='{$city_id}' and close=1");
if($id) {
	
	$results['site_id'] = $city_id;
	$results['site_content'] = '0';
} else {
	$results['site_id'] = '1';
	$results['site_content'] = '1';
}


if($action =='team_list' )
{
	$size = $_REQUEST['page_size'];
	$site_id =$_REQUEST['site_id']; 
	$list = get_apptypeof_goods('team',$_CFG['index_show_team_num'],true,$site_id,$_REQUEST['user_id']);
	$results['error'] = 0;
	$results['content']= $list;
}
if($action =='mall' )
{
	$size = $_REQUEST['page_size'];
	$site_id =$_REQUEST['site_id']; 
	$list = get_apptypeof_goods('mall',$_CFG['index_show_mall_num'],true,$site_id);
	$results['error'] = 0;
	$results['content']= $list;
}
if($action =='zero' )
{
	$size = $_REQUEST['page_size'];
	$site_id =$_REQUEST['site_id']; 
	$list = get_apptypeof_goods('mall',$size,true,$site_id);
	$results['error'] = 0;
	$results['content']= $list;
}


echo $json->encode($results);
die();
//function get_str_replace_region_name($type,$name)
//{
//	
//        if ($type == 1) {
//            $region_name = str_replace(array('省', '市', '自治区', '回族', '地区'), '', $name);
//		
//            $sql = 'SELECT `region_id` from ' . $GLOBALS['hhs']->table('region') . " where `region_name` = '{$region_name}' and region_type ='{$type}'";
//            return $GLOBALS['db']->getOne($sql);
//        } elseif ($type == 2) {
//            $region_name = str_replace(array('省', '市', '自治区', '回族', '地区'), '', $name);
//            $sql = 'SELECT `region_id` from ' . $GLOBALS['hhs']->table('region') . " where `region_name` = '{$region_name}'";
//            return $GLOBALS['db']->getOne($sql);
//        }
//		else
//		{
//            $sql = 'SELECT `region_id` from ' . $GLOBALS['hhs']->table('region') . " where `region_name` = '{$name}'";
//            return $GLOBALS['db']->getOne($sql);
//		}
//	
//}
function get_apptypeof_goods($type,$pageSize = 12, $is_best = false,$site_id,$user_id=''){
	global $redirect_uri,$hhs,$db; 
    if (! in_array($type, array('zero','mall','team','luck','miao'))) {
        return array();
    }    
    $where = "g.is_on_sale = 1 AND g.is_alone_sale = 1 AND g.is_delete = 0 AND g.is_".$type." = 1 ";
    //获得区域级别
    $current_region_type=get_region_type($site_id); 
    if($current_region_type<=2){
         $where.=" and (g.city_id='".$site_id . "' or g.city_id=1) ";
    }elseif($current_region_type==3){
        $where.=" and (g.district_id='".$site_id . "' or g.city_id=1) ";
    }   
    if($is_best){
        $where.=" and g.`is_best` = 1";
    }
    
    $limit = " limit " . $pageSize;
    
    $sql = 'SELECT g.goods_id,g.cat_id, g.goods_name, g.goods_number, g.suppliers_id, g.goods_name_style, g.market_price, g.shop_price, g.ts_a, g.ts_b, g.ts_c , ' .
                'g.promote_start_date, g.promote_end_date, g.goods_brief, g.goods_thumb , g.goods_img,g.little_img ' .
            ' ,g.team_num,g.team_price '.
            'FROM ' . $GLOBALS['hhs']->table('goods') . ' AS g ' .
            "WHERE $where ORDER BY g.sort_order, g.goods_id DESC" . $limit;
    $res = $GLOBALS['db']->getAll($sql);

    $arr = array();
    foreach ($res AS $idx => $row)
    {
        $arr[$idx]['goods_id']          = $row['goods_id'];
        $arr[$idx]['goods_name']          = $row['goods_name'];
        $arr[$idx]['goods_brief']       = $row['goods_brief'];
        $arr[$idx]['goods_number']       = $row['goods_number'];
		$arr[$idx]['ts_a']       = $row['ts_a'];
		$arr[$idx]['ts_b']       = $row['ts_b'];
		$arr[$idx]['ts_c']       = $row['ts_c'];
		$arr[$idx]['cat_id']       = $row['cat_id'];
        
        $arr[$idx]['market_price']    = app_price_format($row['market_price'],false);
        $arr[$idx]['shop_price']    = app_price_format($row['shop_price'],false);
        
        $arr[$idx]['goods_thumb']      = $redirect_uri.get_image_path($row['goods_id'], $row['goods_thumb'], true);
        $arr[$idx]['goods_img']        = $redirect_uri.get_image_path($row['goods_id'], $row['goods_img']);
        $arr[$idx]['url']              = build_uri('goods', array('gid'=>$row['goods_id']), $row['goods_name']);
        $arr[$idx]['team_num']    = $row['team_num'];
        $arr[$idx]['team_price']    = app_price_format($row['team_price'],false);
        $arr[$idx]['team_discount']    = number_format($row['team_price']/$row['market_price']*10,1);
        $arr[$idx]['little_img']        = $redirect_uri.$row['little_img'];
		
		
		
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
    }  
    return $arr;  
}
