<?php
define('IN_HHS', true);

$goods_id = isset($_REQUEST['id'])  ? intval($_REQUEST['id']) : 0;

$properties = get_goods_properties($goods_id);  // 获得商品的规格和属性
$properties_spe =  $properties['spe'];



$i =0;
foreach($properties_spe as $idx=>$v)
{
	$v['id']= $idx;
	$properties_spe_array[] = $v;

	
}

if($properties_spe_array)
{
	$result['error'] =0;
	$result['content'] = $properties_spe_array;

}
else
{
	$result['error'] =1;
	
}

$result['content'] = $properties_spe_array;
echo $json->encode($result);
	exit();
?>