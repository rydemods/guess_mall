<?php
/********************************************************************* 
// 파 일 명     : lib_coupon.php
// 설     명    : 쿠폰 관련 함수
// 상세설명     : 2016-05-19 이전에 작성된 쿠폰function
// 작 성 자     : 2016-05-19 유동혁
// 수 정 자     : 
// 
*********************************************************************/ 

/*
함수 목록

function MemberCoupon
function CouponDiscount
function CouponProductCheck
function PossibleCoupon
function DownloadCoupon
function AmountFloor

//
function couponDisPrice
function getTotalPriceDc
//

*/


?>
<?php

# 회원의 쿠폰정보 2016-02-12 유동혁 2016-04-04 수정
/**
* 함수명 :MemberCoupon
* 쿠폰 할인가 생성
* parameter :
*   - int $type : 불러올 쿠폰 대상 ( 0 - 전체, 1 - 사용가능한 쿠폰 )
*   - string $is_mobile : 불러올 대상 쿠폰종류 A - PC/MOBILE, P - PC, M - MOBILE ( 값이 없으면 전체를 불러온다 )
*   - string $add_pc    : 불러올  PC 추가 대상 B - PC + MOBILE, C - PC + APP, BC - PC + MOBILE || PC + APP
* return :
*   - array( object( 쿠폰정보 ) )
*/
function MemberCoupon( $type = 0, $is_mobile = '', $add_pc='' )
{
    global $_ShopInfo;

    $now_date = date("YmdH");

    $sql = "SELECT issue.coupon_code, issue.id, issue.date_start, issue.date_end, ";
    $sql.= "issue.used, issue.issue_member_no, issue.issue_recovery_no, issue.ci_no, ";
    $sql.= "info.coupon_name, info.sale_type, info.sale_money, info.amount_floor, ";
    $sql.= "info.productcode, info.use_con_Type1, info.use_con_type2, info.description, ";
    $sql.= "info.use_point, info.vender, info.delivery_type, info.coupon_use_type, ";
    $sql.= "info.coupon_type, info.sale_max_money, info.coupon_is_mobile ";
    $sql.= "FROM tblcouponissue issue ";
    $sql.= "JOIN tblcouponinfo info ON info.coupon_code = issue.coupon_code ";
    $sql.= "WHERE issue.id = '".$_ShopInfo->getMemid()."' ";
    if( strlen( $is_mobile ) > 0 ) {
        $sql.= "AND ( info.coupon_is_mobile = '".$is_mobile."' OR info.coupon_is_mobile = 'A' ";
        if( $add_pc == 'B' ){
            $sql.= " OR info.coupon_is_mobile = 'B' ";
        } else if( $add_pc == 'C' ) {
            $sql.= " OR info.coupon_is_mobile = 'C' ";
        } else if( $add_pc == 'BC' ){
            $sql.= " OR info.coupon_is_mobile = 'B' OR info.coupon_is_mobile = 'C' ";
        }
        $sql.= " ) ";
    }

    switch( $type ){ 
        case 0 : //전체
            break;
        case 1 : //사용가능한 것만
            $sql.= "AND issue.used = 'N' ";
            $sql.= "AND ( issue.date_start <= '".$now_date."' AND issue.date_end >= '".$now_date."' ) ";
            $sql.= "AND ( issue.date_end <= info.date_end ) ";
            break;
        default :
            break;
    }

    $result = pmysql_query( $sql, get_db_conn() );
    $member_coupon = array();
    while( $row = pmysql_fetch_object( $result ) ){
        $member_coupon[] = $row;
    }

    return $member_coupon;

}


