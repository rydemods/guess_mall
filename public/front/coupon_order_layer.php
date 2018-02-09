<?php
#lib.php에 include 시키기전에 오류가 안나도록 불러오기로 한다
# lib에 쿠폰과 관련된 함수는 libcoupon.php 미리 분류시킨다
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
include_once($Dir."lib/basket.class.php");
include_once($Dir."lib/order.class.php");
include_once($Dir."lib/coupon.class.php");

$basketidxs = $_REQUEST['basketidxs'];
//$basketidxs = '258457|258317|258316';

$Order = new Order();
$Order->order_setting( $basketidxs ); //주문할 장바구니 정보
$_odata = $Order->get_order_object(); //주문에 들어가는 상품정보
$venderArr = ProductToVender_Sort( $_odata );

#쿠폰 설정
$_CouponInfo = new CouponInfo( '6' );

# 회원쿠폰
// 회원 쿠폰 목록 찾기
$_CouponInfo->search_member_coupon( $_ShopInfo->memid );
// 해당 상품에서 사용 가능한지 확인 
$memCoupon      = $_CouponInfo->mem_coupon;

?>
<html/>
<head>
<link rel="stylesheet" href="<?=$Dir?>static/css/common.css">
<link rel="stylesheet" href="<?=$Dir?>static/css/component.css">
<link rel="stylesheet" href="<?=$Dir?>static/css/content.css">
<!-- <link rel="stylesheet" href="<?=$Dir?>static/css/jquery.mCustomScrollbar-3.1.3.css">
<link rel="stylesheet" href="<?=$Dir?>static/css/ion.rangeSlider.css">
<link rel="stylesheet" href="<?=$Dir?>static/css/ion.rangeSlider.skinHTML5.css"> -->
<script type="text/javascript" src="<?=$Dir?>static/js/jquery-1.12.0.min.js"></script>
<!-- <script type="text/javascript" src="<?=$Dir?>static/js/TweenMax-1.18.2.min.js"></script>
<script type="text/javascript" src="<?=$Dir?>static/js/jquery.mCustomScrollbar.concat-3.1.3.min.js"></script>
<script type="text/javascript" src="<?=$Dir?>static/js/ion.rangeSlider.min.js"></script>
<script type="text/javascript" src="<?=$Dir?>static/js/jquery.nanoscroller.js"></script>
<script type="text/javascript" src="<?=$Dir?>static/js/jquery.nanoscroller.min.js"></script>
<script type="text/javascript" src="<?=$Dir?>static/js/jquery.bxslider.min.js"></script> -->
<!-- <script type="text/javascript" src="<?=$Dir?>static/js/deco_ui.js"></script> -->
<script src="<?=$Dir?>lib/lib.js.php" type="text/javascript"></script>
</head>
<body style=' padding-top: 0px !important; '>
<script>

</script>

        <div class="layer-content order-coupon-iframe">
            <p class="title">상품 쿠폰 선택</p>
            <?php
$couponIndex = 0; // radiobox 고유번호
$productIndex = 0; // 상품별 radiobox 고유번호
foreach( $venderArr as $vender=>$vederObj ){
    foreach( $vederObj as $product ) {
        $tmp_opt_price = 0;
?>
                <div class="goods-coupon">
                    <div class="inner">
                        <p class="pic">
                            
                            <img src='<?=getProductImage( $productImgPath, $product['tinyimage'] )?>' >
                        </p>
                        <p class="info">
                            <span><?=get_vender_name( $vender )?></span>
                            <span><?=$product['productname']?></span>
<?php
        if( count( $product['option'] ) > 0 ){
            if( $product['option_type'] == 1 ){ // 독립형 옵션
                $tmp_opt_subject = explode( '@#', $product['option_subject'] );
                foreach( $product['option'] as $optKey=>$optVal ){
                    $tmp_opt_content = explode( chr(30), $optVal['option_code'] );
                    echo $tmp_opt_subject[$optKey].' : '.$tmp_opt_content[1].'<br>';
                    $tmp_opt_price += $optVal['option_price'] * $product['option_quantity'];
                } // option foreach
            } else { // 조합형 옵션
                $tmp_option = $product['option'][0];
                $tmp_opt_subject = explode( '@#', $product['option_subject'] );
                $tmp_opt_contetnt = explode( chr(30), $tmp_option['option_code'] );
                foreach( $tmp_opt_subject as $optKey=>$optVal ){
                    echo $optVal.' : '.$tmp_opt_contetnt[$optKey].'<br>';
                }
                $tmp_opt_price += $tmp_option['option_price'] * $product['option_quantity'];

            } // option_type else
        } // count option if

        if( $product['text_opt_content'] ){ // 추가문구 옵션
            $tmp_text_subejct = explode( '@#', $product['text_opt_subject'] );
            $text_opt_content = explode( '@#', $product['text_opt_content'] );
            foreach( $text_opt_content as $textKey=>$textVal ){
                if( $textVal != '' ) {
                    echo $tmp_text_subejct[$textKey].' : '.$textVal.'<br>';
                }
            }
        }
        $prPrice = ( $product['price'] * $product['option_quantity'] ) + $tmp_opt_price;
?>
                        </p>
                        
                    </div>
                    <ul class="coupon-choice2" data-productcode='<?=$product['productcode']?>' >
<?php
        foreach( $memCoupon as $prcouponKey=>$prcouponVal ){
            if( $prcouponVal->coupon_use_type == '2' && $_CouponInfo->check_coupon_product( $product['productcode'], 2, $prcouponVal ) ){
?>

                        <li>
                            <input type="radio" class="radio-def" id='no_<?=$couponIndex?>' name='coupon_select[<?=$productIndex?>]' 
                                value='<?=$prcouponVal->ci_no?>' data-sellprice='<?=$prPrice?>' data-bridx='<?=$product['basketidx']?>'
                                idx='<?=$productIndex?>' >
                            <label for="no_<?=$couponIndex?>" ><?=$prcouponVal->coupon_name?></label>
                        </li>
<?php
				$couponIndex++;
			} // coupon_chk if
		} // product_coupon foreach
?>
                        <li>
                            <input type="radio" class="radio-def" name="coupon_select[<?=$productIndex?>]" id='coupon-a<?=$productIndex?>' 
                            value='' data-prdouct='' checked idx='<?=$productIndex?>' data-bridx='' data-sellprice='0' >
                            <label for="coupon-a<?=$productIndex?>">선택안함</label>
                        </li>
                    </ul>
                    <p class="coupon-price" id="coupon_price_<?=$productIndex?>" name='NM_coupon_price' >
                            할인금액 0
                    </p>
                </div>
<?php
    $productIndex++;
    } // $vednerObj foreach
}  // venderArr foreach
?>
            <div class="btn-place mb-20"><button class="btn-dib-function" onclick='javascript:set_prcoupon();' ><span>OK</span></button></div>
        <div>

</body>
