<style>
	.CLS_store_layer_open{
		cursor:pointer;
	}
	.CLS_store_layer{
		display:none; position: absolute; background: #FFFFFF; padding: 8px; border: 1px solid #999; text-align:left;z-index:999;
	}
</style>

<script>

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
</script>
<div id="contents">
	<div class="mypage-page">

		<h2 class="page-title">주문/배송조회</h2>

		<div class="inner-align page-frm clear">

			<? include  "mypage_TEM01_left.php";  ?>
			<article class="my-content order-detail">
				<ul class="order-flow clear">
					<li><i><img src="/sinwon/web/static/img/icon/icon_my_order_ok.png" alt="주문접수"></i><p>01.주문접수</p></li>
					<li><i><img src="/sinwon/web/static/img/icon/icon_my_payment_ok.png" alt="결제완료"></i><p>02.결제완료</p></li>
					<li><i><img src="/sinwon/web/static/img/icon/icon_my_delivery_ready.png" alt="배송준비"></i><p>03.배송준비</p></li>
					<li><i><img src="/sinwon/web/static/img/icon/icon_my_delivery_ing.png" alt="배송중"></i><p>04.배송중</p></li>
					<li><i><img src="/sinwon/web/static/img/icon/icon_my_delivery_end.png" alt="배송완료"></i><p>05.배송완료</p></li>
				</ul>
				<form name=form1 method=post action="<?=$_SERVER['PHP_SELF']?>">
				<input type=hidden name=tempkey>
				<input type=hidden name=ordercode>
				<input type=hidden name=type>
				<input type=hidden name=ordercodeid value="<?=$ordercodeid?>">
				<input type=hidden name=ordername value="<?=$ordername?>">
				<input type=hidden name=refund_bankcode value="<?=$refund_bankcode?>">
				<input type=hidden name=refund_bankaccount value="<?=$refund_bankaccount?>">
				<input type=hidden name=refund_bankuser value="<?=$refund_bankuser?>">
				<input type=hidden name=refund_bankusertel value="<?=$refund_bankusertel?>">
				<section class="mt-50">
					<header class="my-title">
						<h3 class="fz-0">주문 목록</h3>
						<div class="count">전체 <strong><?=number_format(count($orProduct))?></strong></div>
						<p class="ord-no"><span class="fz-13">주문번호</span> : <?=$ordercode?><span class="fz-13 pl-30">주문날짜</span> : <?=substr($ordercode,0,4).".".substr($ordercode,4,2).".".substr($ordercode,6,2)?></p>
					</header>
					<table class="th-top">
						<caption>주문 목록</caption>
						<colgroup>
							<col style="width:auto">
							<col style="width:80px">
							<col style="width:120px">
							<col style="width:120px">
							<col style="width:105px">
							<col style="width:155px">
						</colgroup>
						<thead>
							<tr>
								<th scope="col">주문상품</th>
								<th scope="col">수량</th>
								<th scope="col">판매가</th>
								<th scope="col">배송정보</th>
								<th scope="col">상태</th>
								<th scope="col">취소/확정/리뷰</th>
							</tr>
						</thead>
						
						<tbody>
