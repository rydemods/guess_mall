<?
include_once dirname(__FILE__)."/../../lib/product.class.php";
$product = new PRODUCT();
$dc_data = $product->getProductDcRate($productcode);

$groupPriceList = $product->getProductGroupPrice($productcode);
/*
$_pdata->sellprice = exchageRate($_pdata->sellprice);
$_pdata->consumerprice = exchageRate($_pdata->consumerprice);
*/	
//exdebug($_data);
/*
if ($groupPriceList) { // 일반 및 도매회원 금액 세팅시 로그인 되어잇는 user 등급에 따라 판매 금액 적용
	$_pdata->sellprice = exchageRate($groupPriceList[sellprice]);
	$_pdata->consumerprice = exchageRate($groupPriceList[consumerprice]);
	$_pdata->consumer_reserve = $groupPriceList[consumer_reserve];
} 
*/
$option1Arr;$option2Arr;
//exdebug($dc_data);

/*
[deli_type] => T //
[deli_basefee] => 2600 배송비 
[deli_basefeetype] => N // N일경우 (배송비 산출시 개별배송 상품금액은 제외)  Y (배송비 산출시 개별배송 상품금액도 포함)
[deli_miniprice] => 100000   배송비 유료일경우 입력된금액 이상일경우 무료
[deli_oneprprice] => N
[deli_setperiod] => 7*/
/*exdebug("deli_type:".$_data->deli_type);
exdebug("deli_basefee:".$_data->deli_basefee);
exdebug("deli_basefeetype:".$_data->deli_basefeetype);
exdebug("deli_miniprice:".$_data->deli_miniprice);
exdebug("deli_oneprprice:".$_data->deli_oneprprice);
exdebug("deli_setperiod:".$_data->deli_setperiod);*/
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

$codenavi=getCodeLoc3($code);

?>

<style>
	.btn_opt_del {
		cursor: pointer;
	}
</style>
<!--<script type="text/javascript" src="../js/jquery.sudoSlider.js" ></script>
<script type="text/javascript" src="../js/custom.js" ></script>-->
<script src="../js/jquery.elevatezoom.js" type="text/javascript"></script>
<script type="text/javascript">
	var gBlock = 0;
	var gGotopage = 1;
	var gqBlock = 0;
	var gqGotopage = 1;
	$(function(){
		/*
		$('div.top_line_banner a.close').click(function(){
			$('div.top_line_banner').slideUp(500);
		});
		$('a.btn_quick_close').click(function(){
			$('div.right_quick_menu_wrap').css('display' , 'none');
		});
		$('a.btn_quick_open').click(function(){
			$('div.right_quick_menu_wrap').css('display' , 'block');
		});
		$('a.btn_quick_close').click(function(){
			$('a.btn_quick_close , a.btn_quick_open').css('right' , '0px');
		});
		$('a.btn_quick_open').click(function(){
			$('a.btn_quick_close , a.btn_quick_open').css('right' , '105px');
		});*/


		/*
			구매 옵션
		*/
		$(".btn_opt_del").click(function(index){
			//alert($(this).parent().parent().html());
			alert(index);
		});


		/*
			상품 Review Start
		*/
		$(".reviewStars li").click(function(){
			$(this).parent().prev().html($(this).children().html());
			$(this).parent().parent().removeClass('open');
			$('#rmarks').val($(this).children().attr('star'));
		})
		/*
		$(document).on("click", ".reviewCommentShowAjax", function(){
			$(this).parent().next().children().slideDown(400);
		});
		*/
		$(document).on("click", ".reviewCommentReportAjax", function(){
		});
		$(document).on("click", ".reviewContentsDeleteAjax", function(){
			if(confirm('해당 리뷰를 삭제하시겠습니까?')){
				$.ajax({
					type: "POST",
					url: "../front/prreview_tem001_comment_proc.php",
					data: "num="+$(this).prev().val()+"&productcode="+$("input[name='productcode']").val()+"&mode=deleteReview"
				}).done(function ( data ) {
					$("#reviewTotalCount").html(data);
					$(".reviewTotalMenuBar").html("("+$("#reviewTotalCount").html()+")");
					$(".goods_right_review_list").load("../front/prreview_tem001_right.php?productcode="+$("input[name='productcode']").val());
					GoPageAjax(gBlock, gGotopage);
				});
			}
		});
		$(document).on("click", ".reviewCommentDeleteAjax", function(){
			if(confirm('해당 리플을 삭제하시겠습니까?')){
				var objComment = $(this).parent().parent().parent().parent();
				$.ajax({
					type: "POST",
					url: "../front/prreview_tem001_comment_proc.php",
					data: "no="+$(this).prev().val()+"&num="+$(this).prev().prev().val()+"&mode=deleteReviewContents"
				}).done(function ( data ) {
					$(objComment).html(data);
				});
			}
		});
		$(document).on("click", ".reviewCommentAjax", function(){
			var objText = $(this).prev();
			var objComment = $(this).parent().next();
			$.ajax({
				type: "POST",
				url: "../front/prreview_tem001_comment_proc.php",
				data: "num="+$(this).prev().prev().val()+"&contents="+$(this).prev().val()+"&mode=write"
			}).done(function ( data ) {
				$(objComment).html(data);
				$(objText).val('');
			});
		});
		$(".reviewTotalMenuBar").html("("+$("#reviewTotalCount").html()+")");


		$(".goods_right_review_list").load("../front/prreview_tem001_right.php?productcode="+$("input[name='productcode']").val());
		$(".view_list_wrap").load("../front/prvcount_tem001_right.php");
		/*
			상품 Review End
		*/





		/*
			상품 QNA Start
		*/
		$(document).on("click", ".chkQnaPasswd", function(){
			var obj = $(this);
			$.ajax({
				type: "POST",
				url: "../front/prqna_tem001_pass_proc.php",
				data: "passwd="+$(this).prev().val()+"&id_num="+$(this).attr('idx')
			}).done(function ( data ) {
				if(data == '1'){
					$(obj).parent().hide();
					$(obj).parent().next().show();
				}else{
					alert("비밀번호가 틀렸습니다.");
					$(obj).prev().val('');
					$(obj).prev().focus();
					$(obj).parent().show();
					$(obj).parent().next().hide();
				}
			});
		})

		/*$(document).on("click", ".qnaViewPanel", function(){
			if($(this).next().css('display') == 'none'){
				$(this).next().show();
			}else{
				$(this).next().hide();
			}


		})*/
		/*
			상품 QNA End
		*/



		/*
			상품 할인율 Start
		*/
		if($("#ID_priceDcPercent").val() > 0){
			$("#ID_priceDcPercentLayer").html("단독 "+$("#ID_priceDcPercent").val()+"% 할인");
		}
		/*
			상품 할인율 End
		*/



		/*
			URL복사 Start
		*/
		$(".CLS_urlcopy").click(function(){
			var trb = $("#ID_faceboolMallUrl").val();
			var IE=(document.all)?true:false;
			if (IE) {
				if(confirm("이 글의 트랙백 주소를 클립보드에 복사하시겠습니까?"))
				window.clipboardData.setData("Text", trb);
			} else {
				temp = prompt("이 글의 트랙백 주소입니다. Ctrl+C를 눌러 클립보드로 복사하세요", trb);
			}
		})
		/*
			URL복사 End
		*/

	});
