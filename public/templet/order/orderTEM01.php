
<div id="contents">
	<div class="cartOrder-page">

		<article class="cart-order-wrap">
			<header class="progess-title">
				<h2>주문/결제</h2>
				<ul class="flow clear">
					<li><div><i></i><span>STEP 1</span>장바구니</div></li>
					<li class="active"><div><i></i><span>STEP 2</span>주문하기</div></li>
					<li><div><i></i><span>STEP 3</span>주문완료</div></li>
				</ul>
			</header>
			<div class="dimm-loading" id="dimm-loading" style="display: none;">
				<div id="loading"></div>
			</div>
<!-- 브랜드별 -->
<?php
$sumprice = 0;
$deli_price = 0; // 선불 배송료
$deli_price2 = 0; //착불 배송료
$sum_product_reserve	= 0; // 총 예상 적립금
$checkTodayDelivery = false; // 당일 배송이 있는지 여부
$checkMarketDelivery = false; // 매장픽업이 있는지 여부
$arrDeliveryTodayAddress = array(); // 당일 배송이 있으면 해당 주소 저장 배열
$vender	=0; //밴더 기본배송비로 고정2017-02-27
$o2o_check=0;
foreach( $brandArr as $brand=>$brandObj ){
	foreach($brandObj as $bo=>$bos){
		if($bos[delivery_type]=='0') $o2o_check++;
	}
	$brand_name = get_brand_name( $brand );
	
	//$vender	=$brandVenderArr[$brand];
	$vender_price = 0;
	$product_reserve = 0;
	$product_price = 0;
	if($o2o_check){
?>
			<section class="mt-70">
				<header class="cart-section-title">
					<h3><?=$brand_name?> 주문상품</h3>
					<p class="att">*본사물류 또는 해당 브랜드 매장에서 택배로 고객님께 상품이 배송됩니다. (주문 완료 후, 3~5일 이내 수령)</p>
				</header>
				<table class="th-top">
					<caption><?=$brand_name?> 주문 상품</caption>
					<colgroup>
						<col style="width:auto">
						<col style="width:90px">
						<col style="width:90px">
						<col style="width:130px">
						<?php
//							if(strlen($_ShopInfo->getMemid())>0 && $_data->coupon_ok=="Y" && $staff_order == 'N' && $cooper_order == 'N' ) {
							if(strlen($_ShopInfo->getMemid())>0 && $_data->coupon_ok=="Y" && $staff_order == 'N') {
						?>
						<col style="width:170px">
						<?	}?>
						<col style="width:136px">
					</colgroup>
					<thead>
						<tr>
							<th scope="col">상품정보</th>
							<th scope="col">수량</th>
							<th scope="col">적립</th>
							<th scope="col">판매가</th>
							<?php
//								if(strlen($_ShopInfo->getMemid())>0 && $_data->coupon_ok=="Y" && $staff_order == 'N' && $cooper_order == 'N' ) {
								if(strlen($_ShopInfo->getMemid())>0 && $_data->coupon_ok=="Y" && $staff_order == 'N') {
							?>
							<th scope="col">쿠폰선택</th>
							<?	}?>
							<th scope="col">배송정보</th>
						</tr>
					</thead>
					
					<tbody>
<?php
	$product_count="0";
	foreach( $brandObj as $product ) {
		if($product['delivery_type'] == '0') {	//2016-10-07 libe90 매장발송일 경우 재고 가장 많은 매장으로 매장정보 표시
			$shop_code_set = getErpProdShopStock_Type($product['prodcode'], $product['colorcode'], $product['option'][0]['option_code'], 'delivery');
			$product['store_code'] = $shop_code_set['shopcd'];
		}else{
			continue;
		}
		$brand_product_name = get_brand_name( $product[brand] );
//		$storeData = getStoreData($product['store_code']);
		$opt_price = 0; // 상품별 옶션가
		$pr_reserve = 0; //상품별 마일리지
        $tmp_opt_price = 0;
		if($product['delivery_type'] == '3'){
			$checkTodayDelivery = true;
			$arrDeliveryTodayAddress = array('post'=>$product['post_code'], 'address1'=>$product['address1'], 'address2'=>$product['address2']);
		}
		if($product['delivery_type'] == '1'){
			$checkMarketDelivery = true;
		}


		$vender_deli_price = 0;

		if( $product_deli[$vender][$product['productcode']] ){
			$vender_deli_price += $product_deli[$vender][$product['productcode']]['deli_price'];
			/*
			foreach( $product_deli[$vender][$product['productcode']] as $prDeliKey => $prDeliVal ){
				exdebug($prDeliKey);
				$vender_deli_price += $prDeliVal['deli_price'];
			}*/
		}
		
		$vender_deli_price += $vender_deli[$vender]['deli_price'];
?>
						<input type="hidden" name="obj_basketidx" value="<?=$product[basketidx]?>">
						<input type="hidden" name="obj_ci_no[<?=$product[basketidx]?>]" value="">
						<input type="hidden" name="obj_coupon_code[<?=$product[basketidx]?>]" value="">
						<input type="hidden" name="obj_dc[<?=$product[basketidx]?>]" value="">
						<input type="hidden" name="obj_product_price[<?=$product[basketidx]?>]" value="<?=$product['price']?>">

						<tr>
							<td class="pl-25">
								<div class="goods-in-td product_idx">
									<div class="thumb-img"><a href="<?=$Dir.FrontDir?>productdetail.php?productcode=<?=$product['productcode']?>" target="_blink"><img src="<?=getProductImage( $productImgPath, $product['tinyimage'] )?>"></a></div>
									<div class="info">
										<p class="brand-nm"><?=$brand_product_name?></p>
										<p class="goods-nm"><?=$product['productname']?></p>
										<p class="opt">
										<?php
											if($product[prodcode]){
												echo "품번 : ".$product[prodcode];
											}
											if($product[colorcode]){
												echo " / ";
												echo "색상 : ".$product[colorcode];
											}
											if( count( $product['option'] ) > 0 || strlen( $product['text_opt_subject'] ) > 0 ){
												if( count( $product['option'] ) > 0 ){
													$tmp_opt_subject = explode( '@#', $product['option_subject'] );
													if( $product['option_type'] == 0 ){ // 조합형 옵션
														$tmp_option = $product['option'][0];
														$tmp_opt_contetnt = explode( chr(30), $tmp_option['option_code'] );
														if($tmp_opt_subject){
															echo " / ";
															foreach( $tmp_opt_subject as $optKey=>$optVal ){
																echo $optVal.' : '.$tmp_opt_contetnt[$optKey];
																$tmp_opt_price += $optVal['option_price'] * $product['option_quantity'];
															}// option foreach
														}
														$opt_price += $tmp_option['option_price'] * $product['option_quantity'];
													}
													if( $product['option_type'] == 1 ){ // 독립형 옵션
														
														if($product['option']){
															echo " / ";
															foreach( $product['option'] as $optKey=>$optVal ){
																$tmp_opt_content = explode( chr(30), $optVal['option_code'] );
																echo $tmp_opt_subject[$optKey].' : '.$tmp_opt_content[1];
																$tmp_opt_price += $optVal['option_price'] * $product['option_quantity'];
																$opt_price += $optVal['option_price'] * $product['option_quantity'];
															}// option foreach
														}
													}
												} // count option

												if( $product['text_opt_content'] ){ // 추가문구 옵션
													$tmp_text_subejct = explode( '@#', $product['text_opt_subject'] );
													$text_opt_content = explode( '@#', $product['text_opt_content'] );
													if($text_opt_content){
														echo " / ";
														foreach( $text_opt_content as $textKey=>$textVal ){
															if( $textVal != '' ) {
																echo $tmp_text_subejct[$textKey].' : '.$textVal;
															}
														}
													}
												}  // text_opt_content if
												if( $tmp_opt_price > 0 ){
													echo '(추가금액 : '.number_format( $tmp_opt_price ).')';
												}
											} else {
												echo "-";
											}// count option || text_opt_subject if

											//$pr_reserve = getReserveConversion( $product['reserve'], $product['reservetype'], ( $product['price'] * $product['quantity'] ) + $opt_price , "N" );
											$pr_reserve = getReserveConversion( $product['reserve'], "Y", ( $product['price'] * $product['quantity'] ) + $opt_price , "N" );
											if( strlen( $_ShopInfo->getMemid() ) == 0 ) $pr_reserve	= 0;
											$product_reserve += $pr_reserve; // 벤더별 상품 예상 적립금
											if($cooper_order == 'Y'){
												// order.class.php 옴겨야함 20170828 제휴사 수정
												list($sale_num) = pmysql_fetch("select b.group_productcode from tblmember a left join tblcompanygroup b on a.company_code=b.group_code where a.cooper_yn='Y' and a.id= '".$_ShopInfo->getMemid()."' ");
												$c_productcode = $product['productcode'];

												list($company_price) = pmysql_fetch("select ".$sale_num." from tblproduct where productcode= '".$c_productcode."' ");
												
												$t_product_price = ( $product['price']  * $product['quantity'] ) + $opt_price; //옵션가와 상품가를 합산해준다
												$c_product_price = ( $company_price  * $product['quantity'] ) ;

												if($c_product_price >= $t_product_price || $c_product_price == 0){
													$product_price = $t_product_price;
												}else{
													$product_price = $c_product_price;
												}
											}else{
												$product_price = ( $product['price']  * $product['quantity'] ) + $opt_price; //옵션가와 상품가를 합산해준다
											}
											$vender_price += $product_price; // 벤더별 상품가격

											$sum_product_reserve += $pr_reserve; // 총 예상 적립금
										?>
										<!--색상 : NAM  / 사이즈 55-->
										</p>
									</div>
								</div>
							</td>
							<td><?=$product['quantity']?></td>
							<td class="txt-toneB"><?=number_format($pr_reserve)?>P</td>
							<td class="txt-toneA">\ <?=number_format($product_price)?></td>
							<?php
//								if(strlen($_ShopInfo->getMemid())>0 && $_data->coupon_ok=="Y" && $staff_order == 'N' && $cooper_order == 'N' ) {
								if(strlen($_ShopInfo->getMemid())>0 && $_data->coupon_ok=="Y" && $staff_order == 'N' ) {
							?>
<?if($cooper_order == 'Y'  ) {?>
							<td><button class="btn-basic h-small w70 btn-couponList" type="button" onclick="javascript:product_ccoupon_pop(<?=$product[basketidx]?>,<?=$product_price?>)"><span>쿠폰사용</span></button></td>
<?}else{?>
							<td><button class="btn-basic h-small w70 btn-couponList" type="button" onclick="javascript:product_coupon_pop(<?=$product[basketidx]?>)"><span>쿠폰사용</span></button></td>
<?}?>
							<?	}?>
							<!--<td class="flexible-delivery"><strong class="txt-toneA">\3,000</strong><div class="pt-5">50,000원 이상<br>무료배송</div></td>-->
							<?if(!$product_count){?>
							<td class="flexible-delivery" rowspan="<?=count($brandObj)?>"><div class="pt-5">
								<?if($vender_info[$vender]['deli_price_min']){?><?=number_format($vender_info[$vender]['deli_price_min'])?>원 이상<br><?}?>무료배송
							</div></td>
							<?}?>
						</tr>
<!-- //상품단위 종료 -->
<?php
		# 장바구니 쿠폰 제외
		foreach( $basket_coupon as $basketKey=>$basketVal ){
			if( !$_CouponInfo->check_coupon_product( $product['productcode'], 2, $basketVal ) ){
				unset( $basket_coupon[$basketKey] );
			}
		}
		$product_count++;
	} //foreach
?>
					</tbody>
					
					<tfoot>
<?php
	
	if( $vender_info[$vender] ){
		
?>
						<input type='hidden' name='select_price[<?=$vender?>]' value='<?=$vender_deli_price?>' data-vender='<?=$vender?>' >
						<tr>
							<td colspan="8" class="reset">
								<div class="cart-total-price clear">
									<dl>
										<dt>상품합계</dt>
										<dd>\ <?=number_format( $vender_price )?></dd>
									</dl>
<!--
									<div <?if(strlen($_ShopInfo->getMemid())>0 && $_data->coupon_ok=="Y" && $staff_order == 'N' && $cooper_order == 'N' ) {}else {?> class='hide'<?}?>>
-->
									<div <?if(strlen($_ShopInfo->getMemid())>0 && $_data->coupon_ok=="Y" && $staff_order == 'N' ) {}else {?> class='hide'<?}?>>
									<span class="txt point-color">-</span>
									<dl class="point-color">
										<dt>쿠폰할인</dt>
										<dd>\ <em class="CLS_prCoupon" style="vertical-align: baseline;">0</em></dd>
									</dl>
									</div>
									<span class="txt">+</span>
									<dl>
										<dt>배송비</dt>
										<dd>\ <?=number_format( $vender_deli_price )?></dd>
									</dl>
									<dl class="sum">
										<dt>합계</dt>
										<dd>\ <em class="CLS_Tprice" style="vertical-align: baseline;"><?=number_format( $vender_price + $vender_deli_price )?></em></dd>
									</dl>
								</div>
							</td>
						</tr>
<?php
	}
?>
					</tfoot>
					
				</table>
			</section><!-- //브랜드 주문상품 -->
					<!-- // 상품 리스트 -->
<?php
	if( $vender_info[$vender]['deli_select'] == '0' || $vender_info[$vender]['deli_select'] == '2' ) $deli_price += $vender_deli_price;
    if( $vender_info[$vender]['deli_select'] == '1' ) $deli_price2 += $vender_deli_price;
	$sumprice += $vender_price;
	}
} // foreach

