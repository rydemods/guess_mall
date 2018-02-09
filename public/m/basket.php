<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
include_once($Dir."lib/basket.class.php");
include_once($Dir."lib/delivery.class.php");

$Basket = new Basket(); //장바구니 초기화를 위해 불러온다 
$Basket->revert_id_item(); // 같은아이디일경우 템프키 변경

//배송료조회 david 2017-03-14
$sqlinfo = "SELECT deli_type,deli_basefee,deli_basefeetype,deli_miniprice,deli_setperiod,deli_limit, deli_select, ";
$sqlinfo.= "order_msg,deli_area,deli_area_limit FROM tblshopinfo ";
$result=pmysql_query($sqlinfo,get_db_conn());
if ($data=pmysql_fetch_object($result)) {
	$deli_basefee_origin = $data->deli_basefee;
	$deli_miniprice = $data->deli_miniprice;
}

$vdate = date("YmdHis");


include_once('outline/header_m.php');



?>


<style>
	#ajaxLoaderImg{
		position:absolute;
		top:50%;
		left:50%;
	}
</style>

<script type="text/javascript" src="../js/json_adapter/json_adapter.js"></script>
<script type="text/javascript" src="../js/json_adapter/Basket2.js"></script>
<script type="text/javascript">

var db = new JsonAdapter();
var util = new UtilAdapter();

var req = JSON.parse('<?=json_encode($_REQUEST)?>');
var ses = JSON.parse('<?=json_encode($_SESSION)?>');
var pArr 	= new Array(); //상품배열
var poArr 	= new Array(); //상품옵션배열
var grpidxArr = new Array();
var sum_sellprice =0;
var sum_basong =0;
var o2oprice =0;
var sum_totalprice =0;
var deli_basefee_origin = '<?=$deli_basefee_origin?>';
var deli_miniprice = '<?=$deli_miniprice?>';

req.tempkey = '<?=$_ShopInfo->getTempkey()?>';
req.memid = '<?=$_ShopInfo->getMemid()?>';
req.vdate = '<?=$vdate?>';
req.staff_yn = '<?=$_ShopInfo->staff_yn?>';
req.cooper_yn = '<?=$_ShopInfo->cooper_yn?>';

var basket = new Basket(req);

//$(document).ready( function() {
function show_basket() {

	init();
	$(window).ajaxStart(function(){
		$('#ajaxLoaderImg').show();
	});
	$(window).ajaxStop(function(){
		$('#ajaxLoaderImg').hide();
	});
	
}



