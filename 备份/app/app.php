<?php
/**
 * 昊海电商 goods_list接口
 * ============================================================================
 * * 版权所有 2012-2014 西安昊海网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.xaphp.cn；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: sxc_shop $
 * $Id: goods.php 15921 2009-05-07 05:35:58Z sxc_shop $
*/

define('IN_HHS', true);
require(dirname(__FILE__) . '/../includes/init2.php');
require(ROOT_PATH . 'includes/cls_json.php');
require(dirname(__FILE__) . '/commcon.php');
$json = new JSON;
$results = array(
    'error' => 1,
    'message' => '',
    'content' => ''
    );
	
$verify = trim($_REQUEST['verify']);
if (!check_verify($verify)&&$_REQUEST['op']!='goods_desc') {
    $results['message'] = '非法提交！';
    echo $json->encode($results);
    die();
}
$redirect_uri="http://" . $_SERVER['HTTP_HOST']."/";
$op= $_REQUEST['op'];
$action = $_REQUEST['act'];
include_once(ROOT_PATH . 'app/app_'.$op.'.php');

?>