$o2o_check=0;
foreach( $brandArr as $brand=>$brandObj ){
	foreach($brandObj as $bo=>$bos){
		if($bos[delivery_type]!='0') $o2o_check++;
	}
	$brand_name = get_brand_name( $brand );
	//$vender	=$brandVenderArr[$brand];
	$vender_price = 0;
	$product_reserve = 0;
	$product_price = 0;

	if($o2o_check){
?>
			<section class="mt-70">
				<header class="cart-section-title">
					<h3>O2O 주문상품</h3>
					<p class="att">*본사물류 또는 해당 브랜드 매장 중 가까운 매장에서 픽업할 수 있는 O2O서비스 입니다.</p>
				</header>
				<table class="th-top">
					<caption><?=$brand_name?> 주문 상품</caption>
					<colgroup>
						<col style="width:auto">
						<col style="width:90px">
						<col style="width:90px">
						<col style="width:130px">
						<?php
//							if(strlen($_ShopInfo->getMemid())>0 && $_data->coupon_ok=="Y" && $staff_order == 'N' && $cooper_order == 'N' ) {
							if(strlen($_ShopInfo->getMemid())>0 && $_data->coupon_ok=="Y" && $staff_order == 'N' ) {
						?>
						<col style="width:170px">
						<?	}?>
						<col style="width:136px">
					</colgroup>
					<thead>
						<tr>
							<th scope="col">상품정보</th>
							<th scope="col">수량</th>
							<th scope="col">적립</th>
							<th scope="col">판매가</th>
							<?php
//								if(strlen($_ShopInfo->getMemid())>0 && $_data->coupon_ok=="Y" && $staff_order == 'N' && $cooper_order == 'N' ) {
								if(strlen($_ShopInfo->getMemid())>0 && $_data->coupon_ok=="Y" && $staff_order == 'N' ) {
							?>
							<th scope="col">쿠폰선택</th>
							<?	}?>
							<th scope="col">배송정보</th>
						</tr>
					</thead>
					
					<tbody>
<?php
	$product_count="0";
	foreach( $brandObj as $product ) {
		if($product['delivery_type'] == '0') {	//2016-10-07 libe90 매장발송일 경우 재고 가장 많은 매장으로 매장정보 표시
			continue;
		}
		$brand_product_name = get_brand_name( $product[brand] );
		$storeData = getStoreData($product['store_code']);
		$opt_price = 0; // 상품별 옶션가
		$pr_reserve = 0; //상품별 마일리지
        $tmp_opt_price = 0;
		if($product['delivery_type'] == '3'){
			$checkTodayDelivery = true;
			$arrDeliveryTodayAddress = array('post'=>$product['post_code'], 'address1'=>$product['address1'], 'address2'=>$product['address2']);
		}
		if($product['delivery_type'] == '1'){
			$checkMarketDelivery = true;
		}


		$vender_deli_price = 0;

		if( $product_deli[$vender][$product['productcode']] ){
			$vender_deli_price += $product_deli[$vender][$product['productcode']]['deli_price'];
			/*
			foreach( $product_deli[$vender][$product['productcode']] as $prDeliKey => $prDeliVal ){
				exdebug($prDeliKey);
				$vender_deli_price += $prDeliVal['deli_price'];
			}*/
		}
		/*
		if( $product_deli[$vender] ){
			foreach( $product_deli[$vender] as $prDeliKey => $prDeliVal ){
				$vender_deli_price += $prDeliVal['deli_price'];
			}
		}*/
		
		//$vender_deli_price += $vender_deli[$vender]['deli_price'];
?>
						<input type="hidden" name="obj_basketidx" value="<?=$product[basketidx]?>">
						<input type="hidden" name="obj_ci_no[<?=$product[basketidx]?>]" value="">
						<input type="hidden" name="obj_coupon_code[<?=$product[basketidx]?>]" value="">
						<input type="hidden" name="obj_dc[<?=$product[basketidx]?>]" value="">
						<input type="hidden" name="obj_product_price[<?=$product[basketidx]?>]" value="<?=$product['price']?>">

						<tr>
							<td class="pl-25">
								<div class="goods-in-td product_idx">
									<div class="thumb-img"><a href="<?=$Dir.FrontDir?>productdetail.php?productcode=<?=$product['productcode']?>" target="_blink"><img src="<?=getProductImage( $productImgPath, $product['tinyimage'] )?>"></a></div>
									<div class="info">
										<p class="brand-nm"><?=$brand_product_name?></p>
										<p class="goods-nm"><?=$product['productname']?></p>
										<p class="opt">
										<?php
											if($product[prodcode]){
												echo "품번 : ".$product[prodcode];
											}
											if($product[colorcode]){
												echo " / ";
												echo "색상 : ".$product[colorcode];
											}

											if( count( $product['option'] ) > 0 || strlen( $product['text_opt_subject'] ) > 0 ){
												if( count( $product['option'] ) > 0 ){
													$tmp_opt_subject = explode( '@#', $product['option_subject'] );
													if( $product['option_type'] == 0 ){ // 조합형 옵션
														$tmp_option = $product['option'][0];
														$tmp_opt_contetnt = explode( chr(30), $tmp_option['option_code'] );
														foreach( $tmp_opt_subject as $optKey=>$optVal ){
															echo " / ";
															echo $optVal.' : '.$tmp_opt_contetnt[$optKey];
															$tmp_opt_price += $optVal['option_price'] * $product['option_quantity'];
														}// option foreach
														$opt_price += $tmp_option['option_price'] * $product['option_quantity'];
													}
													if( $product['option_type'] == 1 ){ // 독립형 옵션
														foreach( $product['option'] as $optKey=>$optVal ){
															$tmp_opt_content = explode( chr(30), $optVal['option_code'] );
															echo $tmp_opt_subject[$optKey].' : '.$tmp_opt_content[1];
															$tmp_opt_price += $optVal['option_price'] * $product['option_quantity'];
															$opt_price += $optVal['option_price'] * $product['option_quantity'];
														}// option foreach
													}
												} // count option

												if( $product['text_opt_content'] ){ // 추가문구 옵션
													$tmp_text_subejct = explode( '@#', $product['text_opt_subject'] );
													$text_opt_content = explode( '@#', $product['text_opt_content'] );
													foreach( $text_opt_content as $textKey=>$textVal ){
														echo " / ";
														if( $textVal != '' ) {
															echo $tmp_text_subejct[$textKey].' : '.$textVal;
														}
													}
												}  // text_opt_content if
												if( $tmp_opt_price > 0 ){
													echo '(추가금액 : '.number_format( $tmp_opt_price ).')';
												}
											} else {
												echo "-";
											}// count option || text_opt_subject if

											//$pr_reserve = getReserveConversion( $product['reserve'], $product['reservetype'], ( $product['price'] * $product['quantity'] ) + $opt_price , "N" );
											$pr_reserve = getReserveConversion( $product['reserve'], "Y", ( $product['price'] * $product['quantity'] ) + $opt_price , "N" );
											if( strlen( $_ShopInfo->getMemid() ) == 0 ) $pr_reserve	= 0;
											$product_reserve += $pr_reserve; // 벤더별 상품 예상 적립금
											if($cooper_order == 'Y'){
												// order.class.php 옴겨야함 20170828 제휴사 수정
												list($sale_num) = pmysql_fetch("select b.group_productcode from tblmember a left join tblcompanygroup b on a.company_code=b.group_code where a.cooper_yn='Y' and a.id= '".$_ShopInfo->getMemid()."' ");
												$c_productcode = $product['productcode'];

												list($company_price) = pmysql_fetch("select ".$sale_num." from tblproduct where productcode= '".$c_productcode."' ");
												
												$t_product_price = ( $product['price']  * $product['quantity'] ) + $opt_price; //옵션가와 상품가를 합산해준다
												$c_product_price = ( $company_price  * $product['quantity'] ) ;

												if($c_product_price >= $t_product_price || $c_product_price == 0){
													$product_price = $t_product_price;
												}else{
													$product_price = $c_product_price;
												}
											}else{
												$product_price = ( $product['price']  * $product['quantity'] ) + $opt_price; //옵션가와 상품가를 합산해준다
											}
											$vender_price += $product_price; // 벤더별 상품가격

											$sum_product_reserve += $pr_reserve; // 총 예상 적립금
										?>
										<!--색상 : NAM  / 사이즈 55-->
										</p>
									</div>
								</div>
							</td>
							<td><?=$product['quantity']?></td>
							<td class="txt-toneB"><?=number_format($pr_reserve)?>P</td>
							<td class="txt-toneA">\ <?=number_format($product_price)?></td>
							<?php
//								if(strlen($_ShopInfo->getMemid())>0 && $_data->coupon_ok=="Y" && $staff_order == 'N' && $cooper_order == 'N' ) {
								if(strlen($_ShopInfo->getMemid())>0 && $_data->coupon_ok=="Y" && $staff_order == 'N' ) {
							?>
<?if($cooper_order == 'Y'  ) {?>
							<td><button class="btn-basic h-small w70 btn-couponList" type="button" onclick="javascript:product_ccoupon_pop(<?=$product[basketidx]?>,<?=$product_price?>)"><span>쿠폰사용</span></button></td>
<?}else{?>
							<td><button class="btn-basic h-small w70 btn-couponList" type="button" onclick="javascript:product_coupon_pop(<?=$product[basketidx]?>)"><span>쿠폰사용</span></button></td>
<?}?>
							<?	}?>
							<!--<td class="flexible-delivery"><strong class="txt-toneA">\3,000</strong><div class="pt-5">50,000원 이상<br>무료배송</div></td>-->
							
							<td class="flexible-delivery">
								<div class="with-question">
									<strong class="txt-toneA"><?if($product['delivery_type'] == '1'){?>[매장픽업]<?}else if($product['delivery_type'] == '3'){?>[당일수령]<?}?></strong>

									<div class="question-btn">
										<i class="icon-question">배송설명</i>
										<?if($product['delivery_type'] == '1'){?>
										<div class="comment"><?=$product['reservation_date']?>일에 <?=$storeData['name']?>에서 수령하시면 됩니다. </div>
										<?}else if($product['delivery_type'] == '3'){?>
										<div class="comment">선택하신 상품은 당일수령이 가능한 상품입니다. </div>
										<?}?>
									</div>
								</div>
								<?if($product['delivery_type'] == '1'){?>
								<strong class="txt-toneA">예약일 : <?=$product['reservation_date']?></strong><div class="pt-5"><?=$storeData['name']?></div>
								<?}else if($product['delivery_type'] == '3'){?>
								<strong class="txt-toneA">\<?=number_format($product_deli[$vender][$product['productcode']][deli_price])?></strong><div class="pt-5"><?=$storeData['name']?></div>
								<?}?>
								<button class="btn-basic h-small w70 mt-5 btn-infoStore" onclick="javascript:store_map('<?=$product['store_code']?>')" type="button"><span>매장안내</span></button>
							</td>
							<?/*?>
							<td class="flexible-delivery" rowspan="<?=count($brandObj)?>"><div class="pt-5">
								<?if(count($storeData) > 0 && $product['delivery_type'] != '3'){	//2016-10-07 libe90 매장발송 정보표시?>
									<!--  <li style = 'color:blue;'>[<?=$arrDeliveryType[$product['delivery_type']]?>] <?=$storeData['name']?></li>-->
									<?if($product['delivery_type'] == '1'){?>
										<span style = 'color:blue;'>예약일 : <?=$product['reservation_date']?></span>
									<?}?>
								<?}else if($product['delivery_type'] == '3'){?>
									<span style = 'color:blue;'>[<?=$arrDeliveryType['2']?>] <?=$storeData['name']?></span>
									<span style = 'color:blue;'>주소 : [<?=$product['post_code']?>] <?=$product['address1']?> <?=$product['address2']?></span>
								<?}?>		
							</div></td>
							<?*/?>
							
						</tr>
<!-- //상품단위 종료 -->
<?php
		# 장바구니 쿠폰 제외
		foreach( $basket_coupon as $basketKey=>$basketVal ){
			if( !$_CouponInfo->check_coupon_product( $product['productcode'], 2, $basketVal ) ){
				unset( $basket_coupon[$basketKey] );
			}
		}
		$product_count++;
	} //foreach
?>
					</tbody>
					
					<tfoot>
<?php
	
	if( $vender_info[$vender] ){
		
?>
						<tr>
							<td colspan="8" class="reset">
								<div class="cart-total-price clear">
									<dl>
										<dt>상품합계</dt>
										<dd>\ <?=number_format( $vender_price )?></dd>
									</dl>
<!--
									<div <?if(strlen($_ShopInfo->getMemid())>0 && $_data->coupon_ok=="Y" && $staff_order == 'N' && $cooper_order == 'N' ) {}else {?> class='hide'<?}?>>
-->
									<div <?if(strlen($_ShopInfo->getMemid())>0 && $_data->coupon_ok=="Y" && $staff_order == 'N' && $cooper_order == 'N') {}else {?> class='hide'<?}?>>
									<span class="txt point-color">-</span>
									<dl class="point-color">
										<dt>쿠폰할인</dt>
										<dd>\ <em class="CLS_prCoupon" style="vertical-align: baseline;">0</em></dd>
									</dl>
									</div>
									<span class="txt">+</span>
									<dl>
										<dt>배송비</dt>
										<dd>\ <?=number_format( $vender_deli_price )?></dd>
									</dl>
									<dl class="sum">
										<dt>합계</dt>
										<dd>\ <em class="CLS_Tprice" style="vertical-align: baseline;"><?=number_format( $vender_price + $vender_deli_price )?></em></dd>
									</dl>
								</div>
							</td>
						</tr>
<?php
	}
?>
					</tfoot>
					
				</table>
			</section><!-- //브랜드 주문상품 -->
					<!-- // 상품 리스트 -->
<?php
	
	if( $vender_info[$vender]['deli_select'] == '0' || $vender_info[$vender]['deli_select'] == '2' ) $deli_price += $vender_deli_price;
    if( $vender_info[$vender]['deli_select'] == '1' ) $deli_price2 += $vender_deli_price;
	$sumprice += $vender_price;
	}
} // foreach
?>

		

			<div class="order-infoReg clear mt-60">
				<div class="inner-input">
					<!-- 할인 및 결제정보 -->
					<section>
						<header class="cart-section-title"><h3>할인 및 결제정보</h3></header>
						<table class="th-left">
							<caption>할인 및 결제 확인</caption>
							<colgroup>
								<col style="width:178px">
								<col style="width:auto">
							</colgroup>
							<tbody>
								<tr>
									<th scope="row"><label>총 상품금액</label></th>
									<td>\ <?=number_format($sumprice)?></td>
								</tr>