function setcardInfo() {
	window.open("./setcardInfoForm.html","setcardInfoForm","height=570,width=590,scrollbars=yes");
}
function clickScoll(num){	
	var val = $('#tap'+num).offset();
	$('body,html').animate({scrollTop:val.top},20);
}

</script>
<script src="../js/jquery.elevatezoom.js" type="text/javascript"></script>
<script type="text/javascript" src="../js/jcarousellite_1.0.js" ></script>

<style>

</style> 
<div id="body_contents"> <!-- 123 --> 
	<div class="line_map01">   
		<div class="container"> 
			<? for($i=0;$i<count($codenavi);$i++) {?>
				<? if($i != count($codenavi)-1) { ?> 
				<em>&gt;</em><a><?=$codenavi[$i]?></a> 
				<? } else { ?> 
				<em>&gt;</em><span><a><?=$codenavi[$i]?></a></span>
				<? } ?>
			<? } ?>
			<span style="float:right;"><a href="javascript:window.history.back()">< 이전페이지</a></span>
		</div>
		
	</div>
	<div class="containerBody">  
		<div class="goods_info_section">
			<div class="detail_info_wrap">				
				<div class="thumb">		
					<ul class="small_thumb">
			
<?php
	##### 기본 이미지 썸네일
	if(strlen($_pdata->maximage)>0 && is_file($Dir.DataDir."shopimages/product/".$_pdata->maximage)) {
		$width=GetImageSize($Dir.DataDir."shopimages/product/".$_pdata->maximage);
		if($width[0]>=300) $width[0]=440;
		else if (strlen($width[0])==0) $width[0]=440;
		if($changetype=="0"){
			$ahref_def = "<a href=\"javascript:primg_preview_def('".$_pdata->maximage."','','')\" onmouseover=\"primg_preview4('".$_pdata->maximage."','','')\">";
		}else{
			$ahref_def = "<a href=\"javascript:primg_preview_def('".$_pdata->maximage."','{$imgsize[0]}','{$imgsize[1]}')\">";
		}
		
?>
		<li>
			<?=$ahref_def?>
				<img src="<?=$Dir.DataDir?>shopimages/product/<?=$_pdata->minimage?>" alt="" style="width:78px;height:78px"/>
			</a></li>
<?php 
	} elseif(is_file($Dir.$_pdata->maximage)) { 
		$width=GetImageSize($Dir.DataDir."shopimages/product/".$_pdata->maximage);
		if($width[0]>=300) $width[0]=440;
		else if (strlen($width[0])==0) $width[0]=440;
		if($changetype=="0"){
			$ahref_def = "<a href=\"javascript:primg_preview_def('".$_pdata->maximage."','','')\"  onmouseover=\"primg_preview4('".$_pdata->maximage."','','')\">";
		}else{
			$ahref_def = "<a href=\"javascript:primg_preview_def('".$_pdata->maximage."','{$imgsize[0]}','{$imgsize[1]}')\">";
		}

?>
		<li>
			<?=$ahref_def?>
				<img src="<?=$Dir.$_pdata->maximage?>" alt="" style="width:78px;height:78px"/>	
			</a></li>
<?php } else { ?>
		<li><a href=""><img src="<?=$Dir?>images/no_img.gif" style="width:78px;height:78px" alt=""></a></li>
<?php
	}

		##### //기본 이미지 썸네일
		if($multi_img=="Y" && $yesimage[0]) {
			$imagepath_multi=$Dir.DataDir."shopimages/multi/";	
			##### 나머지 ETC 이미지 썸네일
			for($i=0;$i<$y;$i++) {
				if($changetype=="0") {	//마우스 오버
					$ahref_type =  "<a href=\"javascript:primg_preview('{$yesimage[$i]}','{$xsize[$i]}','{$ysize[$i]}')\" onmouseover=\"primg_preview2('{$yesimage[$i]}','{$xsize[$i]}','{$ysize[$i]}')\">";
				}else{
					$ahref_type = "<a href=\"javascript:primg_preview('{$yesimage[$i]}','{$xsize[$i]}','{$ysize[$i]}')\">";
				}
	?>	
				<li><?=$ahref_type?><img src="<?=$imagepath_multi?>s<?=$yesimage[$i]?>" alt="" /></a></li>
	<?	
			}
		}
?>
			</ul>
		<?php 	if(is_file($Dir.DataDir."shopimages/product/".$_pdata->maximage)) { 	?>
					<p class="big_thumb"><a href="#"><img id="zoom" data-zoom-image="<?=$Dir.DataDir."shopimages/product/".$_pdata->maximage?>" src="<?=$Dir.DataDir."shopimages/product/".$_pdata->maximage?>" 
						name="primg" alt="" style="  width: 425px; height: 455px;" /></a></p>   
		<?php	}else { 	?>   
					<p class="big_thumb"><a href="#"><img id="zoom" data-zoom-image="<?=$Dir.DataDir."shopimages/product/".$_pdata->maximage?>" src="<?=$Dir?>images/no_img.gif" name="primg" alt="" style="width: 285px;" /></a></p>
		<?php	}	?>     
					<!--<p class="big_thumb"><img src="../img/test/test_goods350.jpg" width="425" height="455" alt="큰 썸네일" /></p>					-->
				</div>
