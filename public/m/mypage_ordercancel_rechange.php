	<section class="top_title_wrap">
		<h2 class="page_local">
			<a href="<?=$Dir.MDir?>mypage_orderlist_view.php?ordercode=<?=$ordercode?>" class="prev"></a>
			<span>교환 신청</span>
			<a href="<?=$Dir.MDir?>" class="home"></a>
		</h2>
	</section>
	<form name=form1 method=post action="<?=$_SERVER['PHP_SELF']?>">
	<input type=hidden name=re_type value="<?=$re_type?>">
	<input type=hidden name=ordercode value="<?=$ordercode?>">
	<input type=hidden name=idx value="<?=$idx?>">
	<input type=hidden name=idxs value="<?=$idxs?>">
	<input type=hidden name=paymethod value="<?=$_ord->paymethod[0]?>">
	<input type=hidden name=pc_type value="<?=$pc_type?>">
	<div class="mypage_sub">
		<p class="att-title">
			<label for="ord-num">주문날짜 : <? echo substr($ordercode,0,4)."-".substr($ordercode,4,2)."-".substr($ordercode,6,2); ?> <span class="code"><?=$ordercode?></span></label>
		</p>



<?php
		$rspan_cnt	= 1;
		$prd_cnt	= 0;
		$ven_cnt	= 1;
		$op_pro_price = 0;
		$op_sale_price = 0;
		$op_deli_price = 0;
		$op_refund_price = 0;
		foreach( $orProduct as $pr_idx=>$prVal ) { // 상품

			$storeData = getStoreData($prVal->store_code);

			//ERP 상품을 쇼핑몰에 업데이트한다.
			getUpErpProductUpdate($prVal->productcode);

			if ($pc_type == "PART") {
				//배송비로 인한 보여지는 가격 재조정
				$prVal->deli_price	= 0;
				list($od_deli_price, $product)=pmysql_fetch_array(pmysql_query("select deli_price, product from tblorder_delivery WHERE ordercode='".trim($ordercode)."' and product LIKE '%".$prVal->productcode."%'"));

				if ($od_deli_price) { //배송료 상세정보에 배송료가 있으면
					// 주문건 묶여있는 상품들중에 현재 주문상품을 제외한것중 1개를 가져온다.
					list($op_idx)=pmysql_fetch_array(pmysql_query("SELECT idx FROM tblorderproduct where ordercode='".trim($ordercode)."' and productcode in ('".str_replace(",","','", $product)."') and idx != '".$pr_idx."' and op_step < 40 limit 1"));
					if (!$op_idx) { // 상품이 없으면
						$prVal->deli_price	= $od_deli_price;
					}
				}
			}

			$op_pro_price	+= ($prVal->price + $prVal->option_price) * $prVal->option_quantity;
			$op_sale_price	+= $prVal->coupon_price + $prVal->use_point;
			$op_deli_price	+= $prVal->deli_price;
			$op_refund_price	+= (($prVal->price + $prVal->option_price) * $prVal->option_quantity) - ($prVal->coupon_price + $prVal->use_point) + $prVal->deli_price;

			$file = getProductImage($Dir.DataDir.'shopimages/product/', $prVal->tinyimage);

			$optStr	= "";
			$option1	 = $prVal->opt1_name;
			$option2	 = $prVal->opt2_name;
			$tmp_opt_price = $prVal->option_price * $prVal->quantity;

			$op_option1		= $prVal->opt1_name;
			$op_text_opt_s	= $prVal->text_opt_subject;

			if( strlen( trim( $prVal->opt1_name ) ) > 0 ) {
				$opt1_name_arr	= explode("@#", $prVal->opt1_name);
				$opt2_name_arr	= explode(chr(30), $prVal->opt2_name);
				for($g=0;$g < sizeof($opt1_name_arr);$g++) {
					if ($g > 0) $optStr	.= " / ";
					$optStr	.= '<span>'.$opt1_name_arr[$g].' : '.$opt2_name_arr[$g].'</span>';
				}
			}

			if( strlen( trim( $prVal->text_opt_subject ) ) > 0 ) {
				$text_opt_subject_arr	= explode("@#", $prVal->text_opt_subject);
				$text_opt_content_arr	= explode("@#", $prVal->text_opt_content);

				for($s=0;$s < sizeof($text_opt_subject_arr);$s++) {
					if ($text_opt_content_arr[$s]) {
						if ($optStr != '') $optStr	.= " / ";
						$optStr	.= '<span>'.$text_opt_subject_arr[$s].' : '.$text_opt_content_arr[$s].'</span>';
					}
				}
			}

			if( $tmp_opt_price > 0 ) $optStr	 .= '<span>&nbsp;( + '.number_format( $tmp_opt_price ).'원)</span>';
			if ($optStr !='') $optStr	 .= ' / ';
            $optStr	 .= '<span>수량 : '.number_format( $prVal->quantity )."개</span>";

			if ($rspan_cnt == 1) {
				$ven_cnt	= $orvender[$prVal->vender]['t_pro_count'];
				$rspan_cnt	= $orvender[$prVal->vender]['t_pro_count'];
			} else {
				$rspan_cnt--;
			}

			$vender_addr_info	= "";

			//입점업체 정보 관련
			if($prVal->vender>0) {
				$sql = "SELECT deli_info, re_addrinfo ";
				$sql.= "FROM tblvenderstore ";
				$sql.= "WHERE vender='{$prVal->vender}' ";
				$result=pmysql_query($sql,get_db_conn());
				if($_vdata=pmysql_fetch_object($result)) {
					$tempvdeli_info=explode("=", stripslashes($_vdata->deli_info));
					if ($_vdata->deli_info && $tempvdeli_info[0]=="Y") {
						$tempaddr_info=explode("|@|",$_vdata->re_addrinfo);
						$vender_addr_info	=  "(".$tempaddr_info[0].") ".$tempaddr_info[3];
					}
				}
				pmysql_free_result($result);
			}

?>
<?
				if ($ven_cnt == $rspan_cnt) {
?>
		<!-- 상품별 섹션 반복 -->
		<h3 class="pro_title"><?=$prVal->brandname?></h3>
		<section class="cart-list-wrap order">
			<ul class="list vender_product_list">
<?
				}
?>
				<!-- 상품 리스트 반복 -->
				<li class="vender_area">
					<div class="product_area">
						<div class="box_cart">
							<figure class="mypage_goods">
								<div class="img"><a href="<?=$Dir.FrontDir?>productdetail.php?productcode=<?=$prVal->productcode?>"><img src="<?=$file?>" alt=""></a></div>
								<figcaption>
									<input type="checkbox" class='hide'>
									<p class="brand">[<?=$prVal->brandname?>]</p>
									<p class="name"><?=$prVal->productname?></p>
									<p class="shipping"><?=$optStr?></p>
									<p class="price"><strong class="point-color"><?=number_format($prVal->price)?>원</strong></p>
									<?if($storeData['name'] && $prVal->delivery_type != '3'){	//2016-10-07 libe90 매장발송 정보표시?>
										<p style = 'color:blue;'>[<?=$arrDeliveryType[$prVal->delivery_type]?>] <?=$storeData['name']?></p>
										<?if($prVal->delivery_type == '1'){?>
											<p style = 'color:blue;'>예약일 : <?=$prVal->reservation_date?></p>
										<?}?>
									<?}else if($prVal->delivery_type == '3'){?>
										<p style = 'color:blue;'>[<?=$arrDeliveryType['3']?>] <?=$storeData['name']?></p>
										<?
											if ($_ord->receiver_addr) {
												$_ord_receiver_addr	= $_ord->receiver_addr;
												$_ord_receiver_addr	= str_replace("우편번호 :","[",$_ord_receiver_addr);
												$_ord_receiver_addr	= str_replace("주소 :","]",$_ord_receiver_addr);
											}
										?>
										<p style = 'color:blue;'>주소 : <?=$_ord_receiver_addr?></p>
									<?}?>
								</figcaption>
							</figure>
						</div>
					</div>
				</li>

<?
				if ($rspan_cnt == '1') {
?>
			</ul>
		</section><!-- //.cart-list-wrap -->
<?php
				}
				$prd_cnt	++;
		}
