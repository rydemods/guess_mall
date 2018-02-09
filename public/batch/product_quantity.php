<?php
exit;
@set_time_limit(0);
header("Content-type: text/html; charset=utf-8");
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

$sql = "
WITH quantity_zero AS ( 
 SELECT pridx, productcode, productname, quantity FROM tblproduct 
 WHERE ( quantity = 0 OR quantity IS NULL ) 
) 
SELECT 
qz.pridx, qz.productcode, qz.productname, qz.quantity, 
si.it_stock_qty, si.it_noti_qty 
FROM quantity_zero qz 
JOIN g5_shop_item si ON si.it_id = qz.productcode 
WHERE it_stock_qty > 0 
";

$result = pmysql_query( $sql, get_db_conn() );

$beforText = '=========================='.date("Y-m-d H:i:s")."=============================\n";
$upQryText = '=========================='.date("Y-m-d H:i:s")."=============================\n";
$cnt = 0;
$upCnt = 0;
$failCnt = 0;
while( $row = pmysql_fetch_array( $result ) ){
	$cnt++;
	$beforText.= '순번 '.$cnt."\n";
	$beforText.= 'productcode  = '.$row['productcode']."\n";
	$beforText.= 'productname  = '.$row['c_category']."\n";
	$beforText.= 'quantity     = '.$row['quantity']."\n";
	$beforText.= 'it_stock_qty = '.$row['it_stock_qty']."\n";

	$upQry = "UPDATE tblproduct SET quantity = ".$row['it_stock_qty']." WHERE pridx = '".$row['pridx']."'";
	$upQryText.= $upQry."\n";
	pmysql_query( $upQry, get_db_conn() );
	if( !pmysql_error() ){
		$upCnt++;
		$beforText.= "type         = SUCCESS! \n";
	} else {
		$failCnt++;
		$beforText.= "type         = FAIL! \n";
	}
	$beforText.= "-----------------------------------------------------\n";
}
pmysql_free_result( $result );


echo '상품 갯수: '.$cnt.'<br>';
echo 'update한 상품 갯수: '.$upCnt.'<br>';
echo '실패한 상품 갯수'.$failCnt.'<br>';

#상품 수량 정보
$beforText.= "\n";
$befor_f = fopen('product_before_Qty_'.date("Ymd").'.txt','a');
fwrite($befor_f, $beforText );
fclose($befor_f);
chmod("product_before_Qty_".date("Ymd").".txt",0777);

#상품수량 Update 쿼리
$upQryText.= "\n";
$upQrt_f = fopen('productUpdateQty_Qry_'.date("Ymd").'.txt','a');
fwrite($upQrt_f, $upQryText );
fclose($upQrt_f);
chmod("productUpdateQty_Qry_".date("Ymd").".txt",0777);


?>