# 상품의 할인가를 받아온다 2016-02-12 유동혁
/**
* 함수명 :CouponDiscount
* 쿠폰 할인가 생성
* parameter :
* 	- int $sellprice : 대상 가격
*	- string $couponcode : 쿠폰 코드
*	- int $ci_no : 쿠폰 고유번호
* return :
*	- array( 쿠폰고유번호, 쿠폰코드, type { 적립 - 0 /할인 - 1 }, 할인가격 )
*/
function CouponDiscount( $sellprice, $ci_no )
{
	global $_ShopInfo;

	$dc_price = 0;
	$reserve_type = true;
    $coupon_code = '';
    $coupon_type = '';

	$coupon_sql = "SELECT issue.coupon_code, issue.id, issue.date_start, issue.date_end, ";
	$coupon_sql.= "issue.used, issue.issue_member_no, issue.issue_recovery_no, issue.ci_no, ";
	$coupon_sql.= "info.coupon_name, info.sale_type, info.sale_money, info.amount_floor, ";
	$coupon_sql.= "info.productcode, info.use_con_Type1, info.use_con_type2, info.description, ";
	$coupon_sql.= "info.use_point, info.vender, info.delivery_type, info.coupon_use_type, ";
	$coupon_sql.= "info.sale_max_money, info.coupon_is_mobile, info.coupon_type, info.mini_price ";
	$coupon_sql.= "FROM tblcouponissue issue ";
	$coupon_sql.= "JOIN tblcouponinfo info ON info.coupon_code = issue.coupon_code ";
	$coupon_sql.= "WHERE ci_no = '".$ci_no."' AND issue.id = '".$_ShopInfo->getMemid()."' ";
    if( $ci_no != '' ){
    	$coupon_result = pmysql_query( $coupon_sql, get_db_conn() );
        $coupon_row = pmysql_fetch_object( $coupon_result );
        pmysql_free_result( $coupon_result );
        switch( $coupon_row->sale_type ){
            case '1' :
                $reserve_type = false;
            case '2' :
                $dc_price = ( ( $sellprice * $coupon_row->sale_money ) / 100 );
                $dc_price = AmountFloor( $coupon_row->amount_floor, $dc_price );
                break;
            case '3' :
                $reserve_type = false;
            case '4' :
                //$dc_price =  $coupon_row->sale_money;
                if($coupon_row->sale_money > $sellprice){
                    $dc_price = $sellprice;
                 }else{
                    $dc_price =  $coupon_row->sale_money;
                 }
                break;
            default :
                break;
        }

        if( $coupon_row->sale_max_money > 0 && $coupon_row->sale_max_money < $dc_price ) {
            $dc_price = $coupon_row->sale_max_money;
        }

        $coupon_code = $coupon_row->coupon_code;
        $coupon_type = $coupon_row->coupon_type;
    }
	$arr_dc = array(
		$ci_no, 'ci_no'=>$ci_no, $reserve_type, 'type'=>$reserve_type, 
		$sellprice, 'sellprice'=>$sellprice, $dc_price, 'dc'=>$dc_price,
		$coupon_row->coupon_code, 'coupon_code'=>$coupon_row->coupon_code,
		$coupon_row->coupon_type, 'coupon_type'=>$coupon_row->coupon_type,
		$coupon_row->mini_price, 'mini_price'=>$coupon_row->mini_price
	);

	

	return $arr_dc;
}