<?php

	if ( strlen( $_ShopInfo->getMemid() ) > 0 && $user_reserve != 0 ){

?>
<?php
	if($okreserve<0){
		$okreserve=(int)($sumprice*abs($okreserve)/100);
		if($reserve_maxprice>$sumprice) {
			$okreserve=$user_reserve;
			$remainreserve=0;
		} else if($okreserve>$user_reserve) {
			$okreserve=$user_reserve;
			$remainreserve=0;
		} else {
			$remainreserve=$user_reserve-$okreserve;
		}
	}
?>
<!--
								<tr <?if($staff_order == 'Y' || $cooper_order == 'Y' ) {?> class='hide'<?}?>>
-->
								<tr <?if($staff_order == 'Y' ) {?> class='hide'<?}?>>
									<th scope="row"><label for="mileage-use">포인트 사용</label></th>
									<td>
										<input type="hidden" name="okreserve" id='okreserve' value="<?=$user_reserve?>">
<?php
		if( $_data->reserve_maxprice > $sumprice ) {
?>
										<input type="hidden" name="usereserve" id="mileage-use" value='0'> 상품금액 <?=number_format($_data->reserve_maxprice)?>원 이상 사용가능
<?php
		}else if( $_data->reserve_maxuse > $user_reserve ) {
?>
										<input type="hidden" name="usereserve" id="mileage-use" value='0'> 누적포인트가 <?=number_format($_data->reserve_maxuse)?>포인트 이상 사용가능
<?php
		}else if( $user_reserve >= $_data->reserve_maxuse ){
?>
										<div class="input-cover">
											<input type="text" name="usereserve" id="mileage-use" title="E포인트 사용액 입력" id="use-my-ePoint" class="w100" value='0'>
											<span class="txt">P</span>
											<div class="checkbox ml-10">
												<input type="checkbox" id="all-mileage-use">
												<label for="all-mileage-use">모두사용</label>
											</div>
											<span class="pl-20 fz-13">(사용가능 포인트 <?=number_format( $user_reserve )?>P)</span>
										</div>
<?php
		}else{
?>
										<input type="hidden" name="usereserve" id="mileage-use" value='0'> 사용불가
<?php
		}
?>
									</td>
								</tr>
<?php
	} else {
?>
							<input type="hidden" name="usereserve" id="mileage-use" value='0'>
							<input type="hidden" name="okreserve" id='okreserve' value="0">
<?php
	}
