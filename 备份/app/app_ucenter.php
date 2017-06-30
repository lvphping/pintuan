<?php
define('IN_HHS', true);
$user_id = $_REQUEST['user_id'];
include_once(ROOT_PATH .'includes/lib_fenxiao.php');
require_once(ROOT_PATH . 'languages/' .$_CFG['lang']. '/user.php');
    /* 订单状态 */
    
$_LANG['os'][OS_UNCONFIRMED] = '未确认';
$_LANG['os'][OS_CONFIRMED] = '已确认';
$_LANG['os'][OS_SPLITED] = '已确认';
$_LANG['os'][OS_SPLITING_PART] = '已确认';
$_LANG['os'][OS_CANCELED] = '已取消';
$_LANG['os'][OS_INVALID] = '无效';
$_LANG['os'][OS_RETURNED] = '已退货';
$_LANG['ss'][SS_UNSHIPPED] = '未发货';
$_LANG['ss'][SS_PREPARING] = '配货中';
$_LANG['ss'][SS_SHIPPED] = '配送中';//已发货
$_LANG['ss'][SS_RECEIVED] = '已签收';//收货确认
$_LANG['ss'][SS_SHIPPED_PART] = '已发货(部分商品)';
$_LANG['ss'][SS_SHIPPED_ING] = '配货中'; // 已分单
$_LANG['ps'][PS_UNPAYED] = '待支付';
$_LANG['ps'][PS_PAYING] = '付款中';
$_LANG['ps'][PS_PAYED] = '已付款';
$_LANG['ps'][PS_REFUNDED] = '已退款';
$_LANG['cancel'] = '取消订单';
$_LANG['pay_money'] = '付款';
$_LANG['view_order'] = '查看订单';
$_LANG['received'] = '确认收货';
$_LANG['ss_received'] = '已完成';
$_LANG['confirm_received'] = '你确认已经收到货物了吗？';
$_LANG['confirm_cancel'] = '您确认要取消该订单吗？取消后此订单将视为无效订单';
//if(!$_SESSION['user_id'])
//{
//	$results['error'] =0;
//	$results['content'] ='请先登录';
//	echo $json->encode($results);
//	die();
//		
//}

