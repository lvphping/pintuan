<?php
define('IN_HHS', true);
require(ROOT_PATH . 'includes/lib_orders.php');
if($_REQUEST['user_id']=='')
{
		$result['error'] = 0;
		$result['content'] = '请先登录';
		die($json->encode($result));
}
else
{
	$count = $db->getOne("select count(*) from ".$hhs->table('users')." where user_id='$_REQUEST[user_id]'");
	if(!$count)
	{
		$result['error'] = 0;
		$result['content'] = '用户不存在';
		die($json->encode($result));
	}
}
$user_id = $_REQUEST['user_id'];

$_SESSION['user_id'] = $user_id;

if($action =='cart_num')
{
	
    /* 检查购物车中是否有商品 */
    $sql = "SELECT SUM(goods_number) AS number FROM " . $hhs->table('cart') .
        " WHERE user_id = '" . $user_id . "' " .
        "AND parent_id = 0 AND is_gift = 0 AND is_checked = 1 AND rec_type = '0'";
    $num = $db->getOne($sql);
    $result['error'] = 0;
	if($num)
	{
		$result['cart_num'] = $num;
	}
	else
	{
		$result['cart_num'] = 0;
	}
    die($json->encode($result));
	
}


if($action =='add_to_cart')
{
	include_once('includes/cls_json.php');
	//$_POST['goods']=strip_tags(urldecode($_POST['goods']));
   // $_POST['goods'] = json_str_iconv($_POST['goods']);
	//echo "<pre>";
	

	$goods = array();
	$spec = $_POST['spec'];
	$goods['spec'] = explode(",",$spec);
	$goods['number'] = $_REQUEST['number'];
	$goods['rec_type'] = $_REQUEST['rec_type'];
	$goods['type'] =  $_REQUEST['type'];
	$goods['team_sign'] =  $_REQUEST['team_sign'];
	$goods['goods_id'] =  $_REQUEST['goods_id'];
	$goods['quick'] = 0;
	$goods['parent'] = 0;
	$goods = $json->encode($goods);
	
	$_POST['goods']=strip_tags(urldecode($goods));
    $_POST['goods'] = json_str_iconv($_POST['goods']);

			//  $result['content'] = $_POST['goods'] ;
		//  die($json->encode($result));
      
 //   if (empty($_POST['goods']))
//    {
//		  $result['error'] = 0;
//		  $result['content'] = '1111111111';
//		  die($json->encode($result));
//    }
	$user_id = $_REQUEST['user_id'];
	if($_REQUEST['user_id']=='')
	{
            $result['error'] = 0;
			$result['content'] = '请先登录';
        	die($json->encode($result));
	}
	else
	{
		
		$count = $db->getOne("select count(*) from ".$hhs->table('users')." where user_id='$_REQUEST[user_id]'");
		if(!$count)
		{
            $result['error'] = 0;
			$result['content'] = '用户不存在';
        	die($json->encode($result));
		}
	}
	



    if (!empty($_REQUEST['goods_id']) && empty($_POST['goods']))
    {
        if (!is_numeric($_REQUEST['goods_id']) || intval($_REQUEST['goods_id']) <= 0)
        {
            $result['error'] = 0;
			$result['content'] = '非法商品id';
        	die($json->encode($result));
        }
        $goods_id = intval($_REQUEST['goods_id']);
        exit;
    }

    $result = array('error' => 0, 'message' => '', 'content' => '', 'goods_id' => '');
    $json  = new JSON;

         
    if (empty($_POST['goods']))
    {
		  $result['error'] = 0;
		  $result['content'] = '数据为空';
		  die($json->encode($result));
    }

   $goods = $json->decode($_POST['goods']);
	
	//	print_r($goods);
//exit;

    /* 检查：如果商品有规格，而post的数据没有规格，把商品的规格属性通过JSON传到前台 */
    if (empty($goods->spec) AND empty($goods->quick))
    {
        $sql = "SELECT a.attr_id, a.attr_name, a.attr_type, ".
            "g.goods_attr_id, g.attr_value, g.attr_price " .
        'FROM ' . $GLOBALS['hhs']->table('goods_attr') . ' AS g ' .
        'LEFT JOIN ' . $GLOBALS['hhs']->table('attribute') . ' AS a ON a.attr_id = g.attr_id ' .
        "WHERE a.attr_type != 0 AND g.goods_id = '" . $goods->goods_id . "' " .
        'ORDER BY a.sort_order, g.attr_price, g.goods_attr_id';

        $res = $GLOBALS['db']->getAll($sql);

        if (!empty($res))
        {
            $spe_arr = array();
            foreach ($res AS $row)
            {
                $spe_arr[$row['attr_id']]['attr_type'] = $row['attr_type'];
                $spe_arr[$row['attr_id']]['name']     = $row['attr_name'];
                $spe_arr[$row['attr_id']]['attr_id']     = $row['attr_id'];
                $spe_arr[$row['attr_id']]['values'][] = array(
                                                            'label'        => $row['attr_value'],
                                                            'price'        => $row['attr_price'],
                                                            'format_price' => price_format($row['attr_price'], false),
                                                            'id'           => $row['goods_attr_id']);
            }
            $i = 0;
            $spe_array = array();
            foreach ($spe_arr AS $row)
            {
                $spe_array[]=$row;
            }

            $result['error']   = ERR_NEED_SELECT_ATTR;
            $result['goods_id'] = $goods->goods_id;
            $result['parent'] = $goods->parent;
            // $result['message'] = '此商品为多属性商品，请到详情页购买';
            $result['message'] = $spe_array;
            //die(json_encode(array('error'=>22,'message'=>'asdf')));
           
            die($json->encode($result));
        }
    }




 
    /* 更新：如果是一步购物，先清空购物车 */
    if ($_CFG['one_step_buy'] == '1')
    {
        clear_cart('',$user_id);
    }

    /* 检查：商品数量是否合法 */
    if (!is_numeric($goods->number) || intval($goods->number) <= 0)
    {
        $result['error']   = 0;
        $result['message'] = $_LANG['invalid_number'];
    }
    /* 更新：购物车 */
    else
    {
        if(!empty($goods->spec))
        {
            foreach ($goods->spec as  $key=>$val )
            {
                $goods->spec[$key]=intval($val);
            }
        }
        // 更新：添加到购物车
        if (app_addto_cart($goods->goods_id, $goods->number, $goods->spec, $goods->parent,$user_id))
        {
            if ($_CFG['cart_confirm'] > 2)
            {
                $result['message'] = '';
            }
            else
            {
                $result['message'] = '该商品已加入购物车';
            }

            if (empty($goods->spec)) {
                $result['message'] = '';
            }
			$result['error'] = 1;
          //  $result['content'] = insert_cart_info();
           // $result['one_step_buy'] = $_CFG['one_step_buy'];
        }
        else
        {
			$message = $err->last_message();
            $result['message']  = $message[0];
            $result['error']    = 0;
            $result['goods_id'] = stripslashes($goods->goods_id);
           // if (is_array($goods->spec))
//            {
//                $result['product_spec'] = implode(',', $goods->spec);
//            }
//            else
//            {
//                $result['product_spec'] = $goods->spec;
//            }
        }
    }
    $result['rec_type'] = $goods->rec_type;
    $result['type'] = $goods->type;
	
	
//	print_r($result);exit;

   // $result['confirm_type'] = !empty($_CFG['cart_confirm']) ? $_CFG['cart_confirm'] : 2;
    die($json->encode($result));
	
}
elseif($action =='checkout')
{
    /*------------------------------------------------------ */
    //-- 订单确认
    /*------------------------------------------------------ */
    /* 取得购物类型 */
    $flow_type = isset($_SESSION['flow_type']) ? intval($_SESSION['flow_type']) : CART_GENERAL_GOODS;

    /* 团购标志 */
    if ($flow_type == CART_GROUP_BUY_GOODS)
    {
		$results['is_group_buy'] =1;
    }
    /* 积分兑换商品 */
    elseif ($flow_type == CART_EXCHANGE_GOODS)
    {
		$results['is_exchange_goods'] =1;
    }
    else
    {
        //正常购物流程  清空其他购物流程情况
        $_SESSION['flow_order']['extension_code'] = '';
    }

    /* 检查购物车中是否有商品 */
    $sql = "SELECT COUNT(*) FROM " . $hhs->table('cart') .
        " WHERE user_id = '" . $user_id . "' " .
        "AND parent_id = 0 AND is_gift = 0 AND is_checked = 1 AND rec_type = '$flow_type'";

    if ($db->getOne($sql) == 0)
    {
          $result['error'] = 0;
    	  $result['content'] = '购物车没有商品';
          die($json->encode($result));
    }

    unset($_SESSION['flow_consignee']);
    $address_id = isset($_REQUEST['address_id']) ? intval($_REQUEST['address_id']) : 0;
    $consignee = $db->getRow('select * from '.$hhs->table('user_address').' where address_id= "'.$address_id.'" and user_id= "'.$user_id.'"');
    if (empty($consignee)) {
        $consignee = get_consignee($_SESSION['user_id']);
    }

    /* 检查收货人信息是否完整 */
    if (!check_consignee_info($consignee, $flow_type))
    {
        /* 如果不完整则转向到收货人信息填写界面 */
       // $url=urlencode('flows.php?step=address_list');
       // hhs_header("Location: flows.php?step=edit_consignee&back_url=".$url."\n");
      //  exit;
		$results['error'] = 0;
		$results['content'] = '请先完善收货信息';
		die($json->encode($results));
		
    }


 //   $_SESSION['flow_consignee'] = $consignee;
   // $smarty->assign('consignee', $consignee);
	$results['consignee'] =  $consignee;

    /* 对商品信息赋值 */
    $cart_goods = app_cart_goods($flow_type,$user_id); // 取得商品列表，计算合计
    /* 对是否允许修改购物车赋值 */
    if ($flow_type != CART_GENERAL_GOODS || $_CFG['one_step_buy'] == '1')
    {
		$results['allow_edit_cart'] =  0;
    }
    else
    {
      	$results['allow_edit_cart'] =  1;
    }

    /*
     * 取得购物流程设置
     */
    
	//$results['config'] = $_CFG;
    /*
     * 取得订单信息
     */
    unset($_SESSION['flow_order']);
    $order = app_flow_order_info($user_id);
	
	
	$results['order'] = $order;

    /* 计算折扣 */
    if ($flow_type != CART_EXCHANGE_GOODS && $flow_type != CART_GROUP_BUY_GOODS)
    {
        $discount = compute_discount();
		$results['discount'] = $discount['discount'];
       
        $favour_name = empty($discount['name']) ? '' : join(',', $discount['name']);
 	    $results['your_discount'] = sprintf($_LANG['your_discount'], $favour_name, price_format($discount['discount']));
    }

    /*
     * 计算订单的费用
     */
    $total = app_order_fee($order, $cart_goods, $consignee,$user_id);
	$results['total']  = $total;
	$results['shopping_money']  = sprintf($_LANG['shopping_money'], $total['formated_goods_price']);
	$results['market_price_desc'] =  sprintf($_LANG['than_market_price'], $total['formated_market_price'], $total['formated_saving'], $total['save_rate']);

    /* 取得配送列表 */
    $region            = array($consignee['country'], $consignee['province'], $consignee['city'], $consignee['district']);

    $shipping_lists = array();
    $bonus_lists = array();
    foreach ($cart_goods as $suppliers_id => $value) {
        $goods_id_list = array();
        foreach ($value['goods_list'] as $goods) {
            $goods_id_list[] = $goods['goods_id'];
        }

        $shipping_list     = available_shipping_list($region,$suppliers_id,$goods_id_list);
        $cart_weight_price = cart_weight_price($flow_type,$suppliers_id);

        // 自提
        $point_list = array();
        foreach ($shipping_list as $key => $shipping) {
            if ($shipping['shipping_code'] == 'cac') {
                $point_list = available_point_list($region,$suppliers_id);
                if (empty($point_list)) {
                    unset($shipping_list[$key]);
                }
                break;
            }
        }

        // 查看购物车中是否全为免运费商品，若是则把运费赋为零
        $sql = 'SELECT count(*) FROM ' . $hhs->table('cart') . " WHERE `user_id` = '" .$user_id. "' AND `extension_code` != 'package_buy' AND `is_shipping` = 0 AND `suppliers_id` = '" . $suppliers_id. "'";
        $shipping_count = $db->getOne($sql);

        foreach ($shipping_list AS $key => $val)
        {
            $shipping_cfg = unserialize_config($val['configure']);
            $shipping_fee = ($shipping_count == 0 AND $cart_weight_price['free_shipping'] == 1) ? 0 : shipping_fee($val['shipping_code'], unserialize($val['configure']),
            $cart_weight_price['weight'], $cart_weight_price['amount'], $cart_weight_price['number']);

            $shipping_list[$key]['format_shipping_fee'] = price_format($shipping_fee, false);
            $shipping_list[$key]['shipping_fee']        = $shipping_fee;
            $shipping_list[$key]['free_money']          = price_format($shipping_cfg['free_money'], false);
            $shipping_list[$key]['insure_formated']     = strpos($val['insure'], '%') === false ?
                price_format($val['insure'], false) : $val['insure'];

            /* 当前的配送方式是否支持保价 */
            if ($val['shipping_id'] == $order['shipping_id'])
            {
                $insure_disabled = ($val['insure'] == 0);
                $cod_disabled    = ($val['support_cod'] == 0);
            }
            else{
                $insure_disabled   = true;
                $cod_disabled      = true;
            }
            $shipping_list[$key]['insure_disabled'] = $insure_disabled;
            $shipping_list[$key]['cod_disabled'] = $cod_disabled;
			unset($shipping_list[$key]['configure']);
        }
        $cart_goods[$suppliers_id]['shipping_lists'] = $shipping_list;
        unset($shipping_list);
        $cart_goods[$suppliers_id]['point_list'] = $point_list;


        $user_bonus = array();
        $allow_use_bonus = 0;
        /* 如果使用红包，取得用户可以使用的红包及用户选择的红包 */
        if ((!isset($_CFG['use_bonus']) || $_CFG['use_bonus'] == '1')
            && ($flow_type != CART_GROUP_BUY_GOODS && $flow_type != CART_EXCHANGE_GOODS))
        {
			
		
            // 取得用户可用红包
            $user_bonus = user_bonus($user_id, $value['subtotal'], $suppliers_id);
	
	
            if (!empty($user_bonus))
            {
                foreach ($user_bonus AS $key => $val)
                {
                    $user_bonus[$key]['bonus_money_formated'] = price_format($val['type_money'], false);
                }
                $allow_use_bonus = 1;
            }


            foreach($cart_goods[$suppliers_id]['goods_list'] as $v){
                $sql = "SELECT bonus_allowed FROM ".$hhs->table("goods")." WHERE goods_id = " . $v['goods_id'];
                $allow_use_bonus2 =  $db->getOne($sql);
                if($allow_use_bonus2 &&  $allow_use_bonus){
                    $allow_use_bonus = $allow_use_bonus2;
                    break;
                }
            }
            $allow_use_bonus = $allow_use_bonus && $allow_use_bonus2;


        }        
        $cart_goods[$suppliers_id]['bonus_list'] = $user_bonus;
        $cart_goods[$suppliers_id]['allow_use_bonus'] = $allow_use_bonus;
        unset($value);
    }
	$results['goods_list'] = $cart_goods;
	$results['nums'] = count($cart_goods);
  
    /* 取得支付列表 */

    $payment_list = available_payment_list(1);
    if(isset($payment_list))
    {
        foreach ($payment_list as $key => $payment)
        {
            if ($payment['is_cod'] == '1')
            {
                $payment_list[$key]['format_pay_fee'] = '<span id="HHS_CODFEE">' . $payment['format_pay_fee'] . '</span>';
            }
            /* 如果有易宝神州行支付 如果订单金额大于300 则不显示 */
            if ($payment['pay_code'] == 'yeepayszx' && $total['amount'] > 300)
            {
                unset($payment_list[$key]);
            }
            /* 如果有余额支付 */
            if ($payment['pay_code'] == 'balance')
            {
                /* 如果未登录，不显示 */
                if ($_SESSION['user_id'] == 0)
                {
                    unset($payment_list[$key]);
                }
                else
                {
                    if ($_SESSION['flow_order']['pay_id'] == $payment['pay_id'])
                    {
                        $smarty->assign('disable_surplus', 1);
                    }
                }
            }
			unset($payment_list[$key]['pay_config']);
        }
    }
  
	$results['payment_list'] = $payment_list;
    $user_info = user_info($_SESSION['user_id']);

    /* 如果使用余额，取得用户余额 */
    if ((!isset($_CFG['use_surplus']) || $_CFG['use_surplus'] == '1')
        && $_SESSION['user_id'] > 0
        && $user_info['user_money'] > 0)
    {
        // 能使用余额
        $smarty->assign('allow_use_surplus', 1);
		$results['allow_use_surplus'] = 1;
		$results['your_surplus'] = $user_info['user_money'];
    }

    /* 如果使用积分，取得用户可用积分及本订单最多可以使用的积分 */
   // if ((!isset($_CFG['use_integral']) || $_CFG['use_integral'] == '1')
//        && $_SESSION['user_id'] > 0
//        && $user_info['pay_points'] > 0
//        && ($flow_type != CART_GROUP_BUY_GOODS && $flow_type != CART_EXCHANGE_GOODS))
//    {
//        // 能使用积分
//		$results['allow_use_integral'] = 1;
//		$results['order_max_integral'] =  flow_available_points(); // 可用积分
//     	$results['your_integral'] =  $user_info['pay_points']; // 用户积分
//    }

	
	$goods_list = $results['goods_list'];


	foreach($goods_list as $ids=>$vv)
	{
		$vv['logo'] = $redirect_uri.$vv['logo'];
		foreach($vv['goods_list'] as $ii=>$v)
		{
			$vv['goods_list'][$ii]['goods_img'] = $redirect_uri.$v['goods_img'];	
		}
		$content[] = $vv;
	}
	$results['goods_list'] = $content;
	
	
	die($json->encode($results));

    /* 保存 session */
}
elseif($action =='cart')
{
	if(!$_REQUEST['user_id'])
	{
		$result['error'] =0;
		$result['error'] ='请先登录';
		die($json->encode($result));
	}
    /* 取得商品列表，计算合计 */
    $cart_goods = app_get_cart_goods($_REQUEST['user_id']);

	//$result['goods_list'] = $cart_goods['goods_list'];
	//$result['total'] = $cart_goods['total'];
  	//$result['shopping_money'] = sprintf($_LANG['shopping_money'], $cart_goods['total']['goods_price']);
    //$result['market_price_desc'] = sprintf($_LANG['than_market_price'],
    //$cart_goods['total']['market_price'], $cart_goods['total']['saving'], $cart_goods['total']['save_rate']);
	$result['error'] =1;
	
	//$GLOBALS['_CFG']['shop_logo']
	
	
//	echo "<pre>";
	//
	//print_r($cart_goods);exit;
	
	
	
	foreach($cart_goods as $idx=>$v)
	{
		$goods_list   = $cart_goods['goods_list'];
		$i=0;
		foreach($goods_list as $ids=>$s)
		{
			foreach($s['goods_list'] as $id=>$dd)
			{
				$s['goods_list'][$id]['goods_img'] = $redirect_uri.$dd['goods_thumb'];
			}
			$goods[$i]['goods_list'] = $s['goods_list'];
			$goods[$i]['suppliers_name'] = $s['suppliers_name'];	
			if($s['suppliers_id']==0)
			{
				$goods[$i]['logo'] = $redirect_uri.$GLOBALS['_CFG']['shop_logo'];	
			}
			else
			{
				$goods[$i]['logo'] = $redirect_uri.$s['logo'];	
			}
			$i++;
		}
		$list['goods_list'] = $goods;
		if($goods)
		{
			$list['error'] =0;
		}
		else
		{
			$list['error'] =1;
		}
		$list['total'] = $cart_goods['total'];
		
	}
	
	die($json->encode($list));	
}
//删除购物车商品
elseif ($action == 'drop_goods')
{
    $rec_id = intval($_REQUEST['rec_id']);
    flow_drop_cart_goods($rec_id,$_REQUEST['user_id']);

    unset($_SESSION['flow_order']);
    $cart_goods =app_get_cart_goods();
    $data = array(
        'count'  => $cart_goods['total']['real_goods_count'],
        'amount' => $cart_goods['total']['goods_amount'],
    );
    include_once('includes/cls_json.php');
    $json  = new JSON;    
    $result = array(
        'error'   => 1, 
        'message' => '', 
        'content' => '', 
        'rec_id'  => $rec_id, 
        'data'    => $data,
    );
    die($json->encode($result));
    exit;
}
//更新购物车 
elseif ($action == 'update_cart')
{
    // if (isset($_POST['goods_number']) && is_array($_POST['goods_number']))
    // {
    //     flow_update_cart($_POST['goods_number']);
    // }

    // show_message($_LANG['update_cart_notice'], $_LANG['back_to_cart'], 'flow.php');
    
    $rec_id       = intval($_REQUEST['rec_id']);
    $goods_number = intval($_REQUEST['number']);
    if($rec_id && $goods_number){
        flow_update_cart(array(
            $rec_id => $goods_number
        ),$_REQUEST['user_id']);
    }
    $cart_goods = get_cart_goods();
    $find       = false;
    $subtotal   = 0.00;
    foreach ($cart_goods['goods_list'] as $key => $cart_goods_list) {
        foreach ($cart_goods_list['goods_list'] as $goods) {
            if ($goods['rec_id'] == $rec_id) {
                $goods_number = $goods['goods_number'];
                $subtotal     = $goods['subtotal']; 
                $find         = true;
                break;
            }
        }
        if($find)
            break;
    }
    $data = array(
        'count'  => $cart_goods['total']['real_goods_count'],
        'amount' => $cart_goods['total']['goods_amount'],
    );
    include_once('includes/cls_json.php');
    $json  = new JSON;    
    $result = array(
        'error'        => 1, 
        'message'      => '', 
        'content'      => '', 
        'rec_id'       => $rec_id,
        'goods_number' => $goods_number,
        'subtotal'     => $subtotal,
        'data'         => $data,
    );
    die($json->encode($result));
    exit;
}
//更新其中一个商品是否购买
elseif ($action == 'check_goods')
{
    $is_checked = intval($_REQUEST['is_checked']);
    $rec_id     = intval($_REQUEST['rec_id']);
	$user_id= $_REQUEST['user_id'];

    $sql = 'UPDATE '.$hhs->table('cart').' set `is_checked` = "'.$is_checked.'" WHERE rec_id = "'.$rec_id.'" and rec_type = 0';
    $db->query($sql);
    $cart_goods = app_get_cart_goods($user_id);
    $data = array(
        'count'  => $cart_goods['total']['real_goods_count'],
        'amount' => $cart_goods['total']['goods_amount'],
    );
    include_once('includes/cls_json.php');
    $json  = new JSON;    
    $result = array(
        'error'        => 1, 
        'message'      => '', 
        'content'      => '', 
        'data'         => $data,
    );
    die($json->encode($result));
    exit;    
}
elseif ($action == 'check_all')
{
    $is_checked = intval($_REQUEST['is_checked']);
	$user_id= $_REQUEST['user_id'];
    $sql = 'UPDATE '.$hhs->table('cart').' set `is_checked` = "'.$is_checked.'"  WHERE rec_type = 0 and user_id="'.$user_id.'"';
    $db->query($sql);
    $cart_goods = app_get_cart_goods($user_id);
    $data = array(
        'count'  => $cart_goods['total']['real_goods_count'],
        'amount' => $cart_goods['total']['goods_amount'],
    );
    include_once('includes/cls_json.php');
    $json  = new JSON;    
    $result = array(
        'error'        => 1, 
        'message'      => '', 
        'content'      => '', 
        'data'         => $data,
    );
    die($json->encode($result));
    exit;    
}
//清空购物车
elseif($action =='clear')
{
    $sql = "DELETE FROM " . $hhs->table('cart') . " WHERE user_id='" . $_REQUEST['user_id'] . "'";
    $db->query($sql);
	$result['error'] = 1;
    die($json->encode($result));
    exit;    

}
/*------------------------------------------------------ */
//-- 完成所有订单操作，提交到数据库
/*------------------------------------------------------ */
elseif ($action == 'done')
{
    include_once('includes/lib_clips.php');
    include_once('includes/lib_payment.php');
    include_once('includes/cls_json.php');
    $json  = new JSON;    

    /* 取得购物类型 */
    $flow_type = isset($_SESSION['flow_type']) ? intval($_SESSION['flow_type']) : CART_GENERAL_GOODS;

    /* 检查购物车中是否有商品 */
    $sql = "SELECT COUNT(*) FROM " . $hhs->table('cart') .
        " WHERE user_id = '" . $user_id . "' " .
        "AND parent_id = 0 AND is_gift = 0 AND rec_type = '$flow_type'";
    if ($db->getOne($sql) == 0)
    {
        // show_message($_LANG['no_goods_in_cart'], '', '', 'warning');
        $result = array(
            'error'    => 1, 
            'message'  => '购物车没有数据', 
            'url'      => './',
        );
        die($json->encode($result));                   
    }

    /* 检查商品库存 */
    /* 如果使用库存，且下订单时减库存，则减少库存 */
    if ($_CFG['use_storage'] == '1' && $_CFG['stock_dec_time'] == SDT_PLACE)
    {
        $cart_goods_stock = get_cart_goods();
        $_cart_goods_stock = array();
        foreach ($cart_goods_stock['goods_list'] as $value)
        {
            foreach ($value['goods_list'] as $v) {
                $_cart_goods_stock[$v['rec_id']] = $v['goods_number'];
            }
        }

        flow_cart_stock($_cart_goods_stock);
        unset($cart_goods_stock, $_cart_goods_stock);
    }
    $address_id = isset($_REQUEST['address_id']) ? intval($_REQUEST['address_id']) : 0;
    $consignee = $db->getRow('select * from '.$hhs->table('user_address').' where address_id= "'.$address_id.'" and user_id= "'.$_SESSION['user_id'].'"');

   
    /* 检查收货人信息是否完整 */
    // if (!check_consignee_info($consignee, $flow_type))
    // {
    //      如果不完整则转向到收货人信息填写界面 
    //     hhs_header("Location: flows.php?step=consignee\n");
    //     exit;
    // }
	

    /* 订单中的商品 */
    $all_cart_goods = app_cart_goods($flow_type,$user_id);
	
	


    if (empty($all_cart_goods))
    {
        // show_message($_LANG['no_goods_in_cart'], $_LANG['back_home'], './', 'warning');
        $result = array(
            'error'    => 1, 
            'message'  => '购物车没有数据', 
            'url'      => './',
        );
        die($json->encode($result));                   
    }

    $_POST['how_oos'] = isset($_POST['how_oos']) ? intval($_POST['how_oos']) : 0;
    $_POST['card_message'] = isset($_POST['card_message']) ? compile_str($_POST['card_message']) : '';
    $_POST['inv_type'] = !empty($_POST['inv_type']) ? compile_str($_POST['inv_type']) : '';
    $_POST['inv_payee'] = isset($_POST['inv_payee']) ? compile_str($_POST['inv_payee']) : '';
    $_POST['inv_content'] = isset($_POST['inv_content']) ? compile_str($_POST['inv_content']) : '';
    $_POST['postscript'] = isset($_POST['postscript']) ? urldecode($_POST['postscript']) : '';
    $_POST['postscript'] = isset($_POST['postscript']) ? compile_str($_POST['postscript']) : '';

$shipping_id = $_REQUEST['shipping_id'];

$shipping_id_array = explode(",",$shipping_id);



$shipping_row = array();
foreach($shipping_id_array as $idx=>$v)
{
	$shipping_ids = explode("_",$v);
	$shipping_row[$shipping_ids[0]] = $shipping_ids[1];
}

//print_r($shipping_row);exit;
	


$point_id = $_REQUEST['point_id'];
$point_id_array = explode(",",$point_id);
$point_row = array();
foreach($point_id_array as $idx=>$v)
{
	$point_ids = explode("_",$v);
	$point_row[$point_ids[0]] = $point_ids[1];
}


$bonus = $_REQUEST['bonus'];
$bonus_array = explode(",",$bonus);
$bonus_row = array();
foreach($bonus_array as $idx=>$v)
{
	$bonus_ids = explode("_",$v);
	$bonus_row[$bonus_ids[0]] = $bonus_ids[1];
}
//
$checked_mobile = $_POST['checked_mobile'];
$checked_mobile_array = explode(",",$checked_mobile);
$checked_mobile_row = array();
foreach($checked_mobile_array as $idx=>$v)
{
	$checked_mobile_ids = explode("_",$v);
	$checked_mobile_row[$checked_mobile_ids[0]] = $checked_mobile_ids[1];
}
//
$best_time = $_REQUEST['best_time'];
$best_time_array = explode(",",$best_time);
$best_time_row = array();
foreach($best_time_array as $idx=>$v)
{
	$best_time_ids = explode("_",$v);
	$best_time_row[$best_time_ids[0]] = $best_time_ids[1];
}


// 分单start
// 
/* 取得订单信息 */
$orders = app_flow_order_info();

$created_orders = array();// 创建的订单id
$usedSurplus    = 0.00;
$order_amount   = 0.00;
$order_goods_name = array();




foreach ($all_cart_goods as $suppliers_id => $suppliers_goods) {
    if (! is_numeric($suppliers_id)) {
        continue;
    }
    //重新定义cart_goods
    $cart_goods = $suppliers_goods['goods_list'];
    foreach ($cart_goods as $goods) {
        $order_goods_name[] = $goods['goods_name'];
    }
	
	

    $order = array(
        'shipping_id'     => $shipping_row[$suppliers_id],
        'pay_id'          => intval($_POST['payment']),
        'pack_id'         => isset($_POST['pack']) ? intval($_POST['pack']) : 0,
        'card_id'         => isset($_POST['card']) ? intval($_POST['card']) : 0,
        'card_message'    => trim($_POST['card_message']),
        'surplus'         => isset($_POST['surplus']) ? floatval($_POST['surplus']) : 0.00,
        'integral'        => isset($_POST['integral']) ? intval($_POST['integral']) : 0,
        'bonus_id'        => isset($_POST['bonus'][$suppliers_id]) ? intval($_POST['bonus'][$suppliers_id]) : 0,
        'need_inv'        => empty($_POST['need_inv']) ? 0 : 1,
        'inv_type'        => $_POST['inv_type'],
        'inv_payee'       => trim($_POST['inv_payee']),
        'inv_content'     => $_POST['inv_content'],
        'postscript'      => trim($_POST['postscript']),
        'how_oos'         => isset($_LANG['oos'][$_POST['how_oos']]) ? addslashes($_LANG['oos'][$_POST['how_oos']]) : '',
        'need_insure'     => isset($_POST['need_insure']) ? intval($_POST['need_insure']) : 0,
        'user_id'         => $user_id,
        'add_time'        => gmtime(),
        'order_status'    => OS_UNCONFIRMED,
        'shipping_status' => SS_UNSHIPPED,
        'pay_status'      => PS_UNPAYED,
        'agency_id'       => 0,
        'point_id'        => $_POST['point_id'][$suppliers_id],
        );
	
	
            $result = array(
				'shipping_id' =>$order['shipping_id'],
				'suppliers_id' =>$suppliers_id,
                'error'    => 2, 
                'message'  => '配送地址存在问题，请前往订单列表', 
                'url'      => 'user.php?act=order_list',
            );
           // die($json->encode($result));           


    // 合并SESSION中计算好的
     // $order = array_merge($order,$orders[$suppliers_id]['order']);
	
    /* 扩展信息 */
    if (isset($_SESSION['flow_type']) && intval($_SESSION['flow_type']) != CART_GENERAL_GOODS)
    {
        $order['extension_code'] = $_SESSION['extension_code'];
        $order['extension_id'] = $_SESSION['extension_id'];
    }
    else
    {
        $order['extension_code'] = '';
        $order['extension_id'] = 0;
    }

    /* 检查积分余额是否合法 */
  //  $user_id = $_SESSION['user_id'];
    if ($user_id > 0)
    {
        $user_info = user_info($user_id);

        $order['surplus'] = min($order['surplus'], $user_info['user_money'] + $user_info['credit_line'] - $usedSurplus);
        if ($order['surplus'] < 0)
        {
            $order['surplus'] = 0;
        }
        $usedSurplus      += $order['surplus'];//使用了的余额
        $_POST['surplus'] -= $order['surplus'];//post数据还剩余的余额

        // 查询用户有多少积分
        // $flow_points = flow_available_points();  // 该订单允许使用的积分
        // $user_points = $user_info['pay_points']; // 用户的积分总数

        // $order['integral'] = min($order['integral'], $user_points, $flow_points);
        // if ($order['integral'] < 0)
        // {
        //     $order['integral'] = 0;
        // }
    }
    else
    {
        $order['surplus']  = 0;
        $order['integral'] = 0;
    }
    /* 检查红包是否存在 */
    if ($order['bonus_id'] > 0)
    {
        $bonus = bonus_info($order['bonus_id']);
	

        if (empty($bonus) || $bonus['suppliers_id'] != $suppliers_id || $bonus['user_id'] != $user_id || $bonus['order_id'] > 0 || $bonus['min_goods_amount'] > app_cart_amount(true, $flow_type,'',$user_id))
        {
            $order['bonus_id'] = 0;
        }
    }
	
	
    /* 收货人信息 */
    foreach ($consignee as $key => $value)
    {
        $order[$key] = addslashes($value);
    }

    // 自提点
    if ($order['point_id'] > 0) {
        $order['checked_mobile'] = $_POST['checked_mobile'][$suppliers_id];
        $order['best_time']      = $_POST['best_time'][$suppliers_id];
    }
    else{
        $order['checked_mobile'] = '';
        $order['best_time']      = '';
    }
    if (! empty($order['checked_mobile'])) {
        $db->query('update '.$hhs->table('user_address').' set `mobile` = "'.$order['checked_mobile'].'" where user_id = "'.$_SESSION['user_id'].'" AND address_id = "'.$consignee['address_id'].'"');
    } 

   /* 判断是不是实体商品 */
    foreach ($cart_goods AS $val)
    {
        /* 统计实体商品的个数 */
        if ($val['is_real'])
        {
            $is_real_good=1;
        }
    }
    if(isset($is_real_good))
    {
        $sql="SELECT shipping_id FROM " . $hhs->table('shipping') . " WHERE shipping_id=".$order['shipping_id'] ." AND enabled =1"; 
		$shipping_id_t = $db->getOne($sql);
	
	    //die(json_encode(array('error'=>2,'message'=>$db->getOne($sql) )));
        if($shipping_id_t=='')
        {
            $result = array(
                'error'    => 2, 
                'message'  => '配送地址存在问题，请前往订单列表', 
                'url'      => 'user.php?act=order_list',
            );
            die($json->encode($result));           
        }
		
    }
    // print_r($suppliers_goods);
    /* 订单中的总额 */
    // $total = order_fee($order, $cart_goods, $consignee);
    $suppliers_fee = calc_suppliers_fee($order, $suppliers_goods['goods_list'], $consignee, $suppliers_id);
    $total = $suppliers_fee['total'];

    $order['bonus']        = $total['bonus'];
    $order['goods_amount'] = $total['goods_price'];
    $order['discount']     = $total['discount'];
    $order['surplus']      = $total['surplus'];
    $order['tax']          = $total['tax'];

    // 购物车中的商品能享受红包支付的总额
    $discount_amout = compute_discount_amount($suppliers_id);
    // 红包和积分最多能支付的金额为商品总额
    $temp_amout = $order['goods_amount'] - $discount_amout;
    if ($temp_amout <= 0)
    {
        $order['bonus_id'] = 0;
    }

    /* 配送方式 */
    if ($order['shipping_id'] > 0)
    {
        $shipping = shipping_info($order['shipping_id']);
        $order['shipping_name'] = addslashes($shipping['shipping_name']);
    }
    $order['shipping_fee'] = $total['shipping_fee'];
    $order['insure_fee']   = $total['shipping_insure'];

    /* 支付方式 */
    if ($order['pay_id'] > 0)
    {
        $payment = payment_info($order['pay_id']);
        $order['pay_name'] = addslashes($payment['pay_name']);
    }
    $order['pay_fee'] = $total['pay_fee'];
    $order['cod_fee'] = $total['cod_fee'];

    /* 商品包装 */
    // if ($order['pack_id'] > 0)
    // {
    //     $pack               = pack_info($order['pack_id']);
    //     $order['pack_name'] = addslashes($pack['pack_name']);
    // }
    // $order['pack_fee'] = $total['pack_fee'];

    /* 祝福贺卡 */
    // if ($order['card_id'] > 0)
    // {
    //     $card               = card_info($order['card_id']);
    //     $order['card_name'] = addslashes($card['card_name']);
    // }
    // $order['card_fee']      = $total['card_fee'];

    $order['order_amount']  = number_format($total['amount'], 2, '.', '');

    /* 如果全部使用余额支付，检查余额是否足够 */
    if ($payment['pay_code'] == 'balance' && $order['order_amount'] > 0)
    {
        if($order['surplus'] >0) //余额支付里如果输入了一个金额
        {
            $order['order_amount'] = $order['order_amount'] + $order['surplus'];
            $order['surplus'] = 0;
        }
        if ($order['order_amount'] > ($user_info['user_money'] + $user_info['credit_line']))
        {
            $result = array(
                'error'    => 2, 
                'message'  => '您的余额不足以支付整个订单，请选择其他支付方式', 
                'url'      => 'user.php?act=order_list',
            );
            die($json->encode($result));
            // show_message($_LANG['balance_not_enough']);
        }
        else
        {
            $order['surplus'] = $order['order_amount'];
            $order['order_amount'] = 0;
        }
    }

    /* 如果订单金额为0（使用余额或积分或红包支付），修改订单状态为已确认、已付款 */
    if ($order['order_amount'] <= 0)
    {
        $order['order_status'] = OS_CONFIRMED;
        $order['confirm_time'] = gmtime();
        $order['pay_status']   = PS_PAYED;
        $order['pay_time']     = gmtime();
        $order['order_amount'] = 0;
    }

    $order['integral_money']   = $total['integral_money'];
    $order['integral']         = $total['integral'];

    if ($order['extension_code'] == 'exchange_goods')
    {
        $order['integral_money']   = 0;
        $order['integral']         = $total['exchange_integral'];
    }

    $order['from_ad']          = !empty($_SESSION['from_ad']) ? $_SESSION['from_ad'] : '0';
    $order['referer']          = !empty($_SESSION['referer']) ? addslashes($_SESSION['referer']) : '';

    /* 记录扩展信息 */
    if ($flow_type != CART_GENERAL_GOODS)
    {
        $order['extension_code'] = $_SESSION['extension_code'];
        $order['extension_id'] = $_SESSION['extension_id'];
    }

    $order['parent_id'] = $parent_id;

    $order_amount += $order['order_amount'];

    /* 插入订单表 */
    $error_no = 0;
    do
    {
        $order['order_sn'] = get_order_sn(); //获取新订单号
        $GLOBALS['db']->autoExecute($GLOBALS['hhs']->table('order_info'), $order, 'INSERT');

        $error_no = $GLOBALS['db']->errno();

        if ($error_no > 0 && $error_no != 1062)
        {
            die($GLOBALS['db']->errorMsg());
        }
    }
    while ($error_no == 1062); //如果是订单号重复则重新提交数据

    $new_order_id = $db->insert_id();
    $order['order_id'] = $new_order_id;

    /* 插入订单商品 */
    $sql = "INSERT INTO " . $hhs->table('order_goods') . "( " .
                "order_id, goods_id, goods_name, goods_sn, product_id, goods_number, market_price, ".
                "goods_price, goods_attr, is_real, extension_code, parent_id, is_gift, goods_attr_id,rate_1,rate_2,rate_3) ".
            " SELECT '$new_order_id', goods_id, goods_name, goods_sn, product_id, goods_number, market_price, ".
                "goods_price, goods_attr, is_real, extension_code, parent_id, is_gift, goods_attr_id,rate_1,rate_2,rate_3".
            " FROM " .$hhs->table('cart') .
            " WHERE user_id = '".$user_id."' AND is_checked=1 AND rec_type = '$flow_type' AND suppliers_id = '$suppliers_id'";
    $db->query($sql);
    /* 修改拍卖活动状态 */
    if ($order['extension_code']=='auction')
    {
        $sql = "UPDATE ". $hhs->table('goods_activity') ." SET is_finished='2' WHERE act_id=".$order['extension_id'];
        $db->query($sql);
    }

    /* 处理余额、积分、红包 */
    if ($order['user_id'] > 0 && $order['surplus'] > 0)
    {
        log_account_change($order['user_id'], $order['surplus'] * (-1), 0, 0, 0, sprintf($_LANG['pay_order'], $order['order_sn']));
    }
    if ($order['user_id'] > 0 && $order['integral'] > 0)
    {
        log_account_change($order['user_id'], 0, 0, 0, $order['integral'] * (-1), sprintf($_LANG['pay_order'], $order['order_sn']));
    }


    if ($order['bonus_id'] > 0 && $temp_amout > 0)
    {
        use_bonus($order['bonus_id'], $new_order_id);
    }

    /* 如果使用库存，且下订单时减库存，则减少库存 */
    // if ($_CFG['use_storage'] == '1' && $_CFG['stock_dec_time'] == SDT_PLACE)
    // {
    //     change_order_goods_storage($order['order_id'], true, SDT_PLACE);
    // }

    /* 给商家发邮件 */
    /* 增加是否给客服发送邮件选项 */
    // if ($_CFG['send_service_email'] && $_CFG['service_email'] != '')
    // {
    //     $tpl = get_mail_template('remind_of_new_order');
    //     $smarty->assign('order', $order);
    //     $smarty->assign('goods_list', $cart_goods);
    //     $smarty->assign('shop_name', $_CFG['shop_name']);
    //     $smarty->assign('send_date', date($_CFG['time_format']));
    //     $content = $smarty->fetch('str:' . $tpl['template_content']);
    //     send_mail($_CFG['shop_name'], $_CFG['service_email'], $tpl['template_subject'], $content, $tpl['is_html']);
    // }

    /* 如果需要，发短信 */
    // if ($_CFG['sms_order_placed'] == '1' && $_CFG['sms_shop_mobile'] != '')
    // {
    //     include_once('includes/cls_sms.php');
    //     $sms = new sms();
    //     $msg = $order['pay_status'] == PS_UNPAYED ?
    //         $_LANG['order_placed_sms'] : $_LANG['order_placed_sms'] . '[' . $_LANG['sms_paid'] . ']';
    //     $sms->send($_CFG['sms_shop_mobile'], sprintf($msg, $order['consignee'], $order['tel']),'', 13,1);
    // }

    /* 如果订单金额为0 处理虚拟卡 */
    if ($order['order_amount'] <= 0)
    {
        $sql = "SELECT goods_id, goods_name, goods_number AS num FROM ".
               $GLOBALS['hhs']->table('cart') .
                " WHERE is_real = 0 AND extension_code = 'virtual_card'".
                " AND session_id = '".SESS_ID."' AND rec_type = '$flow_type'";

        $res = $GLOBALS['db']->getAll($sql);

        $virtual_goods = array();
        foreach ($res AS $row)
        {
            $virtual_goods['virtual_card'][] = array('goods_id' => $row['goods_id'], 'goods_name' => $row['goods_name'], 'num' => $row['num']);
        }

        if ($virtual_goods AND $flow_type != CART_GROUP_BUY_GOODS)
        {
            /* 虚拟卡发货 */
            if (virtual_goods_ship($virtual_goods,$msg, $order['order_sn'], true))
            {
                /* 如果没有实体商品，修改发货状态，送积分和红包 */
                $sql = "SELECT COUNT(*)" .
                        " FROM " . $hhs->table('order_goods') .
                        " WHERE order_id = '$order[order_id]' " .
                        " AND is_real = 1";
                if ($db->getOne($sql) <= 0)
                {
                    /* 修改订单状态 */
                    update_order($order['order_id'], array('shipping_status' => SS_SHIPPED, 'shipping_time' => gmtime()));

                    /* 如果订单用户不为空，计算积分，并发给用户；发红包 */
                    if ($order['user_id'] > 0)
                    {
                        /* 取得用户信息 */
                        $user = user_info($order['user_id']);

                        /* 计算并发放积分 */
                        $integral = integral_to_give($order);
                        log_account_change($order['user_id'], 0, 0, intval($integral['rank_points']), intval($integral['custom_points']), sprintf($_LANG['order_gift_integral'], $order['order_sn']));

                        /* 发放红包 */
                        send_order_bonus($order['order_id']);
                    }
                }
            }
        }

    }
    /* 插入支付日志 */
    $order['log_id'] = insert_pay_log($new_order_id, $order['order_amount'], PAY_ORDER);

    $created_orders[] = $new_order_id;
}

///////////////////////////////////////////////
///分单 end
    /* 清空购物车 */
    clear_cart('',$user_id);
    /* 清除缓存，否则买了商品，但是前台页面读取缓存，商品数量不减少 */
    clear_all_files();

    /* 取得支付信息，生成支付代码 */
    $payment = payment_info(intval($_POST['payment']));
    $pay_code   = $payment['pay_code'];
    if ($order_amount > 0)
    {
		$result['order'] = $order;
        $result['error']=0;
        $result['message']='余额支付以外的支付';

    }else{
        //require(ROOT_PATH . 'includes/lib_order.php');
        $result['error']=0;
        $result['order'] = $order;
    }
    if(!empty($order['shipping_name']))
    {
        $order['shipping_name']=trim(stripcslashes($order['shipping_name']));
    }

 //   unset($_SESSION['flow_consignee']); // 清除session中保存的收货人信息
//    unset($_SESSION['flow_order']);
//    unset($_SESSION['direct_shopping']);
	$pay_config = unserialize_config($payment['pay_config']);
	
   	if($pay_code=='wxpay'){
  			include_once('includes/modules/payment/' . $payment['pay_code'] . '.php');
			$Wxpay_client_pub = new Wxpay_client_pub();
			
			
			$Wxpay_client_pub->setParameter("nonce_str",    $Wxpay_client_pub->createNoncestr());//随机字符串，不长于32位
			
			$Wxpay_client_pub->setParameter("mch_id",    $pay_config['wxpay_mchid']);//商户号
			$Wxpay_client_pub->setParameter("wxappid",    $pay_config['wxpay_app_id']);
			$Wxpay_client_pub->setParameter("device_info", 'WEB');//提供方名称
			$Wxpay_client_pub->setParameter("body",$order['order_sn']);//商品描述
			$sign = $Wxpay_client_pub->getSign($Wxpay_client_pub->parameters);//签名
			$nonce_str = $Wxpay_client_pub->createNoncestr();
	}
	else
	{
		
		$sign ='';
		$nonce_str ='';
	}



    $result = array(
        'error'    => 0, 
        'message'  => '提交成功', 
        'pay_code' => $pay_code, 
		'partnerid'=> $pay_config['wxpay_mchid'],
		'appid'    => $pay_config['wxpay_app_id'],
		'wxpay_app_secret' =>$pay_config['wxpay_app_secret'],
		
		'timestamp' => gmtime(),
		'nonce_str' => $nonce_str,
		'sign'   => $sign,
 		'order_sn' => $order['order_sn'],
        'content'  => $pay_online,
        'order_id' => join(',',$created_orders),
        'url'      => 'user.php?act=order_list',
    );
    die($json->encode($result));
}

