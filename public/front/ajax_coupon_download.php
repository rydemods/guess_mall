<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
include_once($Dir."lib/coupon.class.php");

$coupon_code = $_POST['coupon_code'];
$returnArr   = array();

$return_arr['code']      = '01';
$return_arr['msg']      = '쿠폰이 발급되지 않았습니다.\\n관리자에게 문의하세요.';
$return_arr['success']   = false;
$return_arr['html']      = '';
$return_arr['next_down'] = false;

if( strlen( $_ShopInfo->getMemid() ) > 0 ){
	#쿠폰 설정
	$_CouponInfo = new CouponInfo( '6' );
	#쿠폰 확인
	$_CouponInfo->search_coupon( $coupon_code );
	#insert 설정
	$_CouponInfo->set_couponissue( $_ShopInfo->memid );
	#쿠폰 확인 
	$msg = $_CouponInfo->search_coupon( $coupon_code, $_ShopInfo->memid ); 

	if( $msg == '0' ){
		$return_arr['msg']			= "해당하는 쿠폰이 없습니다.";
	} else if( $msg == '4' ) {
		$return_arr['msg']			= "쿠폰이 이미 발급되었습니다. MY PAGE>쿠폰에서 확인할 수 있습니다.";
	} else if( $msg == '5' ) {
		$return_arr['msg']			= "쿠폰이 이미 발급되었습니다. MY PAGE>쿠폰에서 확인할 수 있습니다.";
	} else if( $msg == '1' ) {
		$_CouponInfo->set_couponissue( $_ShopInfo->memid ); 
		if ($_CouponInfo->issue_type != '0') {
			#insert 설정 
			$return_data = $_CouponInfo->insert_couponissue(); 
			if( $return_data[0] === 0 ) {
				$sql = "
					SELECT 
						info.coupon_code, info.coupon_name, info.sale_money, info.sale_type, 
						issue.date_start, issue.date_end
					FROM
						tblcouponinfo AS info
					JOIN
						( 
							SELECT coupon_code, date_start, date_end FROM tblcouponissue WHERE coupon_code = '".$return_data[1][0]."' 
							AND id = '".$_ShopInfo->memid."'
						) AS issue ON ( info.coupon_code = issue.coupon_code )
				";

				$result = pmysql_query( $sql, get_db_conn() );
				$row    = pmysql_fetch_object( $result );
				pmysql_free_result( $result );
				$won = '';
				switch( $row->sale_type ){
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

				$layerText = "<tr name='TR_memcoupon' data-code='".$row->coupon_code."' >";
				$layerText.= "	<td>".$row->coupon_name."</td>";
				$layerText.= "	<td>".$row->sale_money.' '.$won."</td>";
				$layerText.= "	<td>";
				$layerText.= "		".toDate( $row->date_start, '-' )."<br>";
				$layerText.= "		~ ".toDate( $row->date_end, '-' );
				$layerText.= "		</td>";
				$layerText.= "</tr>";

				$return_arr['code']      = '00';
				$return_arr['success']   = true;
				$return_arr['html']      = $layerText;
				$return_arr['next_down'] = false;
				$return_arr['msg']			= "쿠폰이 발급되었습니다.";
			} else {
				$return_arr['msg']			= "쿠폰이 발급되지 않았습니다.\\n관리자에게 문의하세요.";
			}
		} else {
			$return_arr['msg']			= "쿠폰이 발급되지 않았습니다.\\n관리자에게 문의하세요.";
		}
	}
} else {
	$return_arr['code']      = '99';
	$return_arr['msg']      = '로그인후 다운로드 가능합니다.';
}
echo json_encode( $return_arr );

?>