if($action =='')
{
	$action ='default';	
}
if ($action == 'default')
{
	include_once(ROOT_PATH .'includes/lib_clips.php');
        /**
     * 申请供应商什么的
     */
    $sql = "SELECT `is_check`,`suppliers_id` from ".$hhs->table('suppliers')." WHERE `user_id` = '$user_id'";
    $row = $db->getRow($sql);
	
	
	
	if($row['is_check']!=''&&$row['suppliers_id'])
	{
		$user_info['is_check'] = $row['is_check'];
	}
	else
	{
		$user_info['is_check'] = 10;
	}
	$user_info['suppliers_id'] = $row['suppliers_id'];
    $user_info['info'] = get_user_default($user_id);
   	$user_info['user_notice'] = $_CFG['user_notice'];
    $user_info['prompt'] =get_user_prompt($user_id);
	
	$daifukuan = $db->getOne("SELECT COUNT(*) FROM " .$hhs->table('order_info'). " WHERE user_id = '$user_id' and order_status in (0,1,5) and pay_status=0");
	$daifahuo = $db->getOne("SELECT COUNT(*) FROM " .$hhs->table('order_info'). " WHERE user_id = '$user_id' " .order_query_sql('await_ship'));
	$daishouhuo = $db->getOne("SELECT COUNT(*) FROM " .$hhs->table('order_info'). " WHERE user_id = '$user_id' " .order_query_sql('shipped2'));
	$daipingjia = $db->getOne("SELECT COUNT(*) FROM " .$hhs->table('order_info'). " WHERE user_id = '$user_id' " .order_query_sql());
	
	
	$user_info['daifukuan']=$daifukuan;
	$user_info['daifahuo']=$daifahuo;
	$user_info['daishouhuo'] =$daishouhuo;
	$user_info['daipingjia']=$daipingjia;
	
	
	
	
	$results['content'] =$user_info;
	echo $json->encode($results);
	die();
}
elseif($action =='order_list')
{
    include_once(ROOT_PATH . 'includes/lib_transaction.php');
    include_once(ROOT_PATH . 'includes/lib_payment.php');
    include_once(ROOT_PATH . 'includes/lib_order.php');
    include_once(ROOT_PATH . 'includes/lib_clips.php');
    $page = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
    $composite_status = isset($_REQUEST['composite_status']) ? intval($_REQUEST['composite_status']) : -1;
    $where=" AND is_luck = 0 ";
    //未付款
    if($_REQUEST['composite_status'] =='100')
    {
        $where = " and order_status in (0,1,5)  and pay_status=0 ";
    }
    //待收货
    if($_REQUEST['composite_status'] =='180')
    {
        $where .= order_query_sql('await_ship');
    }
    /* 已发货订单：不论是否付款 */
    if($_REQUEST['composite_status'] =='120')
    {
        $where .= order_query_sql('shipped2');
    }
    /* 已完成订单 */
    if($_REQUEST['composite_status'] =='999')
    {
        $where .= order_query_sql();
    }

    $page = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
    $orders = app_get_user_orders_ex($user_id,2, 0, $where);
	foreach($orders as $idx=>$v)
	{
		foreach($v['goods_list'] as $id=>$vs)
		{
			$v['goods_list'][$id]['goods_thumb'] = $redirect_uri.$vs['goods_thumb'];
			$v['goods_list'][$id]['little_img'] = $redirect_uri.$vs['little_img'];
		}
		$v['supp_logo'] = $redirect_uri.$v['supp_logo'];
		$orders[$idx] = $v;
	}
	
	if(!empty($orders))
	{
		$results['error'] =1;
	}
	else
	{
		$results['error'] =0;
		
	}

	
	
  	//$orders['composite_status'] = $composite_status;
	$results['content'] = $orders;
	//$results['composite_status'] = $composite_status;
	//$results['error'] =1;
	echo $json->encode($results);
	die();
}
elseif($action =='affirm_received')
{
    include_once(ROOT_PATH . 'includes/lib_transaction.php');
    include_once(ROOT_PATH . 'includes/lib_order.php');
    include_once(ROOT_PATH . 'includes/lib_fenxiao.php');
    //payment_info
    $order_id = isset($_REQUEST['order_id']) ? intval($_REQUEST['order_id']) : 0;
 
	
    
    if (affirm_received($order_id, $user_id))
    {

    	//分销更新状态
        $update_at = gmtime();
        updateMoney($order_id,$update_at);
        
        $order_info = order_info($order_id);
    	// 收货之后发优惠券
        if($_CFG['send_bonus_time'] == 1){
        	$bonus_list=send_order_bonus($order_id);
        }
      //  doFenxiao($order_info);
        if($order_info['team_sign'] > 0 && $_CFG['send_bonus_time'] == 0){
        	$bonus_list=send_order_bonus($order_id);
        }
        if(!empty($bonus_list)){
			
			
			$results['error'] =2;
			$results['order_id'] = $order_id;
			echo $json->encode($results);
			die();
			
        }
		
		$results['error'] =0;
		$results['composite_status'] = 999;
		echo $json->encode($results);
		die();

    }
    else
    {
		$results['error'] =1;
		echo $json->encode($results);
		die();

        //$err->show($_LANG['order_list_lnk'], 'user.php?act=order_list');
    }	
	
}
elseif($action =='cancel_order')
{
    include_once(ROOT_PATH . 'includes/lib_transaction.php');
    include_once(ROOT_PATH . 'includes/lib_order.php');
    $order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;
    if (cancel_order($order_id, $user_id))
    {
		$results['error'] =0;
		echo $json->encode($results);
		die();
   }
    else
    {
		$results['error'] =1;
		echo $json->encode($results);
		die();
    }
}
elseif($action =='team_list')
{
    include_once(ROOT_PATH . 'includes/lib_transaction.php');
	$composite_status = isset($_REQUEST['composite_status']) ? intval($_REQUEST['composite_status']) : '';
	$where = ' AND `is_luck` = 0 ';
	switch ($composite_status) {
        case 999:
            $where .= " AND `team_status` > 2 ";
            break;
        case 120:
            $where .= " AND `team_status` = 2 ";
            break;
        case 100:
            $where .= " AND `team_status` = 1 ";
            break;
        
        default:
            $where .= " AND `team_status` > 0 ";
            break;
    }
	
    $page = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
    $orders = get_user_team_orders($user_id, 10000, 0, $where);
	
	
	
	
	
	
	foreach($orders as $idx=>$v)
	{
		foreach($v['goods_list'] as $id=>$vs)
		{
			$v['goods_list'][$id]['goods_thumb'] = $redirect_uri.$vs['goods_thumb'];
			$v['goods_list'][$id]['little_img'] = $redirect_uri.$vs['little_img'];
		}
		$orders[$idx] = $v;
	}
	
	if(!empty($orders))
	{
		$results['error'] =0;	
	}
	else
	{
		$results['error'] =1;	
	}
	
	
  	//$orders['composite_status'] = $composite_status;
	$results['content'] = $orders;
	$results['composite_status'] = $composite_status;
	
	echo $json->encode($results);
	die();
}
elseif($action =='order_detail')
{
    include_once(ROOT_PATH . 'includes/lib_transaction.php');
    include_once(ROOT_PATH . 'includes/lib_payment.php');
    include_once(ROOT_PATH . 'includes/lib_order.php');
    include_once(ROOT_PATH . 'includes/lib_clips.php');
    $order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;
    /* 订单详情 */
    $order = get_order_detail($order_id, $user_id);
    if($order['is_luck']){
        $luck_rows = $db->getAll('select * from '.$hhs->table("order_luck").' WHERE order_id = "'.$order_id.'" ');
        $order['luck_rows'] = $luck_rows;
    }
    $team = isset($_GET['team']) ? intval($_GET['team']) : 0;
//    if($team>0 && !empty($order['team_sign']) && $order['team_status']!=0&&!empty($order['pay_time'])){//
//        //成功的回调
//        //include_once(ROOT_PATH .'successurl.php');
//    
//        hhs_header("location:share.php?team_sign=".$order['team_sign']);
//        exit();
//
//    }
//    if ($order === false)
//    {
//        //$err->show($_LANG['back_home_lnk'], './');
//        hhs_header("location:index.php");
//        exit;
//    }

    /* 是否显示添加到购物车 */
//    if ($order['extension_code'] != 'group_buy' && $order['extension_code'] != 'exchange_goods')
//    {
//        $smarty->assign('allow_to_cart', 1);
//    }

    /* 订单商品 */
    $goods_list = order_goods($order_id);
    foreach ($goods_list AS $key => $value)
    {
        $goods_list[$key]['market_price'] = price_format($value['market_price'], false);
        $goods_list[$key]['goods_price']  = price_format($value['goods_price'], false);
        $goods_list[$key]['subtotal']     = price_format($value['subtotal'], false);
    }

     /* 设置能否修改使用余额数 */
//    if ($order['order_amount'] > 0)
//    {
//        if ($order['order_status'] == OS_UNCONFIRMED || $order['order_status'] == OS_CONFIRMED)
//        {
//            $user = user_info($order['user_id']);
//            if ($user['user_money'] + $user['credit_line'] > 0)
//            {
//                $smarty->assign('allow_edit_surplus', 1);
//                $smarty->assign('max_surplus', sprintf($_LANG['max_surplus'], $user['user_money']));
//            }
//        }
//    }

    /* 未发货，未付款时允许更换支付方式 */
    if ($order['order_amount'] > 0 && $order['pay_status'] == PS_UNPAYED && $order['shipping_status'] == SS_UNSHIPPED)
    {
        $payment_list = available_payment_list(false, 0, true);

        /* 过滤掉当前支付方式和余额支付方式 */
        if(is_array($payment_list))
        {
            foreach ($payment_list as $key => $payment)
            {
                if ($payment['pay_id'] == $order['pay_id'] || $payment['pay_code'] == 'balance')
                {
                    unset($payment_list[$key]);
                }
            }
        }
		$order['payment_list'] = $payment_list;
      //  $smarty->assign('payment_list', $payment_list);
    }

    
    $order['order_status_cy']=$order['order_status'] ;
    $order['pay_status_cy']=$order['pay_status'] ;
    $order['shipping_status_cy']=$order['shipping_status'] ;
    /*可进行的操作*/
    
    if ($order['order_status'] == 0)
    {
		@$order['handler'] = $order['pay_online'];
        $order['handler'] .= "<a class='state_btn_1' href=\"user.php?act=cancel_order&order_id=" .$order['order_id']. "\" onclick=\"if (!confirm('".$GLOBALS['_LANG']['confirm_cancel']."')) return false;\">取消订单</a>";
        
    }
    else if ($order['order_status'] == OS_SPLITED)
    {
        /* 对配送状态的处理 */
        if ($order['shipping_status'] == SS_SHIPPED)
        {
            @$order['handler'] = "<a class='state_btn_1' href=\"user.php?act=affirm_received&order_id=" .$order['order_id']. "\" onclick=\"if (!confirm('".$GLOBALS['_LANG']['confirm_received']."')) return false;\">".$GLOBALS['_LANG']['received']."</a>";
            
            @$order['handler'] .= "<a class='state_btn_2' href=\"javascript:void(0);\" onclick='get_invoice(\"".$order['shipping_name']."\",\"".$order['invoice_no']."\");'>查看物流</a>";
        }/*
        elseif ($row['shipping_status'] == SS_RECEIVED)
        {
            @$row['handler'] = '<span style="color:red">'.$GLOBALS['_LANG']['ss_received'] .'</span>';
        }*/
        else
        {
            if ($order['pay_status'] == PS_UNPAYED)
            {
                @$order['handler'] = $order['pay_online'];
            }     
        }
    }
 
   
    $order['order_status'] = $_LANG['os'][$order['order_status']] . ',' . $_LANG['ps'][$order['pay_status']] . ',' . $_LANG['ss'][$order['shipping_status']];
	$province=$db->getRow("select region_name from hhs_region where region_id='$order[province]'");
    $city=$db->getRow("select region_name from hhs_region where region_id='$order[city]'");
    $district=$db->getRow("select region_name from hhs_region where region_id='$order[district]'");
    $order['province']=$province['region_name'];
    $order['city']=$city['region_name'];
    $order['district']=$district['region_name'];

	if($order['point_id'])
	{
		$order['shipping_point'] = get_shipping_point_name($order['point_id']);
		
	}
    $order['add_time']=local_date("Y-m-d H:i:s",$order['add_time']);
	$order['goods_list'] = $goods_list;
//    $smarty->assign('order',      $order);
//    $smarty->assign('goods_list', $goods_list);
//    $smarty->display('user_order.dwt');
	$results['error'] =0;
	echo $json->encode($order);
	die();	
	
}
elseif($action=='lottery_list')
{
	
	
    include_once(ROOT_PATH . 'includes/lib_transaction.php');
	$composite_status = isset($_REQUEST['composite_status']) ? intval($_REQUEST['composite_status']) : '';
	$where = ' AND `is_luck` = 1 ';
    if($_REQUEST['composite_status'] =='100')
    {
        $where .= " and pay_status =2 and team_status=1 ";
    }
    //待收货
    if($_REQUEST['composite_status'] =='120')
    {
        $where .= " and order_status in (0,1,5) and team_status=2";
    }
    //評論
    if($_REQUEST['composite_status'] =='999')
    {
        $where .= "  and pay_status=2 and shipping_status=2 and is_comm = 0 ";
    }

    $page = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
    $orders = get_user_team_orders($user_id, 10000, 0, $where);
	
	foreach($orders as $idx=>$v)
	{
		foreach($v['goods_list'] as $id=>$vs)
		{
			$v['goods_list'][$id]['goods_thumb'] = $redirect_uri.$vs['goods_thumb'];
			$v['goods_list'][$id]['little_img'] = $redirect_uri.$vs['little_img'];
		}
		$orders[$idx] = $v;
	}
	if(!empty($orders))
	{
		$results['error'] =0;	
	}
	else
	{
		$results['error'] =1;	
	}
	
	
  	//$orders['composite_status'] = $composite_status;
	$results['content'] = $orders;
	$results['composite_status'] = $composite_status;
	echo $json->encode($results);
	die();	
	
}
/* 添加收藏商品(ajax) */
elseif ($action == 'collect')
{
    include_once(ROOT_PATH .'includes/cls_json.php');
    $json = new JSON();
    $result = array('error' => 0, 'message' => '');
    $goods_id = $_REQUEST['goods_id'];
    if (!isset($user_id) || $user_id == 0)
    {
        $result['error'] = 4;
        $result['message'] = '请先登录';
        die($json->encode($result));
    }
    else
    {
        /* 检查是否已经存在于用户的收藏夹 */
        $sql = "SELECT COUNT(*) FROM " .$GLOBALS['hhs']->table('collect_goods') .
            " WHERE user_id='$user_id' AND goods_id = '$goods_id'";
        if ($GLOBALS['db']->GetOne($sql) > 0)
        {
            $result['error'] = 2;
            $result['message'] = '该商品已收藏过了';
            die($json->encode($result));
        }
        else
        {
            $time = gmtime();
            $sql = "INSERT INTO " .$GLOBALS['hhs']->table('collect_goods'). " (user_id, goods_id, add_time)" .
                    "VALUES ('$user_id', '$goods_id', '$time')";
            if ($GLOBALS['db']->query($sql) === false)
            {
                $result['error'] = 3;
                $result['message'] = $GLOBALS['db']->errorMsg();
                die($json->encode($result));
            }
            else
            {
                $result['error'] = 1;
                $result['message'] = '收藏成功';
                die($json->encode($result));
            }
        }
    }
}
elseif ($action == 'collect_store')
{

    include_once(ROOT_PATH .'includes/cls_json.php');
    $json = new JSON();
    $result = array('error' => 0, 'message' => '');
    $goods_id = $_REQUEST['store_id'];
   if (!isset($user_id) || $user_id == 0)
    {
        $result['error'] = 3;
        $result['message'] = '请先登录';
        die($json->encode($result));
    }
    else
    {
        /* 检查是否已经存在于用户的收藏夹 */
        $sql = "SELECT COUNT(*) FROM " .$GLOBALS['hhs']->table('collect_store') .
            " WHERE user_id='$user_id' AND suppliers_id = '$goods_id'";
        if ($GLOBALS['db']->GetOne($sql) > 0)
        {
            $result['error'] = 2;
            $result['message'] ='该店铺已经在收藏夹中';
            die($json->encode($result));
        }
        else
        {
            $time = gmtime();
            $sql = "INSERT INTO " .$GLOBALS['hhs']->table('collect_store'). " (user_id, suppliers_id, add_time)" .
                    "VALUES ('$user_id', '$goods_id', '$time')";
            if ($GLOBALS['db']->query($sql) === false)
            {
                $result['error'] = 3;
                $result['message'] = $GLOBALS['db']->errorMsg();
                die($json->encode($result));
            }
            else
            {
                $result['error'] = 1;
                $result['message'] = '收藏店铺成功';
                die($json->encode($result));
            }
        }
    }
}
elseif($action =='collection_list')
{
    include_once(ROOT_PATH . 'includes/lib_clips.php');
  
    $goods_list = get_collection_goods_app($user_id);
	
	foreach($goods_list as $idx=>$v)
	{
		$goods_list[$idx]['goods_thumb'] = $redirect_uri.$v['goods_thumb'];
	}
	
	
	if(!empty($goods_list))
	{
		$results['error'] =0;	
	}
	else
	{
		$results['error'] =1;	
	}
	
	
	
	$results['goods_list'] = $goods_list;
	$results['url'] = $hhs->url();

	echo $json->encode($results);
	die();
}
/* 删除收藏的商品 */
elseif ($action == 'delete_collection')
{
    include_once(ROOT_PATH . 'includes/lib_clips.php');
    $collection_id = isset($_REQUEST['collection_id']) ? intval($_REQUEST['collection_id']) : 0;
    if ($collection_id > 0)
    {
        $db->query('DELETE FROM ' .$hhs->table('collect_goods'). " WHERE rec_id='$collection_id' AND user_id ='$user_id'" );
    }
	$results['error'] =1;
	echo $json->encode($results);
	die();
}
elseif ($action == 'collect_store_list')
{
    include_once(ROOT_PATH . 'includes/lib_clips.php');
	$sql = "select c.*,s.suppliers_name,s.real_name from ".$GLOBALS['hhs']->table('collect_store')." as c,".$GLOBALS['hhs']->table('suppliers')." as s where s.suppliers_id=c.suppliers_id and c.user_id='$user_id' order by id desc  ";
	$list = $GLOBALS['db'] -> getAll($sql);
	foreach($list as  $idx=>$value)
	{
		$list[$idx]['add_time']= local_date($GLOBALS['_CFG']['time_format'], $value['add_time']);
		$list[$idx]['logo'] = $redirect_uri.get_logo($value['suppliers_id']);
	}
	
	
	if(!empty($list))
	{
		$results['error'] =0;	
	}
	else
	{
		$results['error'] =1;	
	}
	
	
	
	
	$results['store_list'] = $list;
	$results['url'] = $hhs->url();
	echo $json->encode($results);
	die();
}
elseif ($action == 'del_collect_store')
{
    $rec_id = (int)$_GET['id'];
    if ($rec_id)
    {
        $db->query('DELETE FROM' .$hhs->table('collect_store'). "  WHERE suppliers_id='$rec_id' AND user_id ='$user_id'" );
    }
	$results['error'] =1;
	echo $json->encode($results);
	die();
}
elseif($action =='bonus')
{
    include_once(ROOT_PATH .'includes/lib_transaction.php');
    $page = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
    $record_count = $db->getOne("SELECT COUNT(*) FROM " .$hhs->table('user_bonus'). " WHERE user_id = '$user_id'");
	
	// $smarty->assign('send_bouns',$_REQUEST['send_bouns']);
		
    //$pager = get_pager('user.php', array('act' => $action), $record_count, $page);
    $bonus = get_user_bouns_list2($user_id);
    if($_REQUEST['status']=='not_start'){
        $smarty->assign('status', 'not_start');
        $arr=$bonus['not_start'];
        $bonus=array();
        $bonus['not_start']=$arr;
    }elseif($_REQUEST['status']=='overdue'){
        $smarty->assign('status', 'overdue');
        $arr=$bonus['overdue'];
        $bonus=array();
        $bonus['overdue']=$arr;
    }
	$results['content'] = $bonus;
	if(!empty($bonus))
	{
		$results['error'] =0;	
	}
	else
	{
		$results['error'] =1;	
	}

	
	
	
	echo $json->encode($results);
	die();

}
elseif($action =='add_bonus')
{
	if(isset($_REQUEST['send_bouns'])){
		$result = app_add_bonus($user_id, $_REQUEST['send_bouns']);
		if($result==1)
		{
			$results['error'] =1;
			$results['content'] ='请先登录';
		}
		if($result==2)
		{
			$results['error'] =2;
			$results['content'] ='优惠券已过期';
		}
		if($result==3)
		{
			$results['error'] =3;
			$results['content'] ='绑定成功';
		}
		if($result==4)
		{
			$results['error'] =4;
			$results['content'] ='绑定失败';
		}
		if($result==5)
		{
			$results['error'] =5;
			$results['content'] ='优惠券已经绑定过了';
		}
		if($result==6)
		{
			$results['error'] =6;
			$results['content'] ='优惠券已被其他人使用过了';
		}
		if($result==7)
		{
			$results['error'] =7;
			$results['content'] ='优惠券不存在';
		}
	}
	else
	{
		$results['error'] =0;
		$results['content'] ='请输入优惠券序列号';
	}
		echo $json->encode($results);
		die();
	
}
elseif($action =='get_regions')
{
	
	$list = $db->getAll("select * from ".$hhs->table('region')." where region_type=1");

	foreach($list as $idx=>$v)
	{
		
		$parent_id = $v['parent_id'];
		$city_list = $db->getAll("select * from ".$hhs->table('region')." where parent_id='$parent_id'");
		foreach($city_list as $key=>$vs)
		{
			$pid = $vs['parent_id'];
			$district_list = $db->getAll("select * from ".$hhs->table('region')." where parent_id='$pid'");
			$city_list[$key]['district_list'] = $district_list;
		}
		$list[$idx]['city_list'] = $city_list;
	}	
	$results['content'] = $list;
	echo $json->encode($results);
	die();
}
elseif ($action == 'drop_consignee')
{
    include_once('includes/lib_transaction.php');
    $consignee_id = intval($_REQUEST['id']);
    if (app_drop_consignee($consignee_id,$user_id))
    {
		$results['error'] =0;
		echo $json->encode($results);
		die();
    }
    else
    {
		$results['error'] =1;
		echo $json->encode($results);
		die();
    }
}
elseif($action=='address_list')
{
	
   include_once(ROOT_PATH . 'includes/lib_transaction.php');
   include_once(ROOT_PATH . 'includes/lib_orders.php');
    include_once(ROOT_PATH . 'languages/' .$_CFG['lang']. '/shopping_flow.php');
    $smarty->assign('lang',  $_LANG);
  
    /* 获得用户所有的收货人信息 */
    $consignee_list = get_consignee_list($user_id);
	$address_id  = $db->getOne("SELECT address_id FROM " .$hhs->table('users'). " WHERE user_id='$user_id'");

   
    //取得国家列表，如果有收货人列表，取得省市区列表
    foreach ($consignee_list AS $region_id => $consignee)
    {
		if($consignee['address_id'] ==$address_id)
		{
			 $consignee_list[$region_id]['is_default'] =1;
		}
		else
		{
			 $consignee_list[$region_id]['is_default'] =0;
		}
        $consignee_list[$region_id]['province_name'] = get_regions_name($consignee['province']);
		$consignee_list[$region_id]['city_name'] = get_regions_name($consignee['city']);
        $consignee_list[$region_id]['district_name'] = get_regions_name($consignee['district']);
        
    }
    /* 获取默认收货ID */
    
	if(!empty($consignee_list))
	{
		$results['error'] =0;	
	}
	else
	{
		$results['error'] =1;	
	}
	
	
   
	$results['consignee_list'] = $consignee_list;

	echo $json->encode($results);
	die();	
}
elseif ($action == 'edit_consignee')
{
    //编辑收货人地址
    include_once('includes/lib_transaction.php');
	  include_once(ROOT_PATH . 'includes/lib_orders.php');

    $address_id=$_REQUEST['address_id'];
    $sql = "SELECT * FROM " . $GLOBALS['hhs']->table('user_address') .
    " WHERE address_id = '$address_id' ";
    $consignee=$GLOBALS['db']->getRow($sql);
	
    $consignee['province_name'] = get_regions_name($consignee['province']);
	$consignee['city_name'] = get_regions_name($consignee['city']);
    $consignee['district_name'] = get_regions_name($consignee['district']);

	$results['consignee'] = $consignee;
	$results['error'] = 0;
	echo $json->encode($results);
	die();	

}
elseif($action =='act_edit_consignee')
{
    include_once(ROOT_PATH . 'includes/lib_transaction.php');
    include_once(ROOT_PATH . 'languages/' .$_CFG['lang']. '/shopping_flow.php');
    $smarty->assign('lang', $_LANG);
    $address = array(
        'user_id'    => $user_id,
        'address_id' => intval($_REQUEST['address_id']),
		'address_type' => intval($_REQUEST['address_type']),
        'country'    => isset($_REQUEST['country'])   ? $_REQUEST['country']  : 1,
        'province'   => isset($_REQUEST['province'])  ? $_REQUEST['province'] : 0,
        'city'       => isset($_REQUEST['city'])      ? $_REQUEST['city']     : 0,
        'district'   => isset($_REQUEST['district'])  ? $_REQUEST['district'] : 0,
		'address_type'   => isset($_REQUEST['address_type'])  ? intval($_REQUEST['address_type']) : 0,
        'address'    => isset($_REQUEST['address'])   ? compile_str(trim($_REQUEST['address']))    : '',
        'consignee'  => isset($_REQUEST['consignee']) ? compile_str(trim($_REQUEST['consignee']))  : '',
        'email'      => isset($_REQUEST['email'])     ? compile_str(trim($_REQUEST['email']))      : '',
        'tel'        => isset($_REQUEST['tel'])       ? compile_str(make_semiangle(trim($_REQUEST['tel']))) : '',
        'mobile'     => isset($_REQUEST['mobile'])    ? compile_str(make_semiangle(trim($_REQUEST['mobile']))) : '',
        'best_time'  => isset($_REQUEST['best_time']) ? compile_str(trim($_REQUEST['best_time']))  : '',
        'sign_building' => isset($_REQUEST['sign_building']) ? compile_str(trim($_REQUEST['sign_building'])) : '',
        'zipcode'       => isset($_REQUEST['zipcode'])       ? compile_str(make_semiangle(trim($_REQUEST['zipcode']))) : '',
    );	
	

	$address['province'] =get_str_replace_region_name(1,$address['province']);
	$address['city'] =get_str_replace_region_name(2,$address['city']);
	$address['district'] =get_str_replace_region_name(3,$address['district']);
	
	
    if (update_address($address))
    {  
		//$results['address_id'] = update_address($address);
		$results['error'] = 1;
		echo $json->encode($results);
		die();	
    }
	else
	{
		$results['error'] = 0;
		echo $json->encode($results);
		die();	
	}
	
}
elseif($action =='account_log')
{
	
    include_once(ROOT_PATH . 'includes/lib_clips.php');
    $page = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
    /* 获取记录条数 */
    $sql = "SELECT COUNT(*) FROM " .$hhs->table('user_account').
           " WHERE user_id = '$user_id'" .
           " AND process_type " . db_create_in(array(SURPLUS_SAVE, SURPLUS_RETURN));
    $record_count = $db->getOne($sql);
    //分页函数
    $pager = get_pager('user.php', array('act' => $action), $record_count, $page);
    //获取剩余余额
    $surplus_amount = get_user_surplus($user_id);
    if (empty($surplus_amount))
    {
        $surplus_amount = 0;
    }
    //获取余额记录
    $account_log = app_get_account_log($user_id);
	

	
	
	
	//获取剩余积分
	$points = get_user_points($user_id);
	$smarty->assign('points', $points);
	$results['points'] = $points;
	$results['account_log'] = $account_log;
	$results['surplus_amount'] = price_format($surplus_amount, false);
	echo $json->encode($results);
	die();	
}
elseif($action =='cancel')
{
	
    include_once(ROOT_PATH . 'includes/lib_clips.php');
    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    if ($id == 0 || $user_id == 0)
    {
		$results['error'] =1;
		echo $json->encode($results);
		die();	
    }
    $result = del_user_account($id, $user_id);
    if ($result)
    {
		$results['error'] =0;
		echo $json->encode($results);
		die();	
    }
	
}
elseif($action =='act_account')
{
    include_once(ROOT_PATH . 'includes/lib_clips.php');
    include_once(ROOT_PATH . 'includes/lib_order.php');
    $amount = isset($_REQUEST['amount']) ? floatval($_REQUEST['amount']) : 0;
    if ($amount <= 0)
    {
        $results['error'] = 0;
		$results['content'] = '输入金额大于0';
		echo $json->encode($results);
		die();	
    }
    /* 变量初始化 */
    $surplus = array(
            'user_id'      => $user_id,
            'rec_id'       => !empty($_REQUEST['rec_id'])      ? intval($_REQUEST['rec_id'])       : 0,
            'process_type' => 1,
            'payment_id'   => isset($_REQUEST['payment_id'])   ? intval($_REQUEST['payment_id'])   : 0,
            'user_note'    => isset($_REQUEST['user_note'])    ? trim($_REQUEST['user_note'])      : '',
            'amount'       => $amount
    );
	

	
   
	if ($amount < 1.00)
	{
        $results['error'] = 0;
		$results['content'] = '输入金额要大于1元';
		echo $json->encode($results);
		die();	
	}
	if ($amount > 200.00)
	{
		$results['error'] = 0;
		$results['content'] = '提现金额不得高于￥ 200.00';
		echo $json->encode($results);
		die();	

	}    
	/* 判断是否有足够的余额的进行退款的操作 */
	$sur_amount = get_user_surplus($user_id);
	if ($amount > $sur_amount)
	{
		$results['error'] = 0;
		$results['content'] = '输入金额大于当前钱包金额';
		echo $json->encode($results);
		die();	
	}
	//插入会员账目明细
	$amount = '-'.$amount;
	$surplus['payment'] = '';
	$surplus['rec_id']  = insert_user_account($surplus, $amount);

	$results['error'] = 1;
	$results['content'] = '提现成功';
	echo $json->encode($results);
	die();	
}
elseif($action =='account_detail')
{
	
    include_once(ROOT_PATH . 'includes/lib_clips.php');
    $page = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
    $account_type = 'user_money';
    /* 获取记录条数 */
    $sql = "SELECT COUNT(*) FROM " .$hhs->table('account_log').
           " WHERE user_id = '$user_id'" .
           " AND $account_type <> 0 ";
    $record_count = $db->getOne($sql);
    //分页函数
    $pager = get_pager('user.php', array('act' => $action), $record_count, $page);
    //获取剩余余额
    $surplus_amount = get_user_surplus($user_id);
    if (empty($surplus_amount))
    {
        $surplus_amount = 0;
    }
	//获取剩余积分
	$points = get_user_points($user_id);

	
	
    //获取余额记录
    $account_log = array();
    $sql = "SELECT * FROM " . $hhs->table('account_log') .
           " WHERE user_id = '$user_id'" .
           " AND $account_type <> 0 " .
           " ORDER BY log_id DESC";
    $res = $GLOBALS['db']->selectLimit($sql, $pager['size'], $pager['start']);
    while ($row = $db->fetchRow($res))
    {
        $row['change_time'] = local_date($_CFG['date_format'], $row['change_time']);
        $row['type'] = $row[$account_type] > 0 ? '增加' : '减少';
        $row['user_money'] = price_format(abs($row['user_money']), false);
        $row['frozen_money'] = price_format(abs($row['frozen_money']), false);
        $row['rank_points'] = abs($row['rank_points']);
        $row['pay_points'] = abs($row['pay_points']);
        $row['short_change_desc'] = sub_str($row['change_desc'], 60);
        $row['amount'] = $row[$account_type];
        $account_log[] = $row;
    }
	$results['points'] = $points;
	$results['surplus_amount'] = price_format($surplus_amount, false);
	$results['account_log'] = $account_log;
	echo $json->encode($results);
	die();		
}
elseif($action =='account_raply')
{
    include_once(ROOT_PATH . 'includes/lib_clips.php');
    //获取剩余余额
    $surplus_amount = get_user_surplus($user_id);
    if (empty($surplus_amount))
    {
        $surplus_amount = 0;
    }
	//获取剩余积分
	$points = get_user_points($user_id);
	$results['points'] = $points;
	$results['surplus_amount'] = price_format($surplus_amount, false);
	echo $json->encode($results);
	die();		
	
}
//elseif($action =='act_account')
//{
//    include_once(ROOT_PATH . 'includes/lib_clips.php');
//    include_once(ROOT_PATH . 'includes/lib_order.php');
//    $amount = isset($_POST['amount']) ? floatval($_POST['amount']) : 0;
//    if ($amount <= 0)
//    {
//		$results['error'] =1;
//		$results['message'] ='提现金额要大于0';
//		echo $json->encode($results);
//		die();		
//    }
//    /* 变量初始化 */
//    $surplus = array(
//            'user_id'      => $user_id,
//            'rec_id'       => !empty($_REQUEST['rec_id'])      ? intval($_REQUEST['rec_id'])       : 0,
//            'process_type' => 1,
//            'payment_id'   => isset($_REQUEST['payment_id'])   ? intval($_REQUEST['payment_id'])   : 0,
//            'user_note'    => isset($_REQUEST['user_note'])    ? trim($_REQUEST['user_note'])      : '',
//            'amount'       => $amount
//    );
//	echo "<pre>";
//	print_r($surplus);
//	exit;
//	
//	
//	
//	if ($amount < 1.00)
//	{
//		$results['error'] =1;
//		$results['message'] ='提现金额不得低于￥ 1.00';
//		echo $json->encode($results);
//		die();		
//	}
//	if ($amount > 200.00)
//	{
//		$results['error'] =1;
//		$results['message'] ='提现金额不得高于￥ 200.00';
//		echo $json->encode($results);
//		die();		
//	}    
//	/* 判断是否有足够的余额的进行退款的操作 */
//	$sur_amount = get_user_surplus($user_id);
//	if ($amount > $sur_amount)
//	{
//		$results['error'] =1;
//		$results['message'] ='余额不足';
//		echo $json->encode($results);
//		die();		
//	}
//	//插入会员账目明细
//	$amount = '-'.$amount;
//	$surplus['payment'] = '';
//	$surplus['rec_id']  = insert_user_account($surplus, $amount);
//	if ($surplus['rec_id'] > 0)
//    {
//		$results['error'] =0;
//		$results['message'] ='提交成功';
//		echo $json->encode($results);
//		die();		
//	}
//	else
//	{
//		$results['error'] =1;
//		$results['message'] ='提交失败';
//		echo $json->encode($results);
//		die();		
//	}
//}
elseif($action =='account_deposit')
{
    include_once(ROOT_PATH . 'includes/lib_clips.php');
	//获取剩余余额
    $surplus_amount = get_user_surplus($user_id);
    if (empty($surplus_amount))
    {
        $surplus_amount = 0;
    }
	//获取剩余积分
	$points = get_user_points($user_id);
	$results['points'] = $points;
	$results['surplus_amount'] = price_format($surplus_amount, false);
	$results['payment'] = get_online_payment_list(false);
	$results['error'] =0;
	echo $json->encode($results);
	die();		
	 //$_SESSION['user_id'] 
}
elseif($action =='account_deposit_act')
{
    include_once(ROOT_PATH . 'includes/lib_clips.php');
    include_once(ROOT_PATH . 'includes/lib_order.php');
    $amount = isset($_REQUEST['amount']) ? floatval($_REQUEST['amount']) : 0;
    if ($amount <= 0)
    {
		$results['error'] =1;
		$results['message'] ='充值金额要大于0';
		echo $json->encode($results);
		die();		
    }
    /* 变量初始化 */
    $surplus = array(
            'user_id'      => $user_id,
            'rec_id'       => !empty($_REQUEST['rec_id'])      ? intval($_REQUEST['rec_id'])       : 0,
            'process_type' => isset($_REQUEST['surplus_type']) ? intval($_REQUEST['surplus_type']) : 0,
            'payment_id'   => isset($_REQUEST['payment_id'])   ? intval($_REQUEST['payment_id'])   : 0,
            'user_note'    => isset($_REQUEST['user_note'])    ? trim($_REQUEST['user_note'])      : '',
            'amount'       => $amount
    );
	
	
        include_once(ROOT_PATH .'includes/lib_payment.php');
      
        if ($surplus['rec_id'] > 0)
        {
            //更新会员账目明细
            $surplus['rec_id'] = update_user_account($surplus);
        }
        else
        {
            //插入会员账目明细
            $surplus['rec_id'] = insert_user_account($surplus, $amount);
        }
     
        $order = array();
        $order['user_name']      = $_SESSION['user_name'];
        $order['order_amount']   = $amount;
        //记录支付log
        $order['order_sn'] = insert_pay_log($surplus['rec_id'], $order['order_amount'], $type=PAY_SURPLUS, 0);
        /* 调用相应的支付方式文件 */
		$results['error'] =0;
		$results['order'] =$order;
		$results['message'] ='提交成功';
		echo $json->encode($results);
		die();		
}
elseif($action =='account_deposit_resultalipay')
{
	include_once(ROOT_PATH .'includes/lib_payment.php');
	$resultstatus = $_REQUEST['resultstatus'];
	$order_sn = $_REQUEST['order_sn'];
	if($resultstatus ==9000)
	{
		order_paid($order_sn);
		$results['error'] =0;
		$results['message'] ='充值成功';
	}
	else
	{
		$results['error'] =1;
		$results['message'] ='充值失败';
		
	}
	echo $json->encode($results);
	die();		
}
elseif($action =='fenxiao')
{
	$level = isset($_REQUEST['level']) ? intval($_REQUEST['level']) : 0;
    $smarty->assign('level', $level);
	$page = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
	$page = $page > 0 ? $page : 1;
	$fenxiao = getMoneyList($user_id,$page,$level);
    $smarty->assign('fenxiao', $fenxiao);
    $record_count = getMoneyListCount($user_id,$level);
	$pager  = get_pager('user.php', array('act' => $action,'level' => $level), $record_count, $page);
	$amount = getMoneyAmount($user_id);
    $smarty->assign('amount', $amount);
	$info = getPidInfo($user_id);
    $smarty->assign('root', $_SERVER['HTTP_HOST']);
	$level1_nums = getFollowsNum($user_id,1);
	$level2_nums = getFollowsNum($user_id,2);
	$level3_nums = getFollowsNum($user_id,3);
	$all_nums = $level1_nums + $level2_nums + $level3_nums;
	$checkedAmount    = price_format(getMoneyAmount($user_id,1));
	$notCheckedAmount = price_format(getMoneyAmount($user_id,0));
	$results['info'] = getPidInfo($user_id);
	$results['all_nums'] = $all_nums;
	$results['level1_nums'] = $level1_nums;
	$results['level2_nums'] = $level2_nums;
	$results['level3_nums'] = $level3_nums;
	$results['record_count'] = $record_count;
	$results['checkedAmount'] = $checkedAmount;
	echo $json->encode($results);
	die();		
}
elseif($action =='level')
{
	$level = isset($_REQUEST['level']) ? intval($_REQUEST['level']) : 0;
    $smarty->assign('level', $level);
	$page = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
	$page = $page > 0 ? $page : 1;
	$follows = getFollows($user_id,$level,$page);
	$results['follows'] = $follows;
	
	if(!empty($follows))
	{
		$results['error'] =0;	
	}
	else
	{
		$results['error'] =1;	
	}
	
	
	echo $json->encode($results);
	die();		
	
}
elseif($action =='money' || $action == 'moneycheck')
{
	$checked = isset($_REQUEST['checked']) ? intval($_REQUEST['checked']) : '';
   
	$uid = isset($_REQUEST['uid']) ? intval($_REQUEST['uid']) : 0;
   
	$page = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
	$page = $page > 0 ? $page : 1;
	$moneyList = app_getMoneyList($user_id,$page,$level,$checked,$uid);
    $record_count = getMoneyListCount($user_id,$level,$checked,$uid);
	$results['moneyList'] = $moneyList;
	
	
	
	if(!empty($moneyList))
	{
		$results['error'] =0;	
	}
	else
	{
		$results['error'] =1;	
	}
	
	echo $json->encode($results);
}

