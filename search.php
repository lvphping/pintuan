<?php
/**
 * 昊海电商 搜索程序
 * $Author: pangbin $
 * $Id: search.php 17217 2014-05-12 06:29:08Z pangbin $
*/
define('IN_HHS', true);
if (!function_exists("htmlspecialchars_decode")) {
    function htmlspecialchars_decode($string, $quote_style = ENT_COMPAT)
    {
        return strtr($string, array_flip(get_html_translation_table(HTML_SPECIALCHARS, $quote_style)));
    }
}
require dirname(__FILE__) . '/includes/init.php';
$_REQUEST['act'] = !empty($_REQUEST['act']) ? trim($_REQUEST['act']) : '';
/*------------------------------------------------------ */
//-- 高级搜索
/*------------------------------------------------------ */
$_REQUEST['keywords'] = !empty($_REQUEST['keywords']) ? htmlspecialchars(trim($_REQUEST['keywords'])) : '';
$_REQUEST['brand'] = !empty($_REQUEST['brand']) ? intval($_REQUEST['brand']) : 0;
$_REQUEST['category'] = !empty($_REQUEST['category']) ? intval($_REQUEST['category']) : 0;
$_REQUEST['min_price'] = !empty($_REQUEST['min_price']) ? intval($_REQUEST['min_price']) : 0;
$_REQUEST['max_price'] = !empty($_REQUEST['max_price']) ? intval($_REQUEST['max_price']) : 0;
$_REQUEST['goods_type'] = !empty($_REQUEST['goods_type']) ? intval($_REQUEST['goods_type']) : 0;
$_REQUEST['sc_ds'] = !empty($_REQUEST['sc_ds']) ? intval($_REQUEST['sc_ds']) : 0;
$_REQUEST['outstock'] = !empty($_REQUEST['outstock']) ? 1 : 0;
$action = '';
if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'form') {
    /* 要显示高级搜索栏 */
    $adv_value['keywords'] = htmlspecialchars(stripcslashes($_REQUEST['keywords']));
    $adv_value['brand'] = $_REQUEST['brand'];
    $adv_value['min_price'] = $_REQUEST['min_price'];
    $adv_value['max_price'] = $_REQUEST['max_price'];
    $adv_value['category'] = $_REQUEST['category'];
    $attributes = get_seachable_attributes($_REQUEST['goods_type']);
    /* 将提交数据重新赋值 */
    foreach ($attributes['attr'] as $key => $val) {
        if (!empty($_REQUEST['attr'][$val['id']])) {
            if ($val['type'] == 2) {
                $attributes['attr'][$key]['value']['from'] = !empty($_REQUEST['attr'][$val['id']]['from']) ? htmlspecialchars(stripcslashes(trim($_REQUEST['attr'][$val['id']]['from']))) : '';
                $attributes['attr'][$key]['value']['to'] = !empty($_REQUEST['attr'][$val['id']]['to']) ? htmlspecialchars(stripcslashes(trim($_REQUEST['attr'][$val['id']]['to']))) : '';
            } else {
                $attributes['attr'][$key]['value'] = !empty($_REQUEST['attr'][$val['id']]) ? htmlspecialchars(stripcslashes(trim($_REQUEST['attr'][$val['id']]))) : '';
            }
        }
    }
    if ($_REQUEST['sc_ds']) {
        $smarty->assign('scck', 'checked');
    }
    $smarty->assign('adv_val', $adv_value);
    $smarty->assign('goods_type_list', $attributes['cate']);
    $smarty->assign('goods_attributes', $attributes['attr']);
    $smarty->assign('goods_type_selected', $_REQUEST['goods_type']);
    $smarty->assign('cat_list', cat_list(0, $adv_value['category'], true, 2, false));
    $smarty->assign('brand_list', get_brand_list());
    $smarty->assign('action', 'form');
    $smarty->assign('use_storage', $_CFG['use_storage']);
    $action = 'form';
}
/* 初始化搜索条件 */
$keywords = '';
$tag_where = '';
//获得区域级别
$current_region_type = get_region_type($_SESSION['site_id']);
if ($current_region_type <= 2) {
    $tag_where .= " and (g.city_id='" . $_SESSION['site_id'] . "' or g.city_id=1) ";
} elseif ($current_region_type == 3) {
    $tag_where .= " and (g.district_id='" . $_SESSION['site_id'] . "' or g.city_id=1) ";
}
if (!empty($_REQUEST['keywords'])) {
    $arr = array();
    if (stristr($_REQUEST['keywords'], ' AND ') !== false) {
        /* 检查关键字中是否有AND，如果存在就是并 */
        $arr = explode('AND', $_REQUEST['keywords']);
        $operator = " AND ";
    } elseif (stristr($_REQUEST['keywords'], ' OR ') !== false) {
        /* 检查关键字中是否有OR，如果存在就是或 */
        $arr = explode('OR', $_REQUEST['keywords']);
        $operator = " OR ";
    } elseif (stristr($_REQUEST['keywords'], ' + ') !== false) {
        /* 检查关键字中是否有加号，如果存在就是或 */
        $arr = explode('+', $_REQUEST['keywords']);
        $operator = " OR ";
    } else {
        /* 检查关键字中是否有空格，如果存在就是并 */
        $arr = explode(' ', $_REQUEST['keywords']);
        $operator = " AND ";
    }
    $keywords = 'AND (';
    $goods_ids = array();
    foreach ($arr as $key => $val) {
        if ($key > 0 && $key < count($arr) && count($arr) > 1) {
            $keywords .= $operator;
        }
        $val = mysql_like_quote(trim($val));
        $sc_dsad = $_REQUEST['sc_ds'] ? " OR goods_desc LIKE '%{$val}%'" : '';
        $keywords .= "(goods_name LIKE '%{$val}%' OR goods_sn LIKE '%{$val}%' OR keywords LIKE '%{$val}%' {$sc_dsad})";
        $sql = 'SELECT DISTINCT goods_id FROM ' . $hhs->table('tag') . " WHERE tag_words LIKE '%{$val}%' ";
        $res = $db->query($sql);
        while ($row = $db->FetchRow($res)) {
            $goods_ids[] = $row['goods_id'];
        }
        $db->autoReplace($hhs->table('keywords'), array('date' => local_date('Y-m-d'), 'searchengine' => 'hhshop', 'keyword' => addslashes(str_replace('%', '', $val)), 'count' => 1), array('count' => 1));
    }
    $keywords .= ')';
    $goods_ids = array_unique($goods_ids);
    $tag_where = implode(',', $goods_ids);
    if (!empty($tag_where)) {
        $tag_where = 'OR g.goods_id ' . db_create_in($tag_where);
    }
}
$category = !empty($_REQUEST['category']) ? intval($_REQUEST['category']) : 0;
$categories = $category > 0 ? ' AND ' . get_children($category) : '';
$brand = $_REQUEST['brand'] ? " AND brand_id = '{$_REQUEST['brand']}'" : '';
$outstock = !empty($_REQUEST['outstock']) ? " AND g.goods_number > 0 " : '';
$min_price = $_REQUEST['min_price'] != 0 ? " AND g.shop_price >= '{$_REQUEST['min_price']}'" : '';
$max_price = $_REQUEST['max_price'] != 0 || $_REQUEST['min_price'] < 0 ? " AND g.shop_price <= '{$_REQUEST['max_price']}'" : '';
// comments
$comments = assign_comment($goods_id, 0, 1);
$smarty->assign('comments', $comments['comments']);
$smarty->assign('comments_nums', $comments['pager']['record_count']);
$sql = "select g.shop_price as goods_price,g.team_price,g.goods_name,g.goods_id,g.goods_img,c.rec_id from " . $hhs->table("goods") . " as g LEFT JOIN " . $hhs->table("collect_goods") . " as c ON  c.user_id='" . $_SESSION['user_id'] . "' and c.goods_id=g.goods_id where g.is_on_sale = 1 AND g.is_alone_sale = 1 AND g.is_delete = 0 order by rand() limit 6";
$rands_goods = $db->getAll($sql);
foreach ($rands_goods as $ids => $v) {
    if ($v['team_price'] == '0.00') {
        $rands_goods[$ids]['team_price'] = $v['goods_price'];
    }
}
$smarty->assign('rands_goods', $rands_goods);
/* 排序、显示方式以及类型 */
$default_display_type = $_CFG['show_order_type'] == '0' ? 'list' : ($_CFG['show_order_type'] == '1' ? 'grid' : 'text');
$default_sort_order_method = $_CFG['sort_order_method'] == '0' ? 'DESC' : 'ASC';
$default_sort_order_type = $_CFG['sort_order_type'] == '0' ? 'goods_id' : ($_CFG['sort_order_type'] == '1' ? 'shop_price' : 'last_update');
$sort = isset($_REQUEST['sort']) && in_array(trim(strtolower($_REQUEST['sort'])), array('goods_id', 'shop_price', 'last_update')) ? trim($_REQUEST['sort']) : $default_sort_order_type;
$order = isset($_REQUEST['order']) && in_array(trim(strtoupper($_REQUEST['order'])), array('ASC', 'DESC')) ? trim($_REQUEST['order']) : $default_sort_order_method;
$display = isset($_REQUEST['display']) && in_array(trim(strtolower($_REQUEST['display'])), array('list', 'grid', 'text')) ? trim($_REQUEST['display']) : (isset($_SESSION['display_search']) ? $_SESSION['display_search'] : $default_display_type);
$_SESSION['display_search'] = $display;
$page = !empty($_REQUEST['page']) && intval($_REQUEST['page']) > 0 ? intval($_REQUEST['page']) : 1;
$size = 50;
$intromode = '';
//方式，用于决定搜索结果页标题图片
if (!empty($_REQUEST['intro'])) {
    switch ($_REQUEST['intro']) {
        case 'best':
            $intro = ' AND g.is_best = 1';
            $intromode = 'best';
            $ur_here = $_LANG['best_goods'];
            break;
        case 'new':
            $intro = ' AND g.is_new = 1';
            $intromode = 'new';
            $ur_here = $_LANG['new_goods'];
            break;
        case 'hot':
            $intro = ' AND g.is_hot = 1';
            $intromode = 'hot';
            $ur_here = $_LANG['hot_goods'];
            break;
        case 'promotion':
            $time = gmtime();
            $intro = " AND g.promote_price > 0 AND g.promote_start_date <= '{$time}' AND g.promote_end_date >= '{$time}'";
            $intromode = 'promotion';
            $ur_here = $_LANG['promotion_goods'];
            break;
        default:
            $intro = '';
    }
} else {
    $intro = '';
}
if (empty($ur_here)) {
    $ur_here = $_LANG['search_goods'];
}
/*------------------------------------------------------ */
//-- 属性检索
/*------------------------------------------------------ */
$attr_in = '';
$attr_num = 0;
$attr_url = '';
$attr_arg = array();
if (!empty($_REQUEST['attr'])) {
    $sql = "SELECT goods_id, COUNT(*) AS num FROM " . $hhs->table("goods_attr") . " WHERE 0 ";
    foreach ($_REQUEST['attr'] as $key => $val) {
        if (is_not_null($val) && is_numeric($key)) {
            $attr_num++;
            $sql .= " OR (1 ";
            if (is_array($val)) {
                $sql .= " AND attr_id = '{$key}'";
                if (!empty($val['from'])) {
                    $sql .= is_numeric($val['from']) ? " AND attr_value >= " . floatval($val['from']) : " AND attr_value >= '{$val['from']}'";
                    $attr_arg["attr[{$key}][from]"] = $val['from'];
                    $attr_url .= "&amp;attr[{$key}][from]={$val['from']}";
                }
                if (!empty($val['to'])) {
                    $sql .= is_numeric($val['to']) ? " AND attr_value <= " . floatval($val['to']) : " AND attr_value <= '{$val['to']}'";
                    $attr_arg["attr[{$key}][to]"] = $val['to'];
                    $attr_url .= "&amp;attr[{$key}][to]={$val['to']}";
                }
            } else {
                /* 处理选购中心过来的链接 */
                $sql .= isset($_REQUEST['pickout']) ? " AND attr_id = '{$key}' AND attr_value = '" . $val . "' " : " AND attr_id = '{$key}' AND attr_value LIKE '%" . mysql_like_quote($val) . "%' ";
                $attr_url .= "&amp;attr[{$key}]={$val}";
                $attr_arg["attr[{$key}]"] = $val;
            }
            $sql .= ')';
        }
    }
    /* 如果检索条件都是无效的，就不用检索 */
    if ($attr_num > 0) {
        $sql .= " GROUP BY goods_id HAVING num = '{$attr_num}'";
        $row = $db->getCol($sql);
        if (count($row)) {
            $attr_in = " AND " . db_create_in($row, 'g.goods_id');
        } else {
            $attr_in = " AND 0 ";
        }
    }
} elseif (isset($_REQUEST['pickout'])) {
    /* 从选购中心进入的链接 */
    $sql = "SELECT DISTINCT(goods_id) FROM " . $hhs->table('goods_attr');
    $col = $db->getCol($sql);
    //如果商店没有设置商品属性,那么此检索条件是无效的
    if (!empty($col)) {
        $attr_in = " AND " . db_create_in($col, 'g.goods_id');
    }
}
$and_where = '';
if ($_REQUEST['search_type']) {
    $search_type = trim($_REQUEST['search_type']);
    switch ($search_type) {
        case 'is_zero':
            $and_where = " and g.`is_zero` = 1 ";
            break;
        case 'is_mall':
            $and_where = " and g.`is_mall` = 1 ";
            break;
        case 'is_team':
            $and_where = " and g.`is_team` = 1 ";
            break;
        default:
            # code...
            break;
    }
}
/* 获得符合条件的商品总数 */
$luckdraw_goods_id_list = $GLOBALS['db']->getCol("SELECT goods_id FROM " . $GLOBALS['hhs']->table('luckdraw') . " WHERE start_time < " . gmtime() . " and end_time >" . gmtime());
$luckdraw_goods_id_list = implode(',', $luckdraw_goods_id_list);
if($luckdraw_goods_id_list){
   $sql = "SELECT COUNT(*) FROM " . $hhs->table('goods') . " AS g " . "WHERE g.is_luck = 0 AND g.is_miao = 0 AND g.is_delete = 0 AND g.is_on_sale = 1 and g.goods_id not in (" . $luckdraw_goods_id_list . ") AND g.is_alone_sale = 1 {$attr_in} " . "AND (( 1 " . $categories . $keywords . $brand . $min_price . $max_price . $intro . $outstock . " ) " . $tag_where . $and_where . " )";
}
$sql = "SELECT COUNT(*) FROM " . $hhs->table('goods') . " AS g " . "WHERE g.is_luck = 0 AND g.is_miao = 0 AND g.is_delete = 0 AND g.is_on_sale = 1 AND g.is_alone_sale = 1 {$attr_in} " . "AND (( 1 " . $categories . $keywords . $brand . $min_price . $max_price . $intro . $outstock . " ) " . $tag_where . $and_where . " )";
$count = $db->getOne($sql);
$smarty->assign('count', $count);
$max_page = $count > 0 ? ceil($count / $size) : 1;
if ($page > $max_page) {
    $page = $max_page;
}
/* 查询商品 */
$sql = "SELECT g.is_team,g.is_mall, g.goods_id,g.is_zero, g.suppliers_id, g.goods_name, g.goods_number, g.market_price, g.is_new, g.is_best, g.is_hot, g.team_num,g.team_price, g.shop_price AS org_price, " . "IFNULL(mp.user_price, g.shop_price * '{$_SESSION['discount']}') AS shop_price, " . "g.promote_price, g.promote_start_date, g.promote_end_date, g.goods_brief, g.goods_thumb, g.goods_img, g.little_img, g.goods_brief, g.goods_type " . "FROM " . $hhs->table('goods') . " AS g " . "LEFT JOIN " . $GLOBALS['hhs']->table('member_price') . " AS mp " . "ON mp.goods_id = g.goods_id AND mp.user_rank = '{$_SESSION['user_rank']}' " . "WHERE g.is_miao = 0 AND g.is_luck = 0 AND g.is_delete = 0 AND g.is_on_sale = 1 AND g.is_alone_sale = 1 {$attr_in} " . "AND (( 1 " . $categories . $keywords . $brand . $min_price . $max_price . $intro . $outstock . " ) " . $tag_where . " ) " . $and_where . "ORDER BY {$sort} {$order}";
$res = $db->SelectLimit($sql, $size, ($page - 1) * $size);
$arr = array();
while ($row = $db->FetchRow($res)) {
    if ($row['promote_price'] > 0) {
        $promote_price = bargain_price($row['promote_price'], $row['promote_start_date'], $row['promote_end_date']);
    } else {
        $promote_price = 0;
    }
    /* 处理商品水印图片 */
    /* 处理商品水印图片 */
    $watermark_img = '';
    if ($promote_price != 0) {
        $watermark_img = "watermark_promote_small";
    } elseif ($row['is_new'] != 0) {
        $watermark_img = "watermark_new_small";
    } elseif ($row['is_best'] != 0) {
        $watermark_img = "watermark_best_small";
    } elseif ($row['is_hot'] != 0) {
        $watermark_img = 'watermark_hot_small';
    }
    if ($watermark_img != '') {
        $arr[$row['goods_id']]['watermark_img'] = $watermark_img;
    }
    $arr[$row['goods_id']]['is_mall'] = $row['is_mall'];
    $arr[$row['goods_id']]['is_team'] = $row['is_team'];
    $arr[$row['goods_id']]['is_zero'] = $row['is_zero'];
    $arr[$row['goods_id']]['goods_id'] = $row['goods_id'];
    $arr[$row['goods_id']]['goods_name'] = $row['goods_name'];
    $arr[$row['goods_id']]['goods_brief'] = $row['goods_brief'];
    $arr[$row['goods_id']]['goods_number'] = $row['goods_number'];
    $arr[$row['goods_id']]['type'] = $row['goods_type'];
    $arr[$row['goods_id']]['market_price'] = price_format($row['market_price']);
    $arr[$row['goods_id']]['shop_price'] = price_format($row['shop_price']);
    $arr[$row['goods_id']]['promote_price'] = $promote_price > 0 ? price_format($promote_price) : '';
    $arr[$row['goods_id']]['goods_brief'] = $row['goods_brief'];
    $arr[$row['goods_id']]['goods_thumb'] = get_image_path($row['goods_id'], $row['goods_thumb'], true);
    $arr[$row['goods_id']]['goods_img'] = get_image_path($row['goods_id'], $row['goods_img']);
    $arr[$row['goods_id']]['little_img'] = get_image_path($row['goods_id'], $row['little_img']);
    $arr[$row['goods_id']]['url'] = build_uri('goods', array('gid' => $row['goods_id']), $row['goods_name']);
    $arr[$row['goods_id']]['team_num'] = $row['team_num'];
    $arr[$row['goods_id']]['team_price'] = price_format($row['team_price'], false);
    $arr[$row['goods_id']]['team_discount'] = number_format($row['team_price'] / $row['market_price'] * 10, 1);
    $arr[$row['goods_id']]['goods_number'] = $row['goods_number'];
    $sql = "select suppliers_name from " . $GLOBALS['hhs']->table('suppliers') . " where suppliers_id=" . $row['suppliers_id'];
    $arr[$row['goods_id']]['suppliers_name'] = $GLOBALS['db']->getOne($sql);
    $todayDate = gmtime();
    $luckdraw_goods = $GLOBALS['db']->getOne("SELECT goods_id FROM " . $GLOBALS['hhs']->table('luckdraw') . " WHERE start_time < " . $todayDate . " and end_time >" . $todayDate . " and goods_id = " . $row['goods_id']);
    if ($luckdraw_goods > 0) {
        unset($arr);
    }
}
$smarty->assign('category', $category);
$smarty->assign('keywords', htmlspecialchars(stripslashes($_REQUEST['keywords'])));
$smarty->assign('search_keywords', stripslashes(htmlspecialchars_decode($_REQUEST['keywords'])));
$smarty->assign('brand', $_REQUEST['brand']);
$smarty->assign('min_price', $min_price);
$smarty->assign('max_price', $max_price);
$smarty->assign('outstock', $_REQUEST['outstock']);
/* 分页 */
$url_format = "search.php?category={$category}&amp;keywords=" . urlencode(stripslashes($_REQUEST['keywords'])) . "&amp;brand=" . $_REQUEST['brand'] . "&amp;action=" . $action . "&amp;goods_type=" . $_REQUEST['goods_type'] . "&amp;sc_ds=" . $_REQUEST['sc_ds'];
if (!empty($intromode)) {
    $url_format .= "&amp;intro=" . $intromode;
}
if (isset($_REQUEST['pickout'])) {
    $url_format .= '&amp;pickout=1';
}
$url_format .= "&amp;min_price=" . $_REQUEST['min_price'] . "&amp;max_price=" . $_REQUEST['max_price'] . "&amp;sort={$sort}";
$url_format .= "{$attr_url}&amp;order={$order}&amp;page=";
$pager['search'] = array('keywords' => stripslashes(urlencode($_REQUEST['keywords'])), 'category' => $category, 'brand' => $_REQUEST['brand'], 'sort' => $sort, 'order' => $order, 'min_price' => $_REQUEST['min_price'], 'max_price' => $_REQUEST['max_price'], 'action' => $action, 'intro' => empty($intromode) ? '' : trim($intromode), 'goods_type' => $_REQUEST['goods_type'], 'sc_ds' => $_REQUEST['sc_ds'], 'outstock' => $_REQUEST['outstock']);
$pager['search'] = array_merge($pager['search'], $attr_arg);
$pager = get_pager('search.php', $pager['search'], $count, $page, $size);
$pager['display'] = $display;
$smarty->assign('url_format', $url_format);
$smarty->assign('pager', $pager);
assign_template();
$position = assign_ur_here(0, $ur_here . ($_REQUEST['keywords'] ? '_' . $_REQUEST['keywords'] : ''));
$smarty->assign('page_title', $position['title']);
// 页面标题
$smarty->assign('ur_here', $position['ur_here']);
// 当前位置
$smarty->assign('search_keywords', $_REQUEST['keywords']);
$smarty->assign('goods_list', $arr);
$smarty->display('search.dwt');
/*------------------------------------------------------ */
//-- PRIVATE FUNCTION
/*------------------------------------------------------ */
/**
 *
 *
 * @access public
 * @param
 *
 * @return void
 */
