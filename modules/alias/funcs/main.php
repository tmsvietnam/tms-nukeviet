<?php
/**
 * @Project NUKEVIET 4.x
 * @Author VIETNAM DIGITAL TRADING TECHNOLOGY  (contact@thuongmaiso.vn)
 * @Copyright (C) 2014 VIETNAM DIGITAL TRADING TECHNOLOGY . All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate 2-10-2010 20:59
 */

$array_mod = LoadModulesSearch();
$is_search = false;
$search = array(
    'key' => '',
    'len_key' => 0,
    'mod' => 'all',
    'logic' => 1, //OR
    'page' => 1,
    'is_error' => false,
    'errorInfo' => '',
    'content' => ''
);


$search['tag'] = $nv_Request->get_title('tag', 'get', 'all', $search['mod']);
if(empty($alias))
{
	$alias = $search['tag'];
	$id_alias = $db->query("SELECT id FROM ". NV_PREFIXLANG ."_alias WHERE alias ='". $alias ."'")->fetchColumn();
	
}

if (!empty($alias) and $id_alias > 0) {
    $is_search = true;

    $search['key'] = $alias;
    $search['key'] = str_replace('+', ' ', urldecode($search['key']));
    $search['mod'] = $nv_Request->get_title('m', 'get', 'all', $search['mod']);
    $search['logic'] = $nv_Request->get_int('l', 'get', $search['logic']);
    $search['page'] = $nv_Request->get_int('page', 'get', 1);

    if ($search['logic'] != 1) {
        $search['logic'] = 0;
    }
    if (! isset($array_mod[$search['mod']])) {
        $search['mod'] = 'all';
    }

    $base_url_rewrite = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . urlencode($search['key']);
    if ($search['mod'] != 'all') {
        $base_url_rewrite .= '&m=' . htmlspecialchars(nv_unhtmlspecialchars($search['mod']));
    }
    if ($search['logic'] != 1) {
        $base_url_rewrite .= '&l=' . $search['logic'];
    }
    if ($search['page'] > 1) {
        $base_url_rewrite .= '&page=' . $search['page'];
    }
    $base_url_rewrite = nv_url_rewrite($base_url_rewrite, true);
    $request_uri = $_SERVER['REQUEST_URI'];
    if ($request_uri != $base_url_rewrite and NV_MAIN_DOMAIN . $request_uri != $base_url_rewrite) {
       // nv_redirect_location($base_url_rewrite);
    }

    if (! empty($search['key'])) {
        if (! $search['logic']) {
            $search['key'] = preg_replace(array( "/^([\S]{1})\s/uis", "/\s([\S]{1})\s/uis", "/\s([\S]{1})$/uis" ), " ", $search['key']);
        }
        $search['key'] = trim($search['key']);
        $search['len_key'] = nv_strlen($search['key']);
    }

    if (false) {
        $search['is_error'] = true;
        $search['errorInfo'] = sprintf($lang_module['searchQueryError'], NV_MIN_SEARCH_LENGTH);
    } else {
        if (! empty($search['mod']) and isset($array_mod[$search['mod']])) {
            $mods = array( $search['mod'] => $array_mod[$search['mod']] );
            $limit = 20;
            $is_generate_page = true;
        } else {
            $mods = $array_mod;
            $limit = 3;
            $is_generate_page = false;
        }

        $dbkeyword = $db->dblikeescape($search['key']);
        $dbkeywordhtml = $db->dblikeescape(nv_htmlspecialchars($search['key']));
        $logic = $search['logic'] ? 'AND' : 'OR';
        $key = $search['key'];
		$i = 1;
		
		 if (! empty($mod)) {
            $mods = array( $mod => $array_mod[$mod] );
            $limit = 20;
            $is_generate_page = true;
        }
		
        foreach ($mods as $m_name => $m_values) {
            $page = $search['page'];
            $num_items = 0;
            $result_array = array();
            include NV_ROOTDIR . '/modules/' . $m_values['module_file'] . '/search.php' ;
			
            if (! empty($num_items) and ! empty($result_array)) { 
                $search['content'] .= search_result_theme($result_array, $m_name, $m_values['custom_title'], $search, $is_generate_page, $limit, $num_items);
            }
			$i++;
        }
		
		// THÔNG TIN DES TAG
		$data = $db->query('SELECT * FROM ' . NV_PREFIXLANG . '_' . $module_data . ' WHERE id ='.$id_alias)->fetch();
		
		

        if (empty($search['content'])) {
            $search['content'] = $lang_module['search_none'] . ' &quot;' . $search['key'] . '&quot;';
        }
    }
}

$contents = search_main_theme($is_generate_page, $is_search, $data, $search, $array_mod);

$page_title = $module_info['site_title'];

if (! empty($search['key'])) {
    $page_title .= ' ' . NV_TITLEBAR_DEFIS . ' ' . $search['key'];

    if ($search['page'] > 1) {
        $page_title .= ' ' . NV_TITLEBAR_DEFIS . ' ' . $lang_global['page'] . ' ' . $search['page'];
    }
}

$key_words = $description = 'no';
$mod_title = isset($lang_module['main_title']) ? $lang_module['main_title'] : $module_info['custom_title'];

include NV_ROOTDIR . '/includes/header.php';
echo nv_site_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';
