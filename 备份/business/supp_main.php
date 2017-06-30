<?php
define('IN_HHS', true);
if($action =='default')
{
	$info = $db->getRow("select * from ".$hhs->table('suppliers')." where suppliers_id='$suppliers_id'");
	$goodsnum=$db->getOne("SELECT COUNT(*) FROM " .$hhs->table('goods')." where  is_delete=0 and suppliers_id=".$_SESSION['suppliers_id']);
	$nogoodsnum=$db->getOne("SELECT COUNT(*) FROM " .$hhs->table('goods')." where is_on_sale=0 and is_delete=0 and suppliers_id=".$_SESSION['suppliers_id']);
	$articlenum=$db->getOne("SELECT COUNT(*) FROM " .$hhs->table('article')." where  suppliers_id=".$_SESSION['suppliers_id']);
  
	
	$smarty->assign('info',$info);
	$smarty->assign('articlenum',$articlenum);
	$smarty->assign('goodsnum',$goodsnum);
	$smarty->assign('nogoodsnum',$nogoodsnum);
	$delivery_count = $db->getOne("SELECT COUNT(*) FROM " . $GLOBALS['hhs']->table('delivery_order') . " where suppliers_id='$suppliers_id' and status=2");
	
	$smarty->assign('delivery_count',$delivery_count);
	//已完成结算订单
	$sql="SELECT COUNT(*) FROM " . $GLOBALS['hhs']->table('suppliers_accounts') . " where suppliers_id='$suppliers_id' and settlement_status=7";
	$receive_count = $db->getOne($sql);
	$smarty->assign('receive_count',$receive_count);
	//未完成结算订单
	$sql="SELECT COUNT(*) FROM " . $GLOBALS['hhs']->table('suppliers_accounts') . " where suppliers_id='$suppliers_id' and settlement_status=1";
	$unpay_count = $db->getOne($sql);
	$smarty->assign('unpay_count',$unpay_count);
	
	$smarty->display("supp_main.dwt");	
}