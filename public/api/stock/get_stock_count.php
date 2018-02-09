<?php
$Dir = $_SERVER[DOCUMENT_ROOT]."/";
include_once($Dir."/lib/init.php");
include_once($Dir."/lib/lib.php");
include_once($Dir."/lib/sync.class.php");
include_once($Dir."/lib/shopdata.php");
//include_once($_SERVER[DOCUMENT_ROOT]."/api/config.php");
include_once($_SERVER[DOCUMENT_ROOT]."/lib/lib_erp.php");

$resultArr 	= array();

$shopCd     = $_POST["shop_code"];
$prodCd     = $_POST["prod_code"];
$colorCd    = $_POST["color"];
$sizeCd     = $_POST["size"];
$qty 		= $_POST["qty"];
$conn		= null;

$noShopCd = $_POST["no_shop_code"];
/*
$prodCd="BJP22600";
$colorCd="WH";
$sizeCd="55";
$shopCd="G9043B";
*/

try {
	//$conn = oci_connect("SMK_ONLINE", "SMK_ONLINE_0987", "1.209.88.42/ORA11", "AL32UTF8");
	/*
    $conn = GetErpDBConn();

	$sql = "SELECT  a.SHOPCD, a.PRODCD, a.SIZECD, a.AVAILQTY   
			FROM    ".$erp_account.".HOTT_ON_STOCK_V a 
			WHERE   a.PRODCD = '".$prodCd."' AND a.COLORCD = '".$colorCd."' AND a.SIZECD = '".$sizeCd."'";
		
	if(isset($shopCd)) $sql .= " AND a.SHOPCD = '".$shopCd."'";
	if(isset($noShopCd)) $sql .= " AND a.SHOPCD NOT IN (".$noShopCd.")";
	if(isset($qty)) $sql .= " AND a.AVAILQTY > ".$qty;

	$sql .= " ORDER BY a.AVAILQTY DESC";


	$smt_stock = oci_parse($conn, $sql);
	oci_execute($smt_stock);
	//exdebug($sql);

	

	$shop_stock = array();
	
	while($data = oci_fetch_array($smt_stock, OCI_BOTH+OCI_RETURN_NULLS+OCI_RETURN_LOBS)) {
		$shop_stock[$i][shop_code]         = $data[SHOPCD];
		$shop_stock[$i][prod_code]         = $data[PRODCD];
		$shop_stock[$i][size_code]         = $data[SIZECD];
		$shop_stock[$i][qty]       		   = $data[AVAILQTY];
		$i++;
	}


	oci_free_statement($smt_stock);
	oci_close($conn);
*/
	$i = 0;
	$data=getErpProdShopStock_Part($prodCd, $colorCd, $sizeCd, $shopCd);

	if ( $data[sumqty] == 0 ) {
		if(isset($shopCd)) {
			$shop_stock[$i][shop_code]     	= $shopCd;
			$shop_stock[$i][prod_code]     	= $prodCd;
			$shop_stock[$i][size_code]     	= $sizeCd;
			$shop_stock[$i][qty]       		= "0";
			$resultArr["result"]    = $shop_stock;
			$code = 0;
			$length = 0;
			$message = "데이터가 존재하지 않습니다.";
		} else {
			$code = 0;
			$length = 0;
			$message = "데이터가 존재하지 않습니다.";
		}
	} else {
		$shop_stock[$i][shop_code]         = $shopCd;
		$shop_stock[$i][prod_code]         = $prodCd;
		$shop_stock[$i][size_code]         = $sizeCd;
		$shop_stock[$i][qty]       		   = $data[sumqty];

		$code = 0;
		$message = "success";
		$length = count($shop_stock);
		$resultArr["result"]    = $shop_stock;
	}
} catch (Exception $e) {
	$code = 1;
	$length = 0;
	$message = "에러발생";	
	
	if($conn != null)oci_close($conn);
} 

$resultArr["code"]      = $code;
$resultArr["message"]   = $message;
$resultArr["length"]	= $length;

echo json_encode($resultArr);

?>
