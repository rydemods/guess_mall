
<div id="contents">
	<div class="cartOrder-page">

		<article class="cart-order-wrap">
			<header class="progess-title">
				<h2>주문/결제</h2>
				<ul class="flow clear">
					<li><div><i></i><span>STEP 1</span>장바구니</div></li>
					<li><div><i></i><span>STEP 2</span>주문하기</div></li>
					<li class="active"><div><i></i><span>STEP 3</span>주문완료</div></li>
				</ul>
			</header>
			<?php
	if (strstr("B", $_ord->paymethod[0]) || (strstr("G", $_ord->paymethod[0]) && $_ord->pay_flag =='N') || (strstr("VOQCPMY", $_ord->paymethod[0]) && strcmp($_ord->pay_flag,"0000")==0)){
		if(strstr("CPMY", $_ord->paymethod[0])){
			$step_type="결제완료";
		}else{
			$step_type="주문완료";
		}
?>
			<div class="orderEnd-result mt-80">
				<p>주문이 정상적으로 완료되었습니다.</p>
				<p class="point-color">주문번호 : <?=$ordercode?></p>
			</div>
<?php
	}else{
		$step_type="결제취소";
?>
			<div class="orderEnd-result mt-80">
				<p>결제가 취소 되었습니다.</p>
				<p class="point-color">주문번호 : <?=$ordercode?></p>
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
			<section class="mt-60">
				<header class="cart-section-title">
					<h3>주문상품</h3>
				</header>
				<table class="th-top">
					<caption>주문 상품 확인</caption>
					<colgroup>
						<col style="width:auto">
						<col style="width:90px">
						<col style="width:150px">
						<col style="width:140px">
						<col style="width:170px">
						<col style="width:136px">
					</colgroup>
					<thead>
						<tr>
							<th scope="col">상품정보</th>
							<th scope="col">수량</th>
							<th scope="col">적립 포인트</th>
							<th scope="col">판매가</th>
							<th scope="col">배송정보</th>
							<th scope="col">주문상태</th>
						</tr>
					</thead>
					<tbody>
<!-- *) 제품결제처리 -->
<!-- A Square|Site Analyst eCommerce (Cart_Inout) v7.5 Start -->
<!-- Function and Variables Definition Block Start -->
<script language='javascript'>
	var _SA_cnt=0;
	var _SA_pl = Array(1) ;
	var _SA_nl = Array(1) ;
	var _SA_ct = Array(1) ;
	var _SA_pn = Array(1) ;
	var _SA_amt = Array(1) ;
