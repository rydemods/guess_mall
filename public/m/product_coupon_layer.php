<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
include_once($Dir."lib/basket.class.php");
include_once($Dir."lib/order.class.php");
include_once($Dir."lib/coupon.class.php");

$basketidxs = $_REQUEST['basketidxs'];
$prCouponObj=$_POST["prCouponObj"];
//$basketidxs = '258513|258512|258490|258492|258488|258474';
$productImgPath = $Dir.DataDir."shopimages/product/";

$Order = new Order();
$Order->order_setting( $basketidxs ); //주문할 장바구니 정보
$_odata = $Order->get_order_object(); //주문에 들어가는 상품정보
foreach( $_odata as $_proData =>$_proObj ){
	$brandVenderArr[$_proObj['brand']]	=  $_proObj['vender'];
}
$brandArr = ProductToBrand_Sort( $_odata );
#쿠폰 설정
$_CouponInfo = new CouponInfo();
# 회원쿠폰
$_CouponInfo->search_member_coupon( $_ShopInfo->memid, 1, 1 );
$memCoupon      = $_CouponInfo->mem_coupon;
#상품쿠폰과 장바구니 쿠폰을 나눈다
$basket_coupon = array();
$product_coupon = array();
$deliver_coupon = array();
$chk_coupon     = array();
foreach( $memCoupon as $couponVal ){
    if( $couponVal->coupon_use_type == '1' && $couponVal->coupon_type != '9' ){
        if( array_search( $couponVal->coupon_code, $chk_coupon) === false ){
            $basket_coupon[] = $couponVal;
            $chk_coupon[]    = $couponVal->coupon_code;
        }
    } else if( $couponVal->coupon_type == '9' ) {
        $deliver_coupon[] = $couponVal;
    }else {
        $product_coupon[] = $couponVal;
    }
}

# 상품 토탈가  + 토탈 수량
if( strlen( $basketidxs ) > 0 ){
    $total_sql = "
        SELECT
            SUM( ( sellprice + option_price ) * quantity  ) AS total_price_sum, SUM( quantity )::int AS total_qty
        FROM
            (
                SELECT 
                    p.sellprice, bk.quantity,  COALESCE( po.option_price, 0 ) AS option_price
                FROM 
                    tblbasket AS bk
                JOIN
                    tblproduct AS p ON ( bk.productcode = p.productcode )  
                LEFT JOIN
                    tblproduct_option AS po ON ( bk.productcode = po.productcode AND bk.optionarr = po.option_code )
                WHERE 
                    id = '".$_ShopInfo->getMemid()."' 
                AND
                    basketidx IN ( ".str_replace( '|', ',', $basketidxs )." )
        ) AS basket
    ";
    $total_res = pmysql_query( $total_sql, get_db_conn() );
    $total_row = pmysql_fetch_object( $total_res );
    pmysql_free_result( $total_res );
}

foreach($prCouponObj as $pco=>$pcov){
	$coupon_ci_no[$pcov[obj][basketidx]]=$pcov[obj][ci_no];
}

?>
<div class="list_type">
	<table class="list_with_radio">
		<colgroup>
			<col style="width:40px;">
			<col style="width:auto;">
		</colgroup>
<?

