<!-- 메인 컨텐츠 -->
<form name=form1 action="<?=$Dir.FrontDir?>ordersend.php" method=post>
<input type="hidden" name="addorder_msg" value="">
<input type="hidden" name="deli_type" value="0">
<div class="main_wrap">
	<div class="cart_wrap">
		<div class="cart_order_wrap">
			<h3 class="title mt_20">
				주문/결제
				<p class="line_map"><a>장바구니</a> &gt; <a class="on">주문/결제</a> &gt; <a>주문완료</a></p>
			</h3>

			<!-- 주문 상품 -->
			<table class="list_table" summary="담은 상품의 정보, 판매가, 수량, 할인금액, 결제 예정가, 적립금을 확인할 수 있습니다.">
				<caption>01. 주문 상품</caption>
				<colgroup>
					<col style="width:auto" />
					<col style="width:95px" />
					<col style="width:85px" />
					<!--<col style="width:85px" />-->
					<col style="width:95px" />
					<col style="width:85px" />
				</colgroup>
				<thead>
					<tr>
						<th scope="col">상품정보</th>
						<th scope="col">판매가</th>
						<th scope="col">수량</th>
						<!--<th scope="col">행사할인</th>-->
						<!--<th scope="col">회원할인</th>-->
						<th scope="col">결제 예정가</th>
						<th scope="col">적립금</th>
					</tr>
				</thead>
				<tbody>
