<?php
define('IN_HHS', true);

$list = get_sitelists();


$results['content'] = $list;


echo $json->encode($results);
exit();
?>