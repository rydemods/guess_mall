<?php 
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
include_once($Dir."lib/product.class.php");
$product_class = new PRODUCT();

if(strlen($_ShopInfo->getMemid())==0) {
	exit;
}

$sumprice=$_POST["sumprice"];
$sumprice_t=$_POST["sumprice"];
$used=$_POST["used"];
$chk_mobile=$_POST["chk_mobile"];
$id=$_ShopInfo->getMemid();
//exdebug( $sumprice );
?>
<html>
<head>
<title>쿠폰 조회 및 적용</title>
<meta http-equiv="CONTENT-TYPE" content="text/html;charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
<script type="text/javascript" src="<?=$Dir?>js/jquery-1.10.1.min.js"></script>
<link rel="stylesheet" href="../css/sub.css" />
<link rel="stylesheet" href="../css/common.css" />
<style>
td	{font-family:"굴림,돋움";color:#4B4B4B;font-size:12px;line-height:17px;}
BODY,DIV,form,TEXTAREA,center,option,pre,blockquote {font-family:Tahoma;color:000000;font-size:9pt;}

.input{font-size:12px;BORDER-RIGHT: #DCDCDC 1px solid; BORDER-TOP: #C7C1C1 1px solid; BORDER-LEFT: #C7C1C1 1px solid; BORDER-BOTTOM: #DCDCDC 1px solid; HEIGHT: 18px; BACKGROUND-COLOR: #ffffff;padding-top:2pt; padding-bottom:1pt; height:19px}
.select{color:#444444;font-size:12px;}
.textarea {border:solid 1;border-color:#e3e3e3;font-family:돋음;font-size:9pt;color:333333;overflow:auto; background-color:transparent}
</style>
<SCRIPT LANGUAGE="JavaScript">
<!--
$(document).ready(function(){

	$(".coupon_cancel").click(function(e){
		var mem_info = $(this).prev().children(":first").attr("mem_info");
		var tempValue = 0;
		var select_obj = $(this).prev().find('.' + mem_info);
		$(this).prev().prop('disabled', false);
		$(this).prev().children(":first").attr("mem_info","");
		$(this).prev().children(":first").attr("selected","selected");
		//취소시 해당 카드쿠폰 다른상품에서 사용 가능하게 변경
		CardCoupon_UnChecked( select_obj );
		//취소시 해당 쿠폰을 다른상품에서 사용 가능하게 변경
		$("."+mem_info).css("display","block");
		$("."+mem_info).prop('disabled', false);
		$(this).css("display","none");
		$(this).parent().next().html('─');
		$(this).parent().next().next().html('─');
		$(".CLS_description").eq( $('.coupon_cancel').index( $(this) ) ).html('');
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
		
	});

});

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
};
//window.moveTo(10,10);
window.resizeTo(630,650);
var all_list=new Array();
var bankStr = '해당 쿠폰은 현금결제시에만 사용가능합니다.\n무통장입금을 선택하셔야만 쿠폰 사용이 가능합니다.';

function CheckForm() {
	var selectCoupon = 0;
	var tempDcPrice = 0;
	var tempReservePrice = 0;
	var htmlCouponLayer = "";
	var payment_disabled = false;

	$(".CLS_goods_coupon_code").each(function(){
		if($(this).val()){
			var couponDataArray = $(this).val().split('||');
			htmlCouponLayer += "<input type='hidden' name='coupon_code_goods[]' value = '"+couponDataArray[0]+"||"+couponDataArray[1]+"'>";
			
			if(couponDataArray[3] == 'dc'){
				tempDcPrice += parseInt(couponDataArray[2]);
			}else if(couponDataArray[3] == 'pt'){
				tempReservePrice +=  parseInt(couponDataArray[2]);
			}
			if( couponDataArray[6] == '8' ){
				payment_disabled = true;
			}
			selectCoupon++;
		}
	});
	
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
	//카드쿠폰이 존재할 경우 신용카드 결제만 가능하게 만든다
	if( payment_disabled ){
		opener.payment_card();
	}
	//console.log( $("#ID_coupon_code_layer", opener.document).html() );
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
	opener.payment_disabled_off();
	window.close();
}

function change_group_goods(idx, obj){
	var couponDataArray = $(obj).val().split('||');
	var display_coupon = $(obj).children(":selected").attr("class");
	var select_index = $("select[name='goods_coupon_code']").index( obj );
	
	if( display_coupon.length > 0 ){
		$(obj).children(":first").attr("mem_info",display_coupon);
		$(obj).prop('disabled', true);
		// 다른 상품은 해당 쿠폰을 선택 못한다
		$("."+display_coupon).css("display","none");
		//$("."+display_coupon).prop('disabled', true);
		$("."+display_coupon).prop( 'selected', function( coupon_idx , coupon_prop ) {
			if( !coupon_prop ){
				$(this).prop('disabled', true);
			}
		});

		$(obj).children(":selected").css("display","block");
		$(obj).next().css("display","block");
	}

	CardCoupon_Checked( obj );

	if(couponDataArray[4] == 'Y' && ($("input[name='dev_payment']:checked", opener.document).val() != "O" && $("input[name='dev_payment']:checked", opener.document).val() != "B")){
		alert(bankStr);
		 $(obj).val("");
		return false;
	}
	
	if( couponDataArray[5].length > 0 ){
		$(".CLS_description").eq(select_index).html( ' * ' + couponDataArray[5] );
	}
	if(couponDataArray[3] == 'dc'){
		$(obj).parent().next().html(comma(couponDataArray[2])+"원");
		$(obj).parent().next().next().html('─');
	}else{
		$(obj).parent().next().html('─');
		$(obj).parent().next().next().html('─');
	}
}

function CardCoupon_Checked( obj ){
	var couponDataArray = $(obj).val().split('||');
	if( couponDataArray[6] == '8' ){
		$("select[name='goods_coupon_code']").each(function(){
			$(this).find('option').each(function(){
				var temDataArr = $(this).val().split('||');
				if( ( temDataArr[0] != couponDataArray[0] ) && ( temDataArr[6] == couponDataArray[6]) ){
					//console.log( $(this).val() );
					$(this).css('display', 'none');
					$(this).prop('disabled', true);
				}
			});
		});
	}
}

function CardCoupon_UnChecked( obj ){
	var couponDataArray = $(obj).val().split('||');
	if( couponDataArray[6] == '8' ){
		$("select[name='goods_coupon_code']").each(function(){
			$(this).find('option').each(function(){
				var temDataArr = $(this).val().split('||');
				if( temDataArr[6] == couponDataArray[6] ){
					//console.log( $(this).val() );
					$(this).css('display', 'block');
					$(this).prop('disabled', false );
				}
			});
		});
	}
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
<div class="popup_def_wrap" style="width:100%">
	<div class="title_wrap">
		<p class="title">쿠폰조회 및 적용</p>
		<a href="javascript:window.close();" class="btn_close"></a>
	</div>
	
	<table cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td style="padding:10px;">
				<table cellpadding="0" cellspacing="0" width="100%" >
					<tr>
						<td>
							<table border="0" cellpadding="0" cellspacing="0" width="100%" class="th_top">
								<col/><col width="80"/><col width="100"/><col width="60"/><col width="60"/>
								<tr>
									<th>상품명</th>
									<th>상품금액</th>	
									<th>쿠폰선택</th>
									<th>할인</th>
									<!-- <th>적립</th> -->
								</tr>
<?php
	$goods_sql = "WITH basket_product AS ( ";
	$goods_sql.= " SELECT productcode, ";
	$goods_sql.= "  SUM (  ( CASE WHEN pricearr = '' THEN 0 ELSE pricearr :: Integer END ) ";
	$goods_sql.= "         * ( CASE WHEN quantityarr = '' THEN 0 ELSE quantityarr :: Integer END )";
	$goods_sql.= "  ) AS option_price, ";
	$goods_sql.= " SUM( quantity ) as basket_quantity ";
	$goods_sql.= " FROM tblbasket ";
	$goods_sql.= " WHERE tempkey = '".$_ShopInfo->getTempkey()."' ";
	$goods_sql.= " GROUP BY productcode ";
	$goods_sql.= ")";
	$goods_sql.= "SELECT bk.productcode, bk.option_price, bk.basket_quantity, pr.vender, ";
	$goods_sql.= "pr.productname, pr.sellprice, ( pr.sellprice * bk.basket_quantity ) AS realprice ";
	$goods_sql.= "FROM basket_product bk  ";
	$goods_sql.= "LEFT JOIN tblproduct pr ON pr.productcode = bk.productcode ";
	/*
	if($chk_mobile){
		$goods_sql.= "AND a.ord_state=true ";
	}
	*/
	//exdebug($goods_sql);
	$goods_result=pmysql_query($goods_sql,get_db_conn());
	$goods_sumprice=array();
	$goods_basketcnt=array();
	$goods_prcode=array();
	$goods_prname=array();
	$goods_productall=array();
	while($goods_row = pmysql_fetch_object($goods_result)) {	
		$goods_prcode=$goods_row->productcode;
		$goods_prname=str_replace('"','', strip_tags($goods_row->productname));

		if ( $goods_row->option_price <= 0 ) {										
			$goods_price = $goods_row->realprice;
		} else {												
			$goods_option_price = $goods_row->option_price;
			$goods_price = $goods_row->realprice + $goods_option_price;
		}

		$goods_dc_data = $product_class->getProductDcRate($goods_row->productcode);
		$goods_salemoney = getProductDcPrice($goods_price,$goods_dc_data[price]);
		#$goods_price = $goods_price - $goods_salemoney;
								
		$goods_cate_sql = "SELECT c_category FROM tblproductlink WHERE c_productcode = '".$goods_prcode."'";
		$goods_cate_result = pmysql_query($goods_cate_sql,get_db_conn());
		$categorycode = array();
		while($goods_cate_row=pmysql_fetch_object($goods_cate_result)) {
			list($cate_a, $cate_b, $cate_c, $cate_d) = sscanf($goods_cate_row->c_category,'%3s%3s%3s%3s');
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
				a.use_point, a.vender, b.date_start, b.date_end, a.coupon_use_type,
				a.description, a.coupon_type 
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
			$goods_description = $goods_coupon_row->description;
			$goods_coupon_type = $goods_coupon_row->coupon_type;
			
			
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
				$couponDcPrice = floor( $couponDcPrice / pow(10, $goods_amount_floor) ) * pow(10, $goods_amount_floor);
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
				$couponOptionArray[$goods_coupon_code] = "<option class='{$goods_coupon_code}' value=\"{$goods_coupon_code}||{$goods_prcode}||$couponDcPrice||$saleType||$goods_bank_only||$goods_description||$goods_coupon_type\">{$goods_coupon_name}</option>\n";
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
		/*쿠폰 조회 종료*/
?>
										<tr height="26" align="center">
											<td id='idx_prname' style="color:#333333;text-align:left">
												<?=$goods_prname?>
												<div class='CLS_description' style='color: red; font-weight : bold;'>
												</div>
											</td>
											<td id='idx_prprice' style="color:#333333"><?=number_format($goods_price)."원";?></td>
											<td>
												<select name='goods_coupon_code' class = 'CLS_goods_coupon_code' onchange="change_group_goods(this.value, this)" style="font-size:11px;width:80px;">
												<option value="">쿠폰선택</option>
												<?=$couponOption?>
												</select>
												<a class="coupon_cancel" href='javascript:;' style="display:none">취소</a>
											</td>
											<td class='CLS_idx_sale_money1' style="color:red">─</td>
											<!-- <td class='CLS_idx_sale_money2' style="color:red">─</td> -->
										</tr>

<?php
		}
		pmysql_free_result($goods_result);
?>
							</table>
						</td>
					</tr>

					<tr>
						<td style="border-bottom:1px solid #e1e1e1">
							<div id="div_price">
								<table WIDTH="100%" BORDER="0" CELLPADDING="0" CELLSPACING="0">
									<tr bgcolor="#F8F8F8" height="30" align="right">
										<td><font color="#333333"><b>총 구매 가격 <span id="total_price"><?=number_format($sumprice_t);?></span>원</b></font></td>
									</tr>
								</table>
							</div>
						</td>
					</tr>

				</table>
				<div class="ta_c mt_30">
<?php
	if($used!="N"){
?>
					<a class="btn_D on" href="javascript:CheckForm();">적용</a>
					<a class="btn_D" href="javascript:coupon_cancel();">취소</a>
<?php
	} else {
?>
					<a class="btn_D" href="javascript:window.close();">확인</a>
<?php
	}
?>
				</div>
			</td>
		</tr>
	</table>
</div>
</form>
</body>
</html>