<script type="text/javascript">
$("#zoom").elevateZoom({gallery:'small_thumb'});
/*
$("#zoom").bind("click",function(e){
	var ez = $("#zoom").data("elevateZoom");
	$.fancybox(ez.getGalleryList());
	return false; 
})
*/
</script>
				<div class="spec_info">
					<form name="groupHiddenFrom" id= 'group_hidden_frm'>
					<input type="hidden" name="s_cnt" value="<?=$s_cnt?>" />
					<? foreach($s_price as $sKey=>$sVal){ ?>
					<input type="hidden" name="s_price[]" value="<?=$sVal['price']?>"/>
					<input type="hidden" name="s_min[]" value="<?=$sVal['min_num']?>"/>
					<input type="hidden" name="s_max[]" value="<?=$sVal['max_num']?>"/>
					<? } ?>
					</form>
					<form name=form1 id = 'ID_goodsviewfrm' method=post action="<?=$Dir.FrontDir?>basket.php">
					<h3 class="name"><?=$_pdata->productname?></h3>
					<p>모델번호 : </p>
					<table class="detail_info" width="100%">
						<caption>상품의 판매가격,제조사/원산지,제품등급,소재,사이즈,구성 정보와 총 주문 금액을 확인</caption>
						<colgroup><col style="width:140px"/><col style="width:auto"/></colgroup>
						<tr>
							<th class="price">판매가</th>
							<td class="price"><span class="price_c"><?=number_format($_pdata->consumerprice)?>원</span></td>
						</tr>
						<?
							$reserveconv=getReserveConversion($_pdata->reserve,$_pdata->reservetype,$_pdata->sellprice,"Y");
							$SellpriceValue=0;
							//if(strlen($dicker=dickerview($_pdata->etctype,number_format($_pdata->sellprice),1))>0){
							if(false){
						?>
						<tr>
							<th class="sale">할인가</th>
							<td class="sale"><span class="price_d">원</span>
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
							<th class="sale">할인가</th>
							<td class="sale"><span class="price_d"><?=number_format(str_replace(",","",$pricetok[0]))?>원</span>
							</td>
						</tr>
						<?
								$SellpriceValue=str_replace(",","",$pricetok[0]);
							//} else if(strlen($optcode)>0) {
							} else if(false) {
						?>
						<tr>
							<th class="sale">할인가</th>
							<td class="sale"><span class="price_d"><?=number_format($_pdata->sellprice)?>원</span>
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
							<th class="sale">할인가</th>
							<td class="sale"><span class="price_d">
								<?=number_format(($miniq>1?$miniq*$_pdata->sellprice:$_pdata->sellprice))?>원</span>
								<input type=hidden name=price value="<?=number_format(($miniq>1?$miniq*$_pdata->sellprice:$_pdata->sellprice))?>">
								<input type=hidden name=sprice value="<?=number_format($_pdata->sellprice)?>">
								<input type=hidden name=consumer value="<?=number_format(($miniq>1?$miniq*$_pdata->consumerprice:$_pdata->consumerprice))?>">
								<input type=hidden name=o_reserve value="<?=number_format(($miniq>1?$miniq*$_pdata->option_reserve:$_pdata->option_reserve))?>">
							</td>
						</tr>
						<?
							$SellpriceValue=($miniq>1?$miniq*$_pdata->sellprice:$_pdata->sellprice);
							} else {
						?>
						<tr>
							<th class="sale">할인가</th>
							<td class="sale"><span class="price_d">
								<?=number_format($_pdata->sellprice)?>원</span>
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
						<?if($_pdata->reserve>0){
							$getReserveConversion = getReserveConversion($_pdata->reserve, $_pdata->reservetype, $_pdata->sellprice,'Y');
						?>
						<tr>
							<th class="origin">적립금</th>  
							<td class="origin" id="ID_displyReserv"><?=number_format($getReserveConversion)?> point</td>
							<!--<td class="origin"><?=number_format($_pdata->reserve)?> point</td>-->
							<input type="hidden" id="ID_reserv" value="<?=$_pdata->reserve?>" >
						</tr>
						<?}?> 

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
						<!--<tr>
							<td colspan="3" class="line_1px" ><em></em></td>
						</tr>-->
						<tr>
							<th><?=$tok[0]?></th>
							<td>
								<div class="select_type" style="width:180px;z-index:10;">
									<select name="option1" id="option1" style="width: 225px;" alt='<?=$tok[0]?>'>
									<option value="">옵션을 선택해주세요.</option>
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
												</option>
											<?}?>
										<?}?>
									</select>
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
							<td>
								<div class="select_type" style="width:180px;z-index:10;">
									<select name="option2" id="option2" style="width: 225px;" alt='<?=$tok[0]?>'>
									<option value="">옵션을 선택해주세요.</option>
										<?for($i=1;$i<$count2;$i++) {?>
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
									</select>
								</div>
							</td>
						</tr>
						<?
							}
						?>
						<?if( strlen($_pdata->option1) == 0 ){?>
						<tr class="line">
							<th class="ea">주문수량</th>
							<td>
								<div class="ea_select">
									<input type="text" readonly="true" name="quantity" id="quantity" value="1" onkeyup="strnumkeyup(this)" class="amount" size="2">
									<a href="javascript:change_quantity('up')" class="btn_plus"></a>
									<a href="javascript:change_quantity('dn')" class="btn_minus"></a>
								</div>
							</td>
						</tr>
						<?}?>
						
					<?
						
						
						/*할인율 계산*/
						if($SellpriceValue != $_pdata->consumerprice && $_pdata->consumerprice > 0){
							$priceDcPercent = floor(100 - ($SellpriceValue / $_pdata->consumerprice * 100));
						}else{
							$priceDcPercent = 0;
						}
					?>
					<input type='hidden' value='<?=$priceDcPercent?>' id = 'ID_priceDcPercent'>
						<script>
					var optpriceArr_;
					var option1Arr_;
					var option2Arr_;
					var quantity_ = "<?=$_pdata->option_quantity?>";
					quantity_ = quantity_.split(',');
					
					//수량에 의한 가격변동
					var s_cnt = $("input[name='s_cnt']").val();       
					var price = $("input[name='s_price[]']").map(function (){return this.value});
					var s_min = $("input[name='s_min[]']").map(function (){return this.value});
					var s_max = $("input[name='s_max[]']").map(function (){return this.value});
					
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
						change_quantityOpt("up");
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
						change_quantityOpt("dn");
						setTotalPrice();
					}
					function items_del(conIdx,idx){
						//적립금
						var itemQuantity = $("#quantityea-"+conIdx).val();       
					<?php if($_pdata->reserve>0){ ?>
						var tmp_reserve = $("#ID_reserv").val();
						$("#ID_displyReserv").html(comma((tmp-(itemQuantity-1))*tmp_reserve)+" point");
					<?php } ?>
						document.form1.quantity.value -= (itemQuantity-1);
						if(document.form1.quantity.value < 0) document.form1.quantity.value = 0;
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
							appendHtml += "	<span class='opt_name'>-<?=$_pdata->productname?>,"+option1Arr_[val1]+"</span> <span class='price' id='itemPrice-"+controlIdx_+"' alt="+itemTotalPrice+">"+jsSetComa(itemTotalPrice)+"원</span>";
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
							appendHtml += "	<span class='opt_name'>-<?=$_pdata->productname?>,"+option1Arr_[val1]+","+option2Arr_[val2]+"</span> <span class='price' id='itemPrice-"+controlIdx_+"' alt="+itemTotalPrice+">"+jsSetComa(itemTotalPrice)+"원</span>";
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
						$("#result_total_price").html("<span class='price_d'><strong>"+jsSetComa(totaltemp)+"원</strong></span>");
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
				
				function newOptionPrice(tmp_price,count_){
					var itemPrice = 0;
					if(s_cnt > 0){
						$.each(price,function(index,item){
							if(Number(s_min[index]) <= Number(count_) && Number(s_max[index]) >= Number(count_)) tmp_price = item;
						});
						//if(tmp_price <= 0) tmp_price = itemPrice;
						//itemPrice = Number(tmp_price)+ Number($("#optionPrice-"+conIdx).val());
						return tmp_price;
					}
					return tmp_price;
				}
				</script>
						<style>
							.item_info_area{width:75%; float:left;}
							.item_info_area > span {display:inline-block;}
							.item_info_area > span.opt_name {}
							.item_info_area > span.price {padding-left:5px; font-weight:bold; color:#ef4035;}
							.item_editer_area{width:25%; float:right;}
							.item_editer_area span{cursor:pointer;}
							.item_editer_area img{margin-top: 0 !important;} 
							.opt_list li {display:block;width:100%;}
						</style>
						<tr>
							<td colspan="2">
								<ul class="opt_list" style="display:none;">
									
								</ul>
							</td>
						</tr>
						
						
						<!--<tr>
							<th>부가정보</th>
							<td></td>
						</tr>-->
						<tr style="display: none">
							<th>배송비</th>
							<td id="td_deli_price">
								<p id="deli_price_result"></p>
								<P><?=$deliState_[msg]?></P>
							</td>
							<input id="deli_price" type="hidden" value="<?=$deli_price?>" />
							<!--<input id="deli_type" type="text" value="<?=$deli_type?>" />-->
							<input id="deli_type" type="hidden" value="<?=$deli_state?>" />
							<input id="deli_miniprice" type="hidden" value="<?=$_data->deli_miniprice?>" />
						</tr>
						
						<tfoot>
						<tr>
							<th>총 주문금액</th>
							<td>
							<?php
								//if(sizeof($option1Arr)>1 && sizeof($option2Arr)<1) { 
								if(false) { 
							?>
								<p id="result_total_price" class="price">
									<span class="price_d"><strong>
										<?=number_format($SellpriceValue)?>원
									</strong></span>
								</p>
								<input type = 'hidden' value = '<?=$SellpriceValue?>' id = 'ID_goodsprice' name="ID_goodsprice">
							<?
								} else if(sizeof($option1Arr)>0) {
							?>
								<p id="result_total_price" class="price">
									<span class="price_d"><strong>
										<?=number_format($SellpriceValue)?>원
									</strong></span>
								</p>
								<input type = 'hidden' value = '<?=$SellpriceValue?>' id = 'ID_goodsprice' name="ID_goodsprice">
							<?
								} else {
							?>
								<p id="result_total_price" class="price">
									<span class="price_d"><strong>
									<?=number_format($SellpriceValue)?>원
									</strong></span>
								</p>
								<input type = 'hidden' value = '<?=$SellpriceValue?>' id = 'ID_goodsprice' name="ID_goodsprice">
							<?
								}
							?>
							</td>

							<input type = 'hidden' name = 'option1price' id = 'ID_option1price'>
						</tr>
						
						<tr style="display: none;">
							<td colspan="2"> 
								<?if( strlen($_pdata->option1) > 0 ){?>
								<input type="text" readonly="true" name="quantity" id="quantity" value="1">
								<?}?>
								<input type="hidden" name="constant_quantity" id="constant_quantity" value="<?=$_pdata->quantity?>" />
								<input type=hidden name=optionArr value="">
								<input type=hidden name=priceArr value="">
								<input type=hidden name=quantityArr value="">
								<input type=hidden name=code value="<?=$code?>">
								<input type="hidden" name="mainCode" value="<?=$_cdata->c_category?>">
								<input type=hidden name=productcode value="<?=$productcode?>">
								<input type=hidden name=productquantity id="productquantity" value="">
								<input type=hidden name=ordertype>
								<input type=hidden name=opts>
								<input type=hidden name=vip_type value="<?=$vrow->vip_type?>">
								<input type=hidden name=staff_type value="<?=$vrow->staff_type?>">
								<?=($brandcode>0?"<input type=hidden name=brandcode value=\"".$brandcode."\">\n":"")?>
							</td>
						</tr>
						
						</tfoot>
						
					</table>
					<ul class="buy_btn">
					
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
								?>
					<?
								if(strlen($_pdata->quantity)>0 && ($_pdata->quantity<="0" || (count($check_optin)=='0' && $check_optea))){
					?>
						<li><FONT style="color:#F02800;"><b>품 절</b></FONT></li>
					<?
								}else {
					?>
									<?if (strlen($_ShopInfo->getMemid())>0 && $_ShopInfo->getMemid()!="deleted") {?>
						<li><a href="javascript:CheckForm('ordernow','<?=$opti?>');" class="">
							<img src="../img/btn/btn_buy_now.gif" alt="바로구매" />
						</a></li>
						<li><a href="javascript:CheckForm('','<?=$opti?>');" class="">
							<img src="../img/btn/btn_cart.gif" alt="장바구니" />
						</a></li>
						<li><a href="javascript:CheckForm('wishlist','<?=$opti?>')" class="">
							<img src="../img/btn/btn_wishlist.gif" alt="관심상품" />
						</a></li>
									<?} else {?>
						<li><a href="javascript:CheckForm('ordernow','<?=$opti?>');" class="">
							<img src="../img/btn/btn_buy_now.gif" alt="바로구매" />
						</a></li>
						<li><a href="javascript:CheckForm('','<?=$opti?>');" class="">
							<img src="../img/btn/btn_cart.gif" alt="장바구니" />
						</a></li>
						<li><a href="javascript:check_login();" class="">
							<img src="../img/btn/btn_wishlist.gif" alt="관심상품" />
						</a></li>
									<?}?>
					<?
								}
							}
						}
					?>
					   
						<!--<li><a href="#"><img src="../img/btn/btn_buy_now.gif" alt="바로구매" /></a></li>
						<li><a href="#"><img src="../img/btn/btn_cart.gif" alt="장바구니" /></a></li>
						<li><a href="#"><img src="../img/btn/btn_wishlist.gif" alt="관심상품" /></a></li>-->
					</ul>
					</form>
				</div>
				<div class="product_news">상품 소문내기 
					<a href="<?=$facebookurl?>" target = "_blank">
						<img src="../img/icon/icon_f.gif" alt="facebook" />
					</a> 
					<a href="javascript:;" class="CLS_urlcopy">
						<img src="../img/icon/icon_url.gif" alt="URL" />
						<input type = 'hidden' value = '<?=$faceboolMallUrl?>' id = 'ID_faceboolMallUrl'>
					</a>
				</div>
				
				<div class="grade_article_wrap">
					<div class="grade_article">
						<div class="card"><h3>무이자 할부 카드 안내</h3>						
						<p>5만원이상 2,3개월 / 10만원 이상 2,3,5,6개월</p>
						<a href="#"><img src="../img/icon/icon_kb.gif" alt="국민카드" /></a> <a href="#"><img src="../img/icon/icon_bc.gif" alt="BC card" /></a> <a href="#"><img src="../img/icon/icon_sh.gif" alt="신한카드" /></a> <a href="#"><img src="../img/icon/icon_ss.gif" alt="삼성카드" /></a></div>
						<div class="exhibit">
					<? // 기획전 출력
					$currentdate = date('Ymd');
					$date_sql = " (to_char(end_date,'YYYYMMDD') >=  '{$currentdate}' and to_char(start_date,'YYYYMMDD') <=  '{$currentdate}') ";

					$psql = "SELECT idx, title FROM tblpromo WHERE (display_type='A' OR display_type='P') AND ".$date_sql." ORDER BY display_seq LIMIT 3 ";
					$pres = pmysql_query($psql);
					$pcnt = pmysql_num_rows($pres);						
					?>
							<h3>관련기획전(<?=number_format($pcnt)?>개)</h3>					
							<p>
							<? while($prow=pmysql_fetch_object($pres)){?>
								<a href="promotion.php?pidx=<?=$prow->idx?>">- <?=$prow->title?></a><br>
							<?}?>
							</p>
						</div>
					</div>
				</div><!-- //div.grade_article -->
			</div><!-- //div.detail_info_wrap -->
		</div><!-- //div.goods_info_section -->
		<div id="detail">
		<a name="tab01"></a>
		<div class="tab_detail01">
			<ul class="detail_tab">
				<li><a class="point_bg" href="#tab01">상세정보</a></li>
				<li><a href="#tab02">상품리뷰</a></li>
				<li><a href="#tab03">상품문의</a></li>
				<li class="last"><a href="#tab04">배송/교환/환불</a></li>
			</ul>

			<!-- 상세 에디터영역 --> 
			<div class="detailimg">
				<?
					$_pdata_content = stripslashes($_pdata->content);
					//exdebug($_pdata_content); 
					if(strlen($detail_filter)>0) {
						$_pdata_content = preg_replace($filterpattern,$filterreplace,$_pdata_content);
					}
					if (strpos($_pdata_content,"table>")!=false || strpos($_pdata_content,"TABLE>")!=false)
						echo "<pre>".$_pdata_content."</pre>";
					else if(strpos($_pdata_content,"</")!=false)
						echo nl2br($_pdata_content);
					else if(strpos($_pdata_content,"img")!=false || strpos($_pdata_content,"IMG")!=false)
						echo nl2br($_pdata_content);
					else
					echo str_replace(" ","&nbsp;",nl2br($_pdata_content));
				?>     
			</div><!-- //상세 에디터영역 -->
		</div><!-- //.tab_detail01 -->		

		<a name="tab02"></a>
		<div class="tab_detail02">
			<ul class="detail_tab">
				<li><a href="#tab01">상세정보</a></li>
				<li><a class="point_bg" href="#tab02">상품리뷰</a></li>
				<li><a href="#tab03">상품문의</a></li>
				<li class="last"><a href="#tab04">배송/교환/환불</a></li>
			</ul>

			<?if($_data->review_type!="N") {?>
				<?php include($Dir.FrontDir."prreview_tem001.php"); ?>
			<?}?> 

		<a name="tab03"></a>
		<div class="tab_detail03">
			<ul class="detail_tab">
				<li><a href="#tab01">상세정보</a></li>
				<li><a href="#tab02">상품리뷰</a></li>
				<li><a class="point_bg"  href="#tab03">상품문의</a></li>
				<li class="last"><a href="#tab04">배송/교환/환불</a></li>
			</ul>  

			<?php include($Dir.FrontDir."prqna_tem001.php"); ?>

		</div><!-- //.tab_detail03 -->

		<a name="tab04"></a>
		<div class="tab_detail04">
			<ul class="detail_tab">
				<li><a href="#tab01">상세정보</a></li>
				<li><a href="#tab02">상품리뷰</a></li>
				<li><a href="#tab03">상품문의</a></li>
				<li class="last"><a class="point_bg" href="#tab04">배송/교환/환불</a></li>
			</ul>
			<div class="delivery_wrap">
				<dl>
					<dt>구매전 필독사항</dt>
					<dd>구매전 필독사항구매전 필독사항구매전 필독사항구매전 필독사항구매전 필독사항구매전 필독사항구매전 필독사항구매전 필독사항구매전 필독사항구매전 필독사항구매전 필독사항구매전 필독사항구매전 필독사항구매전 필독사항구매전 필독사항</dd>
				</dl>
				<dl>
					<dt>배송정보</dt>
					<dd>
						- 배송기간은 오전 9시까지 결제 완료시 당일출고되며, 오전 9시 이후 주무건은 익일 출고됩니다. <br>
						- 배송은 주말, 공휴일 제외 2~3일 이내 수령이 가능합니다.<br>
						- 배송은 결제완료 순으로 출고되며, 품절시 주말제외 2일이내 연락드립니다.<br>
						- 당사에서 구이한 상품은 CJ GLS택배를 이용하여 발송됩니다.<br>
						- 당사 상품은 해외 배송이 불가합니다.
					</dd>
				</dl>
				<dl>
					<dt>주문 취소 안내</dt>
					<dd>
						- 주문 취소는 미결제인 상태에서는 홈페이지내 마이페이지에서 고객님이 직접 취소 가능합니다.<br>
						- 결제 후 취소 (송장 번호입력전)에는 고객센터(1588-3637)로 문의 해주시기 바랍니다.<br>
						- 송장번호가 입력된 후에는 취소, 변경이 불가합니다.<br>
						- 무통장 입금의 경우 4일 이내 입금 되지 않으면 자동 주문 취소 처리 됩니다.
					</dd>
				</dl>
				<dl>
					<dt>교환 및 환불 안내</dt>
					<dd>
						- 교환 및 반품 요청시 고객센터로 접수 후 OO택배를 이용하여 반송처리 해주셔야 합니다.<br>
						- 제품불량으로 교환요청을 하실 경우 동일 제품, 동일사이즈로만 무상교환처리가 가능하며, 
					</dd>
				</dl>
			</div>
		</div><!-- //.tab_detail04 -->
	</div>
	</div><!-- //containerBody -->
	<div class="related_goods">
			<h4>관련인기상품</h4>
			<ul class="related">
			<? foreach($popular_product as $popularVal){ ?>
				<li>
					<div class="goods_A">
						<a href="<?=$Dir.FrontDir."productdetail.php?productcode=".$popularVal['productcode']?>">
							<p><img src="<?=$Dir.DataDir."shopimages/product/".$popularVal['maximage']?>" width="150" height="150" alt=""></p>
							<span class="subject"><?=$popularVal['productname']?></span>  
							<span class="price price_c"><?=number_format($popularVal['sellprice'])?>원</span> 
						</a> 
					</div>
				</li>
			<? } ?>
			</ul>
		</div><!-- //div.related_goods -->      