elseif($action =='suggestion')
{
	
	$list = get_suggestion();
	if(!empty($list))
	{
		$results['error'] =0;	
	}
	else
	{
		$results['error'] =1;	
	}
	
	
	$results['list'] =$list;
	echo $json->encode($results);
}
elseif($action =='suggestion_content')
{
	$article_id = $_REQUEST['id'];
	$article = get_article_info($article_id);
	
	if(!empty($article))
	{
		$results['error'] =0;	
	}
	else
	{
		$results['error'] =1;	
	}
	$article['content'] = img_url_replace($article['content']);

	
	$results['article'] =$article;
	
	
	echo $json->encode($results);
}
elseif($action =='create_comment')
{
	
    $content      = trim($_REQUEST['content']);
    $comment_rank = intval($_REQUEST['stars']);
    $id_value     = $_REQUEST['id_value'];
    $add_time     = gmtime();
    $ip_address   = real_ip();
    $order_id     = intval($_REQUEST['order_id']);
    $user_name    = $db->getOne("select user_name from ".$hhs->table('users')." where user_id='$user_id'");

    $sql = "select comment_id from ".$hhs->table('comment')." where user_id = '".$user_id."' and id_value ='$id_value'";
	
    $comment_id = $db->getOne($sql);
    if ($comment_id) {
        $res = array(
            'error' => 1,
            'message' => '已评论过了！'
        );
        echo json_encode($res);
        exit();
    }
//    
    $sql = "insert into ".$hhs->table('comment')." (`user_name`,`user_id`,`content`,`comment_rank`,`id_value`,`add_time`,`ip_address`,`order_id`) values ('$user_name','$user_id','$content','$comment_rank','$id_value','$add_time','$ip_address','$order_id')";
	//echo $sql."sdfsfd";exit;
    $db->query($sql);
    $id = $db->insert_id();
    if ($id) {
    	//$db->query('update '.$hhs->table('order_info').' set `is_comm` = 1 where `order_id` = "'.$order_id.'"');
    	$res = array(
    		'isError' => 0,
			'message' => '评论成功！'
    	);
    }
    else{
    	$res = array(
    		'isError' => 2,
    		'message' => '评论失败，请重试！'
    	);
    }
    echo json_encode($res);
    exit();	
}
elseif($action =='update_username')
{
	$user_id = $_REQUEST['user_id'];
	$uname = $_REQUEST['uname'];
	$sql = $db->query("update ".$hhs->table('users')."  set uname='$uname' where user_id='$user_id'");
	if($sql)
	{
		$results['error'] =0;
		$results['message'] ='编辑成功';
		echo $json->encode($results);
		die();		
	}
	else
	{
		$results['error'] =1;
		$results['message'] ='编辑失败';
		echo $json->encode($results);
		die();		
	}
	
}
elseif($action =='create_square')
{
    $square   = trim($_REQUEST['square']);
    if(empty($square)){
        $res = array(
            'error' => 1,
            'message' => '评论内容不能为空！'
        );
        echo json_encode($res);
        exit();
    }
    $order_id = intval($_REQUEST['order_id']);
    $user_id  = $_REQUEST['user_id'];

    $sql = "update ".$hhs->table('order_info')." set `square` = '".$square."' where user_id = '".$user_id."' and `order_id` = '".$order_id."'";
    $db->query($sql);
    
    $res = array(
        'error' => 0,
		'message' => '发布成功！'
    );
    echo json_encode($res);
    exit();
	
}
elseif($action =='get_regions_list')
{
	$type=$_REQUEST['type'];
	$id = $_REQUEST['id'];
	$list = get_regions($type,$id);
	if($list)
	{
		$results['error'] =0;
	}
	else
	{
		$results['error'] =1;
	}
	$results['list'] = $list;
    echo json_encode($results);
    exit();
}
/* 设置默认地址 */
elseif ($action == 'set_address')
{
        $address_id = empty($_REQUEST['id'])?0:intval($_REQUEST['id']);
        if($db->query("UPDATE " . $hhs->table('users') . " SET address_id = $address_id  WHERE user_id='$user_id'")){ 
			$results['error'] =0;
        }
		else
		{
			$results['error'] =1;
		}
    echo json_encode($results);
    exit();
}
elseif($action =='share_code')
{
	
	$file_name ='data/share/u_'.$user_id.'.jpg';
	$exist = 0;
	if (is_file(ROOT_PATH . $file_name) && (filemtime(ROOT_PATH . $file_name)+30*86400)>time()) {
		$exist = 1;
	}
	$results['file_name'] =  $redirect_uri.$file_name;
	$results['exist'] = $exist;
    echo json_encode($results);
    exit();
}
elseif($action =='share_code_show')
{
	$file_name = 'data/share/u_'.$user_id.'.jpg';
	$force = intval($_REQUEST['force']);
	if ($force == 0 && is_file(ROOT_PATH . $file_name) && (filemtime(ROOT_PATH . $file_name)+30*86400)>time()) {
		$result = array('error' => 0, 'message' => '', 'content' =>  $redirect_uri.$file_name);
		ob_end_clean();
		die($json->encode($result));
	}
	$weixin_config_rows = $db->getRow("select * from ".$hhs->table('weixin_config')."");
	$appid = $weixin_config_rows['appid'];
	$appsecret =$weixin_config_rows['appsecret'];
	$weixin=new class_weixin($appid,$appsecret);
	


	//时效二维码
	$qrcode = $weixin->getWxCode($user_id);
	
	if (! $qrcode) {
		$result = array('error' => 1, 'message' => '系统出错了，请稍后再试', 'content' => '');
		ob_end_clean();
		die($json->encode($result));
	}
	$avatar = getUserAvatar($user_id);
	$text   = !empty($_REQUEST['text']) ? trim($_REQUEST['text']) : '大王叫我来巡山';
	$uname  = $db->getOne('select uname from '.$hhs->table('users').' where user_id = ' . $user_id);
	getFinal($user_id,$uname,$text,$avatar,$qrcode);

	$result = array('error' => 0, 'message' => '', 'content' =>  $redirect_uri.$file_name);
	ob_end_clean();
	die($json->encode($result));
	
	
}
elseif($action =='registration')
{
	
	if($user_id < 1)
	{
		$res['error'] = 4;
		$res['content'] = '请先登录';
		die($json->encode($res));
	}
	else
	{
		$user_info = $db->getRow("select * from ". $hhs->table('users') . "where user_id = ".$user_id);
		
		//系统设置签到积分
		$system_integral = $GLOBALS['_CFG']['qiandao_integral'];
		
		$today     = local_date("Ymd",gmtime());
		$last_time = local_date("Ymd",$user_info['registration_time']);
		
		if($GLOBALS['_CFG']['qiandao'] ==0)
		{
			$res['error'] = 3;
			$res['content'] = '签到活动已关闭，敬请期待';
			die($json->encode($res));
		}
		
	 
		//判断最后一次签到的时间是否和当前时间相同
		if($today == $last_time)
		{
			$res['error'] = 2;
			$res['content'] = '已签到，请明天再来吧!';
			die($json->encode($res));
		}
		else
		{
			$change_desc = "会员签到 增加".$system_integral." 积分";
			
			log_account_change($user_id, 0, 0, 0, $system_integral, $change_desc);
			//记录签到时间
			$db->query("update " . $hhs->table('users') . " set registration_time = '".gmtime()."' where user_id = $user_id ");

			$res['error'] = 1;
			$res['qiandao_integral'] = $system_integral;
			$pay_points = $db->getOne("select pay_points from ". $hhs->table('users') . "where user_id = ".$user_id);
			$res['pay_points'] = $pay_points.$GLOBALS['_CFG']['integral_name'];
			$res['content'] = '签到成功，获得"'.$system_integral.'"积分';
			
			die($json->encode($res));
			
		}
	
	}
	
	
	
	
}
function get_suggestion()
{
   
    $sql = 'SELECT article_id, title' .
               ' FROM ' .$GLOBALS['hhs']->table('article') .
               ' WHERE is_open = 1 AND cat_id = 23 ' .
               ' ORDER BY article_id DESC';
	$res = $GLOBALS['db']->getAll($sql);
    return $res;
}