<?php
		$rspan_cnt	= 1;
		$ven_cnt	= 1;
		$can_cnt	= 0;
		$pr_idxs		= "";
		$op_cnt	= count($orProduct);
		$op_step_chk	= "";
		$op_step_cnt	= 0;
		$chkDeliveryType = true;
		//exdebug($orProduct);
		foreach( $orProduct as $pr_idx=>$prVal ) { // 상품

			$storeData = getStoreData($prVal->store_code);
			if($prVal->delivery_type == '3'){
				# 당일 배송이 있는지 체크
				# 당일 배송이 있을 경우 배송지 정보수정을 막기 위해.
				$chkDeliveryType = false;
			}

			if ($pr_idxs == '') {
				$pr_idxs		.= $pr_idx;
			} else {
				$pr_idxs		.= "|".$pr_idx;
			}

			if ($op_step_chk == "") {
				$op_step_chk = $prVal->op_step;
			} else {
				if ($op_step_chk != $prVal->op_step) {
					$op_step_cnt++;
				}
			}


			$file = getProductImage($Dir.DataDir.'shopimages/product/', $prVal->tinyimage);

			$optStr	= "";
			$option1	 = $prVal->opt1_name;
			$option2	 = $prVal->opt2_name;

			if($prVal->prodcode){
				$optStr	.= "품번 : ".$prVal->prodcode;
			}
			if($prVal->colorcode){
				$optStr	.= " / ";
				$optStr	.= "색상 : ".$prVal->colorcode;
			}

			if( strlen( trim( $prVal->opt1_name ) ) > 0 ) {
				$opt1_name_arr	= explode("@#", $prVal->opt1_name);
				$opt2_name_arr	= explode(chr(30), $prVal->opt2_name);
				$optStr	.= " / ";
				for($g=0;$g < sizeof($opt1_name_arr);$g++) {
					if ($g > 0) $optStr	.= " / ";
					$optStr	.= $opt1_name_arr[$g].' : '.$opt2_name_arr[$g];
				}
			}

			if( strlen( trim( $prVal->text_opt_subject ) ) > 0 ) {
				$text_opt_subject_arr	= explode("@#", $prVal->text_opt_subject);
				$text_opt_content_arr	= explode("@#", $prVal->text_opt_content);
				$optStr	.= " / ";
				for($s=0;$s < sizeof($text_opt_subject_arr);$s++) {
					if ($text_opt_content_arr[$s]) {
						if ($optStr != '') $optStr	.= " / ";
						$optStr	.= $text_opt_subject_arr[$s].' : '.$text_opt_content_arr[$s];
					}
				}
			}

			if ($rspan_cnt == 1) {
				$ven_cnt	= $orvender[$prVal->brand]['t_pro_count'];
				$rspan_cnt	= $orvender[$prVal->brand]['t_pro_count'];
			} else {
				$rspan_cnt--;
			}

			if ($ven_cnt == $rspan_cnt && $ven_cnt > 1) {
				$rowspan	= " rowspan='".$rspan_cnt."'";
			} else {
				$rowspan	= "";
			}

			//배송비로 인한 보여지는 가격 재조정
			$can_deli_price	= 0;
			$can_total_price	= (($prVal->price + $prVal->option_price) * $prVal->option_quantity) - ($prVal->coupon_price + $prVal->use_point + $prVal->use_epoint) + $prVal->deli_price;

			list($od_deli_price, $product)=pmysql_fetch_array(pmysql_query("select deli_price, product from tblorder_delivery WHERE ordercode='".trim($ordercode)."' and product LIKE '%".$prVal->productcode."%'"));
			//echo $od_deli_price;
			if ($od_deli_price) { //배송료 상세정보에 배송료가 있으면
				// 주문건 묶여있는 상품들중에 현재 주문상품을 제외한것중 1개를 가져온다.
				list($op_idx)=pmysql_fetch_array(pmysql_query("SELECT idx FROM tblorderproduct where ordercode='".trim($ordercode)."' and productcode in ('".str_replace(",","','", $product)."') and idx != '".$pr_idx."' and op_step < 40 limit 1"));
				//echo "SELECT idx FROM tblorderproduct where ordercode='".trim($ordercode)."' and productcode in ('".str_replace(",","','", $product)."') and idx != '".$pr_idx."' and op_step < 40 limit 1<br>";
				if ($op_idx) { // 상품이 있으면
					if ($prVal->deli_price > 0) $can_total_price	= $can_total_price - $od_deli_price;
				} else {
					$can_deli_price	= $od_deli_price;
				}
			}

			$pro_info	 = $prVal->productcode."!@#";
			$pro_info	.= substr($ordercode,0,4).".".substr($ordercode,4,2).".".substr($ordercode,6,2)."!@#";
			$pro_info	.= $file."!@#";
			$pro_info	.= $prVal->brandname."!@#";
			$pro_info	.= $prVal->productname."!@#";
			$pro_info	.= $optStr."!@#";
			$pro_info	.= $option1."!@#";
			$pro_info	.= $option2."!@#";
			$pro_info	.= $prVal->text_opt_subject."!@#";
			$pro_info	.= $prVal->text_opt_content."!@#";
			$pro_info	.= $prVal->option_price_text."!@#";
			$pro_info	.= $prVal->consumerprice."!@#";
			$pro_info	.= ($prVal->price + $prVal->option_price)."!@#";
			$pro_info	.= $prVal->deli_price."!@#";
			$pro_info	.= $prVal->coupon_price."!@#";
			$pro_info	.= $prVal->use_point."!@#";
			$pro_info	.= (($prVal->price + $prVal->option_price) * $prVal->option_quantity) - ($prVal->coupon_price + $prVal->use_point + $prVal->use_epoint) + $prVal->deli_price."!@#";
			$pro_info	.= $prVal->option_type."!@#";
			$pro_info	.= $prVal->option1_tf."!@#";
			$pro_info	.= $prVal->option2_tf."!@#";
			$pro_info	.= $prVal->option2_maxlen."!@#";

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
						$pro_info	.=  "(".$tempaddr_info[0].") ".$tempaddr_info[3]."!@#";
					} else {
						$pro_info	.=  "!@#";
					}
				} else {
					$pro_info	.=  "!@#";
				}
				pmysql_free_result($result);
			} else {
				$pro_info	.=  "!@#";
			}

            list($stock_yn) = pmysql_fetch("Select store_stock_yn from tblorderproduct Where idx = ".$pr_idx."");
            if($stock_yn == "N") $stock_status = "<br>(재고부족)";
            else $stock_status = "";

			$pro_info	.= $can_deli_price."!@#";
			$pro_info	.= $can_total_price."!@#";
			$pro_info	.= ($prVal->option_quantity)."!@#";
			$pro_info	.= $arrDeliveryType[$prVal->delivery_type]."!@#";
			$pro_info	.= $prVal->delivery_type."!@#";
			$pro_info	.= $prVal->reservation_date."!@#";
			$pro_info	.= $storeData['name']."!@#";
			$pro_info	.= $prVal->use_epoint;

			