</div>	<!-- 123 -->

<div style='clear:both;'>&nbsp;</div>

<script>
//카테고리 관련 javascript start
	/*
$(function(){
	var presentCode = document.form1.mainCode.value;
	$("#category_dep_01").change(function(){
		$.post("../front/category_proc.php", {c_lev:"b",c_code:$(this).val(),presentCode:presentCode}, function(data) {
			if (data == "0"){
			} else {
				data = "<option value=''>선택</option>"+data;
				$(".ta_c").show();
				$("#category_dep_02").show().html(data);
			}
		});
	});
	$("#category_dep_02").change(function(){
		location.href="../front/productlist.php?code="+$(this).val();
	});
});
$(document).ready(function(){
	var presentCode = document.form1.mainCode.value;
	$.post("../front/category_proc.php", {c_lev:"a",c_code:presentCode.substr(0,3),presentCode:presentCode}, function(data) {
		$("#category_dep_01").html(data);
	});
	//$("#category_dep_01 > option[value=<?=substr($code,0,3).'000000000'?>]").attr("selected", "true");
	$.post("../front/category_proc.php", {c_lev:"b",c_code:presentCode.substr(0,3),presentCode:presentCode}, function(data) {
		if (data == "0"){
		} else {
			data = "<option value=''>선택</option>"+data;
			$(".ta_c").show();
			$("#category_dep_02").show().html(data);
		}
	});
});
*/
//카테고리 관련 javascript end
</script>


