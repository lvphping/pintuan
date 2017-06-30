<?php
define('IN_HHS', true);


$site_id = $_REQUEST['site_id'];
$goodslist =  get_goodslist($page,$page_size,$site_id);
$results['content'] = $goodslist;

echo $json->encode($results);
die();
function get_goodslist($page = 1,$pageSize,$site_id)
{
	global $redirect_uri,$hhs,$db;
	
    switch (intval($_REQUEST['orderby'])) {
        case 0:
            $orderby = ',g.goods_id desc';
            break;
        
         case 1:
            $orderby = ',g.goods_number asc ';
            break;
        
         case 2:
            $orderby = ',g.team_num asc';
            break;
        
        default:
            $orderby = '';
            break;
    }
	$where = "g.is_on_sale = 1 AND g.is_alone_sale = 1 AND g.is_delete = 0 AND g.is_luck = 1 ";
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

    //统计页面总数
    $sql    = "select count(*) FROM ".$GLOBALS['hhs']->table('goods')." as g WHERE " . $where;
    $allNum = $GLOBALS['db']->getOne($sql);
    $pages  = ceil($allNum/$pageSize);
    //page 溢出
    $page   = $page<=$pages ? $page : $pages;
    $skip     = ($page - 1) * $pageSize;
	if($skip<0)
	{
		$skip=0;
	}
    $limit = " limit " . $skip . "," . $pageSize;
    $sql = 'SELECT g.team_num,g.goods_number, g.goods_id, g.goods_name, g.goods_number, g.suppliers_id, g.goods_name_style, g.market_price, g.shop_price AS shop_price, ' .
                " g.promote_price, g.goods_type, " .
                'g.promote_start_date, g.promote_end_date, g.goods_brief, g.goods_thumb , g.goods_img,g.little_img ' .
            ' ,g.team_num,g.team_price '.
            'FROM ' . $GLOBALS['hhs']->table('goods') . ' AS g ' .
         //   'LEFT JOIN ' . $GLOBALS['hhs']->table('member_price') . ' AS mp ' .
             //   "ON mp.goods_id = g.goods_id AND mp.user_rank = '$_SESSION[user_rank]' " .
            "WHERE $where ORDER BY g.sort_order ".$orderby." " ;
    $res = $GLOBALS['db']->getAll($sql);

    $arr = array();
    foreach ($res AS $idx => $row)
    {
        $arr[$idx]['goods_name']          = $row['goods_name'];
        $arr[$idx]['goods_brief']       = $row['goods_brief'];
		$arr[$idx]['goods_number']       = $row['goods_number'];
        
        $arr[$idx]['market_price']    = app_price_format($row['market_price'],false);
		$arr[$idx]['shop_price']    = app_price_format($row['shop_price'],false);
		
        $arr[$idx]['goods_thumb']      = $redirect_uri.get_image_path($row['goods_id'], $row['goods_thumb'], true);
	
        $arr[$idx]['goods_img']        = $redirect_uri.get_image_path($row['goods_id'], $row['goods_img']);
        $arr[$idx]['url']              = build_uri('goods', array('gid'=>$row['goods_id']), $row['goods_name']);
        $arr[$idx]['team_num']         = $row['team_num'];
        $arr[$idx]['team_price']       = app_price_format($row['team_price'],false);
        
        $arr[$idx]['team_discount']    = number_format($row['team_price']/$row['market_price']*10,1);
        $arr[$idx]['little_img']        = $redirect_uri.$row['little_img'];

        $arr[$idx]['promote_start_date1']      = ($row['promote_start_date']);
        $arr[$idx]['promote_end_date1']        = ($row['promote_end_date']);

        $arr[$idx]['start_date'] = local_date("Y-m-d H:i:s",$row['promote_start_date']);
        $arr[$idx]['end_date']   = local_date("Y-m-d H:i:s",$row['promote_end_date']);

        $arr[$idx]['promote_start_date']      = strtotime($arr[$idx]['start_date']);
        $arr[$idx]['promote_end_date']        = strtotime($arr[$idx]['end_date']);

        $left_num = $GLOBALS['db']->getOne('SELECT (team_num-teammen_num) as left_num FROM '.$GLOBALS['hhs']->table('order_info').' where goods_id = "'.$row['goods_id'].'" AND pay_status=2 order by order_id desc');
        $left_num = $left_num>0 ? $left_num : $row['team_num'];

        $arr[$idx]['goods_number']       = $left_num;

        $arr[$idx]['left_num']        = ($arr[$idx]['team_num']-$arr[$idx]['goods_number']);
        $arr[$idx]['schedule']        = 100*($arr[$idx]['team_num']-$arr[$idx]['goods_number'])/$arr[$idx]['team_num'];
		
		
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
		
		
    	$left_num = $GLOBALS['db']->getOne('SELECT (team_num-teammen_num) as left_num FROM '.$GLOBALS['hhs']->table('order_info').' where goods_id = "'.$row['goods_id'].'" AND pay_status=2 order by order_id desc');
   		$left_num = $left_num>0 ? $left_num : $goods['team_num'];   
    // $left_num        = ($goods['team_num']-$goods['goods_number']);
   		$schedule        = 100 *(1 - $left_num/$goods['team_num']);
	
		$luck_info['left_num'] = $left_num;
		$luck_info['schedule'] = $schedule;//进度条
		
		
    	$sql = 'select IFNULL(u.uname,u.user_name) as uname,u.headimgurl,o.add_time,o.province, o.city, (select count(*) from '.$hhs->table("order_luck").' as l where l.order_id=o.order_id) as buy_nums from '.$hhs->table("users").' as u,'.$hhs->table("order_info").' as o WHERE u.user_id = o.user_id and o.goods_id = "'.$goods['goods_id'].'" AND o.luck_times = "'.$goods['luck_times'].'" AND o.pay_status = 2';
    // echo $sql;die();
   		$buy_rows = $db->getAll($sql);
   		foreach ($buy_rows as $key => $r) {
        $buy_rows[$key]['add_time']  = local_date("Y-m-d H:i:s",$r['add_time']);
        $buy_rows[$key]['province'] = get_regions_name($r['province']);
        $buy_rows[$key]['city']     = get_regions_name($r['city']);
   		 }
	    $luck_info['buy_rows'] = $buy_rows;//当前正在购买的人
		
		
    	$sql = 'select IFNULL(u.uname,u.user_name) as uname,u.headimgurl,o.add_time,o.province, o.city, (select count(*) from '.$hhs->table("order_luck").' as l where l.order_id=o.order_id) as buy_nums,lk.id as lucker_id,o.luck_times from '.$hhs->table("users").' as u,'.$hhs->table("order_info").' as o,'.$hhs->table("order_luck").' as lk WHERE u.user_id = o.user_id and o.goods_id = "'.$row['goods_id'].'" AND o.pay_status = 2 and o.is_lucker = 1 AND lk.order_id=o.order_id AND lk.is_lucker = 1';
   		$luck_rows = $db->getAll($sql);

   		 foreach ($luck_rows as $key => $rs) {
        $luck_rows[$key]['add_time']  = local_date("Y-m-d H:i:s",$rs['add_time']);
        $luck_rows[$key]['province']  = get_regions_name($rs['province']);
        $luck_rows[$key]['city']      = get_regions_name($rs['city']);
        //开奖时间
        $open_time = $db->getOne("select pay_time from ".$hhs->table("order_info")." WHERE goods_id = '".$goods['goods_id']."' AND pay_status = 2 and luck_times = '".$rs['luck_times']."' order by order_id desc");
        $luck_rows[$key]['open_time'] = local_date("Y-m-d H:i:s",$open_time);
   		 }    
		$luck_info['old_luck_rows'] = $luck_rows;//往期中奖		
		
		$arr[$idx]['luck_info'] = $luck_info;
	
 		$arr[$idx]['info']  = $goods_info;
		
    }
    //下一页是否存在

    //改写返回array
    return $arr;

    // return $arr;
}
function get_regions_name($region_id)
{
    return $GLOBALS['db']->getOne("select region_name from ".$GLOBALS['hhs']->table('region')." where region_id='$region_id'");
}
?>