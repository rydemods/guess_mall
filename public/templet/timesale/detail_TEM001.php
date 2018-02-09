<?
include_once dirname(__FILE__)."/../../lib/product.class.php";
$product = new PRODUCT();
$dc_data = $product->getProductDcRate($productcode);

?>

<script type="text/javascript">

	var gBlock = 0;
	var gGotopage = 1;
	var gqBlock = 0;
	var gqGotopage = 1;
		/*
			상품 옵션 선택 Start
		*/
		var option1TempValue = $(".selectOption1").prev().html();
		var clickOption1 = false;
		$(".selectOption1 li").click(function(){
			if($(this).children().attr('opt')){
				$(this).parent().prev().html($(this).children().html());
				$("#ID_option1").val($(this).children().attr('opt'));
				$(this).parent().parent().removeClass('open');
				option1TempValue = $(this).children().html();
				clickOption1 = true;
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


	
	
</script>
<script>

$(document).ready(function() {
	
	function event_counter(){

			var h_html='<div class="hour">';
			var m_html='<div class="min">';
			var s_html='<div class="sec">';
			
			
			$.post( "ajax_for_today_date.php", function(data){
				$("#ddate").val(data);
			});
			
			today = new Date($("#ddate").val());
			d_day = new Date($("#edate").val());
			daysround=0;
			
			hours = (d_day - today) / 1000 / 60 / 60;
			hoursround = Math.floor(hours);
			minutes = (d_day - today) / 1000 /60 - (24 * 60 * daysround) - (60 * hoursround);
			minutesround = Math.floor(minutes);
			seconds = (d_day - today) / 1000 - (24 * 60 * 60 * daysround) - (60 * 60 * hoursround) -
			(60 * minutesround);
			secondsround = Math.round(seconds);
			
			if(hoursround>999){
				hoursround=999;
			}


			hoursround = hoursround.toString();
			minutesround = minutesround.toString();
			secondsround = secondsround.toString();
			
			//시간끝나면 버튼 사라짐
			if(parseInt(hoursround)<0 || parseInt(minutesround)<0 || parseInt(secondsround)<0 ){
				hoursround = "000";
				minutesround = "00";
				secondsround = "00";

				$(".btn_container_time").html("<img src='../img/common/sale_comp.png'>");
			}

			//잔여 없으면 버튼 사라짐

			if($("#stock").val()<=0){
				hoursround = "000";
				minutesround = "00";
				secondsround = "00";
				$(".btn_container_time").html("<img src='../img/common/sale_comp.png'>");
			}

			//시간
			for(var h=0;h<(3-hoursround.length);h++){
				var h_html = h_html+"<span>0</span>";
			}
			for(var h=0;h<hoursround.length;h++){
				var h_html = h_html+"<span>"+hoursround.charAt(h)+"</span>";
			}
			h_html=h_html+"</div>";
			
			//분
			if(minutesround.length==1){
				var m_html = m_html+"<span>0</span>";
			}
			for(var h=0;h<minutesround.length;h++){
				var m_html = m_html+"<span>"+minutesround.charAt(h)+"</span>";
			}
			m_html=m_html+"</div>";
			
			//초
			if(secondsround.length==1){
				var s_html = s_html+"<span>0</span>";
			}
			for(var h=0;h<secondsround.length;h++){
				var s_html = s_html+"<span>"+secondsround.charAt(h)+"</span>";
			}
			s_html=s_html+"</div>";
			
			$(".timer").html(h_html+m_html+s_html);
			
			setTimeout(event_counter, 1000);
	}

	event_counter();
	
});
</script>


<div class="main_wrap">
	<div class="containerBody">	
		<!-- 타임세일 달력 레이어 -->
		<!--
		<div class="time_sail_calender_wrap">
			<div class="title"><a href="#" class="close"></a></div>
			<div class="celender_layer">
			</div>
		</div>
		-->
		<!--상품 상세-->
		<div class="goods_view_detail_wrap" style="margin-bottom: 20px; width: 1100px;">
			<div class="view_time_sales">
			<h2 style="margin: 5px 0px 0px 5px;">타임세일 </h2>
			<form name=form1 id = 'ID_goodsviewfrm' method=post action="<?=$Dir.FrontDir?>basket.php">
			<input type="hidden" name="ordertype" />
			<input type="hidden" name="productcode" value="<?=$_odata["productcode"]?>" />
			<input type=hidden name=optionArr id="optionArr" value="">
			<input type=hidden name=priceArr id="priceArr" value="">
			<input type=hidden name=quantityArr id="quantityArr" value="">
				<div class="community_timesale">
				<div class="timesale_content">
					<img class="image" src="../data/shopimages/timesale/<?=$_odata['view_v_img']?>" alt="" style="width:879px;height:506px"/>
					<div class="percent"><span><em><?=$_odata['ratio']?></em>%</span></div>
					<div class="timesale_info">
						<h3><?=$_odata['title']?></h3>
						<input type="hidden" id="edate" value="<?=$_odata['e_date']?>">
						<input type="hidden" id="ddate" value="<?=$ddate?>">
						<input type="hidden" id="stock" value="<?=($_odata['ea']-$_odata['sale_cnt'])?>">
						<div class="timer"></div>		
						<div class="price">
						<p class="sell"><span>판매가</span><span class="right"><del><?=number_format($_pdata->consumerprice)?></del>원</span></p>
						<p class="special"><span>특가</span><span class="right"><em><?=number_format($_odata['s_price'])?></em>원</span></p>
					</div>			
						<!--<div class="condition">
						<span class="left"><em><?=$_odata['sale_cnt']?>개</em>구매</span><span class="right">잔여<?=number_format($_odata['ea']-$_odata['sale_cnt'])?> / 총<?=number_format($_odata['ea'])?></span>						<?$sale_per = $_odata['ea'] / 100 * $_odata['sale_cnt']; ?>
						<span class="gage"><span class="bar" style="width:<?=$sale_per?>%"></span></span>
						</div>	-->		
						<div class="sleaamount">
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
						<dl>
							<dt><?=$tok[0]?></dt>
							<dd>
								<div class="select_type" style="width:125px;">
									<select name="option1" id="option1" style="width: 125px;" alt='<?=$tok[0]?>'>
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
							</dd>
						</dl>
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
						<dl>
							<dt><?=$tok[0]?></dt>
							<dd>
								<div class="select_type" style="width:125px;">
									<select name="option2" id="option2" style="width: 125px;" alt='<?=$tok[0]?>'>
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
							</dd>
						</dl>
						<?
							}
						?>
						<dl>
							<dt>구매수량</dt>
							<dd>
								<div class="quantity">
								<a href="javascript:change_quantity('dn')" target="_self">수량 1개 빼기</a>
								<input class="textbox" type="text" name="quantity" id="quantity" title="" value="<?=$miniq>1?$miniq:1?>" />
								<a href="javascript:change_quantity('up')" target="_self">수량 1개 더하기</a>
								</div>
							</dd>
						</dl>
						<dl>
							<dt>배송비</dt>
							<dd><?=number_format($_data->deli_basefee)?>원</dd>
						</dl>
					</div>
							<div class="btn_container btn_container_time">
							<a href="javascript:CheckForm('ordernow','')" target="_self"><img src="../img/common/btn_nowbuy.gif" alt="바로구매" /></a>
							<!--a href="#" target="_self"><img src="../image/sub/btn_cart4.png" alt="장바구니" /></a-->
						</div>			
							<img class="icon_arrow" src="../img/common/community_timesale_arrow.png" alt="" />
					</div>
				</div>
				</div>
			
			</form>
			</div>
		</div>
		
	</div>
		<!--상품 상세-->

</div>

<?=$count2?>
<?php $priceindex=0?>


<script language="JavaScript">
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

