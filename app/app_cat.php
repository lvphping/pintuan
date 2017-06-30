<?php
define('IN_HHS', true);

$list = get_sitelists();
 
$list = $db->getAll("select * from ".$hhs->table('category')." where is_show=1 and parent_id=0 order by sort_order");

$results['content'] = $list;


echo $json->encode($results);
exit();
?>