?>
<?php
	if ( strlen( $_ShopInfo->getMemid() ) > 0 && $user_point != 0 ){
?>
<?php
	if($okpoint<0){
		$okpoint=(int)($sumprice*abs($okpoint)/100);
		if($e_reserve_maxprice>$sumprice) {
			$okpoint=$user_point;
			$remainpoint=0;
		} else if($okpoint>$user_point) {
			$okpoint=$user_point;
			$remainpoint=0;
		} else {
			$remainpoint=$user_point-$okpoint;
		}
	}
?>
<!--
								<tr <?if($staff_order == 'Y' || $cooper_order == 'Y' ) {?> class='hide'<?}?>>
-->
								<tr <?if($staff_order == 'Y' ) {?> class='hide'<?}?>>
									<th scope="row"><label for="use-my-ePoint">E포인트 사용</label></th>
									<td>
										<input type="hidden" name="okpoint" id='okpoint' value="<?=$user_point?>">
<?php
		if( $_data->e_reserve_maxprice > $sumprice ) {
?>
										<input type="hidden" name="usepoint" id="point-use" value='0'> 상품금액 <?=number_format($_data->e_reserve_maxprice)?>원 이상 사용가능
<?php
		}else if( $_data->e_reserve_maxuse > $user_point ) {
?>
										<input type="hidden" name="usereserve" id="point-use" value='0'> 누적포인트가 <?=number_format($_data->e_reserve_maxuse)?>포인트 이상 사용가능
<?php
		}else if( $user_point >= $_data->e_reserve_maxuse ){
?>
										<div class="input-cover">
											<input type="text" name="usepoint" id="point-use" title="E포인트 사용액 입력" id="use-my-ePoint" class="w100" value='0'>
											<span class="txt">P</span>
											<div class="checkbox ml-10">
												<input type="checkbox" id="check-epoint-all">
												<label for="check-epoint-all">모두사용</label>
											</div>
											<span class="pl-20 fz-13">(사용가능 포인트 <?=number_format( $user_point )?>P)</span>
										</div>
<?php
		}else{
?>
										<input type="hidden" name="usepoint" id="point-use" value='0'> 사용불가
<?php
		}
?>
									</td>
								</tr>
<?php
	} else {
?>
							<input type="hidden" name="usepoint" id="point-use" value='0'>
							<input type="hidden" name="okpoint" id='okpoint' value="0">
<?php
	}
?>
								<tr <?if(strlen($_ShopInfo->getMemid())>0 && $_data->coupon_ok=="Y" && $staff_order == 'N' ) {}else {?> class='hide'<?}?>>
