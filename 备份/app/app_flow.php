<?php

define('IN_HHS', true);
require(ROOT_PATH . 'includes/lib_order.php');
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
$_SESSION['user_id'] = $_REQUEST['user_id'];
	
if($action =='add_to_cart')
{
    //清除掉上次的express_id

    include_once('includes/cls_json.php');
	//echo $_POST['goods'];exit;
         ///   $result['error'] = 22222;
			//$result['content'] = '请先登录';
        //	die($json->encode($result));
	$goods = array();
	$spec = $_REQUEST['spec'];
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

 	$goods = $json->decode($_POST['goods']);
	
	
	
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



    if (empty($_POST['goods']))
    {
		  $result['error'] = 0;
		  $result['content'] = '数据为空';
		  die($json->encode($result));
    }
    $goods = $json->decode($_POST['goods']);
	
	

    //判断是否需要关注

//    $sql="select subscribe from ".$hhs->table('goods')." where goods_id=".$goods->goods_id;
//
//    $subscribe=$db->getOne($sql);
//
//    if($subscribe==1){
//
//        $sql="select is_subscribe from ".$hhs->table('users')." where user_id=".$_SESSION['user_id'];
//
//        $is_subscribe=$db->getOne($sql);
//
//        if($is_subscribe==0){
//
//            if(!empty($_CFG['subscribe_url'])){
//
//                $result['message']  = "您要购买的商品需要您关注后购买，点击确定立即去关注,取消返回首页";//
//
//                $result['error']    =  7;
//
//                $result['url']    =  $_CFG['subscribe_url'];
//
//                $result['url2']    =  "index.php";
//
//                die($json->encode($result));
//
//            }else{
//
//                $result['message']  = "您要购买的商品需要您关注后购买";
//
//                $result['error']    =  8;
//
//                $result['url']    =  "index.php";
//
//                die($json->encode($result));
//
//            }
//
//        }
//
//         
//
//    }

    

    //判断商品是否已经下架或删除

    $sql="select count(*) from ".$hhs->table('goods')." where goods_id ='$_REQUEST[goods_id]' and is_delete=0 and is_on_sale=1 ";
    if($db->getOne($sql)==0){
		  $result['error'] = 0;
		  $result['content'] = '该商品不存在或已经删除';
		  die($json->encode($result));

    }

    

    //判断商品是否限购

    $limit_row=$db->getRow("select limit_buy_one,limit_buy_bumber,is_fresh ,promote_end_date,is_luck,is_miao from ".$hhs->table('goods')." where goods_id ='$_REQUEST[goods_id]' and  is_delete=0  ");

    

    if($limit_row['limit_buy_one'] ==1)

    {

        $where = '  og.goods_id = ' . $_REQUEST[goods_id] . ' and  oi.pay_status=2 ';

        $sql = 'select count(oi.order_id) from ' . $hhs->table('order_info') . ' as oi left join ' . $hhs->table('order_goods') . ' as og on oi.order_id = og.order_id where ' . $where . ' and oi.user_id = ' . $user_id . ' ';

        $is_buy = $db->GetOne($sql);

        if($is_buy)

        {

            $result['error']   = 5;

            $result['message'] = '该商品限购，一个用户只能购买一次';

            die($json->encode($result));

        }

        

    }

    if($limit_row['limit_buy_bumber'] && intval($goods->number) > $limit_row['limit_buy_bumber'])

    {

        $result['error']   = 5;

        $result['message'] = '该商品限购，一个用户只能购买'.$limit_row['limit_buy_bumber'];

        die($json->encode($result));

    }
    if($limit_row['is_fresh'] ==1)
    {

        $sql = 'select count(*) from ' . $hhs->table('order_info') . ' where user_id = ' . $user_id . ' ';

        $is_buy = $db->GetOne($sql);

        if($is_buy)
        {

            $result['error']   = 5;

            $result['message'] = '该商品仅限新人购买！';

            die($json->encode($result));
        }       
    }


    /* 检查：商品数量是否合法 */

    if (!is_numeric($goods->number) || intval($goods->number) <= 0)
    {
        $result['error']   = 1;
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

        $_SESSION['flow_type']=$goods->rec_type;
        if($goods->rec_type==5){
            $_SESSION['extension_code']='team_goods';
            $_SESSION['extension_id']=$goods->goods_id;
            $_SESSION['team_sign']=$goods->team_sign;
        }else{
            $_SESSION['extension_code']='';
            $_SESSION['extension_id']=$goods->goods_id;
            $_SESSION['team_sign']='';
        }

        // 更新：添加到购物车
        if (app_addto_cart($goods->goods_id, $goods->number, $goods->spec, $goods->parent,$goods->rec_type,$goods->team_sign,$user_id ))
        {
            //$rows = $GLOBALS['db']->getRow("select goods_brief,shop_price,goods_name,goods_thumb,suppliers_id from ".$GLOBALS['hhs']->table('goods')." where goods_id=".$goods->goods_id);
//            $result['shop_price'] = price_format($rows['shop_price']);
//            $result['goods_name'] = $rows['goods_name'];
//            $result['goods_thumb'] = $rows['goods_thumb'];
//            $result['goods_brief'] = $rows['goods_brief'];
//            $result['goods_id'] = $goods->goods_id;
//            $sql = 'SELECT SUM(goods_number) AS number, SUM(goods_price * goods_number) AS amount' .
//
//                   ' FROM ' . $GLOBALS['hhs']->table('cart') .
//
//                   " WHERE session_id = '" . SESS_ID . "' AND rec_type = '" . CART_GENERAL_GOODS . "'";
//            $rowss = $GLOBALS['db']->GetRow($sql);
//            $result['goods_price'] = price_format($rowss['amount']);
//            $result['goods_number'] = $rowss['number'];
//            //$result['cart_num'] = insert_cart_num();
//            $result['content'] = insert_cart_info();
//            /**
//
//             * 添加商品suppliers_id到session，方便选择配送地址
//
//             */
//            $_SESSION['goods_suppliers_id'] = $rows['suppliers_id'];
			  $result['message'] = '该商品已加入购物车';
			 $result['error'] = 1;
        }
        else
        {
            $result['message']  = $err->last_message();
            $result['error']    = $err->error_no;
            $result['goods_id'] = stripslashes($goods->goods_id);
            if (is_array($goods->spec))
            {
                $result['product_spec'] = implode(',', $goods->spec);
            }
            else
            {
                $result['product_spec'] = $goods->spec;

            }
        }
    }
	
	$result['team_sign'] = $goods->team_sign;
    $result['rec_type'] = $goods->rec_type;
	$result['session_id'] = SESS_ID;
    $result['confirm_type'] = !empty($_CFG['cart_confirm']) ? $_CFG['cart_confirm'] : 2;
    die($json->encode($result));	
}
elseif($action =='checkout')
{
	$user_id = $_REQUEST['user_id'];
    /*------------------------------------------------------ */

    //-- 订单确认

    /*------------------------------------------------------ */
    /* 取得购物类型 */
    $flow_type = 5;
	$_SESSION['extension_code']='team_goods';
	$_SESSION['team_sign'] = $_REQUEST['team_sign'];
    $today = date("w");
    $date_open = in_array($today, array(0,1,2,3)) ? 0 :1;
	
    //$smarty->assign("date_open", $date_open );
    /* 团购标志 */
    if ($flow_type == CART_GROUP_BUY_GOODS)
    {
        $smarty->assign('is_group_buy', 1);
    }
    /* 积分兑换商品 */
    elseif ($flow_type == CART_EXCHANGE_GOODS)
    { 
        $smarty->assign('is_exchange_goods', 1);
    }
    /*团购商品*/
    elseif($flow_type == 5){
        $smarty->assign('is_team_goods', 1);
    }    
    else
    {
        //正常购物流程  清空其他购物流程情况
        $_SESSION['flow_order']['extension_code'] = '';
    }
    /* 检查购物车中是否有商品 */
    $sql = "SELECT COUNT(*) FROM " . $hhs->table('cart') .
        " WHERE user_id = '" . $user_id . "' " .
        "AND parent_id = 0 AND is_gift = 0 AND rec_type = '$flow_type'";
	//echo $sql;exit;
		
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

    if ( !app_check_consignee_info($consignee, $flow_type))
    {
        /* 如果不完整则转向到收货人信息填写界面 */
          $result['error'] = 0;
    	  $result['content'] = '请先完善收货信息';
          die($json->encode($result));
    }

    $_SESSION['flow_consignee'] = $consignee;
    $smarty->assign('consignee', $consignee);
    /* 对商品信息赋值 */
    $cart_goods = app_cart_goods($flow_type,$user_id); // 取得商品列表，计算合计
	
	$result['cart_goods'] = $cart_goods;
	
  //  $smarty->assign('goods_list', $cart_goods);
    // teammem +-按钮

    if($_SESSION['extension_code']=='team_goods' && !empty($_SESSION['team_sign']) ){

        //通过团购分享购物显示更改数量的按钮

		$result['teammem'] = 1;

    }

    else{

       $result['teammem'] = 0;

    }

    // 夺宝商品直接为1，显示

    if($_SESSION['is_luck']){

        $result['teammem'] = 1;

    }


    /*

     * 取得订单信息

     */

    $order = app_flow_order_info();

    $result['order'] =$order;
    /*
     * 计算订单的费用
     */
    $total = app_order_fee($order, $cart_goods, $consignee);
	$result['total'] =$total;
 	$result['shopping_money'] =sprintf($_LANG['shopping_money'], $total['formated_goods_price']);
	$result['market_price_desc'] = sprintf($_LANG['than_market_price'], $total['formated_market_price'], $total['formated_saving'], $total['save_rate']);
	

    /* 取得配送列表 */

    $region            = array($consignee['country'], $consignee['province'], $consignee['city'], $consignee['district']);
    $shipping_list     = available_shipping_list($region);
    $cart_weight_price = app_cart_weight_price($flow_type);
    $insure_disabled   = true;
    $cod_disabled      = true;
    // 查看购物车中是否全为免运费商品，若是则把运费赋为零
    $sql = 'SELECT count(*) FROM ' . $hhs->table('cart') . " WHERE `user_id` = '" . $user_id. "' AND `extension_code` != 'package_buy' AND `is_shipping` = 0";
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
		unset($shipping_list[$key]['configure']);
    }
    // 商品邮费模板
    $goods_id = $cart_goods[0]['goods_id'];
    $goods_express = $db->getAll('select * from '.$hhs->table('goods_express').' WHERE goods_id = ' . $goods_id .' AND region_id ' . db_create_in($region) .        ' group by shipping_id');
    if(!empty($goods_express)){
        $has_cac = false;
        $shipping_list = $goods_express;
        foreach ($shipping_list as $key => $item) {
            if($item['shipping_code'] == 'cac')
            {
                $has_cac = true;
                break;
            }
        }
        if(! $has_cac){
            $point_list = array();
        }
    }
	

	
	
	$result['shipping_list'] =$shipping_list;

   

    $point_list        = available_shipping_point_list($region);

    $result['point_list'] =$point_list;



    /* 取得支付列表 */

    if ($order['shipping_id'] == 0)
    {
        $cod        = true;
        $cod_fee    = 0;
    }
    else
    {
        $shipping = shipping_info($order['shipping_id']);
        $cod = $shipping['support_cod'];
        if ($cod)
        {
            /* 如果是团购，且保证金大于0，不能使用货到付款 */
            if ($flow_type == CART_GROUP_BUY_GOODS)
            {
                $group_buy_id = $_SESSION['extension_id'];
                if ($group_buy_id <= 0)
                {
                    show_message('error group_buy_id');
                }

                $group_buy = group_buy_info($group_buy_id);

                if (empty($group_buy))

                {

                    show_message('group buy not exists: ' . $group_buy_id);

                }



                if ($group_buy['deposit'] > 0)

                {

                    $cod = false;

                    $cod_fee = 0;



                    /* 赋值保证金 */

                    $smarty->assign('gb_deposit', $group_buy['deposit']);

                }

            }



            if ($cod)

            {

                $shipping_area_info = shipping_area_info($order['shipping_id'], $region);

                $cod_fee            = $shipping_area_info['pay_fee'];

            }

        }

        else

        {

            $cod_fee = 0;

        }

    }



    // 给货到付款的手续费加<span id>，以便改变配送的时候动态显示

    $payment_list = available_payment_list(1, $cod_fee);
    if(isset($payment_list))
    {

        foreach ($payment_list as $key => $payment)


        {
			
			unset($payment_list[$key]['pay_config']);


            if ($payment['is_cod'] == '1')

            {

                $payment_list[$key]['format_pay_fee'] = '<span id="ECS_CODFEE">' . $payment['format_pay_fee'] . '</span>';

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

        }

    }


 	$result['payment_list'] =$payment_list;
	$result['user_info'] =$user_info;

    $user_info = user_info($user_id);



    /* 如果使用余额，取得用户余额 */

    if ((!isset($_CFG['use_surplus']) || $_CFG['use_surplus'] == '1')
        && $_SESSION['user_id'] > 0
        && $user_info['user_money'] > 0)
    {
        // 能使用余额
		$result['your_surplus'] =$user_info['user_money'];
		$result['allow_use_surplus'] =1;


    }
 
	/* 如果使用积分，取得用户可用积分及本订单最多可以使用的积分 */
    /*if($_REQUEST['as']=='change'){
        if()
    }*/

   

    /* 如果使用红包，取得用户可以使用的红包及用户选择的红包 */

    $goods_id = $cart_goods[0]['goods_id'];

    $sql = "SELECT bonus_allowed,bonus_free_all FROM ".$hhs->table("goods")." WHERE goods_id = " . $goods_id;

    $bonus_meta = $db->getRow($sql);

    $bonus_allowed = $bonus_meta['bonus_allowed'];

    $bonus_free_all = $bonus_meta['bonus_free_all'];

        
   


    // 取得用户可用优惠劵

    if(empty($_SESSION['team_sign'])){

        $team_first=true;

    }else{

        $team_first=false;

    }


    if ( $cart_goods[0]['bonus_allowed']

        && (!isset($_CFG['use_bonus']) || $_CFG['use_bonus'] == '1')

        && ($flow_type != CART_GROUP_BUY_GOODS && $flow_type != CART_EXCHANGE_GOODS))

    {
		

        // 取得用户可用红包

        // $user_bonus = user_bonus($_SESSION['user_id'], $total['goods_price']);

        $suppliers_id = $cart_goods[0]['suppliers_id'] ? $cart_goods[0]['suppliers_id'] :0;

        $user_bonus = team_user_bonus($user_id, $total['goods_price'],$suppliers_id);
	
    }


    // 免单券

    if($bonus_free_all && $team_first)

    {

        $suppliers_id = $cart_goods[0]['suppliers_id'] ? $cart_goods[0]['suppliers_id'] :0;

        $user_bonus = user_bonus($user_id, $total['goods_price'],true, $bonus_free_all ,1,$suppliers_id);

    }

    else{

        foreach ($user_bonus as $key => $bonus) {

            if($bonus['free_all'] ==1)

                unset($user_bonus[$key]);

        }

    }


	

    if (!empty($user_bonus))

    {

        $muse_end_time=$user_bonus[0]['use_end_date'];

        $mbonus_id=$user_bonus[0]['bonus_id'];

        foreach ($user_bonus AS $key => $val)

        {

            if($muse_end_time<$val['use_end_date']){

                $muse_end_time=$val['use_end_date'];

                $mbonus_id=$val['bonus_id'];

            }

            $user_bonus[$key]['bonus_money_formated'] = price_format($val['type_money'], false);

            $user_bonus[$key]['use_startdate']   = local_date($GLOBALS['_CFG']['date_format'], $val['use_start_date']);

            $user_bonus[$key]['use_enddate']     = local_date($GLOBALS['_CFG']['date_format'], $val['use_end_date']);
        }
		$idsss =0;
		foreach($user_bonus as $idss=>$v)
		{
			$user_bonusa[$idsss] = $v;
			$idsss++;
		}
		
		
		$result['mbonus_id'] =$mbonus_id;
		$result['bonus_list'] =$user_bonus;
		$result['abonus_list'] =$user_bonusa;
    }  
	  // 能使用优惠劵
	$result['allow_use_bonus'] =$bonus_allowed;
		
	$result['team_sign'] = $_SESSION['team_sign'];
	
	
	  
  
    if (empty($_SESSION['user_loc'])) {
        $sql="select lat,lng from ".$hhs->table("users")." where user_id='".$user_id."' ";
        $_SESSION['user_loc'] = $db->getRow($sql);
    }
    //$smarty->assign('lat', $_SESSION['user_loc']['lat']);

   // $smarty->assign('lng', $_SESSION['user_loc']['lng']);

    /* 保存 session */

    $_SESSION['flow_order'] = $order;	
	die($json->encode($result));
	
}
elseif($action =='update_cart')
{
   	$rec_id = $_REQUEST['rec_id'];
    $number = $_REQUEST['number'];
    $goods_id = $_REQUEST['goods_id'];
	
    $result['rec_id'] = $rec_id;

    /* 检查：库存 */

    if ($GLOBALS['_CFG']['use_storage'] == 1)

    {

        $goods_number = $GLOBALS['db']->getOne("select goods_number from ".$GLOBALS['hhs']->table('goods')." where goods_id='$goods_id'");
        if($number>$goods_number)
        {
             $result['error'] = 1 ;
             $result['content'] ='对不起,您选择的数量超出库存您最多可购买'.$goods_number."件";
             $result['number']=$goods_number;
             die($json->encode($result));
        }
    }
    $limit_buy_bumber = $db->getOne("select limit_buy_bumber from ".$hhs->table('goods')." where goods_id='$goods_id'");
    if ($number == 0)
    {
        $result['error'] = 1 ;
        $result['number'] = $number = 1;
        die($json->encode($result));
    }
    if($number>$limit_buy_bumber&&$limit_buy_bumber>0)
    {
        $result['error'] = 1 ;
        $result['content'] = '购买数量不可大于限购数量';
        $result['number'] = $limit_buy_bumber;
        die($json->encode($result));
    }
    if($_SESSION['is_luck']){
       $left_num = $GLOBALS['db']->getOne('select (team_num-teammen_num) as left_num from '.$GLOBALS['hhs']->table('order_info').' where extension_id="'.$goods_id.'" and team_status = 1 AND pay_status=2 order by order_id desc');
       // $left_num && $goods['goods_number'] = $left_num;
        if($left_num > 0 && $number>$left_num)
        {
            $result['error'] = 1 ;
            $result['message'] = '对不起,您选择的数量超出库存您最多可购买'.$left_num."件";
            $result['number'] = $left_num;
            die($json->encode($result));
        }
    }
    $sql = "UPDATE " . $GLOBALS['hhs']->table('cart') . " SET goods_number = '$number' WHERE rec_id = $rec_id";
    $GLOBALS['db']->query($sql);
    /* 获得收货人信息 */
    $consignee = get_consignee($user_id);
    $order = flow_order_info();
    $cart_goods = app_cart_goods(5,$user_id); // 取得商品列表，计算合计
    $total = app_order_fee($order, $cart_goods, $consignee);
	$result['total'] = $total;
    $result['number'] = $number ;
    die($json->encode($result));	
	
}
elseif ($action == 'json_done')