<script language="JavaScript">

	$(".jCarouselLite").jCarouselLite({
		btnNext: ".coodi_pick_wrap .right",
		btnPrev: ".coodi_pick_wrap .left",
		visible: 5,
	    auto: 2000,
	    speed: 1000
	});

var miniq=<?=($miniq>1?$miniq:1)?>;
var ardollar=new Array(3);
ardollar[0]="<?=$ardollar[0]?>";
ardollar[1]="<?=$ardollar[1]?>";
ardollar[2]="<?=$ardollar[2]?>";
<?
if(strlen($optcode)==0) {
	$maxnum=($count2-1)*10;
	if($optioncnt>0) {
		echo "num = new Array(";
		for($i=0;$i<$maxnum;$i++) {
			if ($i!=0) echo ",";
			if(strlen($optioncnt[$i])==0) echo "100000";
			else echo $optioncnt[$i];
		}
		echo ");\n";
	}
?>

function change_price(temp,temp2,temp3) {

<?=(strlen($dicker)>0)?"return;\n":"";?>
	if(temp3=="") temp3=1;
	price = new Array(<?if($priceindex>0) echo "'".number_format($_pdata->sellprice)."','".number_format($_pdata->sellprice)."',"; for($i=0;$i<$priceindex;$i++) { if ($i!=0) { echo ",";} echo "'".$pricetok[$i]."'"; } ?>);

	sprice = new Array(<?if($priceindex>0) echo "'".number_format($_pdata->sellprice)."','".number_format($_pdata->sellprice)."',"; for($i=0;$i<$priceindex;$i++) { if ($i!=0) { echo ",";} echo "'".$spricetok[$i]."'"; } ?>);


	consumer = new Array(<?if($priceindex>0) echo "'".number_format($_pdata->consumerprice)."','".number_format($_pdata->consumerprice)."',"; for($i=0;$i<$priceindex;$i++) { if ($i!=0) { echo ",";} echo "'".$consumertok[$i]."'"; } ?>);
	o_reserve = new Array(<?if($priceindex>0) echo "'".number_format($_pdata->option_reserve)."','".number_format($_pdata->option_reserve)."',"; for($i=0;$i<$priceindex;$i++) { if ($i!=0) { echo ",";} echo "'".$reservetok[$i]."'"; } ?>);
	doprice = new Array(<?if($priceindex>0) echo "'".number_format($_pdata->sellprice/$ardollar[1],2)."','".number_format($_pdata->sellprice/$ardollar[1],2)."',"; for($i=0;$i<$priceindex;$i++) { if ($i!=0) { echo ",";} echo "'".$pricetokdo[$i]."'"; } ?>);
	if(temp==1) {
		if (document.form1.option1.selectedIndex><? echo $priceindex+2 ?>)
			temp = <?=$priceindex?>;
		else temp = document.form1.option1.selectedIndex;
		document.form1.price.value = price[temp];

		document.all["idx_price"].innerHTML = document.form1.price.value+"원";


		if(sprice[temp]!='0'){
		document.form1.sprice.value = sprice[temp];
		document.all["idx_sprice"].innerHTML = document.form1.sprice.value+"원";
		}else{
			if(sprice[0]!='0'){
			document.form1.sprice.value = sprice[0];
			document.all["idx_sprice"].innerHTML = document.form1.sprice.value+"원";
			}
		}


		if(consumer[temp]!='0'){
		document.form1.consumer.value = consumer[temp];
		document.all["idx_consumer"].innerHTML = document.form1.consumer.value+"원";
		}else{
			if(consumer[0]!='0'){
			document.form1.consumer.value = consumer[0];
			document.all["idx_consumer"].innerHTML = document.form1.consumer.value+"원";
			}
		}
		if(o_reserve[temp]!='0'){
		document.form1.o_reserve.value = o_reserve[temp];
		document.all["idx_reserve"].innerHTML = document.form1.o_reserve.value+"원";
		}else{
			if(o_reserve[0]!='0'){
			document.form1.o_reserve.value = o_reserve[0];
			document.all["idx_reserve"].innerHTML = document.form1.o_reserve.value+"원";
			}
		}

<?if($_pdata->reservetype=="Y" && $_pdata->reserve>0) { ?>
		if(document.getElementById("idx_reserve")) {
			var reserveInnerValue="0";
			if(document.form1.price.value.length>0) {
				var ReservePer=<?=$_pdata->reserve?>;
				var ReservePriceValue=Number(document.form1.price.value.replace(/,/gi,""));
				if(ReservePriceValue>0) {
					reserveInnerValue = Math.round(ReservePer*ReservePriceValue*0.01)+"";
					var result = "";
					for(var i=0; i<reserveInnerValue.length; i++) {
						var tmp = reserveInnerValue.length-(i+1);
						if(i%3==0 && i!=0) result = "," + result;
						result = reserveInnerValue.charAt(tmp) + result;
					}
					reserveInnerValue = result;
				}
			}
			document.getElementById("idx_reserve").innerHTML = reserveInnerValue+"원";
		}
<? } ?>
		if(typeof(document.form1.dollarprice)=="object") {
			document.form1.dollarprice.value = doprice[temp];
			document.all["idx_dollarprice"].innerHTML=ardollar[0]+" "+document.form1.dollarprice.value+" "+ardollar[2];
		}
	}
	packagecal(); //패키지 상품 적용
	if(temp2>0 && temp3>0) {
		if(num[(temp3-1)*10+(temp2-1)]==0){
			alert('해당 상품의 옵션은 품절되었습니다. 다른 상품을 선택하세요');
			if(document.form1.option1.type!="hidden") document.form1.option1.focus();
			return;
		}
	} else {
		if(temp2<=0 && document.form1.option1.type!="hidden") document.form1.option1.focus();
		else document.form1.option2.focus();
		return;
	}
}

<? } else if(strlen($optcode)>0) { ?>

function chopprice(temp){
<?=(strlen($dicker)>0)?"return;\n":"";?>
	ind = document.form1.mulopt[temp];
	price = ind.options[ind.selectedIndex].value;
	originalprice = document.form1.price.value.replace(/,/g, "");
	document.form1.price.value=Number(originalprice)-Number(document.form1.opttype[temp].value);
	if(price.indexOf(",")>0) {
		optprice = price.substring(price.indexOf(",")+1);
	} else {
		optprice=0;
	}
	document.form1.price.value=Number(document.form1.price.value)+Number(optprice);
	if(typeof(document.form1.dollarprice)=="object") {
		document.form1.dollarprice.value=(Math.round(((Number(document.form1.price.value))/ardollar[1])*100)/100);
		document.all["idx_dollarprice"].innerHTML=ardollar[0]+" "+document.form1.dollarprice.value+" "+ardollar[2];
	}
	document.form1.opttype[temp].value=optprice;
	var num_str = document.form1.price.value.toString()
	var result = ''

	for(var i=0; i<num_str.length; i++) {
		var tmp = num_str.length-(i+1)
		if(i%3==0 && i!=0) result = ',' + result
		result = num_str.charAt(tmp) + result
	}
	document.form1.price.value = result;
	document.all["idx_price"].innerHTML=document.form1.price.value+"원";
	packagecal(); //패키지 상품 적용
}

<?}?>
<? if($_pdata->assembleuse=="Y") { ?>
function setTotalPrice(tmp) {
<?=(strlen($dicker)>0)?"return;\n":"";?>
	var i=true;
	var j=1;
	var totalprice=0;
	while(i) {
		if(document.getElementById("acassemble"+j)) {
			if(document.getElementById("acassemble"+j).value) {
				arracassemble = document.getElementById("acassemble"+j).value.split("|");
				if(arracassemble[2].length) {
					totalprice += arracassemble[2]*1;
				}
			}
		} else {
			i=false;
		}
		j++;
	}
	totalprice = totalprice*tmp;
	var num_str = totalprice.toString();
	var result = '';
	for(var i=0; i<num_str.length; i++) {
		var tmp = num_str.length-(i+1);
		if(i%3==0 && i!=0) result = ',' + result;
		result = num_str.charAt(tmp) + result;
	}
	if(typeof(document.form1.price)=="object") { document.form1.price.value=totalprice; }
	if(typeof(document.form1.dollarprice)=="object") {
		document.form1.dollarprice.value=(Math.round(((Number(document.form1.price.value))/ardollar[1])*100)/100);
		document.all["idx_dollarprice"].innerHTML=ardollar[0]+" "+document.form1.dollarprice.value+" "+ardollar[2];
	}
	if(document.getElementById("idx_assembleprice")) { document.getElementById("idx_assembleprice").value = result; }
	if(document.getElementById("idx_price")) { document.getElementById("idx_price").innerHTML = result+"원"; }
	if(document.getElementById("idx_price_graph")) { document.getElementById("idx_price_graph").innerHTML = result+"원"; }
	<?if($_pdata->reservetype=="Y" && $_pdata->reserve>0) { ?>
		if(document.getElementById("idx_reserve")) {
			var reserveInnerValue="0";
			if(document.form1.price.value.length>0) {
				var ReservePer=<?=$_pdata->reserve?>;
				var ReservePriceValue=Number(document.form1.price.value.replace(/,/gi,""));
				if(ReservePriceValue>0) {
					reserveInnerValue = Math.round(ReservePer*ReservePriceValue*0.01)+"";
					var result = "";
					for(var i=0; i<reserveInnerValue.length; i++) {
						var tmp = reserveInnerValue.length-(i+1);
						if(i%3==0 && i!=0) result = "," + result;
						result = reserveInnerValue.charAt(tmp) + result;
					}
					reserveInnerValue = result;
				}
			}
			document.getElementById("idx_reserve").innerHTML = reserveInnerValue+"원";
		}
	<? } ?>
}
<? } ?>

