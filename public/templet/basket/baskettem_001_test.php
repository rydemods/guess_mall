<? /* include "_header.html"; 파일없음 */ ?>
<style>
div.cart_wrap table.list_table td div.updatebtn{width:31px;display:inline-block;float:right;margin-left:-15px;}
</style>
<!-- start container -->
<div id="container">
	<table border="0" cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td>
			<!-- 메인 컨텐츠 -->
			<div class="main_wrap">


				<div class="cart_wrap">
					<h3 class="title mt_20">
						장바구니
						<p class="line_map"><a class="on">장바구니</a> &gt; <a>주문/결제</a> &gt; <a>주문완료</a></p>
					</h3>

					<div class="info_area hide">
						<span class="name"><?=$_ShopInfo->memname?> 님은 <?=$grp_name?> 회원입니다.</span>
						<span class="coupon">쿠폰 : <strong><?=number_format($cnt_coupon)?></strong>개</span>
						<span class="point">적립금 : <strong><?=number_format($reserve)?></strong>원</span>
					</div>

					<!-- 담은 상품 -->
					<table class="list_table" summary="담은 상품의 정보, 판매가, 수량, 할인금액, 결제 예정가, 적립금을 확인할 수 있습니다.">
						<caption>담은 상품<span class = 'CLS_basketTotalCount'>(ㅡ)</span></caption>
						<colgroup>
							<col style="width:65px" />
							<col style="width:auto" />
							<col style="width:95px" />
							<col style="width:85px" />
							<!--<col style="width:85px" />
							<col style="width:95px" />-->
							<col style="width:85px" />
							<col style="width:75px" />
						</colgroup>
						<thead>
							<tr>
								<th scope="col"><input type="checkbox" title="담은 상품 전체선택" class="allCheck" checked/></th>
								<th scope="col">상품정보</th>
								<th scope="col">판매가</th>
								<th scope="col">수량</th>
								<!--<th scope="col">행사할인</th>-->
								<!--<th scope="col">회원할인</th>-->
								<th scope="col">결제 예정가</th>
								<th scope="col">적립금</th>
								<th scope="col">선택</th>
							</tr>
						</thead>
<?
$sql = "SELECT b.vender FROM tblbasket a, tblproduct b WHERE a.tempkey='".$_ShopInfo->getTempkey()."' ";
$sql.= "AND a.productcode=b.productcode GROUP BY b.vender ";
$res=pmysql_query($sql,get_db_conn());