elseif ($action == 'select_shipping')
{
    /*------------------------------------------------------ */
    //-- 改变配送方式
    /*------------------------------------------------------ */
    include_once('includes/cls_json.php');
    $json = new JSON;
    $result = array('error' => '', 'content' => '', 'need_insure' => 0);

    /* 取得购物类型 */
    $flow_type =0;
    $suppliers_id = isset($_REQUEST['suppliers_id']) ? intval($_REQUEST['suppliers_id']) : 0;

    /* 获得收货人信息 */
    $consignee = get_consignee($user_id);


    /* 对商品信息赋值 */
    $cart_goods = app_cart_goods($flow_type,$user_id); // 取得商品列表，计算合计
	


    if (empty($cart_goods[$suppliers_id]) || !check_consignee_info($consignee, $flow_type))
    {
        $result['error'] = $_LANG['no_goods_in_cart'];
    }
    else
    {
        /* 取得订单信息 */
        $order = app_flow_order_info($user_id);
		
	
		
		
		$shipping_id = $_REQUEST['shipping_id'];
		$shipping_id_array = explode(",",$shipping_id);
		$shipping_row = array();
		foreach($shipping_id_array as $idx=>$v)
		{
			$shipping_ids = explode("_",$v);
			$order[$shipping_ids[0]]['order']['shipping_id'] = intval($shipping_ids[1]);
		}
		
		
		$bonus = $_REQUEST['bonus_id'];
		$bonus_array = explode(",",$bonus);
	
		$bonus_row = array();
		foreach($bonus_array as $idx=>$v)
		{
			$bonus_ids = explode("_",$v);
			$bonus_row[$bonus_ids[0]] = $bonus_ids[1];
			$bonus = bonus_info(intval($bonus_ids[1]));
			if ((!empty($bonus) && $bonus['user_id'] == $user_id && $bonus['suppliers_id'] == $bonus_ids[0]) || $bonus_ids[1] == 0)
			{
				$order[$bonus_ids[0]]['order']['bonus_id'] = intval($bonus_ids[1]);
				//echo $order[$bonus_ids[0]]['order']['bonus_id'] ;exit;
			}
			else
			{
				$order[$suppliers_id]['order']['bonus_id'] = 0;
				$result['error'] = $_LANG['invalid_bonus'];
			}
		
		}		
		
        
		
      //  $order[$suppliers_id]['order']['shipping_id'] = intval($_REQUEST['shipping_id']);
        $order[$suppliers_id]['order']['express_id']  = intval($_REQUEST['express_id']);
        $order[$suppliers_id]['order']['point_id']    = 0;
        $regions = array($consignee['country'], $consignee['province'], $consignee['city'], $consignee['district']);




/* 计算订单的费用 */       
        $total = app_order_fee($order, $cart_goods, $consignee,$user_id);
		
		
	
        $result['data'] = $total;
    }

    echo $json->encode($result);
    exit;
}
elseif ($action == 'change_bonus')
{
    /*------------------------------------------------------ */
    //-- 改变红包
    /*------------------------------------------------------ */
    include_once('includes/cls_json.php');
    $result = array('error' => '', 'content' => '');

    /* 取得购物类型 */
    $flow_type = isset($_SESSION['flow_type']) ? intval($_SESSION['flow_type']) : CART_GENERAL_GOODS;
    $suppliers_id = isset($_REQUEST['suppliers_id']) ? intval($_REQUEST['suppliers_id']) : 0;
    /* 获得收货人信息 */
    $consignee = get_consignee($user_id);
    /* 对商品信息赋值 */
    $cart_goods = app_cart_goods($flow_type,$user_id); // 取得商品列表，计算合计


    if (empty($cart_goods[$suppliers_id]) || !check_consignee_info($consignee, $flow_type))
    {
        $result['error'] ='购物车为空';
    }
    else
    {
		
        /* 取得订单信息 */
        $order = app_flow_order_info($user_id);
		
		
		
		$shipping_id = $_REQUEST['shipping_id'];
		$shipping_id_array = explode(",",$shipping_id);
		$shipping_row = array();
		foreach($shipping_id_array as $idx=>$v)
		{
			$shipping_ids = explode("_",$v);
			$order[$shipping_ids[0]]['order']['shipping_id'] = intval($shipping_ids[1]);
		}
		
		
		$bonus = $_REQUEST['bonus_id'];
		$bonus_array = explode(",",$bonus);
		$bonus_row = array();
		foreach($bonus_array as $idx=>$v)
		{
			$bonus_ids = explode("_",$v);
			$bonus_row[$bonus_ids[0]] = $bonus_ids[1];
			$bonus = bonus_info(intval($bonus_ids[1]));
			if ((!empty($bonus) && $bonus['user_id'] == $user_id && $bonus['suppliers_id'] == $bonus_ids[0]) || $bonus_ids[1] == 0)
			{
				$order[$bonus_ids[0]]['order']['bonus_id'] = intval($bonus_ids[1]);
			}
			else
			{
				$order[$suppliers_id]['order']['bonus_id'] = 0;
				$result['error'] = $_LANG['invalid_bonus'];
			}
		
		}
		
		
        /* 计算订单的费用 */
        $total = app_order_fee($order, $cart_goods, $consignee,$user_id);
        $result['data'] = $total;
    }
    $json = new JSON();
    die($json->encode($result));
}
elseif($action =='alipayresult')
{
	include_once('includes/lib_clips.php');
    include_once('includes/lib_payment.php');
    include_once('includes/cls_json.php');

	//$result = 
	$resultstatus = $_REQUEST['resultstatus'];
	$order_sn = $_REQUEST['order_sn'];
	$order_id = $_REQUEST['order_id'];
	$attach = $order_id;	
	
	//$attach ='';
	$result = alipay_change_wfx_paystatus($resultstatus,$order_sn,$attach);
	if($result)
	{
	
		 $results['pay'] =1;
		 $results['order_id'] = $order_id;
		 $orderid_row = explode(',',$order_id);
		 $order_row = $db->getRow("select * from ".$hhs->table('order_info')." where order_id='$orderid_row[0]'");

		 $results['order'] = $order_row;
  	 	 $json = new JSON();
   		 die($json->encode($results));
	}
	else
	{
		 $results['order_id'] = $order_id;
		 
		 $results['pay'] =0;
  	 	 $json = new JSON();
   		 die($json->encode($results));
		
	}
	
}
function app_addto_cart($goods_id, $num = 1, $spec = array(), $parent = 0,$user_id)
{
    clear_cart(5,$user_id);

    $GLOBALS['err']->clean();
    $_parent_id = $parent;

    /* 取得商品信息 */
    $sql = "SELECT g.rate_1,g.rate_2,g.rate_3,g.limit_buy_one, g.limit_buy_bumber, g.is_fresh, g.goods_name, g.goods_sn, g.is_on_sale, g.is_real, ".
                "g.market_price, g.shop_price AS org_price, g.promote_price, g.promote_start_date, ".
                "g.promote_end_date, g.goods_weight, g.integral, g.extension_code,g.goods_thumb as 'goods_img',g.suppliers_id,  ".
                "g.goods_number, g.is_alone_sale, g.is_shipping,".
                "IFNULL(mp.user_price, g.shop_price * '$_SESSION[discount]') AS shop_price ".
            " FROM " .$GLOBALS['hhs']->table('goods'). " AS g ".
            " LEFT JOIN " . $GLOBALS['hhs']->table('member_price') . " AS mp ".
                    "ON mp.goods_id = g.goods_id AND mp.user_rank = '$_SESSION[user_rank]' ".
            " WHERE g.goods_id = '$goods_id'" .
            " AND g.is_delete = 0";
    $goods = $GLOBALS['db']->getRow($sql);

    if (empty($goods))
    {
        $GLOBALS['err']->add($GLOBALS['_LANG']['goods_not_exists'], ERR_NOT_EXISTS);

        return false;
    }

    /* 如果是作为配件添加到购物车的，需要先检查购物车里面是否已经有基本件 */
    if ($parent > 0)
    {
        $sql = "SELECT COUNT(*) FROM " . $GLOBALS['hhs']->table('cart') .
                " WHERE goods_id='$parent' AND session_id='" . SESS_ID . "' AND extension_code <> 'package_buy'";
        if ($GLOBALS['db']->getOne($sql) == 0)
        {
            $GLOBALS['err']->add($GLOBALS['_LANG']['no_basic_goods'], ERR_NO_BASIC_GOODS);

            return false;
        }
    }

    /* 是否正在销售 */
    if ($goods['is_on_sale'] == 0)
    {
        $GLOBALS['err']->add($GLOBALS['_LANG']['not_on_sale'], ERR_NOT_ON_SALE);

        return false;
    }

    /* 不是配件时检查是否允许单独销售 */
    if (empty($parent) && $goods['is_alone_sale'] == 0)
    {
        $GLOBALS['err']->add($GLOBALS['_LANG']['cannt_alone_sale'], ERR_CANNT_ALONE_SALE);

        return false;
    }
    
    if($goods['is_fresh'] ==1)
    {
        $sql = 'select count(*) from ' . $GLOBALS['hhs']->table('order_info') . ' where user_id = ' . $user_id . ' ';
        $is_buy = $GLOBALS['db']->GetOne($sql);
        if($is_buy)
        {
            $GLOBALS['err']->add('该商品仅限新人购买！', 5);

            return false;
        }       
    }    
    if($goods['limit_buy_one'] ==1)
    {
        $where = '  og.goods_id = ' . $goods_id . ' and oi.order_status=1 and oi.pay_status=2 ';
        $sql = 'select count(oi.order_id) from ' . $GLOBALS['hhs']->table('order_info') . ' as oi left join ' . $GLOBALS['hhs']->table('order_goods') . ' as og on oi.order_id = og.order_id where ' . $where . ' and oi.user_id = ' . $_SESSION['user_id'] . ' ';
        $is_buy = $GLOBALS['db']->GetOne($sql);
        if($is_buy)
        {
            $GLOBALS['err']->add('该商品限购，一个用户只能购买一次', 5);

            return false;
        }
        
    }
    if($goods['limit_buy_bumber'] && $num > $goods['limit_buy_bumber'])
    {
        $GLOBALS['err']->add('该商品限购，一个用户只能购买'.$goods['limit_buy_bumber'], 5);

        return false;
    }

    /* 如果商品有规格则取规格商品信息 配件除外 */
    $sql = "SELECT * FROM " .$GLOBALS['hhs']->table('products'). " WHERE goods_id = '$goods_id' LIMIT 0, 1";
    $prod = $GLOBALS['db']->getRow($sql);

    if (is_spec($spec) && !empty($prod))
    {
        $product_info = get_products_info($goods_id, $spec);
    }
    if (empty($product_info))
    {
        $product_info = array('product_number' => '', 'product_id' => 0);
    }

    /* 检查：库存 */
    if ($GLOBALS['_CFG']['use_storage'] == 1)
    {
        //检查：商品购买数量是否大于总库存
        if ($num > $goods['goods_number'])
        {
            $GLOBALS['err']->add(sprintf($GLOBALS['_LANG']['shortage'], $goods['goods_number']), ERR_OUT_OF_STOCK);

            return false;
        }

        //商品存在规格 是货品 检查该货品库存
        if (is_spec($spec) && !empty($prod))
        {
            if (!empty($spec))
            {
                /* 取规格的货品库存 */
                if ($num > $product_info['product_number'])
                {
                    $GLOBALS['err']->add(sprintf($GLOBALS['_LANG']['shortage'], $product_info['product_number']), ERR_OUT_OF_STOCK);
    
                    return false;
                }
            }
        }       
    }

    /* 计算商品的促销价格 */
    $spec_price             = spec_price($spec);
    $goods_price            = get_final_price($goods_id, $num, true, $spec);
    $goods['market_price'] += $spec_price;
    $goods_attr             = get_goods_attr_info($spec);
    $goods_attr_id          = join(',', $spec);
    /* 初始化要插入购物车的基本件数据 */
    $parent = array(
        'user_id'       => $user_id,
        'session_id'    => SESS_ID,
        'goods_id'      => $goods_id,
        'goods_sn'      => addslashes($goods['goods_sn']),
        'product_id'    => $product_info['product_id'],
        'goods_name'    => addslashes($goods['goods_name']),
        'goods_img'     => $goods['goods_img'],
        'market_price'  => $goods['market_price'],
        'suppliers_id'  => $goods['suppliers_id'],
        'goods_attr'    => addslashes($goods_attr),
        'goods_attr_id' => $goods_attr_id,
        'is_real'       => $goods['is_real'],
        'extension_code'=> $goods['extension_code'],
        'is_gift'       => 0,
        'is_shipping'   => $goods['is_shipping'],
        'rate_1'        => $goods['rate_1'],
        'rate_2'        => $goods['rate_2'],
        'rate_3'        => $goods['rate_3'],
        'rec_type'      => CART_GENERAL_GOODS
    );

    /* 如果该配件在添加为基本件的配件时，所设置的“配件价格”比原价低，即此配件在价格上提供了优惠， */
    /* 则按照该配件的优惠价格卖，但是每一个基本件只能购买一个优惠价格的“该配件”，多买的“该配件”不享 */
    /* 受此优惠 */
    $basic_list = array();
    $sql = "SELECT parent_id, goods_price " .
            "FROM " . $GLOBALS['hhs']->table('group_goods') .
            " WHERE goods_id = '$goods_id'" .
            " AND goods_price < '$goods_price'" .
            " AND parent_id = '$_parent_id'" .
            " ORDER BY goods_price";
    $res = $GLOBALS['db']->query($sql);
    while ($row = $GLOBALS['db']->fetchRow($res))
    {
        $basic_list[$row['parent_id']] = $row['goods_price'];
    }

    /* 取得购物车中该商品每个基本件的数量 */
    $basic_count_list = array();
    if ($basic_list)
    {
        $sql = "SELECT goods_id, SUM(goods_number) AS count " .
                "FROM " . $GLOBALS['hhs']->table('cart') .
                " WHERE user_id = '" . $user_id . "'" .
                " AND parent_id = 0" .
                " AND extension_code <> 'package_buy' " .
                " AND goods_id " . db_create_in(array_keys($basic_list)) .
                " GROUP BY goods_id";
        $res = $GLOBALS['db']->query($sql);
        while ($row = $GLOBALS['db']->fetchRow($res))
        {
            $basic_count_list[$row['goods_id']] = $row['count'];
        }
    }

    /* 取得购物车中该商品每个基本件已有该商品配件数量，计算出每个基本件还能有几个该商品配件 */
    /* 一个基本件对应一个该商品配件 */
    if ($basic_count_list)
    {
        $sql = "SELECT parent_id, SUM(goods_number) AS count " .
                "FROM " . $GLOBALS['hhs']->table('cart') .
                " WHERE session_id = '" . SESS_ID . "'" .
                " AND goods_id = '$goods_id'" .
                " AND extension_code <> 'package_buy' " .
                " AND parent_id " . db_create_in(array_keys($basic_count_list)) .
                " GROUP BY parent_id";
        $res = $GLOBALS['db']->query($sql);
        while ($row = $GLOBALS['db']->fetchRow($res))
        {
            $basic_count_list[$row['parent_id']] -= $row['count'];
        }
    }

    /* 循环插入配件 如果是配件则用其添加数量依次为购物车中所有属于其的基本件添加足够数量的该配件 */
    foreach ($basic_list as $parent_id => $fitting_price)
    {
        /* 如果已全部插入，退出 */
        if ($num <= 0)
        {
            break;
        }

        /* 如果该基本件不再购物车中，执行下一个 */
        if (!isset($basic_count_list[$parent_id]))
        {
            continue;
        }

        /* 如果该基本件的配件数量已满，执行下一个基本件 */
        if ($basic_count_list[$parent_id] <= 0)
        {
            continue;
        }

        /* 作为该基本件的配件插入 */
        $parent['goods_price']  = max($fitting_price, 0) + $spec_price; //允许该配件优惠价格为0
        $parent['goods_number'] = min($num, $basic_count_list[$parent_id]);
        $parent['parent_id']    = $parent_id;

        /* 添加 */
        $GLOBALS['db']->autoExecute($GLOBALS['hhs']->table('cart'), $parent, 'INSERT');

        /* 改变数量 */
        $num -= $parent['goods_number'];
    }

    /* 如果数量不为0，作为基本件插入 */
    if ($num > 0)
    {
        /* 检查该商品是否已经存在在购物车中 */
        $sql = "SELECT goods_number FROM " .$GLOBALS['hhs']->table('cart').
                " WHERE user_id = '" .$user_id. "' AND goods_id = '$goods_id' ".
                " AND parent_id = 0 AND goods_attr = '" .get_goods_attr_info($spec). "' " .
                " AND extension_code <> 'package_buy' " .
                " AND rec_type = 'CART_GENERAL_GOODS'";

        $row = $GLOBALS['db']->getRow($sql);

        if($row) //如果购物车已经有此物品，则更新
        {
            $num += $row['goods_number'];
            if(is_spec($spec) && !empty($prod) )
            {
             $goods_storage=$product_info['product_number'];
            }
            else
            {
                $goods_storage=$goods['goods_number'];
            }

            if ($goods['limit_buy_bumber'] > 0 && $goods['limit_buy_bumber'] < $num)
            {
                $GLOBALS['err']->add('该商品限购，一个用户只能购买' . $goods['limit_buy_bumber']);

                return false;
            }

            if ($GLOBALS['_CFG']['use_storage'] == 0 || $num <= $goods_storage)
            {
                $goods_price = get_final_price($goods_id, $num, true, $spec);
                $sql = "UPDATE " . $GLOBALS['hhs']->table('cart') . " SET goods_number = '$num'" .
                       " , goods_price = '$goods_price'".
                       " WHERE user_id = '" .$user_id. "' AND goods_id = '$goods_id' ".
                       " AND parent_id = 0 AND goods_attr = '" .get_goods_attr_info($spec). "' " .
                       " AND extension_code <> 'package_buy' " .
                       "AND rec_type = 'CART_GENERAL_GOODS'";
                $GLOBALS['db']->query($sql);
            }
            else
            {
               $GLOBALS['err']->add(sprintf($GLOBALS['_LANG']['shortage'], $num), ERR_OUT_OF_STOCK);

                return false;
            }
        }
        else //购物车没有此物品，则插入
        {
            $goods_price = get_final_price($goods_id, $num, true, $spec);
            $parent['goods_price']  = max($goods_price, 0);
            $parent['goods_number'] = $num;
            $parent['parent_id']    = 0;
			
			
            $GLOBALS['db']->autoExecute($GLOBALS['hhs']->table('cart'), $parent, 'INSERT');
        }
    }

    /* 把赠品删除 */
    $sql = "DELETE FROM " . $GLOBALS['hhs']->table('cart') . " WHERE user_id = '" . $user_id . "' AND is_gift <> 0";
    $GLOBALS['db']->query($sql);

    return true;
}

