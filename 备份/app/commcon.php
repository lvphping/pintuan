<?php

//检查用户是否登录
function check_login($user_id){
	return $GLOBALS['db']->getOne("select `user_id` from ".$GLOBALS['hhs']->table('users')." where `user_id` = '$user_id' limit 1");
}
//verify
function get_verify(){
	//$code = $GLOBALS['db']->getOne("SELECT `value` FROM " . $GLOBALS['hhs']->table('shop_config') . " WHERE `code`='verify' limit 1");
	$code ='123456';
	return md5($code);
}
function get_verify_ry($verify){
	$code = $GLOBALS['db']->getOne("SELECT count(*) FROM " . $GLOBALS['hhs']->table('suppliers') . " WHERE `parms`='".$verify."' limit 1");
	return $code>0?true:false;
}
//检查verify
function check_verify($verify){
	$code = get_verify();
	return $verify == $code ? true : false;
}


function img_url_replace($content){
global $redirect_uri;
	//提取图片路径的src的正则表达式
	preg_match_all("/<img(.*)src=\"([^\"]+)\"[^>]*>/isU",$content,$matches);

	$img = "";
	if(!empty($matches)) {
		//注意，上面的正则表达式说明src的值是放在数组的第三个中
		$img = $matches[2];
	}else {
		$img = "";
	}
	if (!empty($img)) {
		$img_url = $redirect_uri;
			
		$patterns= array();
		$replacements = array();
			
		foreach($img as $imgItem){
			if(strpos($imgItem, 'http://')===false ){
				$final_imgUrl = $img_url.$imgItem;
				$replacements[] = $final_imgUrl;
					
				$img_new = "/".preg_replace("/\//i","\/",$imgItem)."/";
				$patterns[] = $img_new;
			}
		}
		//让数组按照key来排序
		ksort($patterns);
		ksort($replacements);
		//替换内容
		$vote_content = preg_replace($patterns, $replacements, $content);
		return  $vote_content;
	} else{
		return $content;
	}
}
/**
 * 格式化商品价格
 *
 * @access  public
 * @param   float   $price  商品价格
 * @return  string
 */
function app_price_format($price, $change_price = true)
{
    if($price==='')
    {
     $price=0;
    }
    if ($change_price && defined('HHS_ADMIN') === false)
    {
        switch ($GLOBALS['_CFG']['app_price_format'])
        {
            case 0:
                $price = number_format($price, 2, '.', '');
                break;
            case 1: // 保留不为 0 的尾数
                $price = preg_replace('/(.*)(\\.)([0-9]*?)0+$/', '\1\2\3', number_format($price, 2, '.', ''));

                if (substr($price, -1) == '.')
                {
                    $price = substr($price, 0, -1);
                }
                break;
            case 2: // 不四舍五入，保留1位
                $price = substr(number_format($price, 2, '.', ''), 0, -1);
                break;
            case 3: // 直接取整
                $price = intval($price);
                break;
            case 4: // 四舍五入，保留 1 位
                $price = number_format($price, 1, '.', '');
                break;
            case 5: // 先四舍五入，不保留小数
                $price = round($price);
                break;
        }
    }
    else
    {
        $price = number_format($price, 2, '.', '');
    }

    return sprintf('¥%s', $price);
}


/**

 * 查询评论内容

 *

 * @access  public

 * @params  integer     $id

 * @params  integer     $type

 * @params  integer     $page

 * @return  array

 */

function assign_comment_app($id, $type, $page = 1)