?>
							<tr id="idx_<?=$pr_idx?>" class="bold"  info = "<?=$pro_info?>">
								<td class="pl-5">
									<div class="goods-in-td">
										<div class="thumb-img"><a href="<?=$Dir.FrontDir?>productdetail.php?productcode=<?=$prVal->productcode?>"><img src="<?=$file?>" alt="<?=$prVal->productname?>"></a></div>
										<div class="info">
											<p class="brand-nm"><?=$prVal->brandname?></p>
											<p class="goods-nm"><?=$prVal->productname?></p>
											<p class="opt"><?=$optStr?><?if ($prVal->option_price > 0) {?>(+ <?=number_format($prVal->option_price)?>원)<?}?></p>
										</div>
									</div>
								</td>
								<td class="txt-toneB"><?=number_format($prVal->option_quantity)?></td>
								<td class="txt-toneA fw-bold">\ <?=number_format($prVal->price)?></td>
								<td class="flexible-delivery">

								<?if($prVal->delivery_type){?>
									<strong class="txt-toneA">[<?=$arrDeliveryType[$prVal->delivery_type]?>]</strong><br>
									<?if($prVal->delivery_type == '1'){?>
									<strong class="txt-toneA">예약일 : <?=$prVal->reservation_date?></strong><div class="pt-5"><?=$storeData['name']?></div>
									<?if($prVal->store_code){?><button class="btn-basic h-small mt-5 btn-infoStore" onclick="javascript:store_map('<?=$prVal->store_code?>')" type="button"><span>매장안내</span></button><?}?>
									<?}else if($prVal->delivery_type == '3'){?>
									<strong class="txt-toneA">\<?=number_format($prVal->deli_price)?></strong><div class="pt-5"><?=$storeData['name']?></div>
									<?}?>
								<?}else{ ?>
									<strong class="txt-toneA">[<?=$arrDeliveryType[$prVal->delivery_type]?>]</strong>
								<?}?>
								
								<!-- 20170704 택배발송 & 매장발송 통일로 택배발송  -->
								<!-- 
								<?if($prVal->delivery_type){?>
									<strong class="txt-toneA">[<?=$arrDeliveryType[$prVal->delivery_type]?>]</strong><br>
									<?if($prVal->delivery_type == '1'){?>
									<strong class="txt-toneA">예약일 : <?=$prVal->reservation_date?></strong><div class="pt-5"><?=$storeData['name']?></div>
									<?}else if($prVal->delivery_type == '3'){?>
									<strong class="txt-toneA">\<?=number_format($prVal->deli_price)?></strong><div class="pt-5"><?=$storeData['name']?></div>
									<?}?>
									<?if($prVal->store_code){?><button class="btn-basic h-small mt-5 btn-infoStore" onclick="javascript:store_map('<?=$prVal->store_code?>')" type="button"><span>매장안내</span></button><?}?>
								<?}else{ ?>
									<strong class="txt-toneA">[<?=$arrDeliveryType[$prVal->delivery_type]?>]</strong>
								<?}?>
								-->
								<!-- 20170807 택배발송 매장발송 분리 수정
								<?if($prVal->delivery_type == 0 || $prVal->delivery_type == 2){?>
									<strong class="txt-toneA">[택배발송]</strong>
								<?}else{ ?>
									<strong class="txt-toneA">[<?=$arrDeliveryType[$prVal->delivery_type]?>]</strong><br>
									<?if($prVal->delivery_type == '1'){?>
									<strong class="txt-toneA">예약일 : <?=$prVal->reservation_date?></strong><div class="pt-5"><?=$storeData['name']?></div>
									<?if($prVal->store_code){?><button class="btn-basic h-small mt-5 btn-infoStore" onclick="javascript:store_map('<?=$prVal->store_code?>')" type="button"><span>매장안내</span></button><?}?>
									<?}?>
								<?}?>
								-->
								</td>
								<td class="txt-toneA fz-13 fw-bold">
								<?
									$_ord_oi_step1	= $_ord->oi_step1;
									if($prVal->op_step=="3" && $prVal->deli_closed){
										$status_name="배송완료";
									}else{
										$status_name=GetStatusOrder("p", $_ord_oi_step1, $_ord->oi_step2, $prVal->op_step, $prVal->redelivery_type, $prVal->order_conf);
									}


									$status_qry="";
									if($status_name=="환불접수" || $status_name=="환불완료"){
										list($status_sold)=pmysql_fetch("select count(*) from tblorderproduct_log where ordercode='".$ordercode."' and idx='".$pr_idx."' and step_next in ('41','44') and reg_type in ('api','admin')");

									}
									if ($_ord->oi_step1 > 2 && $prVal->op_step >=40 && $prVal->deli_num =='') $_ord_oi_step1 =2;
								?>
								<?=$status_name?></span><?if($status_sold){echo "(품절)";}?><br>
								<?
									 if( $prVal->op_step == 3 ){ // 배송중일 경우
									?>
										<button class="btn-line h-small mt-10 CLS_delivery_tracking" type="button" urls = "<?=$delicomlist[$prVal->deli_com]->deli_url.$prVal->deli_num?>"><span>배송추적</span></button>
									<?
									}
								?>
								</td>
								<td>
									<div class="refund-btnGroup">
									<? if ($prVal->op_step < 40 && $_ord->paymethod[0]!='Q' ) { //주문취소 신청및 완료상태가 아닌경우
//										if( $prVal->op_step == 1/* || $prVal->op_step == 2 */){ // 입금완료, 배송 준비중일 경우
										if( $prVal->op_step == 1 || ($prVal->op_step == 2 && $prVal->store_code == '')){ // 입금완료, 매장입찰 배송준비중 추가 20180201
											if ($op_cnt == 1 || ( $_ord->paymethod[0] == "M" && $_ord->paymethod[1] == "E" ) ) {
												// 주문상품이 한개일경우 전체 취소로 한다.
												// 또는 다날 휴대폰 결제인 경우도 전체 취소로 한다.
												echo "-";
											} else {
								?>
										<button class="btn-basic cancel btn-deliveryRefund w100-per ord_cancel" type="button" ordercode = "<?=$ordercode?>" pg_ordercode = "<?=$pg_ordercode?>" idx = "<?=$pr_idx?>" pc_type="PART" paymethod="<?=$_ord->paymethod[0]?>"><span>주문취소</span></button>
								<?
											}
										} else if( $prVal->op_step == 3 ){ // 배송중일 경우
								?>
										<button class="btn-basic btn-deliveryRefund ord_regoods" type="button" ordercode = "<?=$ordercode?>" pg_ordercode = "<?=$pg_ordercode?>" idx = "<?=$pr_idx?>" pc_type="PART" paymethod="<?=$_ord->paymethod[0]?>"><span>반품</span></button>
										<button class="btn-line ml-5 btn-deliveryExchange ord_change" type="button" ordercode = "<?=$ordercode?>" pg_ordercode = "<?=$pg_ordercode?>" idx = "<?=$pr_idx?>" pc_type="PART" paymethod="<?=$_ord->paymethod[0]?>"><span>교환</span></button>
										<button class="btn-point w100-per deli_ok" type="button" ordercode = "<?=$ordercode?>" pg_ordercode = "<?=$pg_ordercode?>" idx = "<?=$pr_idx?>" pc_type="PART" paymethod="<?=$_ord->paymethod[0]?>"><span>구매확정</span></button>
								<?
										} else if(  $prVal->op_step == 4) { //배송완료일 경우
											//if ($prVal->order_conf =='1') { // 구매확정인 경우
								?>
											<a href="<?=$Dir.FrontDir?>productdetail.php?productcode=<?=$prVal->productcode?>"><button class="btn-point w100-per" type="button"><span>리뷰작성</span></button></a>
								<?
										} else {
											echo "-";
										}
									} else if($_ord->paymethod[0]!='Q') {
                                        if($prVal->op_step == "40" && $_ord->oi_step1 == "3") {
                                            //echo "-"."/".$_ord->oi_step1."/".$_ord->oi_step2."/".$prVal->op_step."/".$prVal->redelivery_type."/".$prVal->order_conf;
                                ?>
                                            <button class="btn-point w100-per ord_req_cancel" type="button" ordercode = "<?=$ordercode?>" pg_ordercode = "<?=$pg_ordercode?>" idx = "<?=$pr_idx?>" oc_no ="<?=$prVal->oc_no?>"><span>신청철회</span></button>
                                <?
                                        } else {
                                            echo "-";
                                        }
									} else if( $_ord->paymethod[0]=='Q' && $prVal->op_step == 4) { //배송완료일 경우
										
											//if ($prVal->order_conf =='1') { // 구매확정인 경우
								?>
											<a href="<?=$Dir.FrontDir?>productdetail.php?productcode=<?=$prVal->productcode?>"><button class="btn-point w100-per" type="button"><span>리뷰작성</span></button></a>
								<?
										
									}else{
										echo "-";
									}
								?>
									
										
									</div>
								</td>
							</tr>
