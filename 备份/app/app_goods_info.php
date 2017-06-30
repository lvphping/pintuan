<?php
define('IN_HHS', true);

$goods_id = isset($_REQUEST['id'])  ? intval($_REQUEST['id']) : 0;

$user_id = $_REQUEST['user_id'];

$goods = get_goods_info($goods_id);

$goods['goods_thumb'] = $redirect_uri.$goods['goods_thumb'];
$goods['goods_img'] = $redirect_uri.$goods['goods_img'];
$goods['little_img'] = $redirect_uri.$goods['little_img'];
$goods['goods_desc'] = img_url_replace($goods['goods_desc']);
$store_id = $goods['suppliers_id'];

$goods['buy_sum']= get_buy_sum($goods_id)+$goods['sales_num'];


if(empty($user_id)){
	$goods['collect']        = 0;
}else{
	$sql = "select rec_id,user_id from" . $GLOBALS['hhs']->table('collect_goods')." where user_id=" .$user_id ." and goods_id='$goods_id'";
	$collectInfo = $GLOBALS['db']->getRow($sql);
	$goods['collect']   = empty($collectInfo)?"0":"1";
}		





$info['info'] = $goods;

if ($goods['suppliers_id']){
    $stores_info = get_suppliers_info($goods['suppliers_id']);
	
 	$sql = "SELECT count(*) FROM ".$hhs->table('goods')." as g WHERE is_on_sale = 1 AND is_alone_sale = 1 AND is_delete = 0 and  `suppliers_id` = " . $goods['suppliers_id'].$where;
    $stores_info['goods_num'] = $db->getOne($sql);	
	
	$sql = "SELECT sum(`sales_num`) FROM ".$hhs->table('goods')." WHERE `suppliers_id` = " .$goods['suppliers_id'];
	$sales_num = $db->getOne($sql);
	$sql = "SELECT count(*) FROM  ".$hhs->table('order_goods')." as o,".$hhs->table('goods')." as g WHERE g.`goods_id` = o.`goods_id` and g.`suppliers_id` = " .$goods['suppliers_id'];
	$sales_num += $db->getOne($sql);
	$stores_info['sales_num'] = $sales_num;
	
    $info['stores_info'] = $stores_info;
}
else
{
	$info['stores_info'] ='';
}
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


$comments = assign_comment_app($goods_id,0,1);
$info['comments'] = $comments;

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
$info['rands_goods'] = $rands_goods;


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
$info['group_list'] = $group_list;
$results['info'] = $info;
	echo $json->encode($results);
	exit();


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