$cnt=0;
$sumprice = 0;
$deli_price = 0;
$reserve = 0;
$formcount=0;
$row_sellprice = 0;
$total_price_ = 0; // 최종금액
while($vgrp=pmysql_fetch_object($res)) {
	//1. vender가 0이 아니면 해당 입점업체의 배송비 추가 설정값을 가져온다.
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

	$sql = "SELECT a.basketidx, a.opt1_idx,a.opt2_idx,a.optidxs,a.quantity,b.productcode,b.productname,b.sellprice,b.membergrpdc, b.option_reserve,b.consumerprice,";
	$sql.= "b.reserve,b.reservetype,b.addcode,b.tinyimage,b.option_price,b.option_quantity,b.option1,b.option2, ";
	$sql.= "c.consumerprice as group_consumerprice,c.sellprice as group_sellprice,c.sell_reserve as group_reserve, c.sellprice*a.quantity as group_realprice, ";
	$sql.= "b.etctype,b.deli_price, b.deli,b.sellprice*a.quantity as realprice, b.selfcode, b.vip_product,a.assemble_list,a.assemble_idx,a.package_idx ";
	$sql.= "FROM tblbasket a, tblproduct b LEFT OUTER JOIN (SELECT * FROM tblmembergroup_price where group_code = '{$_ShopInfo->memgroup}') c ON b.productcode = c.productcode";
	$sql.= " WHERE 1=1 ";
	$sql.= "AND b.vender='".$vgrp->vender."' ";
	$sql.= "AND a.tempkey='".$_ShopInfo->getTempkey()."' ";
	$sql.= "AND a.productcode=b.productcode order by basketidx desc";
	//exdebug($sql);
	$result=pmysql_query($sql,get_db_conn());

	$vender_sumprice = 0;	//해당 입점업체의 총 구매액
	$vender_delisumprice = 0;//해당 입점업체의 기본배송비 총 구매액
	$vender_deliprice = 0;
	$deli_productprice=0;
	$deli_init = false;

	$arrCriteo = array();

	while($row = pmysql_fetch_object($result)) {
		//달러 -> 원화 변경 
		$row->sellprice = exchageRate($row->sellprice);
		$row->consumerprice = exchageRate($row->consumerprice);
		$row->realprice = exchageRate($row->group_realprice);
		//exdebug($row->realprice);
		if ($row->group_sellprice != "") {
			$row->sellprice = $row->group_sellprice;
			$row->consumerprice = $row->group_consumerprice;
			$row->reserve = $row->group_reserve;
			$row->realprice = $row->group_realprice;
			
			//달러 -> 원화 변경 
			$row->sellprice = exchageRate($row->sellprice);  
			$row->consumerprice = exchageRate($row->consumerprice);
			$row->realprice = exchageRate($row->group_realprice);
			
		}
		
		if (strlen($row->option_price)>0 && $row->opt1_idx==0) {
			$sql = "DELETE FROM tblbasket WHERE tempkey='".$_ShopInfo->getTempkey()."' ";
			$sql.= "AND productcode='".$row->productcode."' AND opt1_idx='".$row->opt1_idx."' ";
			$sql.= "AND opt2_idx='".$row->opt2_idx."' AND optidxs='".$row->optidxs."' ";
			pmysql_query($sql,get_db_conn());

			alert_go("필수 선택 옵션 항목이 있습니다.\\n옵션을 선택하신후 장바구니에\\n담으시기 바랍니다.",$Dir.FrontDir."productdetail.php?productcode=".$row->productcode);
		}
		if(preg_match("/^\[OPTG\d{4}\]$/",$row->option1)){
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
?>
						<tbody>
<?
		$assemble_str="";
		$package_str="";
		$packagelist_str="";
		//######### 옵션에 따른 가격 변동 체크 ###############
		/*
		if (strlen($row->option_price)==0) {
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
		}
		*/
		$row_sellprice = $row->sellprice;
		$option_price = $row->option_price;
		$pricetok=explode(",",$option_price);
		//exdebug($pricetok);
		$selectOptionPrice = $pricetok[$row->opt1_idx-1];
		$price = $selectOptionPrice*$row->quantity;
		if ($selectOptionPrice != "" || $selectOptionPrice != null) {
			//exdebug($row_sellprice."=". $selectOptionPrice ."+". $row_sellprice);
			$row_sellprice = (int)$selectOptionPrice+(int)$row_sellprice;
			//exdebug($row_sellprice."=". $selectOptionPrice ."+". $row_sellprice);
		}
		$total_price_ = $row_sellprice * $row->quantity;
		$tempreserve = getReserveConversion($row->reserve,$row->reservetype,$pricetok[$row->opt1_idx-1],"N");
		
		//######### 옵션에 따른 가격 변동 체크 끝 ############
		//$bf_sumprice += $before_sellprice*$row->quantity;
		$bf_sumprice += $total_price_;
		//$sumprice += $price;
		//exdebug($row->consumerprice);
		//exdebug($selectOptionPrice);
		$sumprice += ((int)$row->consumerprice + (int)$selectOptionPrice) * $row->quantity;
		//exdebug($sumprice);
		//$vender_sumprice += $price;

		### 타임 세일 / 오늘의 특가 가격으로 재 셋팅
		$timesale_sellprice = 0;
		/*
		$timesale_sellprice = getSpeDcPrice($row->productcode);
		if($timesale_sellprice > 0) $sellprice = $timesale_sellprice;
		*/
		######### 상품 특별할인률 적용 ############
		/*
		$dc_data = $product->getProductDcRate($row->productcode);
		if($row->vip_product == 0){
		$salemoney = getProductDcPrice($sellprice,$dc_data[price]);
		$salemoney2 = getProductDcPrice($sellprice,$dc_data[price]);
		}else{
		$salemoney = getProductDcPrice($sellprice,0);
		$salemoney2 = 0;
		}
		$salereserve = getProductDcPrice($sellprice,$dc_data[reserve]);
		*/
		//######### 옵션별 적립금 적용 ############
		/*
		$option_reserve = explode(',',$row->option_reserve);
		if($option_reserve[$row->opt1_idx-1]>0){
			$tempreserve=$option_reserve[$row->opt1_idx-1];
		}
		*/

		//회원 할인율 적용
		/*$before_sellprice=$sellprice;
		$bf_price = $before_sellprice*$row->quantity;
		if($row->vip_product == 0){
		$sellprice = $sellprice - $salemoney;
		}else{
		$sellprice = $sellprice;
		}*/
		$price = $sellprice*$row->quantity;

		//추가 적립금 적용
		$tempreserve+=$salereserve;

		if($row->consumerprice > $before_sellprice){
			$salemoney+=$row->consumerprice-$before_sellprice;
			$before_sellprice=$row->consumerprice;
		}

		//비회원이면 적립금 노출 X
		if(strlen($_ShopInfo->getMemgroup())==0) $tempreserve=0;

		

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

										if (strlen($row->option1)>0 || strlen($row->option2)>0 || strlen($optvalue)>0) {// 특징 및 선택사항이 있으면
											// ###### 특성 #########
											if (strlen($row->option1)>0) {
												$temp = $row->option1;
												$tok = explode(",",$temp);										
											}
											if (strlen($row->option2)>0) {
												$temp2 = $row->option2;
												$tok2 = explode(",",$temp2);																								
											}
											if(strlen($optvalue)>0) {										
											}
										}										
									?>

							<tr>
								<td>
									<form name=form_<?=$formcount?> method=post action="<?=$Dir.FrontDir?>basket.php">
										<input type=hidden name=mode>
										<input type=hidden name=code value="<?=$code?>">
										<input type=hidden name=productcode value="<?=$row->productcode?>">
										<input type=hidden name=orgquantity value="<?=$row->quantity?>">
										<input type=hidden name=orgoption1 value="<?=$row->opt1_idx?>">
										<input type=hidden name=orgoption2 value="<?=$row->opt2_idx?>">
										<input type=hidden name=option1 value="<?=$row->opt1_idx?>">
										<input type=hidden name=option2 value="<?=$row->opt2_idx?>">
										<input type=hidden name=opts value="<?=$row->optidxs?>">
										<input type=hidden name=brandcode value="<?=$brandcode?>">
										<input type=hidden name=assemble_list value="<?=$row->assemble_list?>">
										<input type=hidden name=assemble_idx value="<?=$row->assemble_idx?>">
										<input type=hidden name=package_idx value="<?=$row->package_idx?>">
										<input type=hidden name=quantity value="<?=$row->quantity ?>">		<!-- 기존 수량값을 보관하는 변수이므로 건들면 안됨 -->

										<input type=hidden name=temp_quantity value="<?=$row->quantity*$row_sellprice ?>"/>		
			
										<input type=hidden name=temp_row_sellprice value="<?=$row_sellprice?>">

										<input name="checkProduct" class="checkProduct" value="<?=$row->basketidx?>" type="checkbox" checked/>
										<!--
										<input type=hidden name=temp_option_price value="<?=$selectOptionPrice?>">			
										<input type=hidden name=temp_option value="<?=$tok[$row->opt1_idx]?>">
										<input type=hidden name=temp_reserve value="<?=$row->reserve?>">
										<input type=hidden name=temp_row_sellprice value="<?=$row_sellprice?>">
										<input type=hidden name=temp_product_name value="<?=viewproductname($productname,$row->etctype,$row->selfcode,$row->addcode) ?><?=$bankonly_html?><?=$setquota_html?><?//=$deli_str?>">
										<input type=hidden name=temp_tinyimage value="<?=$row->tinyimage?>">
											
										<input type=hidden name=quantity value="<?=$row->quantity ?>">
										-->
									</form>
								</td>
								<td class="info" width="500">
									<a href="<?=$Dir.FrontDir?>productdetail.php?productcode=<?=$row->productcode?>">
										<?if(strlen($row->tinyimage)!=0 && file_exists($Dir.DataDir."shopimages/product/".$row->tinyimage)){?>
											<?$file_size=getImageSize($Dir.DataDir."shopimages/product/".$row->tinyimage);?>
											<img src="<?=$Dir.DataDir?>shopimages/product/<?=$row->tinyimage?>" <?if($file_size[0]>=$file_size[1]){ echo " width='126'"; }else{ echo "height='126'"; }?>>
										<?}else if(strlen($row->tinyimage)!=0 && file_exists($Dir.$row->tinyimage)){?>
											<?$file_size2=getImageSize($Dir.$row->tinyimage);?>
											<img src="<?=$Dir.$row->tinyimage?>" <?if($file_size2[0]>=$file_size2[1]){ echo " width='126'"; }else{ echo "height='126'"; }?>>
										<?}else{?>
											<img src="<?=$Dir?>images/no_img.gif" width="126">
										<?}?>
									</a>
									<span class="name">
									<a href="<?=$Dir.FrontDir?>productdetail.php?productcode=<?=$row->productcode?>">
										<?=viewproductname($productname,$row->etctype,$row->selfcode,$row->addcode) ?><?=$bankonly_html?><?=$setquota_html?><?//=$deli_str?>
									</a>
									<br>
									<?
										if (strlen($row->option1)>0 || strlen($row->option2)>0 || strlen($optvalue)>0) {// 특징 및 선택사항이 있으면
											// ###### 특성 #########
											if (strlen($row->option1)>0) {
												$temp = $row->option1;
												$tok = explode(",",$temp);
												echo "<span class=\"option\">옵션 : ".$tok[$row->opt1_idx]."</span>";
														
											}
											if (strlen($row->option2)>0) {
												$temp2 = $row->option2;
												$tok2 = explode(",",$temp2);
												echo "<span class=\"option\">&nbsp;/&nbsp;".$tok2[$row->opt2_idx]."</span>";
													
											}
											if(strlen($optvalue)>0) {
												echo $optvalue."</font>\n";
											}
										}

										if (strlen($package_str)>0) { // 패키지 정보
									?>
									<img src="<?=$Dir?>images/common/icn_package.gif" border="0" align="absmiddle" style="width:36px;height:16px"> <?=(strlen($package_str)>0?$package_str:"")?>
									<?
										}
										if (strlen($packagelist_str)>0) { // 패키지 정보
											echo "<table><tr>".$packagelist_str."</tr></table>";
										}
										if (strlen($assemble_str)>0) { // 코디 정보
											echo "<table><tr>".$assemble_str."</tr></table>";
										}
									?>
									</span>
								</td>
								<td><strong><?=number_format($row_sellprice)?></strong></td>
								<td>
									<form name=form_v_<?=$formcount?>>
									<?$formcount++;?>
									<div class="qty">
										<!--<input type="text" name="quantity_v" value="<?=$row->quantity ?>" title="수량을 입력하세요." readonly/>-->
										<input type="text" name="quantity_v" value="<?=$row->quantity ?>" title="수량을 입력하세요." readonly/>
										<a href="javascript:change_quantity('up',<?=$formcount-1;?>)"><img src="../img/button/cart_qty_up.gif" alt="수량 1개 더하기" style="cursor:pointer" /></a>
										<a href="javascript:change_quantity('dn',<?=$formcount-1;?>)"><img src="../img/button/cart_qty_down.gif" alt="수량 1개 빼기" style="cursor:pointer" /></a>
									</div>
									<div class="updatebtn">
										<a href="javascript:CheckForm('upd',<?=$formcount-1?>)"><img src="../image/cart/c_modify_btn.gif" type="image" /></a>
									</div>
									</form>
								</td>
								<!--<td><?=number_format($salemoney-$salemoney2)?></td>-->
								<!--<td><?=number_format($salemoney2)?></td>-->
								<td><strong><?=number_format($total_price_)?></strong></td>
								<td class="point"  width="110"><img src="../img/icon/cart_point_icon.gif" alt="적립금" /><?=number_format($tempreserve*$row->reserve)?></td>
								<td class="button" width="70">
									<a href="javascript:;" class = 'CLS_DirectBuyBtn' target="_self"><img src="../img/button/cart_buy_btn.gif" alt="바로구매" /></a>
									<a href="javascript:;" class = 'CLS_WishlistBtn' target="_self"><img src="../img/button/cart_wishlist_btn.gif" alt="위시리스트" /></a>
									<a href="javascript:CheckForm('del_chk','<?=$row->basketidx?>')"><img src="../img/button/cart_remove_btn.gif" alt="삭제" /></a>
								</td>
							</tr>
							<!--<script language='javascript'>
								_A_amt[i]='<?=$price?>';
								_A_nl[i]='<?=$row->quantity ?>';
								_A_pl[i]='<?=$row->productcode?>';
								_A_pn[i]='<?=$row->productname?>';
								_A_ct[i]='';
								i++;
							</script>-->
						</tbody>
						<? $arrCriteo[$formcount][code] = $row->productcode; ?>
						<? $arrCriteo[$formcount][price] = $price/$row->quantity; ?>
						<? $arrCriteo[$formcount][ea] = $row->quantity; ?>
<?
	}
}

		pmysql_free_result($result);

		$vender_deliprice=$deli_productprice;

		if($_vender) {
			if($_vender->deli_price>0) {
				if($_vender->deli_pricetype=="Y") {
					$vender_delisumprice = $vender_sumprice;
				}

				if ($vender_delisumprice<$_vender->deli_mini && $deli_init) {
					$vender_deliprice+=$_vender->deli_price;
				}
			} else if(strlen($_vender->deli_limit)>0) {
				if($_vender->deli_pricetype=="Y") {
					$vender_delisumprice = $vender_sumprice;
				}
				if($deli_init) {
					$delilmitprice = setDeliLimit($vender_delisumprice,$_vender->deli_limit);
					$vender_deliprice+=$delilmitprice;
				}
			}
		} else {
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
		}
		$deli_price+=$vender_deliprice;
		//해당 입점업체의 상품구매액, 배송비 등의 결제금액을 구한다.

		pmysql_free_result($res);

		if($cnt==0) {
?>
							<tr><td colspan="9">쇼핑하신 상품이 없습니다.</td></tr>
<?
		}
?>
						<tfoot>
							<tr>
								<td colspan="9" bgcolor="#fafafa">
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
					<!-- // 담은 상품 -->

					<div class="button_area">
						<div class="button_left">
							<a href="javascript:;" target="_self"><img src="../img/button/cart_select_all_btn.gif" alt="전체선택" class = 'allCheckButton'/></a>
							<a href="javascript:;" target="_self"><img src="../img/button/cart_select_cancel_btn.gif" alt="선택해제" class = 'allUnCheckButton'/></a>

<?php if(isdev()){ ?>
							<a href="javascript:basket_clear();" target="_self"><img src="../img/button/cart_delete_all_btn.gif" alt="전체삭제" class = ''/></a>

<?php } ?>

						</div>
						<div class="button_right">
						
							<a href="javascript:;"  class="estimate_sheet  btn_B wide">견적서 출력</a>
							<!-- 추가--> 

							<a href="../" target="_self" class="btn_B wide">쇼핑 계속하기</a>
							<a href="<?=$Dir.FrontDir?>login.php?buy=1&chUrl=<?=urlencode($Dir.FrontDir."order.php")?>"></a>
							<a href="javascript:;" class="selectProduct btn_B wide">선택상품 주문하기</a>
							<a href="javascript:;" class="allBuyProduct btn_A wide">전체상품 주문하기</a>
						</div>
					</div>
					<?
						$sqlVcount = "
							SELECT a.* ,c.sellprice as group_sellprice,c.consumerprice as group_consumerprice 
							FROM tblproduct a
							LEFT OUTER JOIN tblproductgroupcode b ON a.productcode=b.productcode 
							LEFT OUTER JOIN (SELECT * FROM tblmembergroup_price WHERE group_code = '{$_ShopInfo->memgroup}' ) c 
							ON a.productcode = c.productcode
							WHERE a.display = 'Y' 
							order by a.vcnt desc 
							limit 4 offset 0 ";
						$resultVcount=pmysql_query($sqlVcount,get_db_conn());
					?>
					<!-- 추천 -->
					<div class="recomm_area">
						<h3>추천!<span>다른 고객님들이 많이 구입하신 상품들</span></h3>
						<ul >
							<?
								while($row=pmysql_fetch_object($resultVcount)) {
									if ($row->group_sellprice) {
									$row->sellprice = $row->group_sellprice;
									}
									if ($row->group_consumerprice) {
										$row->consumerprice = $row->group_consumerprice;
									}
									$sellPrice = 0;
									$optcode = substr($row->option1,5,4);
									$miniq = 1;
									if (ord($row->etctype)) {
										$etctemp = explode("",$row->etctype);
										for ($i=0;$i<count($etctemp);$i++) {
											if (strpos($etctemp[$i],"MINIQ=")===0) $miniq=substr($etctemp[$i],6);
										}
									}

									if(strlen($dicker=dickerview($row->etctype,number_format($row->sellprice),1))>0){
										$sellPrice = $dicker;
									} else if(strlen($optcode)==0 && strlen($row->option_price)>0) {
										$sellPrice = $row->sellprice;
									} else if(strlen($optcode)>0) {
										$sellPrice = $row->sellprice;
									} else if(strlen($row->option_price)==0) {
										if($row->assembleuse=="Y") {
											$sellPrice = ($miniq>1?$miniq*$row->sellprice:$row->sellprice);
										} else {
											$sellPrice = $row->sellprice;
										}
									}
							?>
							<li style="float:left; margin-left:20px; width:240px; height:204px; overflow:hidden;">
							<!--
								<div class="goods_img">
									<div class="layer_goods_info">
										<span class="subject"><?=$row->productname?></span>
										<span class="price_oiginal"><?=number_format(exchageRate($row->consumerprice))?>원</span>
										<span class="price"><?=number_format(exchageRate($row->sellprice))?>원</span>
										<p class="btn">
											<a href="<?=$Dir.FrontDir?>productdetail.php?productcode=<?=$row->productcode?>" class="btn_C">자세히보기</a><a  class="btn_C  busket" alt="<?=$row->productcode?>">장바구니</a>
										</p>
									</div>
									<p class="icon">
									<?if(is_file($Dir."img/icon/".$row->icon)){?>
										<img src="../img/icon/<?=$row->icon?>" alt="" />
									<?}?>
									</p>
									<?if(is_file("../data/shopimages/product/".$row->maximage)){?>
										<img width=240px; height=184px; src="<?=$Dir.DataDir?>shopimages/product/<?=$row->maximage?>" alt="" />
									<?}else{?>
										<img width=240px; height=184px; src="../images/no_img.gif" alt="" />
									<?}?>
								</div>
								-->
								<div class="goods_img"  alt="<?=$row->productcode?>" >
									<div class="layer_goods_info"  style="width:240px; height:204px; overflow:hidden; z-index:30;">
										<span class="subject" style="text-indent:10px; text-align:center;"><?=$row->productname?></span>
										<span class="price_oiginal" style="color:#999; font:12px dotum; text-decoration:line-through; margin-top:13px"><?=number_format(exchageRate($row->consumerprice))?>원</span>
										<span class="price" ><?=number_format(exchageRate($row->sellprice))?>원</span>
										<p class="btn" style="margin-top:20px; text-align:center;">
											<a class="btn"  style="display:inline; color:#ccc; height:24px; width:76px; border:1px solid #999; text-align:center; border-radius:2px; margin-right:8px; font:11px/26px dotum;"; href="<?=$Dir.FrontDir?>productdetail.php?productcode=<?=$row->productcode?>">자세히보기</a>
											<a class="busket" style="display:inline; color:#ccc; height:24px; width:76px; border:1px solid #999; text-align:center; border-radius:2px; margin-right:8px; font:11px/26px dotum"; alt="<?=$row->productcode?>"  href="javascript:CheckForm('','<?=$row->productcode?>');">장바구니</a>
										</p>
									</div>
									<p class="icon">
									<?if(is_file($Dir."img/icon/".$row->icon)){?>
										<img style="max-width:240px;"  src="<?=$Dir?>img/icon/<?=$row->icon?>" alt="" />
									<?}?>
									</p>
									<?if(is_file($Dir.DataDir."shopimages/product/".$row->maximage)){?>
										<img style="max-width:240px;" src="<?=$Dir.DataDir?>shopimages/product/<?=$row->maximage?>" alt="" />
									<?}else{?>
										<img style="max-width:240px; width:240px;" src="<?=$Dir?>images/no_img.gif" alt="" />
									<?}?>
								</div>						
							<!--
								<a href="../front/productdetail.php?productcode=<?=$row->productcode?>" target="_self">
									<?if (strlen($row->maximage)>0 && file_exists($Dir.DataDir."shopimages/product/".$row->maximage)) { ?>
										<img src="<?=$Dir.DataDir."shopimages/product/".urlencode($row->maximage)?>" width = '180'>
									<?}else if(strlen($row->maximage)>0 && file_exists($Dir.$row->maximage)){?>
										<img src="<?=$Dir.$row->maximage?>" width = '180'>
									<?}else {?>
										<img src="<?=$Dir?>images/no_img.gif" border="0" align="center" width = '180'>
									<?}?>
									<span class="info_box">
										<span class="name"><?=$row->productname?></span>
										<?if($row->consumerprice && $row->consumerprice > $sellPrice){?>
										<span class="price"><del><?=number_format(exchageRate($row->consumerprice))?></del><strong><?=number_format(exchageRate($sellPrice))?>원</strong></span>
										<?}else{?>
										<span class="price"><strong><?=number_format(exchageRate($sellPrice))?>원</strong></span>
										<?}?>-->
										<!--<span class="point"><img src="http://nexolve.ajashop.co.kr/img/icon/icon_p.gif" alt="포인트" /><?=number_format($row->reserve)?></span>-->
								<!--	</span>
								</a>-->
							
							</li>
							<?
								}
							?>
							
						</ul>
					</div>
					<!-- // 추천 -->

					<!-- 무이자 할부혜택 -->
					<div class="benefit_area hide">
						<h3>무이자 할부혜택</h3>
						<!-- (D) 카드사 로고 이미지의 높이는 20px 입니다. -->
						<ul>
							<li>
								<img src="../img/test/cart_benefit_card1.gif" alt="삼성카드" /><br />
								<span>2, 3개월 <strong>(10만원↑)</strong></span>
							</li>
							<li>
								<img src="../img/test/cart_benefit_card2.gif" alt="국민카드" /><br />
								<span>2, 3개월 <strong>(10만원↑)</strong></span>
							</li>
							<li>
								<img src="../img/test/cart_benefit_card3.gif" alt="신한카드" /><br />
								<span>2, 3개월 <strong>(10만원↑)</strong></span>
							</li>
							<li>
								<img src="../img/test/cart_benefit_card4.gif" alt="현대카드" /><br />
								<span>2, 3, 4, 5개월 <strong>(10만원↑)</strong></span>
							</li>
							<li>
								<img src="../img/test/cart_benefit_card1.gif" alt="삼성카드" /><br />
								<span>2, 3개월 <strong>(10만원↑)</strong></span>
							</li>
							<li>
								<img src="../img/test/cart_benefit_card2.gif" alt="국민카드" /><br />
								<span>2, 3개월 <strong>(10만원↑)</strong></span>
							</li>
							<li>
								<img src="../img/test/cart_benefit_card3.gif" alt="신한카드" /><br />
								<span>2, 3개월 <strong>(10만원↑)</strong></span>
							</li>
							<li>
								<img src="../img/test/cart_benefit_card4.gif" alt="현대카드" /><br />
								<span>2, 3, 4, 5개월 <strong>(10만원↑)</strong></span>
							</li>
						</ul>
					</div>
					<!-- // 무이자 할부혜택 -->
				</div>

			</div>
			<!-- //메인 컨텐츠 -->
		</td>
	</tr>
	</table>
</div>
<!-- 장바구니로 이동 -->
<form name=form1 id = 'ID_goodsviewfrm' method=post action="<?=$Dir.FrontDir?>basket.php">
	<input type="hidden" name="productcode"></input> 
</form>
<!--견적서 출력-->
<form name=estimate_sheet_form  method=post>
	<input type=hidden name=strBasket></input>
	<input type=hidden name=rowid value=<?=$_ShopInfo->memname?>></input>
</form>
<!---->
<?
	$strCriteo = '';
	if(count($arrCriteo)>0){
		$arrCriteoReSettings = array();
		foreach($arrCriteo as $dc){
			$arrCriteoReSettings[] = '{ id: "'.$dc['code'].'", price: '.$dc['price'].', quantity: '.$dc['ea'].' }';
		}
		$strCriteo = implode(", ", $arrCriteoReSettings);
?>

<!--  장바구니 페이지 트래커  ---->
<script type="text/javascript" src="//static.criteo.net/js/ld/ld.js" async="true"></script>
<script type="text/javascript">
	window.criteo_q = window.criteo_q || [];
	window.criteo_q.push(
			{ event: "setAccount", account: 15622 },
			{ event: "setEmail", email: ["<?=$_ShopInfo->mememail?>"] },
			{ event: "setSiteType", type: "d" },
			{ event: "viewBasket", item: [<?=$strCriteo?>]}
	);
</script>
<?
	}
?>