function packagecal() {
<?=(count($arrpackage_pricevalue)==0?"return;\n":"")?>
	pakageprice = new Array(<? for($i=0;$i<count($arrpackage_pricevalue);$i++) { if ($i!=0) { echo ",";} echo "'".$arrpackage_pricevalue[$i]."'"; }?>);
	var result = "";
	var intgetValue = document.form1.price.value.replace(/,/g, "");
	var temppricevalue = "0";
	for(var j=1; j<pakageprice.length; j++) {
		if(document.getElementById("idx_price"+j)) {
			temppricevalue = (Number(intgetValue)+Number(pakageprice[j])).toString();
			result="";
			for(var i=0; i<temppricevalue.length; i++) {
				var tmp = temppricevalue.length-(i+1);
				if(i%3==0 && i!=0) result = "," + result;
				result = temppricevalue.charAt(tmp) + result;
			}
			document.getElementById("idx_price"+j).innerHTML=result+"원";
		}
	}

	if(typeof(document.form1.package_idx)=="object") {
		var packagePriceValue = Number(intgetValue)+Number(pakageprice[Number(document.form1.package_idx.value)]);

		if(packagePriceValue>0) {
			result = "";
			packagePriceValue = packagePriceValue.toString();
			for(var i=0; i<packagePriceValue.length; i++) {
				var tmp = packagePriceValue.length-(i+1);
				if(i%3==0 && i!=0) result = "," + result;
				result = packagePriceValue.charAt(tmp) + result;
			}
			returnValue = result;
		} else {
			returnValue = "0";
		}
		if(document.getElementById("idx_price")) {
			document.getElementById("idx_price").innerHTML=returnValue+"원";
		}
		if(document.getElementById("idx_price_graph")) {
			document.getElementById("idx_price_graph").innerHTML=returnValue+"원";
		}
		if(typeof(document.form1.dollarprice)=="object") {
			document.form1.dollarprice.value=Math.round((packagePriceValue/ardollar[1])*100)/100;
			if(document.getElementById("idx_price_graph")) {
				document.getElementById("idx_price_graph").innerHTML=ardollar[0]+" "+document.form1.dollarprice.value+" "+ardollar[2];
			}
		}
	}
}
</script>