/**
 * 获得购物车中的商品
 *
 * @access  public
 * @return  array
 */
function app_get_cart_goods($user_id)
{
    /* 初始化 */
    $goods_list = array();
    $total = array(
        'goods_price'  => 0, // 本店售价合计（有格式）
        'market_price' => 0, // 市场售价合计（有格式）
        'saving'       => 0, // 节省金额（有格式）
        'save_rate'    => 0, // 节省百分比
        'goods_amount' => 0, // 本店售价合计（无格式）
    );

    /* 循环、统计 */
    $sql = "SELECT *, IF(parent_id, parent_id, goods_id) AS pid " .
            " FROM " . $GLOBALS['hhs']->table('cart') . " " .
            " WHERE user_id = '" . $user_id . "' AND rec_type = '" . CART_GENERAL_GOODS . "'" .
            " ORDER BY pid, parent_id";
    $res = $GLOBALS['db']->query($sql);

    /* 用于统计购物车中实体商品和虚拟商品的个数 */
    $virtual_goods_count = 0;
    $real_goods_count    = 0;

    while ($row = $GLOBALS['db']->fetchRow($res))
    {
        if($row['is_checked'])
        {
            $total['goods_price']  += $row['goods_price'] * $row['goods_number'];
            $total['market_price'] += $row['market_price'] * $row['goods_number'];
        }

        $row['subtotal']     = price_format($row['goods_price'] * $row['goods_number'], false);
        $row['goods_price']  = price_format($row['goods_price'], false);
        $row['market_price'] = price_format($row['market_price'], false);

        /* 统计实体商品和虚拟商品的个数 */
        if ($row['is_real'])
        {
            $real_goods_count++;
        }
        else
        {
            $virtual_goods_count++;
        }

        /* 查询规格 */
        if (trim($row['goods_attr']) != '')
        {
            $row['goods_attr']=addslashes($row['goods_attr']);

            $sql = "SELECT attr_value FROM " . $GLOBALS['hhs']->table('goods_attr') . " WHERE goods_attr_id " .

            db_create_in($row['goods_attr']);

             $attr_list = $GLOBALS['db']->getCol($sql);

             foreach ($attr_list AS $attr)
             {

                 $row['goods_name'] .= ' [' . $attr . '] ';

             }
        }
        /* 增加是否在购物车里显示商品图 */
       // if (($GLOBALS['_CFG']['show_goods_in_cart'] == "2" || $GLOBALS['_CFG']['show_goods_in_cart'] == "3") && $row['extension_code'] != 'package_buy')
        //{
            $goods_thumb = $GLOBALS['db']->getOne("SELECT `goods_thumb` FROM " . $GLOBALS['hhs']->table('goods') . " WHERE `goods_id`='{$row['goods_id']}'");
            $row['goods_thumb'] = get_image_path($row['goods_id'], $goods_thumb, true);
       // }
        if ($row['extension_code'] == 'package_buy')
        {
            $row['package_goods_list'] = get_package_goods($row['goods_id']);
        }
        $goods_list[] = $row;
    }
    $total['goods_amount'] = $total['goods_price'];
    $total['saving']       = price_format($total['market_price'] - $total['goods_price'], false);
    if ($total['market_price'] > 0)
    {
        $total['save_rate'] = $total['market_price'] ? round(($total['market_price'] - $total['goods_price']) *
        100 / $total['market_price']).'%' : 0;
    }
    $total['goods_price']  = price_format($total['goods_price'], false);
    $total['market_price'] = price_format($total['market_price'], false);
    $total['real_goods_count']    = $real_goods_count;
    $total['virtual_goods_count'] = $virtual_goods_count;

    $suppliers_goods_list = array();
    foreach ($goods_list as $key => $goods) {
        $suppliers_goods_list[$goods['suppliers_id']]['goods_list'][] = $goods;
        if(! isset($suppliers_goods_list[$goods['suppliers_id']]['suppliers_name']))
        {
            if($goods['suppliers_id'])
            {
                $suppliers = $GLOBALS['db']->getRow("SELECT suppliers_name,supp_logo FROM " . $GLOBALS['hhs']->table('suppliers') . " WHERE `suppliers_id`='{$goods['suppliers_id']}'");
            }
            else{
                $suppliers = array('suppliers_name'=>'自营店','supp_logo'=>'');
            }
            $suppliers_goods_list[$goods['suppliers_id']]['suppliers_name'] = $suppliers['suppliers_name'];
            $suppliers_goods_list[$goods['suppliers_id']]['logo'] = $suppliers['supp_logo'];
			$suppliers_goods_list[$goods['suppliers_id']]['suppliers_id'] = $goods['suppliers_id'];
        }
    }

    return array('goods_list' => $suppliers_goods_list, 'total' => $total);
}
/**
 * 删除购物车中的商品
 *
 * @access  public
 * @param   integer $id
 * @return  void
 */
