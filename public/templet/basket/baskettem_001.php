<?

//현재페이지안씀



?>


<script type="text/javascript" src="json_adapter.js"></script>
<script type="text/javascript">
var db = new JsonAdapter();
var req = JSON.parse('<?=json_encode($_REQUEST)?>');
var ses = JSON.parse('<?=json_encode($_SESSION)?>');
var pArr 	= new Array(); //상품배열

//옵션변경취소
function cancelOptionChange(basketidx){
	$('#'+basketidx).removeClass('active');
	
}

//옵션변경
function optionChange(basketidx){
	
	var chkval = $('[name="cartOptSize_'+basketidx+'"]:checked').val();
	var tempkey = '<?=$_ShopInfo->getTempkey()?>';
	
	//ajax 처리
	var sp_param = chkval+'|'+basketidx;
	var data = db.setDBFunc({sp_name: 'basket_option_change', sp_param : sp_param});
	if(data.code){
		alert('정상적으로 변경되었습니다.');
		location.reload();
	}else{
		alert('수정시 오류가 발생했습니다!');
	}
	
}

//수량변경
function quantityChange(basketidx){
	
	var chkval = $('#quantity_'+basketidx).val();
	//ajax 처리
	var sp_param = chkval+'|'+basketidx;
	var data = db.setDBFunc({sp_name: 'basket_quantity_change', sp_param : sp_param});
	if(data.code){
		alert('정상적으로 변경되었습니다.');
		location.reload();
	}else{
		alert('수정시 오류가 발생했습니다!');
	}
}

/* 수량조절재고체크 */
function setQntPlus(basketidx){
	

	
	var qnty = Number($('#quantity_'+basketidx).val());
	qnty += 1;
	var qmax = $('#quantity_max_'+basketidx).val();
	if(!qmax) qmax= 100;
	
	if(qnty > qmax){
		alert('구매가능한 재고는 '+qmax+'개 입니다.');
		return false;
	}else{
		$('#quantity_'+basketidx).val(qnty);
		//var sum_price = Number($("#sellprice").val()*qnty);
		//$("#sellprice_txt").text("\\"+comma(sum_price));
	}

	
}

function setQntMinus(basketidx){
	
	var qnty = Number($('#quantity_'+basketidx).val());
	qnty -= 1;
	
	if(qnty > 0){
		$('#quantity_'+basketidx).val(qnty);
		//var sum_price = Number($("#sellprice").val()*qnty);
		//$("#sellprice_txt").text("\\"+comma(sum_price));
	}
	
	
}

function delBasket(delidx){

	var sp_param =''; 
	if(delidx=='choise' || delidx=='all'){
		
		if(delidx=='all') $('input[name=checkBasket]').prop("checked", true);
		
		$("input[name=checkBasket]:checked").each(function() {
			sp_param += "'"+$(this).val()+"',";
		});
		sp_param = sp_param.substring( 0, sp_param.length-1 );
		
	}else{
		sp_param = "'"+delidx+"'"; 
	}
	
	if(confirm('장바구니에서 삭제하시겠습니까?')){
		
		//$('#itemPut01'+basketidx).prop("checked", true);
		//return false;
		
		var data = db.setDBFunc({sp_name: 'delete_basket', sp_param : sp_param});
		if(data.code){
			alert('정상적으로 삭제되었습니다.');
			location.reload();
		}else{
			alert('수정시 오류가 발생했습니다!');
		}
	}
}

</script>


<div id="contents">
	<div class="cartOrder-page">

		<article class="cart-order-wrap">
			<header class="progess-title">
				<h2>주문/결제</h2>
				<ul class="flow clear">
					<li class="active"><div><i></i><span>STEP 1</span>장바구니</div></li>
					<li><div><i></i><span>STEP 2</span>주문하기</div></li>
					<li><div><i></i><span>STEP 3</span>주문완료</div></li>
				</ul>
			</header>
			
<?php
$all_reserve = 0;
$bf_sumprice = 0;
$staff_sumprice = 0;
$all_deli_price = 0;
$all_deli_price_last = 0;
$deli_basefee_last =0;