{
	

    include_once('includes/lib_clips.php');

    include_once('includes/lib_payment.php');

    include_once('includes/cls_json.php');

    // include_once('includes/lib_fenxiao.php');

    $json = new JSON();

    $result = array('error' => 0,'message'=>'', 'content' => '');
  
  
	$_POST = $_REQUEST;


  

    /* 取得购物类型 */
    $flow_type = 5;
	$_SESSION['extension_code']='team_goods';
	$_SESSION['team_sign'] = $_REQUEST['team_sign'];
	$_SESSION['flow_type']  = $flow_type;
    /* 检查购物车中是否有商品 */

    $sql = "SELECT COUNT(*) FROM " . $hhs->table('cart') .
    " WHERE user_id = '" . $user_id . "' " .
    "AND parent_id = 0 AND is_gift = 0 AND rec_type = '$flow_type'";

    if ($db->getOne($sql) == 0)
    {
        $result['error']=1;
        $result['message']='购物车没有商品';
        $result['url']="index.php";
        die($json->encode($result));
        //show_message($_LANG['no_goods_in_cart'], '', '', 'warning');

    }

   

    //检查是否已满团购-》支付回调再判断一次

    if ( $_SESSION['extension_code']=='team_goods' && !empty($_SESSION['team_sign']) )

    { 

        //判断是否是自己的

        $sql="select count(*) from ".$hhs->table('order_info') ." where team_sign=".$_SESSION['team_sign']." and  pay_status > 1 and user_id=".$_SESSION['user_id'];

        $temp=$db->getOne($sql);

        if($temp>0){

            $result['error']=0;
			
			$result['content']='自己的开的团不能再参团';

            $result['url']="share.php?team_sign=".$_SESSION['team_sign']."&uid=".$uid;

            die($json->encode($result));

        }

        $sql="select team_num from ".$hhs->table('order_info') ." where order_id=".$_SESSION['team_sign'];

        $team_num=$db->getOne($sql);

        //实际人数

        $sql="select count(*) from ".$hhs->table('order_info')." where team_sign=".$_SESSION['team_sign']." and team_status>0 ";

        $rel_num=$db->getOne($sql);

        if($team_num<=$rel_num){

            $result['error']=0;
			
			$result['content']='团人数已经够了';

            $result['url']="share.php?team_sign=".$_SESSION['team_sign']."&uid=".$uid;

            die($json->encode($result));

        }

    }



    $consignee = get_consignee($_SESSION['user_id']);



    /* 检查商品库存 */

    /* 如果使用库存，且下订单时减库存，则减少库存 */

    if ($_CFG['use_storage'] == '1' && $_CFG['stock_dec_time'] == SDT_PLACE)

    {

        $cart_goods_stock = get_cart_goods();

        $_cart_goods_stock = array();

        foreach ($cart_goods_stock['goods_list'] as $value)

        {

            $_cart_goods_stock[$value['rec_id']] = $value['goods_number'];

        }

        flow_cart_stock($_cart_goods_stock,$user_id);

        unset($cart_goods_stock, $_cart_goods_stock);

    }



    /*

     * 检查用户是否已经登录

    * 如果用户已经登录了则检查是否有默认的收货地址

    * 如果没有登录则跳转到登录和注册页面

    */



    

    $_POST['how_oos'] = isset($_POST['how_oos']) ? intval($_POST['how_oos']) : 0;

    $_POST['card_message'] = isset($_POST['card_message']) ? compile_str($_POST['card_message']) : '';

    $_POST['inv_type'] = !empty($_POST['inv_type']) ? compile_str($_POST['inv_type']) : '';

    $_POST['inv_payee'] = isset($_POST['inv_payee']) ? compile_str($_POST['inv_payee']) : '';

    $_POST['inv_content'] = isset($_POST['inv_content']) ? compile_str($_POST['inv_content']) : '';

    $_POST['postscript'] = isset($_POST['postscript']) ? urldecode($_POST['postscript']) : '';

    $_POST['postscript'] = isset($_POST['postscript']) ? compile_str($_POST['postscript']) : '';



    $order = array(

        'shipping_id'     =>intval($_POST['shipping_id']),

        'suppliers_id'     =>intval($_POST['suppliers_id']),

        'point_id'         =>intval($_POST['point_id']),

        'pay_id'          => intval($_POST['payment']),

        'pack_id'         => isset($_POST['pack']) ? intval($_POST['pack']) : 0,

        'card_id'         => isset($_POST['card']) ? intval($_POST['card']) : 0,

        'card_message'    => trim($_POST['card_message']),

        'surplus'         => isset($_POST['surplus']) ? floatval($_POST['surplus']) : 0.00,

        'integral'        => isset($_POST['integral']) ? intval($_POST['integral']) : 0,

        'bonus_id'        => isset($_POST['bonus']) ? intval($_POST['bonus']) : 0,

        'need_inv'        => empty($_POST['need_inv']) ? 0 : 1,

        'inv_type'        => $_POST['inv_type'],

        'inv_payee'       => trim($_POST['inv_payee']),

        'inv_content'     => $_POST['inv_content'],

        'postscript'      => trim($_POST['postscript']),

        'how_oos'         => isset($_LANG['oos'][$_POST['how_oos']]) ? addslashes($_LANG['oos'][$_POST['how_oos']]) : '',

        'need_insure'     => isset($_POST['need_insure']) ? intval($_POST['need_insure']) : 0,

        'user_id'         => $_SESSION['user_id'],



        'is_miao'         => $_SESSION['is_miao'], //秒杀产品



        'add_time'        => gmtime(),

        'order_status'    => OS_UNCONFIRMED,

        'shipping_status' => SS_UNSHIPPED,

        'pay_status'      => PS_UNPAYED,

        'agency_id'       => get_agency_by_regions(array($consignee['country'], $consignee['province'], $consignee['city'], $consignee['district'])),

        'lat'           =>trim($_POST['lat']),

        'lng'           =>trim($_POST['lng']),

        'city_id'           =>intval($_POST['city_id']),

        'district_id'           =>intval($_POST['district_id']),

        'checked_mobile' =>trim($_POST['checked_mobile']),//自提点手机

        'package_one'    =>isset($_POST['package_one'])?intval($_POST['package_one']):0,//团长收货        

    );





    if (! empty($order['checked_mobile'])) {

        $db->query('update '.$hhs->table('user_address').' set `mobile` = "'.$order['checked_mobile'].'" where user_id = "'.$user_id.'" AND address_id = "'.$consignee['address_id'].'"');

    }

    $cart_goods = app_cart_goods($flow_type,$user_id);
 

    /* 扩展信息 */

    if (isset($_SESSION['flow_type']) && intval($_SESSION['flow_type']) != CART_GENERAL_GOODS)

    {

        $order['extension_code'] = $_SESSION['extension_code'];

        $order['extension_id'] = $cart_goods[0]['goods_id'];

        $order['team_sign'] = $_SESSION['team_sign'];

        //$order['team_first'] = 1;

    }

    else

    {

        $order['extension_code'] = '';

        $order['extension_id'] = $_SESSION['extension_id'];

        $order['team_sign'] = 0;

        //$order['team_first'] = 0;

    }
   // $user_id = $_SESSION['user_id'];
    $user_info = user_info($user_id);


    /* 检查积分余额是否合法 */

    

    if ($user_id > 0)
    {
        $user_info = user_info($user_id);
        $order['surplus'] = min($order['surplus'], $user_info['user_money'] + $user_info['credit_line']);
        if ($order['surplus'] < 0)
        {
            $order['surplus'] = 0;
        }
		
		 // 查询用户有多少积分
        $flow_points = flow_available_points($user_id);  // 该订单允许使用的积分
        $user_points = $user_info['pay_points']; // 用户的积分总数

        $order['integral'] = min($order['integral'], $user_points, $flow_points);
        if ($order['integral'] < 0)
        {
            $order['integral'] = 0;
        }

    }

    else

    {

        $order['surplus']  = 0;

    }

    $order['integral'] = 0;



    /* 检查优惠劵是否存在 */

    if ($order['bonus_id'] > 0)

    {

        $bonus = bonus_info($order['bonus_id']);



        if (empty($bonus) || $bonus['user_id'] != $user_id || $bonus['order_id'] > 0 || $bonus['min_goods_amount'] > app_cart_amount(true, $flow_type,'',$user_id))

        {

            $order['bonus_id'] = 0;

        }

    }

    elseif (isset($_POST['bonus_sn']))

    {

        $bonus_sn = trim($_POST['bonus_sn']);

        $bonus = bonus_info(0, $bonus_sn);

        $now = gmtime();

        if (empty($bonus) || $bonus['user_id'] > 0 || $bonus['order_id'] > 0 || $bonus['min_goods_amount'] > cart_amount(true, $flow_type) || $now > $bonus['use_end_date'])

        {

        }

        else

        {

            if ($user_id > 0)

            {

                $sql = "UPDATE " . $hhs->table('user_bonus') . " SET user_id = '$user_id' WHERE bonus_id = '$bonus[bonus_id]' LIMIT 1";

                $db->query($sql);

            }

            $order['bonus_id'] = $bonus['bonus_id'];

            $order['bonus_sn'] = $bonus_sn;

        }

    }



    /* 订单中的商品 */

	
	$order['is_luck'] =  $cart_goods[0]['is_luck'];
	
	$order['luck_times'] =  $cart_goods[0]['luck_times'];
	
	       
   
    $gods_img = $cart_goods[0]['little_img'];
   // print_R($cart_goods);die;

    if (empty($cart_goods))

    {

        //show_message($_LANG['no_goods_in_cart'], $_LANG['back_home'], './', 'warning');

        $result['error']=0;

        $result['message']=$_LANG['no_goods_in_cart'];

        die($json->encode($result));

    }

    $order['suppliers_id'] = $cart_goods[0]['suppliers_id'];

    $order['goods_id'] = $cart_goods[0]['goods_id'];



    /* 检查商品总额是否达到最低限购金额 */

    if ($flow_type == CART_GENERAL_GOODS && app_cart_amount(true, CART_GENERAL_GOODS,'',$user_id) < $_CFG['min_goods_amount'])

    {

        //show_message(sprintf($_LANG['goods_amount_not_enough'], price_format($_CFG['min_goods_amount'], false)));

        $result['error']=1;

        $result['message']=sprintf($_LANG['goods_amount_not_enough'], price_format($_CFG['min_goods_amount'], false));

        die($json->encode($result));

    }



    /* 收货人信息 */

    unset($consignee['region']);//剔除，表里面没有这个

    foreach ($consignee as $key => $value)
    {
		unset($consignee['user_id']);
        $order[$key] = addslashes($value);

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

    /* 订单中的总额 */

    $total = app_order_fee($order, $cart_goods, $consignee);

    $order['bonus']        = $total['bonus'];

    $order['goods_amount'] = $total['goods_price'];

    $order['discount']     = $total['discount'];

    $order['surplus']      = $total['surplus'];

    $order['tax']          = $total['tax'];



    

    // 购物车中的商品能享受优惠劵支付的总额

    $discount_amout = compute_discount_amount();

    // 优惠劵和积分最多能支付的金额为商品总额

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
            //show_message($_LANG['balance_not_enough']);
            $result['error']=2;
            $result['message']='当前余额不足支付此订单';
            die($json->encode($result));
        }
        else
        {
            $order['surplus'] = $order['order_amount'];
            $order['order_amount'] = 0;
        }
    }

    /* 如果订单金额为0（使用余额或积分或优惠劵支付），修改订单状态为已确认、已付款 */
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
    //系统自带的分成
    $order['parent_id'] = 0;
    //区分购买类别
    if ($order['extension_code']=='team_goods')
    {
        $order['order_type'] = 2;//团购
    }
    else{

        if($cart_goods[0]['is_zero'] == 1){
            $order['order_type'] = 3;//0元购
        }
        else{
            $order['order_type'] = 1;//普通商城
        }
    }
    $best_time = isset($_POST['best_time']) ? urldecode($_POST['best_time']) : '';
    $order['best_time'] = $best_time;
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
        "city_id,district_id,suppliers_id,order_id, goods_id, goods_name, goods_sn, product_id, goods_number, market_price, ".
        "goods_price, goods_attr, is_real, extension_code, parent_id, is_gift, goods_attr_id,rate_1,rate_2,rate_3) ".
        " SELECT city_id,district_id,suppliers_id,'$new_order_id', goods_id, goods_name, goods_sn, product_id, goods_number, market_price, ".
        "goods_price, goods_attr, is_real, '$_SESSION[extension_code]' , parent_id, is_gift, goods_attr_id,rate_1,rate_2,rate_3".
        " FROM " .$hhs->table('cart') .
        " WHERE user_id = '".$user_id."' AND rec_type = '$flow_type'";
    $db->query($sql);
    if ($order['extension_code']=='team_goods' )
    {
        if(empty($order['team_sign'])){
            $sql = "UPDATE ". $hhs->table('order_info') ." SET team_sign=".$order['order_id'].",team_first=1  WHERE order_id=".$order['order_id'];
            $db->query($sql);
            $order['team_sign']=$order['order_id'];
            $order['team_first']=1;
        }else{
            $sql = "UPDATE ". $hhs->table('order_info') ." SET team_first=2  WHERE order_id=".$order['order_id'];
            $db->query($sql);
            $order['team_first']=2;
			// $order['team_sign']=$order['order_id'];
        }
    }
     
    /*积分买商品*/
    if($_POST['chage']==1){
        
        $sql = "select is_exchange from ".$hhs->table('order_info')." where order_id='$new_order_id'";
        $sta = $db->getOne($sql);
        
        if($sta == 0){
            $pay_points = $user_info['pay_points']-$_SESSION['exchange_integral'];
            $sql = "update ".$hhs->table('users')." set  pay_points='$pay_points' where user_id = $user_id";
            $rs = $db->query($sql);
            if($rs){
                $sql = "update ".$hhs->table('order_info')." set is_exchange=1 where order_id='$new_order_id'";
                $db->query($sql);
                $sql = "insert into ".$hhs->table('account_log')."(user_id,pay_points,change_time,change_desc,change_type) values('$user_id','-{$_SESSION['exchange_integral']}',".gmtime().",'消费积分减少',99)";
                $db->query($sql);
                
                require_once(ROOT_PATH.'/includes/modules/payment/wxpay.php');
                $sqs = "select openid from ".$hhs->table('users')." where user_id = $user_id";
                 $openid = $db->getOne($sqs);
                $url = 'user.php?act=order_detail&order_id='.$new_order_id.'&uid='.$user_id;

                $desc = "恭喜您成功兑换'".$cart_goods[0]['goods_name']."'商品，\r\n稍后我们会为您尽快安排发货";
                
                $weixin=new class_weixin($GLOBALS['appid'],$GLOBALS['appsecret']);
                
                $img = $gods_img;
                $weixin->send_wxmsg($openid, '兑换成功' , $url , $desc,$img);
            }
        }        
    }
        
    /* 清空购物车 */

    app_clear_cart($flow_type,$user_id);

    /* 清除缓存，否则买了商品，但是前台页面读取缓存，商品数量不减少 */

    clear_all_files();
    /* 插入支付日志 */
    $order['log_id'] = insert_pay_log($new_order_id, $order['order_amount'], PAY_ORDER);
    /* 如果使用库存，且下订单时减库存，则减少库存 */

    // if ($_CFG['use_storage'] == '1' && $_CFG['stock_dec_time'] == SDT_PLACE)

    // {

    //     change_order_goods_storage($order['order_id'], true, SDT_PLACE);

    // }

 