# 상품군별 쿠폰 사용가능 확인 2016-02-12 유동혁
/**
* 함수명 :CouponProductCheck
* 상품군별 쿠폰 사용가능 확인
* parameter :
* 	- string $coupon_code    : 쿠폰코드
*	- string $coupon_product : 쿠폰적용 상품군
*	- string $productcode    : 상품코드
*   - string $type           : 제외 / 선택 type ( 0 제외 , 1 선택 )
* return :
*	- bool
    현제 기존 쿠폰이 type이 1이기 때문에 값을 1로 넣어줌
*/
function CouponProductCheck( $coupon_code ,$coupon_product, $productcode, $type = '1' )
{
	$on_coupon = false;

	switch( $coupon_product ){
		case 'ALL' :
			$on_coupon = true;
			break;
		case 'CATEGORY' :
			$sql = "SELECT COUNT( cc.coupon_code ) FROM tblproductlink pl ";
			$sql.= "JOIN tblcouponcategory cc ON ( pl.c_category LIKE cc.categorycode||'%' ) ";
			$sql.= "WHERE pl.c_productcode = '".$productcode."' AND cc.coupon_code = '".$coupon_code."' AND cc.type ='".$type."' ";
			$sql.= "GROUP BY cc.coupon_code ";
			$result = pmysql_query( $sql, get_db_conn() );
			$row = pmysql_fetch_row( $result );
			if( $row[0] > 0 ) $on_coupon = true;
			pmysql_free_result( $result );
			break;
		case 'GOODS' :
			$sql = "SELECT COUNT( * ) FROM tblcouponproduct WHERE coupon_code = '".$coupon_code."' AND productcode = '".$productcode."' AND type ='".$type."' ";
			$result = pmysql_query( $sql, get_db_conn() );
			$row = pmysql_fetch_row( $result );
			if( $row[0] > 0 ) $on_coupon = true;
			pmysql_free_result( $result );
			break;
		default :
			$on_coupon = false;
			break;
	} // switch

	return $on_coupon;

}


# 상품의 사용가능 쿠폰정보를 받아온다 2016-02-18 유동혁
/**
* 함수명 :PossibleCoupon
* 특정 상품의 사용가능 쿠폰확인
* parameter :
* 	- string $productcode : 상품코드
* return :
*	- array( 쿠폰정보 ) or bool false
*/
function PossibleCoupon( $productcode, $coupon_is_mobile = 'P' ){

	global $_ShopInfo;
	$qry = '';
	$coupons = array();

	$mainCate = ProductMainCate( $productcode );
	if( $mainCate !== false ){ // 카테고리 쿠폰 조건

		$code_a = $mainCate->code_a; 
		$code_b = $mainCate->code_b;
		$code_c = $mainCate->code_c;
		$code_d = $mainCate->code_d;

		$qry.= "OR ( role = 'CATEGORY' ";
		$qry.= " AND ( categorycode LIKE '".$code_a."%' ";
		$qry.= "  OR categorycode LIKE '".$code_a.$code_b."%' ";
		$qry.= "  OR categorycode LIKE '".$code_a.$code_b.$code_c."%' ";
		$qry.= "  OR categorycode = '".$code_a.$code_b.$code_c.$code_d."' ";
		$qry.= " ) ";
		$qry.= ") ";

	}
	// 상품쿠폰 조건
	$qry.= "OR ( role = 'GOODS' ";
	$qry.= " AND ( productcode = '".$productcode."' ) ";
	$qry.= ") ";

	$sql = "
		WITH info AS (
		  SELECT coupon_code, coupon_name, productcode AS role, sale_type, sale_max_money, sale_money, issue_member_no, coupon_is_mobile, 
			(
			  CASE 
				WHEN time_type = 'D' THEN date_start
				WHEN time_type = 'P' THEN to_char( now()::date , 'YYYYMMDDHH24' )
			  END
			) AS date_start,
			(
			  CASE 
				WHEN time_type = 'D' THEN date_end
				WHEN time_type = 'P' THEN 
                (
                  CASE
                    WHEN to_char( ( now()::date + abs( date_start::int ) ) - interval '1 hour' , 'YYYYMMDDHH24' ) < date_end 
                      THEN to_char( ( now()::date + abs( date_start::int ) ) - interval '1 hour' , 'YYYYMMDDHH24' )
                    ELSE date_end
                  END
                )
			  END
			) AS date_end
		  FROM tblcouponinfo info 
		  WHERE display = 'Y' AND detail_auto = 'Y'
		  AND issue_type = 'Y' AND ( coupon_type = '1' OR coupon_type = '6' )
		  AND use_con_type2 = 'Y'
		  AND date_end > to_char( now() + interval '1 hour', 'YYYYMMDDHH24' )
		)
	"; // 오늘 날짜로부터 가져올 수 있는다운로드 쿠폰 

	$sql.= "
		, take_coupon AS (
		  SELECT tc.coupon_code
		  FROM 
			( SELECT coupon_code, COUNT( coupon_code ) AS take_cnt FROM tblcouponissue issue WHERE issue.id = '".$_ShopInfo->getMemid()."' AND used = 'N' GROUP BY coupon_code ) AS tc,
			( SELECT coupon_code, issue_member_no FROM info ) AS info
		  WHERE tc.coupon_code = info.coupon_code
		  AND tc.take_cnt >= info.issue_member_no
		)
	"; // 재발급 불가능한 쿠폰

	$sql.= "
		, products AS (
		  SELECT info.coupon_code, info.coupon_name, info.role, info.date_start, info.date_end, info.coupon_is_mobile, 
		  info.sale_type, info.sale_max_money, info.sale_money, cc.categorycode, cp.productcode
		  FROM info 
		  LEFT JOIN tblcouponcategory cc ON cc.coupon_code = info.coupon_code
		  LEFT JOIN tblcouponproduct cp ON cp.coupon_code = info.coupon_code
		  WHERE info.coupon_code NOT IN ( SELECT coupon_code FROM take_coupon ) 
		)
	"; // 상품 및 카테고리 쿠폰 리스트를 가져옴  
	$sql.= "
		SELECT coupon_code, coupon_name, role, date_start, date_end, sale_type, sale_max_money, sale_money, categorycode, productcode 
		FROM products
		WHERE ( coupon_is_mobile = 'A' OR coupon_is_mobile = '".$coupon_is_mobile."' ) 
	"; // 상세조건

	$sql.= " AND ( ( role = 'ALL') ".$qry." ) "; //상세조건 추가
	//exdebug( $sql );
	$result = pmysql_query( $sql, get_db_conn() );
	while( $row = pmysql_fetch_object( $result ) ){
		$coupons[] = $row;
	}
	pmysql_free_result( $result );
	
	if( count( $coupons ) > 0){
		return $coupons;
	} else {
		return false;
	}
}


