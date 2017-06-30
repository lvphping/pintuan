<?php
define('IN_HHS', true);

if($_REQUEST['act'] =='hangye')
{
	    $hangye = $db->getAll("select id,name,pid from " . $hhs->table('hangye')." where pid = 0");
	    foreach ($hangye as $key => $item) {
	    	$hangye[$key]['children'] = $db->getAll("select id,name from " . $hhs->table('hangye')." where pid = " . $item['id']);
	    	unset($item);
	    }
		$results['content'] = $hangye;
		$results['error'] =0;
		echo $json->encode($results);
		exit();
	
}
elseif($_REQUEST['act'] =='enter_act')
{
	$data =$_REQUEST;
	
	$data['city_id'] = get_str_replace_region_name(2,$data['city']);
	$data['province_id'] = get_str_replace_region_name(1,$data['province']);
	$data['district_id'] = get_str_replace_region_name(3,$data['district']);
	$data['add_time'] = gmtime();
	$c = $db->getOne("select count(*) from ".$hhs->table('suppliers')." where suppliers_name='$data[suppliers_name]'");
	if($c)
	{
		$results['content'] = '该商家名称已被占用';
		$results['error'] =1;
		echo $json->encode($results);
		exit();

	}
	
	$is_reg = $db->getOne("select count(*) from ".$hhs->table('suppliers')." where user_name='$data[user_name]'");
	if($is_reg)
	{
		$results['content'] = '该登录用户名已被占用';
		$results['error'] =1;
		echo $json->encode($results);
		exit();

	}
	//$data['business_license'] = $image->upload_image($_FILES['business_license'],'business_file');
	//$data['business_scope'] = $image->upload_image($_FILES['business_scope'],'business_file');
	//$data['cards'] = $image->upload_image($_FILES['cards'],'business_file');
	//$data['certificate'] = $image->upload_image($_FILES['certificate'],'business_file');
	$data['is_check'] =0;
	$data['password'] =md5($data['password']);
	//$data['user_id'] =$data['user_id'];
	$openid = $db->getOne("select openid from ".$hhs->table('users')." where user_id='$data[user_id]'");
	$data['openid'] =$_SESSION['xaphp_sopenid'];
	$db->autoExecute($hhs->table('suppliers'), $data, 'INSERT');
	$suppliers_id = $db->insert_id();
	$dir = 'business/uploads/'.$suppliers_id;
	is_dir($dir) or mkdir($dir, 0777);
	chmod($dir,0777);
	$results['content'] = '入驻成功，请等待工作人员审核!';
	$results['error'] =0;
	echo $json->encode($results);
	exit();
}





?>