//支付类型，在线支付或者代付

    # 在线支付
    /* 处理余额、积分、优惠劵 */
    if ($order['user_id'] > 0 && $order['surplus'] > 0)
    {
        log_account_change($order['user_id'], $order['surplus'] * (-1), 0, 0, 0, sprintf($_LANG['pay_order'], $order['order_sn']));
    }
    if ($order['bonus_id'] > 0 && $temp_amout > 0)
    {
        use_bonus($order['bonus_id'], $new_order_id);
    }
    $order['goods_name'] = $cart_goods[0]['goods_name'];
	$payment = payment_info($order['pay_id']);
	$result['pay_code']  =$payment['pay_code']; 
    /* 取得支付信息，生成支付代码 */
    if ($order['order_amount'] > 0)
    {
		$result['order'] = $order;
        $result['error']=0;

		
        $result['message']='余额支付以外的支付';
    }else{
        $result['error']=0;
        $result['message']='不需要支付直接下单';//不需支付直接下单
		$result['order'] = $order;
		
        if($order['extension_code']=='team_goods'){
           // $result['url']="user.php?act=order_detail&order_id=".$new_order_id."&team=1&uid=".$uid;
		    $result['extension_code'] = 'team_goods';
			
            pay_team_action($order['order_sn']);
        }
        else{
			$order['extension_code'] ='';
           // $result['url']="user.php?act=order_detail&order_id=".$new_order_id."&uid=".$uid;
        }
        //改变团购状态
    }
    
    if(!empty($order['shipping_name']))
    {
        $order['shipping_name']=trim(stripcslashes($order['shipping_name']));
    }



    ob_end_clean();
    die($json->encode($result));

}
elseif($action =='select_shipping')
{
    /*------------------------------------------------------ */
    //-- 改变配送方式
    /*------------------------------------------------------ */
    include_once('includes/cls_json.php');
    $json = new JSON;
    $result = array('error' => '', 'content' => '', 'need_insure' => 0);
    /* 取得购物类型 */
    $flow_type = 5;
    /* 获得收货人信息 */
    $consignee = get_consignee($user_id);
    /* 对商品信息赋值 */
    $cart_goods = app_cart_goods($flow_type,$user_id); // 取得商品列表，计算合计
    if (empty($cart_goods))
    {
        $result['error'] = 0;
		$result['content'] = '购物车没有商品';
    }
    else

    {
        /* 取得订单信息 */

        $order = app_flow_order_info();
        $order['shipping_id'] = intval($_REQUEST['shipping']);
        $regions = array($consignee['country'], $consignee['province'], $consignee['city'], $consignee['district']);
        $shipping_info = shipping_area_info($order['shipping_id'], $regions);
        //express 
        $express_id = isset($_REQUEST['express_id']) ? intval($_REQUEST['express_id']) : 0;
        $_SESSION['express_id'] = $express_id;
		
		
		
        $bonus = bonus_info(intval($_REQUEST['bonus']));
        if ((!empty($bonus) && $bonus['user_id'] == $_SESSION['user_id']) || $_REQUEST['bonus'] == 0)
        {
            if($bonus['suppliers_id'] == $cart_goods[0]['suppliers_id'])
            {
                $order['bonus_id'] = intval($_GET['bonus']);
            }
            else{
                $order['bonus_id'] = 0;
                if(intval($_GET['bonus'])>0)
                    $result['error'] = '该优惠券仅限发券商家使用';
            }
        }
        else
        {
            $order['bonus_id'] = 0;
            $result['error'] = $_LANG['invalid_bonus'];
        }
		
		
		
		

        /* 计算订单的费用 */

        $total = app_order_fee($order, $cart_goods, $consignee);
		
		
		$result['total'] = $total;
        $result['total_integral'] = app_cart_amount(false, $flow_type,'',$user_id) - $total['bonus'] - $total['integral_money'];
		$result['total_bonus']= price_format(get_total_bonus(), false);

        /* 团购标志 */



        $result['cod_fee']     = $shipping_info['pay_fee'];

        if (strpos($result['cod_fee'], '%') === false)

        {

            $result['cod_fee'] = price_format($result['cod_fee'], false);

        }

        $result['need_insure'] = ($shipping_info['insure'] > 0 && !empty($order['need_insure'])) ? 1 : 0;

    }



    echo $json->encode($result);

    exit;
	
	
}


