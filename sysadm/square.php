<?php



/**

 * 昊海电商 用户评论管理程序

 * ============================================================================

 * * 版权所有 2012-2014 西安昊海网络科技有限公司，并保留所有权利。

 * 网站地址: http://www.xaphp.cn；

 * ----------------------------------------------------------------------------

 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和

 * 使用；不允许对程序代码以任何形式任何目的的再发布。

 * ============================================================================

 * $Author: pangbin $

 * $Id: comment_manage.php 17217 2014-05-12 06:29:08Z pangbin $

*/



define('IN_HHS', true);



require(dirname(__FILE__) . '/includes/init.php');



/* act操作项的初始化 */

if (empty($_REQUEST['act']))

{

    $_REQUEST['act'] = 'list';

}

else

{

    $_REQUEST['act'] = trim($_REQUEST['act']);

}



/*------------------------------------------------------ */

//-- 获取没有回复的评论列表

/*------------------------------------------------------ */

if ($_REQUEST['act'] == 'list')

{
	admin_priv('square');

    $smarty->assign('ur_here',      $_LANG['05_comment_manage']);

    $smarty->assign('full_page',    1);



    $list = get_comment_list();



    $smarty->assign('comment_list', $list['item']);

    $smarty->assign('filter',       $list['filter']);

    $smarty->assign('record_count', $list['record_count']);

    $smarty->assign('page_count',   $list['page_count']);



    $sort_flag  = sort_flag($list['filter']);

    $smarty->assign($sort_flag['tag'], $sort_flag['img']);



    assign_query_info();

    $smarty->display('square_list.htm');

}



/*------------------------------------------------------ */

//-- 翻页、搜索、排序

/*------------------------------------------------------ */

if ($_REQUEST['act'] == 'query')

{

    $list = get_comment_list();



    $smarty->assign('comment_list', $list['item']);

    $smarty->assign('filter',       $list['filter']);

    $smarty->assign('record_count', $list['record_count']);

    $smarty->assign('page_count',   $list['page_count']);



    $sort_flag  = sort_flag($list['filter']);

    $smarty->assign($sort_flag['tag'], $sort_flag['img']);



    make_json_result($smarty->fetch('square_list.htm'), '',

        array('filter' => $list['filter'], 'page_count' => $list['page_count']));

}

elseif ($_REQUEST['act'] == 'edit_square')

{

    $id        = intval($_POST['id']);

    $square = json_str_iconv(trim($_POST['val']));



    $db->query("update ".$hhs->table('order_info')." set square='".$square."' where order_id = '".$id."'");



    clear_cache_files();

    make_json_result(stripslashes($square));

    exit;

}

elseif ($_REQUEST['act'] == 'show_square')

{

    $id = intval($_REQUEST['id']);

    

    $sql = "SELECT show_square

            FROM " . $hhs->table('order_info') . "

            WHERE order_id = '$id'";

    $show_square = $db->getOne($sql);



    $show_square = $show_square ? 0 : 1;



    $db->query("update ".$hhs->table('order_info')." set show_square='".$show_square."' where order_id = '".$id."'");



    clear_cache_files();

    make_json_result($show_square);



    exit;

}

/**

 * 获取评论列表

 * @access  public

 * @return  array

 */

function get_comment_list()

{

    /* 查询条件 */

    $filter['keywords']     = empty($_REQUEST['keywords']) ? 0 : trim($_REQUEST['keywords']);

    if (isset($_REQUEST['is_ajax']) && $_REQUEST['is_ajax'] == 1)

    {

        $filter['keywords'] = json_str_iconv($filter['keywords']);

    }

    $filter['sort_by']      = empty($_REQUEST['sort_by']) ? 'add_time' : trim($_REQUEST['sort_by']);

    $filter['sort_order']   = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);





    $where = " WHERE o.team_status = 1 and o.team_first = 1 ";

    if ($filter['show_square'] !='')

    {

        $where .= " AND o.show_square = '".$filter['show_square']."'";

    }

    $where .= (!empty($filter['keywords'])) ? " AND o.square LIKE '%" . mysql_like_quote($filter['keywords']) . "%' " : '';



    $sql = "SELECT count(*) FROM " .$GLOBALS['hhs']->table('order_info'). " as o $where";

    $filter['record_count'] = $GLOBALS['db']->getOne($sql);



    /* 分页大小 */

    $filter = page_and_size($filter);



    /* 获取评论数据 */

    $arr = array();

    $sql  = "SELECT o.order_id, o.show_square,o.square,u.uname,g.goods_name ".

            " FROM " . $GLOBALS['hhs']->table('order_info') . " AS o " .

            " LEFT JOIN " .$GLOBALS['hhs']->table('users'). " AS u ON u.user_id=o.user_id ".

            " LEFT JOIN " .$GLOBALS['hhs']->table('order_goods'). " AS g ON g.order_id=o.order_id ".

            $where .

            " ORDER BY $filter[sort_by] $filter[sort_order] ".

            " LIMIT ". $filter['start'] .", $filter[page_size]";

    $rows  = $GLOBALS['db']->getAll($sql);



    $filter['keywords'] = stripslashes($filter['keywords']);

    $arr = array('item' => $rows, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);



    return $arr;

}



?>

