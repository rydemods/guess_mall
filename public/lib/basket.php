<?php

//$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

$imagepath_product = $Dir.DataDir.'shopimages/product/';
$mode = $_POST['mode'];
$productcode = $_POST['productcode'];
$productcodes = $_POST['productcodes'];

//장바구니 인증키 확인
if(ord($_ShopInfo->getTempkey())==0 || $_ShopInfo->getTempkey()=="deleted") {
	$_ShopInfo->setTempkey($_data->ETCTYPE["BASKETTIME"]);
} else {
	//인증키가 다를경우가 발생 - 아이디가 같을경우 인증키 업데이트한다.(2015.11.18 - 김재수)
	$cok_sql		= "SELECT tempkey FROM tblbasket WHERE id = '".$_ShopInfo->getMemid()."' LIMIT 1";
	$cok_result		= pmysql_query($cok_sql,get_db_conn());
	$countOldkey = pmysql_fetch_object($cok_result);
	if($countOldkey->tempkey != $_ShopInfo->getTempkey()){
		$upNewQuery = "UPDATE tblbasket SET tempkey = '".$_ShopInfo->getTempkey()."' WHERE tempkey='".$countOldkey->tempkey."'";
		pmysql_query($upNewQuery);
	}
	
}
// 바로구매 상품 UPDATE ( 상품 주문중이 아닐경우 )
if( strpos( $_SERVER['HTTP_REFERER'], 'order.php' ) === false ) {
	list($countOldItem) = pmysql_fetch("SELECT count(basketidx) FROM tblbasket WHERE tempkey = '".$_ShopInfo->getTempkeySelectItem()."'");
	if($countOldItem > 0){
		$selectItemQuery = "UPDATE tblbasket SET tempkey = '".$_ShopInfo->getTempkey()."' WHERE tempkey='".$_ShopInfo->getTempkeySelectItem()."'";
		pmysql_query($selectItemQuery);
	}
}

// 장바구니 삭제
if( $mode == 'delete' ){
	if( strlen( $productcode ) > 0 ){
		$sql = "DELETE FROM tblbasket WHERE productcode = '".$productcode."' AND tempkey = '".$_ShopInfo->getTempkey()."' ";
		pmysql_query($sql,get_db_conn());
		if( pmysql_error() ){
			echo '<script>';
			echo "	alert('해당 상품이 삭제되었습니다.')";
			echo '</script>';
		} else {
			echo '<script>';
			echo "	alert('장바구니 삭제가 실패하였습니다.\n다시시도해 주세요.')";
			echo '</script>';
		}
	}
}
// 장바구니 전체 삭제
if( $mode == 'delete_all' ){
	$sql = "DELETE FROM tblbasket WHERE tempkey = '".$_ShopInfo->getTempkey()."' ";
	pmysql_query($sql,get_db_conn());
	if( !pmysql_error() ){
		echo '<script>';
		echo "	alert('전체 상품이 삭제되었습니다.')";
		echo '</script>';
	} else {
		echo '<script>';
		echo "	alert('장바구니 삭제가 실패하였습니다.\n다시시도해 주세요.')";
		echo '</script>';
	}
}

if( $mode == 'select_delete' ){
	$productcodes = substr( $productcodes, 0, -1 );
	$tempCodes = explode( '|', $productcodes );
	$implodeCodes = implode( "', '", $tempCodes );
	$sql = "DELETE FROM tblbasket WHERE productcode IN ('".$implodeCodes."') AND tempkey = '".$_ShopInfo->getTempkey()."' ";
	pmysql_query($sql,get_db_conn());
	if( !pmysql_error() ){
		echo '<script>';
		echo "	alert('상품이 삭제되었습니다.')";
		echo '</script>';
	} else {
		echo '<script>';
		echo "	alert('장바구니 삭제가 실패하였습니다.');";
		echo '</script>';
	}
}

# 장바구니 상품정보 가져오기
$prSql = "SELECT bk.productcode, bk.tempkey, SUM(bk.quantity) AS quantity, pr.productname, pr.sellprice, ";
$prSql.= "pr.option1, pr.option2, pr.tinyimage, pr.supply_subject ";
$prSql.= "FROM tblbasket bk ";
$prSql.= "LEFT JOIN tblproduct pr ON bk.productcode = pr.productcode ";
$prSql.= "WHERE bk.tempkey = '".$_ShopInfo->getTempkey()."' ";
$prSql.= "GROUP BY bk.tempkey, bk.productcode , pr.productname, pr.sellprice, pr.option1, pr.option2, ";
$prSql.= "pr.tinyimage, pr.supply_subject ";
$prRes = pmysql_query( $prSql, get_db_conn() );
while( $prRow = pmysql_fetch_object( $prRes ) ){
	//장바구니 상품을 담는다
	if( is_file($imagepath_product.$prRow->tinyimage) ){
		$prRow->tinyimage = $imagepath_product.$prRow->tinyimage;
	} else {
		$prRow->tinyimage = $Dir."images/common/noimage.gif";
	}
	$basket[] = $prRow;

	# 장바구니 옵션 및 수량 가져오기
	$sql = "SELECT bk.basketidx, bk.productcode, bk.opt1_idx, bk.opt2_idx, bk.optidxs, ";
	$sql.= "bk.quantity, bk.optionarr, bk.quantityarr, bk.pricearr, bk.op_type, ";
	$sql.= "bk.assemble_list, bk.assemble_idx, bk.package_idx, ";
	$sql.= "op.option_num, op.option_code, op.option_price, op.option_quantity ";
	$sql.= "FROM tblbasket bk ";
	$sql.= "LEFT JOIN tblproduct_option op ON ( bk.optionarr = op.option_code AND bk.productcode = op.productcode )";
	$sql.= "WHERE bk.tempkey='".$_ShopInfo->getTempkey()."' ";
	$sql.= "AND bk.productcode = '".$prRow->productcode."' ";
	$sql.= "ORDER BY bk.tempkey ASC, bk.productcode ASC, bk.basketidx ASC ";
	$result = pmysql_query( $sql, get_db_conn() );
	while( $row = pmysql_fetch_object( $result ) ) {
		$option[$row->productcode][] = $row;
	}
	pmysql_free_result( $result );
	
}
pmysql_free_result( $prRes );

?>