/**
 *  给指定用户添加一个指定优惠劵
 *
 * @access  public
 * @param   int         $user_id        用户ID
 * @param   string      $bouns_sn       优惠劵序列号
 *
 * @return  boolen      $result
 */
function app_add_bonus($user_id, $bouns_sn)
{
    if (empty($user_id))
    {

        return 1;//请登录
    }

    /* 查询优惠劵序列号是否已经存在 */
    $sql = "SELECT bonus_id, bonus_sn, user_id, bonus_type_id FROM " .$GLOBALS['hhs']->table('user_bonus') .
           " WHERE bonus_sn = '$bouns_sn'";
    $row = $GLOBALS['db']->getRow($sql);
    if ($row)
    {
        if ($row['user_id'] == 0)
        {
            //优惠劵没有被使用
            $sql = "SELECT send_end_date, use_end_date ".
                   " FROM " . $GLOBALS['hhs']->table('bonus_type') .
                   " WHERE type_id = '" . $row['bonus_type_id'] . "'";

            $bonus_time = $GLOBALS['db']->getRow($sql);

            $now = gmtime();
            if ($now > $bonus_time['use_end_date'])
            {
              
                return 2;
            }

            $sql = "UPDATE " .$GLOBALS['hhs']->table('user_bonus') . " SET user_id = '$user_id' ".
                   "WHERE bonus_id = '$row[bonus_id]'";
            $result = $GLOBALS['db'] ->query($sql);
            if ($result)
            {
                 return 3;
            }
            else
            {
                return 4;
            }
        }
        else
        {
            if ($row['user_id']== $user_id)
            {
                //优惠劵已经添加过了。
                $error = 5;
            }
            else
            {
                //优惠劵被其他人使用过了。
               $error = 6;
            }

            return $error;
        }
    }
    else
    {
        //优惠劵不存在
        
        return 7;
    }

}


