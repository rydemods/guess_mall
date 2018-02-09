<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
include_once($Dir."lib/coupon.class.php");

$mode        = $_POST['mode']; 
$productcode = $_POST['productcode']; //상품코드
$sellprice   = $_POST['sellprice']; //상품가격
$ci_no       = $_POST['ci_no']; // 쿠폰고유번호

$_CouponInfo = new CouponInfo();
$dc_arr = array();
$dc_arr = $_CouponInfo->discountPrice( $sellprice, $ci_no );

echo json_encode( $dc_arr );
?>