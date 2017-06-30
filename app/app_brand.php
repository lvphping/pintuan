<?php
define('IN_HHS', true);
if (!empty($_REQUEST['id']))
{
    $brand_id = intval($_REQUEST['id']);
}
else
{
	$results['error'] =1;
	$results['content'] ='品牌id不能为空';
	echo $json->encode($results);
	exit();
}

$brand_info = get_brand_info($brand_id);
$brand_info['brand_logo'] = $redirect_uri.'data/brandlogo/'.$brand_info['brand_logo'];


$goodslist = brand_get_goods($brand_id);

if($goodslist)
{
	$results['error'] =1;	
}
else
{
	$results['error'] =0;	
}
$results['brand_info'] = $brand_info;
$results['content'] = $goodslist;

echo $json->encode($results);
exit();


function get_brand_info($id)
{
    $sql = 'SELECT * FROM ' . $GLOBALS['hhs']->table('brand') . " WHERE brand_id = '$id'";

    return $GLOBALS['db']->getRow($sql);
}


/**
 * 获得品牌下的商品
 *
 * @access  private
 * @param   integer  $brand_id
 * @return  array
 */
function brand_get_goods($brand_id)
{
	global $redirect_uri,$hhs,$db; 
    $cate_where = ($cate > 0) ? 'AND ' . get_children($cate) : '';

    /* 获得商品列表 */
    $sql = 'SELECT g.goods_id, g.goods_name, g.market_price, g.shop_price AS org_price, ' .
                "IFNULL(mp.user_price, g.shop_price * '$_SESSION[discount]') AS shop_price, g.promote_price, " .
                'g.promote_start_date, g.promote_end_date, g.goods_brief, g.goods_thumb , g.goods_img ' .
            'FROM ' . $GLOBALS['hhs']->table('goods') . ' AS g ' .
            'LEFT JOIN ' . $GLOBALS['hhs']->table('member_price') . ' AS mp ' .
                "ON mp.goods_id = g.goods_id AND mp.user_rank = '$_SESSION[user_rank]' " .
            "WHERE g.is_on_sale = 1 AND g.is_alone_sale = 1 AND g.is_delete = 0 AND g.brand_id = '$brand_id' $cate_where".
            "ORDER BY goods_id DESC";

    $res = $GLOBALS['db']->query($sql);

    $arr = array();
	$i =0;
    while ($row = $GLOBALS['db']->fetchRow($res))
    {
        if ($row['promote_price'] > 0)
        {
            $promote_price = bargain_price($row['promote_price'], $row['promote_start_date'], $row['promote_end_date']);
        }
        else
        {
            $promote_price = 0;
        }
		

        $arr[$i]['goods_id']      = $row['goods_id'];
		
		
		
		
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
		$comments = assign_comment_app($row['goods_id'],0,1);
		$goods_info['stores_info'] = $stores_info;
 		$goods_info['info'] = $goods;
		$goods_info['goods_gallery'] = $list;
		$goods_info['properties'] = $properties;
		$goods_info['comments'] = $comments;
		
		
 		$arr[$i]['info']  = $goods_info;
				
		
		
		
		
		
		
		
		
		
		
		
		
		
        $arr[$i]['goods_name']       = $row['goods_name'];
        $arr[$i]['market_price']  = price_format($row['market_price']);
        $arr[$i]['shop_price']    = price_format($row['shop_price']);
        $arr[$i]['promote_price'] = ($promote_price > 0) ? price_format($promote_price) : '';
        $arr[$i]['goods_brief']   = $row['goods_brief'];
        $arr[$i]['goods_thumb']   = $redirect_uri.get_image_path($row['goods_id'], $row['goods_thumb'], true);
        $arr[$i]['goods_img']     = $redirect_uri.get_image_path($row['goods_id'], $row['goods_img']);
        $arr[$i]['url']           = build_uri('goods', array('gid' => $row['goods_id']), $row['goods_name']);
		$i++;
    }

	

    return $arr;
}
?>
