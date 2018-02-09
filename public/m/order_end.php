<?
//$subTitle = "주문하기";
$Dir = '../';
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

$ordercode=$_REQUEST["ordercode"];
//if( $_SERVER['REMOTE_ADDR'] == '218.234.32.12' )$ordercode = "2016032217411014212A";

$sql = "SELECT * FROM tblorderinfo WHERE ordercode='{$ordercode}' ";
$result=pmysql_query($sql,get_db_conn());
if($row=pmysql_fetch_object($result)) {
	$_ord = $row;

	if($_ord){
		$addr_tmp = explode("주소 :",$_ord->receiver_addr);
		$_ord->zipcode=str_replace("우편번호 :","",$addr_tmp[0]);
		$_ord->address=$addr_tmp[1];
	}
	if (strstr("B", $_ord->paymethod[0]) || (strstr("G", $_ord->paymethod[0]) && $_ord->pay_flag =='N') || (strstr("VOQCPMY", $_ord->paymethod[0]) && strcmp($_ord->pay_flag,"0000")==0)){// 주문성공
	} else {//주문실패
		// 주문의 상태값을 변경한다.
		$osu_sql = " UPDATE tblorderinfo SET ";
		$osu_sql.= "oi_step2 = '54' ";
		$osu_sql.= " WHERE ordercode='".trim($ordercode)."' AND oi_step2 != 54";
		//echo $osu_sql;
		pmysql_query($osu_sql,get_db_conn());

		//주문상품의 상태값을 변경한다.
		$osup_sql = "Update tblorderproduct Set op_step = '54' Where ordercode='".trim($ordercode)."' AND op_step != 54";
		pmysql_query($osup_sql,get_db_conn());
	}

}else if( $fail_msg == 'msg' ){
	echo "<html></head><body onload=\"alert('배송메세지에 사용할 수 없는 문자열이 포함 되어 있습니다'); location.href='/m'\"></body></html>";
	exit;
}else {
	echo "<html></head><body onload=\"alert('오류발생,관리자에게 문의해주세요'); location.href='/m'\"></body></html>";
	exit;
}
$imgPath = $Dir.ImageDir.'product/';
# 주문 세팅
$sql = "SELECT op.vender, pr.brand, op.ordercode, op.productcode, op.productname, ";
$sql.= "op.opt1_name, op.opt2_name, op.addcode, op.quantity, ";
$sql.= "op.price, op.reserve, op.date, op.selfcode, ";
$sql.= "op.option_price, op.option_quantity, op.coupon_price, op.deli_price, op.use_point, ";
$sql.= "op.text_opt_subject, op.text_opt_content, op.option_price_text, ";
$sql.= "op.option_type, pr.tinyimage, pr.minimage, pr.consumerprice, ";
$sql.= "vi.deli_mini, op.delivery_type, op.store_code, op.reservation_date, pr.colorcode, pr.prodcode ";
$sql.= "FROM tblorderproduct op LEFT JOIN tblproduct pr ON op.productcode = pr.productcode ";
$sql.= "LEFT JOIN tblvenderinfo vi ON op.vender = vi.vender ";
$sql.= "WHERE op.ordercode = '".$ordercode."' ";
$sql.= "ORDER BY op.vender ASC, op.productcode ASC, op.date DESC ";
$result=pmysql_query($sql,get_db_conn());

while($row=pmysql_fetch_array($result)) {
	$productArr[] = $row;
}
pmysql_free_result( $result );

include_once('outline/header_m.php');
//include_once('sub_header.inc.php');

foreach( $productArr as $_proData =>$_proObj ){
	//exdebug($_proObj['vender']);
	$brandVenderArr[$_proObj['brand']]	=  $_proObj['vender'];
}


$brandArr = ProductToBrand_Sort( $productArr );
?>

<SCRIPT LANGUAGE="JavaScript">
<!--
	$(document).ready(function(){
		$(".CLS_OrderView").click(function(){
			$("form[name='mypageOrderViewFrm']").submit();
		})
	});
	function store_map(storecode){
		if( storecode ){
			$.ajax({
				cache: false,
				type: 'POST',
				url: 'ajax_store_map.php',
				data : { storecode : storecode },
				success: function(data) {
					$(".store_view").html(data);
					$('.pop-infoStore').show();
				//	$('html,body').css('position','fixed');
				}
			});
		}
		
	}

