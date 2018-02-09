<?php
$Dir = $_SERVER[DOCUMENT_ROOT]."/";
include_once($Dir."/lib/init.php");
include_once($Dir."/lib/lib.php");
include_once($Dir."/lib/sync.class.php");
include_once($Dir."/lib/shopdata.php");
//include_once($_SERVER[DOCUMENT_ROOT]."/api/config.php");
include_once($_SERVER[DOCUMENT_ROOT]."/lib/lib_erp.php");

$resultArr 	= array();

$prodCd     = $_POST["prod_code"];
$colorCd    = $_POST["color"];
$sizeCd     = $_POST["size"];
$qty 		= $_POST["qty"];


//$conn = oci_connect("SMK_ONLINE", "SMK_ONLINE_0987", "1.209.88.42/ORA11", "AL32UTF8");
/*
$conn = GetErpDBConn();

$sql = "SELECT  a.SHOPCD   
		FROM    ".$erp_account.".HOTT_ON_STOCK_V a 
		WHERE   a.PRODCD = '".$prodCd."' AND a.COLORCD = '".$colorCd."' AND a.SIZECD = '".$sizeCd."' AND a.AVAILQTY >= ".$qty." 
		ORDER BY a.AVAILQTY DESC";

$smt_stock = oci_parse($conn, $sql);
oci_execute($smt_stock);
//print_r($sql);

$shops = array();

while($data = oci_fetch_array($smt_stock, OCI_BOTH+OCI_RETURN_NULLS+OCI_RETURN_LOBS)) {
	$shops[]  = $data[SHOPCD];
}


oci_free_statement($smt_stock);
oci_close($conn);
*/

$data=getErpProdShopStock($prodCd, $colorCd, $sizeCd);

$i=0;
for($i=0;$i<count($data[shopcd]);$i++){
	if($data[availqty][$i]>0){
		$shops[]  = $data[shopcd][$i];
		$availqty[]  = $data[availqty][$i];
	}
}

if ( count($shops) == 0 ) {
    $code = 1;
	$length = 0;
    $message = "데이터가 존재하지 않습니다.";
} else {
	$code = 0;
	$length = count($shops);
	$message = "success";
	$resultArr["result"]    = $shops;
	$resultArr["result2"]    = $availqty;
}

$resultArr["code"]      = $code;
$resultArr["length"]	= $length;
$resultArr["message"]   = $message;

echo json_encode($resultArr);

?>