function flow_drop_cart_goods($id,$user_id)
{
    /* 取得商品id */
    $sql = "SELECT * FROM " .$GLOBALS['hhs']->table('cart'). " WHERE rec_id = '$id'";
    
	$row = $GLOBALS['db']->getRow($sql);
	
	
    if ($row)
    {
        //如果是超值礼包
        if ($row['extension_code'] == 'package_buy')
        {
            $sql = "DELETE FROM " . $GLOBALS['hhs']->table('cart') .
                    " WHERE user_id = '" . $user_id . "' " .
                    "AND rec_id = '$id' LIMIT 1";
        }

        //如果是普通商品，同时删除所有赠品及其配件
        elseif ($row['parent_id'] == 0 && $row['is_gift'] == 0)
        {
            /* 检查购物车中该普通商品的不可单独销售的配件并删除 */
            $sql = "SELECT c.rec_id
                    FROM " . $GLOBALS['hhs']->table('cart') . " AS c, " . $GLOBALS['hhs']->table('group_goods') . " AS gg, " . $GLOBALS['hhs']->table('goods'). " AS g
                    WHERE gg.parent_id = '" . $row['goods_id'] . "'
                    AND c.goods_id = gg.goods_id
                    AND c.parent_id = '" . $row['goods_id'] . "'
                    AND c.extension_code <> 'package_buy'
                    AND gg.goods_id = g.goods_id
                    AND g.is_alone_sale = 0";
            $res = $GLOBALS['db']->query($sql);
            $_del_str = $id . ',';
            while ($id_alone_sale_goods = $GLOBALS['db']->fetchRow($res))
            {
                $_del_str .= $id_alone_sale_goods['rec_id'] . ',';
            }
            $_del_str = trim($_del_str, ',');

            $sql = "DELETE FROM " . $GLOBALS['hhs']->table('cart') .
                    " WHERE user_id = '" . $user_id . "' " .
                    "AND (rec_id IN ($_del_str) OR parent_id = '$row[goods_id]' OR is_gift <> 0)";
        }

        //如果不是普通商品，只删除该商品即可
        else
        {
            $sql = "DELETE FROM " . $GLOBALS['hhs']->table('cart') .
                    " WHERE user_id = '" . $user_id . "' " .
                    "AND rec_id = '$id' LIMIT 1";
        }

        $GLOBALS['db']->query($sql);
    }

    flow_clear_cart_alone($user_id);
}