<!--								<tr <?if(strlen($_ShopInfo->getMemid())>0 && $_data->coupon_ok=="Y" && $staff_order == 'N' && $cooper_order == 'N' ) {}else {?> class='hide'<?}?>>-->

									<th scope="row"><label>쿠폰할인</label></th>
									<td class="point-color">- \ <em class="CLS_prCoupon">0</em></td>
								</tr>
								<div id = "ID_coupon_code_layer">
									<div id = "ID_prd_coupon_layer" ></div>
									<!--
									<div id = "ID_bk_coupon_layer" ></div>
									<div id = "ID_deli_coupon_layer" ></div>-->
								</div>
								<tr>
									<th scope="row"><label>배송비</label></th>
									<td>\ <em id='delivery_price'><?=number_format($deli_price)?></em></td>
								</tr>
								<tr>
									<th scope="row"><label>도서산간 배송비</label></th>
									<td>\ <em class='area_delivery_price'>0</em></td>
								</tr>
								<tr>
									<th scope="row"><label>실 결제금액</label></th>
									<td class="fz-14 fw-bold point-color">\ <em class="price_sum" id="price_sum"><?=number_format($sumprice+$deli_price)?></em></td>
								</tr>
							</tbody>
						</table>
					</section><!-- //.할인 및 결제정보 -->

					<p class="mt-10 point-color">※ 포인트 사용 시 전체 상품에 분배되어 적용되며 부분 취소 시 개별 상품에 적용된 포인트만 환불됩니다.<br>※ E포인트는 10만원 이상 구매 시 사용 가능/ 통합 포인트는 20,000P 이상 적립 시 사용 가능</p>
					
					<!-- 주문고객 정보 -->
					<section class="mt-40">
						<header class="cart-section-title"><h3>주문고객 정보</h3></header>
						<table class="th-left">
							<caption>주문고객 정보 확인</caption>
							<colgroup>
								<col style="width:178px">
								<col style="width:auto">
							</colgroup>
							<tbody>
								<tr>
									<th scope="row"><label for="order_name" class="essential">주문자</label></th>
									<td><div class="input-cover">
									<?php