function get_collection_goods_app($user_id)

{

    $sql = 'SELECT g.goods_id, g.goods_name, g.goods_thumb, g.market_price, g.shop_price AS org_price, '.


                'g.promote_price,g.shop_price, g.promote_start_date,g.promote_end_date, c.rec_id, c.is_attention' .

            ' FROM ' . $GLOBALS['hhs']->table('collect_goods') . ' AS c' .

            " LEFT JOIN " . $GLOBALS['hhs']->table('goods') . " AS g ".

                "ON g.goods_id = c.goods_id ".

            " LEFT JOIN " . $GLOBALS['hhs']->table('member_price') . " AS mp ".

                "ON mp.goods_id = g.goods_id AND mp.user_rank = '$_SESSION[user_rank]' ".

            " WHERE c.user_id = '$user_id' ORDER BY c.rec_id DESC";

    $res = $GLOBALS['db'] -> query($sql);



    $goods_list = array();
	$i=0;

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



        $goods_list[$i]['rec_id']        = $row['rec_id'];

        $goods_list[$i]['is_attention']  = $row['is_attention'];

        $goods_list[$i]['goods_id']      = $row['goods_id'];

        $goods_list[$i]['goods_name']    = $row['goods_name'];
		$goods_list[$i]['goods_thumb']    = $row['goods_thumb'];
		

        $goods_list[$i]['market_price']  = price_format($row['market_price']);

        $goods_list[$i]['shop_price']    = price_format($row['shop_price']);

        $goods_list[$i]['promote_price'] = ($promote_price > 0) ? price_format($promote_price) : '';

        $goods_list[$i]['url']           = build_uri('goods', array('gid'=>$row['goods_id']), $row['goods_name']);
		$i++;

    }



    return $goods_list;

}
function app_get_account_log($user_id)
{

    $account_log = array();
    $sql = 'SELECT * FROM ' .$GLOBALS['hhs']->table('user_account').
           " WHERE user_id = '$user_id'" .
           " AND process_type " . db_create_in(array(SURPLUS_SAVE, SURPLUS_RETURN)) .
           " ORDER BY add_time DESC";
    $res = $GLOBALS['db']->query($sql);
    if ($res)
    {
        while ($rows = $GLOBALS['db']->fetchRow($res))
        {
            $rows['add_time']         = local_date($GLOBALS['_CFG']['date_format'], $rows['add_time']);
            $rows['admin_note']       = nl2br(htmlspecialchars($rows['admin_note']));
            $rows['short_admin_note'] = ($rows['admin_note'] > '') ? sub_str($rows['admin_note'], 30) : 'N/A';
            $rows['user_note']        = nl2br(htmlspecialchars($rows['user_note']));
            $rows['short_user_note']  = ($rows['user_note'] > '') ? sub_str($rows['user_note'], 30) : 'N/A';
            $rows['pay_status']       = ($rows['is_paid'] == 0) ? '未确认': '已确认';
            $rows['amount']           = price_format(abs($rows['amount']), false);
            /* 会员的操作类型： 冲值，提现 */
            if ($rows['process_type'] == 0)
            {
                $rows['type'] = '充值';
            }
            else
            {
                $rows['type'] = '提现';
            }
            /* 支付方式的ID */

            $sql = 'SELECT pay_id FROM ' .$GLOBALS['hhs']->table('payment').
                   " WHERE pay_name = '$rows[payment]' AND enabled = 1";
            $pid = $GLOBALS['db']->getOne($sql);
            /* 如果是预付款而且还没有付款, 允许付款 */
            if (($rows['is_paid'] == 0) && ($rows['process_type'] == 0))
            {
                $rows['handle'] = '<a href="user.php?act=pay&id='.$rows['id'].'&pid='.$pid.'">'.$GLOBALS['_LANG']['pay'].'</a>';
            }
            $account_log[] = $rows;
        }
        return $account_log;
    }
    else
    {
         return false;
    }

}
/**
 * 删除一个收货地址
 *
 * @access  public
 * @param   integer $id
 * @return  boolean
 */
