<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VIETNAM DIGITAL TRADING TECHNOLOGY  <contact@thuongmaiso.vn>
 * @Copyright (C) 2017 VIETNAM DIGITAL TRADING TECHNOLOGY . All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate 04/18/2017 09:47
 */

if (! defined('NV_IS_MOD_SHOPS')) {
    die('Stop!!!');
}

$order_id = $nv_Request->get_string('order_id', 'get', '');
$checkss = $nv_Request->get_string('checkss', 'get', '');

if ($order_id > 0 and $checkss == md5($order_id . $global_config['sitekey'] . session_id())) {
    $table_name = $db_config['prefix'] . '_' . $module_data . '_orders';
    $link = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=';

    $result = $db->query('SELECT * FROM ' . $table_name . ' WHERE order_id=' . $order_id);
    $data = $result->fetch();
	
    if (empty($data)) {
        nv_redirect_location(NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name, true);
    }

    // Thong tin chi tiet mat hang trong don hang
    $listid = $listnum = $listprice = $listgroup = array();
    $result = $db->query('SELECT * FROM ' . $db_config['prefix'] . '_' . $module_data . '_orders_id WHERE order_id=' . $order_id);
    while ($row = $result->fetch()) {
        $listid[] = $row['proid'];
        $listid_order[] = $row['id'];
        $listnum[] = $row['num'];
        $listprice[] = $row['price'];
        $list_setting_price[] = $row['setting_price'];
    }

    $data_pro = array();
    $temppro = array();
    $i = 0;

    foreach ($listid as $proid) {
        if (empty($listprice[$i])) {
            $listprice[$i] = 0;
        }
        if (empty($listnum[$i])) {
            $listnum[$i] = 0;
        }

        $temppro[$proid] = array( 'price' => $listprice[$i], 'num' => $listnum[$i] );

        $arrayid[] = $proid;
        ++$i;
    }
//print_r($_SESSION[$module_data . '_coupons']);die;
    foreach ($listid_order as $id_order) {
           
            $sql = 'SELECT t3.num, t3.setting_price, t3.listgroupid, t1.id, t1.listcatid, t1.publtime, t1.' . NV_LANG_DATA . '_title, t1.' . NV_LANG_DATA . '_alias, t1.' . NV_LANG_DATA . '_hometext, t2.' . NV_LANG_DATA . '_title, t1.money_unit, t1.discount_id FROM ' . $db_config['prefix'] . '_' . $module_data . '_rows AS t1, ' . $db_config['prefix'] . '_' . $module_data . '_units AS t2, ' . $db_config['prefix'] . '_' . $module_data . '_orders_id AS t3  WHERE t1.product_unit = t2.id AND t1.id = t3.proid AND t3.id ='. $id_order .' AND t3.order_id=' . $order_id . ' AND t1.status =1 limit 0,1';
			//die($sql);
            $result = $db->query($sql);
            while (list($num, $setting_price, $listgroupid, $id, $listcatid, $publtime, $title, $alias, $hometext, $unit, $money_unit, $discount_id) = $result->fetch(3)) {
                $price = nv_get_price($id, $pro_config['money_unit'], $num, true);
				
                $data_pro[] = array(
                    'id' => $id,
                    'publtime' => $publtime,
                    'title' => $title,
                    'alias' => $alias,
                    'hometext' => $hometext,
                    'product_price' => $price['sale'],
                    'product_unit' => $unit,
                    'money_unit' => $money_unit,
                    'discount_id' => $discount_id,
                    'link_pro' => $link . $global_array_shops_cat[$listcatid]['alias'] . '/' . $alias . $global_config['rewrite_exturl'],
                    'product_number' => $num,
                    'group' => $listgroupid,
                    'setting_price' => $setting_price
                );
            }
        }
		
	// Thong tin van chuyen
    $result = $db->query('SELECT * FROM ' . $db_config['prefix'] . '_' . $module_data . '_orders_shipping WHERE order_id = ' . $data['order_id']);
    $data_shipping = $result->fetch();
	
	$data['coupons'] = $db->query('SELECT amount FROM ' . $db_config['prefix'] . '_' . $module_data . '_coupons_history WHERE order_id ='.$data['order_id'])->fetchColumn();
	
    $page_title = $data['order_code'];
    $contents = call_user_func('print_pay', $data, $data_pro, $data_shipping);

    include NV_ROOTDIR . '/includes/header.php';
    echo nv_site_theme($contents, false);
    include NV_ROOTDIR . '/includes/footer.php';
} else {
    nv_redirect_location(NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name, true);
}
