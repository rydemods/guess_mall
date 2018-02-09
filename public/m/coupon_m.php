<? 
	$Dir="../";
	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");
	include_once($Dir."lib/product.class.php");
	include_once("lib.inc.php");
	include_once("shopdata.inc.php");

	include ("header.popup.inc.php");

	$product_class = new PRODUCT();

	if(strlen($_ShopInfo->getMemid())==0) {
		exit;
	}
	$sumprice=$_POST["sumprice"];
	$sumprice_t=$_POST["sumprice"];
	$used=$_POST["used"];
	$chk_mobile=$_POST["chk_mobile"];
?>
<SCRIPT LANGUAGE="JavaScript">
<!--

function comma(x)
{
	var temp = "";
	var x = String(uncomma(x));

	num_len = x.length;
	co = 3;
	while (num_len>0){
		num_len = num_len - co;
		if (num_len<0){
			co = num_len + co;
			num_len = 0;
		}
		temp = ","+x.substr(num_len,co)+temp;
	}
	return temp.substr(1);
}

function uncomma(x)
{
	var reg = /(,)*/g;
	x = parseInt(String(x).replace(reg,""));
	return (isNaN(x)) ? 0 : x;
}

// 개발자 도구 띄우는 F12 키 막기
/*
document.onkeydown = function(e) {
    e = e || window.event;
    var nKeyCode = e.keyCode;
    try {
		if(nKeyCode == 123) {
            if(!+"\v1") {  // IE일 경우
                e.keyCode = e.returnValue = 0;
            } else {  // IE가 아닌 경우
                e.preventDefault();
            }
        }
    } catch(err) {}
};*/
//window.moveTo(10,10);
window.resizeTo(630,650);
var all_list=new Array();
var bankStr = '해당 쿠폰은 현금결제시에만 사용가능합니다.\n무통장입금을 선택하셔야만 쿠폰 사용이 가능합니다.';
function prvalue() {
	var argv = prvalue.arguments;   
	var argc = prvalue.arguments.length;
	
	this.classname		= "prvalue"
	this.debug			= false;
	this.bank_only		= new String((argc > 0) ? argv[0] : "N");
	this.sale_type		= new String((argc > 1) ? argv[1] : "");
	this.use_con_type2	= new String((argc > 2) ? argv[2] : "");
	this.sale_money		= new String((argc > 3) ? argv[3] : "");
	this.prname			= new String((argc > 4) ? argv[4] : "");
	this.prprice		= new String((argc > 5) ? argv[5] : "");
}

function CheckForm() {
	var selectCoupon = 0;
	var tempDcPrice = 0;
	var tempReservePrice = 0;
	var htmlCouponLayer = "";

	$(".CLS_goods_coupon_code").each(function(){
		if($(this).val()){
			var couponDataArray = $(this).val().split('||');
			htmlCouponLayer += "<input type='hidden' name='coupon_code_goods[]' value = '"+couponDataArray[0]+"||"+couponDataArray[1]+"'>";
			
			if(couponDataArray[3] == 'dc'){
				tempDcPrice += parseInt(couponDataArray[2]);
			}else if(couponDataArray[3] == 'pt'){
				tempReservePrice +=  parseInt(couponDataArray[2]);
			}
			selectCoupon++;
		}
	})

	if(!$("input[name='coupon_choice']:checked").attr('name') && selectCoupon == 0){
		alert("사용하실 쿠폰을 선택하세요.");
		//document.form1.coupon_code.focus();
		return;
	}

	$("input[name='coupon_choice']").each(function(){
		if($(this).prop('checked')){
			htmlCouponLayer += "<input type='hidden' name='coupon_code_basket[]' value = '"+$(this).next().val()+"'>";
			opener.document.form1.coupon_code.value = $(this).next().val();
		}
	})
	$("#ID_coupon_code_layer", opener.document).html(htmlCouponLayer);
	opener.document.form1.bank_only.value=document.form1.bank_only.value;
	opener.document.form1.coupon_dc.value=0;
	$(".CLS_saleCoupon", opener.document).html('0원');

	s_delivery_type=document.form1.delivery_type.value;
	s_total_price=parseInt(document.getElementById("total_price").innerHTML.replace(/\,/g,""));
	s_coupon_dc=parseInt(document.form1.coupon_dc.value);
	s_delivery_price=parseInt(opener.document.getElementById("delivery_price").innerHTML.replace(/\,/g,""));
	s_usereserve=parseInt(opener.document.form1.usereserve.value);
	
	if(opener.document.form1.okreserve==true){
		s_okreserve=parseInt(opener.document.form1.okreserve.value);
	}
		
	if(s_delivery_type=='N'){
		 goods_total=s_total_price;
		if(s_delivery_price<s_usereserve) t_delivery=s_delivery_price;	
		else t_delivery=s_usereserve;	
	}else{
		 goods_total=s_total_price+s_delivery_price;	
		 t_delivery=0;
	}

	/* 개별 쿠폰가격과 장바구니 쿠폰가격을 합산 */
	if(tempDcPrice > 0){
		document.form1.coupon_dc.value=parseInt(document.form1.coupon_dc.value) + parseInt(tempDcPrice);
	}
	if(tempReservePrice > 0){
		document.form1.coupon_reserve.value=parseInt(document.form1.coupon_reserve.value) + parseInt(tempReservePrice);
	}

	if(goods_total<s_coupon_dc){
		if(opener.document.form1.okreserve==true){
			opener.document.form1.okreserve.value=s_okreserve+s_usereserve-t_delivery;
		}
		opener.document.form1.usereserve.value=t_delivery;
		
		$(".CLS_saleMil", opener.document).html(comma(t_delivery)+'원');

		opener.document.form1.coupon_dc.value=goods_total;

		$(".CLS_saleCoupon", opener.document).html(comma(goods_total)+'원');

		opener.document.getElementById("price_sum").innerHTML=comma(parseInt(opener.document.form1.total_sum.value)-parseInt(goods_total)-parseInt(t_delivery));
		
	}else{
		if(goods_total<(s_coupon_dc+s_usereserve)){
			if(opener.document.form1.okreserve==true){
				opener.document.form1.okreserve.value=s_okreserve+(s_usereserve-(goods_total-s_coupon_dc));
			}
			opener.document.form1.usereserve.value=goods_total-s_coupon_dc;

			$(".CLS_saleMil", opener.document).html(comma(goods_total-s_coupon_dc)+'원');

			dc_price=parseInt(opener.document.form1.usereserve.value)+parseInt(document.form1.coupon_dc.value);
			opener.document.form1.coupon_dc.value=document.form1.coupon_dc.value;
			
			$(".CLS_saleCoupon", opener.document).html(comma(document.form1.coupon_dc.value)+'원');

			opener.document.getElementById("price_sum").innerHTML=comma(parseInt(opener.document.form1.total_sum.value)-parseInt(dc_price));
		}else{
			dc_price=parseInt(opener.document.form1.usereserve.value)+parseInt(document.form1.coupon_dc.value);
			opener.document.form1.coupon_dc.value=document.form1.coupon_dc.value;

			$(".CLS_saleCoupon", opener.document).html(comma(document.form1.coupon_dc.value)+'원');

			opener.document.getElementById("price_sum").innerHTML=comma(parseInt(opener.document.form1.total_sum.value)-parseInt(dc_price));
		}
	}
	opener.document.form1.coupon_reserve.value=document.form1.coupon_reserve.value;
	
	//에스크로를 미리 선택하고 할인으로 에스크로 금액 이하로 떨어질 때 에스크로 결제가 되는 문제
	opener.payment_reset();

	window.close();
}