<?
	$sql = "SELECT b.vender FROM tblbasket a, tblproduct b WHERE a.tempkey='".$_ShopInfo->getTempkey()."' ";
	$sql.= "AND a.productcode=b.productcode GROUP BY b.vender ";
	$res=pmysql_query($sql,get_db_conn());

	$cnt=0;
	$sumprice = 0;
	$deli_price = 0;
	$reserve = 0;
	$arr_prlist=array();
	$row_sellprice = 0;
	$total_price_ = 0; // 최종금액
	while($vgrp=pmysql_fetch_object($res)) {
		$_vender=NULL;
		if($vgrp->vender>0) {
			$sql = "SELECT deli_price, deli_pricetype, deli_mini, deli_limit FROM tblvenderinfo WHERE vender='".$vgrp->vender."' ";
			$res2=pmysql_query($sql,get_db_conn());
			if($_vender=pmysql_fetch_object($res2)) {
				if($_vender->deli_price==-9) {
					$_vender->deli_price=0;
					$_vender->deli_after="Y";
				}
				if ($_vender->deli_mini==0) $_vender->deli_mini=1000000000;
			}
			pmysql_free_result($res2);

		}
		
		$sql = "SELECT a.opt1_idx,a.opt2_idx,a.optidxs,a.quantity,b.productcode,b.productname,b.sellprice,b.membergrpdc, b.option_reserve, ";
		$sql.= "b.reserve,b.reservetype,b.addcode,b.tinyimage,b.option_price,b.option_quantity,b.option1,b.option2, ";
		$sql.= "c.consumerprice as group_consumerprice,c.sellprice as group_sellprice,c.sell_reserve as group_reserve, c.sellprice*a.quantity as group_realprice, ";
		$sql.= "b.etctype,b.deli_price,b.deli,b.sellprice*a.quantity as realprice, b.selfcode, b.vip_product,a.assemble_list,a.assemble_idx,a.package_idx, b.consumerprice ";
		$sql.= "FROM tblbasket a, tblproduct b LEFT OUTER JOIN (SELECT * FROM tblmembergroup_price where group_code = '{$_ShopInfo->memgroup}') c ON b.productcode = c.productcode";
		$sql.= " WHERE 1=1 ";
		$sql.= "AND b.vender='".$vgrp->vender."' ";
		$sql.= "AND a.tempkey='".$_ShopInfo->getTempkey()."' ";
		$sql.= "AND a.productcode=b.productcode ";
		$sql.= "ORDER BY a.date DESC ";
		$result=pmysql_query($sql,get_db_conn());

		$mem_dc_price=0;  //회원등급에 의한 할인가
		$vender_sumprice = 0;
		$vender_delisumprice = 0;//해당 입점업체의 기본배송비 총 구매액
		$vender_deliprice = 0;
		$deli_productprice=0;
		$deli_init = false;
		//$ShopData = new ShopData();
		//exdebug($_data);
		/*
			[deli_type] => T
			[deli_basefee] => 2500
			[deli_basefeetype] => N
			[deli_miniprice] => 50000
			[deli_oneprprice] => N
			[deli_setperiod] => 7
			[deli_limit] => 

		*/
		while($row = pmysql_fetch_object($result)) {
			/*상품 재고및 유효성 검사*/
			$stateResult = $order->procductSellState($row->productcode,$row->opt1_idx,$row->opt2_idx);
			
			if (!$stateResult[result]) {
				$msg = $row->productname."은\\r\\n".$stateResult[message];
				alert_go($msg,'/front/basket.php');
			}
			if ($row->group_sellprice != "") {
				$row->sellprice = $row->group_sellprice;
				$row->consumerprice = $row->group_consumerprice;
				$row->reserve = $row->group_reserve;
				$row->realprice = $row->group_realprice;
			}
			if (strlen($row->option_price)>0 && $row->opt1_idx==0) {
				$sql = "DELETE FROM tblbasket WHERE tempkey='".$_ShopInfo->getTempkey()."' ";
				$sql.= "AND productcode='".$row->productcode."' AND opt1_idx='".$row->opt1_idx."' ";
				$sql.= "AND opt2_idx='".$row->opt2_idx."' AND optidxs='".$row->optidxs."' ";
				pmysql_query($sql,get_db_conn());
				alert_go("필수 선택 옵션 항목이 있습니다.\\n옵션을 선택하신후 장바구니에\\n담으시기 바랍니다.",$Dir.FrontDir."productdetail.php?productcode=".$row->productcode);
			}
			if(preg_match("/^(\[OPTG)([0-9]{4})(\])$/",$row->option1)){
				$optioncode = substr($row->option1,5,4);
				$row->option1="";
				$row->option_price="";
				if(!empty($row->optidxs)) {
					$tempoptcode = rtrim($row->optidxs,',');
					$exoptcode = explode(",",$tempoptcode);

					$sqlopt = "SELECT * FROM tblproductoption WHERE option_code='".$optioncode."' ";
					$resultopt = pmysql_query($sqlopt,get_db_conn());
					if($rowopt = pmysql_fetch_object($resultopt)){
						$optionadd = array (&$rowopt->option_value01,&$rowopt->option_value02,&$rowopt->option_value03,&$rowopt->option_value04,&$rowopt->option_value05,&$rowopt->option_value06,&$rowopt->option_value07,&$rowopt->option_value08,&$rowopt->option_value09,&$rowopt->option_value10);
						$opti=0;
						$optvalue="";
						$option_choice = $rowopt->option_choice;
						$exoption_choice = explode("",$option_choice);
						while(strlen($optionadd[$opti])>0){
							if($exoption_choice[$opti]==1 && $exoptcode[$opti]==0){
								$delsql = "DELETE FROM tblbasket WHERE tempkey='".$_ShopInfo->getTempkey()."' ";
								$delsql.= "AND productcode='".$row->productcode."' ";
								$delsql.= "AND opt1_idx='".$row->opt1_idx."' AND opt2_idx='".$row->opt2_idx."' ";
								$delsql.= "AND optidxs='".$row->optidxs."' ";
								pmysql_query($delsql,get_db_conn());
								alert_go("필수 선택 옵션 항목이 있습니다.\\n옵션을 선택하신후 장바구니에\\n담으시기 바랍니다.",$Dir.FrontDir."productdetail.php?productcode=".$row->productcode);
							}
							if($exoptcode[$opti]>0){
								$opval = explode("",str_replace('"','',$optionadd[$opti]));
								$optvalue.= ", ".$opval[0]." : ";
								$exop = explode(",",str_replace('"','',$opval[$exoptcode[$opti]]));
								if ($exop[1]>0) $optvalue.=$exop[0]."(<font color=#FF3C00>+".number_format($exop[1])."원</font>)";
								else if($exop[1]==0) $optvalue.=$exop[0];
								else $optvalue.=$exop[0]."(<font color=#FF3C00>".number_format($exop[1])."원</font>)";
								$row->realprice+=($row->quantity*$exop[1]);
							}
							$opti++;
						}
						$optvalue = ltrim($optvalue,',');
					}
				}
			} else {
				$optvalue="";
			}

			$cnt++;

			$assemble_str="";
			$package_str="";
		//######### 옵션에 따른 가격 변동 체크 ###############
		/*if (strlen($row->option_price)==0) {
			$price = $row->realprice;
			$tempreserve = getReserveConversion($row->reserve,$row->reservetype,$row->sellprice,"N");
			$sellprice=$row->sellprice;
		} else if (strlen($row->opt1_idx)>0) {
			$option_price = $row->option_price;
			$pricetok=explode(",",$option_price);
			$priceindex = count($pricetok);
			$price = $pricetok[$row->opt1_idx-1]*$row->quantity;
			$tempreserve = getReserveConversion($row->reserve,$row->reservetype,$pricetok[$row->opt1_idx-1],"N");
			$sellprice=$pricetok[$row->opt1_idx-1];
		}*/
		$row_sellprice = $row->sellprice;
		$option_price = $row->option_price;
		$pricetok=explode(",",$option_price);
		$selectOptionPrice = $pricetok[$row->opt1_idx-1];
		$price = $selectOptionPrice*$row->quantity;
		if ($selectOptionPrice != "" || $selectOptionPrice != null) {
			$row_sellprice = (int)$selectOptionPrice+(int)$row_sellprice;
		}
		$total_price_ = $row_sellprice * $row->quantity;
		$tempreserve = getReserveConversion($row->reserve,$row->reservetype,$pricetok[$row->opt1_idx-1],"N");
		
		//######### 옵션에 따른 가격 변동 체크 끝 ############
		//$bf_sumprice += $before_sellprice*$row->quantity;
		$bf_sumprice += $total_price_;
		//$sumprice += $price;
		$sumprice += ((int)$row->consumerprice + (int)$selectOptionPrice) * $row->quantity;
		//$vender_sumprice += $price;
		### 타임 세일 / 오늘의 특가 가격으로 재 셋팅
		$timesale_sellprice = 0;
		/*$timesale_sellprice = getSpeDcPrice($row->productcode);
		if($timesale_sellprice > 0) $sellprice = $timesale_sellprice;*/

		######### 상품 특별할인률 적용 ############
		/*$dc_data = $product->getProductDcRate($row->productcode);
		if($row->vip_product == 0){
		$salemoney = getProductDcPrice($sellprice,$dc_data[price]);
		$salemoney2 = getProductDcPrice($sellprice,$dc_data[price]);
		}else{
		$salemoney = getProductDcPrice($sellprice,0);
		$salemoney2 = 0;
		}
		$salereserve = getProductDcPrice($sellprice,$dc_data[reserve]);*/

		//######### 옵션별 적립금 적용 ############
		/*$option_reserve = explode(',',$row->option_reserve);
		if($option_reserve[$row->opt1_idx-1]>0){
			$tempreserve=$option_reserve[$row->opt1_idx-1];
		}*/


		//회원 할인율 적용
		/*$before_sellprice=$sellprice;
		$bf_price = $before_sellprice*$row->quantity;
		if($row->vip_product == 0){
		$sellprice = $sellprice - $salemoney;
		}else{
		$sellprice = $sellprice;
		}
		$price = $sellprice*$row->quantity;
		*/
		//추가 적립금 적용
		/*
		$tempreserve+=$salereserve;

		if($row->consumerprice > $before_sellprice){
			$salemoney+=$row->consumerprice-$before_sellprice;
			$before_sellprice=$row->consumerprice;
		}
		*/
		//비회원이면 적립금 노출 X
		if(strlen($_ShopInfo->getMemgroup())==0) $tempreserve=0;

		//######### 옵션에 따른 가격 변동 체크 끝 ############
		/*$bf_sumprice += $before_sellprice*$row->quantity;
		$sumprice += $price;
		$vender_sumprice += $price;*/
		//################ 개별 배송비 체크 #################
		$deli_str = "";
		if (($row->deli=="Y" || $row->deli=="N") && $row->deli_price>0) {
			if($row->deli=="Y") {
				$deli_productprice += $row->deli_price*$row->quantity;
				$deli_str = "&nbsp;<font color=a00000>- 개별배송비<font color=#FF3C00>(구매수 대비 증가:".number_format($row->deli_price*$row->quantity)."원)</font></font>";
			} else {
				$deli_productprice += $row->deli_price;
				$deli_str = "&nbsp;<font color=a00000>- 개별배송비<font color=#FF3C00>(".number_format($row->deli_price)."원)</font></font>";
			}
		} else if($row->deli=="F" || $row->deli=="G") {
			$deli_productprice += 0;
			if($row->deli=="F") {
				$deli_str = "&nbsp;<font color=a00000>- 개별배송비<font color=#0000FF>(무료)</font></font>";
			} else {
				$deli_str = "&nbsp;<font color=a00000>- 개별배송비<font color=#38A422>(착불)</font></font>";
			}
		} else {
			$deli_init=true;
			$vender_delisumprice += $total_price_;
		}
			
		//###################################################
		$productname=$row->productname;

		$arr_prlist[$row->productcode]=$row->productname;

		$reserve += $tempreserve*$row->quantity;

		//######## 특수값체크 : 현금결제상품//무이자상품 #####
		$bankonly_html = ""; $setquota_html = "";
		if (strlen($row->etctype)>0) {
			$etctemp = explode("",$row->etctype);
			for ($i=0;$i<count($etctemp);$i++) {
				switch ($etctemp[$i]) {
					case "BANKONLY": $bankonly = "Y";
						$bankonly_html = " <img src=".$Dir."images/common/bankonly.gif border=0 align=absmiddle> ";
						break;
					case "SETQUOTA":
						if ($_data->card_splittype=="O" && $price>=$_data->card_splitprice) {
							$setquotacnt++;
							$setquota_html = " <img src=".$Dir."images/common/setquota.gif border=0 align=absmiddle>";
							$setquota_html.= "</b><font color=black size=1>(";
							$setquota_html.="3~";
							$setquota_html.= $_data->card_splitmonth.")</font>";
						}
						break;
				}
			}
		}
?>
					<tr>
						<td class="info">
							<a href="<?=$Dir.FrontDir?>productdetail.php?productcode=<?=$row->productcode?>">
								<?if(strlen($row->tinyimage)!=0 && file_exists($Dir.DataDir."shopimages/product/".$row->tinyimage)){?>
									<img src="<?=$Dir.DataDir?>shopimages/product/<?=$row->tinyimage?>" width='126' height='126'>
								<?}elseif(strlen($row->tinyimage)!=0 && file_exists($Dir.$row->tinyimage)){?>
									<img src="<?=$Dir.$row->tinyimage?>" width='126' height='126'>
								<?}else{?>
									<img src="<?=$Dir?>images/no_img.gif" width="126">
								<?}?>
							</a>
							<span class="name">
							<a href="<?=$Dir.FrontDir?>productdetail.php?productcode=<?=$row->productcode?>">
								<?=viewproductname($productname,$row->etctype,$row->selfcode,$row->addcode) ?><?=$bankonly_html?><?=$setquota_html?><?=$deli_str?>
							</a>
							<br>
							<?
								if (strlen($row->option1)>0 || strlen($row->option2)>0 || strlen($optvalue)>0) {// 특징 및 선택사항이 있으면
									// ###### 특성 #########
									if (strlen($row->option1)>0) {
										$temp = $row->option1;
										$tok = explode(",",$temp);
										echo "<span class=\"option\">옵션 : ".$tok[$row->opt1_idx]." </span>";
									}
									if (strlen($row->option2)>0) {
										$temp = $row->option2;
										$tok = explode(",",$temp);
										echo "<span class=\"option\"> /".$tok[$row->opt2_idx]."</span>";
									}
									if(strlen($optvalue)>0) {
										echo $optvalue."</font>\n";
									}
								}
							?>
							</span>
						</td>
						<td><strong><?=number_format($row_sellprice)?></strong></td>
						<td>
							<?=$row->quantity ?>
						</td>
						<td><?=number_format($total_price_)?></td>
						<td class="point"><img src="../img/icon/cart_point_icon.gif" alt="적립금" /><?=number_format($tempreserve*$row->quantity)?></td>
					</tr>
<?
		}
	}
	pmysql_free_result($result);

	$vender_deliprice=$deli_productprice;
	
	if($_data->deli_basefee>0) {
		if($_data->deli_basefeetype=="Y") {
			$vender_delisumprice = $vender_sumprice;
		}

		if ($vender_delisumprice<$_data->deli_miniprice && $deli_init) {
			$vender_deliprice+=$_data->deli_basefee;
		}
	} else if(strlen($_data->deli_limit)>0) {
		if($_data->deli_basefeetype=="Y") {
			$vender_delisumprice = $vender_sumprice;
		}

		if($deli_init) {
			$delilmitprice = setDeliLimit($vender_delisumprice,$_data->deli_limit);
			$vender_deliprice+=$delilmitprice;
		}
	}
	
	$deli_price+=$vender_deliprice;
	pmysql_free_result($res);