function is_not_null($value)
{
    if (is_array($value)) {
        return !empty($value['from']) || !empty($value['to']);
    } else {
        return !empty($value);
    }
}
/**
 * 获得可以检索的属性
 *
 * @access  public
 * @params  integer $cat_id
 * @return  void
 */
function get_seachable_attributes($cat_id = 0)
{
    $attributes = array('cate' => array(), 'attr' => array());
    /* 获得可用的商品类型 */
    $sql = "SELECT t.cat_id, cat_name FROM " . $GLOBALS['hhs']->table('goods_type') . " AS t, " . $GLOBALS['hhs']->table('attribute') . " AS a" . " WHERE t.cat_id = a.cat_id AND t.enabled = 1 AND a.attr_index > 0 ";
    $cat = $GLOBALS['db']->getAll($sql);
    /* 获取可以检索的属性 */
    if (!empty($cat)) {
        foreach ($cat as $val) {
            $attributes['cate'][$val['cat_id']] = $val['cat_name'];
        }
        $where = $cat_id > 0 ? ' AND a.cat_id = ' . $cat_id : " AND a.cat_id = " . $cat[0]['cat_id'];
        $sql = 'SELECT attr_id, attr_name, attr_input_type, attr_type, attr_values, attr_index, sort_order ' . ' FROM ' . $GLOBALS['hhs']->table('attribute') . ' AS a ' . ' WHERE a.attr_index > 0 ' . $where . ' ORDER BY cat_id, sort_order ASC';
        $res = $GLOBALS['db']->query($sql);
        while ($row = $GLOBALS['db']->FetchRow($res)) {
            if ($row['attr_index'] == 1 && $row['attr_input_type'] == 1) {
                $row['attr_values'] = str_replace("\r", '', $row['attr_values']);
                $options = explode("\n", $row['attr_values']);
                $attr_value = array();
                foreach ($options as $opt) {
                    $attr_value[$opt] = $opt;
                }
                $attributes['attr'][] = array('id' => $row['attr_id'], 'attr' => $row['attr_name'], 'options' => $attr_value, 'type' => 3);
            } else {
                $attributes['attr'][] = array('id' => $row['attr_id'], 'attr' => $row['attr_name'], 'type' => $row['attr_index']);
            }
        }
    }
    return $attributes;
}
?>