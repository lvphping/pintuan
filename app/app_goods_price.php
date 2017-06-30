<?php
define('IN_HHS', true);

$goods_id = isset($_REQUEST['id'])  ? intval($_REQUEST['id']) : 0;
if($action =='goods_price')//根据数量改变价格
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