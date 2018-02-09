<?php
if(!stristr($_SERVER["HTTP_REFERER"],$_SERVER["HTTP_HOST"])) {
	header("HTTP/1.0 404 Not Found");
	exit;
}
?>
    var _sum_price         = 0; // 상품 결제가
    var _total_prdc        = 0; // 상품쿠폰가

    var _prCoupon_area     = null; // 상품별 영역
    var _prCouponObj       = [];   // 상품쿠폰 내용

    var useand_pc_yn = "N";
    var all_type     = "N";
    // 쿠폰 내용 초기화
    $(document).ready( function() {

        _prCoupon_area    = $('ul[name="product_area"]');
        _sum_price        = parseInt( $('#total_sum').val() );  // 총 상품 가격
        useand_pc_yn      = $('#useand_pc_yn').val();
        all_type          = $('#all_type').val();

        // 상품쿠폰 obj를 초기화
        $.each( _prCoupon_area, function( _i, _obj ) {
            var prCoupon = $(this).find('input:radio:checked');
            var tmp_obj = {
                "basketidx"     : prCoupon.data('basketidx'),
                "ci_no"         : prCoupon.val(),
                "coupon_code"   : prCoupon.data('cp-code'),
                "product_price" : prCoupon.data('pr-price')
            };
            _prCouponObj[_i] = { "obj" : tmp_obj };
        });

    });
    // 상품쿠폰 클릭시
    $(document).on( 'click', 'input[name^=product_coupon]', function (event) {
        var area_idx    = _prCoupon_area.index( $(this).parent().parent() );
        var basketidx   = $(this).data('basketidx');
        var ci_no       = $(this).val();
        var coupon_code = $(this).data('cp-code');
        var price       = $(this).data('pr-price');
        var tmp_obj     = {
            "basketidx"     : basketidx,
            "ci_no"         : ci_no,
            "coupon_code"   : coupon_code,
            "product_price" : price
        };

        if( _prCouponObj[area_idx].obj.ci_no == ci_no ) return; // 쿠폰 재선택 방지

        $.each( _prCoupon_area, function( _i, _obj ) {
            $.each( $(this).find('input:radio'), function( i, obj ){
                if( basketidx != $(this).data('basketidx') ){
                    if( ci_no == $(this).val() && ci_no != '' ){ // 같은 쿠폰은 중복 처리가 안되게 disabled 시킨다
                        $(this).attr( 'disabled', 'true' );
                    }
                    if( _prCouponObj[area_idx].obj.ci_no == $(this).val() ){ // 이전 선택되었던 쿠폰의 disabled를 풀어준다
                        $(this).removeAttr( 'disabled' );
                    }
                }
            });

        });
        $.extend( _prCouponObj[area_idx].obj, tmp_obj );
        pc_price_sum( area_idx );
    });
    
    // 상품 쿠폰 취소 / 초기화
    function prd_coupon_cancel(){
        $.each( _prCoupon_area, function( _i, _obj ) {
            $(this).find('input:radio').last().trigger('click');
        });
        pc_price_sum();
    }
    // 상품쿠폰 가격을 계산 및 합산
    function pc_price_sum( area_idx ){
        _total_prdc = 0;

        $.each( _prCouponObj , function( _i, _obj ){
            if( _obj.obj.ci_no != '' ){
                $.ajax({
                    method : "POST",
                    url : "../front/ajax_coupon_select.php",
                    data : { mode : 'P01', sellprice : _obj.obj.product_price , ci_no : _obj.obj.ci_no },
                    dataType : "json"
                }).done ( function( data ){
                    if( data.mini_price > _sum_price ){
                        alert('구매 금액이 ' + comma( data.mini_price ) + '이상 주문시 가능합니다.' );
                        prd_coupon_cancel();
                    } else {
                        var tmp_obj = {
                            coupon_type : data.coupon_type,
                            dc : data.dc,
                            type : data.type
                        }
                        $.extend( _obj.obj, tmp_obj );
                        _total_prdc += data.dc;
                        if( $.type( area_idx ) !== 'null' ) {
                            $( _prCoupon_area ).eq( area_idx ).next().html( '할인금액 ' + comma( data.dc ) );
                        }
                    }
                });
            } else {
                $.extend( _obj.obj, { "dc" : 0 } );
                if( $.type( area_idx ) !== 'null' ) {
                    $( _prCoupon_area ).eq( area_idx ).next().html( '할인금액 0');
                }
            }
        });
    }
	
    // 결제로 넘어갈 쿠폰값을 레이어에 넘겨준다
    function set_coupon_layer(){
        var prd_layer        = $('#ID_prd_coupon_layer'); // 상품쿠폰이 담길 레이어 위치
        var pr_coupon_html   = '';

        // 상품쿠폰
        $.each( _prCouponObj, function( _i, _obj ){
            if( _obj.obj.ci_no != '' ){
                pr_coupon_html += '<input type="hidden" name="prcoupon_bridx[]" value="' + _obj.obj.basketidx + '" >';
                pr_coupon_html += '<input type="hidden" name="prcoupon_ci_no[]" value="' + _obj.obj.ci_no + '" >';
            }
        });

        $( prd_layer ).html( pr_coupon_html );

    }
    