//장바구니 리스트세팅 후 총 구입금액표기
function init(){

	//택배
	var data0 = basket.getBasket(0, 'M');	
	//console.log(data0);
	$('#sum_div_sellprice0').html('￦'+util.comma(data0.sum_div_sellprice));	//상품합계
	$('#sum_div_basong0').html('￦'+util.comma(data0.sum_div_basong));		//배송료
	$('#sum_div_basong0_staff').html('￦'+util.comma(data0.sum_div_basong_staff));		//배송료
	$('#sum_div_basong0_cooper').html('￦'+util.comma(data0.sum_div_basong_cooper));		//배송료
	$('#sum_div_totalprice0').html('￦'+util.comma(data0.sum_div_totalprice));//합계
	$('#sum_div_totalprice0_staff').html('￦'+util.comma(data0.sum_div_totalprice_staff));//합계(임직원)
	$('#sum_div_totalprice0_cooper').html('￦'+util.comma(data0.sum_div_totalprice_cooper));//합계(협력업체)
	//매장픽업
	var data1 = basket.getBasket(1, 'M');	
	$('#sum_div_sellprice1').html('￦'+util.comma(data1.sum_div_sellprice));
	$('#sum_div_basong1').html(0);
	$('#sum_div_basong1_staff').html('￦'+util.comma(data1.sum_div_basong_staff));		//배송료
	$('#sum_div_basong1_cooper').html('￦'+util.comma(data1.sum_div_basong_cooper));		//배송료
	$('#sum_div_totalprice1').html('￦'+util.comma(data1.sum_div_totalprice));
	$('#sum_div_totalprice1_staff').html('￦'+util.comma(data1.sum_div_totalprice_staff));//합계(임직원)
	$('#sum_div_totalprice1_cooper').html('￦'+util.comma(data1.sum_div_totalprice_cooper));//합계(협력업체)
		
	
	//당일배송
	var data2 = basket.getBasket(2, 'M');
	$('#sum_div_sellprice2').html('￦'+util.comma(data2.sum_div_sellprice));
	$('#sum_div_basong2').html('￦'+util.comma(data2.sum_div_basong));
	$('#sum_div_basong2_staff').html('￦'+util.comma(data2.sum_div_basong_staff));		//배송료
	$('#sum_div_basong2_cooper').html('￦'+util.comma(data2.sum_div_basong_cooper));		//배송료
	$('#sum_div_totalprice2').html('￦'+util.comma(data2.sum_div_totalprice));
	$('#sum_div_totalprice2_staff').html('￦'+util.comma(data2.sum_div_totalprice_staff));//합계(임직원)
	$('#sum_div_totalprice2_cooper').html('￦'+util.comma(data2.sum_div_totalprice_cooper));//합계(협력업체)


	//총 구입금액
	if(data0.sum_div_sellprice){
		sellprice0 = data0.sum_div_sellprice;
		sellprice0_staff = data0.sum_div_sellprice_staff;
		sellprice0_cooper = data0.sum_div_sellprice_cooper;
		basong0 = data0.sum_div_basong;
		basong0_staff = data0.sum_div_basong_staff;
		basong0_cooper = data0.sum_div_basong_cooper;
	}else{
		sellprice0 = 0;
		sellprice0_staff = 0;
		sellprice0_cooper = 0;
		basong0 = 0;
		basong0_staff = 0;
		basong0_cooper = 0;
	}
	
	if(data1.sum_div_sellprice){
		sellprice1 = data1.sum_div_sellprice;
		sellprice1_staff = data1.sum_div_sellprice_staff;
		sellprice1_cooper = data1.sum_div_sellprice_cooper;
		basong1 = 0;
		basong1_staff = 0;
		basong1_cooper = 0;
	}else{
		sellprice1 = 0;
		sellprice1_staff = 0;
		sellprice1_cooper = 0;
		basong1 = 0;
		basong1_staff = 0;
		basong1_cooper = 0;
	}
	
	if(data2.sum_div_sellprice){
		sellprice2 = data2.sum_div_sellprice;
		sellprice2_staff = data2.sum_div_sellprice_staff;
		sellprice2_cooper = data2.sum_div_sellprice_cooper;
		
		basong2 = data2.sum_div_basong;
		basong2_staff = data2.sum_div_basong_staff;
		basong2_cooper = data2.sum_div_basong_cooper;
	}else{
		sellprice2 = 0;
		sellprice2_staff = 0;
		sellprice2_cooper = 0;
		basong2 = 0;
		basong2_staff = 0;
		basong2_cooper = 0;
	}

	var total_sellprice_type2 = Number(data1.sum_div_sellprice) + Number(data2.sum_div_sellprice);
	var total_basong = Number(basong0) + Number(basong1) + Number(basong2);
	var total_basong_staff = Number(basong0_staff) + Number(basong1_staff) + Number(basong2_staff);
	var total_basong_cooper = Number(basong0_cooper) + Number(basong1_cooper) + Number(basong2_cooper);
	
	$('#sum_sellprice_type1').html('￦'+util.comma(sellprice0));
	$('#sum_sellprice_type2').html('￦'+util.comma(sellprice1 + sellprice2));
	$('#sum_basong').html('￦'+util.comma(total_basong));
	
	$('#sum_totalprice').html('￦'+util.comma(sellprice0 + sellprice1 + sellprice2 + total_basong));
	$('#sum_totalprice_staff').html('￦'+util.comma(sellprice0_staff + sellprice1_staff + sellprice2_staff + total_basong_staff));
	$('#sum_totalprice_cooper').html('￦'+util.comma(sellprice0_cooper + sellprice1_cooper + sellprice2_cooper + total_basong_cooper));
	
}


function allcheck(type){
	var chk = $('#itemPutAll'+type).is(':checked');
	$(':checkbox[name="basketgrpidx'+type+'"]').prop('checked', chk);
	
} 

