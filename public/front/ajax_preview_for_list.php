<?php 
header("Content-Type: text/html;charset=euc-kr");
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
include_once($Dir."lib/product.class.php");
$product = new PRODUCT();
//sleep(5);
$productcode = $_REQUEST['productcode'];

//exdebug($productcode);
$_psql = "SELECT a.*, c.consumerprice as group_consumerprice, c.sellprice as group_sellprice FROM tblproduct a LEFT OUTER JOIN (SELECT * FROM tblmembergroup_price where group_code = '{$_ShopInfo->memgroup}') c ON a.productcode = c.productcode  WHERE a.productcode = '{$productcode}' ";
//exdebug($_psql);
$_pdata = pmysql_fetch_object(pmysql_query($_psql));

//환율
$_pdata->sellprice = exchageRate($_pdata->group_sellprice);
$_pdata->consumerprice = exchageRate($_pdata->group_consumerprice);

//exdebug($_pdata);
$facebook_msg = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER[REQUEST_URI];	
$facebookurl = 'http://www.facebook.com/sharer.php?u='.urlencode($facebook_msg.'&time='.time());
$twitterMsg = "[".$_data->shopname."] ".$_pdata->productname;
$twitterUrl = 'https://twitter.com/intent/tweet?url='.$faceboolMallUrl.'&text='.urlencode(iconv("EUC-KR","UTF-8",$twitterMsg));

//배송비 세팅

$staticDeliType = "0";
$deliState_ = $product->getDeliState($_pdata);

$item_deli_price = $_pdata->deli_price; // 아이템별 배송비 금액
$shop_deli_price = $_data->deli_basefee; // 전체 배송비 설정 금액
$shop_constant_deli_price = $_data->deli_miniprice; // 얼마 이상 무료 기준 금액
$deli_state = $deliState_[itemState];
//$deli_tpye = $_pdata->deli.$_data_deli;
$deli_tpye_common = "";
$deli_tpye_item = "";
$deli_price = 0;
switch ($deli_state) {
	case "1" : $deli_price = $item_deli_price;
		break;
	case "2" : $deli_price = $item_deli_price;
		break;
	case "3" : $deli_price = 0;
		break;
	case "4" : $deli_price = 0;
		break;
	case "5" : $deli_price = 0;
		break;
	case "6" : $deli_price = 0;
		break;
	case "7" : $deli_price = $shop_deli_price;
		break;
	case "8" : $deli_price = $shop_deli_price;
		break;
	default : $deli_price = 0;
}