function coupon_cancel() {
	$("#ID_coupon_code_layer", opener.document).html("");
	dc_price=parseInt(opener.document.form1.usereserve.value);
	opener.document.form1.coupon_dc.value=0;

	$(".CLS_saleCoupon", opener.document).html('0원');

	opener.document.form1.coupon_reserve.value=0;
	opener.document.getElementById("price_sum").innerHTML=comma(parseInt(opener.document.form1.total_sum.value)-parseInt(dc_price));
	
	opener.document.form1.coupon_code.value="";
	opener.document.form1.bank_only.value="N";
	window.close();
}
//-->
</SCRIPT>
</head>
<body topmargin="0" leftmargin="0" rightmargin="0" marginheight="0" marginwidth="0">
<form name=form1 method=post>
	<input type=hidden name=bank_only value="N">
	<input type=hidden name=coupon_dc value="0">
	<input type=hidden name=coupon_reserve value="0">
	<input type=hidden name=delivery_type value="N">
	<input type="hidden" name=rcall_type value="<?=$_data->rcall_type?>">
	<div class="popup_wrapper">
		<div class="pop_container">
			<div class="title">
				<h3>쿠폰조회 및 적용</h3>
				<a href="javascript:coupon_cancel();" class="close"></a>
			</div>
			<div class="pop_body pt_20">
				<table class="coupon">
					<colgroup>
						<col style="width:10%" /><col style="width:40%" /><col style="width:20%" /><col style="width:20%" />
					</colgroup>
					<thead>
					<tr>
						<th>선택</th>
						<th>쿠폰명</th>	
						<th>적용상품</th>
						<th>혜택</th>
					</tr>
					</thead>
					<tbody>
					<?
					$id=$_ShopInfo->getMemid();
					$sql = "SELECT a.coupon_code, a.coupon_name, a.sale_type, a.sale_money, a.sale_max_money, a.bank_only, a.productcode,a.amount_floor, a.delivery_type,";
					$sql.= "a.mini_price, a.use_con_type1, a.use_con_type2, a.use_point, a.vender, b.date_start, b.date_end, a.coupon_use_type ";
					$sql.= "FROM tblcouponinfo a, tblcouponissue b ";
					$sql.= "WHERE b.id='{$id}' AND a.coupon_code=b.coupon_code AND b.date_start<='".date("YmdH")."' ";
					$sql.= "AND (b.date_end>='".date("YmdH")."' OR b.date_end='') ";
					$sql.= "AND b.used='N' AND a.coupon_use_type != '2' ";
					$sql.= "ORDER BY coupon_use_type ASC";
					$result = pmysql_query($sql,get_db_conn());
					$cnt=0;
					$valueCount=0;
					while($row=pmysql_fetch_object($result)) {
						$coupon_code[$cnt]		= $row->coupon_code;
						$use_con_type2[$cnt]	= $row->use_con_type2;
						$sale_type[$cnt]		= $row->sale_type;
						$use_con_type1[$cnt]	= $row->use_con_type1;
						$sale_money[$cnt]		= $row->sale_money;
						$mini_price[$cnt]		= $row->mini_price;
						$vender[$cnt]			= $row->vender;
						$bank_only[$cnt]		= $row->bank_only;
						$amount_floor[$cnt]		= $row->amount_floor;
						$delivery_type[$cnt]		= $row->delivery_type;
						$delivery_type[$cnt]		= $row->delivery_type;
						$sale_max_money[$cnt]		= $row->sale_max_money;
						
							
						$prleng=strlen($row->productcode);

						list($code_a,$code_b,$code_c,$code_d) = sscanf($row->productcode,'%3s%3s%3s%3s');

						$likecode=$code_a;
						if($code_b!="000") $likecode.=$code_b;
						if($code_c!="000") $likecode.=$code_c;
						if($code_d!="000") $likecode.=$code_d;

						if($prleng==18) $productcode[$cnt]=$row->productcode;
						else $productcode[$cnt]=$likecode;

						$cnt++;
						if($row->sale_type<=2) {
							$dan="%";
						} else {
							$dan="원";
						}
						if($row->sale_type%2==0) {
							$sale = "할인";
						} else {
							$sale = "적립";
						}
						
						if($row->productcode=="ALL") {
							if($row->vender==0) {
								$product="전체상품";
							} else {
								$product="해당 입점업체 전체상품";
							}
						} else {
							$product = getCodeLoc($row->productcode);				
							if($row->vender>0) $product.=" (일부상품 제외)";

							if($prleng==18) {
								$sql2 = "SELECT productname as product FROM tblproduct WHERE productcode='{$row->productcode}' ";
								$result2 = pmysql_query($sql2,get_db_conn());
								if($row2 = pmysql_fetch_object($result2)) {
									$product.= " > ".$row2->product;
								}
								pmysql_free_result($result2);
							}
							if($row->use_con_type2=="N") {
								if($row->vender==0) {
									$product="[{$product}] 제외";
								} else {
									$product="[{$product}] 제외한 일부상품";
								}
							}
						}
						$t = sscanf($row->date_start,'%4s%2s%2s%2s');
						$s_time = strtotime("{$t[0]}-{$t[1]}-{$t[2]} {$t[3]}:00:00");
						$t = sscanf($row->date_end,'%4s%2s%2s%2s');
						$e_time = strtotime("{$t[0]}-{$t[1]}-{$t[2]} {$t[3]}:00:00");
						#$divisionStr = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
						#$date=date("Y.m.d H",$s_time)."시 ~<br>".$divisionStr."".date("Y.m.d H",$e_time)."시";
						$date = $divisionStr."".date("Y.m.d H",$e_time)."시";
					?>
						<tr>
							<td>
								<input type = 'checkbox' name = 'coupon_choice' class = 'CLS_coupon_choice<?=$row->coupon_use_type?>' onClick = 'change_group(this.value, this)' value = '<?=$valueCount?>'><input type = 'hidden' value = '<?=$coupon_code[$valueCount]?>'>
							</td>
							<td class="ta_l">
								<?=$row->coupon_name?><br>
								<IMG src="<?=$Dir?>images/common/coupon_open_btn1.gif" align="absMiddle" border="0" style="MARGIN-RIGHT:2px"><font color="#000000" style="FONT-SIZE:11px;LETTER-SPACING:-0.5pt"><b><?=$date?></b>
							</td>
							<td><?=$product?></td>
							<td>
								<?
									if($sale=="할인"){
										$colorCode = "#FF0000";
									}else{
										$colorCode = "#0000FF";
									}
								?>
								<font color="<?=$colorCode?>"><?=number_format($row->sale_money).$dan.$sale?></font>
							</td>
						</tr>
					<?
						/*
						echo "<tr>\n";
						echo "	<td><input type = 'checkbox' name = 'coupon_choice' class = 'CLS_coupon_choice".$row->coupon_use_type."' onClick = 'change_group(this.value, this)' value = '".$valueCount."'><input type = 'hidden' value = '".$coupon_code[$valueCount]."'></td>\n";
						echo "	<td>\n";
						echo "	<TABLE cellSpacing=\"0\" cellPadding=\"0\" width=\"100%\">\n";
						echo "	<TR>\n";
						echo "		<TD height=\"16\"><font color=\"#333333\">{$row->coupon_name}</font></TD>\n";
						echo "	</TR>\n";
						echo "	<TR>\n";
						echo "		<TD height=\"16\" nowrap><IMG src=\"{$Dir}images/common/coupon_open_btn1.gif\" align=\"absMiddle\" border=\"0\" style=\"MARGIN-RIGHT:2px\"><font color=\"#000000\" style=\"FONT-SIZE:11px;LETTER-SPACING:-0.5pt\"><b>{$date}</b></TD>\n";
						echo "	</TR>\n";
						echo "	</TABLE>\n";
						echo "	</td>\n";
						echo "	<td><font color=\"#333333\">{$product}</font></td>\n";
						echo "	<td><font color=\"#333333\">".($row->mini_price=="0"?"제한 없음":number_format($row->mini_price)."원 이상")."</font></td>\n";
						echo "	<td><font color=\"".($sale=="할인"?"#FF0000":"#0000FF")."\">".number_format($row->sale_money).$dan.$sale."</font></td>\n";
						echo "</tr>\n";
						*/
						$valueCount++;
					}
					pmysql_free_result($result);
					if($cnt==0) {
						echo "<tr height=\"30\"><td colspan=\"5\" align=\"center\">보유한 쿠폰내역이 없습니다.</td></tr>\n";
					}

				if($used!="N"){
					$sql = "SELECT a.opt1_idx,a.opt2_idx,a.optidxs,a.quantity,b.productcode,b.productname,b.sellprice, ";
					$sql.= "b.option_price,b.option_quantity,b.option1,b.option2,b.vender,a.assemble_list,a.assemble_idx, ";
					$sql.= "b.sellprice*a.quantity as realprice FROM tblbasket a, tblproduct b ";
					$sql.= "WHERE a.tempkey='".$_ShopInfo->getTempkey()."' ";
					$sql.= "AND a.productcode=b.productcode ";
					$result=pmysql_query($sql,get_db_conn());
					$sumprice=array();
					$basketcnt=array();
					$prcode=array();
					$prname=array();
					$productall=array();
					while($row = pmysql_fetch_object($result)) {
						//환율적용
						$row->sellprice = exchageRate($row->sellprice);
						
						if(ord($prcode[0])) {
							if(substr($row->productcode,0,12)==substr($prcode[0],0,12)) $prcode[0]=substr($prcode[0],0,12);
							elseif(substr($row->productcode,0,9)==substr($prcode[0],0,9)) $prcode[0]=substr($prcode[0],0,9);
							elseif(substr($row->productcode,0,6)==substr($prcode[0],0,6)) $prcode[0]=substr($prcode[0],0,6);
							elseif(substr($row->productcode,0,3)==substr($prcode[0],0,3)) $prcode[0]=substr($prcode[0],0,3);
							else $prcode[0]="";
						}
						if((int)$basketcnt[0]==0) {
							$prcode[0]=$row->productcode;
							$prname[0]=str_replace('"','',strip_tags($row->productname));
						} else {
							$prname[0].="<br>".str_replace('"','',strip_tags($row->productname));
						}
						$productall[0][$basketcnt[0]]["prcode"]=$row->productcode;
						$productall[0][$basketcnt[0]]["prname"]=str_replace('"','',strip_tags($row->productname));
						if($row->vender>0) {
							if(ord($prcode[$row->vender])) {
								if(substr($row->productcode,0,12)==substr($prcode[$row->vender],0,12)) $prcode[$row->vender]=substr($prcode[$row->vender],0,12);
								elseif(substr($row->productcode,0,9)==substr($prcode[$row->vender],0,9)) $prcode[$row->vender]=substr($prcode[$row->vender],0,9);
								elseif(substr($row->productcode,0,6)==substr($prcode[$row->vender],0,6)) $prcode[$row->vender]=substr($prcode[$row->vender],0,6);
								elseif(substr($row->productcode,0,3)==substr($prcode[$row->vender],0,3)) $prcode[$row->vender]=substr($prcode[$row->vender],0,3);
								else $prcode[$row->vender]="";
							}
							if((int)$basketcnt[$row->vender]==0) {
								$prcode[$row->vender]=$row->productcode;
								$prname[$row->vender]=str_replace('"','',strip_tags($row->productname));
							} else {
								$prname[$row->vender].="<br>".str_replace('"','',strip_tags($row->productname));
							}
							$productall[$row->vender][$basketcnt[$row->vender]]["prcode"]=$row->productcode;
							$productall[$row->vender][$basketcnt[$row->vender]]["prname"]=str_replace('"','',strip_tags($row->productname));
						}

						if(preg_match("/^\[OPTG\d{4}\]$/",$row->option1)){
							$optioncode = substr($row->option1,5,4);
							$row->option1="";
							$row->option_price="";
							if(!empty($row->optidxs)) {
								$tempoptcode = rtrim($row->optidxs,',');
								$exoptcode = explode(",",$tempoptcode);

								$sqlopt = "SELECT * FROM tblproductoption WHERE option_code='{$optioncode}' ";
								$resultopt = pmysql_query($sqlopt,get_db_conn());
								if($rowopt = pmysql_fetch_object($resultopt)){
									$optionadd = array (&$rowopt->option_value01,&$rowopt->option_value02,&$rowopt->option_value03,&$rowopt->option_value04,&$rowopt->option_value05,&$rowopt->option_value06,&$rowopt->option_value07,&$rowopt->option_value08,&$rowopt->option_value09,&$rowopt->option_value10);
									$opti=0;
									$option_choice = $rowopt->option_choice;
									$exoption_choice = explode("",$option_choice);
									while(ord($optionadd[$opti])){
										if($exoptcode[$opti]>0){
											$opval = explode("",str_replace('"','',$optionadd[$opti]));
											$exop = explode(",",str_replace('"','',$opval[$exoptcode[$opti]]));
											$row->realprice+=($row->quantity*$exop[1]);
										}
										$opti++;
									}
								}
							}
						}

						if (ord($row->option_price)==0) {
							
							$price = $row->realprice;
						
						} else if (ord($row->opt1_idx)) {
							
							$option_price = $row->option_price;
							$pricetok=explode(",",$option_price);
							$price = $pricetok[$row->opt1_idx-1]*$row->quantity;
						}

						### 타임 세일 / 오늘의 특가 가격으로 재 셋팅
						$timesale_sellprice = 0;
						$timesale_sellprice = getSpeDcPrice($row->productcode);
						if($timesale_sellprice > 0) $price = $timesale_sellprice*$row->quantity;

						$dc_data = $product_class->getProductDcRate($row->productcode);
						$salemoney = getProductDcPrice($price,$dc_data[price]);
						
						
						$productall[0][$basketcnt[0]]["price"]=$price;
						$sumprice[0] += $price;


						
				
						
						if($row->vender>0) {
							$productall[$row->vender][$basketcnt[$row->vender]]["price"]=$price;
							$sumprice[$row->vender] += $price;
						}

						$basketcnt[0]++;
						if($row->vender>0) $basketcnt[$row->vender]++;

						if(strlen($row->productcode)==18) {
							$prname2[0][$row->productcode]=str_replace('"','',strip_tags($row->productname));

							$prprice[0][$row->productcode]=$price;
							$prprice[0][substr($row->productcode,0,3)]+=$price;
							if((int)$prbasketcnt[0][substr($row->productcode,0,3)]==0) {
								$prname2[0][substr($row->productcode,0,3)]=str_replace('"','',strip_tags($row->productname));
							} else {
								$prname2[0][substr($row->productcode,0,3)].="<br>".str_replace('"','',strip_tags($row->productname));
							}
							$prbasketcnt[0][substr($row->productcode,0,3)]++;

							$prprice[0][substr($row->productcode,0,6)]+=$price;
							if((int)$prbasketcnt[0][substr($row->productcode,0,6)]==0) {
								$prname2[0][substr($row->productcode,0,6)]=str_replace('"','',strip_tags($row->productname));
							} else {
								$prname2[0][substr($row->productcode,0,6)].="<br>".str_replace('"','',strip_tags($row->productname));
							}
							$prbasketcnt[0][substr($row->productcode,0,6)]++;

							$prprice[0][substr($row->productcode,0,9)]+=$price;
							if((int)$prbasketcnt[0][substr($row->productcode,0,9)]==0) {
								$prname2[0][substr($row->productcode,0,9)]=str_replace('"','',strip_tags($row->productname));
							} else {
								$prname2[0][substr($row->productcode,0,9)].="<br>".str_replace('"','',strip_tags($row->productname));
							}
							$prbasketcnt[0][substr($row->productcode,0,9)]++;

							$prprice[0][substr($row->productcode,0,12)]+=$price;
							if((int)$prbasketcnt[0][substr($row->productcode,0,12)]==0) {
								$prname2[0][substr($row->productcode,0,12)]=str_replace('"','',strip_tags($row->productname));
							} else {
								$prname2[0][substr($row->productcode,0,12)].="<br>".str_replace('"','',strip_tags($row->productname));
							}
							$prbasketcnt[0][substr($row->productcode,0,12)]++;

							if($row->vender>0) {
								$prname2[$row->vender][$row->productcode]=str_replace('"','',strip_tags($row->productname));

								$prprice[$row->vender][$row->productcode]=$price;
								$prprice[$row->vender][substr($row->productcode,0,3)]+=$price;
								if((int)$prbasketcnt[$row->vender][substr($row->productcode,0,3)]==0) {
									$prname2[$row->vender][substr($row->productcode,0,3)]=str_replace('"','',strip_tags($row->productname));
								} else {
									$prname2[$row->vender][substr($row->productcode,0,3)].="<br>".str_replace('"','',strip_tags($row->productname));
								}
								$prbasketcnt[$row->vender][substr($row->productcode,0,3)]++;

								$prprice[$row->vender][substr($row->productcode,0,6)]+=$price;
								if((int)$prbasketcnt[$row->vender][substr($row->productcode,0,6)]==0) {
									$prname2[$row->vender][substr($row->productcode,0,6)]=str_replace('"','',strip_tags($row->productname));
								} else {
									$prname2[$row->vender][substr($row->productcode,0,6)].="<br>".str_replace('"','',strip_tags($row->productname));
								}
								$prbasketcnt[$row->vender][substr($row->productcode,0,6)]++;

								$prprice[$row->vender][substr($row->productcode,0,9)]+=$price;
								if((int)$prbasketcnt[$row->vender][substr($row->productcode,0,9)]==0) {
									$prname2[$row->vender][substr($row->productcode,0,9)]=str_replace('"','',strip_tags($row->productname));
								} else {
									$prname2[$row->vender][substr($row->productcode,0,9)].="<br>".str_replace('"','',strip_tags($row->productname));
								}
								$prbasketcnt[$row->vender][substr($row->productcode,0,9)]++;

								$prprice[$row->vender][substr($row->productcode,0,12)]+=$price;
								if((int)$prbasketcnt[$row->vender][substr($row->productcode,0,12)]==0) {
									$prname2[$row->vender][substr($row->productcode,0,12)]=str_replace('"','',strip_tags($row->productname));
								} else {
									$prname2[$row->vender][substr($row->productcode,0,12)].="<br>".str_replace('"','',strip_tags($row->productname));
								}
								$prbasketcnt[$row->vender][substr($row->productcode,0,12)]++;
							}
						}
						$prname2[0][$prcode[0]]=$prname[0];
						$prprice[0][$prcode[0]]=$sumprice[0];

						$prname2[$row->vender][$prcode[$row->vender]]=$prname[$row->vender];
						$prprice[$row->vender][$prcode[$row->vender]]=$sumprice[$row->vender];

					}
					pmysql_free_result($result);

					$prscript_basket="";
					for($i=0;$i<$cnt;$i++) {
						if($prcode[$vender[$i]]=="") $prcode[$vender[$i]]="ALL";
						$num = strlen($productcode[$i]);
						$tempprcode = substr($prcode[$vender[$i]],0,$num);
					
						if(
						(    $productcode[$i]=="ALL" 
						|| ($use_con_type2[$i]=="Y" && $tempprcode==$productcode[$i])
						|| ($use_con_type1[$i]=="Y" && $use_con_type2[$i]=="Y" && $productcode[$i]!="ALL" && ord($prname2[$vender[$i]][$productcode[$i]]))
						|| ($use_con_type2[$i]=="N" && $use_con_type1[$i]=="N" && ord($prname2[$vender[$i]][$productcode[$i]])==0)
						|| ($use_con_type1[$i]=="Y" && $use_con_type2[$i]=="N" && $productcode[$i]!="ALL" && $sumprice[$vender[$i]]-$prprice[$vender[$i]][$productcode[$i]]>0)
						) 
						
						&& ($mini_price[$i]==0 || $mini_price[$i]<=$sumprice[$vender[$i]]) && isset($prprice[$vender[$i]])) 

						$prscript_basket.="var prval=new prvalue();\n";
						$prscript_basket.="prval.bank_only=\"{$bank_only[$i]}\";\n";
						$prscript_basket.="prval.sale_type=\"{$sale_type[$i]}\";\n";
						$prscript_basket.="prval.use_con_type2=\"{$use_con_type2[$i]}\";\n";
						$prscript_basket.="prval.sale_money=\"{$sale_money[$i]}\";\n";
						$prscript_basket.="prval.amount_floor=\"{$amount_floor[$i]}\";\n";
						$prscript_basket.="prval.delivery_type=\"{$delivery_type[$i]}\";\n";
						$prscript_basket.="prval.maxprice=\"{$sale_max_money[$i]}\";\n";
						
						if($use_con_type2[$i]=="N") {
							$tmp_prname="";
							$tmp_sumprice=0;
							$tmp_prprice=0;
							$kk=0;
							$temparr=$productall[$vender[$i]];
							if(is_array($temparr)) {
								while(list($key,$val)=each($temparr)) {
									if(substr($val["prcode"],0,$num)!=$productcode[$i]) {
										if($kk>0) $tmp_prname.="<br> ";
										$tmp_prname.=$val["prname"];
										$tmp_prprice+=$val["price"];
										$kk++;
									}
									$tmp_sumprice+=$val["price"];
								}
							}
						} else {
							$tmp_prname="";
							$tmp_sumprice=0;
							$tmp_prprice=0;
							$kk=0;
							$temparr=$productall[$vender[$i]];
							if(is_array($temparr)) {
								while(list($key,$val)=each($temparr)) {
									if((substr($val["prcode"],0,$num)==$productcode[$i]) || $productcode[$i]=="ALL") {
										if($kk>0) $tmp_prname.="<br> ";
										$tmp_prname.=$val["prname"];
										$tmp_prprice+=$val["price"];
										$kk++;
									}
									$tmp_sumprice+=$val["price"];
								}
							}
						}
						$prscript_basket.="prval.prname=\"{$tmp_prname}\";\n";
						$prscript_basket.="prval.prprice=\"".number_format($tmp_prprice)."\";\n";
						$prscript_basket.="all_list[{$i}]=prval;\n";
						$prscript_basket.="prval=null;\n";
					}
					?>
					</tbody>
					<tfoot>
						<td colspan=4>
							<?  echo "<script>\n{$prscript_basket}</script>\n"; ?>
							<input type=hidden name=prname value="<?=$prname[0]?>">
							<input type=hidden name=prprice value="<?=number_format($sumprice[0])."원";?>">
							<input type=hidden name=sale_money1 value="─">
							<input type=hidden name=sale_money2 value="─">
							<div id="div_price">
								총 구매 가격 <span id="total_price"><?=number_format($sumprice_t);?></span>원
							</div>
						</td>
					</tfoot>
				</table>


				<table class="coupon" style="margin-top:30px;">
					<colgroup>
						<col style="width:40%" /><col style="width:20%" /><col style="width:20%" /><col style="width:10%" /><col style="width:10%" />
					</colgroup>
					<thead>
					<tr>
						<th>상품명</th>
						<th>상품금액</th>	
						<th>쿠폰선택</th>
						<th>할인</th>
						<th>적립</th>
					</tr>
					</thead>
					<tbody>
					<? 
						$goods_sql = "SELECT a.opt1_idx,a.opt2_idx,a.optidxs,a.quantity,b.productcode,b.productname,b.sellprice, ";
						$goods_sql.= "b.option_price,b.option_quantity,b.option1,b.option2,b.vender,a.assemble_list,a.assemble_idx, ";
						$goods_sql.= "b.sellprice*a.quantity as realprice FROM tblbasket a, tblproduct b ";
						$goods_sql.= "WHERE a.tempkey='".$_ShopInfo->getTempkey()."' ";
						$goods_sql.= "AND a.productcode=b.productcode ";
						/*
						if($chk_mobile){
							$goods_sql.= "AND a.ord_state=true ";
						}
						*/
						$goods_result=pmysql_query($goods_sql,get_db_conn());
						$goods_sumprice=array();
						$goods_basketcnt=array();
						$goods_prcode=array();
						$goods_prname=array();
						$goods_productall=array();
						while($goods_row = pmysql_fetch_object($goods_result)) {	
							//환율적용
							$goods_row->sellprice = exchageRate($goods_row->sellprice);
							$goods_row->realprice = exchageRate($goods_row->realprice);

							$goods_prcode=$goods_row->productcode;
							$goods_prname=str_replace('"','', strip_tags($goods_row->productname));


							if(preg_match("/^\[OPTG\d{4}\]$/",$goods_row->option1)){
								$goods_optioncode = substr($goods_row->option1,5,4);
								$goods_row->option1="";
								$goods_row->option_price="";
								if(!empty($goods_row->optidxs)) {
									$goods_tempoptcode = rtrim($goods_row->optidxs,',');
									$goods_exoptcode = explode(",",$goods_tempoptcode);

									$goods_sqlopt = "SELECT * FROM tblproductoption WHERE option_code='{$goods_optioncode}' ";
									$goods_resultopt = pmysql_query($goods_sqlopt,get_db_conn());
									if($goods_rowopt = pmysql_fetch_object($goods_resultopt)){
										$goods_optionadd = array (&$goods_rowopt->option_value01,&$goods_rowopt->option_value02,&$goods_rowopt->option_value03,&$goods_rowopt->option_value04,&$goods_rowopt->option_value05,&$goods_rowopt->option_value06,&$goods_rowopt->option_value07,&$goods_rowopt->option_value08,&$goods_rowopt->option_value09,&$goods_rowopt->option_value10);
										$goods_opti=0;
										$goods_option_choice = $goods_rowopt->option_choice;
										$goods_exoption_choice = explode("",$goods_option_choice);
										while(ord($goods_optionadd[$goods_opti])){
											if($goods_exoptcode[$goods_opti]>0){
												$goods_opval = explode("",str_replace('"','',$goods_optionadd[$goods_opti]));
												$goods_exop = explode(",",str_replace('"','',$goods_opval[$goods_exoptcode[$goods_opti]]));
												$goods_row->realprice+=($goods_row->quantity*$goods_exop[1]);
											}
											$goods_opti++;
										}
									}
								}
							}

							if (ord($goods_row->option_price)==0) {												
								$goods_price = $goods_row->realprice;											
							} else if (ord($goods_row->opt1_idx)) {												
								$goods_option_price = $goods_row->option_price;
								$goods_pricetok=explode(",",$goods_option_price);
								$goods_price = $goods_pricetok[$goods_row->opt1_idx-1]*$goods_row->quantity;
							}

							### 타임 세일 / 오늘의 특가 가격으로 재 셋팅
							$timesale_sellprice = 0;
							$timesale_sellprice = getSpeDcPrice($goods_row->productcode);
							if($timesale_sellprice > 0){
								$goods_price = $timesale_sellprice * $goods_row->quantity;
							}else{
								$goods_price = $goods_price * $goods_row->quantity;
							}

							$goods_dc_data = $product_class->getProductDcRate($goods_row->productcode);
							$goods_salemoney = getProductDcPrice($goods_price,$goods_dc_data[price]);

													
							$goods_cate_sql = "SELECT * FROM tblproductlink WHERE c_productcode = '".$goods_prcode."'";
							$goods_cate_result = pmysql_query($goods_cate_sql,get_db_conn());
							$categorycode = array();
							while($goods_cate_row=pmysql_fetch_object($goods_cate_result)) {
								list($cate_a, $cate_b, $cate_c, $cate_d) = sscanf($goods_cate_row->c_category,'%3s%3s%3s%3s');
								/*
								if($cate_b == '000') $categorycode[] = $cate_a;
								if($cate_c == '000') $categorycode[] = $cate_a.$cate_b;
								if($cate_d == '000') $categorycode[] = $cate_a.$cate_b.$cate_c;
								if($cate_a != '000' && $cate_b != '000' && $cate_c != '000' && $cate_d != '000') $categorycode[] = $cate_a.$cate_b.$cate_c.$cate_d;
								*/
								$categorycode[] = $cate_a;
								$categorycode[] = $cate_a.$cate_b;
								$categorycode[] = $cate_a.$cate_b.$cate_c;
								$categorycode[] = $cate_a.$cate_b.$cate_c.$cate_d;
							}
							if(count($categorycode) > 0){											
								$addCategoryQuery = "('".implode("', '", $categorycode)."')";
							}else{
								$addCategoryQuery = "('')";
							}
							/*쿠폰 조회 시작*/
							$goods_coupon_sql = "SELECT 
																	a.coupon_code, a.coupon_name, a.sale_type, a.sale_money, 
																	a.sale_max_money, a.bank_only, a.productcode,a.amount_floor, 
																	a.delivery_type,a.mini_price, a.use_con_type1, a.use_con_type2, 
																	a.use_point, a.vender, b.date_start, b.date_end, a.coupon_use_type 
																FROM 
																	tblcouponinfo a 
																	JOIN tblcouponissue b on a.coupon_code=b.coupon_code 
																	LEFT JOIN tblcouponproduct c on b.coupon_code=c.coupon_code
																	LEFT JOIN tblcouponcategory d on b.coupon_code=d.coupon_code
																WHERE 
																	b.id='{$id}' 
																	AND b.date_start<='".date("YmdH")."' 
																	AND (b.date_end>='".date("YmdH")."' OR b.date_end='') 
																	AND b.used='N' 
																	AND a.coupon_use_type = '2' 
																	AND (c.productcode = '".$goods_prcode."' OR (d.categorycode IN ".$addCategoryQuery." AND a.use_con_type2 = 'Y'))
																ORDER BY 
																	coupon_use_type 
																ASC";
							$goods_coupon_result = pmysql_query($goods_coupon_sql,get_db_conn());
							$couponOption = "";
							$couponOptionArray = array();
							while($goods_coupon_row=pmysql_fetch_object($goods_coupon_result)) {
								$goods_coupon_code = $goods_coupon_row->coupon_code;
								$goods_coupon_name = $goods_coupon_row->coupon_name;
								$goods_use_con_type2 = $goods_coupon_row->use_con_type2;
								$goods_sale_type = $goods_coupon_row->sale_type;
								$goods_use_con_type1 = $goods_coupon_row->use_con_type1;
								$goods_sale_money = $goods_coupon_row->sale_money;
								$goods_mini_price = $goods_coupon_row->mini_price;
								$goods_vender = $goods_coupon_row->vender;
								$goods_bank_only = $goods_coupon_row->bank_only;
								$goods_amount_floor = $goods_coupon_row->amount_floor;
								$goods_delivery_type = $goods_coupon_row->delivery_type;
								$goods_delivery_type = $goods_coupon_row->delivery_type;
								$goods_sale_max_money = $goods_coupon_row->sale_max_money;
								
								
								$goods_prleng=strlen($goods_coupon_row->productcode);

								list($goods_code_a,$goods_code_b,$goods_code_c,$goods_code_d) = sscanf($goods_coupon_row->productcode,'%3s%3s%3s%3s');

								$goods_likecode=$goods_code_a;
								if($goods_code_b!="000") $goods_likecode.=$goods_code_b;
								if($goods_code_c!="000") $goods_likecode.=$goods_code_c;
								if($goods_code_d!="000") $goods_likecode.=$goods_code_d;

								if($goods_prleng==18) $goods_productcode=$goods_coupon_row->productcode;
								else $goods_productcode=$goods_likecode;


								//coupon_money=parseInt(all_list[idx].prprice.replace(/\,/g,""))*(parseInt(sale_money.replace(/\,/g,""))*0.01);
								//coupon_money=comma(Math.floor(coupon_money/Math.pow(10,all_list[idx].amount_floor))*Math.pow(10,all_list[idx].amount_floor));

								if($goods_sale_type <= 2){
									$couponDcPrice = ($goods_price*$goods_sale_money)*0.01;
									$couponDcPrice = ($couponDcPrice / pow(10, $goods_amount_floor)) * pow(10, $goods_amount_floor);
								}else{
									$couponDcPrice = $goods_sale_money;
								}
								if($goods_sale_max_money && $goods_sale_max_money < $couponDcPrice){
									$couponDcPrice = $goods_sale_max_money;
								}
								
								if($goods_sale_type%2==0) {
									$saleType = "dc";
								}else {
									$saleType = "pt";
								}

								if($goods_prcode=="") $goods_prcode = "ALL";
								$goods_num = strlen($goods_productcode);
								$goods_tempprcode = substr($goods_prcode[$goods_vender],0,$goods_num);

								if(($goods_mini_price == 0 || $goods_mini_price <= $goods_price) && isset($goods_price)){
									$couponOptionArray[$goods_coupon_code] = "<option value=\"{$goods_coupon_code}||{$goods_prcode}||$couponDcPrice||$saleType||$goods_bank_only\">{$goods_coupon_name}</option>\n";
									/*
										[0] : 쿠폰 코드
										[1] : 상품 코드
										[2] : 쿠폰 할인 / 적립가
										[3] : 할인(pt) / 적립(dc)
										[4] : 현금결제 전용 쿠폰
									*/
								}
							}
							if(count($couponOptionArray) > 0) $couponOption = implode("", $couponOptionArray);
							pmysql_free_result($goods_coupon_result);
					?>
						<tr>
							<td id='idx_prname' class="ta_l"><?=$goods_prname?></td>
							<td id='idx_prprice'><?=number_format($goods_price)."원";?></td>
							<td>
								<select name='goods_coupon_code' class = 'CLS_goods_coupon_code' onchange="change_group_goods(this.value, this)" style="font-size:11px;width:80px;">
								<option value="">쿠폰선택</option>
								<?=$couponOption?>
								</select>
							</td>
							<td class='CLS_idx_sale_money1' style="color:red">─</td>
							<td class='CLS_idx_sale_money2' style="color:red">─</td>
						</tr>

					<?
						}
						pmysql_free_result($goods_result);
					?>
						</tbody>
					</table>
					<!--div class="ta_c mt_30">
						<a href="javascript:CheckForm();"><img src="<?=$Dir?>img/button/btn_entry01.gif" border="0"></a>
						<a href="javascript:coupon_cancel();"><img src="<?=$Dir?>img/button/customer_notice_cancel_btn.gif" border="0" hspace="5"></a>
					</div-->
					<div class="btn"><a href="javascript:CheckForm();" class="ok">적용</a></div>
				<? } else {?>
					<div class="btn"><a href="javascript:window.close();" class="ok">닫기</a></div>
				<? }?>
			</div>
		</div>
	</div>