foreach( $brandArr as $brand=>$brandObj ){

	$brand_name = get_brand_name( $brand );
	$vender	=$brandVenderArr[$brand];
	$brandArr->delivery_type
	
?>	
			<section class="mt-70">
				<header class="cart-section-title">
					<h3><?
							if($brand=="0") echo "택배배송 상품";
							if($brand=="1") echo "매장픽업 상품";
							if($brand=="2") echo "당일수령 상품";

						?> </h3>
					<p class="att">*본사물류 또는 해당 브랜드 매장에서 택배로 고객님께 상품이 배송됩니다. (주문 완료 후, 3~5일 이내 수령)</p>
				</header>
				<table class="th-top">
					<caption>장바구니 담긴 품목</caption>
					<colgroup>
						<col style="width:54px">
						<col style="width:auto">
						<col style="width:170px">
						<col style="width:90px">
						<col style="width:130px">
						<col style="width:130px">
						<col style="width:116px">
						<col style="width:20px">
					</colgroup>
					<thead>
						<tr>
							<th scope="col"><div class="checkbox"><input type="checkbox" id="adf" class="allCheck"><label for="adf"></label></div></th>
							<th scope="col">상품정보</th>
							<th scope="col">수량</th>
							<th scope="col">적립</th>
							<th scope="col">판매가</th>
							<th scope="col">배송정보</th>
							<th scope="col">선택</th>
							<th scope="col" class="fz-0">삭제</th>
						</tr>
					</thead>
					
					
					<tbody data-ui="TabMenu">


<?php
	$product_price = 0;
	$bf_sumprice = 0;
	$deli_basefee = 0;
	
	foreach( $brandObj as $product ) {
		$sizeString = "";
		if($product['delivery_type'] == '0') {	//2016-10-07 libe90 매장발송일 경우 재고 가장 많은 매장으로 매장정보 표시
			$shop_code_set = getErpProdShopStock_Type($product['prodcode'], $product['colorcode'], $product['opt2_name'], 'delivery');
			$product['store_code'] = $shop_code_set['shopcd'];
			
		}
		$storeData = getStoreData($product['store_code']);
		$all_reserve += $product['reserve'];
		$product_price = ( $product['price'] + $product['option_price'] ) * $product['option_quantity'];
		$bf_sumprice += $product_price;
		
		if($product['delivery_type'] == '0') {
			if($bf_sumprice>=50000){
				$deli_basefee = 0;
			}else{
				$deli_basefee = $deli_basefee_origin;
			}
		}
		$deli_basefee_last += $deli_basefee;
		
		$bf_sumprice_last = $bf_sumprice + $deli_basefee;
		$all_deli_price_last +=$bf_sumprice_last;  
		
		//$staff_product_price = ( $product['staff_price'] + $product['staff_option_price'] ) * $product['option_quantity'];
		//$staff_sumprice += $staff_product_price;

        $hsql = "Select count(*) From tblhott_like Where like_id = '".$_ShopInfo->getMemid()."' and section = 'product' and hott_code = '".$product['productcode']."'";
        list($hcnt) = pmysql_fetch($hsql, get_db_conn());

        if($hcnt) {
            $like_type = "unlike";
            $like_class = "user_like";
        }else {
            $like_type = "like";
            $like_class = "user_like_none";
        }
?>								
						<tr>
							<td><div class="checkbox"><input type="checkbox" name='checkBasket' id='itemPut01<?=$product['basketidx']?>' value='<?=$product['basketidx']?>' data-delivery_type = "<?=$product['delivery_type']?>"><label for="itemPut01<?=$product['basketidx']?>"></label></div></td>
							<td>
								<div class="goods-in-td">
									<div class="thumb-img"><a href="<?=$Dir.FrontDir?>productdetail.php?productcode=<?=$product['productcode']?>">
											<img src="<?=getProductImage($productImgPath,$product['tinyimage'])?>" alt="<?=$product['productname']?>"></a></div>
									<div class="info">
										<p class="brand-nm"><?=$brand_name?></p>
										<p class="goods-nm"><?=$product['productname']?> (<?=$product['prodcode']?>)</p>
										<?if($storeData['name'] && $product['delivery_type'] != '2'){	//2016-10-07 libe90 매장발송 정보표시?>
											<li style = 'color:blue;'>[<?=$arrDeliveryType[$product['delivery_type']]?>] <?=$storeData['name']?></li>
											<?if($product['delivery_type'] == '1'){?>
												<li style = 'color:blue;'>예약일 : <?=$product['reservation_date']?></li>
											<?}?>
										<?}else if($product['delivery_type'] == '2'){?>
											<li style = 'color:blue;'>[<?=$arrDeliveryType['2']?>] <?=$storeData['name']?></li>
											<li style = 'color:blue;'>주소 : [<?=$product['post_code']?>] <?=$product['address1']?> <?=$product['address2']?></li>
										<?}?>
										<p class="opt">색상 : <?=$product['color_code']?>  /  
											
											
										
										<?php
												if( strlen( $product['opt1_name'] ) > 0 ){ // 옵션
									
													if( $product['option_type'] == 0 ){ //조합형 옵션
														$tmpOptName = explode( '@#', $product['opt1_name'] );
														$tmpOptVal = explode( chr(30), $product['opt2_name'] );
														$tmpOptCnt	= 0;
														foreach( $tmpOptName as $tmpKey=>$tmpVal ){
															if( $tmpVal ){
																if ($tmpOptCnt > 0) echo '/&nbsp;';
																echo $tmpVal.':'.$tmpOptVal[$tmpKey].'&nbsp;';
																$optSize = $tmpOptVal[$tmpKey];
																$sizeString = $tmpOptVal[$tmpKey];
																$tmpOptCnt++;
															}
														}
													}
									
													if( $product['option_type'] == 1 ){ // 독립형 옵션
														$tmpOptName = explode( '@#', $product['opt1_name'] );
														$tmpOptVal = explode( '@#', $product['opt2_name'] );
														$tmpOptCnt	= 0;
														foreach( $tmpOptName as $tmpKey=>$tmpVal ){
															if( $tmpVal ){
																$tmpOptVal1	=	explode( chr(30), $tmpOptVal[$tmpKey]);
																if ($tmpOptCnt > 0) echo '/&nbsp;';
																echo $tmpVal.':'.$tmpOptVal1[1].'&nbsp;';
																$sizeString = $tmpOptVal1[1];
															}
														}
													}
									
												}
									
												if( strlen( $product['text_opt_subject'] ) > 0 ) { // 추가 문구 옵션
													$tmpOptSubject = explode( '@#', $product['text_opt_subject'] );
													$tmpOptContent = explode( '@#', $product['text_opt_content'] );
													foreach( $tmpOptSubject as $tmpKey=>$tmpVal ){
														if( $tmpVal ){
															echo '/&nbsp;'.$tmpVal.':'.$tmpOptContent[$tmpKey].'&nbsp;';
														}
													}
												}
									
												if( strlen( $product['opt1_name'] ) > 0 && $product['option_price'] > 0 ){
													echo '&nbsp;( + '.number_format( $product['option_price'] ).' 원)';
												}
									
									?>
											
											
											
											
										</p>
										<button class="btn-line h-small" type="button" data-content="menu"><span>옵션변경</span></button>
									</div>
								</div>
							</td>
							<td class="change-quantity">
								<div class="quantity">
									<input type="text" value="<?=number_format($product['quantity'])?>" id="quantity_<?=$product['basketidx']?>" name="" readonly>
									<button class="plus" onclick="setQntPlus('<?=$product['basketidx']?>');"></button>
									<button class="minus" onclick="setQntMinus('<?=$product['basketidx']?>');"></button>
								</div>
								<input type="hidden" id="quantity_max_<?=$product['basketidx']?>" value="<?=$product['option_quantity_max']?>">
								
								<div class="btn"><button type="button" class="btn-line h-small" onclick="quantityChange('<?=$product['basketidx']?>');"><span>변경</span></button></div>
							</td>
							<td class="txt-toneB">10%</td>
							<td class="txt-toneA">\ <?=number_format( $product_price )?></td>
							
							<td class="flexible-delivery">
								<? if($brand=="0"){ ?>
								<strong class="txt-toneA">\ <?=number_format($deli_basefee)?></strong><div class="pt-5"><?=number_format($deli_miniprice)?>원 이상<br>무료배송</div>
								<? }else if($brand=="1"){ ?>
								<strong class="txt-toneA">[매장픽업]<br></strong><div class="pt-5">BESTI BELLI 강남역점</div>
								<? }else if($brand=="2"){ ?>
								<strong class="txt-toneA">[매장픽업]<br>\3,000</strong><div class="pt-5">BESTI BELLI 강남역점</div>
								<?}?>
							</td>
							
							
							<td>
								<div class="td-btnGroup">
									<button class="btn-basic h-small"><span>좋아요</span></button>
									<? if(!$brand=="0"){ ?>
									<button class="btn-point h-small"><span>택배수령전환</span></button>
									<?}?>
								</div>
							</td>
							<td class="va-t ta-l"><button class="item-del" onclick="delBasket('<?=$product['basketidx']?>');"><span>장바구니에서 삭제</span></button></td>
						</tr>
						<tr data-content="content" id="option_<?=$product['basketidx']?>_tr">
							<td class="reset" colspan="8">
								<div class="opt-change">
									<h4>상품옵션 변경</h4>
									<div>
										<?php
		
										//explode( '@#', $product['opt1_name'] );
										//explode( '@#', $product['text_opt_subject'] );
										$display_cnt = 0;
										if( strlen( $product['opt1_name'] ) > 0 ){
											$display_cnt += count( explode( '@#', $product['opt1_name'] ) );
										}
										if( strlen( $product['text_opt_subject'] ) > 0 ){
											$display_cnt += count( explode( '@#', $product['text_opt_subject'] ) );
										}
										if( strlen( $product['opt1_name'] ) > 0 && $display_cnt < 3 ){
											if( $product['option_type'] == 0 ){ //조합형 옵션
												$tmpOptName = explode( '@#', $product['opt1_name'] );
												$tmpOptVal = explode( chr(30), $product['opt2_name'] );
												$option_depth = count( $tmpOptName );
												foreach( $tmpOptName as $optNameKey=>$optNameVal ){
													$tmpOptCode = ''; //자신의 옵션값
													$get_option = ''; // 옵션정보
													$optCode = ''; // 부모 옵션값
													for( $code_i = 0; $code_i < $optNameKey + 1; $code_i++ ){
								
														if( $code_i == 0 ){
															$tmpOptCode .= $tmpOptVal[$code_i];
														} else {
															$tmpOptCode .= chr(30).$tmpOptVal[$code_i];
														}
													}
													
													$optCode = substr( $tmpOptCode, 0, strrpos( $tmpOptCode, chr(30) ) ); // 해당 옵션 부모값
													$get_option = get_option( $product['productcode'], $optCode, $optNameKey ); //옵션정보
													
													//echo $tmpOptCode;
												}
												
											}
										}?>
												<dl class="d-iblock">
															<dt>사이즈</dt>
															<dd>
																<div class="opt-size">
												<?php
												if( count( $get_option ) > 0 ){
														foreach( $get_option as $optVal ){
															$option_qty = $optVal['qty']; // 수량
															$option_disable = ''; // disabled
															$option_text = ''; // 품절 text
															$option_hover = ''; // 선택값 li class
															$priceText = '';
							
															if( ( $optNameKey + 1 == $option_depth ) && $optVal['price'] > 0 ){
																$priceText = ' ( + '.number_format($optVal['price']).' 원 )';
															} else if( ( $optNameKey + 1 == $option_depth ) && $optVal['price'] < 0 ) {
																$priceText = ' ( - '.number_format($optVal['price']).' 원 )';
															}
															if( 
																( $option_qty !== null && $option_qty <= 0 ) && 
																( ( $option_depth > 0 && $nameKey != 0 ) || ( $option_depth == 1 && $optNameKey == 0 ) ) &&
																$product['quantity'] < 999999999
															){
																$option_disable = 'li-disable';
																$option_text = '[품절]&nbsp;';
															}
															if( strlen( $optCode ) > 0 ) {
																if( $tmpOptCode == $optCode.chr(30).$optVal["code"] ) $option_hover = 'class="hover"';
															} else {
																if( $tmpOptCode == $optVal["code"] ) $option_hover = 'class="hover"';
															}
							
															if( $optNameKey > 0 ){
																$data_code = $optCode.chr(30).$optVal["code"];
															} else {
																$data_code = $optVal["code"];
															}
										
														
															if($option_text.$optVal["code"].$priceText== $optSize){
																$checkedxt ="checked";
															}else{
																$checkedxt ="";
															}
												
												?>
												
												
															<div><input type="radio" name="cartOptSize_<?=$product['basketidx']?>" id="cartOptSize_<?=$product['basketidx']?>_<?=$option_text.$optVal["code"].$priceText?>" value="<?=$option_text.$optVal["code"].$priceText?>" <?=$checkedxt?>>
																<label for="cartOptSize_<?=$product['basketidx']?>_<?=$option_text.$optVal["code"].$priceText?>">
																	<?=$option_text.$optVal["code"].$priceText?>
																</label>
															</div>
												<?
														} // get_option foreach
												} // option1 if
												?>					
														</div>
													</dd>
												</dl>
												
												<!--<li <?=$option_hover?>>
												
														<a href="javascript:;" <?=$option_disable?> 
															data-qty='<?=$option_qty?>' data-code='<?=$data_code?>' >
															<?=$option_text.$optVal["code"].$priceText?>
														</a>
												</li>-->
												
									</div>
									<!--
									<dl class="mt-15">
										<dt><label for="changeOpt_name2">옵션명</label></dt>
										<dd>
											<div class="select">
												<select id="changeOpt_name2">
													<option value="">선택</option>
												</select>
											</div>
										</dd>
									</dl>-->
									<div class="btn">
										<button class="btn-basic h-small" type="button" onclick="optionChange('<?=$product['basketidx']?>')"><span>옵션변경</span></button>
										<button class="btn-line h-small" type="button" onclick="cancelOptionChange('option_<?=$product['basketidx']?>_tr')"><span>변경취소</span></button>
									</div>
									<button class="item-del"><span>닫기</span></button>
								</div>
							</td>
						</tr>
			
	<?}?>						
						
						
					</tbody>
					<tfoot>
						<tr>
							<td colspan="8" class="reset">
								<div class="cart-total-price clear">
									<?if($product['delivery_type'] == '0') {?>
									<dl>
										<dt>상품합계</dt>
										<dd>\ <?=number_format($bf_sumprice)?></dd>
									</dl>
									<span class="txt">+</span>
									<dl>
										<dt>배송비</dt>
										<dd>\ <?=number_format($deli_basefee)?></dd>
									</dl>
									<?}?>
									<dl class="sum">
										<dt>합계</dt>
										<dd>\ <?=number_format($bf_sumprice_last)?></dd>
									</dl>
								</div>
							</td>
						</tr>
					</tfoot>
				</table>
			</section><!-- //.cart-section-title -->
<?}?>	
			
			
			<div class="cart-clear">
				<button class="btn-line w100" onclick="javascript:delBasket('choise');"><span>선택상품 삭제</span></button>
				<button class="btn-line w100" onclick="javascript:delBasket('all');"><span>전체삭제</span></button>
			</div>

			<section class="cart-total-price alone mt-40 clear">
				<h4>총 구입금액</h4>
				<dl>
					<dt>상품합계 총액</dt>
					<dd>\ <?=number_format($all_deli_price_last)?></dd>
				</dl>
				<span class="txt">+</span>
				
				<dl>
					<dt>배송비</dt>
					<dd>\ <?=number_format($deli_basefee_last)?></dd>
				</dl>
				<dl class="sum">
					<dt>총 주문금액</dt>
					<dd class="point-color fz-18">\ <?=number_format($all_deli_price_last + $deli_basefee_last)?></dd>
				</dl>
			</section><!-- //.cart-total-price -->
			<div class="btnPlace mt-45">
				<a href="#" class="btn-line h-large w200">쇼핑 계속하기</a>
				<a href="#" class="btn-line h-large w200">선택 상품 구매</a>
				<a href="#" class="btn-point h-large w200">전체 상품 구매</a>
			</div>
		</article><!-- //.cart-order-wrap -->

	</div>
</div><!-- //#contents -->


