<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VIETNAM DIGITAL TRADING TECHNOLOGY  <contact@thuongmaiso.vn>
 * @Copyright (C) 2017 VIETNAM DIGITAL TRADING TECHNOLOGY . All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate 04/18/2017 09:47
 */

if (!defined('NV_IS_MOD_SHOPS')) {
    die('Stop!!!');
}

if($link_true)
$alias = $nv_Request->get_title('alias', 'get'); //print_r($alias);die;
$array_op = explode('/', $alias);
$alias = $array_op[0];

$compare_id = $nv_Request->get_string($module_data . '_compare_id', 'session', '');
$compare_id = unserialize($compare_id);

$nv_Request->get_int('sorts', 'session', 0);
$sorts = $nv_Request->get_int('sort', 'post', 0);
$sorts_old = $nv_Request->get_int('sorts', 'session', $pro_config['sortdefault']);
$sorts = $nv_Request->get_int('sorts', 'post', $sorts_old);

$nv_Request->get_string('viewtype', 'session', '');
$viewtype = $nv_Request->get_string('viewtype', 'post', '');
$viewtype_old = $nv_Request->get_string('viewtype', 'session', '');
$viewtype = $nv_Request->get_string('viewtype', 'post', $viewtype_old);

if (isset($array_op[1])) {
    if (sizeof($array_op) == 2 and preg_match('/^page\-([0-9]+)$/', $array_op[1], $m)) {
        $page = intval($m[1]);
    } else {
        $alias = '';
    }
}
$page_title = trim(str_replace('-', ' ', $alias));

if (!empty($page_title) and $page_title == strip_punctuation($page_title)) {
    $stmt = $db->prepare('SELECT tid, title, titlesite, image, description,bodytext,ratename,ratenumber, keywords FROM ' . $db_config['prefix'] . '_' . $module_data . '_tags_' . NV_LANG_DATA . ' WHERE alias= :alias');
    $stmt->bindParam(':alias', $alias, PDO::PARAM_STR);
    $stmt->execute();
    list ($tid, $title_tag,$titlesite,$image_tag, $description,$bodytext,$ratename,$ratenumber, $key_words) = $stmt->fetch(3);

    if ($tid > 0) {
        // Fetch Limit
        $db->sqlreset()
            ->select('COUNT(*)')
            ->from($db_config['prefix'] . '_' . $module_data . '_rows t1')
            ->where('status=1 AND id IN (SELECT id FROM ' . $db_config['prefix'] . '_' . $module_data . '_tags_id_' . NV_LANG_DATA . ' WHERE tid=' . $tid . ')');

        $num_items = $db->query($db->sql())
            ->fetchColumn();

        $db->select('t1.id, t1.listcatid, t1.publtime, t1.' . NV_LANG_DATA . '_title, t1.' . NV_LANG_DATA . '_alias, t1.' . NV_LANG_DATA . '_hometext, t1.homeimgalt, t1.homeimgfile, t1.homeimgthumb, t1.product_code, t1.product_number, t1.product_price, t1.money_unit, t1.discount_id, t1.showprice, t2.newday, ' . NV_LANG_DATA . '_gift_content, gift_from, gift_to')
            ->join('INNER JOIN ' . $db_config['prefix'] . '_' . $module_data . '_catalogs t2 ON t2.catid = t1.listcatid')
            ->limit($per_page)
            ->offset(($page - 1) * $per_page);

        $result = $db->query($db->sql());

        while (list ($id, $listcatid, $publtime, $title, $alias, $hometext, $homeimgalt, $homeimgfile, $homeimgthumb, $product_code, $product_number, $product_price, $money_unit, $discount_id, $showprice, $newday, $gift_content, $gift_from, $gift_to) = $result->fetch(3)) {
            if ($homeimgthumb == 1) {
                //image thumb

                $thumb = NV_BASE_SITEURL . NV_FILES_DIR . '/' . $module_upload . '/' . $homeimgfile;
            } elseif ($homeimgthumb == 2) {
                //image file

                $thumb = NV_BASE_SITEURL . NV_UPLOADS_DIR . '/' . $module_upload . '/' . $homeimgfile;
            } elseif ($homeimgthumb == 3) {
                //image url

                $thumb = $homeimgfile;
            } else {
                //no image

                $thumb = NV_BASE_SITEURL . 'themes/' . $module_info['template'] . '/images/' . $module_file . '/no-image.jpg';
            }

            $data_content[] = array(
                'id' => $id,
                'publtime' => $publtime,
                'title' => $title,
                'alias' => $alias,
                'hometext' => $hometext,
                'homeimgalt' => $homeimgalt,
                'homeimgthumb' => $thumb,
                'product_price' => $product_price,
                'product_code' => $product_code,
                'product_number' => $product_number,
                'discount_id' => $discount_id,
                'money_unit' => $money_unit,
                'showprice' => $showprice,
                'newday' => $newday,
                'gift_content' => $gift_content,
                'gift_from' => $gift_from,
                'gift_to' => $gift_to,
                'link_pro' => NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $alias ,
                'link_order' => NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=setcart&amp;id=' . $id
            );
        }

        if (empty($data_content) and $page > 1) {
            nv_redirect_location(NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name, true);
        }

        if (!empty($image_tag)) {
            $image_tag = NV_BASE_SITEURL . NV_FILES_DIR . '/' . $module_upload .'/'. $image_tag;
        }

        $base_url = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=wishlist';
        $html_pages = nv_alias_page($page_title, $base_url, $num_items, $per_page, $page);

	
        $contents = nv_template_tag($data_content, $html_pages, $sorts, $viewtype,$title_tag,$description,$bodytext,$ratename,$ratenumber);
        $array_mod_title[] = array(
            'title' => $page_title
        );
		
   $page_title = empty($titlesite) ? $title : $titlesite;	
	$description = empty($description) ? nv_clean60(strip_tags($bodytext), 160) : $description;	
	 if (! empty($image_tag)) {
                    $meta_property['og:image'] =  NV_MY_DOMAIN .$image_tag;
	 }				
	 
        include NV_ROOTDIR . '/includes/header.php';
        echo nv_site_theme($contents);
        include NV_ROOTDIR . '/includes/footer.php';
    }
}

$redirect = '<meta http-equiv="Refresh" content="3;URL=' . nv_url_rewrite(NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name, true) . '" />';
nv_info_die($lang_global['error_404_title'], $lang_global['error_404_title'], $lang_global['error_404_content'] . $redirect);