?>
<?
	if ($prVal->option1 !='' || $prVal->option2 != '') {
?>

		<div class="order_table">
			<h3>변경할 옵션</h3>
			<table class="my-th-left form_table">
				<colgroup>
					<col style="width:30%;">
					<col style="width:70%;">
				</colgroup>
				<tbody>
<?
		if ($prVal->option1) {
?>
<?
			$option1_arr	= explode("@#", $prVal->option1);
			$option1_tf_arr	= explode("@#", $prVal->option1_tf);
			$option1_cnt	= count($option1_arr);
			if ($prVal->option_type == '0') {							// 조합형
				//$option_arr		= get_option( $prVal->productcode );
			} else if ($prVal->option_type == '1') {					// 독립형
				$option_arr		= get_alone_option( $prVal->productcode );
			}

			for($s=0;$s < sizeof($option1_arr);$s++) {
				$sel_est			= "essential";
				$sel_est_text	= ' <span class="point-color">*</span>';
				if ($prVal->option_type == '1' && $option1_tf_arr[$s] == 'F') {// 독립형 옵션이 필수가 아닐경우
					$sel_est			= "";
					$sel_est_text	= "";
				}
?>
					<tr>
						<th><?=$option1_arr[$s]?><?=$sel_est_text?></th>
						<td>
<?
				if ($prVal->option_type == '0') {							// 조합형
?>
							<select name="sel_option<?=$s?>" class='select_def opt_chk opt_sel'<?if(($s + 1) != $option1_cnt) {?> onChange="javascript:option_change('<?=$prVal->productcode?>','<?=($s+1)?>', '<?=$option1_cnt?>', this.value)"<?}?> alt='<?=$sel_est?>'>
								<option value=''>============선택============</option>
<?
					if ($s == 0) {
						$option_arr		= get_option( $prVal->productcode );
					} else{
						$option_arr		= get_option( $prVal->productcode , $opt2_name_arr[$s-1], $s);
					}
						foreach($option_arr as $key => $val) {
							$disabled_on	= "";
							if ($val['price'] > 0) {
								$option_price		= "(+".number_format($val['price'])."원)";
							} else {
								$option_price		= "";
							}

							if($val['soldout'] == 1) {
								$disabled_on = ' disabled';
								$soldout = '&nbsp;[품절]';
							} else {
								$disabled_on = '';
								$soldout = '';
							}
?>
								<!--<option value="<?=$val['code']?>|!@#|<?=$val['price']?>"<?if($opt2_name_arr[$s] == $val['code']) {?> selected<?}?><?=$disabled_on?>><?=$val['code'].$option_price.$soldout?></option>-->
								<option value="<?=$val['code']?>|!@#|<?=$val['price']?>"<?=$disabled_on?>><?=$val['code'].$option_price.$soldout?></option>
<?
						}
					//}

?>

							</select>
<?

				} else if ($prVal->option_type == '1') {					// 독립형
?>
							<select name="sel_option<?=$s?>" class='select_def opt_chk opt_sel' alt='<?=$sel_est?>'>
								<option value=''>============선택============</option>
<?
					$oa_cnt	= 0;
					foreach($option_arr[$option1_arr[$s]] as $key => $val) {
						$option_code_arr		= explode( chr(30), $val->option_code);
						$option_code			= $option_code_arr[1];
						if ($val->option_price > 0) {
							$option_price		= " (+".number_format($val->option_price)."원)";
						} else {
							$option_price		= "";
						}
?>
								<!--<option value="<?=$option_code?>|!@#|<?=$val->option_price?>"<?if($opt2_name_arr[$s] == $option_code) {?> selected<?}?>><?=$option_code.$option_price?></option>-->
								<option value="<?=$option_code?>|!@#|<?=$val->option_price?>"><?=$option_code.$option_price?></option>
<?
						$oa_cnt++;
					}
?>

							</select>
<?
				}
?>
						</td>
					</tr>
<?
			}
?>

<?
		}
?>
<?

		if ($prVal->option2) {
?>
<?
			$option2_arr				= explode("@#", $prVal->option2);
			$option2_cnt				= count($option2_arr);

			$option2_tf_arr				= explode("@#", $prVal->option2_tf);
			$option2_maxlen_arr	= explode("@#", $prVal->option2_maxlen);

			$text_opt_content_arr	= explode("@#", $prVal->text_opt_content);

			for($s=0;$s < sizeof($option2_arr);$s++) {
				$sel_est			= "essential";
				$sel_est_text	= " *필수";
				if ($option2_tf_arr[$s] == 'F') {// 독립형 옵션이 필수가 아닐경우
					$sel_est			= "";
					$sel_est_text	= "";
				}
?>

					<tr>
						<th><?=$option2_arr[$s]?></th>
						<td><input name="text_option<?=$s?>" value="<?=$text_opt_content_arr[$s]?>" size="45" maxlength="<?=$option2_maxlen_arr[$s]?>" type="text" class="opt_chk opt_text w100-per" alt='<?=$sel_est?>'></td>
					</tr>
<?
			}
?>
<?
		}
?>
				</tbody>
			</table>
		</div>
<?
	}