# 상품의 다운로드 가능한 쿠폰정보를 받아온다 2016-09-19 김재수
/**
* 함수명 :DownPossibleCoupon
* 특정 상품의 다운로드가능 쿠폰확인
* parameter :
* 	- string $productcode : 상품코드
* return :
*	- array( 쿠폰정보 ) or bool false
*/
function DownPossibleCoupon( $productcode, $coupon_is_mobile = 'P' ){

	global $_ShopInfo;
	$qry = '';
	$coupons = array();

	$mainCate = ProductMainCate( $productcode );
	if( $mainCate !== false ){

		$code_a = $mainCate->code_a; 
		$code_b = $mainCate->code_b;
		$code_c = $mainCate->code_c;
		$code_d = $mainCate->code_d;

		$qry.= " categorycode = '".$code_a."' ";
		$qry.= "  OR categorycode = '".$code_a.$code_b."' ";
		$qry.= "  OR categorycode = '".$code_a.$code_b.$code_c."' ";
		$qry.= "  OR categorycode = '".$code_a.$code_b.$code_c.$code_d."' ";

	}

	$sql = "
		WITH info AS (
		  SELECT coupon_code, coupon_type, coupon_name, (CASE WHEN use_con_type2='N' THEN not_productcode ELSE productcode END) AS role, sale_type, sale_max_money, sale_money, issue_member_no, coupon_is_mobile, detail_auto, use_con_type2, 
		  (select count(*) from tblcouponproduct where coupon_code = info.coupon_code and productcode = '".$productcode."') as prod_cnt,
		  (select count(*) from tblcouponcategory where coupon_code = info.coupon_code and (".$qry.")) as cate_cnt, 
			(
			  CASE 
				WHEN time_type = 'D' THEN date_start
				WHEN time_type = 'P' THEN to_char( now()::date , 'YYYYMMDDHH24' )
			  END
			) AS date_start,
			(
			  CASE 
				WHEN time_type = 'D' THEN date_end
				WHEN time_type = 'P' THEN 
                (
                  CASE
                    WHEN to_char( ( now()::date + abs( date_start::int ) ) - interval '1 hour' , 'YYYYMMDDHH24' ) < date_end 
                      THEN to_char( ( now()::date + abs( date_start::int ) ) - interval '1 hour' , 'YYYYMMDDHH24' )
                    ELSE date_end
                  END
                )
			  END
			) AS date_end
		  FROM tblcouponinfo info 
		  WHERE display = 'Y' AND detail_auto = 'Y'
		  AND issue_type = 'Y' AND coupon_type = '6' 
		  /*AND use_con_type2 = 'Y'*/
		  AND date_end > to_char( now() + interval '1 hour', 'YYYYMMDDHH24' )
		)
	"; // 오늘 날짜로부터 가져올 수 있는다운로드 쿠폰 

	$sql.= "
		, products AS (
		  SELECT info.coupon_code, info.coupon_type, info.coupon_name, info.role, info.date_start, info.date_end, info.coupon_is_mobile, 
		  info.sale_type, info.sale_max_money, info.sale_money, cc.categorycode, cp.productcode, case when tc.take_cnt >= info.issue_member_no then 'N' else 'Y' end as take_dn, info.detail_auto, info.use_con_type2, info.prod_cnt, info.cate_cnt 
		  FROM info 
		  LEFT JOIN tblcouponcategory cc ON cc.coupon_code = info.coupon_code
		  LEFT JOIN tblcouponproduct cp ON cp.coupon_code = info.coupon_code
		  LEFT JOIN ( SELECT coupon_code, COUNT( coupon_code ) AS take_cnt FROM tblcouponissue issue WHERE issue.id = '".$_ShopInfo->getMemid()."' AND used = 'N' GROUP BY coupon_code ) AS tc ON tc.coupon_code = info.coupon_code
		)
	"; // 상품 및 카테고리 쿠폰 리스트를 가져옴  
	$sql.= "
		SELECT coupon_code, coupon_type, coupon_name, role, date_start, date_end, sale_type, sale_max_money, sale_money, categorycode, productcode, take_dn, detail_auto 
		FROM products
		WHERE ( coupon_is_mobile = 'A' OR coupon_is_mobile = '".$coupon_is_mobile."' ) 
	"; // 상세조건
	$sql.= " AND ((use_con_type2='Y' AND (prod_cnt > 0 OR cate_cnt > 0)) OR (use_con_type2='N' AND prod_cnt = 0 AND cate_cnt = 0)) ";
	$sql.= " ORDER BY date_start DESC LIMIT 1"; //상세조건 추가
	//exdebug( $sql );
	$result = pmysql_query( $sql, get_db_conn() );
	while( $row = pmysql_fetch_object( $result ) ){
		$coupons[] = $row;
	}
	pmysql_free_result( $result );
	
	if( count( $coupons ) > 0){
		return $coupons;
	} else {
		return false;
	}
}


