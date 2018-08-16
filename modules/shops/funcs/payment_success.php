<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC (contact@vinades.vn)
 * @Copyright (C) 2017 VINADES.,JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate 04/18/2017 09:47
 */
 
if (!defined('NV_IS_MOD_SHOPS')) {
    die('Stop!!!');
}

$page_title = 'Thanh toán online thành công';


$xtpl = new XTemplate('payment_success.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file);
$xtpl->assign('LANG', $lang_module);
$xtpl->assign('FORM_ACTION', NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op . '&amp;catid=' . $data['catid'] . '&amp;parentid=' . $data['parentid']);

include(NV_ROOTDIR . '/modules/' . $module_file . '/nv_checkout/config.php');	
	include(NV_ROOTDIR . '/modules/' . $module_file . '/nv_checkout/include/NL_Checkoutv3.php');

$nlcheckout= new NL_CheckOutV3(MERCHANT_ID,MERCHANT_PASS,RECEIVER,URL_API);
$nl_result = $nlcheckout->GetTransactionDetail($_GET['token']);



if($nl_result){
	$nl_errorcode           = (string)$nl_result->error_code;
	$nl_transaction_status  = (string)$nl_result->transaction_status;
	if($nl_errorcode == '00') {
		if($nl_transaction_status == '00') {
			//trạng thái thanh toán thành công
			
			// CẬP NHẬT ĐƠN HÀNG ĐÃ THANH TOÁN THÀNH CÔNG
			$order_id = $nl_result['order_code'];
			$db->query("UPDATE " . $db_config['prefix'] . "_" . $module_data . "_orders SET transaction_status= 2 WHERE order_id=" . $order_id);
			
            $xtpl->parse('main.thanhcong');
		}
	}else{
	
		$xtpl->assign('loi', $nlcheckout->GetErrorMessage($nl_errorcode));
		$xtpl->parse('main.thatbai');
	}
}

$xtpl->parse('main');
$contents = $xtpl->text('main');

include NV_ROOTDIR . '/includes/header.php';
echo nv_site_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';