/**
 * 删除购物车中不能单独销售的商品
 *
 * @access  public
 * @return  void
 */
function flow_clear_cart_alone($user_id)
{
    /* 查询：购物车中所有不可以单独销售的配件 */
    $sql = "SELECT c.rec_id, gg.parent_id
            FROM " . $GLOBALS['hhs']->table('cart') . " AS c
                LEFT JOIN " . $GLOBALS['hhs']->table('group_goods') . " AS gg ON c.goods_id = gg.goods_id
                LEFT JOIN" . $GLOBALS['hhs']->table('goods') . " AS g ON c.goods_id = g.goods_id
            WHERE c.user_id = '" . $user_id . "'
            AND c.extension_code <> 'package_buy'
            AND gg.parent_id > 0
            AND g.is_alone_sale = 0";
    $res = $GLOBALS['db']->query($sql);
    $rec_id = array();
    while ($row = $GLOBALS['db']->fetchRow($res))
    {
        $rec_id[$row['rec_id']][] = $row['parent_id'];
    }

    if (empty($rec_id))
    {
        return;
    }

    /* 查询：购物车中所有商品 */
    $sql = "SELECT DISTINCT goods_id
            FROM " . $GLOBALS['hhs']->table('cart') . "
            WHERE user_id = '" . $user_id . "'
            AND extension_code <> 'package_buy'";
    $res = $GLOBALS['db']->query($sql);
    $cart_good = array();
    while ($row = $GLOBALS['db']->fetchRow($res))
    {
        $cart_good[] = $row['goods_id'];
    }

    if (empty($cart_good))
    {
        return;
    }

    /* 如果购物车中不可以单独销售配件的基本件不存在则删除该配件 */
    $del_rec_id = '';
    foreach ($rec_id as $key => $value)
    {
        foreach ($value as $v)
        {
            if (in_array($v, $cart_good))
            {
                continue 2;
            }
        }

        $del_rec_id = $key . ',';
    }
    $del_rec_id = trim($del_rec_id, ',');

    if ($del_rec_id == '')
    {
        return;
    }

    /* 删除 */
    $sql = "DELETE FROM " . $GLOBALS['hhs']->table('cart') ."
            WHERE user_id = '" . $user_id . "'
            AND rec_id IN ($del_rec_id)";
    $GLOBALS['db']->query($sql);
}


