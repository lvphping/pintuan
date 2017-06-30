<?php
define('IN_HHS', true);



if($action =='list')
{
	$goodslist = exchange_get_goods($children, $integral_min, $integral_max, $ext, $size, $page, $sort, $order);
	if($goodslist)
	{
		$result['error'] =0;	
	}
	else
	{
		$result['error'] =1;	
	}
	$s =0;
	foreach($goodslist as $idx=>$v)
	{
		$goodslists[$s] = $v;
		$s++;
	}
	
	$results['content'] = $goodslists;
	echo $json->encode($results);
	die();
}
if($action =='view')
{
	$goods_id = $_REQUEST['goods_id'];
	$goods = get_exchange_goods_info($_REQUEST['goods_id']);
    $properties = get_goods_properties($goods_id);  // 获得商品的规格和属性
	//相册
	$list = get_goods_gallery($goods_id);
	foreach($list as $idx=>$v)
	{
		$list[$idx]['img_url'] = $redirect_uri.$v['img_url'];
		$list[$idx]['thumb_url'] = $redirect_uri.$v['thumb_url'];
	}
	$info['goods_gallery'] = $list;	
	$properties = get_goods_properties($goods_id);  // 获得商品的规格和属性
	$spe = $properties['spe'];
	$is =0;
	foreach($spe as $idx=>$v)
	{
		$spes[$is] = $v;
		$is++;
	}
	$properties['spe'] = $spes;
	$info['properties'] = $properties;
	$info['goods_info'] = $goods;
	
	$results['content'] = $info;
	echo $json->encode($results);
	die();
}
if($action =='buy')
{
	$user_id = $_REQUEST['user_id'];
	$goods_id = $_REQUEST['goods_id'];

    /* 查询：判断是否登录 */
    if ($user_id <= 0)
    {
		$results['error'] =0;
		$results['content'] ='请先登录';
		echo $json->encode($results);
		die();
    }

    /* 查询：取得参数：商品id */
    $goods_id = isset($_REQUEST['goods_id']) ? intval($_REQUEST['goods_id']) : 0;
    if ($goods_id <= 0)
    {
		$results['error'] =0;
		$results['content'] ='商品信息不存在';
		echo $json->encode($results);
		die();
    }

    /* 查询：取得兑换商品信息 */
    $goods = get_exchange_goods_info($goods_id);
    if (empty($goods))
    {
		$results['error'] =0;
		$results['content'] ='商品信息不存在';
		echo $json->encode($results);
		die();
    }
	
	
}




/**
 * 获得分类下的商品
 *
 * @access  public
 * @param   string  $children
 * @return  array
 */
