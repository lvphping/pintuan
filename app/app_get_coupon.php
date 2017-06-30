<?php
define('IN_HHS', true);


$bid  = isset($_REQUEST['bid']) ? intval($_REQUEST['bid']) : 0;

$user_id  = isset($_REQUEST['user_id']) ? intval($_REQUEST['user_id']) : '';

if($bid ==0)
{
		$result['error']   =  1;
		$result['message'] ='优惠券id不能为空';
		$result['content'] ='';
	
}

elseif(! checkQuan($bid,$user_id)){
	
	if($user_id =='')
	{
		$result['error']   =  1;
		$result['message'] ='请先登录';
		$result['content'] ='';
	}
	else
	{
	
	$res = getQuan($bid,$user_id);
	$result['error']   = 1;
	//$result['message'] = $res ? '' : '领取失败';
	$result['message'] = '领取成功';
	}
}
else
{
	$result['error']   = 1;
	$result['message'] = '您已经领取过了';
	//$result['content'] ='';
	
}
ob_end_clean();
die($json->encode($result));




function checkQuan($bid,$user_id){
	if(!$bid)
		return true;

	global $db,$hhs;
	$sql = "select `bonus_id` from ".$hhs->table('user_bonus')."  where `bonus_type_id` = '" .$bid. "' and `user_id` = '".$user_id."'";
	return $db->getOne($sql);
}

function getQuan($bid,$user_id){
	if(!$bid)
		return false;
	global $db,$hhs;

	$bonus_id = $db->getOne("select `bonus_id` from ".$hhs->table('user_bonus')." where `bonus_type_id` = '" .$bid. "' and `user_id` = 0 ORDER BY RAND() limit 1");
	if (!$bonus_id) {
		return false;
	}
	$sql = "update ".$hhs->table('user_bonus')." set `user_id` = '".$user_id."' where `user_id` = 0 and `bonus_type_id` = '" .$bid. "' and `bonus_id` = '".$bonus_id."'";

	return $db->query($sql);
}

?>