/**
 * 更新购物车中的商品数量
 *
 * @access  public
 * @param   array   $arr
 * @return  void
 */
function flow_update_cart($arr,$user_id)
{
    include_once('includes/cls_json.php');
    $json  = new JSON; 
    /* 处理 */
    foreach ($arr AS $key => $val)
    {
        $val = intval(make_semiangle($val));
        if ($val <= 0 || !is_numeric($key))
        {
            continue;
        }

        //查询：
        $sql = "SELECT `goods_id`, `goods_attr_id`, `product_id`, `extension_code` FROM" .$GLOBALS['hhs']->table('cart').
               " WHERE rec_id='$key' AND user_id='" . $user_id . "'";
        $goods = $GLOBALS['db']->getRow($sql);

        $sql = "SELECT g.goods_name, g.goods_number, g.limit_buy_bumber ".
                "FROM " .$GLOBALS['hhs']->table('goods'). " AS g, ".
                    $GLOBALS['hhs']->table('cart'). " AS c ".
                "WHERE g.goods_id = c.goods_id AND c.rec_id = '$key'";
        $row = $GLOBALS['db']->getRow($sql);

        if($row['limit_buy_bumber'] > 0 && $val > $row['limit_buy_bumber'])
        {
            $result = array(
                'error'        => 1, 
                'message'      => '该商品限购，一个用户只能购买' . $row['limit_buy_bumber'], 
                'goods_number'      => $row['limit_buy_bumber'], 
                'rec_id'       => $key,
                'content' => ''
            );
            die($json->encode($result));
        }

        //查询：系统启用了库存，检查输入的商品数量是否有效
        if (intval($GLOBALS['_CFG']['use_storage']) > 0 && $goods['extension_code'] != 'package_buy')
        {
            if ($row['goods_number'] < $val)
            {
                // show_message(sprintf($GLOBALS['_LANG']['stock_insufficiency'], $row['goods_name'],
                // $row['goods_number'], $row['goods_number']));
                $result = array(
                    'error'        => 1, 
                    'message'      => sprintf($GLOBALS['_LANG']['stock_insufficiency'], $row['goods_name'],
                    $row['goods_number'], $row['goods_number']), 
                    'goods_number'      => $row['goods_number'], 
                    'rec_id'       => $key,
                    'content' => ''
                );
                die($json->encode($result));
                exit;
            }
            /* 是货品 */
            $goods['product_id'] = trim($goods['product_id']);
            if (!empty($goods['product_id']))
            {
                $sql = "SELECT product_number FROM " .$GLOBALS['hhs']->table('products'). " WHERE goods_id = '" . $goods['goods_id'] . "' AND product_id = '" . $goods['product_id'] . "'";

                $product_number = $GLOBALS['db']->getOne($sql);
                if ($product_number < $val)
                {
                    // show_message(sprintf($GLOBALS['_LANG']['stock_insufficiency'], $row['goods_name'],
                    // $product_number['product_number'], $product_number['product_number']));
                    // exit;
                    $result = array(
                        'error'        => 1, 
                        'message'      => sprintf($GLOBALS['_LANG']['stock_insufficiency'], $row['goods_name'],
                    $product_number['product_number'], $product_number['product_number']), 
                        'goods_number'      => $product_number['product_number'], 
                        'rec_id'       => $key,
                        'content' => ''
                    );
                    die($json->encode($result));
                }
            }
        }
        elseif (intval($GLOBALS['_CFG']['use_storage']) > 0 && $goods['extension_code'] == 'package_buy')
        {
            if (judge_package_stock($goods['goods_id'], $val))
            {
                show_message($GLOBALS['_LANG']['package_stock_insufficiency']);
                exit;
            }
        }

        /* 查询：检查该项是否为基本件 以及是否存在配件 */
        /* 此处配件是指添加商品时附加的并且是设置了优惠价格的配件 此类配件都有parent_id goods_number为1 */
        $sql = "SELECT b.goods_number, b.rec_id
                FROM " .$GLOBALS['hhs']->table('cart') . " a, " .$GLOBALS['hhs']->table('cart') . " b
                WHERE a.rec_id = '$key'
                AND a.user_id = '" . $user_id . "'
                AND a.extension_code <> 'package_buy'
                AND b.parent_id = a.goods_id
                AND b.user_id = '" . $user_id . "'";

        $offers_accessories_res = $GLOBALS['db']->query($sql);

        //订货数量大于0
        if ($val > 0)
        {
            /* 判断是否为超出数量的优惠价格的配件 删除*/
            $row_num = 1;
            while ($offers_accessories_row = $GLOBALS['db']->fetchRow($offers_accessories_res))
            {
                if ($row_num > $val)
                {
                    $sql = "DELETE FROM " . $GLOBALS['hhs']->table('cart') .
                            " WHERE user_id = '" . $user_id . "' " .
                            "AND rec_id = '" . $offers_accessories_row['rec_id'] ."' LIMIT 1";
                    $GLOBALS['db']->query($sql);
                }

                $row_num ++;
            }

            /* 处理超值礼包 */
            if ($goods['extension_code'] == 'package_buy')
            {
                //更新购物车中的商品数量
                $sql = "UPDATE " .$GLOBALS['hhs']->table('cart').
                        " SET goods_number = '$val' WHERE rec_id='$key' AND user_id='" . $user_id . "'";
            }
            /* 处理普通商品或非优惠的配件 */
            else
            {
                $attr_id    = empty($goods['goods_attr_id']) ? array() : explode(',', $goods['goods_attr_id']);
                $goods_price = get_final_price($goods['goods_id'], $val, true, $attr_id);

                //更新购物车中的商品数量
                $sql = "UPDATE " .$GLOBALS['hhs']->table('cart').
                        " SET goods_number = '$val', goods_price = '$goods_price' WHERE rec_id='$key' AND user_id='" . $user_id . "'";
            }
        }
        //订货数量等于0
        else
        {
            /* 如果是基本件并且有优惠价格的配件则删除优惠价格的配件 */
            while ($offers_accessories_row = $GLOBALS['db']->fetchRow($offers_accessories_res))
            {
                $sql = "DELETE FROM " . $GLOBALS['hhs']->table('cart') .
                        " WHERE user_id = '" . $user_id . "' " .
                        "AND rec_id = '" . $offers_accessories_row['rec_id'] ."' LIMIT 1";
                $GLOBALS['db']->query($sql);
            }

            $sql = "DELETE FROM " .$GLOBALS['hhs']->table('cart').
                " WHERE rec_id='$key' AND user_id='" .$user_id. "'";
        }

        $GLOBALS['db']->query($sql);
    }

    /* 删除所有赠品 */
    $sql = "DELETE FROM " . $GLOBALS['hhs']->table('cart') . " WHERE user_id = '" .$user_id. "' AND is_gift <> 0";
    $GLOBALS['db']->query($sql);
}