function exchange_get_goods($children='')
{
	global $redirect_uri,$hhs,$db;  
    $display = $GLOBALS['display'];
    $where = "eg.is_exchange = 1 AND g.is_delete = 0  ";

    if ($min > 0)
    {
        $where .= " AND eg.exchange_integral >= $min ";
    }

    if ($max > 0)
    {
        $where .= " AND eg.exchange_integral <= $max ";
    }

    /* 获得商品列表 */
    $sql = 'SELECT g.goods_id, g.goods_name, g.goods_name_style, g.shop_price, eg.exchange_integral, ' .
                'g.goods_type, g.goods_brief, g.goods_thumb , g.goods_img, eg.is_hot ' .
            'FROM ' . $GLOBALS['hhs']->table('exchange_goods') . ' AS eg, ' .$GLOBALS['hhs']->table('goods') . ' AS g ' .
            "WHERE eg.goods_id = g.goods_id AND $where $ext ORDER BY goods_id desc";
    $res = $GLOBALS['db']->query($sql);

    $arr = array();
    while ($row = $GLOBALS['db']->fetchRow($res))
    {
        /* 处理商品水印图片 */
        $watermark_img = '';

//        if ($row['is_new'] != 0)
//        {
//            $watermark_img = "watermark_new_small";
//        }
//        elseif ($row['is_best'] != 0)
//        {
//            $watermark_img = "watermark_best_small";
//        }
//        else
        if ($row['is_hot'] != 0)
        {
            $watermark_img = 'watermark_hot_small';
        }

        if ($watermark_img != '')
        {
            $arr[$row['goods_id']]['watermark_img'] =  $watermark_img;
        }

        $arr[$row['goods_id']]['goods_id']          = $row['goods_id'];
        if($display == 'grid')
        {
            $arr[$row['goods_id']]['goods_name']    = $GLOBALS['_CFG']['goods_name_length'] > 0 ? sub_str($row['goods_name'], $GLOBALS['_CFG']['goods_name_length']) : $row['goods_name'];
        }
        else
        {
            $arr[$row['goods_id']]['goods_name']    = $row['goods_name'];
        }
        $arr[$row['goods_id']]['name']              = $row['goods_name'];
        $arr[$row['goods_id']]['goods_brief']       = $row['goods_brief'];
        $arr[$row['goods_id']]['goods_style_name']  = add_style($row['goods_name'],$row['goods_name_style']);
        $arr[$row['goods_id']]['exchange_integral'] = $row['exchange_integral'];
        $arr[$row['goods_id']]['type']              = $row['goods_type'];
		$arr[$row['goods_id']]['shop_price']              = price_format($row['shop_price'],false);
        $arr[$row['goods_id']]['goods_thumb']       = $redirect_uri.get_image_path($row['goods_id'], $row['goods_thumb'], true);
        $arr[$row['goods_id']]['goods_img']         = $redirect_uri.get_image_path($row['goods_id'], $row['goods_img']);
        $arr[$row['goods_id']]['url']               = build_uri('exchange_goods', array('gid'=>$row['goods_id']), $row['goods_name']);
    }

    return $arr;
}

/**
 * 获得积分兑换商品的详细信息
 *
 * @access  public
 * @param   integer     $goods_id
 * @return  void
 */
function get_exchange_goods_info($goods_id)
{
	global $redirect_uri,$hhs,$db;  
    $time = gmtime();
    $sql = 'SELECT g.*, c.measure_unit, b.brand_id, b.brand_name AS goods_brand, eg.exchange_integral, eg.is_exchange ' .
            'FROM ' . $GLOBALS['hhs']->table('goods') . ' AS g ' .
            'LEFT JOIN ' . $GLOBALS['hhs']->table('exchange_goods') . ' AS eg ON g.goods_id = eg.goods_id ' .
            'LEFT JOIN ' . $GLOBALS['hhs']->table('category') . ' AS c ON g.cat_id = c.cat_id ' .
            'LEFT JOIN ' . $GLOBALS['hhs']->table('brand') . ' AS b ON g.brand_id = b.brand_id ' .
            "WHERE g.goods_id = '$goods_id' AND g.is_delete = 0 " .
            'GROUP BY g.goods_id';

    $row = $GLOBALS['db']->getRow($sql);

    if ($row !== false)
    {
        /* 处理商品水印图片 */
        $watermark_img = '';

        if ($row['is_new'] != 0)
        {
            $watermark_img = "watermark_new";
        }
        elseif ($row['is_best'] != 0)
        {
            $watermark_img = "watermark_best";
        }
        elseif ($row['is_hot'] != 0)
        {
            $watermark_img = 'watermark_hot';
        }

        if ($watermark_img != '')
        {
            $row['watermark_img'] =  $watermark_img;
        }

        /* 修正重量显示 */
        $row['goods_weight']  = (intval($row['goods_weight']) > 0) ?
            $row['goods_weight'] . $GLOBALS['_LANG']['kilogram'] :
            ($row['goods_weight'] * 1000) . $GLOBALS['_LANG']['gram'];

        /* 修正上架时间显示 */
        $row['add_time']      = local_date($GLOBALS['_CFG']['date_format'], $row['add_time']);

        /* 修正商品图片 */
        $row['goods_img']   = $redirect_uri.get_image_path($goods_id, $row['goods_img']);
        $row['goods_thumb'] = $redirect_uri.get_image_path($goods_id, $row['goods_thumb'], true);

        return $row;
    }
    else
    {
        return false;
    }
}

