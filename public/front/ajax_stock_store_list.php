<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

/*$_REQUEST["prodcd"]	= "BJP22600";
$_REQUEST["colorcd"]	= "WH";
$_REQUEST["size"]	= "55";
$_REQUEST["area_code"]	= "9";
$_REQUEST["search"]	= "";
$_REQUEST["delivery_type"]	= "2";
$_REQUEST["option_quantity"]	= "1";*/

$prodcd				= $_REQUEST["prodcd"];
$colorcd				= $_REQUEST["colorcd"];
$sizecd					= $_REQUEST["size"];
$area_code			= $_REQUEST["area_code"];
$search					= $_REQUEST["search"];
$delivery_type		= $_REQUEST["delivery_type"];
$option_quantity	= $_REQUEST["option_quantity"];
$delivery_type_name = $_REQUEST["delivery_type_name"];
//exdebug($_REQUEST);

$JSON			= "";
$store_cnt		= 0;

$arrWhere = array();
array_push($arrWhere, "view = '0'");

if ( $search != '' ) {
    array_push($arrWhere, "lower(name) LIKE lower('%".$search."%')");
}
if ( !empty($area_code) ) {
    array_push($arrWhere, "area_code = {$area_code}");
}
if ( !empty($category_code) ) {
    array_push($arrWhere, "category = '{$category_code}'");
}

if ( $delivery_type == '2' ){
    array_push($arrWhere, "address like '%서울%'");
}

if ($prodcd && $colorcd) {
	$shopstock = getErpProdShopStock($prodcd, $colorcd, $sizecd, $delivery_type_name);	// 상품 해당 사이즈의 매장별  재고 구하기(실시간)
	//$shopstock = getErpProdShopStock_Type($prodcd, $colorcd, $sizecd, $delivery_type_name);	// 상품 해당 사이즈의 매장별  재고 구하기(실시간)
// 	if(substr($_SERVER['REMOTE_ADDR'],0,10) == '218.234.32'){
// 	echo $prodcd."@@@";
// 	exdebug($shopstock);
// 	}
	//exit;
	//echo count($shopstock);
	
	if (count($shopstock) > 0) {
		$store_codes			= $shopstock["shopcd"];
		$stock_qtys			= $shopstock["availqty"];
		$JSON .= "[ ";
		foreach($store_codes as $scKey => $scVal) {
			$store_code		= $scVal;
			$stock_qty			= $stock_qtys[$scKey];
//			if ($stock_qty > 0 ) {	// 기존 소스
			if ($stock_qty > 0 && $store_code != 'A1801B') {
				//$shopRealtimeStock = getErpPriceNStock($prodcd, $colorcd, $sizecd, $store_code);

				if ($stock_qty > 0 && $stock_qty >= $option_quantity) {

					$where	= "";
					if ( count($arrWhere) >= 1 ) {
						$where = " WHERE " . implode(" AND ", $arrWhere) ." AND store_code = '{$store_code}' ";
					}
					
					$sql  = "SELECT tblResult.*, ";
					$sql .= "(SELECT brandname FROM tblproductbrand WHERE vender = tblResult.vendor) as com_name ";

					$sql .= "FROM (SELECT * FROM tblstore " . $where . "  ORDER BY sort asc, sno desc ) AS tblResult ";
					//echo $sql;
					//AND display_yn='Y'
					//2016-10-05 libe90 사용가능매장만 나오도록 수정
					//exdebug($_REQUEST);
						//exdebug($sql);
					$result	= pmysql_query($sql, get_db_conn());
					$res		= pmysql_fetch_array($result);
					if ($res) {
						$JSON .= "{";
						$JSON .= "\"number\": \"".($store_cnt + 1)."\", " ;
						$JSON .= "\"storeName\": \"".$res['name']."\", " ;
						$JSON .= "\"storeAddress\": \"".$res['address']."\", " ;
						$JSON .= "\"storeTel\": \"".$res['phone']."\", " ;
						$JSON .= "\"storeXY\": \"".$res['coordinate']."\", " ;
						if($res['filename']){//매장이미지 존재하는지 체크해서 넘겨줌. 없으면 다른 임시 이미지
							$JSON .= "\"filename\": \"".$res['map_file_name']."\", " ;
						}else{
							$JSON .= "\"filename\": \"".'h1_logo.jpg'."\", " ;
						}

						$JSON .= "\"storeOfficeHour\": \"" . $res['stime'] . "~" . $res['etime'] . "\", " ;
						$JSON .= "\"storeCategory\": \"" . $store_category[$res['category']] . "\", " ;
						$JSON .= "\"storeVendorName\": \"" . $res['com_name'] . "\", " ;
						$JSON .= "\"storeCode\": \"" . $res['store_code'] . "\", " ;
						$JSON .= "\"remainQty\": \"" . $stock_qty . "\", " ;
						$JSON .= "\"pickupYn\": \"" . $res['pickup_yn'] . "\", " ;	//2016-10-05 libe90 매장픽업가능여부
						$JSON .= "\"deliveryYn\": \"" . $res['delivery_yn'] . "\", " ;	//2016-10-05 libe90 매장발송가능여부
						$JSON .= "\"dayDeliveryYn\": \"" . $res['day_delivery_yn'] . "\", " ;	//2016-10-05 libe90 당일배송가능여부
						$JSON .= "\"sortData\": \"" . $res['sort'] . "\", " ;	//2016-10-06 libe90 정렬기능
						$JSON .= "\"storeAreaCode\": \"" . $res['area_code'] . "\" " ;
						$JSON .= "}";

						$JSON .= ",";
						$store_cnt++;
					}
					pmysql_free_result($result);
				}
			}
		}
		$JSON = trim($JSON, ",");

		$JSON .= "]\n";
	}
}

// 결과가 없을때를 처리한다.
if($store_cnt == 0) {
    //echo("[noRecord]");
    exit;
}

Header("Cache-Control:no-cache");
Header("Pragma: no-cache");
header('Content-Type: application/json; charset=utf-8');
echo($JSON);
exit;
?>