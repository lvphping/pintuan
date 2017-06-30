<?php
define('IN_HHS', true);
$user_id = $_REQUEST['user_id'];
$act = isset($_REQUEST['act']) ? trim($_REQUEST['act']) : 'default';
if($act =='default')
{
	
	$sql="select * from ".$hhs->table('users')." where user_id=".$user_id;
	$user_info=$db->getRow($sql);

	
	

	$send_id = isset($_REQUEST['send_id']) ? trim($_REQUEST['send_id']) : 0;
	if(!empty($send_id)){
		$sql="select * from ".$hhs->table('send_bonus_type')." where send_id=".$send_id;
		$send_bonus_type=$db->getRow($sql);
		if(empty($send_bonus_type)){
			$results['error'] =2;
	   		$results['content'] = 'send_id参数错误';
			echo $json->encode($results);
			die();
		}
		
		if($send_bonus_type['user_id']==$user_id){
			$results['error'] =3;
	   		$results['content'] = '发放优惠券的人打开';
			echo $json->encode($results);
			die();
		}
		$sql="select * from ".$hhs->table('user_bonus')." where send_id=".$send_id." and user_id=0 ";
		$user_bonus=$db->getAll($sql);
		if(empty($user_bonus)){
		    //已经领完
			$results['status'] =1;
			$results['error'] =4;
	   		$results['content'] = '发放优惠券的人打开';
			echo $json->encode($results);
			die();
			
			
			
		}else{
		    //未领完
		    $sql="select * from ".$hhs->table('user_bonus')." where send_id=".$send_id." and user_id= ".$_SESSION['user_id'];
		    $temp=$db->getRow($sql);
		    if(!empty($temp)){
		        //已经领取过一次
				$results['status'] =2;
				$results['error'] =5;
				$results['content'] = '已经领过一次了';
				echo $json->encode($results);
				die();
		    }else{
		        //成功领取
		        $bonus_id=$user_bonus[0]['bonus_id'];
		        $sql="update ".$hhs->table('user_bonus')." set user_id=".$_SESSION['user_id']." where bonus_id=".$bonus_id;
		        $db->query($sql);
		        $sql="select b.type_money,b.use_start_date,b.use_end_date from ".$hhs->table('user_bonus')." as u left join ".$hhs->table('bonus_type')." as b on u.bonus_type_id=b.type_id where u.bonus_id= ".$bonus_id;
		        $row=$db->getRow($sql);
				
				
				
				$results['status'] =3;
				$results['error'] =6;
				$results['bonus_money'] =$row['type_money'];
				$results['use_start_date'] =local_date("Y-m-d",$row['use_start_date']);
				
				$results['use_end_date'] =local_date("Y-m-d",$row['use_end_date']);
				$results['content'] = '领取成功';
				echo $json->encode($results);
				die();
			
		    }
		    
		}
	}
		
	
	
	$order_id = isset($_REQUEST['order_id']) ? trim($_REQUEST['order_id']) : 0;
	if(empty($order_id)){
		$results['error'] =1;
	   	$results['content'] = '订单号不能为空';
		echo $json->encode($results);
		die();
	}
	
	//查询订单的商家id
	$suppliers_id = $GLOBALS['db']->getOne("select 	suppliers_id from ". $GLOBALS['hhs']->table('order_info') ." where order_id='$order_id'");
    $suppliers_id = $suppliers_id>0 ? $suppliers_id : 0;

	$arr=array();
	$bonus_list = order_bonus($order_id, $suppliers_id);

	$bonus_list1=array();
	$bonus_list2=array();
	foreach($bonus_list as $bonus){
		if($bonus['number'] ==0) continue;
		if($bonus['is_share']==0){
		    $bonus['use_start_date']=local_date("Y-m-d", $bonus['use_start_date']);
		    $bonus['use_end_date']=local_date("Y-m-d", $bonus['use_end_date']);
		    $bonus_list1[]=$bonus;
		}elseif($bonus['is_share']==1){//好友券
		    $bonus_list2[]=$bonus;
		}
	}
	
    $sql="select * from ".$hhs->table('send_bonus_type')." where send_order_id=".$order_id;
    $send_bonus=$db->getRow($sql);
    
	$results['bonus_list1'] = $bonus_list1;
	
	$results['send_number'] = $send_bonus['send_number'];

	$results['share_goods_name'] =$_CFG['shop_name']."给您发券了，买商品直接抵现金";
	
	$results['goods_brief'] =$_CFG['shop_name']."给您发券了，买商品直接抵现金";
	
	$results['shop_name'] = $_CFG['shop_title'];
	
	
}
?>