function openOption(basketgrpidx){
	$('#optbox_'+basketgrpidx).toggle();
}



</script>

<div id="page">
<!-- 내용 -->
<main id="content" class="subpage">
	
	

	<section class="page_local">
		<h2 class="page_title">
			<a href="javascript:history.back();" class="prev">이전페이지</a>
			<span>주문/결제</span>
		</h2>
		<div class="page_step">
			<ul class="clear">
				<li class="on"><span class="icon_order_step01"></span>장바구니</li>
				<li><span class="icon_order_step02"></span>주문하기</li>
				<li><span class="icon_order_step03"></span>주문완료</li>
			</ul>
		</div>
	</section><!-- //.page_local -->

	<section class="cartpage">

		<div class="list_cart">

			<!-- 반복 -->
			<div class="list_brand" id="delivery_type0_section" style="display:none;">
				<h3 class="cart_tit">택배 배송 상품</h3>
				<ul class="cart_goods" id="delivery_type0_zone">
				</ul>
				<div class="cart_calc">
					<ul>
						<li>
							<label>상품합계</label>
							<span id="sum_div_sellprice0"></span>
						</li>
						<li>
							<label>배송비</label>
							<span id="sum_div_basong0"></span>
						</li>
						<li class="total">
							<label>합계금액</label>
							<span id="sum_div_totalprice0"></span>
							<?if($_ShopInfo->staff_yn=="Y"){?>
							<span class="staff point-color">(임직원가) <strong id="sum_div_totalprice0_staff"></strong></span>
							<?}?>
							<?if($_ShopInfo->cooper_yn=="Y"){?>
							<span class="staff point-color">(제휴사) <strong id="sum_div_totalprice0_cooper"></strong></span>
							<?}?>
							
						</li>
					</ul>
				</div>
			</div>
			<div class="list_brand" id="delivery_type1_section" style="display:none;">
				<h3 class="cart_tit">O2O 매장픽업 상품</h3>
				<ul class="cart_goods" id="delivery_type1_zone">
				</ul>
				<div class="cart_calc">
					<ul>
						<li>
							<label>상품합계</label>
							<span id="sum_div_sellprice1"></span>
						</li>
						<li>
							<label>배송비</label>
							<span id="sum_div_basong1"></span>
						</li>
						<li class="total">
							<label>합계금액</label>
							<span id="sum_div_totalprice1"></span>
							<?if($_ShopInfo->staff_yn=="Y"){?>
							<span class="staff point-color">(임직원가) <strong id="sum_div_totalprice1_staff"></strong></span>
							<?}?>
							<?if($_ShopInfo->cooper_yn=="Y"){?>
							<span class="staff point-color">(제휴사) <strong id="sum_div_totalprice1_cooper"></strong></span>
							<?}?>
						</li>
					</ul>
				</div>
			</div>
			<div class="list_brand" id="delivery_type2_section" style="display:none;">
				<h3 class="cart_tit">O2O 당일배송 상품</h3>
				<ul class="cart_goods" id="delivery_type2_zone">
				</ul>
				<div class="cart_calc">
					<ul>
						<li>
							<label>상품합계</label>
							<span id="sum_div_sellprice2"></span>
						</li>
						<li>
							<label>배송비</label>
							<span id="sum_div_basong2"></span>
						</li>
						<li class="total">
							<label>합계금액</label>
							<span id="sum_div_totalprice2"></span>
							<?if($_ShopInfo->staff_yn=="Y"){?>
							<span class="staff point-color">(임직원가) <strong id="sum_div_totalprice2_staff"></strong></span>
							<?}?>
							<?if($_ShopInfo->cooper_yn=="Y"){?>
							<span class="staff point-color">(제휴사) <strong id="sum_div_totalprice2_cooper"></strong></span>
							<?}?>
						</li>
					</ul>
				</div>
			</div>
			<!-- //반복 -->
			

			
			<div class="btn_area mt-10 mr-10 ml-10">
				<ul class="ea3">
					<li><a href="javascript:;" id="allCheck" class="btn-line" onclick="basket.allSelect('select');">전체선택</a></li>
					<li><a href="javascript:;" id="allCheckF" class="btn-line" onclick="basket.allSelect('');">선택해제</a></li>
					<li><a href="javascript:;" class="btn-line" onclick="basket.delBasket('choise');return false;">선택삭제</a></li>
				</ul>
			</div>

		</div><!-- //.list_cart -->

		<div class="calc_area"><!-- [D] 체크된 상품의 상품가, 배송비 정보 노출 -->
			<h3 class="cart_tit">총 구입금액</h3>
			<div class="cart_calc">
				<ul>
					<li>
						<label>택배배송 상품가</label>
						<span id="sum_sellprice_type1">0</span>
						
					</li>
					<li>
						<label>O2O 상품가</label>
						<span id="sum_sellprice_type2">0</span>
						
					</li>
					<li>
						<label>배송비</label>
						<span id="sum_basong">0</span>
						
					</li>
				</ul>
			</div>
			
			<div class="cart_calc mt-5">
				<ul>
					<li class="all_total">
						<label>총 주문금액</label>
						<span class="point-color" id="sum_totalprice">0</span>
						<?if($_ShopInfo->staff_yn=="Y"){?>
						<span class="staff point-color">(임직원가) <strong id="sum_totalprice_staff"></strong></span>
						<?}?>
						<?if($_ShopInfo->cooper_yn=="Y"){?>
						<span class="staff point-color">(제휴사) <strong id="sum_totalprice_cooper"></strong></span>
						<?}?>
						<!--<span class="staff point-color">(임직원가) <strong>￦ 20,000,000</strong></span> //[D] 임직원가 수정(2017-04-24) -->
					</li>
				</ul>
			</div>
		</div><!-- //.calc_area -->
		
		<!-- [D] 임직원가 수정(2017-04-24) -->
		<div class="btn_area mt-20 mr-10 ml-10"><!-- [D] 기본 노출 -->
			<?if(($_ShopInfo->staff_yn=="N" and $_ShopInfo->cooper_yn=="N") or $_ShopInfo->staff_yn=="" ){?>
			<ul>
				<li><a href="javascript:;" class="btn-point h-input" onclick="basket.goOrder('','');return false;">선택상품구매</a></li>
			</ul>
			<?}else if($_ShopInfo->staff_yn=="Y"){?>
			<ul class="ea2">
				<li><a href="javascript:;" class="btn-basic h-input" onclick="basket.goOrder('','');return false;">선택상품구매</a></li>
				<li><a href="javascript:;" class="btn-point h-input" onclick="basket.goOrder('','staff');return false;">선택상품 임직원 구매</a></li>
			</ul>		
			<?}else if($_ShopInfo->cooper_yn=="Y"){?>
			<ul class="ea2">
				<li><a href="javascript:;" class="btn-basic h-input" onclick="basket.goOrder('','');return false;">선택상품구매</a></li>
				<li><a href="javascript:;" class="btn-point h-input" onclick="basket.goOrder('','cooper');return false;">선택상품 제휴사 구매</a></li>
			</ul>
			<?}?>
		</div>

		<!--<div class="btn_area mt-20 mr-10 ml-10"> [D] 임직원 구매인 경우 노출
			<ul class="ea2">
				<li><a href="javascript:;" class="btn-basic h-input">선택상품구매</a></li>
				<li><a href="javascript:;" class="btn-point h-input">선택상품 임직원 구매</a></li>
			</ul>
		</div> -->
		<!-- //[D] 임직원가 수정(2017-04-24) -->

	</section>
	
	<form name='orderfrm' id='orderfrm' method='GET' action='<?=$Dir.MDir?>order.php' >
	<input type='hidden' name='basketidxs' id='basketidxs' value='' >
	<input type='hidden' name='staff_order' id='staff_order' value='' >
	<input type='hidden' name='cooper_order' id='cooper_order' value='' >
	</form>