function app_drop_consignee($id,$user_id)
{
    $sql = "SELECT user_id FROM " .$GLOBALS['hhs']->table('user_address') . " WHERE address_id = '$id'";
    $uid = $GLOBALS['db']->getOne($sql);

    if ($uid != $user_id)
    {
        return false;
    }
    else
    {
        $sql = "DELETE FROM " .$GLOBALS['hhs']->table('user_address') . " WHERE address_id = '$id'";
        $res = $GLOBALS['db']->query($sql);
        //设置默认的地址
       
		$sql="select address_id from ".$GLOBALS['hhs']->table('users')." where user_id=".$user_id;
		$address_id=$GLOBALS['db']->getOne($sql);
		if($address_id==$id){
			$sql = "SELECT address_id FROM " .$GLOBALS['hhs']->table('user_address')." where user_id=".$user_id ;
    		$r = $GLOBALS['db']->getAll($sql);
    		if(!empty($r)){
    			$default_address=$r[0]['address_id'];
    			$sql = "update " .$GLOBALS['hhs']->table('users')." set address_id=".$default_address." where user_id=".$user_id;
    			$GLOBALS['db']->query($sql);
    		}
		} /**/
        return $res;
    }
}
/**
 * 获得指定的文章的详细信息
 *
 * @access  private
 * @param   integer     $article_id
 * @return  array
 */
