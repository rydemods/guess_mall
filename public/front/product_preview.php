<?
header("Content-Type:text/html;charset=EUC-KR");
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/cache_product.php");
include_once("lib.inc.php");
include_once("shopdata.inc.php");


$prcodeDetail=$_REQUEST["prcodeDetail"];

$imagepath=$Dir.DataDir."shopimages/product/";

$sql = "SELECT *  FROM tblproduct WHERE productcode = '{$prcodeDetail}'  ";
$res = pmysql_query($sql);
$row = pmysql_fetch_object($res);
$option = explode(",",$row->option1);
$count = count($option);
$optioncnt = explode(",",$row->option_quantity);
$optionprice = explode(",",$row->option_price);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<link rel="stylesheet" href="../css/nexolve.css" />
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
<script type="text/javascript" src="../css/select_type01.js" ></script>

<!-- AceCounter eCommerce (Product_Detail) v6.5 Start -->
<!-- Function and Variables Definition Block Start -->
<script language='javascript'>
var _JV="AMZ2012052201";//script Version
var _UD='undefined';var _UN='unknown';
function _IDV(a){return (typeof a!=_UD)?1:0}
var HL_CRL='http://'+'ngc11.nsm-corp.com:80/';
var HL_GCD='CP6B37879413190';
var _A_cart = "";
if( document.URL.substring(0,8) == 'https://' ){ HL_CRL = 'https://ngc11.nsm-corp.com/logecgather/' ;};
if(!_IDV(_A_i)) var _A_i = new Image() ;if(!_IDV(_A_i0)) var _A_i0 = new Image() ;if(!_IDV(_A_i1)) var _A_i1 = new Image() ;if(!_IDV(_A_i2)) var _A_i2 = new Image() ;if(!_IDV(_A_i3)) var _A_i3 = new Image() ;if(!_IDV(_A_i4)) var _A_i4 = new Image() ;
function _RP(s,m){if(typeof s=='string'){if(m==1){return s.replace(/[#&^@,]/g,'');}else{return s.replace(/[#&^@]/g,'');} }else{return s;} };
function _RPS(a,b,c){var d=a.indexOf(b),e=b.length>0?c.length:1; while(a&&d>=0){a=a.substring(0,d)+c+a.substring(d+b.length);d=a.indexOf(b,d+e);}return a};
function AEC_F_D(pd,md,cnum){ var i = 0 , amt = 0 , num = 0 ; var cat = '' ,nm = '' ; num = cnum ;md=md.toLowerCase(); if( md == 'b' || md == 'i' || md == 'o' ){ for( i = 0 ; i < _A_pl.length ; i ++ ){ if( _A_pl[i] == pd ){ nm = _RP(_A_pn[i]); amt = ( parseInt(_RP(_A_amt[i],1)) / parseInt(_RP(_A_nl[i],1)) ) * num ; cat =  _RP(_A_ct[i]);  _A_cart = HL_CRL+'?cuid='+HL_GCD; _A_cart += '&md='+md+'&ll='+_RPS(escape(cat+'@'+nm+'@'+amt+'@'+num+'^&'),'+','%2B'); break;};};if(_A_cart.length > 0 ) _A_i.src = _A_cart;setTimeout("",2000);};};
if(!_IDV(_A_pl)) var _A_pl = Array(1) ;
if(!_IDV(_A_nl)) var _A_nl = Array(1) ;
if(!_IDV(_A_ct)) var _A_ct = Array(1) ;
if(!_IDV(_A_pn)) var _A_pn = Array(1) ;
if(!_IDV(_A_amt)) var _A_amt = Array(1) ;
if(!_IDV(_pd)) var _pd = '' ;
if(!_IDV(_ct)) var _ct = '' ;
if(!_IDV(_amt)) var _amt = '' ;
</script>
<!-- Function and Variables Definition Block End-->

<!-- AceCounter eCommerce (Product_Detail) v6.5 Start -->
<!-- Data Allocation (Product_Detail) -->
<script language='javascript'>

_pd =_RP("<?=$p_date->productname?>");
_ct =_RP("<?=$_cdata->code_name?>");
_amt = _RP("<?=$_pdata->sellprice?>",1); // _RP(1)-> 가격

_A_amt=Array('<?=$_pdata->sellprice?>');
_A_nl=Array('수량');
_A_pl=Array('<?=$_pdata->productcode?>');
_A_pn=Array('<?=$p_date->productname?>');
_A_ct=Array('<?=$_cdata->code_name?>');
</script>
<!-- AceCounter eCommerce (Product_detail) v6.4 Start -->

<div class="pop_title">
<form name="formD" id = 'ID_goodsviewfrm' method=post action="<?=$Dir.FrontDir?>basket.php">
<input type="hidden" name="ordertype" />
<input type="hidden" name="productcode" value="<?=$prcodeDetail?>" />

	<h3>상품 미리보기<a href="javascript:closeDetail();" class="close">닫기</a></h3>
	<div class="pop_mini_view">
		<div class="goods_title_area">
			<p class="icon"><h2><?=$row->productname?></h2></p>
		
		</div>
		
		
		<!-- 상단 상품 정보 -->
		<div class="goods_info_detail">

			<div class="left_pic">
				<p><img src="<?=$imagepath.$row->maximage?>" alt="" style="width:440px;height:440px;" /></p>
				<!--<ul class="goods_view_thumb">
					<li><img src="../img/test/test_img66.jpg" alt="" /></li>
					<li><img src="../img/test/test_img66.jpg" alt="" /></li>
					<li><img src="../img/test/test_img66.jpg" alt="" /></li>
					<li><img src="../img/test/test_img66.jpg" alt="" /></li>
					<li><img src="../img/test/test_img66.jpg" alt="" /></li>
					<li><img src="../img/test/test_img66.jpg" alt="" /></li>
				</ul>
				<div class="goods_social_icon">
					소문내기 
					<a href="#"><img src="../img/icon/icon_social_kakao.gif" alt="카카오톡" /></a>
					<a href="#"><img src="../img/icon/icon_social_facebook.gif" alt="페이스북" /></a>
					<a href="#"><img src="../img/icon/icon_social_url.gif" alt="URL복사" /></a>
				</div>-->
			</div>
			<div class="right_info">
				<div class="goods_info_total">
					<table class="goods_info" cellpadding=0 cellspacing=0 border=0 width=100%>
						<colgroup>
							<col width="110" /><col width="*px" />
						</colgroup>
						<tr>
							<th>판매가격</th>
							<td><span class="price_off"><?=number_format($row->consumerprice)?></span> 원 <!--<a class="calculator">미리계산</a>--></td>
						</tr>
						<tr>
							<th>쿠폰 적용가</th>
							<td><span class="price_coupon"><?=number_format($row->sellprice)?></span><span class="dahong">원</span> <!--<a href="#" class="goods_dc_coupong"><span>30%</span></a>--></td>
						</tr>
						<tr>
							<th>적립금</th>
							<td><img src="../img/icon/icon_p.gif" alt="" /> <?=number_format($row->reserve)?></td>
						</tr>
					</table>
				</div>
				<table class="goods_info mt_10" cellpadding=0 cellspacing=0 border=0 width=100%>
					<colgroup>
						<col width="110" /><col width="*px" />
					</colgroup>
					<tr>
						<th>원산지</th>
						<td><?=$row->madein?></td>
					</tr>
					<tr>
						<th>제조사</th>
						<td><?=$row->production ?></td>
					</tr>
					<tr>
						<th>옵션선택</th>
						<td>
							<div class="select_type" style="width:150px;z-index:10;">
								<span class="ctrl"><span class="arrow"></span></span>
								<button type="button" class="myValue">사이즈</button>
								<ul class="aList selectOption1">
									<?for($i=1;$i<$count;$i++) {?>
										<li>
										<?if(strlen($option[$i]) > 0) {?>
											<?if(strlen($row->option1) == 0 && $optioncnt[$i-1] == "0"){?>	
												<a href="javascript:;" opt = '' pri = ''><strike><?=$option[$i]." [품절]"?></strike></a>
											<?}else{?>
												<a href="javascript:;" opt = '<?=$i?>' pri = '<?=$optionprice[$i]?>'><?=$option[$i]?></a>
											<?}?>
										<?}?>
										</li>
									<?}?>
								</ul>
								<input type = 'hidden' name = 'option1' id = 'ID_option1'>
								<input type = 'hidden' name = 'optionprice' id = 'optionprice'>
							</div>
						</td>
					</tr>
					<tr>
						<th>수량</th>
						<td>
							<dl class="product_info_spec">
								<dd>
									<input type=text name="quantity" value="<?=($miniq>1?$miniq:"1")?>" class="amount" size = '2' onkeypress="javascript:totalPrice();">	<!-- $miniq = 최소 구매 수량-->
									<a href="javascript:change_quantity('up')">
										<img src="<?=$Dir?>image/detail/amount_plus.gif" alt="수량">
									</a>
									<a href="javascript:change_quantity('dn')">
										<img src="<?=$Dir?>image/detail/amount_minus.gif" alt="수량">
									</a>
								</dd>
							</dl>
						</td>
					</tr>
					<tr>
						<td colspan=2 height=1 bgcolor="d3d3d3"></td>
					</tr>
					<tr>
						<th>합계</th>
						<td>
							<span class="price_coupon" id="price_total">0</span> <span class="dahong fz_16">원</span>
							<input type = 'hidden' id = 'ID_goodsprice'>
						</td>
					</tr>
				</table>
				<div class="goods_buy_btn_pop" align="center">
					<?if($row->quantity == 0){?>
						<FONT style="color:#F02800;"><b>품 절</b></FONT>
					<?}else{?>
						<a href="javascript:CheckForm()" class="btn_buy_now">바로구매</a> 
						<a href="<?=$Dir.FrontDir."productdetail.php?productcode=".$row->productcode?>" class="btn_goods_view">상세페이지</a>
					<?}?>					
				</div>
				<!--<div class="related_event">
					<h4>관련기획전 (3개) +</h4>
					<ul class="related_list">
						<li><a href="#">[패션슈즈] 바캉스 잇 슈즈 제안! 핏플랍 단독세일 </a></li>
						<li><a href="#">[패션슈즈] 바캉스 잇 슈즈 제안! 핏플랍 단독세일 </a></li>
						<li><a href="#">[패션슈즈] 바캉스 잇 슈즈 제안! 핏플랍 단독세일 </a></li>
					</ul>
				</div>-->
			</div>
		</div><!-- //상단 상품 정보 -->
	</div>
</form>
</div>


<script>

function fnComma(num) {
	var pattern = /(-?[0-9]+)([0-9]{3})/;
	while(pattern.test(num)) {
		num = num.replace(pattern,"$1,$2");
	}
	return num;
}

function totalPrice(){
	document.formD.ID_goodsprice.value=document.formD.quantity.value * document.formD.optionprice.value;
	var tmpForTotal = document.formD.ID_goodsprice.value;
	$("#price_total").html(fnComma(tmpForTotal));
}

/*
	상품 옵션 선택 Start
*/
var option1TempValue = $(".selectOption1").prev().html();
var clickOption1 = false;
$(".selectOption1 li").click(function(){
	if($(this).children().attr('opt')){
		$(this).parent().prev().html($(this).children().html());
		$("#ID_option1").val($(this).children().attr('opt'));
		$("#optionprice").val($(this).children().attr('pri'));
		$(this).parent().parent().removeClass('open');
		option1TempValue = $(this).children().html();
		clickOption1 = true;
		totalPrice();
	}else{
		alert("품절된 상품 입니다.");
		if(!clickOption1){
			$(this).parent().prev().removeClass('selected');
		}
		$(this).parent().prev().html(option1TempValue);
		$(this).parent().parent().removeClass('open');
	}
})

function change_quantity(gbn) {
	var tmpD=document.formD.quantity.value;
	if(gbn=="up") {
		tmpD++;
	} else if(gbn=="dn") {
		if(tmpD>1) tmpD--;
	}
	if(document.formD.quantity.value!=tmpD) {	
		document.formD.quantity.value=tmpD;	
		
	}
	totalPrice();
}

function CheckForm() {
	var procBuy = false;
	document.formD.ordertype.value="ordernow";
	if(document.formD.quantity.value.length==0 || document.formD.quantity.value==0) {
		alert("주문수량을 입력하세요.");
		document.formD.quantity.focus();
		return;
	}	
	if(typeof(document.formD.option1)!="undefined" && (!$("#ID_option1").val() && $("#ID_option1").length > 0)) {
		alert('해당 상품의 옵션을 선택하세요.');
		document.formD.option1.focus();
		return;
	}

	$.ajax({
		type: "POST", 
		url: "../front/confirm_basket_proc.php", 
		data: $('#ID_goodsviewfrm').serialize(), 
		async: false,
		beforeSend: function () {
			//전송전
		}
	}).done(function ( msg ) {
		if(msg){
			alert(msg);
			procBuy = false;
			return false;
		}else{
			procBuy = true;
		}
	});
	if(procBuy){					
		/*document.formD.action="/front/confirm_basket.php";
		document.formD.target="confirmbasketlist";
		window.open("about:blank","confirmbasketlist","width=500,height=250,scrollbars=no");*/

		AEC_F_D('<?=$row->productcode?>','i',document.formD.quantity.value);
		document.formD.submit();
	}
}

</script>