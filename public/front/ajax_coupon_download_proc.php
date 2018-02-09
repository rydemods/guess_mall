<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
include_once($Dir."lib/coupon.class.php");

$return_arr['success']   = false;
$return_arr['msg']      = '발급 가능한 쿠폰이 아닙니다.';
	
//$coupon	= '59154325';
//$encrypt_coupon	= encrypt_md5("COUPON|6|".$coupon,"*ghkddnjsrl*");
$encrypt_coupon	= $_POST['coupon'];
//$decrypt_coupon	= decrypt_authkey($encrypt_coupon);
$decrypt_coupon	= urldecode($encrypt_coupon);
$exp_coupon			= explode("|", $decrypt_coupon);

//exdebug($exp_coupon);

$memid	= $_ShopInfo->getMemid();
//echo $memid;
//exit;

#쿠폰 설정 
$_CouponInfo = new CouponInfo( $exp_coupon[1] ); 
//exdebug($_CouponInfo);

#쿠폰 확인 
$msg = $_CouponInfo->search_coupon( $exp_coupon[2], $memid ); 
if( $msg == '0' ){
	$return_arr['msg']			= "해당하는 쿠폰이 없습니다.";
} else if( $msg == '4' ) {
	$return_arr['msg']			= "쿠폰이 이미 발급되었습니다. MY PAGE>쿠폰에서 확인할 수 있습니다.";
} else if( $msg == '5' ) {
	$return_arr['msg']			= "쿠폰이 이미 발급되었습니다. MY PAGE>쿠폰에서 확인할 수 있습니다.";
} else if( $msg == '1' ) {
	$_CouponInfo->set_couponissue( $memid ); 
	if ($_CouponInfo->issue_type != '0') {
		#insert 설정 
		$return_data = $_CouponInfo->insert_couponissue(); 
		if( $return_data[0] === 0 ) {
			$return_arr['success']	= true;
			$return_arr['msg']			= "쿠폰이 발급되었습니다.";
		} else {
			$return_arr['msg']			= "쿠폰이 발급되지 않았습니다.\\n관리자에게 문의하세요.1";
		}
	} else {
		$return_arr['msg']			= "쿠폰이 발급되지 않았습니다.\\n관리자에게 문의하세요.2";
	}
}

echo json_encode( $return_arr );

?>