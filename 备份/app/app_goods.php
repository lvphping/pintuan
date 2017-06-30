<?php
define('IN_HHS', true);

$goods_id = isset($_REQUEST['id'])  ? intval($_REQUEST['id']) : 0;
$user_id = $_REQUEST['user_id'];
if($action =='goods_info')
{
	$goods = get_goods_info($goods_id);
	$goods['goods_thumb'] = $redirect_uri.$goods['goods_thumb'];
	$goods['goods_img'] = $redirect_uri.$goods['goods_img'];
	$goods['little_img'] = $redirect_uri.$goods['little_img'];
	$goods['goods_desc'] = img_url_replace($goods['goods_desc']);
    $store_id = $goods['suppliers_id'];
    $sql = "SELECT sum(`sales_num`) FROM ".$hhs->table('goods')." WHERE `suppliers_id` = " .$store_id;
    $sales_num = $db->getOne($sql);
    $sql = "SELECT count(*) FROM  ".$hhs->table('order_goods')." as o,".$hhs->table('goods')." as g WHERE g.`goods_id` = o.`goods_id` and g.`suppliers_id` = " .$store_id;
    $sales_num += $db->getOne($sql);
	$goods['sales_num'] = $sales_num;
	$results['content'] = $goods;
	echo $json->encode($results);
	exit();

}
elseif($action =='pic_list')//相册
{
	$list = get_goods_gallery($goods_id);
	foreach($list as $idx=>$v)
	{
		$list[$idx]['img_url'] = $redirect_uri.$v['img_url'];
		$list[$idx]['thumb_url'] = $redirect_uri.$v['thumb_url'];
	}
	
	$results['content'] = $list;
	echo $json->encode($results);
	exit();

}
elseif($action =='properties')//属性
{
	
	$properties = get_goods_properties($goods_id);  // 获得商品的规格和属性
	$results['content'] = $properties['spe'];
}
elseif($action =='goods_store_info')//店铺信息
{
	$goods = get_goods_info($goods_id);
    $stores_info = get_suppliers_info($goods['suppliers_id']);
    $sql = "SELECT count(*) FROM ".$hhs->table('goods')." as g WHERE is_on_sale = 1 AND is_alone_sale = 1 AND is_delete = 0 and  `suppliers_id` = " . $goods['suppliers_id'];
    $stores_info['goods_num'] = $db->getOne($sql);
    $sql = "SELECT sum(`sales_num`) FROM ".$hhs->table('goods')." as g WHERE `suppliers_id` = " .$goods['suppliers_id'].$where;
    $stores_info['sales_num'] = $db->getOne($sql);
    $sql = "SELECT count(*) FROM  ".$hhs->table('order_goods')." as o,".$hhs->table('goods')." as g WHERE g.`goods_id` = o.`goods_id` and g.`suppliers_id` = " .$goods['suppliers_id'].$where;
    $stores_info['sales_num'] += $db->getOne($sql);
	$stores_info['supp_logo'] = $redirect_uri.$stores_info['supp_logo'];
	
	
	
	$results['content'] = $stores_info;
	echo $json->encode($results);
	exit();

}
elseif($action =='team_list')//正在进行的团
{
    $sql="select oi.team_sign,oi.pay_time,g.goods_name,g.team_price,u.user_name,u.uname,u.headimgurl, (oi.team_num-oi.teammen_num ) as progress from ".$hhs->table("order_info")." as oi left join "
       .$hhs->table("goods")." as g on oi.extension_id=g.goods_id left join "
       .$hhs->table('users')." as u on oi.user_id=u.user_id where oi.team_first=1 and oi.team_status=1 and oi.pay_status = 2 and oi.extension_code='team_goods' and oi.extension_id='$goods_id' limit 2 ";
       // echo $sql;
    $group_list=$db->getAll($sql);
    $time=gmtime();
    foreach($group_list as $k=>$v){
		
		$group_list[$k]['goods_thumb'] = $redirect_uri.$v['goods_thumb']; 
		$group_list[$k]['goods_img'] = $redirect_uri.$v['goods_img']; 
		$group_list[$k]['little_img'] = $redirect_uri.$v['little_img']; 
		
        $ctime=$group_list[$k]['pay_time']+$_CFG['team_suc_time']*24*3600-$time;
        if($ctime < 0)
        {
            unset($group_list[$k]);
            continue;
        }        
        $hour=intval($ctime/3600);
        
        $minu=intval(($ctime%3600)/60);
        $group_list[$k]['finish_str']="剩余 ".$hour."小时".$minu."分结束";
    }
	

	
	
	if(!empty($group_list))
	{
		$results['error'] =0;
	}
	else
	{
		$results['error'] =1;
	}
	
	
	$results['content'] = $group_list;
	echo $json->encode($results);
	exit();

}
elseif($action =='comment_list')//商品评价
{
	$comments = assign_comment($goods_id,0,1);
	$results['content'] = $group_list;
	echo $json->encode($results);
	exit();
}
elseif($action =='love_goods_list')//可能喜欢的商品
{
	$sql = "select g.shop_price as goods_price,g.team_price,g.goods_name,g.goods_id,g.goods_img,c.rec_id from ".$hhs->table("goods")." as g LEFT JOIN ".$hhs->table("collect_goods")." as c ON  c.user_id='".$user_id."' and c.goods_id=g.goods_id where g.is_on_sale = 1 AND g.is_alone_sale = 1 AND g.is_delete = 0 order by rand() limit 6";
	$rands_goods = $db->getAll($sql);
	foreach($rands_goods as $ids=>$v)
	{
		$rands_goods[$ids]['goods_img'] = $redirect_uri.$v['goods_img']; 
		
		if($v['team_price']=='0.00')
		{
			$rands_goods[$ids]['team_price'] = $v['goods_price'];
		}
	}
	$results['content'] = $rands_goods;
	echo $json->encode($results);
	exit();
}
elseif($action =='luck_info')//夺宝
{
	$goods = get_goods_info($goods_id);
    $left_num = $GLOBALS['db']->getOne('SELECT (team_num-teammen_num) as left_num FROM '.$GLOBALS['hhs']->table('order_info').' where goods_id = "'.$goods['goods_id'].'" AND pay_status=2 order by order_id desc');
    $left_num = $left_num>0 ? $left_num : $goods['team_num'];   
    $goods['goods_number']       = $left_num;
    // $left_num        = ($goods['team_num']-$goods['goods_number']);
    $schedule        = 100 *(1 - $left_num/$goods['team_num']);
	
	$luck_info['left_num'] = $left_num;
	$luck_info['schedule'] = $schedule;//进度条


    $sql = 'select IFNULL(u.uname,u.user_name) as uname,u.headimgurl,o.add_time,o.province, o.city, (select count(*) from '.$hhs->table("order_luck").' as l where l.order_id=o.order_id) as buy_nums from '.$hhs->table("users").' as u,'.$hhs->table("order_info").' as o WHERE u.user_id = o.user_id and o.goods_id = "'.$goods['goods_id'].'" AND o.luck_times = "'.$goods['luck_times'].'" AND o.pay_status = 2';
    // echo $sql;die();
    $buy_rows = $db->getAll($sql);
    foreach ($buy_rows as $key => $row) {
        $buy_rows[$key]['add_time']  = local_date("Y-m-d H:i:s",$row['add_time']);
        $buy_rows[$key]['province'] = get_regions_name($row['province']);
        $buy_rows[$key]['city']     = get_regions_name($row['city']);
    }
	$luck_info['buy_rows'] = $buy_rows;//当前正在购买的人
	
    $sql = 'select IFNULL(u.uname,u.user_name) as uname,u.headimgurl,o.add_time,o.province, o.city, (select count(*) from '.$hhs->table("order_luck").' as l where l.order_id=o.order_id) as buy_nums,lk.id as lucker_id,o.luck_times from '.$hhs->table("users").' as u,'.$hhs->table("order_info").' as o,'.$hhs->table("order_luck").' as lk WHERE u.user_id = o.user_id and o.goods_id = "'.$goods['goods_id'].'" AND o.pay_status = 2 and o.is_lucker = 1 AND lk.order_id=o.order_id AND lk.is_lucker = 1';
    $luck_rows = $db->getAll($sql);

    foreach ($luck_rows as $key => $row) {
        $luck_rows[$key]['add_time']  = local_date("Y-m-d H:i:s",$row['add_time']);
        $luck_rows[$key]['province']  = get_regions_name($row['province']);
        $luck_rows[$key]['city']      = get_regions_name($row['city']);
        //开奖时间
        $open_time = $db->getOne("select pay_time from ".$hhs->table("order_info")." WHERE goods_id = '".$goods['goods_id']."' AND pay_status = 2 and luck_times = '".$row['luck_times']."' order by order_id desc");
        $luck_rows[$key]['open_time'] = local_date("Y-m-d H:i:s",$open_time);
    }    
	$luck_info['luck_rows'] = $luck_rows;//往期中奖
	$luck_info['goods_info'] = $goods_info;
	$results['content'] = $luck_info;
	
	echo $json->encode($results);
	exit();
	
}
elseif($action =='goods_price')//根据数量改变价格
{
	
	
    $attr_id    = isset($_REQUEST['attr']) ? explode(',', $_REQUEST['attr']) : array();
    $number     = (isset($_REQUEST['number'])) ? intval($_REQUEST['number']) : 1;

    if ($goods_id == 0)
    {
        $res['err_msg'] = $_LANG['err_change_attr'];
        $res['err_no']  = 1;
    }
    else
    {
        $goods = $db->getRow("select limit_buy_bumber,team_price,shop_price,promote_price,promote_start_date,promote_end_date,is_miao,goods_number from ".$hhs->table('goods')." where goods_id='$goods_id'");
        $limit_buy_bumber = $goods['limit_buy_bumber'];
        if ($number == 0)
        {
            $res['qty'] = $number = 1;
        }
        else
        {
            $res['qty'] = $number;
        }
        $res['goods_number'] = $goods['goods_number'];
        if ($attr_id) {
            $sql = "SELECT * FROM " .$GLOBALS['hhs']->table('products'). " WHERE goods_id = '$goods_id' LIMIT 0, 1";
            $prod = $GLOBALS['db']->getRow($sql);            
            if (is_spec($attr_id) && !empty($prod))
            {
                $product_info = get_products_info($goods_id, $attr_id);
            }
            if (empty($product_info))
            {
                $product_info = array('product_number' => '', 'product_id' => 0);
            }
            $res = array_merge($res,$product_info);
        }
        // 秒殺價格修正
        if ($goods['is_miao']) {
            $promote_price = bargain_price($goods['promote_price'], $goods['promote_start_date'], $goods['promote_end_date']);
            if($promote_price>0)
            {
                $goods['team_price'] = $goods['promote_price'];
            }
        }        
        if($number>$limit_buy_bumber&&$limit_buy_bumber>0)
        {
            
            $res['err_msg'] = '购买数量不可大于限购数量';
            $shop_price  = get_final_price($goods_id, $limit_buy_bumber, true, $attr_id);
            $res['result'] = app_price_format($shop_price * $limit_buy_bumber);
            $res['number'] = $limit_buy_bumber;
            if ($goods['team_price']>0) {
                $attr_price  = spec_price($attr_id,true);
                $team_price  = $goods['team_price'] + $attr_price;
                $res['team_price'] = app_price_format($team_price * $limit_buy_bumber);
            }
            die($json->encode($res)); 
        }
        else
        {
            $shop_price  = get_final_price($goods_id, $number, true, $attr_id);
            $res['result'] = app_price_format($shop_price * $number);
            if ($goods['team_price']>0) {
                $attr_price  = spec_price($attr_id,true);
                $team_price  = $goods['team_price'] + $attr_price;
                $res['team_price'] = app_price_format($team_price * $number);
            }
            die($json->encode($res)); 
        }
    }
    die($json->encode($res));
}