<?php
				if ($prVal->op_step >= 40) $can_cnt++;
		}
?>

						</tbody>

						<tfoot>
							<tr>
								<td colspan="6" class="reset">
									<div class="cart-total-price clear">
										<dl>
											<dt>상품합계</dt>
											<dd>\ <?=number_format($_ord->price)?></dd>
										</dl>
										<span class="txt point-color">-</span>
										<dl>
											<dt>할인</dt>
											<dd>\ <?=number_format($_ord->dc_price + $_ord->reserve + $_ord->point)?></dd>
										</dl>
										<span class="txt">+</span>
										<dl>
											<dt>배송비</dt>
											<dd>\ <?=number_format($_ord->deli_price)?></dd>
										</dl>
										<dl class="sum">
											<dt>합계</dt>
											<dd class="point-color fz-18">\ <?=number_format($_ord->price-$_ord->dc_price-$_ord->reserve - $_ord->point +$_ord->deli_price)?></dd>
										</dl>
									</div>
								</td>
							</tr>
						</tfoot>
					</table>
<br/><span class="point-color fz-15">
※ 매장발송 주문건의 경우 매장재고의 변동으로 인해 발송지연 및 주문취소가 될수 있는 점 넓은 마음으로 양해를 부탁드립니다.
</span>
					<div class="btnPlace mt-40">
<?

//	20180201 매장입찰 배송준비중 추가
//			if (($_ord->oi_step1 < 2 || $_ord->oi_step1 == 3) && $can_cnt == 0 && $op_step_cnt == 0) {
			if (($_ord->oi_step1 < 2 || $_ord->oi_step1 == 3 || ($_ord->oi_step1 == 2  && $prVal->store_code=='')) && $can_cnt == 0 && $op_step_cnt == 0) {
				if ($_ord->oi_step1 == 0) {
					$add_text			= "취소";
					$add_class		= " ord_receive_cancel";
				} else {
					$add_text			= "환불";
					$add_class		= " ord_cancel";
				}

//				if(($_ord->paymethod[0]=='Q' && !$step_count) || ($_ord->paymethod[0]!='Q' && $_ord->oi_step1 < 2)){
				if(($_ord->paymethod[0]=='Q' && !$step_count) || ($_ord->paymethod[0]!='Q' && $_ord->oi_step1 < 2 || ($_ord->paymethod[0]!='Q' && ($_ord->oi_step1 == 2 && $prVal->store_code=='')))){
?>
						<button 
							class="btn-line h-large w200 btn-line<?=$add_class?>" 
							ordercode = "<?=$ordercode?>" 
							pg_ordercode = "<?=$pg_ordercode?>" 
							idxs = "<?=$pr_idxs?>" 
							pc_type="ALL" 
							paymethod="<?=$_ord->paymethod[0]?>" 
							price="<?=number_format($_ord->price)?>" 
							dcprice="<?=number_format($_ord->dc_price-$_ord->reserve )?>" 
							deliprice="<?=number_format($_ord->point +$_ord->deli_price)?>" 
							productname="<?=$prVal->productname?>" 
							option="<?=$optStr?>" 
							quantity="<?=number_format($prVal->option_quantity)?>" 
							prodcutcode="<?=$prVal->productcode?>" 
							type="button"><span>전체 주문 취소</span></button>
							
<?				}
			}