if(strlen( $_ShopInfo->getMemid() ) > 0 ) {
?>
										<!-- 요청에 의해 readonly를 뺌 2015 12 09 유동혁 -->
										<input type='text' name="sender_name" id="order_name" value="<?=$userName?>" style="width:270px" required msgR="주문하시는분의 이름을 적어주세요">
<?php
} else {
?>
										<input type='text'  name="sender_name" id="order_name" value="" style="width:270px" required msgR="주문하시는분의 이름을 적어주세요">
<?php
} // else
?>
										
									</div></td>
								</tr>
								<tr>
									<th scope="row"><label for="order_email">이메일</label></th>
									<td>
										<div class="input-cover">
											<input type="text"  style="width:190px" title="이메일 입력" id="user-email" name='sender_email1' value='<?=$email[0]?>'>
											<span class="txt">@</span>
											<div class="select">
												<select style="width:170px" name="email_select" name="email_select" onchange="javascript:email_change()">
													<option value="">직접입력</option>
													<option value="naver.com" <?=$email[1]=='naver.com'?' selected':''?>>naver.com</option>
													<option value="gmail.com" <?=$email[1]=='gmail.com'?' selected':''?>>gmail.com</option>
													<option value="daum.net" <?=$email[1]=='daum.net'?' selected':''?>>daum.net</option>
													<option value="nate.com" <?=$email[1]=='nate.com'?' selected':''?>>nate.com</option>
													<option value="hanmail.net" <?=$email[1]=='hanmail.net'?' selected':''?>>hanmail.net</option>
													<option value="yahoo.com" <?=$email[1]=='yahoo.com'?' selected':''?>>yahoo.com</option>
													<option value="dreamwiz.com" <?=$email[1]=='dreamwiz.com'?' selected':''?>>dreamwiz.com</option>
												</select>
											</div>
											
											<input type="text" title="도메인 직접 입력" name="sender_email2" id="sender_email2" class="ml-10" value="<?=$email[1]?>" style="width:170px; display:none;"> <!-- [D] 직접입력시 인풋박스 출력 -->
										</div>
									</td>
								</tr>
								<tr>
									<th scope="row"><label for="sender_tel1" class="essential">휴대전화</label></th>
									<td>
										<div class="input-cover">
											<div class="select">
												<select id="sender_tel1" style="width:110px" name="sender_tel1" >
													<option value="010"<?=$mobile[0]=='010'?' selected':''?>>010</option>
													<option value="011"<?=$mobile[0]=='011'?' selected':''?>>011</option>
													<option value="016"<?=$mobile[0]=='016'?' selected':''?>>016</option>
													<option value="017"<?=$mobile[0]=='017'?' selected':''?>>017</option>
													<option value="018"<?=$mobile[0]=='018'?' selected':''?>>018</option>
													<option value="019"<?=$mobile[0]=='019'?' selected':''?>>019</option>
												</select>
											</div>
											<span class="txt">-</span>
											<input type="text" id="user-phone" name="sender_tel2" value="<?=$mobile[1] ?>" maxlength='4' style="width:110px" title="휴대전화번호 가운데 입력자리">
											<span class="txt">-</span>
											<input type="text" name="sender_tel3" value="<?=$mobile[2] ?>" maxlength='4' style="width:110px" title="휴대전화번호 마지막 입력자리">
										</div>
									</td>
								</tr>
								<tr>
									<th scope="row"><label for="home_tel1">전화번호(선택)</label></th>
									<td>
										<div class="input-cover">
											<div class="select">
												<select id="home_tel1" name="home_tel1" style="width:110px">
													<option value="02" selected>02</option>
													<option value="031">031</option>
													<option value="032">032</option>
													<option value="033">033</option>
													<option value="041">041</option>
													<option value="042">042</option>
													<option value="043">043</option>
													<option value="044">044</option>
													<option value="051">051</option>
													<option value="052">052</option>
													<option value="053">053</option>
													<option value="054">054</option>
													<option value="055">055</option>
													<option value="061">061</option>
													<option value="062">062</option>
													<option value="063">063</option>
													<option value="064">064</option>
												</select>
											</div>
											<span class="txt">-</span>
											<input type="text" id="home_tel2" name="home_tel2" maxlength='4' style="width:110px" title="전화번호 가운데 입력자리">
											<span class="txt">-</span>
											<input type="text" name="home_tel3" id='home_tel3' maxlength='4' style="width:110px" title="전화번호 마지막 입력자리">
										</div>
									</td>
								</tr>
							</tbody>
						</table>
					</section><!-- //.주문고객 정보 -->

					<!-- 배송지 정보 -->
					<section class="mt-40">
						<header class="cart-section-title">
							<h3>배송지 정보</h3>
							<div class="att" style="bottom:-9px">
								<?if($checkTodayDelivery == false){?>
								<div class="checkbox">
									<input type="checkbox" name="same" id="dev_orderer" value="Y" onclick="SameCheck(this.checked)">
									<label for="dev_orderer">주문고객과 동일한 주소 사용</label>
								</div>
								<?if( $_ShopInfo->getMemid()){?>
								<button class="btn-basic h-small ml-20 btn-address-list" type="button" id="btn-deliveryList"><span>배송지목록</span></button>
								<?}?>
								<?}?>
							</div>
						</header>
						<table class="th-left">
							<caption>배송지 정보 확인</caption>
							<colgroup>
								<col style="width:178px">
								<col style="width:auto">
							</colgroup>
							<tbody>
								<tr>
									<th scope="row"><label for="receiver_name" class="essential">받는사람</label></th>
									<td>
										<div class="input-cover">
											<?if($checkTodayDelivery == false && $checkMarketDelivery==true && $_ShopInfo->getMemid()){?>
											<input type="text" id="receiver_name" name = 'receiver_name' style="width:242px" value="<?=$userName?>" required msgR="받으시는분 이름을 입력하세요." title="받는사람 입력자리">
											<?}else{?>
											<input type="text" id="receiver_name" name = 'receiver_name' style="width:242px" required msgR="받으시는분 이름을 입력하세요." title="받는사람 입력자리">
											<?}?>
											<?if($_ShopInfo->getMemid()){?>
											<div class="checkbox ml-20">
												<input type="checkbox" name="destinationt_type" value="Y" id="delivery_default_save">
												<label for="delivery_default_save">기본 배송지로 저장</label>
											</div>
											<?}?>
										</div>
									</td>
								</tr>
								<tr>
									<th scope="row"><label for="receiver_tel21" class="essential">휴대전화</label></th>
									<td>
										<?if($checkTodayDelivery == false && $checkMarketDelivery==true && $_ShopInfo->getMemid()){?>
										<div class="input-cover">
											<div class="select">
												<select id="receiver_tel21" name="receiver_tel21" style="width:110px">
													<option value="010"<?=$mobile[0]=='010'?' selected':''?>>010</option>
													<option value="011"<?=$mobile[0]=='011'?' selected':''?>>011</option>
													<option value="016"<?=$mobile[0]=='016'?' selected':''?>>016</option>
													<option value="017"<?=$mobile[0]=='017'?' selected':''?>>017</option>
													<option value="018"<?=$mobile[0]=='018'?' selected':''?>>018</option>
													<option value="019"<?=$mobile[0]=='019'?' selected':''?>>019</option>
												</select>
											</div>
											<span class="txt">-</span>
											<input type="text" id="receiver_tel22" name="receiver_tel22" maxlength='4' value="<?=$mobile[1] ?>" onKeyUp="strnumkeyup(this)" required style="width:110px" title="휴대전화번호 가운데 입력자리">
											<span class="txt">-</span>
											<input type="text" id="receiver_tel23" name="receiver_tel23" maxlength='4' value="<?=$mobile[2] ?>" onKeyUp="strnumkeyup(this)" required style="width:110px" title="휴대전화번호 마지막 입력자리">
										</div>
										<?}else{?>
										<div class="input-cover">
											<div class="select">
												<select id="receiver_tel21" name="receiver_tel21" style="width:110px">
													<option value="010" selected>010</option>
													<option value="011">011</option>
													<option value="016">016</option>
													<option value="017">017</option>
													<option value="018">018</option>
													<option value="019">019</option>
												</select>												
											</div>
											<span class="txt">-</span>
											<input type="text" id="receiver_tel22" name="receiver_tel22" maxlength='4' onKeyUp="strnumkeyup(this)" required style="width:110px" title="휴대전화번호 가운데 입력자리">
											<span class="txt">-</span>
											<input type="text" id="receiver_tel23" name="receiver_tel23" maxlength='4' onKeyUp="strnumkeyup(this)" required style="width:110px" title="휴대전화번호 마지막 입력자리">
										</div>
										<?}?>
									</td>
								</tr>
								<tr>
									<th scope="row"><label for="receiver_tel11">전화번호(선택)</label></th>
									<td>
										<div class="input-cover">
											<div class="select">
												<select id="receiver_tel11" name="receiver_tel11" style="width:110px">
													<option value="02" selected>02</option>
													<option value="031">031</option>
													<option value="032">032</option>
													<option value="033">033</option>
													<option value="041">041</option>
													<option value="042">042</option>
													<option value="043">043</option>
													<option value="044">044</option>
													<option value="051">051</option>
													<option value="052">052</option>
													<option value="053">053</option>
													<option value="054">054</option>
													<option value="055">055</option>
													<option value="061">061</option>
													<option value="062">062</option>
													<option value="063">063</option>
													<option value="064">064</option>
												</select>
											</div>
											<span class="txt">-</span>
											<input type="text" id="receiver_tel12" name="receiver_tel12" maxlength='4' onKeyUp="strnumkeyup(this)" style="width:110px" title="전화번호 가운데 입력자리">
											<span class="txt">-</span>
											<input type="text" id="receiver_tel13" name="receiver_tel13" maxlength='4' onKeyUp="strnumkeyup(this)" style="width:110px" title="전화번호 마지막 입력자리">
										</div>
									</td>
								</tr>
								<tr>
									<th scope="row"><label class="essential">주소</label></th>
									<td>
										<?if($checkTodayDelivery == false){?>
										<ul class="input-multi input-cover">
											<input type='hidden' id='post5' name='post5' value='' >
											<input type="hidden" id="rpost1" name = 'rpost1'>
											<input type="hidden" id='rpost2' name = 'rpost2'>
											<li><input type="text" name = 'post' id = 'post' title="우편번호 입력자리" readonly><button type="button" class="btn-basic" onclick="javascript:openDaumPostcode();"><span>주소찾기</span></button></li>
											<li><input type="text" name = 'raddr1' id = 'raddr1' title="검색된 주소" class="w100-per" readonly></li>
											<li><input type="text" name = 'raddr2' id = 'raddr2'title="상세주소 입력" class="w100-per"></li>
										</ul>
										<?}else{?>
										<ul class="input-multi input-cover">
											<input type='hidden' id='post5' name='post5' value = '<?=$arrDeliveryTodayAddress['post']?>' readonly>
											<input type="hidden" id="rpost1" name = 'rpost1'>
											<input type="hidden" id='rpost2' name = 'rpost2'>
											<li><input type="text" name = 'post' id = 'post' value = '<?=$arrDeliveryTodayAddress['post']?>' title="우편번호 입력자리" readonly></li>
											<li><input type="text" name = 'raddr1' id = 'raddr1' value = '<?=$arrDeliveryTodayAddress['address1']?>' title="검색된 주소" class="w100-per" readonly></li>
											<li><input type="text" name = 'raddr2' id = 'raddr2' value = '<?=$arrDeliveryTodayAddress['address2']?>' title="상세주소 입력" class="w100-per" readonly></li>
										</ul>
										<?}?>
									</td>
								</tr>
								<tr>
									<th scope="row"><label for="prmsg_chg">배송 요청사항</label></th>
									<td>
										<ul class="input-multi input-cover">
											<li>
												<div class="select">
													<input type="hidden" name="msg_type" value="1">
													<select id="prmsg_chg" name='prmsg_chg' style="width:260px">
														<option value="" selected>직접입력</option>
														<option value="부재시 경비실에 맡겨 주세요">부재시 경비실에 맡겨 주세요</option>
														<option value="부재시 문앞에 놓아주세요">부재시 문앞에 놓아주세요</option>
														<option value="배송전에 연락주세요">배송전에 연락주세요</option>
														<option value="빠른배송 부탁드려요">빠른배송 부탁드려요</option>
														<option value="소화전에 넣어주세요">소화전에 넣어주세요</option>
														<option value="보관함에 넣어주세요">보관함에 넣어주세요</option>
													</select>
												</div>
											</li>
											<li><input type="text" title="배송 요청사항 입력" name = 'order_prmsg' id="order_prmsg" class="w100-per" maxlength="50" ></li>
										</ul>
									</td>
								</tr>
							</tbody>
						</table>
					</section><!-- //.배송지 정보 -->

				</div><!-- //.inner-input -->
				<div class="inner-confirm">
					<!-- 결제 수단 선택 -->
					<section class="order-payType">
						<header class="cart-section-title"><h3>결제 수단 선택</h3></header>
						<div class="frm">
							<dl>
								<dt>신용카드</dt>
								<?if(strstr("YC", $_data->payment_type) && ord($_data->card_id)) {?>
								<dd>
									<div class="radio">
										<input type="radio" id="dev_payment2" name="dev_payment" value="C" class='dev_payment' onclick="sel_paymethod(this);">
										<label for="dev_payment2">신용카드(일반)</label>
									</div>
								</dd>
								<?}?>
								
								<?if($escrow_info["onlycard"]!="Y" && strstr("YN", $_data->payment_type)) {?>
								<dd style="display:none">
									<div class="radio">
										<input type="radio" id="dev_payment1" name="dev_payment" value="B" class='dev_payment' onclick="sel_paymethod(this);">
										<label for="dev_payment1">무통장 입금</label>
									</div>
								</dd>
								<?}?>
							</dl>
							<dl>
								<dt>현금결제</dt>
								<?if($escrow_info["onlycard"]!="Y" && !strstr($_SERVER["HTTP_USER_AGENT"],'Mobile') && !strstr($_SERVER[HTTP_USER_AGENT],"Android") && ord($_data->trans_id)){?>
								<dd>
									<div class="radio">
										<input type="radio" id="dev_payment3" name="dev_payment" value="V" class='dev_payment' onclick="sel_paymethod(this);">
										<label for="dev_payment3">실시간 계좌이체</label>
									</div>
								</dd>
								<?}?>
								<dd>
									<div class="radio">
										<input type="radio"  id="dev_payment4" name="dev_payment" value="O" class='dev_payment' onclick="sel_paymethod(this);" >
										<label for="dev_payment4">가상계좌</label>
									</div>
								</dd>
								<?if(( $escrow_info["escrowcash"]=="A" || ($escrow_info["escrowcash"]=="Y" && (int)($sumprice+$deli_price)>=$escrow_info["escrow_limit"])) ){?>
								<?
									$pgid_info="";
									$pg_type="";
									$pgid_info=GetEscrowType($_data->escrow_id);
									$pg_type=trim($pgid_info["PG"]);
								?>
									<?if(strstr("ABCDG",$pg_type)){?>
								<dd style="padding-top:10px;">
									<div class="radio">
										<input type="radio"  id="dev_payment5" name="dev_payment" value="Q" class='dev_payment' onclick="sel_paymethod(this);" >
										<label for="dev_payment5">에스크로(가상계좌)</label>
									</div>
								</dd>
									<?}?>
								<?}?>
								<dd style="padding-top:10px;">
									<div class="radio">
										<input type="radio"  id="dev_payment6" name="dev_payment" value="M" class='dev_payment' onclick="sel_paymethod(this);" >
										<label for="dev_payment6">휴대폰</label>
									</div>
								</dd>
							</dl>
							<?//if($_ShopInfo->getMemid() == "kyung424" || $_ShopInfo->getMemid() == "yiseoyi" ||  $_ShopInfo->getMemid() == "jjus0827" || $_ShopInfo->getMemid() == "sw160071"  || $_ShopInfo->getMemid() == "sw149010" || $_ShopInfo->getMemid() == "for0319"){?>
							<?/*if($_ShopInfo->getMemid() == "kyung424" || $_ShopInfo->getMemid() == "yiseoyi" || $_ShopInfo->getMemid() == "for0319"){*/?>
							<dl>
								<dt>간편결제</dt>
								<dd>
									<div class="radio">
										<input type="radio" id="dev_payment7" name="dev_payment" value="Y" class='dev_payment' onclick="sel_paymethod(this);">
										<label for="dev_payment7" style="color: red;">PAYCO</label>
									</div>
								</dd>
							</dl>
							<p class="att">
							<!--실행되는 보안 플러그인에 카드정보를 입력해주세요. <br>
							결제는 암호화 처리를 통해 안전합니다. 결제 후 재고가<br>
							없거나 본인이 요청이 있을 경우 배송전 결제를 취소할 수 있습니다.--></p>
							<!-- 신용카드 안내 -->
							<div id="C_notice" class = 'noticeBox hide'>
								<ul class="lst-bullet-dot mb5">
									<li>ㆍ신용카드 결제시 '카드사혜택’ 버튼을 클릭하시면 무이자할부/청구할인/즉시할인에 대한 정보를 보실 수 있습니다.</li>
									<li>ㆍ체크카드, 법인카드의 경우 무이자 할부행사에서 제외됩니다.</li>
									<li>ㆍ신용카드로 결제하시는 최종 결제 금액이 기준금액 미만이거나, 그 외 무이자 할부가 되지 않는 기타 신용카드를 사용하시는 경우는 유이자 할부로 결제되오니 반드시 참고하시기 바랍니다.</li>
								</ul>
								<?if(strlen($cb_nointerest_info_pc) > 10){?>
								<a href="#modalCard" class="btn btn-default" data-toggle="modal">카드사 혜택</a>
								<?}?>
							</div>
							<!-- // 신용카드 안내 -->


							<!-- 실시간 계좌이체 안내 -->
							<div id="V_notice" class = 'noticeBox hide'>
								<ul class="lst-bullet-dot mb5">
									<li>ㆍ주문확인 후 NHN KCP 결제창에서 현금영수증 신청이 가능합니다.</li>
									<li>ㆍ결제와 동시에 ㈜신원몰에 입금 처리되며, 10분 이내에 입금확인이 가능합니다.</li>
								</ul>