elseif ($action == 'change_bonus')
{
    /*------------------------------------------------------ */
    //-- 改变优惠劵
    /*------------------------------------------------------ */
    include_once('includes/cls_json.php');
    $result = array('error' => '', 'content' => '');
    /* 取得购物类型 */
    $flow_type = 5;
    /* 获得收货人信息 */
    $consignee = get_consignee($user_id);
    /* 对商品信息赋值 */
    $cart_goods = app_cart_goods($flow_type,$user_id); // 取得商品列表，计算合计
    if (empty($cart_goods) )
    {
        $result['error'] = $_LANG['no_goods_in_cart'];
    }
    else
    {
        /* 取得订单信息 */
        $order = app_flow_order_info();
		
		$order['shipping_id'] = intval($_REQUEST['shipping']);
		
        $bonus = bonus_info(intval($_GET['bonus']));
        if ((!empty($bonus) && $bonus['user_id'] == $_SESSION['user_id']) || $_GET['bonus'] == 0)
        {
            if($bonus['suppliers_id'] == $cart_goods[0]['suppliers_id'])
            {
                $order['bonus_id'] = intval($_GET['bonus']);
            }
            else{
                $order['bonus_id'] = 0;
                if(intval($_GET['bonus'])>0)
                    $result['error'] = '该优惠券仅限发券商家使用';
            }
        }
        else
        {
            $order['bonus_id'] = 0;
            $result['error'] = $_LANG['invalid_bonus'];
        }
        /* 计算订单的费用 */

        $total = app_order_fee($order, $cart_goods, $consignee);
		$result['total'] = $total;
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
	$is_luck = $_REQUEST['is_luck'];
	$team_sign = $_REQUEST['team_sign'];
	$attach ='';
	$result = alipay_change_wfx_paystatus($resultstatus,$order_sn,$attach);
	if($result)
	{
		 $results['is_luck'] = $is_luck;
		 $results['team_sign'] = $team_sign;
		 $results['pay'] =1;
		 $results['order_id'] = $order_id;
  	 	 $json = new JSON();
   		 die($json->encode($results));
	}
	else
	{
		 $results['is_luck'] = $is_luck;
		 $results['team_sign'] = $team_sign;
		 $results['order_id'] = $order_id;
		 
		 $results['pay'] =0;
  	 	 $json = new JSON();
   		 die($json->encode($results));
		
	}
	
}

/**
 * 添加商品到购物车
 *
 * @access  public
 * @param   integer $goods_id   商品编号
 * @param   integer $num        商品数量
 * @param   array   $spec       规格值对应的id数组
 * @param   integer $parent     基本件
 * @return  boolean
 */
function app_addto_cart($goods_id, $num = 1, $spec = array(), $parent = 0,$rec_type=0,$team_sign=0,$user_id)
{
    $GLOBALS['err']->clean();
    $_parent_id = $parent;
    app_clear_cart(0,$user_id);
   app_clear_cart(5,$user_id);
    /* 取得商品信息 */
    $sql = "SELECT g.rate_1,g.rate_2,g.rate_3,g.is_miao,g.luck_times, g.is_luck, g.suppliers_id,g.city_id,g.district_id,g.team_num,g.team_price,g.goods_name, g.goods_sn, g.is_on_sale, g.is_real,g.is_zero,g.shipping_fee,g.is_team,g.bonus_allowed, ".
                "g.market_price, g.shop_price AS org_price, g.promote_price, g.promote_start_date, ".
                "g.promote_end_date, g.goods_weight, g.integral, g.extension_code, ".
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
    //抽奖产品，存一下 what ? insert order 有用
    $_SESSION['is_luck']    = $goods['is_luck'];
    $_SESSION['luck_times'] = $goods['luck_times'];
    $_SESSION['is_miao']    = $goods['is_miao'];

    // 抽奖产品重新定义team_sign
    if($_SESSION['is_luck']){
       $luck_row = $GLOBALS['db']->getRow('select team_sign,(team_num-teammen_num) as left_num from '.$GLOBALS['hhs']->table('order_info').' where extension_id="'.$goods_id.'" and team_first = 1 AND team_status = 1 AND pay_status=2 and luck_times = "'.$_SESSION['luck_times'].'" order by order_id desc');
       $_SESSION['team_sign'] = isset($luck_row['team_sign']) ? intval($luck_row['team_sign']) : 0;
       $goods['goods_number'] = isset($luck_row['left_num']) ? intval($luck_row['left_num']) : $goods['goods_number'];
    }

    /* 如果是作为配件添加到购物车的，需要先检查购物车里面是否已经有基本件 */
    if ($parent > 0)
    {
        $sql = "SELECT COUNT(*) FROM " . $GLOBALS['hhs']->table('cart') .
                " WHERE goods_id='$parent' AND user_id='" . $user_id . "' AND extension_code <> 'package_buy'";
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
        'user_id'        => $user_id,
        'session_id'     => SESS_ID,
        'goods_id'       => $goods_id,
        'goods_sn'       => addslashes($goods['goods_sn']),
        'product_id'     => $product_info['product_id'],
        'goods_name'     => addslashes($goods['goods_name']),
        'market_price'   => $goods['market_price'],
        'goods_attr'     => addslashes($goods_attr),
        'goods_attr_id'  => $goods_attr_id,
        'is_real'        => $goods['is_real'],
        'extension_code' => $goods['extension_code'],
        'is_gift'        => 0,
        'is_shipping'    => $goods['is_shipping'],
        'rec_type'       => $rec_type,
        'team_sign'      => $team_sign,
        'city_id'        => $goods['city_id'],
        'district_id'    => $goods['district_id'],
        'suppliers_id'   => $goods['suppliers_id'],
        
        'bonus_allowed'  => $goods['bonus_allowed'],
        'is_zero'        => $goods['is_zero'],
        'shipping_fee'   => $goods['shipping_fee'],
        'is_team'        => $goods['is_team'],
        'rate_1'        => $goods['rate_1'],
        'rate_2'        => $goods['rate_2'],
        'rate_3'        => $goods['rate_3'],

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
                " WHERE user_id = '" . $user_id . "'" .
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
                " AND rec_type = '$rec_type'";

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
            if ($GLOBALS['_CFG']['use_storage'] == 0 || $num <= $goods_storage)
            {
                //下面的不执行
                $goods_price = get_final_price($goods_id, $num, true, $spec);
                $sql = "UPDATE " . $GLOBALS['hhs']->table('cart') . " SET goods_number = '$num'" .
                       " , goods_price = '$goods_price'".
                       " WHERE user_id = '" .$user_id. "' AND goods_id = '$goods_id' ".
                       " AND parent_id = 0 AND goods_attr = '" .get_goods_attr_info($spec). "' " .
                       " AND extension_code <> 'package_buy' " .
                       "AND rec_type = '$rec_type'";
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
            if($rec_type==5){
                /**
                 * 秒殺的修正價格
                 */
                if ($goods['is_miao']) {
                    $promote_price = bargain_price($goods['promote_price'], $goods['promote_start_date'], $goods['promote_end_date']);
                    if($promote_price>0)
                    {
                        $goods['team_price'] = $goods['promote_price'];
                    }
                }
                $parent['goods_price']=$goods['team_price']+spec_price($spec,true);
            }
            
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
 * 取得购物车商品
 * @param   int     $type   类型：默认普通商品
 * @return  array   购物车商品数组
 */
function app_cart_goods($type = CART_GENERAL_GOODS,$user_id)
{
	global $redirect_uri;
    $sql = "SELECT rec_id, user_id, goods_id, goods_name, goods_sn, goods_number, " .
            "market_price, goods_price, goods_attr, is_real, extension_code, parent_id, is_gift, is_shipping, city_id,district_id,suppliers_id,shipping_fee,is_zero,bonus_allowed," .
            "goods_price * goods_number AS subtotal " .
            "FROM " . $GLOBALS['hhs']->table('cart') .
            " WHERE user_id = '" . $user_id . "' " .
            "AND rec_type = '$type'";

    $arr = $GLOBALS['db']->getAll($sql);

    /* 格式化价格及礼包商品 */
    foreach ($arr as $key => $value)
    {
        $goods_thumb = $GLOBALS['db']->getOne("SELECT `goods_thumb` FROM " . $GLOBALS['hhs']->table('goods') . " WHERE `goods_id`=".$arr[$key]['goods_id']);
        $arr[$key]['goods_thumb'] = $redirect_uri.get_image_path($arr[$key]['goods_id'], $goods_thumb, true);
        $little_img =$arr[$key]['little_img'] = $GLOBALS['db']->getOne("SELECT `little_img` FROM " . $GLOBALS['hhs']->table('goods') . " WHERE `goods_id`=".$arr[$key]['goods_id']);
        $arr[$key]['little_img'] = $redirect_uri.$arr[$key]['little_img'];
        //$goods_kucun = $GLOBALS['db']->getOne("SELECT `goods_number` FROM " . $GLOBALS['hhs']->table('goods') . " WHERE `goods_id`=".$arr[$key]['goods_id']);
        $arr[$key]['goods_kucun'] = $GLOBALS['db']->getOne("SELECT `goods_number` FROM " . $GLOBALS['hhs']->table('goods') . " WHERE `goods_id`=".$arr[$key]['goods_id']);
        
        $arr[$key]['formated_market_price'] = price_format($value['market_price'], false);
        $arr[$key]['formated_goods_price']  = price_format($value['goods_price'], false);
        $arr[$key]['formated_subtotal']     = price_format($value['subtotal'], false);

        if ($value['extension_code'] == 'package_buy')
        {
            $arr[$key]['package_goods_list'] = get_package_goods($value['goods_id']);
        }
    }

    return $arr;
}
/**
 * 获得订单信息
 *
 * @access  private
 * @return  array
 */
function app_flow_order_info()
{
    $order = isset($_SESSION['flow_order']) ? $_SESSION['flow_order'] : array();

    /* 初始化配送和支付方式 */
    if (!isset($order['shipping_id']) || !isset($order['pay_id']))
    {
        /* 如果还没有设置配送和支付 */
        if ($_SESSION['user_id'] > 0)
        {
            /* 用户已经登录了，则获得上次使用的配送和支付 */
            $arr = last_shipping_and_payment();

            if (!isset($order['shipping_id']))
            {
                $order['shipping_id'] = $arr['shipping_id'];
            }
            if (!isset($order['pay_id']))
            {
                $order['pay_id'] = $arr['pay_id'];
            }
        }
        else
        {
            if (!isset($order['shipping_id']))
            {
                $order['shipping_id'] = 0;
            }
            if (!isset($order['pay_id']))
            {
                $order['pay_id'] = 0;
            }
        }
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
        $order['bonus'] = 0;    // 初始化优惠劵
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
    if (isset($_SESSION['flow_type']) && intval($_SESSION['flow_type']) != CART_GENERAL_GOODS )
    {
        $order['extension_code'] = $_SESSION['extension_code'];
        $order['extension_id'] = $_SESSION['extension_id'];
    }

    return $order;
}
/**
 * 检查收货人信息是否完整
 * @param   array   $consignee  收货人信息
 * @param   int     $flow_type  购物流程类型
 * @return  bool    true 完整 false 不完整
 */
function app_check_consignee_info($consignee, $flow_type)
{
  //  if (exist_real_goods(0, $flow_type))
   // {
        /* 如果存在实体商品 */
        $res = !empty($consignee['consignee']) &&
            !empty($consignee['country']) &&
            !empty($consignee['mobile']);
            //!empty($consignee['email']) &&
           // !empty($consignee['tel']);

        if ($res)
        {
            if (empty($consignee['province']))
            {
                /* 没有设置省份，检查当前国家下面有没有设置省份 */
                $pro = get_regions(1, $consignee['country']);
                $res = empty($pro);
            }
            elseif (empty($consignee['city']))
            {
                /* 没有设置城市，检查当前省下面有没有城市 */
                $city = get_regions(2, $consignee['province']);
                $res = empty($city);
            }
            elseif (empty($consignee['district']))
            {
                $dist = get_regions(3, $consignee['city']);
                $res = empty($dist);
            }
        }

        return $res;
   // }
   // else
   // {
        /* 如果不存在实体商品 */
      //  return !empty($consignee['consignee']) &&
       //     !empty($consignee['email']) &&
       //     !empty($consignee['tel']);
   // }
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

function flow_available_points($user_id)
{
    $sql = "SELECT SUM(g.integral * c.goods_number) ".
            "FROM " . $GLOBALS['hhs']->table('cart') . " AS c, " . $GLOBALS['hhs']->table('goods') . " AS g " .
            "WHERE c.user_id = '" . $user_id . "' AND c.goods_id = g.goods_id AND c.is_gift = 0 AND g.integral > 0 " .
            "AND c.rec_type = '" . CART_GENERAL_GOODS . "'";
    $val = intval($GLOBALS['db']->getOne($sql));
    return integral_of_value($val);

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
function app_order_fee($order, $goods, $consignee)
{
    global $db,$hhs,$user_id;
	
	
    /* 初始化订单的扩展code */
    if (!isset($order['extension_code']))
    {
        $order['extension_code'] = '';
    }

    if ($order['extension_code'] == 'group_buy')
    {
        $group_buy = group_buy_info($order['extension_id']);
    }

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
                    'amount'                => 0,
                    'amount_formated'       => '0.00',
                    'shipping_fee_formated' => '0.00',
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
    if ($order['extension_code'] != 'group_buy' && $order['extension_code'] != 'team_goods')
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

    /* 优惠劵 */

    if (!empty($order['bonus_id']))
    {
        $bonus          = bonus_info($order['bonus_id']);
        // 免单券
        if($bonus['free_all'] == 1)
        {
            return $total;
        }
        $total['bonus'] = $bonus['type_money'];
    }
    $total['bonus_formated'] = price_format($total['bonus'], false);

    /* 线下优惠劵 */
     if (!empty($order['bonus_kill']))
    {
        $bonus          = bonus_info(0,$order['bonus_kill']);
        $total['bonus_kill'] = $order['bonus_kill'];
        $total['bonus_kill_formated'] = price_format($total['bonus_kill'], false);
    }



    /* 配送费用 */
    //0元购的邮费
    if($goods[0]['is_zero'] == 1 )
    {
        $total['shipping_fee'] = $goods[0]['shipping_fee'];
        $total['shipping_insure'] = 0;
    }
    elseif($_SESSION['is_luck'] == 1 )
    {
        $total['shipping_fee'] = 0.00;
        $total['shipping_insure'] = 0;
    }
    else
    {
        $shipping_cod_fee = NULL;

        if ($order['shipping_id'] > 0 && $total['real_goods_count'] > 0)
        {
            // 邮费模板附带 express_id
            if(! empty($_SESSION['express_id']))
            {
                $express_id = $_SESSION['express_id'];

                $express = $db->getRow('SELECT * FROM '.$hhs->table('goods_express').' WHERE id=' . $express_id);
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
            else{
                $region['country']  = $consignee['country'];
                $region['province'] = $consignee['province'];
                $region['city']     = $consignee['city'];
                $region['district'] = $consignee['district'];
                $shipping_info = shipping_area_info($order['shipping_id'], $region);
            }

            if (!empty($shipping_info))
            {
                if ($order['extension_code'] == 'group_buy')
                {
                    $weight_price = app_cart_weight_price(CART_GROUP_BUY_GOODS);
                }
                elseif($order['extension_code'] == 'team_goods'){
                    $weight_price = app_cart_weight_price(5);
                }
                else
                {
                    $weight_price = app_cart_weight_price();
                }

                // 查看购物车中是否全为免运费商品，若是则把运费赋为零
                $sql = 'SELECT count(*) FROM ' . $GLOBALS['hhs']->table('cart') . " WHERE  `user_id` = '" . $user_id. "' AND `extension_code` != 'package_buy' AND `is_shipping` = 0";
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
    }


    $total['shipping_fee_formated']    = price_format($total['shipping_fee'], false);
    $total['shipping_insure_formated'] = price_format($total['shipping_insure'], false);

    // 购物车中的商品能享受优惠劵支付的总额
    $bonus_amount = compute_discount_amount();
    // 优惠劵和积分最多能支付的金额为商品总额
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

        // 减去优惠劵金额
        $use_bonus        = min($total['bonus'], $max_amount); // 实际减去的优惠劵金额
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

    /* 保存订单信息 */
    $_SESSION['flow_order'] = $order;

    $se_flow_type = isset($_SESSION['flow_type']) ? $_SESSION['flow_type'] : '';
    
    /* 支付费用 */
    if (!empty($order['pay_id']) && ($total['real_goods_count'] > 0 || $se_flow_type != CART_EXCHANGE_GOODS))
    {
        $total['pay_fee']      = pay_fee($order['pay_id'], $total['amount'], $shipping_cod_fee);
    }

    $total['pay_fee_formated'] = price_format($total['pay_fee'], false);

    $total['amount']           += $total['pay_fee']; // 订单总额累加上支付费用
    $total['amount_formated']  = price_format($total['amount'], false);

    /* 取得可以得到的积分和优惠劵 */
    if ($order['extension_code'] == 'group_buy')
    {
        $total['will_get_integral'] = $group_buy['gift_integral'];
    }
    elseif ($order['extension_code'] == 'exchange_goods')
    {
        $total['will_get_integral'] = 0;
    }
    else
    {
        $total['will_get_integral'] = get_give_integral($goods);
    }
    $total['will_get_bonus']        = $order['extension_code'] == 'exchange_goods' ? 0 : price_format(get_total_bonus(), false);
    $total['formated_goods_price']  = price_format($total['goods_price'], false);
    $total['formated_market_price'] = price_format($total['market_price'], false);
    $total['formated_saving']       = price_format($total['saving'], false);

    if ($order['extension_code'] == 'exchange_goods')
    {
        $sql = 'SELECT SUM(eg.exchange_integral) '.
               'FROM ' . $GLOBALS['hhs']->table('cart') . ' AS c,' . $GLOBALS['hhs']->table('exchange_goods') . 'AS eg '.
               "WHERE c.goods_id = eg.goods_id AND c.session_id= '" . SESS_ID . "' " .
               "  AND c.rec_type = '" . CART_EXCHANGE_GOODS . "' " .
               '  AND c.is_gift = 0 AND c.goods_id > 0 ' .
               'GROUP BY eg.goods_id';
        $exchange_integral = $GLOBALS['db']->getOne($sql);
        $total['exchange_integral'] = $exchange_integral;
    }

    return $total;
}


/**
 * 获得购物车中商品的总重量、总价格、总数量
 *
 * @access  public
 * @param   int     $type   类型：默认普通商品
 * @return  array
 */
function app_cart_weight_price($type = CART_GENERAL_GOODS)
{
 	global $user_id;
    $package_row['weight'] = 0;
    $package_row['amount'] = 0;
    $package_row['number'] = 0;

    $packages_row['free_shipping'] = 1;

    /* 计算超值礼包内商品的相关配送参数 */
    $sql = 'SELECT goods_id, goods_number, goods_price FROM ' . $GLOBALS['hhs']->table('cart') . " WHERE extension_code = 'package_buy' AND user_id = '" . $user_id . "'";
    $row = $GLOBALS['db']->getAll($sql);

    if ($row)
    {
        $packages_row['free_shipping'] = 0;
        $free_shipping_count = 0;

        foreach ($row as $val)
        {
            // 如果商品全为免运费商品，设置一个标识变量
            $sql = 'SELECT count(*) FROM ' .
                    $GLOBALS['hhs']->table('package_goods') . ' AS pg, ' .
                    $GLOBALS['hhs']->table('goods') . ' AS g ' .
                    "WHERE g.goods_id = pg.goods_id AND g.is_shipping = 0 AND pg.package_id = '"  . $val['goods_id'] . "'";
            $shipping_count = $GLOBALS['db']->getOne($sql);

            if ($shipping_count > 0)
            {
                // 循环计算每个超值礼包商品的重量和数量，注意一个礼包中可能包换若干个同一商品
                $sql = 'SELECT SUM(g.goods_weight * pg.goods_number) AS weight, ' .
                    'SUM(pg.goods_number) AS number FROM ' .
                    $GLOBALS['hhs']->table('package_goods') . ' AS pg, ' .
                    $GLOBALS['hhs']->table('goods') . ' AS g ' .
                    "WHERE g.goods_id = pg.goods_id AND g.is_shipping = 0 AND pg.package_id = '"  . $val['goods_id'] . "'";

                $goods_row = $GLOBALS['db']->getRow($sql);
                $package_row['weight'] += floatval($goods_row['weight']) * $val['goods_number'];
                $package_row['amount'] += floatval($val['goods_price']) * $val['goods_number'];
                $package_row['number'] += intval($goods_row['number']) * $val['goods_number'];
            }
            else
            {
                $free_shipping_count++;
            }
        }

        $packages_row['free_shipping'] = $free_shipping_count == count($row) ? 1 : 0;
    }

    /* 获得购物车中非超值礼包商品的总重量 */
    $sql    = 'SELECT SUM(g.goods_weight * c.goods_number) AS weight, ' .
                    'SUM(c.goods_price * c.goods_number) AS amount, ' .
                    'SUM(c.goods_number) AS number '.
                'FROM ' . $GLOBALS['hhs']->table('cart') . ' AS c '.
                'LEFT JOIN ' . $GLOBALS['hhs']->table('goods') . ' AS g ON g.goods_id = c.goods_id '.
                "WHERE c.user_id = '" . $user_id . "' " .
                "AND rec_type = '$type' AND g.is_shipping = 0 AND c.extension_code != 'package_buy'";
    $row = $GLOBALS['db']->getRow($sql);

    $packages_row['weight'] = floatval($row['weight']) + $package_row['weight'];
    $packages_row['amount'] = floatval($row['amount']) + $package_row['amount'];
    $packages_row['number'] = intval($row['number']) + $package_row['number'];
    /* 格式化重量 */
    $packages_row['formated_weight'] = formated_weight($packages_row['weight']);

    return $packages_row;
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
?>