# 다운로드 쿠폰 2016-02-18 유동혁
/**
* 함수명 :DownloadCoupon
* 다운로드 쿠폰 insert
* parameter :
* 	- string $coupon_code : 쿠폰코드
* return :
*	- array( code=>에러코드( 01 일 경우 성공 ), success=>bool 성공유무 )
*/
function DownloadCoupon( $coupon_code ){
	global $_ShopInfo;
	$success = false;
	$error_code = '00';
	$return_html = '';
	$return_arr = array();
	$layerText = '';
	$won = '';
    $next_down = false;

	# 발급된 쿠폰 확인
	$sql = "SELECT COUNT( coupon_code ) AS cnt FROM tblcouponissue WHERE id ='".$_ShopInfo->getMemid()."' ";
    $sql.= "AND coupon_code = '".$coupon_code."' AND used = 'N' GROUP BY coupon_code ";
	$result = pmysql_query( $sql, get_db_conn() );
	$row = pmysql_fetch_row( $result );
	$myCoupon = $row[0];
	pmysql_free_result( $result );

	# 쿠폰 확인
	$info_sql = "SELECT coupon_code, coupon_name, repeat_id, issue_member_no, issue_type, issue_tot_no, issue_no, sale_money, sale_type, ";
	$info_sql.= "( 
                    CASE 
                        WHEN time_type = 'D' THEN date_start 
                        WHEN time_type = 'P' THEN to_char( now()::date , 'YYYYMMDDHH24' ) 
                    END 
                 ) AS date_start, 
    ";
	$info_sql.= "( 
                   CASE 
                     WHEN time_type = 'D' THEN date_end 
                     WHEN time_type = 'P' THEN 
                     (
                       CASE 
                           WHEN to_char( ( now()::date + abs( date_start::int ) + 1 ) - interval '1 hour' , 'YYYYMMDDHH24' ) < date_end 
                               THEN to_char( ( now()::date + abs( date_start::int ) + 1 ) - interval '1 hour' , 'YYYYMMDDHH24' ) 
                           ELSE date_end 
                       END
                     )
                     END
                ) AS date_end 
    ";
	$info_sql.= "FROM tblcouponinfo WHERE coupon_code ='".$coupon_code."' ";
	$info_result = pmysql_query( $info_sql, get_db_conn() );
	$infoCoupon = pmysql_fetch_object( $info_result );
	pmysql_free_result( $info_result );

	switch( $infoCoupon->sale_type ){
		case '1' :
		case '2' :
			$won = '%';
			break;
		case '3' :
		case '4' :
			$won = '원';
			break;
		default :
			break;
	} //switch

	$layerText = "<tr name='TR_memcoupon' data-code='".$infoCoupon->coupon_code."' >";
	$layerText.= "	<td>".$infoCoupon->coupon_name."</td>";
	$layerText.= "	<td>".$infoCoupon->sale_money.' '.$won."</td>";
	$layerText.= "	<td>";
	$layerText.= "		".toDate( $infoCoupon->date_start, '-' )."<br>";
	$layerText.= "		~ ".toDate( $infoCoupon->date_end, '-' );
	$layerText.= "		</td>";
	$layerText.= "</tr>";
	
	/* 수량 상관없이 받을 수 있게됨 2016-02-19 유동혁
	if( $infoCoupon->issue_tot_no == 0 ){
		$success = true;
	} else if( $infoCoupon->issue_no < $infoCoupon->issue_tot_no ){
		$success = true;
	} else {
		$success = false;
		$error_code = '03'; // 쿠폰 발급 수량이 유효하지 않음
	}
	*/

	if( !is_null( $myCoupon ) ){ //발급된 쿠폰이 있으면  && $success
		if( $infoCoupon->repeat_id == 'Y' ){ // 쿠폰 중복발행이 가능하면 && $infoCoupon->issue_member_no > $myCoupon
			if( ( $infoCoupon->date_start < $infoCoupon->date_end ) && $infoCoupon->issue_type == 'Y' ){
				$insert_sql = "INSERT INTO tblcouponissue ( coupon_code, id, date_start, date_end, used, date, issue_recovery_no ) ";
				$insert_sql.= " VALUES ( '".$coupon_code."', '".$_ShopInfo->getMemid()."', '".$infoCoupon->date_start."', '".$infoCoupon->date_end."', ";
				$insert_sql.= "'N', '".date('YmdHis')."', '".$myCoupon."' )";
				pmysql_query( $insert_sql, get_db_conn() );
				if( pmysql_errno() ){
					$success = false;
					$error_code = '01'; // db 입력 실패
				} else {
                    if( $infoCoupon->issue_member_no > $myCoupon + 1 ) $next_down = true;
					$update_sql = "UPDATE tblcouponinfo SET issue_no = issue_no + 1 WHERE coupon_code ='".$coupon_code."'";
					pmysql_query( $update_sql, get_db_conn() );
					$success = true;
				}
			} else { //발급 가능일 확인
				$success = false;
				$error_code = '02'; // 쿠폰 발급일이 지남
			}
		} else {
			$success = false;
			$error_code = '03'; // 쿠폰 발급 수량이 유효하지 않음
		}
	} else  { // 발급된 쿠폰이 없으면 if( $success )
		
		if( ( $infoCoupon->date_start < $infoCoupon->date_end ) && $infoCoupon->issue_type == 'Y' ){ //발급 가능일 확인
			$insert_sql = "INSERT INTO tblcouponissue ( coupon_code, id, date_start, date_end, used, date, issue_recovery_no ) ";
			$insert_sql.= " VALUES ( '".$coupon_code."', '".$_ShopInfo->getMemid()."', '".$infoCoupon->date_start."', '".$infoCoupon->date_end."', ";
			$insert_sql.= "'N', '".date('YmdHis')."', '0' )";
			pmysql_query( $insert_sql, get_db_conn() );
			if( pmysql_errno() ){
				$success = false;
				$error_code = '01'; // db 입력 실패
			} else {
                if( $infoCoupon->issue_member_no > 1 ) $next_down = true;
				$update_sql = "UPDATE tblcouponinfo SET issue_no = issue_no + 1 WHERE coupon_code ='".$coupon_code."'";
				pmysql_query( $update_sql, get_db_conn() );
				$success = true;
			}
		}
		/*else {
			$success = false;
			$error_code = '02'; // 쿠폰 발급일이 지남
		}
		*/
	}

	$return_arr['code'] = $error_code;
	$return_arr['success'] = $success;
	$return_arr['html'] = $layerText;
    $return_arr['next_down'] = $next_down;

	return $return_arr;
}


