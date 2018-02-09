<?php
#lib.php에 include 시키기전에 오류가 안나도록 불러오기로 한다
# lib에 쿠폰과 관련된 함수는 libcoupon.php 미리 분류시킨다
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
include_once($Dir."lib/coupon.class.php");

// 쿠폰의 할인 / 적립 text를 반환
function CouponText( $sale_type ){

	$text_arr = array(
		'text'=>'',
		'won'=>''
	);

	switch( $sale_type ){
		case '1' :
			$text_arr['text'] = '적립';
			$text_arr['won'] = '%';
			break;
		case '2' :
			$text_arr['text'] = '할인';
			$text_arr['won'] = '%';
			break;
		case '3' :
			$text_arr['text'] = '적립';
			$text_arr['won'] = '원';
			break;
		case '4' :
			$text_arr['text'] = '할인';
			$text_arr['won'] = '원';
			break;
		default :
			break;
	} //switch

	return $text_arr;
}

function memCouponLayer( $couponArr ){

    $member_layerHtml = array();
    $mem_layerText    = '';
    $pricetype_text   = '';
    $coupons          = array(); // 쿠폰중복 방지
    if( count( $couponArr ) > 0){
        foreach( $couponArr as $mcKey => $mcVal ){
            if( !in_array( $mcVal->coupon_code, $coupons ) ){ // 같은 종류의 쿠폰은 1만 노출되게 한다
                $coupons[]          = $mcVal->coupon_code;
                $pricetype_text     = CouponText( $mcVal->sale_type );
                $mem_layerText      = "<tr name='TR_memcoupon' data-code='".$mcVal->coupon_code."' >";
                $mem_layerText     .= "	<td>".$mcVal->coupon_name."</td>";
                $mem_layerText     .= "	<td>".$mcVal->sale_money.' '.$pricetype_text['won']."</td>";
                $mem_layerText     .= "	<td>";
                $mem_layerText     .= "		".toDate( $mcVal->date_start, '-' )."<br>";
                $mem_layerText     .= "		~ ".toDate( $mcVal->date_end, '-' );
                $mem_layerText     .= "		</td>";
                $mem_layerText     .= "</tr>";
                $member_layerHtml[] = $mem_layerText;
            }
        }
    }

    return $member_layerHtml;
}

function possibleCouponLayer( $couponArr ){

    $possible_layerHtml = array();
    $possible_layerText = '';
    $pricetype_text     = '';
    if( count( $couponArr ) > 0 ){
        foreach( $couponArr as $pcKey=>$pcVal ){
            $pricetype_text = CouponText( $pcVal->sale_type );
            $possible_layerText   = "<tr>";
            $possible_layerText  .= "	<td>".$pcVal->coupon_name."</td>";
            $possible_layerText  .= "	<td>".$pcVal->sale_money.' '.$pricetype_text['won']."</td>";
            $possible_layerText  .= "	<td>";
            $possible_layerText  .= "	<button type='button' class='btn-dib-function CLS_coupon_download' data-coupon='".$pcVal->coupon_code."' >";
            $possible_layerText  .= "		<span>쿠폰받기</span>";
            $possible_layerText  .= "	</button>";
            $possible_layerText  .= "		</td>";
            $possible_layerText  .= "</tr>";
            $possible_layerHtml[] = $possible_layerText;
        }
    }

    return $possible_layerHtml;

}

$productcode = $_REQUEST['productcode'];
$productcode = 'P0000HHG';
#쿠폰 설정
$_CouponInfo = new CouponInfo( '6' );

# 회원쿠폰
// 회원 쿠폰 목록 찾기
$_CouponInfo->search_member_coupon( 'dong' );
// 해당 상품에서 사용 가능한지 확인 
$_CouponInfo->check_coupon_product( $productcode, 1 );
$memCoupon      = $_CouponInfo->mem_coupon;
$memCouponLayer = memCouponLayer( $memCoupon );

# 받을 수 있는 쿠폰
// 받을 수 있는 쿠폰 목록 찾기 ( 셋팅에서 다운로드로 맞추어 놓음 )
$_CouponInfo->search_coupon();
// 해당 상품에서 사용 가능한지 확인 
$_CouponInfo->check_coupon_product( $productcode, 0 );
$possibleCoupon      =  $_CouponInfo->infoData;
$possibleCouponLayer = possibleCouponLayer( $possibleCoupon );

?>

<link rel="stylesheet" href="static/css/common.css">
<link rel="stylesheet" href="static/css/component.css">
<link rel="stylesheet" href="static/css/content.css">
<script type="text/javascript" src="static/js/jquery-1.12.0.min.js"></script>

		<!-- 팝업 - 사용가능쿠폰 -->
		<!-- <div id="popup-coupon" class="popup-layer-inner"> -->
			<div class="popup-layer-content popup-layer-iframe">
				<table>
                    <caption>사용가능쿠폰</caption>
                    <colgroup>
                        <col style="width:45%">
                        <col style="width:auto">
                        <col style="width:34%">
                    </colgroup>
                    <thead>
                        <tr>
                            <th scope="col">쿠폰명</th>
                            <th scope="col">할인율</th>
                            <th scope="col">보유/유효기간</th>
                        </tr>
                    </thead>
                    <tbody>
<?php
if( strlen( $_ShopInfo->getMemid() ) > 0 ){
	if( count( $coupon_layer[0] ) > 0 ){
		foreach( $coupon_layer[0] as $clVal ){
?>
					<?=$clVal?>
<?php
		} // coupon_layer foreach
	} else {
?>
					<tr class="" id='ID_coupon_no' >
						<td colspan="3" class="login-before">사용가능한 쿠폰이 없습니다.</td>
					</tr>
<?php
	}
} else {
?>
					<!-- 로그인전인 경우 -->
					<tr class="">
						<td colspan="3" class="login-before">로그인 후 확인하세요</td>
					</tr><!-- //로그인전인 경우 -->
<?php
} // $_ShopInfo->getMemid() if else
?>
                    </tbody>
                </table>
                <table>
                    <caption>다운로드 쿠폰</caption>
                    <colgroup>
                        <col style="width:45%">
                        <col style="width:auto">
                        <col style="width:34%">
                    </colgroup>
                    <thead>
                        <tr>
                            <th scope="col">쿠폰명</th>
                            <th scope="col">할인율</th>
                            <th scope="col">보유/유효기간</th>
                        </tr>
                    </thead>
                    <tbody>
<?php
if( strlen( $_ShopInfo->getMemid() ) > 0 ){
	if( count( $coupon_layer[1] ) > 0 ){
		foreach( $coupon_layer[1] as $clVal ){
?>
					<?=$clVal?>
<?php
		} // coupon_layer foreach
	} else {
?>
					<tr class="">
						<td colspan="3" class="login-before">다운로드가능한 쿠폰이 없습니다.</td>
					</tr>
<?php
	}
} else {
?>
					<!-- 로그인전인 경우 -->
					<tr class="">
						<td colspan="3" class="login-before">로그인 후 확인하세요</td>
					</tr><!-- //로그인전인 경우 -->
<?php
} // $_ShopInfo->getMemid() if else
?>
                    </tbody>
                </table>
			</div>
			<a class="btn-close" href="#popup-coupon" onclick="popup_close($(this).attr('href'));return false;"><img src="./static/img/btn/btn_close_popup_x.png" alt="팝업닫기"></a>
		<!-- </div> -->
		<!-- // 팝업 - 사용가능쿠폰 -->
		