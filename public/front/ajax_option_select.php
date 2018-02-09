<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

$productcode = $_POST['productcode'];
$option_code = $_POST['option_code'];
$idx = $_POST['idx']; //1차 2차 옵션 idx ( 1차 = 0 , 2차 = 1 )
$returnArr = get_option( $productcode, $option_code, $idx );

echo json_encode( $returnArr );

exit;

?>