# 자리수 내림 2016-02-12 유동혁
/**
* 함수명 :AmountFloor
* 자리수 내림
* parameter :
* 	- int $amout_floor : 내릴 자리수
*	- int $number : 대상 값
* return :
*	- int return_number : 자리수 내림이 된 값
*/
function AmountFloor( $amout_floor = 0, $number )
{
	$return_number = 0;

	if( $amout_floor > 0 && $number > 0 ) {
		$return_number = floor( $number / pow( 10, $amout_floor ) ) * pow( 10, $amout_floor );
	} else {
		$return_number = $number;
	}

	return (int) $return_number;
}


//쿠폰에 의한 가격 정보
//사용안함 2016-05-19 유동혁
function couponDisPrice($productcode=''){
	global $_data;
	$psql = "SELECT sellprice FROM tblproduct WHERE productcode = '{$productcode}'";
	list($SellpriceValue) = pmysql_fetch(pmysql_query($psql));
	if($productcode){
		# 쿠폰 다운로드 최근 날짜 1장 노출
		$couponDownLoadFlag = false;
		$goods_sale_type = "";
		$goods_sale_money = "";
		$goods_amount_floor = "";
		$goods_sale_max_money = "";
		if($_data->coupon_ok=="Y") {
			$goods_cate_sql = "SELECT * FROM tblproductlink WHERE c_productcode = '".$productcode."'";
			$goods_cate_result = pmysql_query($goods_cate_sql,get_db_conn());
			$categorycode = array();
			while($goods_cate_row=pmysql_fetch_object($goods_cate_result)) {
				list($cate_a, $cate_b, $cate_c, $cate_d) = sscanf($goods_cate_row->c_category,'%3s%3s%3s%3s');
				$categorycode[] = $cate_a;
				$categorycode[] = $cate_a.$cate_b;
				$categorycode[] = $cate_a.$cate_b.$cate_c;
				$categorycode[] = $cate_a.$cate_b.$cate_c.$cate_d;
			}
			if(count($categorycode) > 0){
				$addCategoryQuery = "('".implode("', '", $categorycode)."')";
			}else{
				$addCategoryQuery = "('')";
			}

			$sql = "SELECT a.* FROM tblcouponinfo a ";
			$sql .= "LEFT JOIN tblcouponproduct c on a.coupon_code=c.coupon_code ";
			$sql .= "LEFT JOIN tblcouponcategory d on a.coupon_code=d.coupon_code ";
			if($_pdata->vender>0) {
				$sql .= "WHERE (a.vender='0' OR a.vender='{$_pdata->vender}') ";
			} else {
				$sql .= "WHERE a.vender='0' ";
			}
			$sql .= "AND a.display='Y' AND a.issue_type='Y' AND a.detail_auto='Y' AND a.coupon_type='1' ";
			$sql .= "AND (a.date_end>'".date("YmdH")."' OR a.date_end='') ";
			$sql .= "AND ((a.use_con_type2='Y' AND a.productcode = 'ALL') OR ((a.use_con_type2='Y' AND a.productcode != 'ALL') AND (c.productcode = '".$productcode."' OR (d.categorycode IN ".$addCategoryQuery." AND a.use_con_type2 = 'Y')))) ";
			$sql .= "AND mod(sale_type::int , 2) = '0' ";
			$sql .= "ORDER BY date DESC ";
			$sql .= "LIMIT 1 OFFSET 0";
			$result=pmysql_query($sql,get_db_conn());
			while($row=pmysql_fetch_object($result)) {
				$goods_sale_type = $row->sale_type;
				$goods_sale_money = $row->sale_money;
				$goods_amount_floor = $row->amount_floor;
				$goods_sale_max_money = $row->sale_max_money;
				$goods_coupon_code = $row->coupon_code;

				$couponDownLoadFlag = true;
			}
			pmysql_free_result($result);
		}
		$couprice["goods_sale_type"] = $goods_sale_type;
		$couprice["goods_sale_money"] = $goods_sale_money;
		$couprice["goods_amount_floor"] = $goods_amount_floor;
		$couprice["goods_sale_max_money"] = $goods_sale_max_money;
		$couprice["goods_coupon_code"] = $goods_coupon_code;
		$couprice["couponDownLoadFlag"] = $couponDownLoadFlag;


		if($couponDownLoadFlag){
			if($goods_sale_type <= 2){
				$couponDcPrice = ($SellpriceValue*$goods_sale_money)*0.01;
				$couponDcPrice = ($couponDcPrice / pow(10, $goods_amount_floor)) * pow(10, $goods_amount_floor);
				$goods_dc_coupong = number_format($goods_sale_money)."%";
			}else{
				$couponDcPrice = $goods_sale_money;
				$goods_dc_coupong = number_format($goods_sale_money)."원";
			}
			if($goods_sale_max_money && $goods_sale_max_money < $couponDcPrice){
				$couponDcPrice = $goods_sale_max_money;
			}

		}

		$couprice["coumoney"] = $couponDcPrice;
	}
	return $couprice;
}


//총구매금액대별 할인률 (bf_sumprice: 할인전 총 구매금액)
function getTotalPriceDc($bf_sumprice){
	global $_data, $_ShopInfo;
	
	if($_ShopInfo->memid){
		if($bf_sumprice){
			if($bf_sumprice>=500000){
			$tot_dc_per=$_data->price_dc_50;
			}else if($bf_sumprice>=300000){
				$tot_dc_per=$_data->price_dc_30;
			}else if($bf_sumprice>=100000){
				$tot_dc_per=$_data->price_dc_10;
			}
		}
	}else{
		$tot_dc_per=0;
	}

	return $tot_dc_per;
}


?>