?>
		<div class="order_table">
			<h3>교환사유 및 정보</h3>
			<table class="my-th-left form_table">
				<colgroup>
					<col style="width:30%;">
					<col style="width:70%;">
				</colgroup>
				<tbody>
					<tr>
						<th>교환사유 <span class="point-color">*</span></th>
						<td>
							<select class="select_def" name="c_sel_code" id="c_sel_code">
<?php
							$oc_reason_sub_code_html = "";
							$oc_reason_sub_code_html .= '<div class="mt-10 checkbox-set">';
							$oc_code_cnt = 0;
							foreach($exchange_oc_code as $key => $val) {
								if ($oc_code_cnt == 0) {
									$oc_code_sel	= " selected";
								} else {
									$oc_code_sel	= "";
								}
?>
								<option value="<?=$key?>"<?=$oc_code_sel?>><?=$val['name']?></option>
<?php
								$oc_code_cnt++;
								if($val['detail_code']) {
									$oc_reason_sub_code_html .= '
													<div class="mt-10 CLS_sel_sub_code chk_sub_code_'.$key.'" style="display:none;" ">
												';
									foreach($val['detail_code'] as $c2key => $c2val) {
										$oc_reason_sub_code_html	.= '
																						<input id="checkbox-'.$key.$c2key.'" class="c_sel_sub_code" type="checkbox" name="c_sel_sub_code" value="'.$c2key.'">
																						<label for="checkbox-'.$key.$c2key.'">'.$c2val.'</label>
																					';
									}
									$oc_reason_sub_code_html .= '</div>';
								}
			
							}
							$oc_reason_sub_code_html .= '</div>';
