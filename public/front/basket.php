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

?>

<?php  include ($Dir.MainDir.$_data->menu_type.".php") ?>
<style>
	#ajaxLoaderImg{
		position:absolute;
		top:50%;
		left:50%;
	}
</style>
<script type="text/javascript" src="../js/json_adapter/json_adapter.js"></script>
<script type="text/javascript" src="../js/json_adapter/Basket.js"></script>
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

$(document).ready( function() {

	init();

	$(window).ajaxStart(function(){
		$('#ajaxLoaderImg').show();
	});
	$(window).ajaxStop(function(){
		$('#ajaxLoaderImg').hide();
	});
	
});



//장바구니 리스트세팅 후 총 구입금액표기
function init(){
		
	//택배
	var data0 = basket.getBasket(0);	
	$('#sum_div_sellprice0').html('\\'+util.comma(data0.sum_div_sellprice));	//상품합계
	$('#sum_div_basong0').html('\\'+util.comma(data0.sum_div_basong));		//배송료
	$('#sum_div_basong0_staff').html('\\'+util.comma(data0.sum_div_basong_staff));		//배송료
	$('#sum_div_basong0_cooper').html('\\'+util.comma(data0.sum_div_basong_cooper));		//배송료
	$('#sum_div_totalprice0').html('\\'+util.comma(data0.sum_div_totalprice));//합계
	$('#sum_div_totalprice0_staff').html('\\'+util.comma(data0.sum_div_totalprice_staff));//합계(임직원)
	$('#sum_div_totalprice0_cooper').html('\\'+util.comma(data0.sum_div_totalprice_cooper));//합계(협력업체)
	
	

	//매장픽업
	var data1 = basket.getBasket(1);	
	//console.log(data1);
	$('#sum_div_sellprice1').html('\\'+util.comma(data1.sum_div_sellprice));
	$('#sum_div_basong1').html(0);
	$('#sum_div_basong1_staff').html('\\'+util.comma(data1.sum_div_basong_staff));		//배송료
	$('#sum_div_basong1_cooper').html('\\'+util.comma(data1.sum_div_basong_cooper));		//배송료
	$('#sum_div_totalprice1').html('\\'+util.comma(data1.sum_div_totalprice));
	$('#sum_div_totalprice1_staff').html('\\'+util.comma(data1.sum_div_totalprice_staff));//합계(임직원)
	$('#sum_div_totalprice1_cooper').html('\\'+util.comma(data1.sum_div_totalprice_cooper));//합계(협력업체)
		
	
	//당일배송
	var data2 = basket.getBasket(2);
	$('#sum_div_sellprice2').html('\\'+util.comma(data2.sum_div_sellprice));
	$('#sum_div_basong2').html('\\'+util.comma(data2.sum_div_basong));
	$('#sum_div_basong2_staff').html('\\'+util.comma(data2.sum_div_basong_staff));		//배송료
	$('#sum_div_basong2_cooper').html('\\'+util.comma(data2.sum_div_basong_cooper));		//배송료
	$('#sum_div_totalprice2').html('\\'+util.comma(data2.sum_div_totalprice));
	$('#sum_div_totalprice2_staff').html('\\'+util.comma(data2.sum_div_totalprice_staff));//합계(임직원)
	$('#sum_div_totalprice2_cooper').html('\\'+util.comma(data2.sum_div_totalprice_cooper));//합계(협력업체)
	

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
	
	$('#sum_sellprice_type1').html('\\'+util.comma(sellprice0));
	$('#sum_sellprice_type2').html('\\'+util.comma(sellprice1 + sellprice2));
	$('#sum_basong').html('\\'+util.comma(total_basong));
	
	$('#sum_totalprice').html('\\'+util.comma(sellprice0 + sellprice1 + sellprice2 + total_basong));
	$('#sum_totalprice_staff').html('\\'+util.comma(sellprice0_staff + sellprice1_staff + sellprice2_staff + total_basong_staff));
	$('#sum_totalprice_cooper').html('\\'+util.comma(sellprice0_cooper + sellprice1_cooper + sellprice2_cooper + total_basong_cooper));
	
}


function allcheck(type){
	var chk = $('#itemPutAll'+type).is(':checked');
	$('[name="basketgrpidx'+type+'"]:not(:disabled)').prop('checked', chk);
	
	
} 


</script>


<div id="ajaxLoaderImg" style="display: none;"><img id="" src="/js/json_adapter/img/loading/ajax_loader_gray_48.gif"></div>
<div id="contents">
	<div class="cartOrder-page">