{
    /* 取得评论列表 */

    $count = $GLOBALS['db']->getOne('SELECT COUNT(*) FROM ' .$GLOBALS['hhs']->table('comment').

           " WHERE id_value = '$id' AND comment_type = '$type' AND status = 1 AND parent_id = 0");

    $size  = !empty($GLOBALS['_CFG']['comments_number']) ? $GLOBALS['_CFG']['comments_number'] : 5;



    $page_count = ($count > 0) ? intval(ceil($count / $size)) : 1;



    $sql = 'SELECT * FROM ' . $GLOBALS['hhs']->table('comment') .

            " WHERE id_value = '$id' AND comment_type = '$type' AND status = 1 AND parent_id = 0".

            ' ORDER BY comment_id DESC';

    $res = $GLOBALS['db']->query($sql);



    $arr = array();

    $ids = '';
	
	$id=0;

    while ($row = $GLOBALS['db']->fetchRow($res))

    {

        $ids .= $ids ? ",$row[comment_id]" : $row['comment_id'];

        $arr[$id]['id']       = $row['comment_id'];

        $arr[$id]['email']    = $row['email'];

        $arr[$id]['username'] = $row['user_name'];

        $arr[$id]['content']  = str_replace('\r\n', '<br />', htmlspecialchars($row['content']));

       // $arr[$id]['content']  = nl2br(str_replace('\n', '<br />', $arr[$row['comment_id']]['content']));

        $arr[$id]['rank']     = $row['comment_rank'];

        $arr[$id]['add_time'] = local_date($GLOBALS['_CFG']['time_format'], $row['add_time']);
		$arr[$id]['headimgurl'] = $GLOBALS['db']->getOne('SELECT headimgurl FROM ' .$GLOBALS['hhs']->table('users').
           " WHERE user_id = '".$row['user_id']."'");

		$id++;
    }

    /* 取得已有回复的评论 */

    if ($ids)

    {

        $sql = 'SELECT * FROM ' . $GLOBALS['hhs']->table('comment') .

                " WHERE parent_id IN( $ids )";

        $res = $GLOBALS['db']->query($sql);
		$s =0;

        while ($row = $GLOBALS['db']->fetch_array($res))

        {

            $arr[$s]['re_content']  = nl2br(str_replace('\n', '<br />', htmlspecialchars($row['content'])));

            $arr[$s]['re_add_time'] = local_date($GLOBALS['_CFG']['time_format'], $row['add_time']);

            $arr[$s]['re_email']    = $row['email'];

            $arr[$s]['re_username'] = $row['user_name'];
			$s++;

        }

    }

    /* 分页样式 */

    //$pager['styleid'] = isset($GLOBALS['_CFG']['page_style'])? intval($GLOBALS['_CFG']['page_style']) : 0;

    $pager['page']         = $page;

    $pager['size']         = $size;

    $pager['record_count'] = $count;

    $pager['page_count']   = $page_count;

    $pager['page_first']   = "javascript:gotoPage(1,$id,$type)";

    $pager['page_prev']    = $page > 1 ? "javascript:gotoPage(" .($page-1). ",$id,$type)" : 'javascript:;';

    $pager['page_next']    = $page < $page_count ? 'javascript:gotoPage(' .($page + 1) . ",$id,$type)" : 'javascript:;';

    $pager['page_last']    = $page < $page_count ? 'javascript:gotoPage(' .$page_count. ",$id,$type)"  : 'javascript:;';



    $cmt = array('comments' => $arr, 'pager' => $pager);
    return $arr;

}