$couponIndex = 0; // radiobox 고유번호
$productIndex = 0; // 상품별 radiobox 고유번호
if( count( $brandArr ) > 0 ){
    foreach( $brandArr as $brand => $basket ){

        foreach( $basket as $bKey => $bVal ){

            $vender      = $brandVenderArr[$brand];                                                   // 벤더 코드
            $basketidx   = $bVal['basketidx'];                                      // 장바구니 번호
            $productcode = $bVal['productcode'];                                    // 상품코드
            $productname = $bVal['productname'];                                    // 상품명
            $price       = $bVal['price'];                                          // 상품가격
            $quantity    = $bVal['quantity'];                                       // 상품수량
            $productImg  = getProductImage( $productImgPath, $bVal['tinyimage'] );  // 상품 이미지
            $opt_price   = 0;                                                       // 옵션가격
            $opt_text    = '';                                                      // 옵션 내용
            $opt_subject = explode( '@#', $bVal['option_subject'] );                // 옵션 항목명
            # 옵션 가격을 구한다
            if( count( $bVal['option'] ) > 0 ){
                foreach( $bVal['option'] as $optKey => $optVal ){
                    $opt_price += $optVal['option_price'];
                    if( $optVal['option_type'] == '1' ){
                        $tmp_code = explode( chr(30), $optVal['option_code'] );
                        //exdebug( $tmp_code );
                        $opt_text .= $tmp_code[0].' : '.$tmp_code[1].'<br>'.PHP_EOL;
                    } else {
                        $tmp_code = explode( chr( 30 ), $optVal['option_code'] );
                        foreach( $opt_subject as $sKey => $sVal ){
                            $opt_text .= $sVal.' : '.$tmp_code[$sKey].'<br>'.PHP_EOL;
                        }
                    }
                }
            }
            # text 옵션 내용
            if( strlen( $bVal['text_opt_subject'] ) > 0 ){
                $tmp_text_opt_subject = explode( '@#', $bVal['text_opt_subject'] );
                $tmp_text_opt_content = explode( '@#', $bVal['text_opt_content'] );
                foreach( $tmp_text_opt_subject as $tKey => $tVal ){
                    if( strlen( $tmp_text_opt_content[$tKey] ) > 0 ){
                        $opt_text .= $tVal.' : '.$tmp_text_opt_content[$tKey].'<br>'.PHP_EOL;
                    }
                }
            }
            $product_price = ( $price + $opt_price ) * $quantity;                   // 총 옵션가격

		foreach( $product_coupon as $prcouponKey=>$prcouponVal ){
			if( $_CouponInfo->check_coupon_product( $productcode, 2, $prcouponVal ) ){
				// 사용조건 체크
				if( $prcouponVal->mini_quantity == 0 || ( $prcouponVal->mini_type == 'P' && $prcouponVal->mini_quantity <= $total_row->total_price_sum ) 
					|| ( $prcouponVal->mini_type == 'Q' && $prcouponVal->mini_quantity <= $total_row->total_qty ) 
				){
					if($prcouponVal->sale_type<=2) $dan="%";
					else $dan="원";

					$maxPrice = $prcouponVal->sale_max_money?" (최대 ".number_format($prcouponVal->sale_max_money)."원)":'';
					$sale_text	= number_format($prcouponVal->sale_money).$dan.$maxPrice." 할인";
					
					$dis_check="";
					$dis_name="";
					foreach($prCouponObj as $pco=>$pcov){
						if($pcov[obj][basketidx]==$basketidx && $pcov[obj][ci_no]==$prcouponVal->ci_no){
							$dis_check="checked";
						}else if($pcov[obj][ci_no]==$prcouponVal->ci_no){
							$dis_check="disabled";
							list($dis_name)=pmysql_fetch("select productname from tblbasket b left join tblproduct p on (b.productcode=p.productcode) where b.basketidx='".$pcov[obj][basketidx]."'");
						}
					}
?>
					<tr>
						<th><input type="radio" class="radio_def" name="product_coupon" value="<?=$prcouponVal->ci_no?>"
								data-cp-code="<?=$prcouponVal->coupon_code?>"
								data-pr-price="<?=$product_price?>"
								data-basketidx="<?=$basketidx?>"
								id='no_<?=$couponIndex?>'
								<?=$dis_check?>></th>
						<td>
							<label for="no_<?=$couponIndex?>">
								<p><?=$prcouponVal->coupon_name?></p>
								<p class="point-color"><?=$sale_text?> <?if($dis_name){?>- <?=$dis_name?> 선택<?}?></p>
							</label>
						</td>
					</tr>

<?php
				$couponIndex++;

				}
			} // coupon_chk if
		} // product_coupon foreach

            $productIndex++;
        }
    }
}

?>
		</tbody>
	</table>

	<div class="btn_area">
		<ul class="ea2">
			<li><a href="javascript:;" class="btn-line h-large" onclick="javascript:prd_coupon_cancel(<?=$basketidxs?>);">취소</a></li>
			<li><a href="javascript:;" class="btn-point h-large" onclick="javascript:set_product_coupon();">적용</a></li>
		</ul>
	</div>
</div>
