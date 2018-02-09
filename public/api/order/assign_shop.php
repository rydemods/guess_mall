<?php
/**
 매장정보를 재고 있는 매장으로 배정한다.
 재고가 없으면 주문 취소요청한다.
*/

include_once($_SERVER[DOCUMENT_ROOT]."/api/config.php");
include_once($_SERVER[DOCUMENT_ROOT]."/lib/lib_erp.php");

$resultArr 	= array();

//$docId     	= $_POST["order_code"];
//$ItemNo     = $_POST["itemno"];
$prodCd    = $_POST["prod_code"];
$colorCd    = $_POST["color"];
$sizeCd     = $_POST["size"];
$qty 		= $_POST["qty"];
$shopCd = null;

//$conn = oci_connect("SMK_ONLINE", "SMK_ONLINE_0987", "1.209.88.42/ORA11", "AL32UTF8");
$conn = GetErpDBConn();

//재고가 해당 품목의 많은 매장을 찾는다.
$sql = "SELECT  a.SHOPCD
		FROM    ".$erp_account.".HOTT_ON_STOCK_V a 
		WHERE   a.PRODCD = '".$prodCd."' AND a.COLORCD = '".$colorCd."' AND a.SIZECD = '".$sizeCd."'";

if(isset($qty)) $sql .= " AND a.AVAILQTY >= ".$qty;
$sql .= " ORDER BY a.AVAILQTY DESC";

$smt_stock = oci_parse($conn, $sql);
oci_execute($smt_stock);
//exdebug($sql);

if($data = oci_fetch_array($smt_stock, OCI_BOTH+OCI_RETURN_NULLS+OCI_RETURN_LOBS)) {
	$shopCd = $data[AVAILQTY];
} 

oci_free_statement($smt_stock);

/*
//재고 많은 매장이 있으면 해당매장으로 출고지를 변경하고 아니면 출고지를 null 변경
$sql = "UPDATE SMK_ERP.IF_HOTT_ONLINE_ORDER
		SET
		IF_DIV = 'U'
		,RECVDT = null
		,SHOPCD = '". (isset($shopCd) ? $shopCd : "") ."'
		WHERE DOCID = '".$docId."' AND ITEMNO = '".$ItemNo."'";


$smt_stock = oci_parse($conn,$sql);
$stid   = oci_execute($smt_stock);
if(!$stid) $error = oci_error(); 


oci_free_statement($smt_stock);
*/
oci_close($conn);


if ( isset($error) ) {
    $resultArr["code"] = 1;
    $resultArr["message"] = $error;
} else {
	if(isset($shopCd)) {
		$resultArr["code"] = 1;
		$resultArr["message"] = "데이터가 존재하지 않습니다.";
	} else {
		$resultArr["code"] = 0;
		$resultArr["message"] = "success";
		$resultArr["result"][SHOPCD] = $shopCd;
	}
}

echo json_encode($resultArr);

?>