?>
						<a class="btn-point h-large w200" href="javascript:history.back();"><span>목록</span></a>
					</div>

				</section>
				</form>
				<div class="order-infoDetail clear mt-60">
					<section class="inner-payment">
						<header class="my-title">
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
									<th scope="row"><label>총 주문금액</label></th>
									<td>\ <?=number_format($_ord->price)?></td>
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
									<td class="point-color"><?=($t_product_sale > 0)?"- \ ".number_format($t_product_sale)."원":"없음"?></td>
								</tr>
								<tr>
									<th scope="row"><label>배송비</label></th>
									<td><?=($_ord->deli_price > 0)?"\ ".number_format($_ord->deli_price)."원":"무료"?></td>
								</tr>
								<tr>
									<th scope="row"><label>실 결제금액</label></th>
									<td class="fz-14 fw-bold point-color">\ <?=number_format($_ord->price-$_ord->dc_price-$_ord->reserve-$_ord->point+$_ord->deli_price)?></td>
								</tr>
<?
								if(strstr("VCPMY", $_ord->paymethod[0])) {
									$subject = "승인일자";
									$o_year = substr($ordercode, 0, 4);
									$o_month = substr($ordercode, 4, 2);
									$o_day = substr($ordercode, 6, 2);
									$o_hour = substr($ordercode, 8, 2);
									$o_min = substr($ordercode, 10, 2);
									$o_sec = substr($ordercode, 12, 2);

									$msg = $o_year.".".$o_month.".".$o_day." ".$o_hour.":".$o_min.":".$o_sec;
								} else if (strstr("BOQ", $_ord->paymethod[0])) {
									$_ord_pay_data = explode(" ", $_ord->pay_data);
									if ($_ord->bank_date >= 14) {
										$o_year = substr($_ord->bank_date, 0, 4);
										$o_month = substr($_ord->bank_date, 4, 2);
										$o_day = substr($_ord->bank_date, 6, 2);
										$o_hour = substr($_ord->bank_date, 8, 2);
										$o_min = substr($_ord->bank_date, 10, 2);
										$o_sec = substr($_ord->bank_date, 12, 2);

										$bank_date_msg = $o_year."-".$o_month."-".$o_day." ".$o_hour.":".$o_min.":".$o_sec;
									}
									if(strstr("B", $_ord->paymethod[0])){
										$subject=$_ord->pay_data;
										/*
										$subject = "입금자명";
										$subject2 = "입금은행";
										$subject3 = "입금계좌";
										$msg = $_ord->bank_sender;
										$msg2 = $_ord_pay_data[0];
										$msg3 = $_ord_pay_data[1].' '.$_ord_pay_data[2];
										if ($bank_date_msg) {
											$subject4	= "입금확인";
											$msg4		= $bank_date_msg;
										}*/
									}else{
										$subject = "입금은행";
										$subject2 = "입금계좌";
										$msg = $_ord_pay_data[0];
										$msg2 = $_ord_pay_data[1].' '.$_ord_pay_data[2];
										if ($bank_date_msg) {
											$subject3	= "입금확인";
											$msg3		= $bank_date_msg;
										} else {
											if($_ord->pay_flag=="0000"){
												$subject3 = "입금확인";
												$msg3 = "입금 대기중";
											}
										}
									}
								}
?>
								<tr>
									<th scope="row"><label>결제방법</label></th>
									<td class="fz-13">
										<?if($_ord->paymethod[0]!="B"){echo $arpm[$_ord->paymethod[0]]."<br>";}?>
										<?if($subject){?>
										<?=$subject?>: <?=$msg?>
										<?}?>
										<?if($subject2){?>
										<br> <?=$subject2?>: <?=$msg2?>
										<?}?>
										<?if($subject3){?>
										<br> <?=$subject3?>: <?=$msg3?>
										<?}?>
										<?if($subject4){?>
										<br> <?=$subject4?>: <?=$msg4?>
										<?}?>									
									</td>
								</tr>

							</tbody>
						</table>
					</section><!-- //.inner-payment -->
					<section class="inner-delivery">
						<header class="my-title">
							<h3 class="d-iblock">배송지 정보</h3>
							<!--<button class="btn-line btn-pop-address" id="delivery-change" type="button"><span>배송지 변경</span></button>-->
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
											<?if($_ord->deli_type == "2"){
												echo "<li>해당 주문은 고객 [직접수령] 입니다</li>"; 
											} else { 
												echo "<li>".str_replace("주소 :","]",str_replace("우편번호 : ","[ ",$_ord->receiver_addr))."</li>"; 
											}?>
										</ul>
									</td>
								</tr>
								<tr>
									<th scope="row"><label>배송 요청사항</label></th>
									<td><?=$_ord->order_msg2?$_ord->order_msg2:"-"?></td>
								</tr>
							</tbody>
						</table>
					</section><!-- //.inner-delivery -->
				</div>

			</article><!-- //.my-content -->
		</div><!-- //.page-frm -->

	</div>
</div><!-- //#contents -->


<!-- 마이페이지 > 주문상세 > 반품신청 -->
<div class="layer-dimm-wrap popDelivery-return refund">
	<div class="layer-inner">
		<h2 class="layer-title">환불/반품신청</h2>
		<button class="btn-close" type="button"><span>닫기</span></button>
		<div class="layer-content">
			
			<table class="th-top" id="rg_list">
				<caption>주문 목록</caption>
				<colgroup>
					<col style="width:100px">
					<col style="width:auto">
					<col style="width:80px">
					<col style="width:110px">
					<col style="width:105px">
					<col style="width:95px">
				</colgroup>
				<thead>
					<tr>
						<th scope="col">주문일</th>
						<th scope="col">상품정보</th>
						<th scope="col">수량</th>
						<th scope="col">판매가</th>
						<th scope="col">배송정보</th>
						<th scope="col">할인금액</th>
					</tr>
				</thead>
				<tbody></tbody>
				<tfoot></tfoot>
			</table>

			<div class="refund-frm clear">
				<ul class="comment">
					<li>* 할인금액, 배송비를 제외한 금액으로 환불됩니다.</li>
					<li>* 결제 수단별 환불방법과 환불소요기간에 차이가 있습니다. </li>
				</ul>
				<div class="reason">
					<table class="th-left">
						<caption>환불/반품사유 작성</caption>
						<colgroup>
							<col style="width:120px">
							<col style="width:auto">
						</colgroup>
						<tbody>
							<tr id="tr_refund">
								<th scope="row"><label for="refund_reason" class="essential">환불사유</label></th>
								<td>
									<div class="input-cover">
										<div class="select">
											<select name="b_sel_code" id="refund_reason" style="width:190px" title="반품사유 선택" class="tab-select">
												<option value="">선택</option>