</main>
<!-- //내용 -->
<!-- WIDERPLANET PURCHASE SCRIPT START 2017.9.19 -->
<div id="wp_tg_cts" style="display:none;"></div>
<?php
$mem_id	=	 $_ShopInfo->getMemid();
$tempkey	=	$_ShopInfo->getTempkey();
if($mem_id==''){
	$dyqry = " and (a.tempkey='".$tempkey."' and a.id='') ";
}else{
	$dyqry = " and (a.tempkey='".$tempkey."' or a.id='".$mem_id."') ";
} 	

$sql = "select 
	a.basketgrpidx, a.productcode, sum(a.quantity) as quantity , a.opt2_idx, a.delivery_type, a.delivery_price,a.date ,
	b.tinyimage, b.productname, sum(b.sellprice) as sellprice, b.consumerprice,b.buyprice, b.brand, b.prodcode,b.colorcode, COALESCE(c.likecnt,0) likecnt, COALESCE(c1.likeme,0) likeme
	, d.brandname as brandcdnm, e.name as store_name, a.reservation_date, b.reserve, e.store_code, b.staff_dc_rate ,b.cooper_dc_rate,g.group_productcode
from tblbasket a inner join tblproduct b on a.productcode=b.productcode
left outer join (select count(section) likecnt, section, hott_code from tblhott_like where section='product' group by section, hott_code) c on b.prodcode=c.hott_code
left outer join (select count(section) likeme, section, hott_code from tblhott_like where section='product' and like_id='".$mem_id."' group by section, hott_code) c1 on b.prodcode=c1.hott_code
left outer join tblproductbrand d on b.brand=d.bridx
left outer join tblstore e on a.store_code = e.store_code
left outer join tblmember f on a.id = f.id
left outer join tblcompanygroup g on f.company_code = g.group_code
where 1=1
".$dyqry."
group by 
a.basketgrpidx, a.productcode, a.quantity, a.opt2_idx, a.delivery_type,a.delivery_price,a.date ,
b.tinyimage,b.productname,b.sellprice,b.consumerprice,b.buyprice,b.brand,b.prodcode,b.colorcode, c.likecnt, c1.likeme
,d.brandname, e.name , a.reservation_date, b.reserve ,e.store_code, b.staff_dc_rate ,b.cooper_dc_rate,g.group_productcode
order by a.date desc";
$result=pmysql_query($sql,get_db_conn());
while($row=pmysql_fetch_array($result)) {
	$productArr[] = $row;
}
pmysql_free_result( $result );