</script>
<!-- Function and Variables Definition Block End-->
<?php
	$product_price = 0;
	foreach( $productArr as $key=>$val ){
		$storeData = getStoreData($val['store_code']);
		$product_price = ( $val['price'] + $val['option_price'] ) * $val['quantity'];
		$vender_price += $product_price;
		$vender_deli_price += $val['deli_price'];
		$in_reserve += $val['reserve'];
		$brand_product_name = get_brand_name( $val[brand] );

?>
<!-- A Square|Site Analyst eCommerce (Cart_Inout) v7.5 Start -->
<!-- Data Allocation (Cart_InOut) -->
<script language='javascript'>
	_SA_amt[_SA_cnt]='<?=$product_price?>';
	_SA_nl[_SA_cnt]='<?=$val[quantity]?>';
	_SA_pl[_SA_cnt]='<?=$val[productcode]?>';
	_SA_pn[_SA_cnt]='<?=$val[productname]?>';
	_SA_ct[_SA_cnt]='<?=substr($val[productcode],0,12)?>';
	_SA_cnt++;
</script>

<!-- A Square|Site Analyst eCommerce (Buy_Finish) v7.5 Start -->
						<tr>
							<td class="pl-25">
								<div class="goods-in-td">
									<div class="thumb-img"><a href="<?=$Dir.FrontDir?>productdetail.php?productcode=<?=$val['productcode']?>"><img src="<?= getProductImage( $imgPath, $val['tinyimage'] )?>" alt="<?=$val['productname']?>"></a></div>
									<div class="info">
										<p class="brand-nm"><?=$brand_product_name?></p>
										<p class="goods-nm"><?=$val['productname']?></p>
										<p class="opt">
											<?php
												if($val[prodcode]){
												echo "품번 : ".$val[prodcode];
												}
												if($val[colorcode]){
													echo " / ";
													echo "색상 : ".$val[colorcode];
												}
												if( strlen( $val['opt1_name'] ) > 0  || strlen( $val['text_opt_subject'] ) > 0 ){
													$tmp_opt_subject = explode( '@#', $val['opt1_name'] );
													$tmp_opt_content = explode( chr(30), $val['opt2_name'] );
													if($tmp_opt_subject){
														echo " / ";
														foreach( $tmp_opt_subject as $subjectKey=>$subjectVal ){
															echo $subjectVal.' : '.$tmp_opt_content[$subjectKey];
														} // opt_subject foreach
													}
													if( strlen( $val['text_opt_subject'] ) > 0 ){
														$tmp_text_opt_subject = explode( '@#', $val['text_opt_subject'] );
														$tmp_text_opt_content = explode( '@#', $val['text_opt_content'] );
														if($tmp_text_opt_subject){
															echo " / ";
															foreach( $tmp_text_opt_subject as $subjectKey=>$subjectVal ){
																echo ' [ '.$subjectVal.' : '.$tmp_text_opt_content[$subjectKey];
															} // opt_subject foreach
														}
													}
													if( ($val['option_price'] * $val['quantity']) > 0 ){
														echo '(추가금액 : '.number_format( $val['option_price'] * $val['quantity'] ).')';
													}
												} else {
													echo "-";
												}

												if( strlen( $_ShopInfo->getMemid() ) == 0 ){ // 로그인을 안했을 경우
													$val['reserve']	= 0;
												}
											?>
											
										</p>
									</div>
								</div>
							</td>
							<td><?=$val['quantity']?></td>
							<td class="txt-toneB"><?=number_format($val['reserve'])?> P</td>
							<td class="txt-toneA">\ <?=number_format( $product_price )?></td>
							<td class="flexible-delivery">
								<?if($val['delivery_type']=="1" || $val['delivery_type']=="3"){?>
									<div class="with-question">
										<strong class="txt-toneA">[<?=$arrDeliveryType[$val['delivery_type']]?>]</strong>
										
										<div class="question-btn">
											<i class="icon-question">배송설명</i>
											<?if($val['delivery_type'] == '1'){?>
											<div class="comment"><?=$val['reservation_date']?>일에 <?=$storeData['name']?>에서 수령하시면 됩니다. </div>
											<?}else if($val['delivery_type'] == '3'){?>
											<div class="comment">선택하신 상품은 당일수령이 가능한 상품입니다. </div>
											<?}?>
										</div>
										
									</div>
									<?if($val['delivery_type'] == '1'){?>
									<strong class="txt-toneA">예약일 : <?=$val['reservation_date']?></strong><div class="pt-5"><?=$storeData['name']?></div>
									<?}else if($val['delivery_type'] == '3'){?>
									<strong class="txt-toneA">\<?=number_format($val['deli_price'])?></strong><div class="pt-5"><?=$storeData['name']?></div>
									<?}?>
								<?}else{?>
									<strong class="txt-toneA">[<?=$arrDeliveryType[$val['delivery_type']]?>]</strong>
								<?}?>
							</td>
							<td class="txt-toneA fz-13"><?=$step_type?></td>
						</tr>
					</tbody>
<?}?>
<?
$total_price = $vender_price + $vender_deli_price;
?>
<script language='javascript'>
	var _buy_amt ='<?=$total_price?>';  // 총 구매액 
</script>
					<tfoot>
						<tr>
							<td colspan="6" class="reset">
								<div class="cart-total-price clear">
									<dl>
										<dt>상품합계</dt>
										<dd>\ <?=number_format( $vender_price )?></dd>
									</dl>
									<!--
									<span class="txt point-color">-</span>
									<dl class="point-color">
										<dt>할인</dt>
										<dd>\ 0</dd>
									</dl>-->
									<span class="txt">+</span>
									<dl>
										<dt>배송비</dt>
										<dd>\ <?=number_format( $vender_deli_price )?></dd>
									</dl>
									<dl class="sum">
										<dt>합계</dt>
										<dd class="fz-20 point-color fw-normal">\ <?=number_format( $vender_price + $vender_deli_price )?></dd>
									</dl>
								</div>
							</td>
						</tr>
					</tfoot>

				</table>
			</section><!-- //브랜드 주문상품 -->