//-->
</SCRIPT>



<!-- 내용 -->
<main id="content" class="subpage">
	
	<!-- 매장안내 팝업 -->
	<section class="pop_layer layer_store_info pop-infoStore">
		<div class="inner">
			<h3 class="title">매장 위치정보 <button type="button" class="btn_close">닫기</button></h3>
			<div class="select_store store_view">
				
			</div><!-- //.select_store -->
		</div>
	</section>
	<!-- //매장안내 팝업 -->

	<section class="page_local">
		<h2 class="page_title">
			<a href="javascript:history.back();" class="prev">이전페이지</a>
			<span>주문/결제</span>
		</h2>
		<div class="page_step">
			<ul class="clear">
				<li><span class="icon_order_step01"></span>장바구니</li>
				<li><span class="icon_order_step02"></span>주문하기</li>
				<li class="on"><span class="icon_order_step03"></span>주문완료</li>
			</ul>
		</div>
	</section><!-- //.page_local -->

	<section class="orderpage">
			<?php
	if (strstr("B", $_ord->paymethod[0]) || (strstr("G", $_ord->paymethod[0]) && $_ord->pay_flag =='N') || (strstr("VOQCPMY", $_ord->paymethod[0]) && strcmp($_ord->pay_flag,"0000")==0)){
		if(strstr("CPMY", $_ord->paymethod[0])){
			$step_type="결제완료";
		}else{
			$step_type="주문완료";
		}
?>
		<div class="result_msg">
			<p class="ment">주문이 정상적으로 완료되었습니다.</p>
			<p class="point-color mt-10">주문번호 : <?=$ordercode?></p>
		</div>
<?php
	}else{
		$step_type="결제취소";
?>
		<div class="result_msg">
			<p class="ment">결제가 취소 되었습니다.</p>
			<p class="point-color mt-10">주문번호 : <?=$ordercode?></p>
		</div>
<?	} ?>
<?php
$sumprice = 0;
$in_reserve = 0;
foreach( $brandArr as $brand=>$productArr ){
	$brand_name = get_brand_name( $brand );
	$vender	=$brandVenderArr[$brand];
	$vender_price = 0;
	$vender_deli_price = 0;
?>
		<div class="list_cart">
			<!-- 브랜드별 반복 -->
			<div class="list_brand  with_deli_info">
				<h3 class="cart_tit">주문상품</h3>
				<ul class="cart_goods">
<?php
	$product_price = 0;
	foreach( $productArr as $key=>$val ){
		$product_option="";
		$storeData = getStoreData($val['store_code']);
		$product_price = ( $val['price'] + $val['option_price'] ) * $val['quantity'];
		$vender_price += $product_price;
		$vender_deli_price += $val['deli_price'];
		$in_reserve += $val['reserve'];
		$brand_product_name = get_brand_name( $val[brand] );

		if($val[colorcode]){
			$product_option.= "색상 : ".$val[colorcode];
		}
		if( strlen( $val['opt1_name'] ) > 0  || strlen( $val['text_opt_subject'] ) > 0 ){
			$tmp_opt_subject = explode( '@#', $val['opt1_name'] );
			$tmp_opt_content = explode( chr(30), $val['opt2_name'] );
			if($tmp_opt_subject){
				$product_option.= " / ";
				foreach( $tmp_opt_subject as $subjectKey=>$subjectVal ){
					$product_option.= $subjectVal.' : '.$tmp_opt_content[$subjectKey];
				} // opt_subject foreach
			}
			if( strlen( $val['text_opt_subject'] ) > 0 ){
				$tmp_text_opt_subject = explode( '@#', $val['text_opt_subject'] );
				$tmp_text_opt_content = explode( '@#', $val['text_opt_content'] );
				if($tmp_text_opt_subject){
					$product_option.= " / ";
					foreach( $tmp_text_opt_subject as $subjectKey=>$subjectVal ){
						$product_option.= ' [ '.$subjectVal.' : '.$tmp_text_opt_content[$subjectKey];
					} // opt_subject foreach
				}
			}
			if( ($val['option_price'] * $val['quantity']) > 0 ){
				$product_option.= '(추가금액 : '.number_format( $val['option_price'] * $val['quantity'] ).')';
			}
		} else {
			$product_option.= "-";
		}

		if( strlen( $_ShopInfo->getMemid() ) == 0 ){ // 로그인을 안했을 경우
			$val['reserve']	= 0;
		}

?>
					<!-- 상품 반복 -->
					<li>
						<div class="cart_wrap">
							<div class="clear">
								<div class="goods_area">
									<div class="img"><a href="<?=$Dir?>m/productdetail.php?productcode=<?=$val['productcode']?>"><img src="<?= getProductImage( $imgPath, $val['tinyimage'] )?>" alt="상품 이미지"></a></div>
									<div class="info">
										<p class="brand"><?=$brand_product_name?></p>
										<p class="name"><?=$val['productname']?></p><!-- [D] 상품명은 최대 2줄까지 노출 -->
										<?if($val[prodcode]){?>
										<p class="option">품번: <?=$val[prodcode]?></p>
										<?}?>
										<p class="option"><?=$product_option?> / <?=$val['quantity']?>개</p>
										<p class="price">￦ <?=number_format( $product_price )?></p>
										<?if($val['reserve']){?>
										<div class="save point-color">적립예정 포인트 <?=number_format($val['reserve'])?> P</div>
										<?}?>
										<span class="status_tag btn-point h-small"><?=$step_type?></span>
									</div>
								</div>
							</div>
						</div><!-- //.cart_wrap -->
						<div class="delibox">
							<?if($val[delivery_type] == '1' || $val[delivery_type] == '3'){	//2016-10-07 libe90 매장발송 정보표시?>
							<h4 class="cart_tit">
								<?=$arrDeliveryType[$val[delivery_type]]?>
								<div class="wrap_bubble today_shipping">
									<div class="btn_bubble"><button type="button" class="btn_help">?</button></div>
									<div class="pop_bubble">
										<div class="inner">
											<button type="button" class="btn_pop_close">닫기</button>
											<?if($val[delivery_type] == '1'){?>
											<div class="container">
												<p><?=$val[reservation_date]?>일에 <?=$storeData['name']?>에서 수령하시면 됩니다.</p>
											</div>
											<?}else if($val[delivery_type] == '3'){?>
											<div class="container"><p>선택하신 매장을 방문하여 입어보고 수령하시면 됩니다. <br>(재고가 있을 경우 : 당일~3일 이내 방문수령 / 재고가 없을 경우 : 3일~5일 이내 방문수령)</p></div>
											<?}?>
										</div>
									</div>
								</div><!-- //.wrap_bubble -->
							</h4>
							<div class="change_store">
								<span class="store_name"><?=$storeData['name']?> <?if($val[delivery_type] == '1'){?>(<?=$val[reservation_date]?>)<?}?></span>
								<a href="javascript:store_map('<?=$val[store_code]?>');" class="btn_store_info btn-basic">매장안내</a>
							</div>
							<?}else{?>
							<h4 class="cart_tit">
								택배수령
								<div class="wrap_bubble today_shipping">
									<div class="btn_bubble"><button type="button" class="btn_help">?</button></div>
									<div class="pop_bubble">
										<div class="inner">
											<button type="button" class="btn_pop_close">닫기</button>
											<div class="container">
												<p>본사물류 또는 해당 브랜드 매장에서 택배로 고객님께 상품이 배송됩니다. <br>(주문 완료 후, 3~5일 이내 수령)</p>
											</div>
										</div>
									</div>
								</div><!-- //.wrap_bubble -->
							</h4>
							<?}?>
						</div><!-- //.delibox -->
					</li>
					<!-- //상품 반복 -->
	<?}?>
				</ul><!-- //.cart_goods -->
				<div class="cart_calc">
					<ul>
						<li>
							<label>상품합계</label>
							<span>￦ <?=number_format( $vender_price )?></span>
						</li>
						<li>
							<label>배송비</label>
							<span> ￦ <?=number_format( $vender_deli_price )?></span>
						</li>
						<li class="total">
							<label>합계금액</label>
							<span>￦ <?=number_format( $vender_price + $vender_deli_price )?></span>
						</li>
					</ul>
				</div>
			</div>
			<!-- //브랜드별 반복 -->
		</div><!-- //.list_cart -->
<?php
} // vender foreach
$script_price = $_ord->price + $_ord->deli_price - $_ord->dc_price - $_ord->reserve - $_ord->point;
if( strlen( $_ShopInfo->getMemid() ) == 0 ){ // 로그인을 안했을 경우
	$in_reserve	= 0;
}
?>
		<!-- 할인 및 결제정보 -->
		<div class="order_table">
			<h3 class="cart_tit">할인 및 결제정보</h3>
			<table class="th-left">
				<colgroup>
					<col style="width:29.37%;">
					<col style="width:auto;">
				</colgroup>
				<tbody>
					<tr>
						<th>총 상품금액</th>
						<td>￦ <?=number_format( $_ord->price )?></td>
					</tr>
					<tr>
						<th>포인트 사용</th>
						<td><span class="point-color">- <?=number_format( $_ord->reserve )?> P</span></td>
					</tr>
					<tr>
						<th>E포인트 사용</th>
						<td><span class="point-color">- <?=number_format( $_ord->point )?> P</span></td>
					</tr>
					<tr>
						<th>쿠폰할인</th>
						<td><span class="point-color">- ￦ <?=number_format( $_ord->dc_price )?></span></td>
					</tr>
					<tr>
						<th>배송비</th>
						<td>￦ <?=number_format ( $_ord->deli_price )?></td>
					</tr>
					<tr>
						<th>실 결제금액</th>
						<td><strong class="point-color">￦ <?=number_format( $_ord->price + $_ord->deli_price - $_ord->dc_price - $_ord->reserve - $_ord->point )?></strong></td>
					</tr>
					<?
						$pay_title="";
						if(strstr("VCPMY", $_ord->paymethod[0])) {
							$subject = "결제일자";
							$o_year = substr($ordercode, 0, 4);
							$o_month = substr($ordercode, 4, 2);
							$o_day = substr($ordercode, 6, 2);
							$o_hour = substr($ordercode, 8, 2);
							$o_min = substr($ordercode, 10, 2);
							$o_sec = substr($ordercode, 12, 2);

							$msg = $o_year."-".$o_month."-".$o_day." ".$o_hour.":".$o_min.":".$o_sec;

							$pay_title=$arpm[$_ord->paymethod[0]]."(".$subject." : ".$msg.")";
						} else if (strstr("BOQ", $_ord->paymethod[0])) {
							$_ord_pay_data = explode(" ", $_ord->pay_data);
							if(strstr("B", $_ord->paymethod[0])){
								//$msg = "입금자명 : ".$_ord->bank_sender."<br>입금은행 : ".$_ord_pay_data[0]."<br>입금계좌 : ".$_ord_pay_data[1].' '.$_ord_pay_data[2];
								$msg = $_ord->pay_data;
								$pay_title=$msg;
							}else{
								if($_ord->pay_flag=="0000"){
									$add_msg = " (입금 대기중)";
								}
								if(strstr("O", $_ord->paymethod[0])){
									$msg = "입금은행 : ".$_ord_pay_data[0]."<br>입금계좌 : ".$_ord_pay_data[1].' '.$_ord_pay_data[2].$add_msg;
								}else if(strstr("Q", $_ord->paymethod[0])){
									$msg = "입금은행 : ".$_ord_pay_data[0]."<br>입금계좌 : ".$_ord_pay_data[1].' '.$_ord_pay_data[2].$add_msg;
								}
								$pay_title=$arpm[$_ord->paymethod[0]]."<br>".$msg;
							}
							$subject = "추가정보";

							
						}

						if ($_ord->receiver_addr) {
							$_ord_receiver_addr	= $_ord->receiver_addr;
							$_ord_receiver_addr	= str_replace("우편번호 :","[",$_ord_receiver_addr);
							$_ord_receiver_addr	= str_replace("주소 :","]",$_ord_receiver_addr);
						}
					?>
					<tr>
						<th>결제방법</th>
						<td><?=$pay_title?></td>
					</tr>
				</tbody>
			</table>
		</div><!-- //.order_table -->
		<!-- //할인 및 결제정보 -->

		<!-- 배송지 정보 -->
		<div class="order_table">
			<h3 class="cart_tit">배송지 정보</h3>
			<table class="th-left">
				<colgroup>
					<col style="width:29.37%;">
					<col style="width:auto;">
				</colgroup>
				<tbody>
					<tr>
						<th>받는사람</th>
						<td><?=$_ord->receiver_name?></td>
					</tr>
					<tr>
						<th>휴대전화</th>
						<td><?=$_ord->receiver_tel2?></td>
					</tr>
					<tr>
						<th>전화번호(선택)</th>
						<td><?=$_ord->receiver_tel1?></td>
					</tr>
					<tr>
						<th>주소</th>
						<td>
							<p class="post"><?=$_ord_receiver_addr?></p>
							
						</td>
					</tr>
					<tr>
						<th>배송 요청사항</th>
						<td>
							<?if($_ord->order_msg2){?>
								<?=$_ord->order_msg2?>
							<?}else{?>
								-
							<?}?>
						</td>
					</tr>
				</tbody>
			</table>
		</div><!-- //.order_table -->
		<!-- //배송지 정보 -->

		<div class="btn_area mt-20 mr-10 ml-10">
			<ul class="ea2">
				<li><a href="/m" class="btn-line h-input ">쇼핑 계속하기</a></li>
				<li><a href="javascript:;" class="btn-point h-input CLS_OrderView">주문내역 확인하기</a></li>
			</ul>
		</div>

	</section><!-- //.orderpage -->