?>
<div class="popup_preview">
	<a href="javascript:;" class="pop_close_btn"></a>
	
	<!-- 상세정보 -->
	<div class="goods_view_detail_wrap pop_goods_view_detail">
	
		<!-- 왼쪽 썸네일 -->
		<div class="left_thumb">

	<?php 	if(is_file($Dir.DataDir."shopimages/product/".$_pdata->maximage)) { 	?>
				<img src="<?=$Dir.DataDir."shopimages/product/".$_pdata->maximage?>" alt="" style="width: 330px;" />
	<?php	}else if(is_file($Dir.$_pdata->maximage)) { 	?>
				<img src="<?=$Dir.$_pdata->maximage?>" alt="" style="width: 285px;" />
	<?php 	}else { 	?>
				<img src="<?=$Dir?>images/no_img.gif" alt="" style="width: 285px;" />
	<?php	}	?>
	
			<div class="share_icon w450 hide">
				<span>공유하기</span>
				<a href="<?=$facebookurl?>"><img src="../img/icon/icon_share_f.gif" alt="페이스북" class="facebook" /></a>
				<a href="<?=$twitterUrl?>"><img src="../img/icon/icon_share_t.gif" alt="트위터" /></a>
				<!--<a href=""><img src="../img/icon/icon_share_k.gif" alt="카카오톡" /></a>-->
			</div>
		</div><!-- //div.left_thumb -->
	
		<!-- 센터정보영역 -->
		<div class="center_info_wrap">			
			<!-- <form name=form1 id = 'ID_goodsviewfrm' method=post action="<?=$Dir.FrontDir?>basket.php"> -->
			<form name=form1 id = 'ID_goodsviewfrm' method=post action="<?=$Dir.FrontDir?>confirm_basket.php">
			<p class="sub_txt"><?=$_pdata->mdcomment?></p>
			<table class="goods_detail" width="100%">
				<caption><?=$_pdata->productname?></caption>
				<colgroup><col style="width:110px"/><col style="width:auto"/></colgroup>
				<tr>
					<th>시중가</th>
					<td style="text-align:left"><strike><?=number_format($_pdata->consumerprice)?></strike>원</td>
				</tr>
				<?
					$reserveconv=getReserveConversion($_pdata->reserve,$_pdata->reservetype,$_pdata->sellprice,"Y");
					$SellpriceValue=0;
					//if(strlen($dicker=dickerview($_pdata->etctype,number_format($_pdata->sellprice),1))>0){
					if(false){
				?>
				<tr>
					<th>판매가</th>
					<td class="price" style="text-align:left"><span></span>원
					</td>
				</tr>
				<?
						$prdollarprice="";
						$priceindex=0;
					//} else if(strlen($optcode)==0 && strlen($_pdata->option_price)>0) {
					} else if(false) {
						$option_price = $_pdata->option_price;
						$option_consumer = $_pdata->option_consumer;
						$option_reserve = $_pdata->option_reserve;
						$pricetok=explode(",",$option_price);
						$consumertok=explode(",",$option_consumer);
						$reservetok=explode(",",$option_reserve);
						$priceindex = count($pricetok);
						for($tmp=0;$tmp<=$priceindex;$tmp++) {
							$pricetokdo[$tmp]=number_format($pricetok[$tmp]/$ardollar[1],2);
							$spricetok[$tmp]=number_format($pricetok[$tmp]);
							$pricetok[$tmp]=number_format(getProductSalePrice($pricetok[$tmp], 0));
							if(!$consumertok[$tmp]){
								$consumertok[$tmp] = 0;
							}else{
								$consumertok[$tmp]=number_format($consumertok[$tmp]);
							}
							$reservetok[$tmp]=number_format($reservetok[$tmp]);
						}
				?>
				<tr>
					<th>판매가</th>
					<td class="price" style="text-align:left"><?=number_format(str_replace(",","",$pricetok[0]))?>원
					</td>
				</tr>
				<?
						$SellpriceValue=str_replace(",","",$pricetok[0]);
					//} else if(strlen($optcode)>0) {
					} else if(false) {
				?>
				<tr>
					<th>판매가</th>
					<td class="price" style="text-align:left"><?=number_format($_pdata->sellprice)?>원
						<input type=hidden name=price value="<?=number_format($_pdata->sellprice)?>">
						<input type=hidden name=sprice value="<?=number_format($_pdata->sellprice)?>">
						<input type=hidden name=consumer value="<?=number_format($_pdata->consumerprice)?>">
						<input type=hidden name=o_reserve value="<?=number_format($_pdata->option_reserve)?>">
					</td>
				</tr>
				<?
						$SellpriceValue=$_pdata->sellprice;
					//} else if(strlen($_pdata->option_price)==0) {
					} else if(true) {
						if($_pdata->assembleuse=="Y") {
				?>
				<tr>
					<th>판매가</th>
					<td class="price" style="text-align:left">
						<?=number_format(($miniq>1?$miniq*$_pdata->sellprice:$_pdata->sellprice))?>원
						<input type=hidden name=price value="<?=number_format(($miniq>1?$miniq*$_pdata->sellprice:$_pdata->sellprice))?>">
						<input type=hidden name=sprice value="<?=number_format($_pdata->sellprice)?>">
						<input type=hidden name=consumer value="<?=number_format(($miniq>1?$miniq*$_pdata->consumerprice:$_pdata->consumerprice))?>">
						<input type=hidden name=o_reserve value="<?=number_format(($miniq>1?$miniq*$_pdata->option_reserve:$_pdata->option_reserve))?>">
					</td>
				</tr>
				<?
					$SellpriceValue=($miniq>1?$miniq*$_pdata->sellprice:$_pdata->sellprice);
					} else {
					if ($_pdata->group_sellprice) {
						$_pdata->sellprice = $_pdata->group_sellprice;
						//환율
						$_pdata->sellprice = exchageRate($_pdata->group_sellprice);
					}
					if ($_pdata->group_consumerprice) {
						$_pdata->consumerprice = $_pdata->group_consumerprice;
						//환율
						$_pdata->consumerprice = exchageRate($_pdata->group_consumerprice);
					}

					//exdebug($_pdata);
				?>
				<tr>
					<th>판매가</th>
					<td class="price" style="text-align:left">
						<?=number_format($_pdata->sellprice)?>원
						<input type=hidden name=price value="<?=number_format($_pdata->sellprice)?>">
						<input type=hidden name=ID_sellprice id="ID_sellprice" value="<?=$_pdata->sellprice?>">
						<input type=hidden name=sprice value="<?=number_format($_pdata->sellprice)?>">
						<input type=hidden name=consumer value="<?=number_format($_pdata->consumerprice)?>">
						<input type=hidden name=o_reserve value="<?=number_format($_pdata->option_reserve)?>">
					</td>
				</tr>

				<?
							$SellpriceValue=$_pdata->sellprice;
						}
						$priceindex=0;
					}
				?>
				<?
					if($couponDownLoadFlag){
						if($goods_sale_type <= 2){
							$couponDcPrice = ($SellpriceValue*$goods_sale_money)*0.01;
							$couponDcPrice = ($couponDcPrice / pow(10, $goods_amount_floor)) * pow(10, $goods_amount_floor);
							$goods_dc_coupong = number_format($goods_sale_money)."%";
						}else{
							$couponDcPrice = $goods_sale_money;
							$goods_dc_coupong = number_format($goods_sale_money)."원";
						}
						if($goods_sale_max_money && $goods_sale_max_money < $couponDcPrice){
							$couponDcPrice = $goods_sale_max_money;
						}
						$coumoney = $couponDcPrice;
					}
				?>


				<?
					
					if(strlen($_pdata->option1)>0) {
						$temp = $_pdata->option1;
						$option1Arr = explode(",",$temp);
						$tok = explode(",",$temp);
						$optprice = explode(",", $_pdata->option_price);
						
						$optcode = "";
						if($_pdata->optcode){
							$optcode = explode(",", $_pdata->optcode);
						}
						if (sizeof($optprice)!= sizeof($option1Arr) ) {
							for($i=0; $i<sizeof($option1Arr); $i++){
								$optprice[$i] = $optprice[$i]=="" ? "0":$optprice[$i];
							}
						}
						
						$count=count($tok);

						if ($priceindex!=0) {
							$onchange_opt1="onchange=\"change_price(1,document.form1.option1.selectedIndex-1,";
							if(strlen($_pdata->option2)>0) $onchange_opt1.="document.form1.option2.selectedIndex-1";
							else $onchange_opt1.="''";
							$onchange_opt1.=")";
							$onchange_opt1.="\"";
						} else {
							$onchange_opt1="onchange=\"change_price(0,document.form1.option1.selectedIndex-1,";
							if(strlen($_pdata->option2)>0) $onchange_opt1.="document.form1.option2.selectedIndex-1";
							else $onchange_opt1.="''";
							$onchange_opt1.=")";
							$onchange_opt1.="\"";
						}
						$optioncnt = explode(",",ltrim($_pdata->option_quantity,','));
						if (sizeof($optioncnt) > 1) {
							for ($i=0; $i<sizeof($optioncnt);$i++) {
								if ($optioncnt[$i] == "") {
									$optioncnt[$i] = "0";
								}
							}
						}
				?>
				<?if($_pdata->reserve>0){
					//$getReserveConversion = getReserveConversion($_pdata->reserve, $_pdata->reservetype, $_pdata->sellprice,'Y');
				?>
				<tr>
					<th>적립금</th>
					<!--<td><?=number_format($getReserveConversion)?>원</td>-->
					<td style="text-align:left"><?=number_format($_pdata->consumer_reserve)?>원</td>
				</tr>
				<?}?>
				<tr>
					<td colspan="3" class="line_1px" ><em></em></td>
				</tr>
				<tr>
					<th><?=$tok[0]?></th>
					<td style="text-align:left">
						<div class="select_type" style="width:180px;z-index:10;">
							<select name="option1" id="option1" alt='<?=$tok[0]?>'>
							<option value="">옵션을 선택해주세요..</option>
								<?for($i=1;$i<$count;$i++) {?>
									<?if(strlen($tok[$i]) > 0) {?>
										<option value="<?=$i?>">
										<?if(strlen($_pdata->option2) == 0 && $optioncnt[$i-1] == "0"){?>
											<span class='option_strike'><?=$tok[$i]." [품절]"?></span>
										<?}else{
											$tempopt = $optprice[$i-1] == "" ? "0": $optprice[$i-1];
										?>
											<span><?=$tok[$i]?></span>&nbsp;(<?=number_format($tempopt)?>원)
										<?}?>
									<?}?>
									</option>
								<?}?>
							</option>
						</div>
					</td>
				</tr>
				<?
					}
				?>

				<?
					$onchange_opt2="";
					
					if(strlen($_pdata->option2)>0) {
						$temp = $_pdata->option2;
						$option2Arr = explode(",",$temp);
						$tok = explode(",",$temp);
						$count2=count($tok);
						$onchange_opt2.="onchange=\"change_price(0,";
						if(strlen($_pdata->option1)>0) $onchange_opt2.="document.form1.option1.selectedIndex-1";
						else $onchange_opt2.="''";
						$onchange_opt2.=",document.form1.option2.selectedIndex-1)\"";
				?>
				<tr>
					<th><?=$tok[0]?></th>
					<td style="text-align:left">
						<div class="select_type" style="width:180px;z-index:10;">
							<select name="option2" id="option2" alt='<?=$tok[0]?>'>
							<option value="">옵션을 선택해주세요.</option>
								<?for($i=1;$i<$count;$i++) {?>
									<?if(strlen($tok[$i]) > 0) {?>
										<option value="<?=$i?>">
										<?if(strlen($_pdata->option2) == 0 && $optioncnt[$i-1] == "0"){?>
											<span class='option_strike'><?=$tok[$i]." [품절]"?></span>
										<?}else{?>
											<!-- (<?=number_format($optprice[$i-1])?>원) -->
											<?=$tok[$i]?>
										<?}?>
									<?}?>
									</option>
								<?}?>
							</option>
						</div>
					</td>
				</tr>
				<?
					}
				?>
			<?
				
				
				/*할인율 계산*/
				if($SellpriceValue != $_pdata->consumerprice && $_pdata->consumerprice > 0){
					$priceDcPercent = floor(100 - ($SellpriceValue / $_pdata->consumerprice * 100));
				}else{
					$priceDcPercent = 0;
				}
			?>
			<input type = 'hidden' value = '<?=$priceDcPercent?>' id = 'ID_priceDcPercent'>
				
				<tr><td colspan="2" class="line"><div class="line"></div></td></tr>
				<tr>
					<th>배송비</th>
					<td id="td_deli_price" style="text-align:left">
						<p id="deli_price_result"></p>
						<P><?=$deliState_[msg]?></P>
					</td>
					<input id="deli_price" type="hidden" value="<?=$deli_price?>" />
					<!--<input id="deli_type" type="text" value="<?=$deli_type?>" />-->
					<input id="deli_type" type="hidden" value="<?=$deli_state?>" />
					<input id="deli_miniprice" type="hidden" value="<?=$_data->deli_miniprice?>" />
				</tr>
				<?if(sizeof($option1Arr)<1){?>
				<tr>
					<th>수량1</th>
					<td style="text-align:left">
						<div class="ea_select">
								<input type="text" name="quantity" id="quantity" value="1" onkeyup="quantityKeyUp(this,<?=exchageRate(1)?>)" class="amount" size="2">
								<a href="javascript:change_quantity('up',<?=exchageRate(1)?>)" class="btn_plus"></a>
								<a href="javascript:change_quantity('dn',<?=exchageRate(1)?>)" class="btn_minus"></a>
						</div>
					</td>
				</tr>
				<?}?>
				<?//if( sizeof($option1Arr)>1 && sizeof($option2Arr)<1 ){?>
				<?if( false ){?>
				<tr>
					<th>수량</th>
					<td style="text-align:left">
						<div class="ea_select">
							<input type="text" name="quantity" id="quantity" value="1" onkeyup="strnumkeyup(this)" class="amount" size="2">
							<a href="javascript:change_quantity('up','<?=exchageRate(1)?>')" class="btn_plus"></a>
							<a href="javascript:change_quantity('dn','<?=exchageRate(1)?>')" class="btn_minus"></a>
						</div>
					</td>
				</tr>
				<?}?>
				<script>
				

					var optpriceArr_;
					var option1Arr_;
					var option2Arr_;
					var quantity_ = "<?=$_pdata->option_quantity?>";
					quantity_ = quantity_.split(',');
					var quantityArr_ = new Array();
					quantityArr_[0] = new Array(10);
					quantityArr_[1] = new Array(10);
					quantityArr_[2] = new Array(10);
					quantityArr_[3] = new Array(10);
					quantityArr_[4] = new Array(10);
					<?php
						
						
						$str1 = "0";
						if (sizeof($optprice)>0){
							$str1 = "[";
							for ($i=0 ;$i<sizeof($optprice) ;$i++ ){
								if ($optprice[$i] == ""){ $optprice[$i] = "0";}
								$str1 .= "'".$optprice[$i]."',";
							}
							$str1 .= "]";
						}
						
						$str2 = "0";
						if (sizeof($option1Arr)>0){
							$str2 = "[";
							for ($i=0 ;$i<sizeof($option1Arr) ;$i++ ){
								if($i == 0){continue;}
								$str2 .= "'".$option1Arr[$i]."',";
							}
							$str2 .= "]";
						}
						$str3 = "0";
						if (sizeof($option2Arr)>0){
							$str3 = "[";
							for ($i=0 ;$i<sizeof($option2Arr) ;$i++ ){
								if($i == 0){continue;}
								$str3 .= "'".$option2Arr[$i]."',";
							}
							$str3 .= "]";
						}
					?>
					var optpriceArr_ = <?=trim($str1)?>;
					var option1Arr_ = <?=trim($str2)?>;
					var option2Arr_ = <?=trim($str3)?>;
					var sellprice = <?=$_pdata->sellprice?>;
					
					$(document).ready(function(){
						<?php 
							if (sizeof($option1Arr)>0){
						?>
								setTotalPrice();
						<?
							} else {
						?>
							setDeliPrice(sellprice,"1");
						<?
							}
						?>
						var d1 = 0; 
						var d2 = 0;
						
						for (var i=0;i<quantity_.length ;i++ ){
							if (i>=1 && i<=10){
								quantityArr_[0][d2] = quantity_[i] == "" ? "0":quantity_[i];
								d2++;
								if (d2>9){ d2=0; }
							}else if (i>=11 && i<=20){
								quantityArr_[1][d2] = quantity_[i] == "" ? "0":quantity_[i];
								d2++;
								if (d2>9){ d2=0; }
							}else if (i>=21 && i<=30){
								quantityArr_[2][d2] = quantity_[i] == "" ? "0":quantity_[i];
								d2++;
								if (d2>9){ d2=0; }
							}else if (i>=31 && i<=40){
								quantityArr_[3][d2] = quantity_[i] == "" ? "0":quantity_[i];
								d2++;
								if (d2>9){ d2=0; }
							}else if (i>=41 && i<=50){
								quantityArr_[4][d2] = quantity_[i] == "" ? "0":quantity_[i];
								d2++;
								if (d2>9){ d2=0; }
							}
						}
						
					});