?>
				</tbody>
				<tfoot>
					<tr>
						<td colspan="7" bgcolor="#fafafa">
							<div class="result_box">
								<span class="total">
									<span class="txt">총 판매가</span>
									<strong class="number"><?=number_format($bf_sumprice)?> 원</strong>
								</span>
								<!--<img class="icon" src="../img/icon/cart_list_icon_minus.gif" alt="-" />-->
								<!--<span class="total">
									<span class="txt">총 할인금액</span>
									<strong class="number"><?=number_format($sumprice)?> 원</strong>
								</span>-->
								<img class="icon" src="../img/icon/cart_list_icon_plus.gif" alt="+" />
								<span class="total">
									<span class="txt">총 배송비</span>
									<strong class="number"><?=number_format($deli_price)?> 원</strong>
								</span>
								<img class="icon" src="../img/icon/cart_list_icon_equals.gif" alt="=" />
								<span class="total_payment">
									<span class="txt">총 결제 금액</span>
									<strong class="number"><?=number_format($bf_sumprice+$deli_price)?><span>원</span></strong>
								</span>
							</div>
						</td>
					</tr>
				</tfoot>
			</table>
			<!-- // 주문 상품 -->

			<div class="button_area">
				<a href="../front/basket.php" target="_self"><img src="../img/button/cart_order_move_btn.gif" alt="장바구니로 이동" /></a>
			</div>

			<div class="order_area">

				<!-- 고객정보 -->
				<div class="orderer_area">
					<table class="info_table" summary="주문자명, 주소, 휴대폰 번호, 이메일을 작성할 수 있습니다.">
						<caption>02. 고객정보</caption>
						<colgroup>
							<col style="width:121px" />
							<col style="width:auto" />
						</colgroup>
						<tbody>
							<tr>
								<th scope="row">주문자명 분</th>
								<td class="name">
									<?
										if(strlen($_ShopInfo->getMemid())>0) {
									?>
										<input type=text  name="sender_name" value="<?=$name?>" readonly style="font-weight:bold" style='border:0' required msgR="주문하시는분의 이름을 적어주세요">
									<?
										} else {
									?>
										<input type=text  name="sender_name" value="" style="font-weight:bold" style='border:0' required msgR="주문하시는분의 이름을 적어주세요">
									<?
										}
									?>
								</td>
							</tr>
							<!--tr>
								<th scope="row">주소</th>
								<td class="address">
									<div class="post_box">
										<input type="text" title="우편번호 앞 자리를 입력하세요." /><span>-</span>
										<input type="text" title="우편번호 뒷 자리를 입력하세요." />
										<a href="#" target="_self"><img src="../img/button/cart_order_post_btn.gif" alt="우편번호검색" /></a>
									</div>
									<input class="margin_input" type="text" title="상세주소를 입력하세요." />
									<input type="text" title="상세주소 나머지를 입력하세요." />
								</td>
							</tr-->
							<tr>
								<th scope="row">휴대폰번호</th>
								<td class="phone">
									<input type=text name="sender_tel1" value="<?=$mobile[0] ?>" size=3 maxlength=3 required onKeyUp="strnumkeyup(this)" msgR="휴대폰 번호 앞 자리를 입력하세요."><span>-</span>
									<input type=text name="sender_tel2" value="<?=$mobile[1] ?>" size=4 maxlength=4 required onKeyUp="strnumkeyup(this)" msgR="휴대폰 번호 가운데 자리를 입력하세요."><span>-</span>
									<input type=text name="sender_tel3" value="<?=$mobile[2] ?>" size=4 maxlength=4 required onKeyUp="strnumkeyup(this)" msgR="휴대폰 번호 가운데 자리를 입력하세요.">
								</td>
							</tr>
							<tr>
								<th scope="row">이메일</th>
								<td class="email">
									<?
										$emailArray = explode("@", $email);
										$selected['sel_email_tail'][$emailArray[1]] = "selected";
									?>
									<input type='hidden' name="sender_email" class = 'CLS_email_addr' value="<?=$email?>" required>
									<input type="text" class = 'CLS_email_head' value = '<?=$emailArray[0]?>' required title="이메일 아이디를 입력하세요." /><span>@</span>
									<input type="text" class = 'CLS_email_tail' value = '<?=$emailArray[1]?>' required title="이메일 도메인을 입력하세요." style = 'width:100px;' readonly/>
									<select name="sel_email_tail" id="ID_sel_email_tail" style="width:100px;height:27px;margin-left:10px;">
										<option value="">도메인 선택</option>
										<option value="naver.com" <?=$selected['sel_email_tail']['naver.com']?>>naver.com</option>
										<option value="hanmail.net" <?=$selected['sel_email_tail']['hanmail.net']?>>hanmail.net</option>
										<option value="daum.net" <?=$selected['sel_email_tail']['daum.net']?>>daum.net</option>
										<option value="nate.com" <?=$selected['sel_email_tail']['nate.com']?>>nate.com</option>
										<option value="gmail.com" <?=$selected['sel_email_tail']['gmail.com']?>>gmail.com</option>
										<option value="hotmail.com" <?=$selected['sel_email_tail']['hotmail.com']?>>hotmail.com</option>
										<option value="lycos.co.kr" <?=$selected['sel_email_tail']['lycos.co.kr']?>>lycos.co.kr</option>
										<option value="empal.com" <?=$selected['sel_email_tail']['empal.com']?>>empal.com</option>
										<option value="cyworld.com" <?=$selected['sel_email_tail']['cyworld.com']?>>cyworld.com</option>
										<option value="yahoo.co.kr" <?=$selected['sel_email_tail']['yahoo.co.kr']?>>yahoo.co.kr</option>
										<option value="paran.com" <?=$selected['sel_email_tail']['paran.com']?>>paran.com</option>
										<option value="dreamwiz.com" <?=$selected['sel_email_tail']['dreamwiz.com']?>>dreamwiz.com</option>
										<option value="-">직접 입력</option>
									<select>
									<!--a href="#" target="_self"><img src="../img/button/cart_order_email_btn.gif" alt="직접입력" /></a-->
								</td>
							</tr>
						</tbody>
					</table>
				</div>
				<!-- // 고객정보 -->

				<!-- 배송지정보 -->
				<div class="address_area">
					<table class="info_table" summary="수령자명, 주소, 전화번호, 휴대폰번호, 이메일, 배송 메시지를 작성할 수 있습니다.">
						<caption>03. 배송지 정보 <span class="same_box"><input type='checkbox' name="same" value="Y" onclick="SameCheck(this.checked)" id="dev_orderer"><label for="dev_orderer">주문고객과 동일한 주소 사용</label></span></caption>
						<colgroup>
							<col style="width:121px" />
							<col style="width:auto" />
						</colgroup>
						<tbody>
							<tr>
								<th scope="row">수령자명</th>
								<td class="name">
									<input type="text" name = 'receiver_name' required msgR="주문하시는 분 이름을 입력하세요." />
									<a href="javascript:addrchoice();" target="_self"><img src="../img/button/cart_order_address_list_btn.gif" alt="배송지 목록" /></a>
								</td>
							</tr>
							<tr>
								<th scope="row">주소</th>
								<td class="address">
									<div class="post_box">
										<input type="text" name = 'rpost1' id = 'rpost1' readonly required msgR="우편번호 앞 자리를 입력하세요." /><span>-</span>
										<input type="text" name = 'rpost2' id = 'rpost2' readonly required msgR="우편번호 뒷 자리를 입력하세요." />
										<!--<a href="javascript:get_post();" target="_self"><img src="../img/button/cart_order_post_btn.gif" alt="우편번호검색" /></a>-->
										<a href="javascript:openDaumPostcode();" target="_self"><img src="../img/button/cart_order_post_btn.gif" alt="우편번호검색" /></a>
									</div>
									<input class="margin_input" name = 'raddr1' id = 'raddr1' type="text" readonly required msgR="상세주소를 입력하세요." />
									<input type="text" name = 'raddr2' id = 'raddr2' required msgR="상세주소 나머지를 입력하세요." />
								</td>
							</tr>
							<tr>
								<th scope="row">전화번호</th>
								<td class="phone">
									<input type="text" name="receiver_tel11" maxlength='4' onKeyUp="strnumkeyup(this)" required msgR="전화번호 앞 자리를 입력하세요." /><span>-</span>
									<input type="text" name="receiver_tel12" maxlength='4' onKeyUp="strnumkeyup(this)" required msgR="전화번호 가운데 자리를 입력하세요." /><span>-</span>
									<input type="text" name="receiver_tel13" maxlength='4' onKeyUp="strnumkeyup(this)" required msgR="전화번호 뒷 자리를 입력하세요." />
								</td>
							</tr>
							<tr>
								<th scope="row">휴대폰번호</th>
								<td class="phone">
									<input type="text" name="receiver_tel21" maxlength='3' onKeyUp="strnumkeyup(this)" required msgR="휴대폰 번호 앞 자리를 입력하세요." /><span>-</span>
									<input type="text" name="receiver_tel22" maxlength='4' onKeyUp="strnumkeyup(this)" required msgR="휴대폰 번호 가운데 자리를 입력하세요." /><span>-</span>
									<input type="text" name="receiver_tel23" maxlength='4' onKeyUp="strnumkeyup(this)" required msgR="휴대폰 번호 뒷 자리를 입력하세요." />
								</td>
							</tr>
							<!--tr>
								<th scope="row">이메일</th>
								<td class="email">
									<input type="text" title="이메일 아이디를 입력하세요." /><span>@</span>
									<input type="text" title="이메일 도메인을 입력하세요." />
									<a href="#" target="_self"><img src="../img/button/cart_order_email_btn.gif" alt="직접입력" /></a>
								</td>
							</tr-->
							<tr>
								<th scope="row">배송 메시지</th>
								<td class="message">
									<div class="pos_r">
										<!--
										<?if(count($arr_prlist)>1){?>
										<?}else{?>
											<input type="hidden" name="msg_type" value="2">&nbsp;
										<?}?>
										-->
										<input type="hidden" name="msg_type" value="1">
										<input name = 'order_prmsg' type="text" title="배송 메시지를 입력하세요." />

										<div class="delivery_message">
											<ul class="delivery_message_list">
												<li><a href="javascript:;" class = 'CLS_deliMsg'>부재시 경비실에 맡겨주세요.</a></li>
												<li><a href="javascript:;" class = 'CLS_deliMsg'>빠른 배송 부탁합니다.</a></li>
												<li><a href="javascript:;" class = 'CLS_deliMsg'>배송전 연락 바랍니다.</a></li>
											</ul>
										</div>
										<script type="text/javascript">
										$(function(){
											$('div.cart_order_wrap table.info_table td.message input').mouseenter(function(){
												$('div.delivery_message').css('display' , 'block');
											});
											$('div.delivery_message').mouseleave(function(){
												$('div.delivery_message').css('display' , 'none');
											});
										});
										</script>

									</div>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
				<!-- // 배송지정보 -->

				<!-- 할인정보 -->
				<?if ((strlen($_ShopInfo->getMemid())>0 && $_data->reserve_maxuse>=0 && $user_reserve!=0) || (strlen($_ShopInfo->getMemid())>0 && $_data->coupon_ok=="Y")) {?>
				<div class="dc_area">
					<table class="info_table" summary="할인받을 쿠폰 및 적립금을 입력할 수 있습니다.">
						<caption>04. 할인정보</caption>
						<colgroup>
							<col style="width:121px" />
							<col style="width:auto" />
						</colgroup>
						<?
							if($okreserve<0){
								$okreserve=(int)($bf_sumprice*abs($okreserve)/100);
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


						<?if(strlen($_ShopInfo->getMemid())>0 && $_data->coupon_ok=="Y") {?>
						<tr>
							<th scope="row" rowspan = '2'>쿠폰적용</th>
							<td class="coupon">
								<div id = "ID_coupon_code_layer"></div>
								<input type = "hidden" name = "coupon_code">
								<input type=hidden name="bank_only" value="N">
								<span>할인</span>
								<input type="text" name='coupon_dc' id='coupon_dc' value='0' readonly title="쿠폰으로 할인받은 금액" /><span>원</span>
								<a href="javascript:coupon_check('<?=$okreserve?>');" target="_self">
									<img src="../img/button/cart_order_coupon_btn.gif" alt="쿠폰조회 및 적용" />
								</a>
							</td>
						</tr>
						<tr>
							<td class="coupon">
								<span>적립</span>
								<input type="text" name='coupon_reserve' value = '0' readonly title="쿠폰으로 적립받은 금액" /><span>원</span>
							</td>
						</tr>
						<?}?>

						<?if (strlen($_ShopInfo->getMemid())>0 && $_data->reserve_maxuse>=0 && $user_reserve!=0){?>
						<tr>
							<th scope="row">적립금</th>
							<td class="point">
								<?if($reserve_maxprice>$sumprice) {?>
									<span>구매금액이 <?=number_format($reserve_maxprice)?>원 이상이면 사용가능합니다.</span>
									<input type="hidden" name="usereserve" id="usereserve" value=0>
								<?}else if($user_reserve>=$_data->reserve_maxuse){?>
									<span>보유적립금 : <?=number_format($remainreserve+$okreserve)?>원 | 사용가능적립금 : <?=number_format($okreserve)?>원</span>
									<input type="text" name="usereserve" id="usereserve" value='0' title="사용할 적립금을 입력하세요." /><span>원</span>
									<input type="hidden" name="okreserve" value="<?=$okreserve?>">
									<?if($user_reserve>$reserve_limit){?><input type='hidden' name="remainreserve" value="<?=$remainreserve?>" ><?}?>
									<a href="javascript:reserve_check('<?=$okreserve?>');" target="_self"><img src="../img/button/cart_order_use_btn.gif" alt="사용" /></a>
								<?	}else{?>
									<span><?=number_format($_data->reserve_maxuse)?>원 이상이면 사용가능합니다.(총 적립금 <?=number_format($remainreserve+$okreserve)?>원)</span>
									<input type="hidden"  name="usereserve" id="usereserve" value=0>
								<?}?>
							</td>
						</tr>
						<?} else {?>
							<input type="hidden" name="usereserve" id="usereserve" value=0>
							<input type="hidden" name="okreserve" value="<?=$okreserve?>">
							<input type='hidden' name="remainreserve" value="<?=$remainreserve?>" >
						<?} ?>
					</table>
				</div>
				<?}?>
				<!-- // 할인정보 -->

				<!-- 결제수단 -->
				<div class="means_area">
					<table class="info_table" summary="결제수단을 선택할 수 있습니다.">
						<caption>05. 결제수단</caption>
						<colgroup>
							<col style="width:121px" />
							<col style="width:auto" />
						</colgroup>
						<tbody>
							<tr>
								<th scope="row">결제수단 선택</th>
								<td>
									<ul class="means">
										<?if($escrow_info["onlycard"]!="Y" && strstr("YN", $_data->payment_type)) {?>
											<li><input id="dev_payment1" class="dev_payment" name="dev_payment" type="radio" value="B" onclick="sel_paymethod(this);" /><label for="dev_means1">무통장입금</label></li>
										<?}?>


										<?if(strstr("YC", $_data->payment_type) && ord($_data->card_id)) {?>
											<li><input id="dev_payment2" class="dev_payment" name="dev_payment" type="radio" value="C" onclick="sel_paymethod(this);" /><label for="dev_means2">신용카드</label></li>
										<?}?>


										<?if($escrow_info["onlycard"]!="Y" && !strstr($_SERVER["HTTP_USER_AGENT"],'Mobile') && !strstr($_SERVER[HTTP_USER_AGENT],"Android") && ord($_data->trans_id)){?>
											<li><input id="dev_payment3" class="dev_payment" name="dev_payment" type="radio" value="V" onclick="sel_paymethod(this);" /><label for="dev_means3">계좌이체</label></li>
										<?}?>


										<?if($escrow_info["onlycard"]!="Y" && ord($_data->virtual_id)){?>
											<li><input id="dev_payment4" class="dev_payment" name="dev_payment" type="radio" value="O" onclick="sel_paymethod(this);" /><label for="dev_means4">가상계좌</label></li>
										<?}?>


										<?if(($escrow_info["escrowcash"]=="A" || ($escrow_info["escrowcash"]=="Y" && (int)($sumprice+$deli_price)>=$escrow_info["escrow_limit"])) && ord($_data->escrow_id)){?>
										<?
											$pgid_info="";
											$pg_type="";
											$pgid_info=GetEscrowType($_data->escrow_id);
											$pg_type=trim($pgid_info["PG"]);
										?>
											<?if(strstr("ABCD",$pg_type)){?>
												<li><input id="dev_payment5" class="dev_payment" name="dev_payment" type="radio" value="Q" onclick="sel_paymethod(this);" /><label for="dev_means5">에스크로</label></li>
											<?}?>
										<?}?>
										<!-- 휴대폰 결제시 신용카드 결제창 호출로 임시 주석 처리 수정해야함	outhor 정욱  2014-12-30-->
										<!-- 
										<?if(ord($_data->mobile_id)){?>
											<li><input id="dev_payment6" class="dev_payment" name="dev_payment" type="radio" value="M" onclick="sel_paymethod(this);" /><label for="dev_means6">휴대폰</label></li>
										<?}?>
										-->

									</ul>
								</td>
							</tr>
							<tr>
								<td colspan="2">
									<div class="card_type" id="card_type" style="display:none">
										<div class="table_style">
											<table border=0 cellpadding=0 cellspacing=0 width="100%" summary="임금계좌를 선택" style="text-align:left;background:#FFF;">
												<colgroup>
													<?if($etcmessage[2]=="Y") {?><col width="20%" /><?}?>
													<col />
												</colgroup>
												<?if($etcmessage[2]=="Y") {?>
												<tr>
													<th>입금자명</th>
													<td>
														<input type="text" name="bank_sender" value="" >
													</td>
												</tr>
												<?}?>
												<tr>
													<th>입금계좌</th>
													<td>
														<select name="pay_data_sel" id="pay_data_sel" onchange="sel_account(this)" style="width:350px;">
															<option value='' >입금 계좌번호 선택 (반드시 주문자 성함으로 입금)</option>
															<?foreach($bank_payinfo as $k => $v){?>
															<option value="<?=$v?>" ><?=$v?></option>															
															<?}?>
														</select>
													</td>
												</tr>
												<? //if($_ShopInfo->memid && $_ShopInfo->wsmember=="Y"){?>
												<?if(false){?>
												<tr>
													<th>영수증신청</th>
													<td>
														<input type="radio" name="receipt_yn" id="receipt_yn1" class="receipt_yn" value="N" checked>
														<label label for="receipt_yn1" style="font-weight:bold; font-size:12px;">미신청</label> ( 차후 영수증 발행요청시 총 결제금액의 10%(부가세)가 발생됩니다 )<br>
														<input type="radio" name="receipt_yn" id="receipt_yn2" class="receipt_yn" value="Y">
														<label for="receipt_yn2"  style="font-weight:bold; font-size:12px;">신청</label> ( 영수증 발급시 총 할인액에 -7% 가 됩니다. )
													</td>
												</tr>
												<?}?>
												<?if(abs($dc_cash_pay)){?>
												<tr>
													<th>무통장할인</th>
													<td>
														<?
																$dc_cash_pay=abs($dc_cash_pay);
																if($saletype=="Y") $dc_cash_pay_type='적립';
																else $dc_cash_pay_type='할인';
														?>
														<span class="small_red">※무통장 결제시 <?=$dc_cash_pay?>%가 추가<?=$dc_cash_pay_type?> 됩니다.</span>
													</td>
												</tr>
												<?}?>
											</table>
										</div>
									</div>












									<!--h4>신용카드선택</h4>
									<div class="card_area">
										<div class="card_box">
											<h5>카드선택</h5>
											<div class="content">
												<div class="card_select">
													<select title="카드를 선택하세요.">
														<option value="삼성카드">삼성카드</option>
													</select>
													<input id="dev_samsung" type="checkbox" /><label for="dev_samsung">삼성카드 간편결제</label>
													<a class="btn_question" href="#" target="_self"><img src="../img/button/cart_order_means_question_icon.gif" alt="" /></a>
												</div>
											</div>
										</div>
										<div class="card_box">
											<h5>할부유형</h5>
											<div class="content">
												<ul class="installment">
													<li><input id="dev_type1" name="dev_type" type="radio" /><label for="dev_type1">일시불</label></li>
													<li>
														<input id="dev_type2" name="dev_type" type="radio" /><label for="dev_type2">무이자할부</label>
														<select title="할부기간을 선택하세요.">
															<option value="선택">선택</option>
														</select>
													</li>
													<li>
														<input id="dev_type3" name="dev_type" type="radio" /><label for="dev_type3">일반할부</label>
														<select title="할부기간을 선택하세요.">
															<option value="선택">선택</option>
														</select>
													</li>
												</ul>
											</div>
										</div>
										<dl>
											<dt>신용카드 결제 시 유의사항</dt>
											<dd>카드에 BC마크가 있는 경우, 발급은행과 관계없이 BC카드를 선택하셔야 합니다.</dd>
											<dd>기 발급된 구)LG카드는 신한카드를 선택하셔야 합니다.</dd>
										</dl>
									</div-->
								</td>
							</tr>
						</tbody>
					</table>

					<h4 class="hide">무이자 할부안내</h4>
					<div class="card_info hide">
						<ul>
							<li>
								<img src="../img/test/cart_order_means_card1.gif" alt="롯데카드" />
								<p>
									2,3개월 ( 5만원<img src="../img/icon/cart_order_means_arrow.gif" alt="이상" /> )<br />
									2,3,6개월 ( 30만원<img src="../img/icon/cart_order_means_arrow.gif" alt="이상" /> )<br />
									2,3,6,10개월 ( 70만원<img src="../img/icon/cart_order_means_arrow.gif" alt="이상" /> )
								</p>
							</li>
							<li>
								<img src="../img/test/cart_order_means_card2.gif" alt="비씨카드" />
								<p>
									6개월 ( 30만원<img src="../img/icon/cart_order_means_arrow.gif" alt="이상" /> )<br />
									6,10개월 ( 70만원<img src="../img/icon/cart_order_means_arrow.gif" alt="이상" /> )<br />
								</p>
							</li>
							<li>
								<img src="../img/test/cart_order_means_card3.gif" alt="신한카드" />
								<p>
									6개월 ( 50만원<img src="../img/icon/cart_order_means_arrow.gif" alt="이상" /> )<br />
									6, 10개월 ( 100만원<img src="../img/icon/cart_order_means_arrow.gif" alt="이상" /> )<br />
								</p>
							</li>
							<li>
								<img src="../img/test/cart_order_means_card4.gif" alt="국민카드" />
								<p>
									2,3개월 ( 5만원<img src="../img/icon/cart_order_means_arrow.gif" alt="이상" /> )<br />
									2,3,6개월 ( 50만원<img src="../img/icon/cart_order_means_arrow.gif" alt="이상" /> )<br />
									2,3,6,10개월 ( 100만원<img src="../img/icon/cart_order_means_arrow.gif" alt="이상" /> )
								</p>
							</li>
							<li>
								<img src="../img/test/cart_order_means_card5.gif" alt="삼성카드" />
								<p>
									2,3개월 ( 5만원<img src="../img/icon/cart_order_means_arrow.gif" alt="이상" /> )<br />
									2,3,6개월 ( 50만원<img src="../img/icon/cart_order_means_arrow.gif" alt="이상" /> )<br />
									2,3,6,10개월 ( 100만원<img src="../img/icon/cart_order_means_arrow.gif" alt="이상" /> )
								</p>
							</li>
							<li>
								<img src="../img/test/cart_order_means_card6.gif" alt="외환카드" />
								<p>
									6개월 ( 50만원<img src="../img/icon/cart_order_means_arrow.gif" alt="이상" /> )<br />
									6,10개월 ( 100만원<img src="../img/icon/cart_order_means_arrow.gif" alt="이상" /> )<br />
								</p>
							</li>
							<li>
								<img src="../img/test/cart_order_means_card7.gif" alt="하나SK카드" />
								<p>
									6개월 ( 50만원<img src="../img/icon/cart_order_means_arrow.gif" alt="이상" /> )<br />
									6,10개월 ( 100만원<img src="../img/icon/cart_order_means_arrow.gif" alt="이상" /> )<br />
								</p>
							</li>
						</ul>
					</div>
				</div>
				<!-- // 결제수단 -->

				<!-- 결제하기 -->
				<div class="payment_area">
					<h3>06. 결제하기</h3>
					<div class="content">
						<?//$p_price=$sumprice+$deli_price+$sumpricevat;?>
						<?$p_price=$bf_sumprice+$deli_price+$sumpricevat;?>
						<input type="hidden" name="total_sum" value="<?=$p_price?>">
						<ul>
							<li>
								<span class="txt"><strong>총 상품가격</strong></span>
								<span class="price">
									<strong id="paper_goodsprice" ><?=number_format($bf_sumprice)?>원</strong>
								</span>
							</li>
							<li>
								<span class="txt">쿠폰</span>
								<span class="price CLS_saleCoupon">0원</span>
							</li>
							<li>
								<span class="txt">마일리지</span>
								<span class="price CLS_saleMil">0원</span>
							</li>
							<li>
								<span class="txt">배송비</span>
								<span class="price order_price_style02">
									<font id='delivery_price'><?=number_format($deli_price)?></font>원
								</span>
							</li>
						</ul>
						<p class="sum">
							<span class="txt">최종 결제금액</span>
							<span class="price">
								<strong id="price_sum"><?=number_format($bf_sumprice+$deli_price)?></strong>원
							</span>
						</p>
						<div class="payment_agree">
							<h4>주문확인 및 동의</h4>
							<p>주문하시는 상품의 정보 및 가격, 배송정보를<br />확인하였으며, 구매에 동의하시겠습니까?<br />[전자상거래법 제8조 제2항]</p>
							<div class="agree_box"><input id="dev_agree" type="checkbox" /><label for="dev_agree">동의합니다.</label></div>
						</div>
					</div>
					<div class="button_box">
						<div id="paybuttonlayer" name="paybuttonlayer" style="display:block;">
							<a href="javascript:CheckForm()" onmouseover="window.status='결제';return true;" target="_self">
								<img src="../img/button/cart_order_payment_btn.gif" alt="결제하기" />
							</a>

							<a href="javascript:ordercancel('cancel')" onmouseover="window.status='취소';return true;" target="_self">
								<img src="../img/button/cart_order_cancel_btn.gif" alt="취소하기" />
							</a>
						</div>
						<div id="payinglayer" name="payinglayer" style="display:none;">
							<table border=0 cellpadding=0 cellspacing=0 width=100%>
								<tr>
									<td align=center><img src="<?=$Dir?>images/common/paying_wait.gif" border=0></td>
								</tr>
							</table>
						</div>
					</div>
				</div>
				<!-- // 결제하기 -->

			</div>
		</div>

	</div>


</div><!-- //메인 컨텐츠 -->