<?php
} // vender foreach
$script_price = $_ord->price + $_ord->deli_price - $_ord->dc_price - $_ord->reserve - $_ord->point;
if( strlen( $_ShopInfo->getMemid() ) == 0 ){ // 로그인을 안했을 경우
	$in_reserve	= 0;
}
?>
			<div class="orderEnd-info clear mt-60">
				<section class="inner-payment">
					<header class="cart-section-title">
						<h3>할인 및 결제정보</h3>
					</header>
					<table class="th-left">
						<caption>할인 및 결제 확인</caption>
						<colgroup>
							<col style="width:168px">
							<col style="width:auto">
						</colgroup>
						<tbody>
							<tr>
								<th scope="row"><label>총 상품금액</label></th>
								<td>\ <?=number_format( $_ord->price )?></td>
							</tr>
							<tr>
								<th scope="row"><label>포인트 사용</label></th>
								<td class="point-color">- <?=number_format( $_ord->reserve )?> P</td>
							</tr>
							<tr>
								<th scope="row"><label>E포인트 사용</label></th>
								<td class="point-color">- <?=number_format( $_ord->point )?> P</td>
							</tr>
							<tr>
								<th scope="row"><label>쿠폰할인</label></th>
								<td class="point-color">- \ <?=number_format( $_ord->dc_price )?></td>
							</tr>
							<tr>
								<th scope="row"><label>배송비</label></th>
								<td>\ <?=number_format ( $_ord->deli_price )?></td>
							</tr>
							<tr>
								<th scope="row"><label>실 결제금액</label></th>
								<td class="fz-14 fw-bold point-color">\ <?=number_format( $_ord->price + $_ord->deli_price - $_ord->dc_price - $_ord->reserve - $_ord->point )?></td>
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
								<th scope="row"><label>결제방법</label></th>
								<td class="fz-13"><?=$pay_title?></td>
							</tr>
						</tbody>
					</table>
				</section><!-- //.inner-payment -->
				<section class="inner-delivery">
					<header class="cart-section-title">
						<h3>배송지 정보</h3>
					</header>
					<table class="th-left">
						<caption>배송지 정보 확인</caption>
						<colgroup>
							<col style="width:168px">
							<col style="width:auto">
						</colgroup>
						<tbody>
							<tr>
								<th scope="row"><label>받는사람</label></th>
								<td><?=$_ord->receiver_name?></td>
							</tr>
							<tr>
								<th scope="row"><label>휴대전화</label></th>
								<td><?=$_ord->receiver_tel2?></td>
							</tr>
							<tr>
								<th scope="row"><label>전화번호(선택)</label></th>
								<td><?=$_ord->receiver_tel1?></td>
							</tr>
							<tr>
								<th scope="row"><label>주소</label></th>
								<td>
									<ul class="input-multi">
										<?=$_ord_receiver_addr?>
									</ul>
								</td>
							</tr>
							<tr>
								<th scope="row"><label>배송 요청사항</label></th>
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
				</section><!-- //.inner-delivery -->
			</div><!-- //.orderEnd-info -->
			<div class="btnPlace mt-40">
				<a class="btn-line h-large CLS_OrderView" href="javascript:;" style="width:220px">주문내역 확인하기</a>
				<a class="btn-point h-large"  href="/" style="width:220px">쇼핑 계속하기</a>
			</div>

		</article><!-- //.cart-order-wrap -->

	</div>
</div><!-- //#contents -->


<form name='mypageOrderViewFrm' method='POST' action='<?=$Dir.FrontDir?>mypage_orderlist_view.php'>
	<input type='hidden' name='ordercode' value = '<?=$ordercode?>'>
</form>

<?
	$strCriteo = '';
	if(count($arrCriteo)>0){
		$arrCriteoReSettings = array();
		foreach($arrCriteo as $dc){
			$arrCriteoReSettings[] = '{ i: "'.$dc['code'].'", t: "'.$dc['name'].'", p: "'.$dc['price'].'", q: "'.$dc['ea'].'" }';
		}
		$strCriteo = implode(", ", $arrCriteoReSettings);
?>

<?
	}
?>