function change_quantity(gbn,exchange) {
	tmp=document.form1.quantity.value;
	if(gbn=="up") {
		tmp++;
	} else if(gbn=="dn") {
		if(tmp>1) tmp--;
	}
	var cons_qu = $("#constant_quantity").val();
	if (cons_qu != "" && cons_qu != "0"){
		if (cons_qu<tmp){
			alert('재고량이 부족 합니다.');
			return;
		}
	} else if(cons_qu == "0") {
		alert('품절 입니다.');
		return;
	}
	<?php  if($_pdata->assembleuse=="Y") { ?>
		if(getQuantityCheck(tmp)) {
			if(document.form1.assemblequantity) {
				document.form1.assemblequantity.value=tmp;
			}
			document.form1.quantity.value=tmp;
			setTotalPrice(tmp);
		} else {
			alert('구성상품 중 '+tmp+'보다 재고량이 부족한 상품있어서 변경을 불가합니다.');
			return;
		}
	<?php  } else { ?>
		var tmp_price = $("#ID_goodsprice").val();
		tmp_price = Number(tmp_price)*Number(tmp);
		setDeliPrice(tmp_price,tmp);
		$("#result_total_price").html(jsSetComa(tmp_price*exchange));
		//$("#result_total_price").html(jsSetComa(tmp_price));
		//<?number_format(exchageRate($bottomProduct_row->sellprice))?>
		
		document.form1.quantity.value=tmp;
	<?php  } ?>
}