<?php
										$oc_reason_sub_code_html = "";
										$oc_reason_sub_code_html .= '<div class="mt-10 checkbox-set">';
										foreach($cancel_oc_code as $key => $val) {
?>
											<option value="<?=$key?>"><?=$val['name']?></option>
<?
											if($val['detail_code']) {
												$oc_rsc_addClass	= " style='display:none'";
												$oc_reason_sub_code_html .= '
													<div class="mt-10 CLS_sel_sub_code chk_sub_code_'.$key.'" '.$oc_rsc_addClass.'>
												';
												foreach($val['detail_code'] as $c2key => $c2val) {
													$oc_reason_sub_code_html	.= '<div>
																						<input id="checkbox-'.$key.$c2key.'" class="b_sel_sub_code" type="checkbox" name="b_sel_sub_code" value="'.$c2key.'">
																						<label for="checkbox-'.$key.$c2key.'">'.$c2val.'</label>
																					</div>
																					';
												}
												$oc_reason_sub_code_html .= '</div>';
											}
										}
										$oc_reason_sub_code_html .= '</div>';
?>
											</select>

										</div>
									</div>
									<?=$oc_reason_sub_code_html?>
								</td>
							</tr>

							<tr id="tr_return">
								<th scope="row"><label for="refund_reason" class="essential">반품사유</label></th>
								<td>
									<div class="input-cover">
										<div class="select">
											<select name="b_sel_code2" id="refund_reason" style="width:190px" title="반품사유 선택" class="tab-select">
												<option value="">선택</option>
<?php
										$oc_reason_sub_code_html = "";
										$oc_reason_sub_code_html .= '<div class="mt-10 checkbox-set">';
										foreach($return_oc_code as $key => $val) {
?>
											<option value="<?=$key?>"><?=$val['name']?></option>
<?
											if($val['detail_code']) {
												$oc_rsc_addClass	= " style='display:none'";
												$oc_reason_sub_code_html .= '
													<div class="mt-10 CLS_sel_sub_code chk_sub_code_'.$key.'" '.$oc_rsc_addClass.'>
												';
												foreach($val['detail_code'] as $c2key => $c2val) {
													$oc_reason_sub_code_html	.= '<div>
																						<input id="checkbox-'.$key.$c2key.'" class="b_sel_sub_code" type="checkbox" name="b_sel_sub_code" value="'.$c2key.'">
																						<label for="checkbox-'.$key.$c2key.'">'.$c2val.'</label>
																					</div>
																					';
												}
												$oc_reason_sub_code_html .= '</div>';
											}
										}
										$oc_reason_sub_code_html .= '</div>';
?>
											</select>
											
										</div>
									</div>
									<?=$oc_reason_sub_code_html?>
								</td>
							</tr>
							<tr>
								<th scope="row"><label for="detail_reason2" class="essential">상세사유</label></th>
								<td>
									<textarea id="detail_reason2" name="memo" class="w100-per" style="height:79px" title="상세사유 입력" placeholder=""></textarea>
								</td>
							</tr>
<?
				if ($_ord->paymethod[0] == 'C') { // 카드결제일 경우
					$refund_text	= "신용카드 취소";
					$account_disabled	= " disabled";
				} else if ($_ord->paymethod[0] == 'M') { // 휴대폰결제일 경우
					$refund_text	= "휴대폰결제 취소";
					$account_disabled	= " disabled";
				} else if ($_ord->paymethod[0] == 'Y') { // 페이코결제일 경우
					$refund_text	= "PAYCO결제 취소";
					$account_disabled	= " disabled";
				} else if ($_ord->paymethod[0] == 'V') { // 계좌이체결제일 경우
					$refund_text	= "계좌이체결제 취소";
					$account_disabled	= " disabled";
				} else if ($_ord->paymethod[0] == 'G') { // 임직원 포인트결제일 경우
					$refund_text	= "임직원 포인트 환원";
					$account_disabled	= " disabled";
				} else {
					$refund_text	= "계좌입금(가상계좌 입금의 경우는 계좌입금만 가능)";
					$account_disabled	= "";
				}
?>
							<tr>
								<th scope="row"><label class="essential">환불방법</label></th>
								<td><span class='refund-way'><?=$refund_text?></span><span class='refund-way2 hide'>계좌입금(가상계좌 입금의 경우는 계좌입금만 가능)</span></td>
							</tr>
						</tbody>
					</table>
				</div><!-- //.reason -->


				<div class="account account-info">
					<table class="th-left">
						<caption>계좌정보 작성</caption>
						<colgroup>
							<col style="width:120px">
							<col style="width:auto">
						</colgroup>
						<tbody>
							<tr>
								<th scope="row"><label for="refund_bank" class="essential">은행명</label></th>
								<td>
									<div class="input-cover">
										<div class="select">
											<select name="bankcode" id="refund_bank" style="width:190px" title="은행명 선택" <?=$account_disabled?>>
												<option value=''>선택</option>
