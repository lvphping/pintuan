<?php
define('IN_HHS', true);

if($action =='team_list' )
{
	
	$size = $_REQUEST['size'];
	$site_id =$_REQUEST['site_id']; 
	
	$list = get_apptypeof_goods('team',$size,true,$site_id);
	$results['error'] = 0;
	$results['content']= $list;
}
echo $json->encode($results);
die();