function quantityKeyUp(num,exchange){
	 tmp=document.form1.quantity.value;
	
	 if( isNaN(tmp) == true){
			document.form1.quantity.value=1;
			alert("숫자만 입력 가능합니다");
		return;
	 }
	 var cons_qu = $("#constant_quantity").val();
	if (cons_qu != "" && cons_qu != "0"){
		if (cons_qu<tmp){
			alert('재고량이 부족 합니다.');
			return;
		}
	} else if(cons_qu == "0") {
		alert('품절 입니다.');
		return;
	}
	<?php  if($_pdata->assembleuse=="Y") { ?>
		if(getQuantityCheck(tmp)) {
			if(document.form1.assemblequantity) {
				document.form1.assemblequantity.value=tmp;
			}
			document.form1.quantity.value=tmp; alert(tmp
			setTotalPrice(tmp);
		} else {
			alert('구성상품 중 '+tmp+'보다 재고량이 부족한 상품있어서 변경을 불가합니다.');
			return;
		}
	<?php  } else { ?>
		var tmp_price = $("#ID_goodsprice").val();
		tmp_price = Number(tmp_price)*Number(tmp);
		setDeliPrice(tmp_price,tmp);
		$("#result_total_price").html(jsSetComa(tmp_price*exchange));
		document.form1.quantity.value=tmp;
	<?php  } ?>

}

					function quantityCheck(compareVal,d1,d2){
						var constantVal = 0;
						if (d2 == "0"){
							constantVal = quantityArr_[0][d1-1];
						} else {
							constantVal = quantityArr_[d2-1][d1-1];
						}
						
						if (Number(constantVal) >= Number(compareVal) ){
							return true;
						} else {
							return false;
						}
					}

					function item_ea_up(conIdx,idx){
						var total_quantity = 0;
						var constant_quantity = $("#constant_quantity").val();
						$(".opt_list li").each(function(){
							var id = $(this).attr('id');
							var ex_id = id.split('-');
							total_quantity = Number($("#quantityea-"+ex_id[1]).val())+total_quantity;
						});
						if (constant_quantity != "" && constant_quantity <= total_quantity){
							alert('상품 재고 수량을 초과 하셨습니다.');
							return;
						}
						var goodsprice = $("#ID_goodsprice").val();
						var count_ = $("#quantityea-"+conIdx).val();
						var itemPrice = $("#itemTotalPrice-"+conIdx).val();
						count_ = Number(count_)+1;
						if (count_ < 1){
							count_ = 1;
						}
						var ex_conIdx = conIdx.split('_');
						if (!quantityCheck(count_,ex_conIdx[0],ex_conIdx[1])){
							alert('옵션 재고 수량 초과 입니다.');
							return;
						}
						$("#quantityea-"+conIdx).val(count_);
						$("#itemPrice-"+conIdx).html(jsSetComa(itemPrice*count_)+"원");
						setTotalPrice();
					}
					function item_ea_dn(conIdx,idx){
						var goodsprice = $("#ID_goodsprice").val();
						var count_ = $("#quantityea-"+conIdx).val();
						var itemPrice = $("#itemTotalPrice-"+conIdx).val();
						count_ = Number(count_)-1;
						if (count_ < 1){
							count_ = 1;
						}
						$("#quantityea-"+conIdx).val(count_);
						$("#itemPrice-"+conIdx).html(jsSetComa(itemPrice*count_)+"원");
						setTotalPrice();
					}
					function items_del(conIdx,idx){
						$("#items-"+conIdx).remove();
						setTotalPrice();
					}

					$(function(){
						$("#option1").change(function(){
							if (option2Arr_.length >0){
								return;
							}
							var appendHtml = "";
							var minea = 1; // 최소 구매 수량
							var constant_quantity = $("#constant_quantity").val(); // 재고량
							var val1 = $("#option1 option:selected").val();
							if(val1 == "")	return;
							var op1Title = $("#option1").attr("alt");
							var goodsprice = $("#ID_goodsprice").val();
							var controlIdx_ = val1+"_0";
							var total_quantity = 0;
							var validationControler = true;
							$(".opt_list li").each(function(){
								var id = $(this).attr('id');
								var ex_id = id.split('-');
								if (ex_id[1] == controlIdx_){
									validationControler = false;
								}
								total_quantity = Number($("#quantityea-"+ex_id[1]).val())+total_quantity;
							});
							if (constant_quantity != "" && constant_quantity <= total_quantity){
								alert('상품 재고 수량을 초과 하셨습니다.');
								return;
							}
							if (!validationControler){
								alert('이미 추가되어 있는 옵션입니다.');
								return;
							}
							if (!quantityCheck('1',val1,'0')){
								alert('옵션 재고 수량 초과 입니다.');
								return;
							}
							/*if (val1 == ""){
								alert(op1Title+' 을 선택 하셔야 합니다.');
								$("#option1").focus();
								return;
							}*/
							val1 = Number(val1)-1;
							var itemTotalPrice = Number(sellprice)+Number(optpriceArr_[val1]);
							
							appendHtml += "<li id='items-"+controlIdx_+"'>";
							appendHtml += "<div class='item_info_area'>";
							appendHtml += "	-<?=$_pdata->productname?>,"+option1Arr_[val1]+" <p id='itemPrice-"+controlIdx_+"' alt="+itemTotalPrice+">"+jsSetComa(itemTotalPrice)+"원</p>";
							appendHtml += "</div>";
							appendHtml += "<div class='item_editer_area'>";
							appendHtml += "		<div style='float:left;'>";
							appendHtml += "			<input type=text id='quantityea-"+controlIdx_+"' value='1' class='amount2' size = '2' readonly>";
							appendHtml += "			<input type=hidden id='itemTotalPrice-"+controlIdx_+"' class='itemPrice' value='"+itemTotalPrice+"' >";
							appendHtml += "			<span class='item_ea_up' onclick='javascript:item_ea_up(\""+controlIdx_+"\",\""+val1+"\");'><img src='/image/cart/c_plus_btn.jpg'></span>";
							appendHtml += "			<span class='item_ea_dn' onclick='javascript:item_ea_dn(\""+controlIdx_+"\",\""+val1+"\");'><img src='/image/cart/c_minus_btn.jpg'></span>";
							appendHtml += "		</div>";
							appendHtml += "		<div style='float:right;'><span><img src='/image/cart/c_x_btn.gif' alt='삭제'  onclick='javascript:items_del(\""+controlIdx_+"\",\""+val1+"\");' class='item_del' id='item_del' /></span></div>";
							appendHtml += "	</div>";
							appendHtml += "</li>";

							goodsprice = Number(goodsprice)+Number(itemTotalPrice);
							$(".opt_list").append(appendHtml);
							$(".opt_list").show();
							setTotalPrice();
						});

						$("#option2").change(function(){
						//$('#option2 option').bind('click', function(){
							var minea = 1; // 최소 구매 수량
							var constant_quantity = $("#constant_quantity").val(); // 재고량
							var appendHtml = "";
							var val1 = $("#option1 option:selected").val();
							var op1Title = $("#option1").attr("alt");
							var val2 = $("#option2 option:selected").val();
							var op2Title = $("#option2").attr("alt");
							var goodsprice = $("#ID_goodsprice").val();
							var controlIdx_ = val1+"_"+val2;
							var total_quantity = 0;
							var validationControler = true;
							
							$(".opt_list li").each(function(){
								var id = $(this).attr('id');
								var ex_id = id.split('-');
								if (ex_id[1] == controlIdx_){
									validationControler = false;
								}
								total_quantity = Number($("#quantityea-"+ex_id[1]).val())+total_quantity;
							});
							
							if (constant_quantity != "" && constant_quantity <= total_quantity){
								alert('상품 재고 수량을 초과 하셨습니다.');
								return;
							}

							if (!validationControler){
								alert('이미 추가되어 있는 옵션입니다.');
								return;
							}
							if (!quantityCheck('1',val1,val2)){
								alert('옵션 재고 수량 초과 입니다.');
								return;
							}
							if (val1 == ""){
								alert(op1Title+' 을 선택 하셔야 합니다.');
								$("#option1").focus();
								return;
							}
							if (val2 == ""){
								alert(op2Title+' 을 선택 하셔야 합니다.');
								$("#option2").focus();
								return;
							}
							val1 = Number(val1)-1;
							val2 = Number(val2)-1;
							
							var itemTotalPrice = Number(sellprice)+Number(optpriceArr_[val1]);
							appendHtml += "<li id='items-"+controlIdx_+"'>";
							appendHtml += "<div class='item_info_area'>";
							appendHtml += "	-<?=$_pdata->productname?>,"+option1Arr_[val1]+","+option2Arr_[val2]+" <p id='itemPrice-"+controlIdx_+"' alt="+itemTotalPrice+">"+jsSetComa(itemTotalPrice)+"원</p>";
							appendHtml += "</div>";
							appendHtml += "<div class='item_editer_area'>";
							appendHtml += "		<div style='float:left;'>";
							appendHtml += "			<input type=text id='quantityea-"+controlIdx_+"' value='1' class='amount2' size = '2' readonly>";
							appendHtml += "			<input type=hidden id='itemTotalPrice-"+controlIdx_+"' class='itemPrice' value='"+itemTotalPrice+"' >";
							appendHtml += "			<span class='item_ea_up' onclick='javascript:item_ea_up(\""+controlIdx_+"\",\""+val1+"\");'><img src='/image/cart/c_plus_btn.jpg'></span>";
							appendHtml += "			<span class='item_ea_dn' onclick='javascript:item_ea_dn(\""+controlIdx_+"\",\""+val1+"\");'><img src='/image/cart/c_minus_btn.jpg'></span>";
							appendHtml += "		</div>";
							appendHtml += "		<div style='float:right;'><span><img src='/image/cart/c_x_btn.gif' alt='삭제'  onclick='javascript:items_del(\""+controlIdx_+"\",\""+val1+"\");' class='item_del' id='item_del' /></span></div>";
							appendHtml += "	</div>";
							appendHtml += "</li>";
							goodsprice = Number(goodsprice)+Number(itemTotalPrice);
							$(".opt_list").append(appendHtml);
							$(".opt_list").show();
							
							setTotalPrice();
						});
					});
					function setTotalPrice(){
						var totaltemp = 0;
						var totalea = 0;
						$(".amount2").each(function(index){
							var itemea = $(this).val();
							var id = $(this).attr('id');
							var ex_id = id.split('-');
							var itemIdx = ex_id[1];
							var itemPrice = $("#itemTotalPrice-"+itemIdx).val();
							totaltemp = totaltemp+(itemPrice*itemea);
							totalea = Number(totalea) + Number(itemea);
						});
						if (totaltemp == ""){
							$(".opt_list").hide();
							totaltemp = 0;
						}
						$("#result_total_price").html(jsSetComa(totaltemp));
						$("#ID_goodsprice").val(totaltemp);
						$("#option2 option:eq(0)").attr("selected","true");
						setDeliPrice(totaltemp,totalea);
					}
					
				function setDeliPrice(totalPrice,totalea){
					var deli_type = $("#deli_type").val();
					var deliprice = $('#deli_price').val();
					if (deli_type == "1"){
						$('#deli_price_result').html(jsSetComa(deliprice*totalea)+"원"); // 구매수 대비 증가
						return;
					} else if (deli_type == "2") {
						$('#deli_price_result').html(jsSetComa(deliprice)+"원");
						return;
					} else if (deli_type == "3") {
						$('#deli_price_result').html("0원");
						return;
					} else if (deli_type == "4") {
						$('#deli_price_result').html("착불");
						return;
					} else if (deli_type == "5") {
						$('#deli_price_result').html("착불");
						return;
					} else if (deli_type == "6") {
						$('#deli_price_result').html("0원");
						return;
					} else if (deli_type == "7") {
						if (totalPrice >= $("#deli_miniprice").val() ) {
							deliprice = 0;
						}
						deliprice = deliprice ;
						$('#deli_price_result').html(jsSetComa(deliprice)+"원");
						return;
					} else if (deli_type == "8") {
						if (totalPrice >= $("#deli_miniprice").val() ) {
							deliprice = 0;
						}
						$('#deli_price_result').html(jsSetComa(deliprice)+"원");
						return;
					} else {
						return;
					}
					return;
				}
				</script>
				<style>
					.item_info_area{width:48%; float:left;}
					.item_editer_area{width:48%; float:right;}
					.item_editer_area span{cursor:pointer;}
					.opt_list li {display:block;width:100%;}
				</style>
				<tr>
					<td colspan="2">
						<ul class="opt_list" style="display:none;">
							
						</ul>
					</td>
				</tr>
				<tr>
					<th class="total_price">구매예정금액</th>
					<?php
						if(sizeof($option1Arr)>1 && sizeof($option2Arr)<1) { 
					?>
						<td class="total_price" id="total_price" ><span id="result_total_price"><?=number_format($SellpriceValue)?></span> 원</td>
						<input type = 'hidden' value = '<?=$SellpriceValue?>' id = 'ID_goodsprice' name="ID_goodsprice">
					<?
						} else if(sizeof($option1Arr)>0) {
					?>
						<td class="total_price" id="total_price" ><span id="result_total_price"></span> 원</td>
						<input type = 'hidden' value = '0' id = 'ID_goodsprice' name="ID_goodsprice">
					<?
						} else {
					?>
						<td class="total_price" id="total_price" ><span id="result_total_price"><?=number_format($SellpriceValue)?></span> 원</td>
						<input type = 'hidden' value = '<?=$SellpriceValue?>' id = 'ID_goodsprice' name="ID_goodsprice">
					<?
						}
					?>
					
					<input type = 'hidden' name = 'option1price' id = 'ID_option1price'>
				</tr>
				
				<tr>
					<td colspan="2" class="ta_r">
			<?
				if($_pdata->assembleuse!="Y"){
					if(strlen($dicker)==0) {
						$temp = $_pdata->option1;
						$tok = explode(",",$temp);
						$goods_count=count($tok);

						$check_optea='0';
						if($goods_count>"1"){
							$check_optea="1";
						}

						$optioncnt = explode(",",ltrim($_pdata->option_quantity,','));
						
						if (sizeof($optioncnt) > 1) {
							for ($i=0; $i<sizeof($optioncnt);$i++) {
								if ($optioncnt[$i] == "") {
									$optioncnt[$i] = "0";
								}
							}
						}
						$check_optout=array();
						$check_optin=array();
						for($gi=1;$gi<$goods_count;$gi++) {

							if(strlen($_pdata->option2)==0 && $optioncnt[$gi-1]=="0"){ $check_optout[]='1';}
							else{  $check_optin[]='1';}
						}


						if(strlen($_pdata->quantity)>0 && ($_pdata->quantity<="0" || (count($check_optin)=='0' && $check_optea))){
			?>
							<FONT style="color:#F02800;"><b>품 절</b></FONT>
			<?
						}else {
			?>
							<?if (strlen($_ShopInfo->getMemid())>0 && $_ShopInfo->getMemid()!="deleted") {?>
								<a href="javascript:CheckForm('ordernow','<?=$opti?>');" class="first btn_A">바로구매</a>
								<a href="javascript:CheckForm('','<?=$opti?>');" class="btn_cart btn_B">장바구니</a>								
							<?} else {?>
								<a href="javascript:CheckForm('ordernow','<?=$opti?>');" class="first btn_A">바로구매</a>
								<a href="javascript:CheckForm('','<?=$opti?>');" class="btn_cart btn_B">장바구니</a>
								
							<?}?>
			<?
						}
					}
				}
			?>			
						<input type="hidden" name="constant_quantity" id="constant_quantity" value="<?=$_pdata->quantity?>" />
						<input type=hidden name=optionArr value="">
						<input type=hidden name=priceArr value="">
						<input type=hidden name=quantityArr value="">
						<input type=hidden name=code value="<?=$code?>">
						<input type=hidden name=productcode value="<?=$productcode?>">
						<input type=hidden name=ordertype>
						<input type=hidden name=opts>
						<?=($brandcode>0?"<input type=hidden name=brandcode value=\"".$brandcode."\">\n":"")?>
					</td>
				</tr>
			</table>
		</form>
		</div><!-- //div.center_info_wrap -->
	
	</div><!-- //div.goods_view_detail_wrap -->
	
</div>
<script>
$('a.pop_close_btn').click(function(){
	$('div.popup_preview_warp').html("");
	$('div.popup_preview_warp').hide();
	$('#overDiv').hide();
});


</script>