<article class="cart-order-wrap">
			<header class="progess-title">
				<h2>주문/결제</h2>
				<ul class="flow clear">
					<li class="active"><div><i></i><span>STEP 1</span>장바구니</div></li>
					<li><div><i></i><span>STEP 2</span>주문하기</div></li>
					<li><div><i></i><span>STEP 3</span>주문완료</div></li>
				</ul>
			</header>

		<!-- 택배배송상품 -->
		<section class="mt-70"  id="delivery_type0_section" style="display: none;">
				<header class="cart-section-title">
					<h3>택배 배송 상품</h3>
					<p class="att">*본사물류 또는 해당 브랜드 매장에서 택배로 고객님께 상품이 배송됩니다. (주문 완료 후, 3~5일 이내 수령)</p>
				</header>
				<table class="th-top">
					<caption>장바구니 담긴 품목</caption>
					<colgroup>
						<col style="width:54px">
						<col style="width:auto">
						<col style="width:170px">
						<col style="width:110px">
						<col style="width:140px">
						<col style="width:140px">
						<col style="width:116px">
						<col style="width:40px">
					</colgroup>
					<thead>
						<tr>
							<th scope="col"><div class="checkbox"><input type="checkbox" id="itemPutAll0" onclick="allcheck(0)"><label for="itemPutAll0"></label></div></th>
							<th scope="col">상품정보</th>
							<th scope="col">수량</th>
							<th scope="col">적립</th>
							<th scope="col">판매가</th>
							<th scope="col">배송정보</th>
							<th scope="col">선택</th>
							<th scope="col" class="fz-0">삭제</th>
						</tr>
					</thead>
					<tfoot id="foot_type0_zone">
						<tr>
							<td colspan="8" class="reset">
							<div class="cart-total-price clear">
								<dl>
									<dt>상품합계</dt>
									<dd id="sum_div_sellprice0"></dd>
								</dl>
								<span class="txt">+</span>
								<dl>
									<dt>배송비</dt>
									<dd id="sum_div_basong0"></dd>
								</dl>
								<dl class="sum">
									<dt>합계</dt>
									<dd id="sum_div_totalprice0"></dd>
								</dl>
								<?if($_ShopInfo->staff_yn=="Y"){?>
								<div class="staff_price point-color">
									<dl>
										<dt>(임직원가)</dt>
										<dd id="sum_div_totalprice0_staff"></dd>
									</dl>
								</div>
								<?}?>
								<?if($_ShopInfo->cooper_yn=="Y"){?>
								<div class="staff_price point-color">
									<dl>
										<dt>(제휴사가)</dt>
										<dd id="sum_div_totalprice0_cooper"></dd>
									</dl>
								</div>
								<?}?>
							</div>
						</td>
					</tr>
						
					</tfoot>
					<tbody data-ui="TabMenu" id="delivery_type0_zone">
						
						
						
						
					</tbody>
				</table>
			</section>

		<!-- O2O 매장픽업 -->
		<section class="mt-60" id="delivery_type1_section" style="display:none;">
				<header class="cart-section-title">
					<h3>O2O 매장픽업 상품</h3>
					<p class="att">*고객님께서 주문하신 후, 매장에 직접방문에서 찾아가시거나 매장에서 배송되는 상품입니다.</p>
				</header>
				<table class="th-top">
					<caption>장바구니 담긴 품목</caption>
					<colgroup>
						<col style="width:54px">
						<col style="width:auto">
						<col style="width:170px">
						<col style="width:90px">
						<col style="width:130px">
						<col style="width:130px">
						<col style="width:116px">
						<col style="width:20px">
					</colgroup>
					<thead>
						<tr>
							<th scope="col"><div class="checkbox"><input type="checkbox" id="itemPutAll1" onclick="allcheck(1)"><label for="itemPutAll1"></label></div></th>
							<th scope="col">상품정보</th>
							<th scope="col">수량</th>
							<th scope="col">적립</th>
							<th scope="col">판매가</th>
							<th scope="col">배송정보</th>
							<th scope="col">선택</th>
							<th scope="col" class="fz-0">삭제</th>
						</tr>
					</thead>
					<tfoot id="foot_type1_zone">
						<tr>
							<td colspan="8" class="reset">
							<div class="cart-total-price clear">
								<dl>
									<dt>상품합계</dt>
									<dd id="sum_div_sellprice1"></dd>
								</dl>
								<span class="txt">+</span>
								<dl>
									<dt>배송비</dt>
									<dd id="sum_div_basong1"></dd>
								</dl>
								<dl class="sum">
									<dt>합계</dt>
									<dd id="sum_div_totalprice1"></dd>
								</dl>
								
								<?if($_ShopInfo->staff_yn=="Y"){?>
								<div class="staff_price point-color">
									<dl>
										<dt>(임직원가)</dt>
										<dd id="sum_div_totalprice1_staff"></dd>
									</dl>
								</div>
								<?}?>
								<?if($_ShopInfo->cooper_yn=="Y"){?>
								<div class="staff_price point-color">
									<dl>
										<dt>(제휴사가)</dt>
										<dd id="sum_div_totalprice1_cooper"></dd>
									</dl>
								</div>
								<?}?>
							</div>
						</td>
					</tfoot>
					<tbody data-ui="TabMenu" id="delivery_type1_zone">
						
					</tbody>
					
				</table>
		</section>
		
		<!-- O2O 당일배송 -->
		<section class="mt-60" id="delivery_type2_section" style="display:none;">
				<header class="cart-section-title">
					<h3>O2O 당일배송 상품</h3>
					<p class="att">*고객님께서 주문하신 후, 매장에 직접방문에서 찾아가시거나 매장에서 배송되는 상품입니다.</p>
				</header>
				<table class="th-top">
					<caption>장바구니 담긴 품목</caption>
					<colgroup>
						<col style="width:54px">
						<col style="width:auto">
						<col style="width:170px">
						<col style="width:90px">
						<col style="width:130px">
						<col style="width:130px">
						<col style="width:116px">
						<col style="width:20px">
					</colgroup>
					<thead>
						<tr>
							<th scope="col"><div class="checkbox"><input type="checkbox" id="itemPutAll2"  onclick="allcheck(2)"><label for="itemPutAll2"></label></div></th>
							<th scope="col">상품정보</th>
							<th scope="col">수량</th>
							<th scope="col">적립</th>
							<th scope="col">판매가</th>
							<th scope="col">배송정보</th>
							<th scope="col">선택</th>
							<th scope="col" class="fz-0">삭제</th>
						</tr>
					</thead>
					<tfoot id="foot_type2_zone">
						<tr>
							<td colspan="8" class="reset">
							<div class="cart-total-price clear">
								<dl>
									<dt>상품합계</dt>
									<dd id="sum_div_sellprice2"></dd>
								</dl>
								<span class="txt">+</span>
								<dl>
									<dt>배송비</dt>
									<dd id="sum_div_basong2"></dd>
								</dl>
								<dl class="sum">
									<dt>합계</dt>
									<dd id="sum_div_totalprice2"></dd>
								</dl>
								<?if($_ShopInfo->staff_yn=="Y"){?>
								<div class="staff_price point-color">
									<dl>
										<dt>(임직원가)</dt>
										<dd id="sum_div_totalprice2_staff"></dd>
									</dl>
								</div>
								<?}?>
								<?if($_ShopInfo->cooper_yn=="Y"){?>
								<div class="staff_price point-color">
									<dl>
										<dt>(제휴사가)</dt>
										<dd id="sum_div_totalprice2_cooper"></dd>
									</dl>
								</div>
								<?}?>
							</div>
						</td>
					</tfoot>
					<tbody data-ui="TabMenu" id="delivery_type2_zone">
						
					</tbody>
					
				</table>
		</section>
			
			<div class="cart-clear">
				<button class="btn-line w100" onclick="javascript:basket.delBasket('choise');return false;"><span>선택상품 삭제</span></button>
				<button class="btn-line w100" onclick="javascript:basket.delBasket('all');return false;"><span>전체삭제</span></button>
			</div>

			<section class="cart-total-price alone mt-40 clear">
				<h4>총 구입금액</h4>
				<dl>
					<dt>택배배송 상품가</dt>
					<dd><span id="sum_sellprice_type1">0</span></dd>
				</dl>
				<span class="txt">+</span>
				<dl>
					<dt>O2O 상품가</dt>
					<dd><span id="sum_sellprice_type2">0</span></dd>
				</dl>
				<span class="txt">+</span>
				<dl>
					<dt>배송비</dt>
					<dd><span id="sum_basong">0</span></dd>
				</dl>
				<dl class="sum">
					<dt>총 주문금액</dt>
					<dd class="point-color fz-18"><span id="sum_totalprice">0</span></dd>
				</dl>
				<?if($_ShopInfo->staff_yn=="Y"){?>
				<div class="staff_price">
					<dl class="sum">
						<dt class="point-color">(임직원가)</dt>
						<dd class="point-color fz-18" id="sum_totalprice_staff">0</dd>
					</dl>
				</div>
				<?}?>
				<?if($_ShopInfo->cooper_yn=="Y"){?>
				<div class="staff_price">
					<dl class="sum">
						<dt class="point-color">(제휴사가)</dt>
						<dd class="point-color fz-18" id="sum_totalprice_cooper">0</dd>
					</dl>
				</div>
				<?}?>
				
			</section><!-- //.cart-total-price -->
			<div class="btnPlace mt-45">
				<?if(($_ShopInfo->staff_yn=="N" and $_ShopInfo->cooper_yn=="N") or $_ShopInfo->staff_yn=="" ){?>
				<a href="javascript://" class="btn-line h-large w200" onclick="location.href='/'">쇼핑 계속하기</a>
				<a href="javascript://" class="btn-line h-large w200" onclick="basket.goOrder('','');return false;">선택 상품 구매</a>
				<a href="javascript://" class="btn-point h-large w200" onclick="basket.goOrder('all','');return false;">전체 상품 구매</a>
				<?}else if($_ShopInfo->staff_yn=="Y"){?>
				<a href="javascript://" class="btn-line h-large w200" onclick="location.href='/'">쇼핑 계속하기</a>
				<a href="javascript://" class="btn-line h-large w200" onclick="basket.goOrder('','');return false;">선택 상품 구매</a>
				<a href="javascript://" class="btn-line h-large w200" onclick="basket.goOrder('','staff');return false;">선택 상품 임직원 구매</a>
				<a href="javascript://" class="btn-basic h-large w200" onclick="basket.goOrder('all','');return false;">전체 상품 구매</a>
				<a href="javascript://" class="btn-point h-large w200" onclick="basket.goOrder('all','staff');return false;">전체 상품 임직원 구매</a>	
				<?}else if($_ShopInfo->cooper_yn=="Y"){?>
				<a href="javascript://" class="btn-line h-large w200" onclick="location.href='/'">쇼핑 계속하기</a>
				<a href="javascript://" class="btn-line h-large w200" onclick="basket.goOrder('','');return false;">선택 상품 구매</a>
				<a href="javascript://" class="btn-line h-large w200"  onclick="basket.goOrder('','cooper');return false;">선택 상품 제휴사 구매</a>
				<a href="javascript://" class="btn-basic h-large w200" onclick="basket.goOrder('all','');return false;">전체 상품 구매</a>
				<a href="javascript://" class="btn-point h-large w200" onclick="basket.goOrder('all','cooper');return false;">전체 상품 제휴사 구매</a>
				<?}?>
			</div>
		</article><!-- //.cart-order-wrap -->


	</div>