<!--
								<p>
									고객님의 안전거래를 위해 현금등으로 모든 거래 결제시 저희 쇼핑몰에서<br>
									가입한 KCP 전자결제의 매매보호(에스크로) 서비스를 이용하실 수 있습니다.<br>
									결제대금예치업 등록번호 : 02-006-00001
								</p>
-->
							</div>
							<!-- // 실시간 계좌이체 안내 -->


							<!-- 무통장입금(가상계좌) 안내 -->
							<div id="O_notice" class = 'noticeBox hide'>
								<ul class="lst-bullet-dot mb5">
									<li>ㆍ가상계좌(무통장) 이용 시 포인트, 쿠폰을 사용했을 경우, 유효기간이 지나기 전에 입금해 주셔야 하며, 유효기간 이후 입금할 경우 주문이 취소됩니다. 가상계좌(무통장) 입금의 경우 입금 확인 후부터 배송이 진행됩니다.</li>
									<li>ㆍ가상계좌(무통장) 결제 시 주문일로 부터 익일 이내 입금을 하지 않을 경우 자동 취소됩니다.</li>
									<li>ㆍ입금 시 주문자 성함과 동일하게 기재해 주시기 바랍니다. 다를 경우 고객센터 (<?=$_data->info_tel?>)로 연락 주시기 바랍니다.</li>
									<li>ㆍ결제 금액과 계좌번호를 SMS로 발송하므로 휴대폰 번호를 정확히 입력해 주시기 바랍니다.</li>
									<li>ㆍ현금영수증 신청은 NHN KCP 결제창에서 제공됩니다.</li>
								</ul>
<!--
								<p>
									고객님의 안전거래를 위해 현금등으로 모든 거래 결제시 저희 쇼핑몰에서<br>
									가입한 KCP 전자결제의 매매보호(에스크로) 서비스를 이용하실 수 있습니다.<br>
									결제대금예치업 등록번호 : 02-006-00001
								</p>
-->
							</div>
							<!-- // 무통장입금(가상계좌) 안내 -->

							<!--핸드폰 안내 -->
							<div id="M_notice" class = 'noticeBox hide'>
								<ul class="lst-bullet-dot mb5">
									<li>ㆍ신원몰에서 휴대폰으로 결제 가능한 최대 금액은 월 30만원이나, 개인별 한도금액은 통신사 및 개인 설정에 따라 다를 수 있습니다.</li>
									<li>ㆍ휴대폰으로 결제하신 금액은 익월 휴대폰 요금에 함께 청구되며 별도의 수수료는 부과되지 않습니다.</li>
									<li>ㆍ휴대폰 소액결제로 구매하실 경우 현금영수증이 발급되지 않습니다.</li>
									<li><br/></li>
									<li>ㆍ휴대폰 결제로 구매하신 상품의 취소/반품은 처리완료 시점에 따라 다음과 같이 이루어집니다.</li>
									<li>-결제하신 당월에 취소/반품 처리가 완료되는 경우 휴대폰 이용요금에 부과예정이던 금액이 취소됩니다.</li>
									<li>-결제하신 당월이 지난 후 취소/반품처리가 완료되는 경우, 환불액이 고객님의 계좌로 현금 입금해 드립니다.</li>
									<li><br/></li>
									<li>ㆍ휴대폰결제관련 문의사항은 NHN KCP 고객센터 02-2108-1000 또는 신원몰 고객센터 1661-2585으로 연락주시기 바랍니다.</li>
								</ul>
							</div>
							<!-- // !--핸드폰 안내 -->


							<!-- 페이코 안내 -->
							<div id="Y_notice" class = 'noticeBox hide'>
								<ul class="lst-bullet-dot mb5">
									<li>ㆍPAYCO는 온/오프라인 쇼핑은 물론 송금, 멤버십 적립까지 가능한 통합 서비스입니다.</li>
									<li>ㆍ휴대폰과 카드 명의자가 동일해야 결제 가능하며, 결제금액 제한은 없습니다.</li>
									<li>ㆍ지원카드 : 모든 국내 신용/체크카드</li>
								</ul>
							</div>
							<!-- // 페이코 안내 -->

							<?//}else{?>
							<!--<p class="att">실행되는 보안 플러그인에 카드정보를 입력해주세요. <br>
							결제는 암호화 처리를 통해 안전합니다. 결제 후 재고가<br>
							없거나 본인이 요청이 있을 경우 배송전 결제를 취소할 수 있습니다.</p>-->
							<?//}?>
						</div>
						<div class="pay-type-card" id="card_type" style="display:none">
							<table border=0 cellpadding=0 cellspacing=0 width="100%" summary="임금계좌를 선택">
								<colgroup>
									<?if($etcmessage[2]=="Y") {?><col width="20%" ><?}?>
									<col >
								</colgroup>
								<?if($etcmessage[2]=="Y") {?>
								<tr>
									<th scope="row">입금자명</th>
									<td>
										<input type="text" name="bank_sender" value="" >
									</td>
								</tr>
								<?}?>
								<tr>
									<th scope="row">입금계좌</th>
									<td>
										<select name="pay_data_sel" id="pay_data_sel" onchange="sel_account(this)" style="width:100%;">
											<option value='' >입금 계좌번호 선택 (반드시 주문자 성함으로 입금)</option>
											<?foreach($bank_payinfo as $k => $v){?>
											<option value="<?=$v?>" ><?=$v?></option>
											<?}?>
										</select>
									</td>
								</tr>
								<tr>
									<th></th>
									<td>* 반드시 주문자 성함으로 입금</td>
								</tr>
							</table>
						</div>
					</section><!-- //결제 수단 선택 -->
					

					<!-- 결제금액 -->
					<section class="order-payConfirm mt-40">
						<?$p_price=$sumprice+$sumpricevat;?>
						<input type="hidden" name="total_sum" id='total_sum' value="<?=$p_price?>">
						<input type="hidden" name="total_sumprice" id='total_sumprice' value="<?=$p_price?>">
						<input type='hidden' name='total_deli_price' id='total_deli_price' value="<?=$deli_price?>" >
						<input type='hidden' name='total_deli_price2' id='total_deli_price2' value="<?=$deli_price2?>" >
						<input type='hidden' name='total_deli_price_area' id='total_deli_price_area' value="0" >
						<header class="cart-section-title"><h3>결제금액</h3></header>
						<div class="frm">
							<dl>
								<dt>총 상품금액</dt>
								<dd>\<em id="paper_goodsprice" ><?=number_format($sumprice)?></em></dd>
							</dl>
							<dl>
								<dt>배송비</dt>
								<dd>\ <em id='delivery_price'><?=number_format($deli_price)?></em></dd>
							</dl>
							<dl>
								<dt>도서산간 배송비</dt>
								<dd>\ <em class='area_delivery_price'>0</em></dd>
							</dl>
							<dl>
								<dt>포인트 사용</dt>
								<dd class="point-color">- <em class="CLS_saleMil">0</em> P</dd>
							</dl>
							<dl>
								<dt>E포인트 사용</dt>
								<dd class="point-color">- <em class="CLS_salePoi">0</em> P</dd>
							</dl>
							<dl <?if(strlen($_ShopInfo->getMemid())>0 && $_data->coupon_ok=="Y" && $staff_order == 'N' ) {}else {?> class='hide'<?}?>>