function app_get_user_orders_ex($user_id, $num = 10, $start = 0,$ext=null)
{
    global $_CFG;
	
	$notExpress = $GLOBALS['db']->getOne("SELECT `shipping_id` from ".$GLOBALS['hhs']->table('shipping')." where `shipping_code` = 'cac'");
	
	
	
	
    /*判断组团的状态*/
    $sql="select * from ".$GLOBALS['hhs']->table('order_info') ." where user_id='$user_id' limit ".$start.",".$num;
    $orders=$GLOBALS['db']->getAll($sql);
    if(!empty($orders)){
        foreach($orders as $v){
            if($v['extension_code']=='team_goods'&&$v['team_status']==1 && $v['is_luck'] == 0 && $v['is_miao'] == 0){
                $sql="select pay_time from ".$GLOBALS['hhs']->table('order_info')." where order_id=".$v['team_sign'];
                $pay_time=$GLOBALS['db']->getOne($sql);
                if($pay_time && ($pay_time+$_CFG['team_suc_time']*24*3600)<gmtime()){
                    do_team_refund($v['team_sign']);
                    //取消订单
                    // $sql="update ".$GLOBALS['hhs']->table('order_info')." set team_status=3,order_status=2 where team_status=1 and team_sign=".$v['team_sign'];
                    // $GLOBALS['db']->query($sql);
                    // $sql = "UPDATE ". $GLOBALS['hhs']->table('order_info') ." SET order_status=2 WHERE team_status=0 and team_sign=".$v['team_sign'];
                    // $GLOBALS['db']->query($sql);
                }
            }
        }
    }
    include_once(ROOT_PATH . 'includes/lib_order.php');
    /* 取得订单列表 */
    $arr    = array();

    $sql = "SELECT square,is_comm,is_luck,luck_times,is_lucker,shipping_fee,order_id,share_pay_type, order_sn, order_status,integral, shipping_status, pay_status,pay_id, add_time,order_amount, shipping_name,shipping_id,invoice_no, " .
           "(goods_amount + shipping_fee + insure_fee + pay_fee + pack_fee + card_fee + tax - discount) AS total_fee,extension_code,extension_id,team_sign,team_first,team_status,suppliers_id ".
           " FROM " .$GLOBALS['hhs']->table('order_info') .
           " WHERE user_id = '$user_id' ".$ext." ORDER BY add_time DESC";
    $res = $GLOBALS['db']->query($sql);
	

    while ($row = $GLOBALS['db']->fetchRow($res))
    {
		
		
		
        if ($row['order_status'] == OS_UNCONFIRMED)
        {
            $row['handler'] =$GLOBALS['_LANG']['cancel'];
        }
        else if ($row['order_status'] == OS_SPLITED)
        {
            /* 对配送状态的处理 */
            if ($row['shipping_status'] == SS_SHIPPED)
            {
                @$row['handler'] = "查看物流";
				
                @$row['handler'] .= $GLOBALS['_LANG']['received'];
            }
            elseif ($row['shipping_status'] == SS_RECEIVED)
            {
                //@$row['handler'] = '<span style="color:red">'.$GLOBALS['_LANG']['ss_received'] .'</span>';
            }
            else
            {
                if ($row['pay_status'] == PS_UNPAYED)
                {
                    //@$row['handler'] = "<a href=\"user.php?act=order_detail&order_id=" .$row['order_id']. '">' .$GLOBALS['_LANG']['pay_money']. '</a>';
                }
                else
                {
				
                    //@$row['handler'] = "<a href=\"user.php?act=order_detail&order_id=" .$row['order_id']. '">' .$GLOBALS['_LANG']['view_order']. '</a>';
                }

            }
        }
        else
        {
            //$row['handler'] = '<span style="color:red">'.$GLOBALS['_LANG']['os'][$row['order_status']] .'</span>';
            if($row['team_sign'])
            {
                if($row['team_status'] ==1){
                    $row['handler'] = '团购进行中';
                }
                elseif($row['team_status'] ==2)
                {
                    $row['handler'] = '组团成功';
                    if($row['shipping_id'] == $notExpress){
                        if($row['shipping_status'] <1 && $row['pay_status'] == 2)
                        $row['handler'] .= '核销';
                    }
                    else{
                        if ($row['shipping_status'] == SS_SHIPPED)
                        {
                            $row['handler'] .= "查看物流";
                        }
                    }             
                }
                elseif($row['team_status'] ==3){
                    if($row['pay_status']>2)
                    $row['handler'] = '组团失败已退款';
                    else
                    $row['handler'] = '组团失败待退款';
                }
                elseif($row['team_status'] ==4){
                    $row['handler'] = '组团失败已退款';
                }            
            }
            elseif($row['shipping_id'] == $notExpress){
                if ($row['pay_status'] == 2 && $row['shipping_status'] != 2)
                $row['handler'] = '核销';
            }

        }

        if($row['shipping_status'] == 2)
        {
            // $sql = "select c.id_value from ".$GLOBALS['hhs']->table('order_info')." as o,".$GLOBALS['hhs']->table('order_goods')." as g,".$GLOBALS['hhs']->table('comment')." as c where o.order_id = '".$row['order_id']."' and g.`order_id` = o.`order_id` and g.`goods_id` = c.`id_value`";

            // $comment = $GLOBALS['db']->getOne($sql);
            if($row['is_comm'] == 0)
            {
                $sql = "select g.goods_id from ".$GLOBALS['hhs']->table('order_info')." as o,".$GLOBALS['hhs']->table('order_goods')." as g where o.order_id = '".$row['order_id']."' and g.`order_id` = o.`order_id`";
            $goods_id = $GLOBALS['db']->getOne($sql);

            $row['handler'] .= '去评价';
            }
        }

        $pay_online='';
        if($row['share_pay_type']>0&&$row['pay_status'] == PS_UNPAYED){
            $pay_online="代付";
        }
        if ($row['share_pay_type']==0&&$row['pay_status'] == PS_UNPAYED &&($row['order_status'] == OS_UNCONFIRMED || $row['order_status'] == OS_CONFIRMED))
        {
            
            $payment_info = array();
            $payment_info = payment_info($row['pay_id']);
            //无效支付方式
            if ($payment_info === false)
            {
                $row['pay_online'] = '';
            }
            else
            {
                //取得支付信息，生成支付代码
                $payment = unserialize_config($payment_info['pay_config']);
                //获取需要支付的log_id
                $row['log_id']    = get_paylog_id($row['order_id'], $pay_type = PAY_ORDER);
                $row['user_name'] = $_SESSION['user_name'];
                $row['pay_desc']  = $payment_info['pay_desc'];
                if($row['extension_id']){
                    $sql="select goods_name from ".$GLOBALS['hhs']->table('goods')." where goods_id=".$row['extension_id'];
                    $row['goods_name']=$GLOBALS['db']->getOne($sql);
                }
                /* 取得在线支付方式的支付按钮 */
                if($row['order_amount']>0){
					
					$row['pay_online'] = $payment_info['pay_code'];
                   // if($payment_info['pay_code']!='alipay'){
                        /* 调用相应的支付方式文件 */
                       // include_once(ROOT_PATH . 'includes/modules/payment/' . $payment_info['pay_code'] . '.php');
                       // $pay_obj    = new $payment_info['pay_code'];
                      //  $pay_online = $pay_obj->get_code($row, $payment);
                    
                   // }else{
                       // $pay_online ='<a class="state_btn_2" href="toalipay.php?order_id='.$row['order_id'].'"   >支付宝支付</a>';
                   //}
                }
                
            }
        }
        
        $row['handler']=$pay_online.$row['handler'];
        $row['shipping_status'] = ($row['shipping_status'] == SS_SHIPPED_ING) ? SS_PREPARING : $row['shipping_status'];
        
        /*
        if($row['order_status']==2){
            $row['order_status'] =  $GLOBALS['_LANG']['os'][$row['order_status']] ;
        }else{
            if($row['pay_status']==0){
                $row['order_status'] = $GLOBALS['_LANG']['ps'][$row['pay_status']];
            }else{
                $row['order_status'] =  $GLOBALS['_LANG']['ss'][$row['shipping_status']];// $GLOBALS['_LANG']['ps'][$row['pay_status']] . ',' .
            } 
        }*/
        $row['order_status'] = $GLOBALS['_LANG']['os'][$row['order_status']] . ',' . $GLOBALS['_LANG']['ps'][$row['pay_status']] . ',' . $GLOBALS['_LANG']['ss'][$row['shipping_status']];
        //$GLOBALS['_LANG']['os'][$row['order_status']]. ',' . 
        $row['goods_list'] = get_order_goods_list($row['order_id']);
        $row['can_refund'] = can_refund($row['order_id']);

        $suppliers = $row['suppliers_id']?
            $GLOBALS['db']->getRow('select suppliers_name,supp_logo FROM '.$GLOBALS['hhs']->table('suppliers').' WHERE suppliers_id=' . $row['suppliers_id'])
        :array();


        $row['square'] = $row['team_status'] == 1 ? (empty($row['square']) ?1:0 ):0;

        $arr[] = array( 'order_id'       => $row['order_id'],
                        'order_sn'       => $row['order_sn'],
						'integral'       => $row['integral'],
						'invoice_no'       => $row['invoice_no'],
						'shipping_name'       => $row['shipping_name'],
						'pay_status'       => $row['pay_status'],
						'pay_online'       => $row['pay_online'],
						
						 
						
						
						
                        'is_lucker'      => $row['is_lucker'],
                        'luck_times'     => $row['luck_times'],
                        'lucker_num'     => $row['team_status'] ==2 ? getLuckerNum($row['team_sign']) : 0,
                        'open_times'     => $row['is_luck'] && $row['team_status'] ==2 ? getOpenTime($row['team_sign']) : '',
                        'team_status'    => $row['team_status'],
                        'goods_list'     => $row['goods_list'],
                        'goods_num'      => count($row['goods_list']),
                        'order_time'     => local_date($GLOBALS['_CFG']['time_format'], $row['add_time']),
                        'order_status'   => $row['order_status'],
                        'total_fee'      => price_format($row['total_fee'], false),
                        'order_amount'   => price_format($row['order_amount'], false),
                        'can_refund'     => $row['can_refund'],
                        'shipping_fee'   => price_format($row['shipping_fee'], false),
                        'handler'        => $row['handler'],
                        'suppliers_name' => $suppliers['suppliers_name'],
                        'square' => $row['square'],
                        'supp_logo'      => $suppliers['supp_logo'],
                       );
	//	$arr[] = $row;
    }
    return $arr;
}
function get_str_replace_region_name($type,$name)
{
	
        if ($type == 1) {
            $region_name = str_replace(array('省', '市', '自治区', '回族', '地区'), '', $name);
		
            $sql = 'SELECT `region_id` from ' . $GLOBALS['hhs']->table('region') . " where `region_name` = '{$region_name}' and region_type ='{$type}'";
            return $GLOBALS['db']->getOne($sql);
        } elseif ($type == 2) {
            $region_name = str_replace(array('省', '市', '自治区', '回族', '地区'), '', $name);
            $sql = 'SELECT `region_id` from ' . $GLOBALS['hhs']->table('region') . " where `region_name` = '{$region_name}'";
            return $GLOBALS['db']->getOne($sql);
        }
		else
		{
            $sql = 'SELECT `region_id` from ' . $GLOBALS['hhs']->table('region') . " where `region_name` = '{$name}'";
            return $GLOBALS['db']->getOne($sql);
		}
	
}
function alipay_change_wfx_paystatus($resultstatus,$order_sn,$attach='')
{
	 global $db,$hhs;
	if($resultstatus!=''&&$resultstatus=='9000')
	{
		
		if(isset($attach) && !empty($attach))		
		{
			$attach = explode(',', $attach);
			
			foreach ($attach as $order_id)
			 {
				$sql="select order_sn from ".$hhs->table('order_info')." where order_id='$order_id' ";
				$orsn =  $db->getOne($sql);
				$odid=get_order_id_by_sn($orsn); 
				//确保只发一次
				$sql="select pay_status from ".$hhs->table('order_info')." where order_sn='$orsn' ";
				$pay_status=$db->getOne($sql);
				order_paid($odid);
				$sql="update ".$GLOBALS['hhs']->table('order_info')." set transaction_id='$transaction_id',order_status=1,pay_status=2,wechat_total_fee='$total_fee' where order_sn='$orsn' ";
				$GLOBALS['db']->query($sql);
				//确保只发一次
				if($pay_status!=2){
					pay_team_action($orsn);
				}
			}
		}
		else
		{
			$orsn = $order_sn;
			$odid=get_order_id_by_sn($orsn); 
			//确保只发一次
			$sql="select pay_status from ".$hhs->table('order_info')." where order_sn='$orsn' ";
			$pay_status=$db->getOne($sql);
			order_paid($odid);
			$sql="update ".$GLOBALS['hhs']->table('order_info')." set transaction_id='$transaction_id',order_status=1,pay_status=2,wechat_total_fee='$total_fee' where order_sn='$orsn' ";
			$GLOBALS['db']->query($sql);
			//确保只发一次
			if($pay_status!=2){
			    pay_team_action($orsn);
			}
		}
		return true;
	}
	else
	{
		
		return false;
	}
}