$wptg_arr   = array();
$wptg_items = '';
foreach( $productArr as $prKey=>$prVal ){
	$category =  substr($prVal['productcode'],0,12);
	$wptg_arr[] = '{i:"'.$prVal['productcode'].'", t:"'.$prVal['productname'].'" }';
	$nsm_arr[] = '{pd:"'.$prVal['productcode'].'",pn:"'.$prVal['productname'].'",am:"'.$prVal['sellprice'].'",qy:"'.$prVal['quantity'].'",ct:"'.$category.'" }';
}
$wptg_items = implode( ',', $wptg_arr );
$nsm_items = implode( ',', $nsm_arr );
	if($_SERVER["REMOTE_ADDR"] == "218.234.32.36"){
		//exdebug($wptg_items);
	}
?>
<script type="text/javascript">
var wptg_tagscript_vars = wptg_tagscript_vars || [];
wptg_tagscript_vars.push(
(function() {
	return {
		wp_hcuid:"",  	
		ti:"37370",
		ty:"Cart",
		device:"mobile"
		,items:[
			 <?=$wptg_items?>
		]
	};
}));
</script>
<script type="text/javascript" async src="//cdn-aitg.widerplanet.com/js/wp_astg_4.0.js"></script>
<!-- // WIDERPLANET PURCHASE SCRIPT END 2017.9.19 -->

<!-- *) 장바구니목록페이지 (장바구니보기) -->
<!-- NSM Site Analyst Mobile eCommerce (Cart_Inout) v2.0 Start -->
<script type='text/javascript'>
 var SA_Cart=(function(){
	var c=<?=$nsm_items?>;
	var u=(!SA_Cart)?[]:SA_Cart; u[c.pd]=c;return u;
})();
</script>
<!-- *) 공통 분석스크립트  -->
<!-- 1-script.txt -->


<?
include_once("outline/footer_m.php");
?>
<script>
show_basket();
</script>