/**
 * 获得商品选定的属性的附加总价格
 *
 * @param   integer     $goods_id
 * @param   array       $attr
 *
 * @return  void
 */
function get_attr_amount($goods_id, $attr)
{
    $sql = "SELECT SUM(attr_price) FROM " . $GLOBALS['hhs']->table('goods_attr') .
        " WHERE goods_id='$goods_id' AND " . db_create_in($attr, 'goods_attr_id');

    return $GLOBALS['db']->getOne($sql);
}
//function get_buy_sum($goods_id)
//{
//    $sql = 'SELECT IFNULL(SUM(g.goods_number), 0) ' .
//        'FROM ' . $GLOBALS['hhs']->table('order_info') . ' AS o, ' .
//            $GLOBALS['hhs']->table('order_goods') . ' AS g ' .
//        "WHERE o.order_id = g.order_id " .
//        "AND o.order_status  in (0,1,5)  ".
//        " AND o.shipping_status in (0,1,2) "  .
//        " AND o.pay_status in (1,2) ".
//        " AND g.goods_id = ".$goods_id;
//    
//    return $GLOBALS['db']->getOne($sql);
//}

function get_regions_name($region_id)
{
    return $GLOBALS['db']->getOne("select region_name from ".$GLOBALS['hhs']->table('region')." where region_id='$region_id'");
}
?>