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

$page_title = $module_info['custom_title'];
$key_words = $module_info['keywords'];



if ($nv_Request->isset_request('payvitien', 'get')) {

	if (!defined('NV_IS_USER')) {
		die('1');
	}
	
	
    $order_id = $nv_Request->get_int('order_id', 'get', 0);
    $checkss = $nv_Request->get_title('checkss', 'get', '');
    if (empty($order_id) or $checkss != md5($client_info['session_id'] . $global_config['sitekey'] . $order_id)) {
        die('NO_' . $lang_module['payment_erorr']);
    } else {
	
		// KIỂM TRA ĐƠN HÀNG NÀY ĐÃ ĐƯỢC THANH TOÁN CHƯA
		$tt_don = $db->query('SELECT transaction_status FROM ' . $db_config['prefix'] . '_' . $module_data . '_orders WHERE order_id=' . $order_id)->fetchColumn();
		if($tt_don == 4)
		die('0');
        // Lay thong tin don hang
        $result = $db->query('SELECT * FROM ' . $db_config['prefix'] . '_' . $module_data . '_orders WHERE order_id=' . $order_id);
        $order_data = $result->fetch();
		
		// Lay tiền trong ví của khach hang
		$result = $db->query('SELECT money, money_km FROM ' . $db_config['prefix'] . '_taikhoan_money WHERE userid = ' . $user_info['userid'])->fetch();
		
		// KIỂM TRA XEM TÀI KHOẢN NÀY CÓ ĐỦ TIỀN ĐỂ THANH TOÁN ĐƠN HÀNG NÀY KHÔNG
		
		// TRỪ TRONG TÀI KHOẢN KHUYẾN MÃI TỐI ĐA 20%
		$km = 0;
		if(($result['money_km'] > 0) and ($result['tru_km'] == 0))
		{
			$tru_km = $order_data['order_total'] * 0.20 ;
			
			if($result['money_km'] >= $tru_km)
			{
				$km = $tru_km;
			}
			else $km = $result['money_km'];
		}
		
		// SỐ TIỀN CÒN LẠI TRỪ VÀO TÀI KHOẢN CHÍNH LÀ $order_data['order_total'] - $km
		$money = $order_data['order_total'] - $km ;
		
		//die('NO_Tiền trong tài khoản chính: '.$km . ', Tài khoản khuyến mãi: '.$money);
		
		if($result['money'] < $money)
		{
			die('NO_' . $lang_module['payment_vitien_erorr'] . '. Tiền trong tài khoản không đủ để thanh toán đơn hàng này. Tiền trong tài khoản chính: '.$result['money'] . ', Tài khoản khuyến mãi: '.$result['money_km']);
		}
		else {
            $transaction_status = 4;
            $payment_id = 0;
            $payment_amount = $order_data['order_total'];
            $payment_data = '';
            $payment = '';
            $userid = $user_info['userid'];

            $transaction_id = $db->insert_id("INSERT INTO " . $db_config['prefix'] . "_" . $module_data . "_transaction (transaction_id, transaction_time, transaction_status, order_id, userid, payment, payment_id, payment_time, payment_amount, payment_data) VALUES (NULL, " . NV_CURRENTTIME . ", '" . $transaction_status . "', '" . $order_id . "', '" . $userid . "', '" . $payment . "', '" . $payment_id . "', " . NV_CURRENTTIME . ", '" . $payment_amount . "', '" . $payment_data . "')");

            if ($transaction_id > 0) {
			
                $db->query("UPDATE " . $db_config['prefix'] . "_" . $module_data . "_orders SET transaction_status=" . $transaction_status . ", transaction_id=" . $transaction_id . ", transaction_count=transaction_count+1 WHERE order_id=" . $order_id);

                // Cap nhat lại tiền trong tài khoản

                $db->query("UPDATE " . $db_config['prefix'] . "_taikhoan_money SET money_km=money_km - " . $km . ", money = money - ". $money .", money_out = money_out + ". $order_data['order_total'] ."  WHERE userid=" . $userid);
				
				// lịch sử giao dịch
				
				$money = $order_data['order_total']; 
				$money_km = 100000;
				$money = preg_replace('/[^0-9]/', '', $money);
				$notice = 'Thanh toán đơn hàng '. nv_number_format($money, nv_get_decimals($pro_config['money_unit']));
				$userid = $userid;
				$typeadd = $nv_Request->get_title('typeadd', 'post', '+');
				$typeadd = ($typeadd == '-') ? $typeadd : "+";
				$transaction_type = 0;
				$contents = "NOT";
				
				$query = "INSERT INTO " . $db_config['prefix'] . "_taikhoan_transaction VALUES (NULL," . NV_CURRENTTIME . "," . $transaction_type . ",1," . doubleval($money) . ",0, 0, 1, 1, " . intval($userid) . "," . intval($userid) . ",0,'','','','','', '', 0," . $db->quote($notice) . ",'','','', 1);";
				// print($userid);die;
				
				 $db->query($query);
               
            }
            $nv_Cache->delMod($module_name);
            die('OK_' . $lang_module['payment_complete']);
        }
    }
}

if (!$pro_config['point_active']) {
    nv_redirect_location(NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name, true);
}