</main>
<!-- //내용 -->
<form name='mypageOrderViewFrm' method='POST' action='<?=$Dir?>m/mypage_orderlist_view.php'>
	<input type='hidden' name='ordercode' value = '<?=$ordercode?>'>
</form>
<!-- WIDERPLANET PURCHASE SCRIPT START 2017.9.19 -->
<div id="wp_tg_cts" style="display:none;"></div>
<?php
$wptg_arr   = array();
$wptg_items = '';
foreach( $productArr as $prKey=>$prVal ){
	$category =  substr($prVal['productcode'],0,12);
    $wptg_arr[] = '{i:"'.$prVal['productcode'].'", t:"'.$prVal['productname'].'", p:"'.$prVal['price'].'", q:"'.$prVal['quantity'].'" }';
	$nsm_arr[] = '{pd:"'.$prVal['productcode'].'",pn:"'.$prVal['productname'].'",am:"'.$prVal['price'].'",qy:"'.$prVal['quantity'].'",ct:"'.$category.'" }';
}
$wptg_items = implode( ',', $wptg_arr );
$nsm_items = implode( ',', $nsm_arr );
?>
<script type="text/javascript">
var wptg_tagscript_vars = wptg_tagscript_vars || [];
wptg_tagscript_vars.push(
(function() {
	return {
		wp_hcuid:"",  	
		ti:"37370",
		ty:"PurchaseComplete",
		device:"mobile"
		,items:[
			 <?=$wptg_items?>
		]
	};
}));
</script>
<script type="text/javascript" async src="//cdn-aitg.widerplanet.com/js/wp_astg_4.0.js"></script>
<!-- // WIDERPLANET PURCHASE SCRIPT END 2017.9.19 -->

<!-- *) 제품결제처리 -->
<!-- NSM Site Analyst Mobile eCommerce (Cart_Inout) v2.0 Start -->
<script type='text/javascript'>
var SA_Cart=(function(){
	var c=<?=$nsm_items?>;
	var u=(!SA_Cart)?[]:SA_Cart; u[c.pd]=c;return u;
})();
</script>

<script type='text/javascript'>
	var m_order_code='<?=$ordercode?>';		// 주문코드 필수 입력 
	var m_buy="finish"; //구매 완료 변수(finish 고정값)
</script>


<!-- *) 공통 분석스크립트  -->
<!-- 1-script.txt -->

<? include_once('outline/footer_m.php'); ?>