?>			
							</select>
							<?=$oc_reason_sub_code_html?>
						</td>
					</tr>
					<tr>
						<th>상세사유 <span class="point-color">*</span></th>
						<td><textarea name="memo" id="exchange-info" placeholder="교환에 대한 상세사유를 입력해 주시기 바랍니다."></textarea></td>
					</tr>
<?
					if ($vender_addr_info != '') {
?>
					<tr>
						<th>반송처 주소</th>
						<td><strong><?=$vender_addr_info?></strong></td>
					</tr>
					
<?
}
?>	
					<tr>
						<th>택배비 발송 <span class="point-color">*</span></th>
						<td>
							<select class="select_def" name="return_deli_type" id="return_deli_type">
							<option value="">선택하세요</option>
							<? 
							$oc_delivery_fee_type_cnt = 0;
							foreach($delivery_fee_type as $key => $val) {
 							?>
							<option value="<?=$key?>"><?=$val?></option>
							<?} ?>
							</select>
					</tr>
					<tr id="tr_return_deli_memo" style="display:none;">
						<th>입금자명 <span class="point-color">*</span></th>
						<td>
						<input type="text" id="return_deli_memo" name="return_deli_memo" style="width:70%" />
						</td>
					</tr>
					<input type="hidden" name="return_deli_price" id="return_deli_price" value=""  >
					<input type="hidden" name="return_deli_receipt" id="return_deli_receipt" title="택배비 수령" value=""></td>
					<input type="hidden" name="receipt_addr" id="receipt_addr" value="<?if($_ord->deli_type == "2"){ echo "해당 주문은 고객 [직접수령] 입니다"; } else { echo str_replace("주소 :","]",str_replace("우편번호 : ","[ ",$_ord->receiver_addr)); }?>">
				</tbody>
			</table>
		</div>

		<ul class="list_notice">
			<li>교환은 같은 옵션상품만 가능합니다. 다른 옵션의 상품으로 교환을 원하실 경우, 반품 후 재구매를 해주세요</li>
			<li>상품이 손상/훼손되었거나 이미 사용하셨다면 교환이 불가능합니다</li>
			<li>교환 사유가 구매자 사유일 경우 왕복 교환 배송비를 상품과 함께 박스에 동봉해 주세요</li>
			<li>교환 왕복 배송비가 동봉되지 않았을 경우 별도 입금 요청을 드릴 수 있습니다</li>
			<li>교환 사유가 판매자 사유일 경우 별도 배송비를 동봉하지 않으셔도 됩니다</li>
			<li>상품 확인 후 실제로 판매자 사유가 아닐 경우 별도 배송비 입금 요청을 드릴 수 있습니다</li>
		</ul>
		<button type="button" class="btn-point refundSubmit">신청</button>
	</div>
	<input type=hidden name=option1 value="<?=$op_option1?>">
	<input type=hidden name=text_opt_s value="<?=$op_text_opt_s?>">
	</form>
</div><!-- //.mypage-wrap -->