function app_get_advlist_position_name($position_name,$num,$ad_width)
{
		$redirect_uri="http://" . $_SERVER['HTTP_HOST']."/";
		$arr = array( );
		
		if($ad_width)
		{
			$where = " and ad.ad_width ='$ad_width'";
		}
		else
		{
			$where ='';
		}	

		$sql = "select ap.ad_width,ap.ad_height,ad.ad_id,ad.ad_name,ad.ad_code,ad.media_type,ad.ad_link,ad.ad_id from ".$GLOBALS['hhs']->table( "ad_position" )." as ap left join ".$GLOBALS['hhs']->table( "ad" )." as ad on ad.position_id = ap.position_id where ap.position_name='".$position_name.( "' and ad.enabled=1 $where order by order_sort limit ".$num );

		$res = $GLOBALS['db']->getAll( $sql );

		foreach ( $res as $idx => $row )

		{

				$arr[$row['ad_id']]['name'] = $row['ad_name'];

				$arr[$row['ad_id']]['code'] = $row['ad_code'];

				$arr[$row['ad_id']]['url'] = $row['ad_link'];

				$arr[$row['ad_id']]['image'] = $redirect_uri."/data/afficheimg/".$row['ad_code'];
				

				$arr[$row['ad_id']]['content'] = "<a href='".$arr[$row['ad_id']]['url']."' target='_blank'><img src='data/afficheimg/".$row['ad_code']."' width='".$row['ad_width']."' height='".$row['ad_height']."' /></a>";

				$arr[$row['ad_id']]['width'] = $row['ad_width'];

				$arr[$row['ad_id']]['height'] = $row['ad_height'];

		}

		return $arr;

}
function get_buy_sum($goods_id)
{
    $sql = 'SELECT IFNULL(SUM(g.goods_number), 0) ' .
        'FROM ' . $GLOBALS['hhs']->table('order_info') . ' AS o, ' .
            $GLOBALS['hhs']->table('order_goods') . ' AS g ' .
        "WHERE o.order_id = g.order_id " .
        "AND o.order_status  in (0,1,5)  ".
        " AND o.shipping_status in (0,1,2) "  .
        " AND o.pay_status in (1,2) ".
        " AND g.goods_id = ".$goods_id;
    
    return $GLOBALS['db']->getOne($sql);
}
//function get_regions_name($region_id)
//{
//    return $GLOBALS['db']->getOne("select region_name from ".$GLOBALS['hhs']->table('region')." where region_id='$region_id'");
//}
?>