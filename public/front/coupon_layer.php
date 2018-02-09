<?php
#lib.php에 include 시키기전에 오류가 안나도록 불러오기로 한다
# lib에 쿠폰과 관련된 함수는 libcoupon.php 미리 분류시킨다
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
include_once($Dir."lib/coupon.class.php");

function getProductCouponInfo($productcode) {
	#쿠폰 설정
	$_CouponInfo = new CouponInfo( '6' );
	// 받을 수 있는 쿠폰 목록 찾기 ( 셋팅에서 다운로드로 맞추어 놓음 )
	$_CouponInfo->search_coupon();
	// 해당 상품에서 사용 가능한지 확인 
	$_CouponInfo->check_coupon_product( $productcode, 0 );
	//exdebug( $_CouponInfo->infoData );
	$possibleCoupon      =  $_CouponInfo->infoData;
	
	return $possibleCoupon;
}

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

$productcode = $_REQUEST['productcode'];
$ProductCouponInfo	= getProductCouponInfo($productcode);
exdebug($ProductCouponInfo);
#쿠폰 설정
$_CouponInfo = new CouponInfo( '6' );

# 회원쿠폰
// 회원 쿠폰 목록 찾기
$_CouponInfo->search_member_coupon( $_ShopInfo->memid, 1, 1 );
// 해당 상품에서 사용 가능한지 확인 
$_CouponInfo->check_coupon_product( $productcode, 1 );
$memCoupon      = $_CouponInfo->mem_coupon;

$memCouponLayer = array();
$mem_layerText    = '';
$pricetype_text   = '';
$coupons          = array(); // 쿠폰중복 방지
if( count( $memCoupon ) > 0){
    foreach( $memCoupon as $mcKey => $mcVal ){
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
            $memCouponLayer[]   = $mem_layerText;
        }
    }
}

# 받을 수 있는 쿠폰
// 받을 수 있는 쿠폰 목록 찾기 ( 셋팅에서 다운로드로 맞추어 놓음 )
$_CouponInfo->search_coupon();
// 해당 상품에서 사용 가능한지 확인 
$_CouponInfo->check_coupon_product( $productcode, 0 );
//exdebug( $_CouponInfo->infoData );
$possibleCoupon      =  $_CouponInfo->infoData;

$possibleCouponLayer     = array();
$possible_layerText = '';
$pricetype_text     = '';
if( count( $possibleCoupon ) > 0 ){
    foreach( $possibleCoupon as $pcKey=>$pcVal ){
        //if( $_CouponInfo->search_possesion_check( $pcVal->coupon_code, $_ShopInfo->memid ) ){ // 해당 쿠폰을 가지고 있는지 체크한다
            $pricetype_text      = CouponText( $pcVal->sale_type );
            $possible_layerText  = "<tr>";
            $possible_layerText .= "	<td>".$pcVal->coupon_name."</td>";
            $possible_layerText .= "	<td>".$pcVal->sale_money.' '.$pricetype_text['won']."</td>";
            $possible_layerText .= "	<td>";
            $possible_layerText .= "	<button type='button' class='btn-dib-function CLS_coupon_download' data-coupon='".$pcVal->coupon_code."' >";
            $possible_layerText .= "		<span>쿠폰받기</span>";
            $possible_layerText .= "	</button>";
            $possible_layerText .= "		</td>";
            $possible_layerText .= "</tr>";
            $possibleCouponLayer[]    = $possible_layerText;
       // }
    }
}

?>
<html/>
<head>
<link rel="stylesheet" href="<?=$Dir?>static/css/common.css">
<link rel="stylesheet" href="<?=$Dir?>static/css/component.css">
<link rel="stylesheet" href="<?=$Dir?>static/css/content.css">
<script type="text/javascript" src="<?=$Dir?>static/js/jquery-1.12.0.min.js"></script>
</head>
<body style=' padding-top: 0px !important; ' >
<script>
   
// 쿠폰 다운로드
$(document).on( 'click', '.CLS_coupon_download', function( event ) {
	var coupon_code       = $(this).attr('data-coupon');
	var coupon_button     = $(this);
	var buttonHtml_target = coupon_button.parent();
	var buttonHtml        = $(this)[0].outerHTML;
    var mem_coupon        = true; 
    var bottomTbody       = $( buttonHtml_target ).parent().parent().parent();

	//coupon_button.remove();
	$('tr[name="TR_memcoupon"]').each( function( i, obj ) { // 같은 종류의 쿠폰이 존재 하는지 확인
		if( $(this).attr('data-code') == coupon_code ) {
			mem_coupon = false;
		}
	});

	if( coupon_code.length > 0 ) {
		$.ajax({
			type: "POST",
			url: "../front/ajax_coupon_download.php",
			data : { coupon_code : coupon_code },
			dataType : 'json'
		}).done( function( data ){
			if( data.success === true ){
				alert('쿠폰이 발급 되었습니다.');
				if( $('#ID_coupon_no').length > 0  ) $('#ID_coupon_no').remove();
				if( mem_coupon ) $('#ID_coupon_layer').append( data.html );
				/*if( data.next_down === true ) {
					buttonHtml_target.html( buttonHtml );
				} else {
					//buttonHtml_target.html( '최대 수량 보유' );
					$( buttonHtml_target ).parent().remove();
					if( $(bottomTbody).find('tbody').children().length == 0 ){
						$(bottomTbody).append( '<tbody><tr class=""><td colspan="3" class="login-before">다운로드가능한 쿠폰이 없습니다.</td></tr></tbody>' );
					}
				}*/
			} else {
				alert(data.msg);
			}
		});
	} else {
		alert('발급 가능한 쿠폰이 아닙니다.');
	}
});
</script>
<!-- 사용가능 쿠폰 dimm layer -->
<!-- <div class="layer-dimm-wrap layer-detail-coupon" >
    <div class="dimm-bg"></div>
    <div class="layer-inner layer-coupon" style='height:500px;' > -->
        <!-- <h3 class="layer-title"></h3>
        <button type="button" class="btn-close">창 닫기 버튼</button> -->
        <div class="layer-content js-scroll layer-content-iframe">
            <h4 class="title">사용가능 쿠폰</h4>
            <table class="th-top util">
                <caption>사용가능 쿠폰 리스트</caption>
                <colgroup><col style="auto"><col style="width:96px"><col style="width:134px"></colgroup>
                <thead>
                    <tr>
                        <th scope="col">쿠폰명</th>
                        <th scope="col">할인율</th>
                        <th scope="col">보유 / 유효기간</th>
                    </tr>
                </thead>
                <tbody id='ID_coupon_layer'>
<?php
if( strlen( $_ShopInfo->getMemid() ) > 0 ){
    if( count( $memCouponLayer ) > 0 ){
        foreach( $memCouponLayer as $clVal ){
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

            <h4 class="title">다운로드 쿠폰</h4>
            <table class="th-top util">
                <caption>사용가능 쿠폰 리스트</caption>
                <colgroup><col style="auto"><col style="width:96px"><col style="width:134px"></colgroup>
                <thead>
                    <tr>
                        <th scope="col">쿠폰명</th>
                        <th scope="col">할인율</th>
                        <th scope="col">보유 / 유효기간</th>
                    </tr>
                </thead>
                <tbody>
<?php
if( strlen( $_ShopInfo->getMemid() ) > 0 ){
    if( count( $possibleCouponLayer ) > 0 ){
        foreach( $possibleCouponLayer as $clVal ){
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
        <div>
    <!-- </div>
    </div>
</div> -->
</body>