function get_article_info($article_id)
{
    /* 获得文章的信息 */
    $sql = "SELECT a.*, IFNULL(AVG(r.comment_rank), 0) AS comment_rank ".
            "FROM " .$GLOBALS['hhs']->table('article'). " AS a ".
            "LEFT JOIN " .$GLOBALS['hhs']->table('comment'). " AS r ON r.id_value = a.article_id AND comment_type = 1 ".
            "WHERE a.is_open = 1 AND a.article_id = '$article_id' GROUP BY a.article_id";
    $row = $GLOBALS['db']->getRow($sql);

    if ($row !== false)
    {
        $row['comment_rank'] = ceil($row['comment_rank']);                              // 用户评论级别取整
        $row['add_time']     = local_date($GLOBALS['_CFG']['date_format'], $row['add_time']); // 修正添加时间显示

        /* 作者信息如果为空，则用网站名称替换 */
        if (empty($row['author']) || $row['author'] == '_SHOPHELP')
        {
            $row['author'] = $GLOBALS['_CFG']['shop_name'];
        }
    }

    return $row;
}
function app_getMoneyList($user_id,$page = 1,$level,$checked,$uid = 0)
{
	$pageSize = 10;
	$skip = ($page - 1) * $pageSize;
	$andwhere = $level ? " AND `level` = '$level'" :'';
	switch ($checked) {
		case '1'://已经结算
			$andwhere .= " AND `update_at` > 0";
			# code...
			break;
		case '2'://无效或未结算
			$andwhere .= " AND `update_at` = 0";
			# code...
			break;		
		default://全部
			# code...
			break;
	}
	if ($uid > 0) {
		$andwhere .= " AND o.`user_id` = '".$uid."' ";
	}
	$sql = "SELECT u.`user_id`,u.`uname` as 'user_name',u.`headimgurl`,
			f.`order_id`,f.`level`,f.`amount`,f.`money`,f.`create_at`,f.`update_at`,
			o.`order_sn`  
			FROM ".$GLOBALS['hhs']->table('fenxiao'). " as f,
			".$GLOBALS['hhs']->table('order_info'). " as o,
			".$GLOBALS['hhs']->table('users'). " as u".
			" WHERE f.`user_id` = '" . $user_id . "'" . 
			" AND f.`order_id` = o.`order_id` " . 
			" AND o.`user_id` = u.`user_id` " . $andwhere;
	$rows = $GLOBALS['db']->getAll($sql);
	//处理一下时间
	foreach ($rows as $key => $row) {
		$rows[$key]['create_at'] = local_date($GLOBALS['_CFG']['date_format'], $row['create_at']);
		//这个可能为0，判断一下
		if($row['update_at'])
			$rows[$key]['update_at'] = local_date($GLOBALS['_CFG']['date_format'], $row['update_at']);
		unset($row);
	}
	return $rows;
}
function getUserAvatar($user_id)
{
	global $db,$hhs,$weixin;
	
	
	
	$avatar = $db->getOne('select headimgurl from '.$hhs->table('users').' where user_id = ' . $user_id);
	$img    = $weixin->httpGet($avatar);
	$path   = ROOT_PATH . 'data/avatar/u_'.$user_id.'.jpg';
	file_put_contents($path,$img);
	return $path;
}
function getFinal($user_id,$uname,$text, $avatar,$file_name)
{
	global $_CFG;
	$font_file = ROOT_PATH . 'font/simhei.ttf';//字体
	$fx_img    = ROOT_PATH . str_replace('../', '', $GLOBALS['_CFG']['share_bg']);//背景图

	 //背景图
	 $is_very = file_get_contents($fx_img);
	 if(strlen($is_very) < 1)
	 {
		return false;	 
	 }
	 $QR = imagecreatefromstring($is_very); 
	
	 //二维码
	 $wx_code = imagecreatefromstring(file_get_contents($file_name));
	 //分销说明(字体)
	 
	 $pic_valid_time  = '本二维码有效期至'.local_date("Y-m-d H:i",local_strtotime("+1 month")) ;
	 
	 //字体颜色
	 $color = imagecolorallocate($QR, 51, 51, 51);
	 $white = imagecolorallocate($QR, 255, 255, 255);
	 //微信头像
	 $wx_logo = imagecreatefromstring(file_get_contents($avatar)); 
	
	 $QR_width    = imagesx($QR);//背景图宽度 
	 $QR_height   = imagesy($QR);//背景图高度 
	 
	 $wx_code_width  = imagesx($wx_code);//二维码图片宽度 
	 $wx_code_height = imagesy($wx_code);//二维码图片高度 
	 
	 $wx_logo_width  = imagesx($wx_logo);
	 $wx_logo_height = imagesy($wx_logo);
	
	 //载入头像 
	 imagecopyresampled($QR, $wx_logo, 214, 52, 0, 0, 86, 86, $wx_logo_width, $wx_logo_height);
	 //载入二维码
	 imagecopyresampled($QR, $wx_code, $_CFG['x_pos'], $_CFG['y_pos'], 0, 0, 180, 180, $wx_code_width, $wx_code_height);
	 //载入字体
	 imagefttext($QR , 18, 0, 267, 183, $color, $font_file, mb_convert_encoding($uname, 'html-entities', 'UTF-8'));
	 imagefttext($QR , 18, 0, 210, 220, $white, $font_file, mb_convert_encoding($text, 'html-entities', 'UTF-8'));
	 //载入到期时间字体
	 imagefttext($QR , 9, 0, ($_CFG['x_pos']+10), ($_CFG['y_pos']+200), $color, $font_file, mb_convert_encoding($pic_valid_time, 'html-entities', 'UTF-8'));

	//输出图片 
	//header("Content-type: image/jpeg");
    //imagejpeg($QR);
	//每次发送指令每次重新生成二维码
	imagejpeg($QR,$file_name);
	//销毁资源 
	imagedestroy($QR);	
}
?>