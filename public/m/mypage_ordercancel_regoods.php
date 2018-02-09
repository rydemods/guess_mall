	<section class="top_title_wrap">
		<h2 class="page_local">
			<a href="<?=$Dir.MDir?>mypage_orderlist_view.php?ordercode=<?=$ordercode?>" class="prev"></a>
			<span>취소/반품 신청</span>
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
					<div class="pay-price">
						<section>
							<h4>배송비</h4>
							<div class="price total"><strong><?=number_format($orvender[$prVal->vender]['t_deli_price'])?></strong>원</div>
						</section>
						<section>
							<h4>주문금액</h4>
							<div class="price total"><strong><?=number_format($orvender[$prVal->vender]['t_pro_price'])?></strong>원</div>
						</section>
					</div>
				</section><!-- //.cart-list-wrap -->
<?php
				}
				$prd_cnt	++;
		}
?>
			</ul>
		</section><!-- //.cart-list-wrap -->

		<div class="order_table">
			<h3>환불예정 금액</h3>
			<table class="my-th-left">
				<colgroup>
					<col style="width:30%;">
					<col style="width:70%;">
				</colgroup>
				<tbody>
					<tr>
						<th>결제금액</th>
						<td><?=number_format($op_pro_price)?>원</td>
					</tr>
					<tr>
						<th>할인금액</th>
						<td><?=number_format($op_sale_price)?>원</td>
					</tr>
					<tr>
						<th>배송비</th>
						<td><?=number_format($op_deli_price)?>원</td>
					</tr>
					<tr>
						<th>환불금액</th>
						<td><strong class="point-color"><?=number_format($op_refund_price)?>원</strong></td>
					</tr>
				</tbody>
			</table>
		</div>
		<ul class="list_notice">
			<li>할인 금액, 배송비를 제외된 금액으로 환불됩니다</li>
			<li>결제 수단별 환불 방법과 환불 소요기간에 차이가 있습니다</li>
		</ul>
		<!-- button type="button" class="btn-line">자세히보기</button -->

		<div class="order_table">
			<h3>환불사유</h3>
			<table class="my-th-left form_table">
				<colgroup>
					<col style="width:30%;">
					<col style="width:70%;">
				</colgroup>
				<tbody>
					<tr>

<?php
							if($mode == "cancel"){
?>
							<th>환불사유 <span class="point-color">*</span></th>
							<td>
								<select class="select_def" name="b_sel_code" id="b_sel_code">
<?php
								$oc_reason_sub_code_html = "";
								$oc_reason_sub_code_html .= '<div class="mt-10 checkbox-set">';
								$oc_code_cnt = 0;
								foreach($cancel_oc_code as $key => $val) {
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
												<input id="checkbox-'.$key.$c2key.'" class="b_sel_sub_code" type="checkbox" name="b_sel_sub_code" value="'.$c2key.'">
												<label for="checkbox-'.$key.$c2key.'">'.$c2val.'</label>
																					';
										}
										$oc_reason_sub_code_html .= '</div>';
									}
								}
								$oc_reason_sub_code_html .= '</div>';
							}else if($mode == "regoods"){
?>
								<th>반품사유 <span class="point-color">*</span></th>
								<td>
									<select class="select_def" name="b_sel_code" id="b_sel_code">
<?php
								$oc_reason_sub_code_html = "";
								$oc_reason_sub_code_html .= '<div class="mt-10 checkbox-set">';
								$oc_code_cnt = 0;
								foreach($return_oc_code as $key => $val) {
									if ($oc_code_cnt == 0) {
										$oc_code_sel	= " selected";
									} else {
										$oc_code_sel	= "";
									}
?>
									<option value="<?=$key?>"<?=$oc_code_sel?>><?=$val['name']?></option>
<?
									$oc_code_cnt++;

									if($val['detail_code']) {
										$oc_reason_sub_code_html .= '
											<div class="mt-10 CLS_sel_sub_code chk_sub_code_'.$key.'" style="display:none;" ">
												';
										foreach($val['detail_code'] as $c2key => $c2val) {
											$oc_reason_sub_code_html	.= '
												<input id="checkbox-'.$key.$c2key.'" class="b_sel_sub_code" type="checkbox" name="b_sel_sub_code" value="'.$c2key.'">
												<label for="checkbox-'.$key.$c2key.'">'.$c2val.'</label>
																														';
										}
										$oc_reason_sub_code_html .= '</div>';
									}
								}
								$oc_reason_sub_code_html .= '</div>';
							}
						 	?>
							</select>
							<?=$oc_reason_sub_code_html ?>

							<!-- 20161018 반품사유에 따른 체크사항 -->
							<!--  
							<div class="mt-10 checkbox-set">
								<span>
									<input id="checkbox-51" class="chk_agree checkbox-def" type="checkbox" name="b_sel_sub_code" value="1">
									<label for="checkbox-51">갑피불량</label>
								</span>

								<span>
									<input id="checkbox-52" class="chk_agree checkbox-def" type="checkbox" name="b_sel_sub_code" value="2">
									<label for="checkbox-52">인솔불량</label>
								</span>

								<span>
									<input id="checkbox-53" class="chk_agree checkbox-def" type="checkbox" name="b_sel_sub_code" value="3">
									<label for="checkbox-53">재봉불량</label>
								</span>

								<span>
									<input id="checkbox-54" class="chk_agree checkbox-def" type="checkbox" name="b_sel_sub_code" value="4">
									<label for="checkbox-54">오염</label>
								</span>

								<span>
									<input id="checkbox-55" class="chk_agree checkbox-def" type="checkbox" name="b_sel_sub_code" value="5">
									<label for="checkbox-55">스크레치</label>
								</span>

								<span>
									<input id="checkbox-56" class="chk_agree checkbox-def" type="checkbox" name="b_sel_sub_code" value="6">
									<label for="checkbox-56">접착불량</label>
								</span>

								<span>
									<input id="checkbox-57" class="chk_agree checkbox-def" type="checkbox" name="b_sel_sub_code" value="7">
									<label for="checkbox-57">로고불량</label>
								</span>

								<span>
									<input id="checkbox-58" class="chk_agree checkbox-def" type="checkbox" name="b_sel_sub_code" value="8">
									<label for="checkbox-58">뒤축불량</label>
								</span>

								<span>
									<input id="checkbox-59" class="chk_agree checkbox-def" type="checkbox" name="b_sel_sub_code" value="9">
									<label for="checkbox-59">TAG없음</label>
								</span>

								<span>
									<input id="checkbox-510" class="chk_agree checkbox-def" type="checkbox" name="b_sel_sub_code" value="10">
									<label for="checkbox-510">기타</label>
								</span>
							</div>
							-->
							<!-- // 20161018 반품사유에 따른 체크사항 -->

						</td>
					</tr>
					<tr>
						<th>상세사유 <span class="point-color">*</span></th>
						<td><textarea name="memo" id="exchange-info" placeholder="취소/반품에 대한 상세사유를 입력해 주시기 바랍니다."></textarea></td>
					</tr>
					<tr>
						<th>환불방법 <span class="point-color">*</span></th>
