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

$page_title = 'Thanh toán online';


$xtpl = new XTemplate('checkout.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file);
$xtpl->assign('LANG', $lang_module);

// LẤY THÔNG TIN GIÁ TIỀN CỦA ĐƠN HÀNG order_id RA
$order_id =$_REQUEST['order_id'];

if($order_id > 0)
{
	$tt = $db->query("SELECT * FROM " . $db_config['prefix'] . "_" . $module_data . "_orders WHERE order_id = ". $order_id)->fetch();
	$tt['tien_thanhtoan'] = $tt['order_total'];
	$tt['tien_thanhtoan_fomat'] = number_format($tt['tien_thanhtoan'],0,",",".");

}

$xtpl->assign('tt', $tt);

if(isset($_POST['nlpayment'])) {
	include(NV_ROOTDIR . '/modules/' . $module_file . '/nv_checkout/config.php');	
	include(NV_ROOTDIR . '/modules/' . $module_file . '/nv_checkout/include/NL_Checkoutv3.php');	
	$nlcheckout= new NL_CheckOutV3(MERCHANT_ID,MERCHANT_PASS,RECEIVER,URL_API);
	$total_amount=$_POST['total_amount'];
	 
	 $array_items[0]= array('item_name1' => 'Product name',
					 'item_quantity1' => 1,
					 'item_amount1' => $total_amount,
					 'item_url1' => 'http://nganluong.vn/');
					 
	$array_items=array();				 
	 $payment_method =$_POST['option_payment'];
	 $order_code =$_POST['order_code'];
	 $bank_code = @$_POST['bankcode'];
	
	 $payment_type ='';
	 $discount_amount =0;
	 $order_description='';
	 $tax_amount=0;
	 $fee_shipping=0;
	 $return_url = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=payment_success';
	 $cancel_url = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=payment_success';
	
	 $buyer_fullname =$_POST['buyer_fullname'];
	 $buyer_email =$_POST['buyer_email'];
	 $buyer_mobile =$_POST['buyer_mobile'];
	 
	 $buyer_address ='';
	 
	 
	 
	
	if($payment_method !='' && $buyer_email !="" && $buyer_mobile !="" && $buyer_fullname !="" && filter_var( $buyer_email, FILTER_VALIDATE_EMAIL )  ){
		if($payment_method =="VISA"){
		
			$nl_result= $nlcheckout->VisaCheckout($order_code,$total_amount,$payment_type,$order_description,$tax_amount,
											  $fee_shipping,$discount_amount,$return_url,$cancel_url,$buyer_fullname,$buyer_email,$buyer_mobile, 
									          $buyer_address,$array_items,$bank_code);
											  
		}elseif($payment_method =="NL"){
			$nl_result= $nlcheckout->NLCheckout($order_code,$total_amount,$payment_type,$order_description,$tax_amount,
												$fee_shipping,$discount_amount,$return_url,$cancel_url,$buyer_fullname,$buyer_email,$buyer_mobile, 
												$buyer_address,$array_items);
												
		}elseif($payment_method =="ATM_ONLINE" && $bank_code !='' ){
			$nl_result= $nlcheckout->BankCheckout($order_code,$total_amount,$bank_code,$payment_type,$order_description,$tax_amount,
												  $fee_shipping,$discount_amount,$return_url,$cancel_url,$buyer_fullname,$buyer_email,$buyer_mobile, 
												  $buyer_address,$array_items) ;
		}
		elseif($payment_method =="NH_OFFLINE"){
				$nl_result= $nlcheckout->officeBankCheckout($order_code, $total_amount, $bank_code, $payment_type, $order_description, $tax_amount, $fee_shipping, $discount_amount, $return_url, $cancel_url, $buyer_fullname, $buyer_email, $buyer_mobile, $buyer_address, $array_items);
			}
		elseif($payment_method =="ATM_OFFLINE"){
				$nl_result= $nlcheckout->BankOfflineCheckout($order_code, $total_amount, $bank_code, $payment_type, $order_description, $tax_amount, $fee_shipping, $discount_amount, $return_url, $cancel_url, $buyer_fullname, $buyer_email, $buyer_mobile, $buyer_address, $array_items);
				
			}
		elseif($payment_method =="IB_ONLINE"){
				$nl_result= $nlcheckout->IBCheckout($order_code, $total_amount, $bank_code, $payment_type, $order_description, $tax_amount, $fee_shipping, $discount_amount, $return_url, $cancel_url, $buyer_fullname, $buyer_email, $buyer_mobile, $buyer_address, $array_items);
			}
		elseif ($payment_method == "CREDIT_CARD_PREPAID") {

			$nl_result = $nlcheckout->PrepaidVisaCheckout($order_code, $total_amount, $payment_type, $order_description, $tax_amount, $fee_shipping, $discount_amount, $return_url, $cancel_url, $buyer_fullname, $buyer_email, $buyer_mobile, $buyer_address, $array_items, $bank_code);
		}
		//var_dump($nl_result); die;
		if ($nl_result->error_code =='00'){
		
		//Cập nhât order với token  $nl_result->token để sử dụng check hoàn thành sau này
		?>
		<script type="text/javascript">
		<!--
		window.location = "<?php echo(string)$nl_result->checkout_url; // .'&lang=en' chuyển mặc định tiếng anh  ?>"
		//-->
		</script>
		<?PHP
		}else{
			echo $nl_result->error_message;
		}
			
	}else{
		echo "<h3> Bạn chưa nhập đủ thông tin khách hàng </h3>";
	}
 }	
 

$xtpl->parse('main');
$contents = $xtpl->text('main');

include NV_ROOTDIR . '/includes/header.php';
echo nv_site_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';