/**
 * 取得购物车商品
 * @param   int     $type   类型：默认普通商品
 * @return  array   购物车商品数组
 */
function app_cart_goods($type = CART_GENERAL_GOODS,$user_id)
{
    $sql = "SELECT IFNULL(s.suppliers_name,'自营店') as suppliers_name,s.supp_logo,c.suppliers_id, c.rec_id, c.user_id, c.goods_id, c.goods_name, c.goods_sn, c.goods_number, c.goods_img, c.is_checked, " .
            "c.market_price, c.goods_price, c.goods_attr, c.is_real, c.extension_code, c.parent_id, c.is_gift, c.is_shipping, " .
            "c.goods_price * c.goods_number AS subtotal " .
            "FROM " . $GLOBALS['hhs']->table('cart') . " as c" .
            " LEFT JOIN " . $GLOBALS['hhs']->table('suppliers') . " as s on s.suppliers_id = c.suppliers_id ".
            " WHERE c.user_id = '" . $user_id . "' " .
            " AND is_checked = 1 AND c.rec_type = '$type'";
    $arr = $GLOBALS['db']->getAll($sql);

    /* 格式化价格及礼包商品 */
    foreach ($arr as $key => $value)
    {
        $arr[$key]['formated_market_price'] = price_format($value['market_price'], false);
        $arr[$key]['formated_goods_price']  = price_format($value['goods_price'], false);
        $arr[$key]['formated_subtotal']     = price_format($value['subtotal'], false);

        if ($value['extension_code'] == 'package_buy')
        {
            $arr[$key]['package_goods_list'] = get_package_goods($value['goods_id']);
        }
    }

    // 重构数组
    $data = array();
    foreach ($arr as $key => $value) {
        $suppliers_id = $value['suppliers_id'];
        $subtotal = isset($data[$suppliers_id]['subtotal']) ? $data[$suppliers_id]['subtotal'] : 0.00;
        $subtotal += $value['subtotal'];

        $data[$suppliers_id]['suppliers_id']   = $value['suppliers_id'];
        $data[$suppliers_id]['suppliers_name'] = $value['suppliers_name'];
        $data[$suppliers_id]['logo']           = $value['supp_logo'];
        $data[$suppliers_id]['subtotal']       = price_format($subtotal, false);
        $data[$suppliers_id]['goods_list'][]   = $value;
        unset($value);
    }
    // end

    return $data;
}
/**
 * 获得订单信息
 *
 * @access  private
 * @return  array
 */
function app_flow_order_info($user_id)
{
    // unset($_SESSION['flow_order']);
    $orders = isset($_SESSION['flow_order']) ? $_SESSION['flow_order'] : array();

    $suppliers = app_cart_goods_suppliers('0',$user_id);


    foreach ($suppliers as $supplier) {
        $suppliers_id = $supplier['suppliers_id'];
        $order = isset($orders[$suppliers_id]['order']) ? $orders[$suppliers_id]['order']: array();
        /* 初始化配送和支付方式 */
        if (!isset($order['shipping_id']))
        {
            $order['shipping_id'] = 0;
        }

        if (!isset($order['pack_id']))
        {
            $order['pack_id'] = 0;  // 初始化包装
        }
        if (!isset($order['card_id']))
        {
            $order['card_id'] = 0;  // 初始化贺卡
        }
        if (!isset($order['bonus']))
        {
            $order['bonus'] = 0;    // 初始化红包
        }
        if (!isset($order['integral']))
        {
            $order['integral'] = 0; // 初始化积分
        }
        if (!isset($order['surplus']))
        {
            $order['surplus'] = 0;  // 初始化余额
        }

        /* 扩展信息 */
        if (isset($_SESSION['flow_type']) && intval($_SESSION['flow_type']) != CART_GENERAL_GOODS)
        {
            $order['extension_code'] = $_SESSION['extension_code'];
            $order['extension_id'] = $_SESSION['extension_id'];
        }
        $orders[$suppliers_id]['order'] = $order;
    }

	

    return $orders;
}
function app_cart_goods_suppliers($type = CART_GENERAL_GOODS,$user_id)
{
    $sql = "SELECT suppliers_id FROM " . $GLOBALS['hhs']->table('cart') .
            " WHERE user_id = '" . $user_id . "' " .
            "AND rec_type = '$type'";
			
    return $GLOBALS['db']->getAll($sql);
}


function arrayToObject($e){
    if( gettype($e)!='array' ) return;
    foreach($e as $k=>$v){
        if( gettype($v)=='array' || getType($v)=='object' )
            $e[$k]=(object)arrayToObject($v);
    }
    return (object)$e;
}

/**

 * 检查订单中商品库存

 *

 * @access  public

 * @param   array   $arr

 *

 * @return  void

 */

function flow_cart_stock($arr,$user_id)

{

    foreach ($arr AS $key => $val)

    {

        $val = intval(make_semiangle($val));

        if ($val <= 0 || !is_numeric($key))

        {

            continue;

        }



        $sql = "SELECT `goods_id`, `goods_attr_id`, `extension_code` FROM" .$GLOBALS['hhs']->table('cart').

               " WHERE rec_id='$key' AND user_id='" . $user_id . "'";

        $goods = $GLOBALS['db']->getRow($sql);



        $sql = "SELECT g.goods_name, g.goods_number, c.product_id ".

                "FROM " .$GLOBALS['hhs']->table('goods'). " AS g, ".

                    $GLOBALS['hhs']->table('cart'). " AS c ".

                "WHERE g.goods_id = c.goods_id AND c.rec_id = '$key'";

        $row = $GLOBALS['db']->getRow($sql);



        //系统启用了库存，检查输入的商品数量是否有效

        if (intval($GLOBALS['_CFG']['use_storage']) > 0 && $goods['extension_code'] != 'package_buy')

        {

            if ($row['goods_number'] < $val)

            {

                show_message(sprintf($GLOBALS['_LANG']['stock_insufficiency'], $row['goods_name'],

                $row['goods_number'], $row['goods_number']));

                exit;

            }



            /* 是货品 */

            $row['product_id'] = trim($row['product_id']);

            if (!empty($row['product_id']))

            {

                $sql = "SELECT product_number FROM " .$GLOBALS['hhs']->table('products'). " WHERE goods_id = '" . $goods['goods_id'] . "' AND product_id = '" . $row['product_id'] . "'";

                $product_number = $GLOBALS['db']->getOne($sql);

                if ($product_number < $val)

                {

                    show_message(sprintf($GLOBALS['_LANG']['stock_insufficiency'], $row['goods_name'],

                    $row['goods_number'], $row['goods_number']));

                    exit;

                }

            }

        }

        elseif (intval($GLOBALS['_CFG']['use_storage']) > 0 && $goods['extension_code'] == 'package_buy')

        {

            if (judge_package_stock($goods['goods_id'], $val))

            {

                show_message($GLOBALS['_LANG']['package_stock_insufficiency']);

                exit;

            }

        }

    }
}