<?php
										foreach($oc_bankcode as $key => $val) {
?>
											<option value="<?=$key?>"><?=$val?></option>
<?php
										}
?>
											</select>
										</div>
									</div>
								</td>
							</tr>
							<tr>
								<th scope="row"><label for="refund_account" class="essential">계좌번호</label></th>
								<td>
									<div class="input-cover"><input type="text" class='chk_only_number' id="account-number" name="bankaccount" maxlength="20" class="w100-per" title="환불받을 계좌번호 입력" placeholder="하이픈(-) 없이 입력" style="ime-mode:disabled;"<?=$account_disabled?>></div>
								</td>
							</tr>
							<tr>
								<th scope="row"><label for="refund_account_name" class="essential">예금주</label></th>
								<td>
									<div class="input-cover"><input type="text" class="w100-per" id="account-name" name="bankuser" maxlength="20" title="환불받을 계좌 예금주" placeholder="이름"<?=$account_disabled?>></div>
								</td>
							</tr>
							<tr>
								<th scope="row"><label for="refund_account_tel" class="essential">연락처</label></th>
								<td>
									<div class="input-cover"><input type="text" class="w100-per" class='chk_only_number' id="account-tel" name="bankusertel" maxlength="20" title="환불받는 분 연락처" placeholder="하이픈(-) 없이 입력" style="ime-mode:disabled;"<?=$account_disabled?>></div>
								</td>
							</tr>
						</tbody>
					</table>
				</div><!-- //.account -->
			</div><!-- //.refund-frm -->
			

			<dl class="return-delivery-price" id="parcel_pay">
				<dt>택배비 발송</dt>
				<?
				$oc_delivery_fee_type_cnt = 0;
				foreach($delivery_fee_type as $key => $val) {
 				?>
					<?if($key  == "3"){ ?>
					<dd>
						<div class="radio">
							<input type="radio" id="radio-delivery-fee<?=$key?>" value="<?=$key ?>" name="return_deli_type">
							<label for="radio-delivery-fee<?=$key?>"><?=$val ?></label>
						</div>
						<div class="input-cover d-iblock ml-10"><input type="text" title="입금자명 입력" name="return_deli_memo" id="return_deli_memo" placeholder="입금자명"></div>
					</dd>
					<?}else{ ?>
					<dd>
						<div class="radio">
							<input type="radio" id="radio-delivery-fee<?=$key?>" value="<?=$key ?>" name="return_deli_type">
							<label for="radio-delivery-fee<?=$key?>"><?=$val ?></label>
						</div>
					</dd>
					<?} ?>

				<?} ?>
				
			</dl>
			<input type=hidden name="return_deli_price" id="return_deli_price" value=""  >
			<input type="hidden" name="return_deli_receipt" id="return_deli_receipt" title="택배비 수령" value=""></td>
			<input type="hidden" name="receiver_tel1" id="receiver_tel1" value="<?=$_ord->receiver_tel1?>">

			<dl class="attention-box mt-20">
				<dt>유의사항</dt>
				<dd>상품이 손상/훼손 되었거나 이미 사용하셨다면 반품이 불가능합니다</dd>
				<dd>반품 사유가 단순변심, 구매자 사유일 경우반품 배송비를 상품과 함께 박스에 동봉해 주세요</dd>
				<dd>배송비가 동봉되지 않았을 경우 별도 입금 요청을 드릴 수 있습니다</dd>
				<dd>반품 사유가 상품불량/파손, 배송누락/오배송 등 판매자 사유일 경우 별도 배송비를 동봉하지 않으셔도 됩니다 </dd>
				<dd>상품 확인 후 실제로 판매자 사유가 아닐 경우 별도 배송비 입금 요청을 드릴 수 있습니다</dd>
				<dd>가상계좌로 결제하신 경우에는 환불이 영업일 기준으로 1~2일정도 소요될 수 있습니다.</dd>
			</dl>

			<div class="btnPlace mt-20 mb-40 button_open">
				<!--<button class="btn-line h-large" type="button"><span>취소</span></button>-->
				<button class="btn-point h-large refundSubmit" type="button"><span>신청</span></button>
			</div>

			<div class="mt-40 mb-40 button_close" style="text-align:center; display:none;">
				========== 처리중 입니다 ==========
			</div>

		</div><!-- //.layer-content -->
	</div>
</div><!-- //마이페이지 > 주문상세 > 반품신청 -->



<!-- 마이페이지 > 주문상세 > 교환신청 -->
<div class="layer-dimm-wrap popDelivery-return exchange">
	<div class="layer-inner">
		<h2 class="layer-title">교환신청</h2>
		<button class="btn-close" type="button"><span>닫기</span></button>
		<div class="layer-content">
			
			<table class="th-top"  id="cg_list">
				<caption>주문 목록</caption>
				<colgroup>
					<col style="width:105px">
					<col style="width:auto">
					<col style="width:80px">
					<col style="width:180px">
					<col style="width:110px">
				</colgroup>
				<thead>
					<tr>
						<th scope="col">주문일</th>
						<th scope="col">상품정보</th>
						<th scope="col">수량</th>
						<th scope="col">변경할 옵션</th>
						<th scope="col">판매가</th>
					</tr>
				</thead>
				
				<tbody></tbody>
				<tfoot></tfoot>
			</table>

			<table class="th-left mt-40 exchange-reason-info">
				<caption>교환사유 작성</caption>
				<colgroup>
					<col style="width:120px">
					<col style="width:auto">
				</colgroup>
				<tbody>
					<tr>
						<th scope="row"><label for="exchane_reason" class="essential">교환사유</label></th>
						<td>
							<div class="input-cover">
								<div class="select">
									<select name="c_sel_code" id="exchane_reason" style="width:190px" title="교환사유 선택" class="tab-select">
										<option value=''>선택</option>
