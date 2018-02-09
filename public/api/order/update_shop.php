<?php

include_once($_SERVER[DOCUMENT_ROOT]."/api/config.php");
include_once($_SERVER[DOCUMENT_ROOT]."/lib/lib_erp.php");

$resultArr 	= array();

$shopCd     = $_POST["shop_code"];
$docId     = $_POST["order_code"];
$ItemNo     = $_POST["itemno"];


//$conn = oci_connect("SMK_ONLINE", "SMK_ONLINE_0987", "1.209.88.42/ORA11", "AL32UTF8");
$conn = GetErpDBConn();

$sql = "
		UPDATE ".$erp_account.".IF_HOTT_ONLINE_ORDER
		SET
		IF_DIV = 'U'
		,RECVDT = null
		,SHOPCD = '".$shopCd."'
		WHERE DOCID = '".$docId."' AND ITEMNO = '".$ItemNo."'";


/*$smt_erp = oci_parse($conn,$sql);
$stid   = oci_execute($smt_erp);
if(!$stid) $error = oci_error(); */


if ( !isset($error) ) {
    $code = 1;
    $message = $error;
} else {
	$code = 0;
	$message = "success";
}

$resultArr["code"]      = $code;
$resultArr["message"]   = $message;

echo json_encode($resultArr);

?>
