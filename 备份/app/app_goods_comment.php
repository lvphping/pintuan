<?php
define('IN_HHS', true);
$goods_id = isset($_REQUEST['id'])  ? intval($_REQUEST['id']) : 0;




$comments = assign_comment_app($goods_id,0,1);

foreach($comments as $idx=>$v)
{
	$comments_array[] = $v;	
}
if($comments_array)
{
	$result['error'] =0;
}
else
{
	$result['error'] =1;
}


$result['content'] = $comments_array;
echo $json->encode($result);
	exit();
?>