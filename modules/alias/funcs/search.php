<?php
/**
 * @Project NUKEVIET 4.x
 * @Author VIETNAM DIGITAL TRADING TECHNOLOGY  (contact@thuongmaiso.vn)
 * @Copyright (C) 2014 VIETNAM DIGITAL TRADING TECHNOLOGY . All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate 2-10-2010 20:59
 */

if ( ! defined( 'NV_IS_MOD_SEARCH' ) ) die( 'Stop!!!' );

$page_title = $module_info['custom_title'];
$key_words = $module_info['keywords'];

$per_page = 20;

$base_url = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name;

$array_data = array();

$num_items = $db->query('SELECT count(id) FROM ' . NV_PREFIXLANG . '_' . $module_data)->fetchColumn();

$num = ($page - 1) * $per_page;

$array_data = $db->query('SELECT alias FROM ' . NV_PREFIXLANG . '_' . $module_data . ' limit '.$num.','.$per_page)->fetchAll();



$generate_page = nv_generate_page_alias( $base_url, $num_items, $per_page, $page );

$contents = nv_theme_alias_search( $array_data, $generate_page );

include NV_ROOTDIR . '/includes/header.php';
echo nv_site_theme( $contents );
include NV_ROOTDIR . '/includes/footer.php';
