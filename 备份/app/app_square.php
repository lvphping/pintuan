<?php
define('IN_HHS', true);

$list = get_goodslist();
$results['content'] = $list;
echo $json->encode($results);
die();




function get_goodslist()
{
	global $hhs,$db;
	global $redirect_uri; 
    $keywords = isset($_REQUEST['keywords']) ? trim($_REQUEST['keywords']) : '';
    $orderby = isset($_REQUEST['orderby']) ? trim($_REQUEST['orderby']) : '';

    if($orderby == 'hot'){
        $orderby = 'need asc';
    }
    else{
        $orderby = 'order_id desc';
    }
    $where = " AND o.`square`<> '' ";
    if (!empty($keywords))
    {
        $where = " AND g.goods_name LIKE '%" . mysql_like_quote($keywords) . "%'";
        $sql = "select o.square,o.order_id,o.team_sign,o.team_num,o.teammen_num,(team_num - teammen_num) as need, o.add_time,u.uname,u.headimgurl from ".$hhs->table('order_info')." as o,".$hhs->table('order_goods')." as g,".$hhs->table('users')." as u where o.show_square = 1 and o.user_id = u.user_id and o.team_status = 1 AND o.order_status = 1 AND g.`order_id` = o.`order_id` ".$where." order by " . $orderby;
    }
    else{
        $sql = "select o.square,o.order_id,o.team_sign,o.team_num,o.teammen_num,(team_num - teammen_num) as need, o.add_time,u.uname,u.headimgurl from ".$hhs->table('order_info')." as o,".$hhs->table('users')." as u where o.show_square = 1 and o.user_id = u.user_id and o.team_status = 1 AND o.order_status = 1 ".$where." order by " . $orderby;
    }

    $res = $GLOBALS['db']->getAll($sql);

    $arr = array();
    foreach ($res AS $idx => $row)
    {
        $sql = "select g.goods_name,g.goods_id, g.goods_number, g.goods_thumb,g.little_img,g.goods_img, g.market_price, g.shop_price,g.team_price  from ".$hhs->table('order_goods')." as o,".$hhs->table('goods')." as g where g.`goods_id` = o.`goods_id` and o.`order_id` = '".$row['order_id']."'";
        $goods = $db->getRow($sql);

        $arr[$idx]['goods_id']   = $goods['goods_id'];
		$arr[$idx]['goods_name']   = $goods['goods_name'];
		$arr[$idx]['goods_number'] = $goods['goods_number'];
		$arr[$idx]['market_price'] = app_price_format($goods['market_price'],false);
		$arr[$idx]['shop_price']   = app_price_format($goods['shop_price'],false);
		
        $arr[$idx]['goods_thumb'] = $redirect_uri.get_image_path($goods['goods_id'], $goods['goods_thumb'], true);
        $arr[$idx]['little_img']  = $redirect_uri.get_image_path($goods['goods_id'], $goods['little_img'], true);
        $arr[$idx]['goods_img']   = $redirect_uri.get_image_path($goods['goods_id'], $goods['goods_img']);
        $arr[$idx]['url']         = build_uri('goods', array('gid'=>$goods['goods_id']), $goods['goods_name']);
        $arr[$idx]['team_price']  = app_price_format($goods['team_price'],false);
        $arr[$idx]['team_num']    = $row['team_num'];
        $arr[$idx]['need']    = $row['team_num'] - $row['teammen_num'];
        $arr[$idx]['square']    = $row['square'];
        $arr[$idx]['team_id']    = $row['team_sign'];
        $arr[$idx]['uname']       = $row['uname'];
        $arr[$idx]['headimgurl']  = $row['headimgurl'];
        $arr[$idx]['add_time']    = local_date("Y-m-d H:i:s",$row['add_time']);
        
        $arr[$idx]['team_discount']    = @number_format($goods['team_price']/$goods['market_price']*10,1);

        $arr[$idx]['buy_nums']    = $db->getOne("select count(*) from ".$hhs->table('order_goods')." where goods_id = '".$goods['goods_id']."'");


        $arr[$idx]['gallery']   = $db->getAll("select thumb_url from ".$hhs->table('goods_gallery')." where goods_id = '".$goods['goods_id']."' limit 3");
  
    }

    return $arr;
}