if (!defined('NV_IS_USER')) {
    $redirect = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=point';
    nv_redirect_location(NV_BASE_SITEURL . 'index.php?' . NV_NAME_VARIABLE . '=users&' . NV_OP_VARIABLE . '=login&nv_redirect=' . nv_redirect_encrypt($redirect));
}




$data_content = array();
$point = 0;
$per_page = 20;
$page = $nv_Request->get_int('page', 'get', 1);
$base_url = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op;

// Lay so diem hien tai cua khach hang
$result = $db->query('SELECT point_total FROM ' . $db_config['prefix'] . '_' . $module_data . '_point WHERE userid = ' . $user_info['userid']);
if ($result->rowCount() > 0) {
    $point = $result->fetchColumn();
    $money = $point * $pro_config['point_conversion'];
}

if ($nv_Request->isset_request('paypoint', 'get')) {
    $order_id = $nv_Request->get_int('order_id', 'get', 0);
    $checkss = $nv_Request->get_title('checkss', 'get', '');
    if (empty($order_id) or $checkss != md5($client_info['session_id'] . $global_config['sitekey'] . $order_id)) {
        die('NO_' . $lang_module['payment_erorr']);
    } else {
        // Lay thong tin don hang
        $result = $db->query('SELECT * FROM ' . $db_config['prefix'] . '_' . $module_data . '_orders WHERE order_id=' . $order_id);
        $order_data = $result->fetch();
        $order_point = round($order_data['order_total'] / $pro_config['point_conversion']);
		
		// KIỂM TRA ĐƠN HÀNG NÀY ĐÃ ĐƯỢC THANH TOÁN CHƯA
		$tt_don = $db->query('SELECT transaction_status FROM ' . $db_config['prefix'] . '_' . $module_data . '_orders WHERE order_id=' . $order_id)->fetchColumn();
		if($tt_don == 4)
		die('NO_' . $lang_module['payment_dathanhtoan']);
		
        if (empty($order_data)) {
            die('NO_' . $lang_module['payment_erorr']);
        } elseif ($point < $order_point) {
            die('NO_' . $lang_module['point_payment_error_money']);
        } else {
            $transaction_status = 4;
            $payment_id = 0;
            $payment_amount = 0;
            $payment_data = '';
            $payment = '';
            $userid = $user_info['userid'];

            $transaction_id = $db->insert_id("INSERT INTO " . $db_config['prefix'] . "_" . $module_data . "_transaction (transaction_id, transaction_time, transaction_status, order_id, userid, payment, payment_id, payment_time, payment_amount, payment_data) VALUES (NULL, " . NV_CURRENTTIME . ", '" . $transaction_status . "', '" . $order_id . "', '" . $userid . "', '" . $payment . "', '" . $payment_id . "', " . NV_CURRENTTIME . ", '" . $payment_amount . "', '" . $payment_data . "')");

            if ($transaction_id > 0) {
                $db->query("UPDATE " . $db_config['prefix'] . "_" . $module_data . "_orders SET transaction_status=" . $transaction_status . ", transaction_id=" . $transaction_id . ", transaction_count=transaction_count+1 WHERE order_id=" . $order_id);

                // Cap nhat diem tich luy
				//if($order_data['order_total'] > $pro_config['money_to_point'])
					UpdatePoint($order_data);
				
                $db->query("UPDATE " . $db_config['prefix'] . "_" . $module_data . "_point SET point_total=point_total - " . $order_point . " WHERE userid=" . $userid);
                $db->query("INSERT INTO " . $db_config['prefix'] . "_" . $module_data . "_point_history(userid, order_id, point, time) VALUES (" . $userid . ", " . $order_id . ", -" . $order_point . ", " . NV_CURRENTTIME . ")");
            }
            $nv_Cache->delMod($module_name);
            nv_htmlOutput('OK_' . $lang_module['payment_complete']);
        }
    }
}

$data_content['point'] = $point;
$data_content['money'] = $point * $pro_config['point_conversion'];
$data_content['money'] = nv_number_format($data_content['money'], nv_get_decimals($pro_config['money_unit']));
$data_content['money_unit'] = $pro_config['money_unit'];

// Lich su thuc hien
$db->sqlreset()
  ->select('COUNT(*)')
  ->from($db_config['prefix'] . '_' . $module_data . '_point_history t1')
  ->join('INNER JOIN ' . $db_config['prefix'] . '_' . $module_data . '_orders t2 ON t1.order_id = t2.order_id')
  ->where('userid = ' . $user_info['userid']);

$all_page = $db->query($db->sql())->fetchColumn();

$db->select('t1.*, t2.order_code')
  ->order('id DESC')
  ->limit($per_page)
  ->offset(($page - 1) * $per_page);

$_query = $db->query($db->sql());
$link_module = NV_BASE_SITEURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&amp;" . NV_NAME_VARIABLE . "=" . $module_name;
while ($row = $_query->fetch()) {
    $checkss = md5($row['order_id'] . $global_config['sitekey'] . session_id());
    $row['link'] = $link_module . "&amp;" . NV_OP_VARIABLE . "=payment&amp;order_id=" . $row['order_id'] . "&checkss=" . $checkss;
    $data_content['history'][] = $row;
}

$generate_page = nv_generate_page($base_url, $all_page, $per_page, $page);

$contents = call_user_func('point_info', $data_content, $generate_page);

include NV_ROOTDIR . '/includes/header.php';
echo nv_site_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';