<?php
										$oc_reason_sub_code_html = "";
										$oc_reason_sub_code_html .= '<div class="mt-10 checkbox-set">';
										foreach($exchange_oc_code as $key => $val) {
?>
											<option value="<?=$key?>"><?=$val['name']?></option>
<?
											if($val['detail_code']) {
												$oc_rsc_addClass	= " style='display:none'";
												$oc_reason_sub_code_html .= '
													<div class="mt-10 CLS_sel_sub_code chk_sub_code_'.$key.'" '.$oc_rsc_addClass.'>
												';
												foreach($val['detail_code'] as $c2key => $c2val) {
													$oc_reason_sub_code_html	.= '<div >
																						<input id="checkbox-'.$key.$c2key.'" class="b_sel_sub_code" type="checkbox" name="b_sel_sub_code" value="'.$c2key.'">
																						<label for="checkbox-'.$key.$c2key.'">'.$c2val.'</label>
																						</div>
																					';
												}
												$oc_reason_sub_code_html .= '</div>';
											}
										}
										$oc_reason_sub_code_html .= '</div>';
?>
									</select>

								</div>
							</div>
							<?=$oc_reason_sub_code_html?>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="detail_reason">상세사유</label></th>
						<td>
							<textarea id="detail_reason" class="w100-per" name="memo" cols="5" rows="3" style="height:79px" title="상세사유 입력" placeholder="교환내용을 자세하게 작성해주세요."></textarea>
						</td>
					</tr>
				</tbody>
			</table>

			<dl class="return-delivery-price">
				<dt>택배비 발송</dt>
				<?
				$oc_delivery_fee_type_cnt = 0;
				foreach($delivery_fee_type as $key => $val) {
 				?>
					<?if($key  == "3"){ ?>
					<dd>
						<div class="radio">
							<input type="radio" id="radio-delivery-fee-re<?=$key?>" value="<?=$key ?>" name="return_deli_type">
							<label for="radio-delivery-fee-re<?=$key?>"><?=$val ?></label>
						</div>
						<div class="input-cover d-iblock ml-10"><input type="text" title="입금자명 입력" name="return_deli_memo" id="return_deli_memo2" placeholder="입금자명"></div>
					</dd>
					<?}else{ ?>
					<dd>
						<div class="radio">
							<input type="radio" id="radio-delivery-fee-re<?=$key?>" value="<?=$key ?>" name="return_deli_type">
							<label for="radio-delivery-fee-re<?=$key?>"><?=$val ?></label>
						</div>
					</dd>
					<?} ?>

				<?} ?>
			</dl>

			<dl class="attention-box mt-40">
				<dt>유의사항</dt>
				<dd>교환은 같은 옵션상품만 가능합니다. 다른 옵션의 상품으로 교환을 원하실 경우, 반품 후 재구매를 해주세요 </dd>
				<dd>상품이 손상/훼손되었거나 이미 사용하셨다면 교환이 불가능합니다 </dd>
				<dd>교환 사유가 구매자 사유일 경우 왕복 교환 배송비를 상품과 함께 박스에 동봉해 주세요 </dd>
				<dd>교환 왕복 배송비가 동봉되지 않았을 경우 별도 입금 요청을 드릴 수 있습니다 </dd>
				<dd>교환 사유가 판매자 사유일 경우 별도 배송비를 동봉하지 않으셔도 됩니다 </dd>
				<dd>상품 확인 후 실제로 판매자 사유가 아닐 경우 별도 배송비 입금 요청을 드릴 수 있습니다</dd>
			</dl>

			<div class="btnPlace mt-40 mb-40 button_open">
				<!--<button class="btn-line h-large" type="button"><span>취소</span></button>-->
				<button class="btn-point h-large refundSubmit" type="button"><span>신청</span></button>
			</div>

			<div class="mt-40 mb-40 button_close" style="text-align:center; display:none;">
				========== 처리중 입니다 ==========
			</div>

		</div><!-- //.layer-content -->
	</div>
</div><!-- //마이페이지 > 주문상세 > 교환신청 -->




<script>
// 셀렉트 탭
$(window).ready(function(){

	var blockNum = 4;
	var showNum = "";
	$('.tab-select').on('change', function(){
/*			if($(this).children('option:selected').index() == blockNum)
		{
			$('.parcel-wrap').addClass('on');
		}else{
			$('.parcel-wrap').removeClass('on');
		}
*/
		var val = $(this).val();
		
		if(showNum == ""){
			showNum = val;
			$('.chk_sub_code_'+val).show();
		}else{
			$('.chk_sub_code_'+val).show();
			$('.chk_sub_code_'+showNum).hide();
			showNum = val;
		}

	});

	//택배비 셋팅
	$("input[name=return_deli_type]").change(function() {
		var val = $(this).val();
		if(val == "1" || val == "3"){
			$("input[name=return_deli_price]").val("5000");
		}else if(val == "2"){
			$("input[name=return_deli_price]").val("2500");
		}else{
			$("input[name=return_deli_price]").val("0");
		}
	});

});
</script>

<!-- 주문 > 매장안내 -->
<div class="layer-dimm-wrap pop-infoStore">
	<div class="layer-inner">
		<h2 class="layer-title">매장 위치정보</h2>
		<button class="btn-close" type="button"><span>닫기</span></button>
		<div class="layer-content store_view">

		</div><!-- //.layer-content -->
	</div>
</div><!-- //주문 > 매장안내 -->