<?
				if ($_ord->paymethod[0] == 'C') { // 카드결제일 경우
					$bank_class	= " hide";
?>
						<td><strong>신용카드 취소</strong></td>
<?
                } else if ($_ord->paymethod[0] == 'M') { // 휴대폰결제일 경우
					$bank_class	= " hide";
?>
						<td><strong>휴대폰결제 취소</strong></td>
<?
                } else if ($_ord->paymethod[0] == 'Y') { // PAYCO결제일 경우
					$bank_class	= " hide";
?>
						<td><strong>PAYCO결제 취소</strong></td>
<?php
				} else if ($_ord->paymethod[0] == 'V') { // 계좌이체결제일 경우
					$bank_class	= " hide";
?>
						<td><strong>계좌이체결제 취소</strong></td>
<?php
				} else if ($_ord->paymethod[0] == 'G') { // 임직원 포인트 결제일 경우
					$bank_class	= " hide";
?>
						<td><strong>임직원 포인트 환원</strong></td>
<?php
				} else {
					$bank_class	= "";
?>
						<td><strong>계좌입금</strong> (무통장/가상계좌 입금의 경우 계좌입금만 가능)</td>
<?
				}
?>
					</tr>
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

		<div class="order_table<?=$bank_class?>">
			<h3>환불계좌 정보</h3>
			<table class="my-th-left form_table">
				<colgroup>
					<col style="width:30%;">
					<col style="width:70%;">
				</colgroup>
				<tbody>
					<tr>
						<th>은행명 <span class="point-color">*</span></th>
						<td>
							<select class="select_def" name="bankcode">
<?php
							foreach($oc_bankcode as $key => $val) {
?>
								<option value='<?=$key?>'><?=$val?></option>
<?php
							}
?>
							</select>
						</td>
					</tr>
					<tr>
						<th>계좌번호 <span class="point-color">*</span></th>
						<td><input type="tel" class="" maxlength="20" placeholder="하이픈(-) 없이 입력" id="account-num" name="bankaccount"></td>
					</tr>
					<tr>
						<th>예금주 <span class="point-color">*</span></th>
						<td><input type="text" class="" maxlength="20" placeholder="이름" id="account-nm" name="bankuser"></td>
					</tr>
					<tr>
						<th>연락처 <span class="point-color">*</span></th>
						<td><input type="tel" class="" maxlength="20" placeholder="하이픈(-) 없이 입력" id="account-tel" name="bankusertel"></td>
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
				</tbody>
			</table>
		</div>

		<ul class="list_notice mt-5">
			<li>상품이 손상/훼손되었거나 이미 사용하셨다면 반품이 불가능합니다</li>
			<li>반품 사유가 단순 변심, 구매자 사유일 경우 반품 배송비를 상품과 함께 박스에 동봉해 주세요</li>
			<li>배송비가 동봉되지 않았을 경우 별도 입금 요청을 드릴 수 있습니다</li>
			<li>반품 사유가 상품 불량/파손, 배송 누락/오배송 등 판매자 사유일 경우 별도 배송비를 동봉하지 않으셔도 됩니다</li>
			<li>상품 확인 후 실제로 판매자 사유가 아닐 경우 별도 배송비 입금 요청을 드릴 수 있습니다</li>
		</ul>
		<button type="button" class="btn-point refundSubmit">신청</button>
	</div>
	<input type=hidden name=each_price value="<?=$op_refund_price?>">
	</form>
</div><!-- //.mypage-wrap -->