</form>




































<SCRIPT LANGUAGE="JavaScript">
<!--
	function change_group_goods(idx, obj){
		var couponDataArray = $(obj).val().split('||');
		if(couponDataArray[4] == 'Y' && ($("input[name='dev_payment']:checked", opener.document).val() != "O" && $("input[name='dev_payment']:checked", opener.document).val() != "B")){
			alert(bankStr);
			 $(obj).val("");
			return false;
		}
		if(couponDataArray[3] == 'dc'){
			$(obj).parent().next().html(comma(couponDataArray[2])+"원");
			$(obj).parent().next().next().html('─');
		}else if(couponDataArray[3] == 'pt'){
			$(obj).parent().next().html('─');
			$(obj).parent().next().next().html(comma(couponDataArray[2])+"원");
		}else{
			$(obj).parent().next().html('─');
			$(obj).parent().next().next().html('─');
		}
	}


	function change_group(idx, obj){
		if(all_list[idx].bank_only == 'Y' && ($("input[name='dev_payment']:checked", opener.document).val() != "O" && $("input[name='dev_payment']:checked", opener.document).val() != "B")){
			alert(bankStr);			
			$(obj).prop('checked', false);
			return false;
		}
		var className = $(obj).attr('class');
		if($(obj).prop('checked')){
			$("."+className).prop('disabled', true);
			$(obj).prop('disabled', false);
		}else{
			idx = "";
			$("."+className).prop('disabled', false);
		}


		if(document.form1.rcall_type.value=="N" && opener.document.form1.usereserve.value!=0){
			alert("적립금과 쿠폰은 동시사용이 불가능 합니다.");
			dc_price=parseInt(opener.document.form1.usereserve.value);
			opener.document.form1.coupon_dc.value=0;

			$(".CLS_saleCoupon", opener.document).html('0원');

			opener.document.getElementById("price_sum").innerHTML=comma(parseInt(opener.document.form1.total_sum.value)-parseInt(dc_price));
			opener.document.form1.coupon_code.value="";
			opener.document.form1.bank_only.value="N";
			window.close();
		}
		
		var checkedCount = 0;
		var checkedCountType = "";
		var coupon_money_total = 0;
		var coupon_money_total_integer = 0;
		var coupon_reserve_total = 0;
		var coupon_reserve_total_integer = 0;
		var total_settle = 0;
		$("input[name='coupon_choice']").each(function(){
			if($(this).prop('checked')){
				idx = $(this).val();
				if(idx.length>0) {
					idx = parseInt(idx);
					sale_money="";
					for(var i=0; i<all_list[idx].sale_money.length; i++) {
						var tmp = all_list[idx].sale_money.length-(i+1)
						if(i%3==0 && i!=0) sale_money = ',' + sale_money
						sale_money = all_list[idx].sale_money.charAt(tmp) + sale_money
					}
					if(all_list[idx].sale_type%2==0){
						money1 = document.form1.sale_money1;
						money2 = document.form1.sale_money2;
					} else{
						money1 = document.form1.sale_money2;
						money2 = document.form1.sale_money1;
					}
					if(all_list[idx].sale_type<=2) {
						money1.value=sale_money+"%";
					} else {
						money1.value=sale_money+"원";
					}
					money2.value="─";
					/*
					if(all_list[idx].sale_type%2==0){
						document.all["idx_sale_money1"].innerHTML=money1.value;
						document.all["idx_sale_money2"].innerHTML=money2.value;
					} else{
						document.all["idx_sale_money1"].innerHTML=money2.value;
						document.all["idx_sale_money2"].innerHTML=money1.value;
					}
					*/

					//document.all["idx_prname"].innerHTML=all_list[idx].prname;
					document.form1.bank_only.value=all_list[idx].bank_only;
					document.form1.delivery_type.value=all_list[idx].delivery_type;
					
					s_price=document.getElementById("total_price").innerHTML;
						
					if(all_list[idx].sale_type<=2){
						coupon_money=parseInt(all_list[idx].prprice.replace(/\,/g,""))*(parseInt(sale_money.replace(/\,/g,""))*0.01);
						if(all_list[idx].maxprice > 0 && all_list[idx].maxprice < coupon_money){
							coupon_money = all_list[idx].maxprice;
						}
						coupon_money=comma(Math.floor(coupon_money/Math.pow(10,all_list[idx].amount_floor))*Math.pow(10,all_list[idx].amount_floor));
					}else{
						coupon_money=sale_money;
					}

					if(all_list[idx].sale_type%2==0){
						//dc
						coupon_money_total_integer += parseInt(coupon_money.replace(/\,/g,""));
						coupon_money_total = comma(coupon_money_total_integer);
						document.form1.coupon_dc.value=coupon_money_total.replace(/\,/g,"");

						product_price=parseInt(s_price.replace(/\,/g,""))-parseInt(all_list[idx].prprice.replace(/\,/g,""));
						total_settle=parseInt(all_list[idx].prprice.replace(/\,/g,""))-parseInt(coupon_money_total.replace(/\,/g,""))+product_price;
					}else{
						//reserve
						coupon_reserve_total_integer += parseInt(coupon_money.replace(/\,/g,""));
						coupon_reserve_total = comma(coupon_reserve_total_integer);
						document.form1.coupon_reserve.value=coupon_reserve_total.replace(/\,/g,"");

						product_price=parseInt(s_price.replace(/\,/g,""))-parseInt(all_list[idx].prprice.replace(/\,/g,""));
						total_settle=parseInt(all_list[idx].prprice.replace(/\,/g,""))+product_price;
					}				
					

					if(total_settle<=0) total_settle=0;
					else total_settle=comma(total_settle);

					product_price=comma(product_price);

					if(all_list[idx].sale_type%2==0){
						checkedCountType = "1";
					}else{
						checkedCountType = "2";
					}
				}
				checkedCount++;
			}
		})

		if(checkedCount > 0) {
			if(checkedCountType == 1){
				table_set="";
				table_set+="총 구매 가격 <span id=\"total_price\">"+s_price+"</span>원";
				table_set+="<br><span style=\"color:red; font:bold;\">쿠폰 적용 금액 "+total_settle+"원 | 쿠폰 적립 금액 "+coupon_reserve_total+"원</span>";

				//opener.document.form1.coupon_reserve.value=document.form1.coupon_reserve.value;	
				//opener.document.form1.coupon_reserve.value=0;

				document.getElementById("div_price").innerHTML=table_set;

			}else if(checkedCountType == 2){
				table_set="";
				table_set+="총 구매 가격 <span id=\"total_price\">"+s_price+"</span>원";
				table_set+="<br><span style=\"color:red; font:bold;\">쿠폰 적용 금액 "+total_settle+"원 | 쿠폰 적립 금액 "+coupon_reserve_total+"원</span>";

				//opener.document.form1.coupon_reserve.value=document.form1.coupon_reserve.value;	

				document.getElementById("div_price").innerHTML=table_set;
			}
		}else{
			document.form1.sale_money1.value="─";
			document.form1.sale_money2.value="─";
			document.form1.bank_only.value="N";
			document.form1.delivery_type.value="N";
			/*
			document.all["idx_sale_money1"].innerHTML=document.form1.sale_money1.value;
			document.all["idx_sale_money2"].innerHTML=document.form1.sale_money2.value;
			*/
			//document.all["idx_prname"].innerHTML=document.form1.prname.value;
			
			s_price=document.getElementById("total_price").innerHTML;
			table_set="";
			table_set+="총 구매 가격 <span id=\"total_price\">"+s_price+"</span>원";
			document.getElementById("div_price").innerHTML=table_set;
		}
	}
	function comma(x)
	{
		var temp = "";
		var x = String(uncomma(x));

		num_len = x.length;
		co = 3;
		while (num_len>0){
			num_len = num_len - co;
			if (num_len<0){
				co = num_len + co;
				num_len = 0;
			}
			temp = ","+x.substr(num_len,co)+temp;
		}
		return temp.substr(1);
	}
	function uncomma(x)
	{
		var reg = /(,)*/g;
		x = parseInt(String(x).replace(reg,""));
		return (isNaN(x)) ? 0 : x;
	}
	$(document).ready(function(){
		$(".CLS_coupon_choice1").click(function(){
			if($(".CLS_coupon_choice1:checkbox:checked").length == 1){
				$(".CLS_goods_coupon_code").val("");
				$(".CLS_goods_coupon_code").prop("disabled", true);
				change_group_goods("", $(".CLS_goods_coupon_code"));
			}else if($(".CLS_coupon_choice1:checkbox:checked").length == 0){
				$(".CLS_goods_coupon_code").prop("disabled", false);
			}
		})
		$(".CLS_goods_coupon_code").change(function(){
			var tempValue = 0;
			$(".CLS_goods_coupon_code").each(function(){
				if($(this).val()){
					tempValue++;
				}
			})
			if(tempValue > 0){
				$(".CLS_coupon_choice1").prop('disabled', true);
			}else{
				$(".CLS_coupon_choice1").prop('disabled', false);
			}
		})
	})
//-->
</SCRIPT>
</body>
</html>