<SCRIPT LANGUAGE="JavaScript">

var imagepath="<?=$imagepath_multi?>";
var prdimagepath="<?=$Dir?>";
var setcnt=0;

function primg_preview(img,width,height) {

	if($("img[name='primg']")!=null) {
		setcnt=0;
		$("img[name='primg']").attr("src",imagepath+img);
		$("img[name='primg']").attr("data-zoom-image",imagepath+img);
		if(width>0){
			$("img[name='primg']").css("width",width+"px");
		}
		if(height>0){
			$("img[name='primg']").css("height",height+"px");
		}
		//alert($("img[name='primg']").css("width"));
	} 
	/*
	else {
		if(setcnt<=10) {
			setcnt++;
			setTimeout("primg_preview('"+img+"','"+width+"','"+height+"')",500);
		}
	}
	*/
}

function primg_preview3(img,width,height) {

	if($("img[name='primg']")!=null) {
		$("img[name='primg']").attr("src",imagepath+img);

		if(width>0){
			$("img[name='primg']").css("width",width+"px");
		}
		if(height>0){
			$("img[name='primg']").css("height",height+"px");
		}
	}
}

function primg_preview2(img,width,height) {
	obj = event.srcElement;
	clearTimeout(obj._tid);
	obj._tid=setTimeout("primg_preview3('"+img+"','"+width+"','"+height+"')",500);
}