function app_cart_amount($include_gift = true, $type = CART_GENERAL_GOODS, $suppliers_id = '-1',$user_id)
{
    $sql = "SELECT SUM(goods_price * goods_number) " .
            " FROM " . $GLOBALS['hhs']->table('cart') .
            " WHERE user_id = '" . $user_id . "' " .
            "AND rec_type = '$type' ";

    if (!$include_gift)
    {
        $sql .= ' AND is_gift = 0 AND goods_id > 0';
    }
    if($suppliers_id != '-1')
    {
        $sql .= ' AND suppliers_id = "'.$suppliers_id.'" ';        
    }

    return floatval($GLOBALS['db']->getOne($sql));
}
function app_clear_cart($type = CART_GENERAL_GOODS,$user_id)
{
    $sql = "DELETE FROM " . $GLOBALS['hhs']->table('cart') .
            " WHERE user_id = '" . $user_id . "' AND rec_type = '$type'";
    $GLOBALS['db']->query($sql);
}
function app_order_fee($orders, $goods, $consignee,$user_id)
{
	
    $total = array(
        'amount_fee'   => 0.00,
        'shipping_fee' => 0.00,
        'bonus_fee'    => 0.00,
    );
	
    foreach ($orders as $suppliers_id => $order) {
		
	
        $orders[$suppliers_id] = app_calc_suppliers_fee($order['order'], $goods[$suppliers_id]['goods_list'], $consignee, $suppliers_id,$user_id);
		
		
        unset($order);

        $total['amount_fee']   += $orders[$suppliers_id]['total']['amount_formated'];
        $total['shipping_fee'] += $orders[$suppliers_id]['total']['shipping_fee_formated'];
        $total['bonus_fee']    += $orders[$suppliers_id]['total']['bonus_formated'];
    }
    /* 保存订单信息 */
    $_SESSION['flow_order'] = $orders;

    $total['amount_fee']   = price_format($total['amount_fee'], false);
    $total['shipping_fee'] = price_format($total['shipping_fee'], false);
    $total['bonus_fee']    = price_format($total['bonus_fee'], false);

    return $total;
}
/**
 * 获得订单中的费用信息
 *
 * @access  public
 * @param   array   $order
 * @param   array   $goods
 * @param   array   $consignee
 * @param   bool    $is_gb_deposit  是否团购保证金（如果是，应付款金额只计算商品总额和支付费用，可以获得的积分取 $gift_integral）
 * @return  array
 */
function app_calc_suppliers_fee($order, $goods, $consignee, $suppliers_id,$user_id)
{
	
	
    $order['suppliers_id'] = $suppliers_id;
    $total  = array('real_goods_count' => 0,
                    'gift_amount'      => 0,
                    'goods_price'      => 0,
                    'market_price'     => 0,
                    'discount'         => 0,
                    'pack_fee'         => 0,
                    'card_fee'         => 0,
                    'shipping_fee'     => 0,
                    'shipping_insure'  => 0,
                    'integral_money'   => 0,
                    'bonus'            => 0,
                    'surplus'          => 0,
                    'cod_fee'          => 0,
                    'pay_fee'          => 0,
                    'tax'              => 0,
    );
    $weight = 0;

    /* 商品总价 */
    foreach ($goods AS $val)
    {
        /* 统计实体商品的个数 */
        if ($val['is_real'])
        {
            $total['real_goods_count']++;
        }

        $total['goods_price']  += $val['goods_price'] * $val['goods_number'];
        $total['market_price'] += $val['market_price'] * $val['goods_number'];
    }

    $total['saving']    = $total['market_price'] - $total['goods_price'];
    $total['save_rate'] = $total['market_price'] ? round($total['saving'] * 100 / $total['market_price']) . '%' : 0;

    $total['goods_price_formated']  = price_format($total['goods_price'], false);
    $total['market_price_formated'] = price_format($total['market_price'], false);
    $total['saving_formated']       = price_format($total['saving'], false);

    /* 折扣 */
    if ($order['extension_code'] != 'group_buy')
    {
        $discount = compute_discount();
        $total['discount'] = $discount['discount'];
        if ($total['discount'] > $total['goods_price'])
        {
            $total['discount'] = $total['goods_price'];
        }
    }
    $total['discount_formated'] = price_format($total['discount'], false);

    /* 税额 */
    if (!empty($order['need_inv']) && $order['inv_type'] != '')
    {
        /* 查税率 */
        $rate = 0;
        foreach ($GLOBALS['_CFG']['invoice_type']['type'] as $key => $type)
        {
            if ($type == $order['inv_type'])
            {
                $rate = floatval($GLOBALS['_CFG']['invoice_type']['rate'][$key]) / 100;
                break;
            }
        }
        if ($rate > 0)
        {
            $total['tax'] = $rate * $total['goods_price'];
        }
    }
    $total['tax_formated'] = price_format($total['tax'], false);

    /* 包装费用 */
    if (!empty($order['pack_id']))
    {
        $total['pack_fee']      = pack_fee($order['pack_id'], $total['goods_price']);
    }
    $total['pack_fee_formated'] = price_format($total['pack_fee'], false);

    /* 贺卡费用 */
    if (!empty($order['card_id']))
    {
        $total['card_fee']      = card_fee($order['card_id'], $total['goods_price']);
    }
    $total['card_fee_formated'] = price_format($total['card_fee'], false);

    /* 红包 */

    if (!empty($order['bonus_id']))
    {
        $bonus          = bonus_info($order['bonus_id']);
        $total['bonus'] = $bonus['type_money'];
    }

    $total['bonus_formated'] = price_format($total['bonus'], false);

    /* 线下红包 */
     if (!empty($order['bonus_kill']))
    {
        $bonus          = bonus_info(0,$order['bonus_kill']);
        $total['bonus_kill'] = $order['bonus_kill'];
        $total['bonus_kill_formated'] = price_format($total['bonus_kill'], false);
    }



    /* 配送费用 */
    $shipping_cod_fee = NULL;


    if (($order['express_id'] > 0 || $order['shipping_id'] > 0) && $total['real_goods_count'] > 0)
    {
        if ($order['express_id'] > 0) {
            $express_id = $order['express_id'];

            $express = $GLOBALS['db']->getRow('SELECT * FROM '.$GLOBALS['hhs']->table('goods_express').' WHERE id=' . $express_id);

            $configure = array(
                array('name'=>'item_fee','value'=>$express['shipping_fee']),
                array('name'=>'base_fee','value'=>$express['shipping_fee']),
                array('name'=>'basic_fee','value'=>$express['shipping_fee']),
                array('name'=>'step_fee','value'=>$express['step_fee']),
                array('name'=>'fee_compute_mode','value'=>$express['fee_compute_mode']),
                array('name'=>'free_money','value'=>''),
                array('name'=>'pay_fee','value'=>''),
            );
            $shipping_info = array(
                'shipping_code' => $express['shipping_code'],
                'shipping_name' => $express['shipping_name'],
                'pay_fee'       => 0,
                'insure'        => 0,
                'support_cod'   => 0,
                'configure'     => $configure,
            );
        }
        else
        {
            $region['country']  = $consignee['country'];
            $region['province'] = $consignee['province'];
            $region['city']     = $consignee['city'];
            $region['district'] = $consignee['district'];
            $shipping_info = shipping_area_info($order['shipping_id'], $region, $suppliers_id);
        }

        if (!empty($shipping_info))
        {
            if ($order['extension_code'] == 'group_buy')
            {
                $weight_price = cart_weight_price(CART_GROUP_BUY_GOODS,$suppliers_id);
            }
            else
            {
                $weight_price = cart_weight_price(CART_GENERAL_GOODS,$suppliers_id);
            }

            // 查看购物车中是否全为免运费商品，若是则把运费赋为零
            $sql = 'SELECT count(*) FROM ' . $GLOBALS['hhs']->table('cart') . " WHERE  `user_id` = '" . $user_id. "' AND `extension_code` != 'package_buy' AND `is_shipping` = 0 AND  `suppliers_id` = '" . $suppliers_id. "'";
			
            $shipping_count = $GLOBALS['db']->getOne($sql);

            $total['shipping_fee'] = ($shipping_count == 0 AND $weight_price['free_shipping'] == 1) ?0 :  shipping_fee($shipping_info['shipping_code'],$shipping_info['configure'], $weight_price['weight'], $total['goods_price'], $weight_price['number']);

            if (!empty($order['need_insure']) && $shipping_info['insure'] > 0)
            {
                $total['shipping_insure'] = shipping_insure_fee($shipping_info['shipping_code'],
                    $total['goods_price'], $shipping_info['insure']);
            }
            else
            {
                $total['shipping_insure'] = 0;
            }

            if ($shipping_info['support_cod'])
            {
                $shipping_cod_fee = $shipping_info['pay_fee'];
            }
        }
    }

    $total['shipping_fee_formated']    = price_format($total['shipping_fee'], false);
    $total['shipping_insure_formated'] = price_format($total['shipping_insure'], false);

    // 购物车中的商品能享受红包支付的总额
    $bonus_amount = compute_discount_amount($suppliers_id);
    // 红包和积分最多能支付的金额为商品总额
    $max_amount = $total['goods_price'] == 0 ? $total['goods_price'] : $total['goods_price'] - $bonus_amount;

    /* 计算订单总额 */
    if ($order['extension_code'] == 'group_buy' && $group_buy['deposit'] > 0)
    {
        $total['amount'] = $total['goods_price'];
    }
    else
    {
        $total['amount'] = $total['goods_price'] - $total['discount'] + $total['tax'] + $total['pack_fee'] + $total['card_fee'] +
            $total['shipping_fee'] + $total['shipping_insure'] + $total['cod_fee'];

        // 减去红包金额
        $use_bonus        = min($total['bonus'], $max_amount); // 实际减去的红包金额
        if(isset($total['bonus_kill']))
        {
            $use_bonus_kill   = min($total['bonus_kill'], $max_amount);
            $total['amount'] -=  $price = number_format($total['bonus_kill'], 2, '.', ''); // 还需要支付的订单金额
        }

        $total['bonus']   = $use_bonus;
        $total['bonus_formated'] = price_format($total['bonus'], false);

        $total['amount'] -= $use_bonus; // 还需要支付的订单金额
        $max_amount      -= $use_bonus; // 积分最多还能支付的金额

    }

    /* 余额 */
    $order['surplus'] = $order['surplus'] > 0 ? $order['surplus'] : 0;
    if ($total['amount'] > 0)
    {
        if (isset($order['surplus']) && $order['surplus'] > $total['amount'])
        {
            $order['surplus'] = $total['amount'];
            $total['amount']  = 0;
        }
        else
        {
            $total['amount'] -= floatval($order['surplus']);
        }
    }
    else
    {
        $order['surplus'] = 0;
        $total['amount']  = 0;
    }
    $total['surplus'] = $order['surplus'];
    $total['surplus_formated'] = price_format($order['surplus'], false);

    /* 积分 */
    $order['integral'] = $order['integral'] > 0 ? $order['integral'] : 0;
    if ($total['amount'] > 0 && $max_amount > 0 && $order['integral'] > 0)
    {
        $integral_money = value_of_integral($order['integral']);

        // 使用积分支付
        $use_integral            = min($total['amount'], $max_amount, $integral_money); // 实际使用积分支付的金额
        $total['amount']        -= $use_integral;
        $total['integral_money'] = $use_integral;
        $order['integral']       = integral_of_value($use_integral);
    }
    else
    {
        $total['integral_money'] = 0;
        $order['integral']       = 0;
    }
    $total['integral'] = $order['integral'];
    $total['integral_formated'] = price_format($total['integral_money'], false);

    $se_flow_type = isset($_SESSION['flow_type']) ? $_SESSION['flow_type'] : '';
    
    /* 支付费用 */
    if (!empty($order['pay_id']) && ($total['real_goods_count'] > 0 || $se_flow_type != CART_EXCHANGE_GOODS))
    {
        $total['pay_fee']      = pay_fee($order['pay_id'], $total['amount'], $shipping_cod_fee);
    }

    $total['pay_fee_formated'] = price_format($total['pay_fee'], false);

    $total['amount']           += $total['pay_fee']; // 订单总额累加上支付费用
    $total['amount_formated']  = price_format($total['amount'], false);

    $total['formated_goods_price']  = price_format($total['goods_price'], false);
    $total['formated_market_price'] = price_format($total['market_price'], false);
    $total['formated_saving']       = price_format($total['saving'], false);

    return array('order'=>$order,'total'=>$total);
}
?>