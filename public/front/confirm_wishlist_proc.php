<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

#상품의 옵션정보를 가져옴
function select_options( $productcode, $option_code = '', $option_type = 0 )
{
	$sql = "SELECT option_num, option_code, productcode, option_price, option_quantity, option_quantity_noti, option_type, option_use  ";
	$sql.= "FROM tblproduct_option WHERE productcode = '".$productcode."' AND option_type = '".$option_type."' AND option_use = 1 ";
	if( strlen( $option_code ) > 0 ) $sql.= "AND option_code = '".$option_code."' ";
	$sql.= "ORDER BY option_num ASC ";
	$result = pmysql_query( $sql, get_db_conn() );
	while( $row = pmysql_fetch_object( $result ) ){
		$select_options[] = $row;
	}
	pmysql_free_result( $result );
	
	return $select_options;
}

#장바구니에 넣을 상품정보를 가져옴
function select_product( $productcode )
{
	$sql = "SELECT pridx, productcode, productname, sellprice, consumerprice, ";
	$sql.= "buyprice, reserve, reservetype, quantity, option1, option2, addcode, ";
	$sql.= "maximage, minimage, tinyimage, deli, deli_price, display, selfcode, ";
	$sql.= "vender, min_quantity, max_quantity, setquota, supply_subject, deli_qty ";
	//$sql.= "detail_deli, deli_min_price, deli_package ";
	$sql.= "FROM tblproduct WHERE productcode = '".$productcode."' ";
	$result = pmysql_query( $sql, get_db_conn() );
	$row = pmysql_fetch_object( $result );
	$select_product = $row;
	pmysql_free_result( $result );

	return $select_product;
}

#옵션 수량을 체크함
function check_option_quantity( $option, $option_quantity )
{
	if( $option->option_quantity <= 0 ) {
		return false;
	} else if(  $option->option_quantity < $option_quantity ) {
		return false;
	} else {
		return true;
	}
	//else $this->success = true;
}

#상품 수량을 체크함
function check_product_quantity( $product, $quantity )
{
	if( $product->quantity <= 0 ) return false;
	else if(  $product->quantity < $quantity ) return false;
	else return true;
}

#상품 그룹을 체크함
function check_product_group( $productcode )
{
	global $_ShopInfo;

	//상품의 해당 카테고리를 가져옴
	$cate_sql = "SELECT c_category FROM tblproductlink WHERE c_productcode = '".$productcode."' AND c_maincate = 1 ";
	$cate_res = pmysql_query( $cate_sql, get_db_conn() );
	$cate_row = pmysql_fetch_object( $cate_res );
	pmysql_free_result( $cate_res );

	if( strlen( $cate_row->c_category ) == 0 ) {
		return false;
	} else {
		$sql = "SELECT group_code FROM tblproductcode WHERE code_a||code_b||code_c||code_d = '".$cate_row->c_category."' ";
		$result = pmysql_query( $sql, get_db_conn() );
		$row = pmysql_fetch_object( $result );
		if($row->group_code=="NO") {	//숨김 분류
			return false;
		} elseif($row->group_code=="ALL" && strlen($_ShopInfo->getMemid())==0) {	//회원만 접근가능
			return false;
		} elseif(ord($row->group_code) && $row->group_code!="ALL" && $row->group_code!=$_ShopInfo->getMemgroup()) {	//그룹회원만 접근
			return false;
		} else {
			return true;
		}
		pmysql_free_result( $result );
	}
}

#상품 조건부 수량을 체크함
function check_product( $product, $quantity )
{
	if( $product->min_quantity != 0 && $product->min_quantity > 1 && $quantity < $product->min_quantity ){ //최소 구매수량
		return false;
	}
	
		if( $product->max_quantity > 0 && $quantity > $product->max_quantity ){ //최대 구매수량
		return false;
	}

	return true;

}

$productcode = $_POST['productcode']; //상품코드
$option_code = $_POST['option_code']; // 옵션명
//$quantity = $_POST['quantity']; // 상품수량
$option_type = $_POST['option_type'];

$spl_option = $_POST['spl_option']; //추가옵션
$mode = $_POST['mode'];
$errMsg = '';
$addQueryCol = '';
$addQueryVal = '';

if( $mode == 'insert' ){
	$wish_item_cnt = 0;
	if( $option_code != '' ){
		$tmp_opcode = explode( chr(30), $option_code );
		$opt1_code = '';
		$opt2_code = '';
		if( $tmp_opcode[0] ) $opt1_code = $tmp_opcode[0];
		if( $tmp_opcode[1] ) $opt2_code = $tmp_opcode[1];
	}
	$check_sql = "SELECT COUNT(*) AS cnt FROM tblwishlist WHERE productcode = '".$productcode."' AND id = '".$_ShopInfo->getMemid()."' ";
	$check_sql.= " AND opt1_idx ='".$opt1_code."' AND opt2_idx = '".$opt2_code."' ";
	$check_res = pmysql_query( $check_sql, get_db_conn() );
	while( $check_row = pmysql_fetch_object( $check_res ) ){
		$wish_item_cnt = $check_row->cnt;
	}
	pmysql_free_result( $check_res );

	

	if( !check_product_group( $productcode ) ) {
		$errMsg = '해상 상품이 존재하지 않습니다.';
	}

	if( $wish_item_cnt == 0 && strlen( $errMsg ) == 0 ){
		#옵션이 존재할 경우
		//데코엔이에서는 옵션과 상관없이 위시리스트에 상품이 등록됨 2016-05-04 유동혁
		/*
		if( $option_code != '' ){
			$select_option = select_options( $productcode, $option_code );
			if( count( $select_option ) == 0 ){
				$errMsg = '해상 상품이 존재하지 않습니다.';
			} else {
				$tmp_option = explode( chr(30), $option_code );
				$opt1_idx = '';
				$opt2_idx = '';
				if( $tmp_option[0] ) $opt1_idx = $tmp_option[0];
				if( $tmp_option[1] ) $opt2_idx = $tmp_option[1];

				//추가 컬럼 add되는 내용이기 , 를 앞에찍음
				$addQueryCol .= ' ,opt1_idx ';
				$addQueryCol .= ' ,opt2_idx ';
				$addQueryCol .= ' ,op_type ';
				//추가 내용도 add되는 내용이기 , 를 앞에찍음 앞에 컬럼과 순서가 같아야함
				$addQueryVal .= " ,'".$opt1_idx."' ";
				$addQueryVal .= " ,'".$opt2_idx."' ";
				$addQueryVal .= " ,'".$option_type."' ";
			}
		}
		*/

		if( strlen( $errMsg ) == 0 ){
			$sql = "INSERT INTO tblwishlist ( ";
			$sql.= "id, productcode, date, marks ".$addQueryCol;
			$sql.= ") VALUES ( ";
			$sql.= "'".$_ShopInfo->getMemid()."', '".$productcode."', '".date('YmdHis')."', 0 ".$addQueryVal;
			$sql.= " ) ";
			$result = pmysql_query( $sql, get_db_conn() );
			if ( empty($result) ) {
				$errMsg = '등록에 실패했습니다.';
			}
		}
	} else if( $wish_item_cnt > 0 ) {
		$errMsg = '이미 위시리스트에 존재하는 상품입니다.';
	}
}

echo $errMsg;

?>