</div><!-- //#contents -->




<?php
 //include ($Dir.TempletDir."basket/basket{$_data->design_basket}.php");


//$sql = "update tblbasket set ord_state=false where tempkey = '".$_ShopInfo->getTempkey()."' ";
//pmysql_query($sql,get_db_conn());
?>
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
if($_SERVER["REMOTE_ADDR"] == "218.234.32.36"){
//exdebug($sql);
//exit;
}
pmysql_free_result( $result );

$wptg_arr   = array();
$wptg_items = '';
foreach( $productArr as $prKey=>$prVal ){
    $wptg_arr[] = '{i:"'.$prVal['productcode'].'", t:"'.$prVal['productname'].'" }';
}
$wptg_items = implode( ',', $wptg_arr );
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
		device:"web"
		,items:[
			 <?=$wptg_items?>
		]
	};
}));
</script>
<script type="text/javascript" async src="//cdn-aitg.widerplanet.com/js/wp_astg_4.0.js"></script>
<!-- // WIDERPLANET PURCHASE SCRIPT END 2017.9.19 -->


<?php include ($Dir."lib/bottom.php") ?>

<form name='orderfrm' id='orderfrm' method='GET' action='<?=$Dir.FrontDir?>order.php' >
<input type='hidden' name='basketidxs' id='basketidxs' value='' >
<input type='hidden' name='staff_order' id='staff_order' value='' >
<input type='hidden' name='cooper_order' id='cooper_order' value='' >
</form>
</BODY>
</HTML>