<!--							<dl <?if(strlen($_ShopInfo->getMemid())>0 && $_data->coupon_ok=="Y" && $staff_order == 'N' && $cooper_order == 'N' ) {}else {?> class='hide'<?}?>>-->


								<dt>쿠폰 사용</dt>
								<dd class="point-color">- \ <em class="CLS_prCoupon">0</em></dd>
							</dl>
							<!--
							<dl>
								<dt>장바구니 쿠폰 사용</dt>
								<dd class="point-color">- \ 0</dd>
							</dl>-->
							<dl class="final-price">
								<dt>실 결제금액</dt>
								<dd class="point-color">\ <em class="price_sum" id="price_sum"><?=number_format($sumprice+$deli_price)?></em></dd>
							</dl>
							<?if($staff_order == 'Y') { // 임직원 구매이면?>
							<p class="att">임직원 포인트</p>
							<dl class="pt-15">
								<dt>보유 포인트</dt>
								<dd class="fz-14 fw-bold"><?=number_format($staff_reserve)?>P</dd>
							</dl>
							<dl class="pt-15">
								<dt>사용예정 포인트</dt>
								<dd class="fz-14 fw-bold point-color">- <?=number_format($staff_pr_price)?>P</dd>
							</dl>
							<?}else if($cooper_order == 'Y'){?>
<!--
							<p class="att">제휴사 적립금</p>
							<dl class="pt-15">
								<dt>보유 적립금</dt>
								<dd class="fz-14 fw-bold"><?=number_format($cooper_reserve)?>P</dd>
							</dl>
							<dl class="pt-15">
								<dt>사용예정 적립금</dt>
								<dd class="fz-14 fw-bold point-color"><?=number_format($cooper_pr_price)?>P</dd>
							</dl>
-->
							<p class="att">총 적립예정 적립금</p>
							<dl class="pt-15">
								<dt>적립금</dt>
								<dd class="fz-14 fw-bold"><?=number_format($sum_product_reserve)?>P</dd>
							</dl>
							<?}else{?>
							<p class="att">총 적립예정 포인트</p>
							<dl class="pt-15">
								<dt>포인트</dt>
								<dd class="fz-14 fw-bold"><?=number_format($sum_product_reserve)?>P</dd>
							</dl>
							<?}?>

							<!--
							<dl class="pt-15">
								<dt>E포인트</dt>
								<dd class="fz-14 fw-bold"><?=number_format($sum_product_reserve)?>P</dd>
							</dl>-->
						</div>
					</section><!-- //결제금액 -->

					<div class="final-agree mt-10">
						<div class="checkbox">
							<input type="checkbox" id="dev_agree">
							<label for="dev_agree">동의합니다. (전자상거래법 제 8조 제 2항)</label>
						</div>
						<p>주문하실 상품,가격,배송정보,할인내역 등을 최종<br>확인하였으며,구매에 동의하시겠습니까?</p>
					</div>

					<div class="order-buy mt-10 button_open"><button type="button" class="btn-point w100-per" onclick="javascript:CheckForm()"><span>결제하기</span></button></div>
					<div class="order-buy mt-10 button_close" style="text-align:center; display:none; padding-top:30px;">
						========== 처리중 입니다 ==========
					</div>
					
					<div class="final-agree mt-10">
						고객님께 다양한 제품을 선보이고자 본사에 재고가 없는  <br>
						제품은 전국 오프라인 매장에서 발송을 진행하고 있습니다. <br>
						다만 오프라인 매장에서 발송하는 주문건은 매장재고의 변동으로 인해  
						발송지연 및 주문취소가 될 수 있는점 넓은 마음으로 <br> 양해를 부탁드립니다.
					</div>
					
				</div><!-- //.inner-confirm -->
			</div>


		</article><!-- //.cart-order-wrap -->

	</div>
</div><!-- //#contents -->

<!-- 주문 > 배송지목록 -->
<div class="layer-dimm-wrap popList delivery">
	<div class="layer-inner">
		<h2 class="layer-title">배송지 목록</h2>
		<button class="btn-close" type="button"><span>닫기</span></button>
		<div class="layer-content">
		<input type="hidden" name="dn_inr" id="dn_inr">
			<ul class="list">
<?
foreach( $dn_info as $dn_vkey=>$dn_val ){
	//exdebug($dn_val);
?>
				<li>
					<div class="radio ml-20">
						<input type="radio" name="my_deliveryList deli_check" id="deliver_list<?=$dn_vkey?>" onClick="javascript:Dn_InReceivercheck('<?=$dn_val->no.'|@|'.$dn_val->destination_name.'|@|'.$dn_val->get_name.'|@|'.addMobile($dn_val->mobile).'|@|'.$dn_val->postcode.'|@|'.$dn_val->postcode_new.'|@|'.$dn_val->addr1.'|@|'.$dn_val->addr2?>')">
						<label for="deliver_list<?=$dn_vkey?>"></label>
					</div>
					<div class="content w300">
						<p class="bold"><?=$dn_val->destination_name?></p>
						<p class="txt-toneB"><?=$dn_val->addr1?> <?=$dn_val->addr2?></p>
					</div>
				</li>
<?
}
?>
			</ul>
			<div class="btnPlace mt-10">
				<button class="btn-line  h-large" type="button" onclick="javascript:Dn_InReceiver('cancel')"><span>취소</span></button>
				<button class="btn-point h-large" type="button" onclick="javascript:Dn_InReceiver('in')"><span>적용</span></button>
			</div>

		</div><!-- //.layer-content -->
	</div>
</div><!-- //주문 > 배송지목록 -->

<!-- 주문 > 쿠폰목록 -->
<div class="layer-dimm-wrap popList coupon">
	<div class="layer-inner">
		<h2 class="layer-title">쿠폰 목록</h2>
		<button class="btn-close" type="button"><span>닫기</span></button>
		<div class="layer-content coupon_list">

		</div><!-- //.layer-content -->
	</div>
</div><!-- //주문 > 쿠폰목록 -->

<!-- 주문 > 매장안내 -->
<div class="layer-dimm-wrap pop-infoStore">
	<div class="layer-inner">
		<h2 class="layer-title">매장 위치정보</h2>
		<button class="btn-close" type="button"><span>닫기</span></button>
		<div class="layer-content store_view">

		</div><!-- //.layer-content -->
	</div>
</div><!-- //주문 > 매장안내 -->