function primg_preview4(img,width,height){
	obj = event.srcElement;
	clearTimeout(obj._tid);
	
	obj._tid=setTimeout(function(){
		if($("img[name='primg']")!=null) {
			setcnt=0;
			$("img[name='primg']").attr("src",prdimagepath+"data/shopimages/product/"+img);

			if(width>0){
				$("img[name='primg']").css("width",width+"px");
			}
			if(height>0){
				$("img[name='primg']").css("height",height+"px");
			}
		} 
	},500);
}


function primg_preview_def(img,width,height) {
	
	if($("img[name='primg']")!=null) {
		setcnt=0;
		$("img[name='primg']").attr("src",prdimagepath+"data/shopimages/product/"+img);
		$("img[name='primg']").attr("data-zoom-image",prdimagepath+"data/shopimages/product/"+img);

		if(width>0){
			$("img[name='primg']").css("width",width+"px");
		}
		if(height>0){
			$("img[name='primg']").css("height",height+"px");
		}
		//alert($("img[name='primg']").css("width"));
	} else {
		if(setcnt<=10) {
			setcnt++;
			setTimeout("primg_preview('"+img+"','"+width+"','"+height+"')",500);
		}
	}
}



//primg_preview('<?=$yesimage[0]?>','<?=$xsize[0]?>','<?=$ysize[0]?>');


		/*
			상품 옵션 선택 Start
		*/
		var option1TempValue = $(".selectOption1").prev().html();
		var clickOption1 = false;
		$(".selectOption1 li").click(function(){
			if($(this).children().attr('opt')){
				
				$(this).parent().prev().html($(this).children().html());
				$("#ID_option1").val($(this).children().attr('opt'));
				$("#ID_option1price").val($(this).children().attr('opt1'));
				$(this).parent().parent().removeClass('open');
				option1TempValue = $(this).children().html();
				clickOption1 = true;
				change_total_price();
			}else{
				alert("품절된 상품 입니다.");
				if(!clickOption1){
					$(this).parent().prev().removeClass('selected');
				}
				$(this).parent().prev().html(option1TempValue);
				$(this).parent().parent().removeClass('open');
			}
		})
		var option2TempValue = $(".selectOption2").prev().html();
		var clickOption2 = false;
		$(".selectOption2 li").click(function(){
			if($(this).children().attr('opt2')){
				$(this).parent().prev().html($(this).children().html());
				$("#ID_option2").val($(this).children().attr('opt2'));
				$(this).parent().parent().removeClass('open');
				option2TempValue = $(this).children().html();
				clickOption2 = true;
				change_total_price();
			}else{
				alert("품절된 상품 입니다.");
				if(!clickOption2){
					$(this).parent().prev().removeClass('selected');
				}
				$(this).parent().prev().html(option2TempValue);
				$(this).parent().parent().removeClass('open');
			}
		})
		/*
			상품 옵션 선택 End
		*/

</SCRIPT>
 
<!-- 레이어 팝업 스타일 -->
<style type="text/css">
	.layer {display:none; position:fixed; _position:absolute; top:0; left:0; width:100%; height:100%; z-index:100;}
		.layer .bg {position:absolute; top:0; left:0; width:100%; height:100%; background:#000; opacity:.5; filter:alpha(opacity=50);}
		.layer .pop-layer {display:block;}

	.pop-layer {display:none; position: absolute; top: 50%; left: 50%; width: 855px; height:auto;  background-color:#fff; border: 5px solid #716d6c; z-index: 10;}	
	.pop-layer .pop-container {padding: 20px 25px;}
	.pop-layer p.ctxt {color: #666; line-height: 25px;}
	.pop-layer .btn-r {width: 100%; margin:10px 0 20px; padding-top: 10px; border-top: 1px solid #DDD; text-align:right;}

	a.cbtn {display:inline-block; height:25px; padding:0 14px 0; border:1px solid #5e5e5e; background-color:#585858; font-size:13px; color:#fff; line-height:25px;}	
	a.cbtn:hover {border: 1px solid #5e5e5e; background-color:#5e5e5e; color:#fff;}
	
	a.ibtn {display:inline-block; height:25px; padding:0 14px 0; border:1px solid #5e5e5e; background-color:#585858; font-size:13px; color:#fff; line-height:25px;}	
	a.ibtn:hover {border: 1px solid #5e5e5e; background-color:#5e5e5e; color:#fff;}
</style>
<!--// 레이어 팝업 스타일 -->

<!--//레이어 팝업 스크립트 -->
<script type="text/javascript">
	function layer_open(el){

		var temp = $('#' + el);
		var bg = temp.prev().hasClass('bg');	//dimmed 레이어를 감지하기 위한 boolean 변수

		if(bg){
			//$('.layer').fadeIn();	//'bg' 클래스가 존재하면 레이어가 나타나고 배경은 dimmed 된다. 
			$('.' + el).fadeIn();	//'bg' 클래스가 존재하면 레이어가 나타나고 배경은 dimmed 된다. 
		}else{
			temp.fadeIn();
		}

		// 화면의 중앙에 레이어를 띄운다.
		if (temp.outerHeight() < $(document).height() ) temp.css('margin-top', '-'+temp.outerHeight()/2+'px');
		else temp.css('top', '0px');
		if (temp.outerWidth() < $(document).width() ) temp.css('margin-left', '-'+temp.outerWidth()/2+'px');
		else temp.css('left', '0px');

		temp.find('a.cbtn').click(function(e){
			if(bg){
				//$('.layer').fadeOut(); //'bg' 클래스가 존재하면 레이어를 사라지게 한다. 
				$('.' + el).fadeOut(); //'bg' 클래스가 존재하면 레이어를 사라지게 한다. 
			}else{
				temp.fadeOut();
			}
			e.preventDefault();
		});

		$('.layer .bg').click(function(e){	//배경을 클릭하면 레이어를 사라지게 하는 이벤트 핸들러
		
			if(bg){
				//$('.layer').fadeOut(); //'bg' 클래스가 존재하면 레이어를 사라지게 한다. 
				$('.' + el).fadeOut(); //'bg' 클래스가 존재하면 레이어를 사라지게 한다. 
			}else{
				temp.fadeOut();
			}
			
			//$('.layer').fadeOut();
			e.preventDefault();
		});

	}				
</script>
<!--//레이어 팝업 스크립트 -->
