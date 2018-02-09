<?

$Htime = date("H");
$vdate = date("YmdHis");

/* 배송/교환/환불정보 */

$sql = "SELECT deli_info FROM tblshopinfo";
$result=pmysql_query($sql,get_db_conn());
$_data_detail=pmysql_fetch_object($result);
pmysql_free_result($result);

if(ord($_data_detail->deli_info)) {
	$tempdeli_info=explode("=",$_data_detail->deli_info);
	$deliinfook=$tempdeli_info[0];
	$deliinfotype=$tempdeli_info[1];
	if($deliinfotype=="TEXT") {
		$deliinfotext1=$tempdeli_info[2];
		$deliinfotext2=$tempdeli_info[3];
	} else if($deliinfotype=="HTML") {
		$deliinfohtml=$tempdeli_info[2];
		$deliinfohtml_m=$tempdeli_info[3];
	}
} else {
	$deliinfook="N";
	$deliinfotype="TEXT";
}

if(ord($deliinfotype)==0) $deliinfotype="TEXT";

//echo $_ShopInfo->staff_yn;
//echo $_ShopInfo->cooper_yn;

//기간할인적용
$_pdata->sellprice = timesale_price($_REQUEST[productcode]);


$sql="select minimage, productname from tblproduct where productcode='$_REQUEST[productcode]' ";
$result=pmysql_query($sql);
$data=pmysql_fetch_object($result);
$minimage	= $data->minimage;
//$productname = $data->productname <--- 
$productname = $data->productname;

if($_ShopInfo->cooper_yn == 'Y'){
	// 20170830 나중에 js 수정
	$sql="select b.group_productcode from tblmember a left join tblcompanygroup b on a.company_code=b.group_code where a.cooper_yn='Y' and a.id='{$_ShopInfo->getMemid()}' ";
	$result=pmysql_query($sql);
	$data=pmysql_fetch_object($result);
	$tsale_num	= $data->group_productcode;
	$sale_num	= substr($tsale_num, -1);
}

?>

<!-- WIDERPLANET  SCRIPT START 2017.9.18 -->
<div id="wp_tg_cts" style="display:none;"></div>
<script type="text/javascript">
var wptg_tagscript_vars = wptg_tagscript_vars || [];
wptg_tagscript_vars.push(
(function() {
	return {
		wp_hcuid:"",  	/*Cross device targeting을 원하는 광고주는 로그인한 사용자의 Unique ID (ex. 로그인 ID, 고객넘버 등)를 암호화하여 대입.
				 *주의: 로그인 하지 않은 사용자는 어떠한 값도 대입하지 않습니다.*/
		ti:"37370",
		ty:"Item",
		device:"web"
		,items:[{i:"<?=$_pdata->productcode?>",	t:"<?=$_pdata->productname?>"}] /* i:상품 식별번호 (Feed로 제공되는 상품코드와 일치하여야 합니다.) t:상품명 */
		};
}));
</script>
<script type="text/javascript" async src="//cdn-aitg.widerplanet.com/js/wp_astg_4.0.js"></script>
<!-- // WIDERPLANET  SCRIPT END 2017.9.18 -->

<!--<script type="text/javascript" src="../static/js/product.js"></script>-->
<script type="text/javascript" src="../js/json_adapter/json_adapter.js"></script>
<script type="text/javascript" src="../js/json_adapter/Product.js"></script>
<script type="text/javascript" src="../js/json_adapter/Like.js"></script>
<script type="text/javascript">
var _pd ="<?=$_pdata->productname?>";
var _ct ="<?=substr($_pdata->productcode,0,12)?>";
var _amt = "<?=$_pdata->sellprice?>"; 
	
var _SA_amt=Array('<?=$_pdata->sellprice?>');
var _SA_nl=Array('1');
var _SA_pl=Array('<?=$_pdata->productcode?>');
var _SA_pn=Array('<?=$_pdata->productname?>');
var _SA_ct=Array('<?=substr($_pdata->productcode,0,12)?>');

var db = new JsonAdapter();
var req = JSON.parse('<?=json_encode($_REQUEST)?>');

var pArr 	= new Array(); //상품배열
var poArr 	= new Array(); //상품옵션배열
var pmiArr 	= new Array(); //상품멀티이미지배열
var basketArr = new Array(); //장바구니용배열
var map, places, iw, search_now;
var beaches = [];
var markers = [];
var markersArray = [];
var endCnt	= 0;
var productcode = '';
var join_type= false; //false일반 true결합 
var size_x = 34;
var size_y = 52;
var icon_x = 20;
var icon_y = 30;
var sessid= '<?=$_ShopInfo->getMemid()?>';
var sale_num= '<?=$sale_num?>';
var sellprice = '<?$_pdata->sellprice?>';
var tempkey = '<?=$_ShopInfo->getTempkey()?>';
var vdate = '<?=$vdate?>';
req.sessid = sessid;
req.tempkey = tempkey;
req.vdate = vdate;

var product = new Product(req);
var like = new Like(req);

var now_prodcode	= '';
var now_productcode = '';

//바디 고정,해제
function bodyFix(){
	$('html,body').css('overflow-y','hidden');
}
function bodyStatic(){
	$('html,body').css('overflow-y','auto');
}

$(function(){

	//sns 이벤트
	$('#facebook-link').click( snsLinkPop );
	$('#twitter-link').click( snsLinkPop );
	$('#band-link').click( snsLinkPop );


	
	$(document).ready(function(){
		$(".btn-preview").unbind('click');
		localStorage.setItem("detailpage",'Y');
	});


	// 배송방법 선택시
	$("[name='delivery_type']").on("click", function(){
		var type = $(this).data("type");
		
		//임식막기
		if(type=='3'){
			alert('서비스 준비중입니다.');
			return false;
		}
		
		
		$(".store-select").removeClass("hide").hide();
		$("#store-select-"+type).show();
	});
	
	//상품DB조회
	getDBProduct('<?=$_pdata->prodcode?>', req.productcode);
	
	
	
	product.mdChoise();//mdchoise
	
	product.categorybest();
	
});


/* 상품DB조회 */
function getDBProduct(prodcode, productcode){
	

	this.productcode = productcode;
	
	var p = product.getProduct(prodcode, productcode);
	if (p == 'SOLDOUT'){

	} else {
		$("#colorChoice_"+now_prodcode+"_"+now_productcode).parents('label').removeClass('active');
		$("#colorChoice_"+prodcode+"_"+productcode).parents('label').addClass('active');
		now_prodcode	= prodcode;
		now_productcode = productcode;
	}
	//alert(p.staff_dc_rate);
	//alert(p.cooper_dc_rate);
	
	//임직원가여부
	var staff_yn = '<?=$_ShopInfo->staff_yn?>';
	var cooper_yn = '<?=$_ShopInfo->cooper_yn?>';
	var sellprice = 0;
	
	//pArr = p.pArr;
	
	//시즌명
	$('#season_eng_name').html(p.season_eng_name);
	
	//수량초기화
	$('#quantity').val(1);
	
	//임직원등은 기간할인제외
	var staff_consumerprice = p.pArr[productcode].consumerprice;
	
	//기간할인체크
	$.ajax({
        url: '/front/promotion_indb.php',
        type:'post',
        data:{gubun :'timesale_price', productcode:productcode},
        dataType: 'text',
        async: false,
        success: function(data) {
        	//console.log($.trim(data));
        	p.pArr[productcode].sellprice = $.trim(data);
         	p.sellprice = $.trim(data);
         	sellprice = $.trim(data);
        }
    });
    
    var dcrate =Math.round(100-(sellprice/p.pArr[productcode].consumerprice)*100);
	$('#discount_zone').html('<span>'+Math.round(dcrate)+'</span>% <i class="icon-dc-arrow">할인</i>');
	
	if(dcrate==0){
		$('.discount_pricezone').hide();
	}
	
	$('#sellprice_txt').html('￦'+util.comma(sellprice));
	//alert(util.comma(sellprice));
	
	//상품별 할인율가져오기
	var ins_per = product.product_discount_rate(p.pArr[productcode].brand, dcrate);
	$('#point_zone').html(util.comma((sellprice * ins_per /100))+'P ('+ins_per+'%)');
	
	
	//반하트,지크는 o2o제외
	if(p.pArr[productcode].brandcd=='O' || p.pArr[productcode].brandcd=='P' ){
		$('.o2o_select_area').hide();
	}
	
	
	//1-1. 결합상품
	if(p.join_yn =='Y'){
	
		join_type = true;
		
		$('#sellpricetxt').html(util.comma(p.sellprice));
		$('#consumerpricetxt').html(util.comma(p.consumerprice));
		//$('#join_product_area').html(p.joinproduct);
		
		
		$('.o2o_radio').hide();
		$('#join_product_area').show();
		
		
	//1-2. 일반상품		
	}else{ 
	
		//alert(p.pArr[productcode].staff_dc_rate);
	
		$('#sellpricetxt').html(util.comma(p.pArr[productcode].sellprice));
		//console.log(p.opt_zone);
		$('#opt_zone').html(p.opt_zone);
		$('#product_area').show();
		
		
	}
	
	//임직원가
	if(staff_yn=='Y'){
		
		var basic_consumerprice = staff_consumerprice;
		basic_consumerprice = Math.round(basic_consumerprice * ((100-p.pArr[productcode].staff_dc_rate)/100));
		var total_dc_rate = p.pArr[productcode].staff_dc_rate;
		
		var row = "<label>임직원가</label><strong class='point-color'> \\"+util.comma(basic_consumerprice)+"</strong>";
			if(total_dc_rate!=0){
			row += '<span >';
			row += '<del>\<span>￦'+util.comma(p.pArr[productcode].consumerprice)+'</span></del>';
			row += '<div class="discount" id="discount_zone"><span>'+Math.round(total_dc_rate)+'</span>% <i class="icon-dc-arrow">할인</i></div>';
			row += '</span>';
			}
		$('#price_staff').html(row);
		
	}
	
	//제휴사가
	if(cooper_yn=='Y'){

		switch (sale_num){
		case '1':
			var sale_conprice = p.pArr[productcode].sale_price1;
			break;
		case '2':
			var sale_conprice = p.pArr[productcode].sale_price2;
			break;
		case '3':
			var sale_conprice = p.pArr[productcode].sale_price3;
			break;
		case '4':
			var sale_conprice = p.pArr[productcode].sale_price4;
			break;
		case '5':
			var sale_conprice = p.pArr[productcode].sale_price5;
			break;
		case '6':
			var sale_conprice = p.pArr[productcode].sale_price6;
			break;
		case '7':
			var sale_conprice = p.pArr[productcode].sale_price7;
			break;
		case '8':
			var sale_conprice = p.pArr[productcode].sale_price8;
			break;
		case '9':
			var sale_conprice = p.pArr[productcode].sale_price9;
			break;
		case '10':
			var sale_conprice = p.pArr[productcode].sale_price10;
			break;
		default :
			alert('제휴사 회원이 아닙니다.');
			return false;
   }
		var basic_consumerprice = Number(sale_conprice);
		var total_dc_rate = (((basic_consumerprice /p.pArr[productcode].consumerprice)-1)*100)* -1;
		
		if(sellprice < basic_consumerprice || basic_consumerprice == 0){
			var row = '<label>제휴사가 </label><strong class="point-color"> \\'+util.comma(sellprice)+'</strong>';
			$('#price_staff').html(row);
		}else{
			var row = '<label>제휴사가 </label><strong class="point-color"> \\'+util.comma(basic_consumerprice)+'</strong>';
				if(total_dc_rate!=0){
				row += '<span >';
				row += '<del>\<span>￦'+util.comma(p.pArr[productcode].consumerprice)+'</span></del>';
				row += '<div class="discount" id="discount_zone"><span>'+Math.round(total_dc_rate)+'</span>% <i class="icon-dc-arrow">할인</i></div>';
				row += '</span>';
				}
			$('#price_staff').html(row);

		}
	}
	
	
	//좋아요
	$('.like-cnt-txt').html(p.like_cnt);
	
	//본인좋아요
	

	if(p.like_my_cnt=='0'){
		$('#like_main').addClass('icon-like');
	}else{
		$('#like_main').addClass('icon-dark-like');
	}
	
	
	//메인대이미지
	var imgdir = '';	
	if(p.minimage.indexOf('http')==-1){
		imgdir = '/data/shopimages/product/';
	}
	$('#product_maximage').html('<img src="'+imgdir +p.minimage+'" alt="상품 대표 썸네일">');
	
	//멀티이미지출력
	var rows = '<li><img src="'+imgdir +p.minimage+'" alt=""></a></li>';

	pmiArr = p.pmiArr;
	

	for(var i = 0; i < pmiArr.length; i++){
		
		if(pmiArr[i].productcode==productcode){	
		
			//이미지url경로체크
			var imgdir = '';	
			if(pmiArr[i].primg01.indexOf('http')==-1){
				imgdir = '/data/shopimages/product/';
			}
			
			
			if(pmiArr[i].primg01!='')	rows += '	<li><img src="'+ imgdir + pmiArr[i].primg01+'" alt=""></a></li> ';
			if(pmiArr[i].primg02!='')	rows += '	<li><img src="'+ imgdir + pmiArr[i].primg02+'" alt=""></a></li> ';
			if(pmiArr[i].primg03!='')	rows += '	<li><img src="'+ imgdir + pmiArr[i].primg03+'" alt=""></a></li> ';
			if(pmiArr[i].primg04!='')	rows += '	<li><img src="'+ imgdir + pmiArr[i].primg04+'" alt=""></a></li> ';
			//if(pmiArr[i].primg05!='')	rows += '	<li><img src="'+ imgdir + pmiArr[i].primg05+'" alt=""></a></li> ';
			//if(pmiArr[i].primg06!='')	rows += '	<li><img src="'+ imgdir + pmiArr[i].primg06+'" alt=""></a></li> ';
			//if(pmiArr[i].primg07!='')	rows += '	<li><img src="'+ imgdir + pmiArr[i].primg07+'" alt=""></a></li> ';
			//if(pmiArr[i].primg08!='')	rows += '	<li><img src="'+ imgdir + pmiArr[i].primg08+'" alt=""></a></li> ';
			//if(pmiArr[i].primg09!='')	rows += '	<li><img src="'+ imgdir + pmiArr[i].primg09+'" alt=""></a></li> ';
			//if(pmiArr[i].primg10!='')	rows += '	<li><img src="'+ imgdir + pmiArr[i].primg10+'" alt=""></a></li> ';
			
		}
	}

	$('.thumbList-big').html(rows);
	
	//소이미지(tinyimage)
	var rows = "";
	var imgdir = '';	
	if(p.minimage.indexOf('http')==-1){
		imgdir = '/data/shopimages/product/';
	}
	rows += '	<li id="product_maximage"><a data-slide-index="0"><img src="'+imgdir +p.minimage+'" alt="상품 대표 썸네일"></a></li>';
	
	for(var i = 0; i < pmiArr.length; i++){
		if(pmiArr[i].productcode==productcode){
			
			var imgdir = '';	
			if(pmiArr[i].primg01.indexOf('http')==-1){
				imgdir = '/data/shopimages/product/';
			}
				
			if(pmiArr[i].primg01!='')	rows += '	<li><a data-slide-index="1"><img src="'+ imgdir +pmiArr[i].primg01+'" alt=""></a></li> ';
			if(pmiArr[i].primg02!='')	rows += '	<li><a data-slide-index="2"><img src="'+ imgdir +pmiArr[i].primg02+'" alt=""></a></li> ';
			if(pmiArr[i].primg03!='')	rows += '	<li><a data-slide-index="3"><img src="'+ imgdir +pmiArr[i].primg03+'" alt=""></a></li> ';
			if(pmiArr[i].primg04!='')	rows += '	<li><a data-slide-index="4"><img src="'+ imgdir +pmiArr[i].primg04+'" alt=""></a></li> ';
			//if(pmiArr[i].primg05!='')	rows += '	<li><a data-slide-index="5"><img src="'+ imgdir +pmiArr[i].primg05+'" alt=""></a></li> ';
			//if(pmiArr[i].primg06!='')	rows += '	<li><a data-slide-index="6"><img src="'+ imgdir +pmiArr[i].primg06+'" alt=""></a></li> ';
			//if(pmiArr[i].primg07!='')	rows += '	<li><a data-slide-index="7"><img src="'+ imgdir +pmiArr[i].primg07+'" alt=""></a></li> ';
			//if(pmiArr[i].primg08!='')	rows += '	<li><a data-slide-index="8"><img src="'+ imgdir +pmiArr[i].primg08+'" alt=""></a></li> ';
			//if(pmiArr[i].primg09!='')	rows += '	<li><a data-slide-index="9"><img src="'+ imgdir +pmiArr[i].primg09+'" alt=""></a></li> ';
			//if(pmiArr[i].primg10!='')	rows += '	<li><a data-slide-index="10"><img src="'+ imgdir +pmiArr[i].primg10+'" alt=""></a></li> ';
			
		}
	}
	$('.thumbList-small').html(rows);
	
	
	
	
	//썸네일
	$('.thumbList-big').bxSlider({
		mode:'fade',
		controls:false,
		pagerCustom: '.thumbList-small'
	});
	
	
	
	//큰이미지클릭시상세
	$('#pr_content_li').html(rows);

	//상세정보bind
	$('#content_li').html(p.main_content);
	$('#pr_content').html(p.pr_content);

	
	//정보고시
	var jungbogosi	= '';
	var prop_option	= p.prop_option;
	var prop_val	= p.prop_val;
	if(prop_option !='') {
		jungbogosi += "<table class=\"th-left mb-50\">";
		jungbogosi += "	<caption>상품 고시정보</caption>";
		jungbogosi += "	<colgroup>";
		jungbogosi += "		<col style=\"width:190px\">";
		jungbogosi += "		<col style=\"width:auto\">";
		jungbogosi += "	</colgroup>";
		jungbogosi += "	<tbody>";
		var jb_option = prop_option.split("||");
		var jb_val = prop_val.split("||");
		for(var i = 1; i < jb_option.length; i++){
			jungbogosi += "		<tr>";
			jungbogosi += "			<th scope=\"row\">"+jb_option[i]+"</th>";
			jungbogosi += "			<td>"+jb_val[i]+"</td>";
			jungbogosi += "		</tr>";
		}
		jungbogosi += "		<tr>";
		jungbogosi += "			<th scope=\"row\">*수선불가 항목</th>";
		jungbogosi += "			<td>소매기장 / 총장 / 밑단 수선등 디자인 변경불가 (리폼불가)</td>";
		jungbogosi += "		</tr>";

		jungbogosi += "	</tbody>";
		jungbogosi += "</table>";
	}
	$('#jg_content').html(jungbogosi);
		
}


 
// 쿠폰 다운로드
$(document).on( 'click', '.CLS_coupon_download', function( event ) {
	var coupon_code       = $(this).attr('data-coupon');
	if( coupon_code.length > 0 ) {
		$.ajax({
			type: "POST",
			url: "../front/ajax_coupon_download.php",
			data : { coupon_code : coupon_code },
			dataType : 'json'
		}).done( function( data ){
			if( data.success === true ){
				alert('쿠폰이 발급 되었습니다.');
			} else {
				alert(data.msg);
				if (data.code == '99') {
					document.location.href="<?=$Dir.FrontDir?>login.php?chUrl=<?=getUrl()?>";
				}
			}
		});
	} else {
		alert('발급 가능한 쿠폰이 아닙니다.');
	}
});

function order_check(gubun){
	
	var delivery_type = $('[name="delivery_type"]:checked').val();
	
	
	if(delivery_type=='1' || delivery_type=='3'){

//	20170824 수정
//		if(gubun=='staff'){
//			alert('임직원 구매는 O2O 배송을 지원하지 않습니다.');
//			return false;	
//		}
//		if(gubun=='cooper'){
//			alert('제휴업체 구매는 O2O 배송을 지원하지 않습니다.');
//			return false;	
//		}
		
	}
	
	
	
	if(gubun==''){
			
	}else if(gubun=='staff'){
		$('#staff_order').val('Y');
	}else if(gubun=='cooper'){
		$('#cooper_order').val('Y');
	}
// /* 20170824 수정
//	if(gubun=='staff'){
//		p_optsize=$(":radio[name='optSize']:checked").val();
//		p_quantity=$("#quantity").val();
//		$.ajax({
//			cache: false,
//			type: 'POST',
//			url: "../front/productquantity_check.php",
//			data : { optsize : p_optsize, quantity : p_quantity, productcode : productcode, page_type : "detail" },
//			success: function(data) {
//				if( data == "OK" ){
//					product.basketInsert('direct');
//				} else {
//					alert("임직원 구매 가능 수량은 "+data+"개입니다.");
//					$('#staff_order').val('');
//					return;
//				}
//			}
//		});

//	}else{
// */
		p_optsize=$(":radio[name='optSize']:checked").val();
		p_quantity=$("#quantity").val();
		$.ajax({
			cache: false,
			type: 'POST',
			url: "../front/productquantity2015_check.php",
			data : { optsize : p_optsize, quantity : p_quantity, productcode : productcode, page_type : "detail" },
			success: function(data) {
				if( data == "OK" ){
					if(gubun == 'basketI'){
						product.basketInsert();
						return false;
					}else{
						product.basketInsert('direct');
					}
				} else {
					alert("매장발송 불가상품입니다.");
					return;
				}
			}
		});
//	}
		
}

</script>
<?php 
// echo "product [".$_pdata."]";
?>

<!-- 상품상세 - 리뷰 -->
<? include($Dir.FrontDir."prreview_tem001.php"); ?>
<!-- // 상품상세 - 리뷰 -->

<!-- 상품상세 - Q&A -->
<? include($Dir.FrontDir."prqna_tem001.php"); ?>
<!-- // 상품상세 - Q&A -->


<div id="contents">
	<div class="goodsView-page goodsView">
		<article class="goods-view-wrap">
		
				<div class="goods-info-area clear">
					<div class="thumb-box">
						<div class="big-thumb" id="thumb-zoomView">
							<ul class="thumbList-big" >
								<li><img src="<?=$minimage?>" alt=""></a></li>
							</ul>
						</div>
						
						<ul class="thumbList-small clear">
							<li><a data-slide-index="0"><img src="<?=$minimage?>" alt=""></a></li>
						</ul>
					</div><!-- //.thumb-box -->
					
					
					<div class="specification">
						<section class="box-intro">
							<h2>브랜드,상품명,금액,간략소개</h2>
							<p class="brand-nm" id="brand-nm"><?=$_pdata->brand?></p>
							<p class="goods-nm" id="goods-nm"><?=strip_tags($_pdata->productname)?></p>
							<!--<p class="goods-code" id="goods-code"><?php if($_pdata->prodcode){?>(<?=strip_tags($_pdata->prodcode)?>)<?php }?></p>-->
							<div class="price">
								<label>판매가</label>
								<strong>\<span id="sellpricetxt"><?=number_format( $_pdata->sellprice )?></span></strong>
								
								<span class="discount_pricezone">
									<del>\<span id="consumerpricetxt"><?=number_format( $_pdata->consumerprice )?></span></del>
									<div class="discount" id="discount_zone"><span><?=$_pdata->price_percent?></span>% <i class="icon-dc-arrow">할인</i></div>
								</span>
								
								<input type="hidden" name="sellprice" id="sellprice" value="<?=$_pdata->sellprice?>" />
							</div>
							
							<div class="price staff" id="price_staff">
								
							</div>
							
							
							
							<div class="summarize-ment">
								<p><?=$_pdata_prcontent?></p>
							</div>
						</section><!-- //.box-intro -->
						<section class="box-summary">
							<h2>상품의 포인트, 할인정보, 배송비 정보</h2>
							<ul class="goods-summaryList">
								<li>
									<label>포인트 적립</label>
									<div id="point_zone"></div>
								</li>
								<?if(count($_pdata->coupon)!=0){?>
								<li>
									<label>할인정보</label>
									<div class="coupon-down">
										<div class="btn-line"><span>쿠폰 다운로드<i class="icon-download"></i></span></div>
										<ul class="list">
										<?php
										foreach( $_pdata->coupon as $pcKey=>$pcVal ){?>
											<li>
												<p><?=$pcVal->coupon_name?></p>
												<button type="button" class="btn-line CLS_coupon_download" data-coupon='<?=$pcVal->coupon_code?>'><span>쿠폰 다운로드<i class="icon-download"></i></span></button>
											</li>
										<?php }?>
										</ul>
									</div>
								</li>
								<?}?>
								<li>
									<label>시즌정보</label>
									<div id="season_eng_name"><!-- 2016SS --></div>
								</li>
								<li>
									<label>배송비</label>
									<div>
										<p class="delivery-ment"><?=number_format($_pdata->deli_miniprice)?>원 이상 무료배송 </p>
										<div class="question-btn ml-5">
											<i class="icon-question">무료배송기준 설명</i>
											
											<div class="comment">
											<dl>
												<dt>배송비 안내</dt>
												<dd><strong>택배발송:</strong> <?=number_format($_pdata->deli_miniprice)?>원 이상 결제시 무료배송</dd>
												<dd><strong>당일수령:</strong> 거리별 추가 배송비 발생</dd>
												<dd><strong>매장픽업:</strong> 배송비 발생하지 않음</dd>
											</dl>
										</div>
										</div>
									</div>
								</li>
							</ul>
						</section><!-- //.box-summary -->
						
						<div class="set-goods" id="join_product_area" style="display: none;">
							
						</div>
						
						<section class="box-opt"  id="product_area" style="display: none;">
							<h2>상품의 색상,사이즈,수량</h2>
							<div class="goods-colorChoice2">
								<?php
								$isActive = "";
					
								foreach ($_pdata->color as $color){

									if($_pdata->color_code == $color['color_code']){
										$isActive = "active";
									}else{
										$isActive = "";
									}
									
								?>
								<label class="<?=$isActive?>" style="background-color: <?=$color['color_rgb']?>;" id="<?=$color['color_code']?>">
									<input type="radio" name="add_option[]" value="<?=$color['color_code']?>"  onclick="getDBProduct('<?=$_pdata->prodcode?>','<?=$color['productcode']?>');" id="colorChoice_<?=$_pdata->prodcode.'_'.$color['productcode']?>">
									<span><?=$color['color_name']?></span>
								</label>
								<?php 
								}?> 
							</div>
							
							<div class="opt-size-wrap">
								<div class="opt-size " id="opt_zone">
									<!--사이즈binding-->
								</div>
								<a href="#" class="btn-size-guide">사이즈 가이드</a>
							</div>
							
							
							
						</section><!-- //.box-opt -->
						
						
						<div class="opt-size-wrap">
							<div class="opt-size">
								
							</div>
							<a href="javascript:void();" class="btn-size-guide">사이즈 가이드</a>
						</div>
						<div class="quantity mt-10">
							<input type="text" value="1" id="quantity" name="add_quantity[]" readonly>
							<button class="plus" onclick="product.setQntPlus();"></button>
							<button class="minus" onclick="product.setQntMinus();"></button>
						</div>
						<input type="hidden" id="quantity_max">
						
					
						<section class="box-delivery">
							<h2>상품수령방법 선택 - 택배수령,당일수령,매장픽업</h2>
							<div class="delivery-type mt-20" data-ui="TabMenu">
								<div class="type">
									<div class="radio">
										<input type="radio" class="CLS_delivery_type" id="deliver_type0" name="delivery_type" data-type="0" value="0" checked="">
										<label for="deliver_type0">택배발송</label>
									</div>
									<div class="radio o2o_radio o2o_select_area" >
										<input type="radio" class="CLS_delivery_type" id="deliver_type1" name="delivery_type" data-type="1" value="1">
										<label for="deliver_type1">매장픽업</label>
									</div>
									<div class="radio o2o_radio o2o_select_area">
										<input type="radio" class="CLS_delivery_type" id="deliver_type3" name="delivery_type" data-type="3" value="3">
										<label for="deliver_type3">당일수령</label>
									</div>
			
									<div class="question-btn">
										<i class="icon-question">타이틀</i>
										<div class="comment">
										<dl>
											<dt>배송방법 안내</dt>
											<dd><strong>택배발송:</strong> 택배로 발송하는 기본 배송 서비스</dd>
											<dd><strong>당일수령:</strong> 당일수령이 가능한 라이더 배송 서비스</dd>
											<dd><strong>매장픽업:</strong> 원하는 날짜, 원하는 매장에서 상품을 <br><span style="padding-left:54px"></span>받아가는 맞춤형 배송 서비스</dd>
										</dl>
									</div>
									</div>
								</div>
								
								
							
								<div id="store-select-1" class="store-select hide">
									&nbsp;
									<span id="mapSelectStoreName1"></span>
									
									<button class="btn-basic" type="button" id="" onclick="getShopO2O(1)"><span>매장 선택</span></button>
									<a class="btn-type1" id="setStorePickupMark"></a> 
								</div>
							
								<div id="store-select-3" class="store-select hide">
									&nbsp;
									<span id="mapSelectStoreName2">당일수령은 서울지역만 가능합니다.</span>
									
									<button class="btn-basic" type="button" id="" onclick="getShopO2O(3)"><span>매장 선택</span></button>
									<a class="btn-type1" id="setStorePickupMark"></a> 
								</div>
								
								
								<input type="hidden" name="mapSelectStore" id="mapSelectStore">
								
							</div>
						</section><!-- //.box-delivery -->
						
						<section class="box-price">
							<h2>총 금액확인, 구매버튼, 장바구니버튼, 좋아요버튼</h2>
							<div class="total clear"><span>총 합계</span><strong id="sellprice_txt">\<?=number_format( $_pdata->sellprice )?></strong></div>
							<div class="buy-btn clear">
							
								<?
								if(($_ShopInfo->staff_yn=="N" and $_ShopInfo->cooper_yn=="N") or $_ShopInfo->staff_yn=="" ){
								
								?>
							
									
									<?if( $_pdata->quantity <= 0 || $_pdata->soldout == 'Y' ) {?>
									<a href="javascript:alert('품절된 상품입니다.');" class="btn-point w100-per">품절된 상품입니다.</a>
									<?php 
									} else {
										$mem_auth_type	= getAuthType($_ShopInfo->getMemid());
										if($mem_auth_type!='sns') {
										?>
									<a href="javascript:order_check('<?=strlen( $_ShopInfo->getMemid() )?>','N');" class="btn-point w100-per">바로구매</a>	
										<? 	 
										} else {
										?>
									<a href="javascript:chkAuthMemLoc('','pc');" class="btn-point w100-per">바로구매</a>	
										<? 
										}
									}?>
								
								
								<?
								}else if($_ShopInfo->staff_yn=="Y"){
								?>
								
									<ul class="clear"><!-- [D] 임직원 구매인 경우 노출 -->
										<li>
											<?if( $_pdata->quantity <= 0 || $_pdata->soldout == 'Y' ) {?>
											<a href="javascript:alert('품절된 상품입니다.');" class="btn-basic w100-per">품절된 상품입니다.</a>
											<?php 
											} else {
												$mem_auth_type	= getAuthType($_ShopInfo->getMemid());
												if($mem_auth_type!='sns') {
												?>
											<a href="javascript:order_check('');" class="btn-basic w100-per">바로구매</a>	
												<? 	 
												} else {
												?>
											<a href="javascript:chkAuthMemLoc('','pc');" class="btn-basic w100-per">바로구매</a>	
												<? 
												}
											}?>
										</li>
										<li>
											<?if( $_pdata->quantity <= 0 || $_pdata->soldout == 'Y' ) {?>
											<a href="javascript:alert('품절된 상품입니다.');" class="btn-point w100-per">품절된 상품입니다.</a>
											<?php 
											} else {
												$mem_auth_type	= getAuthType($_ShopInfo->getMemid());
												if($mem_auth_type!='sns') {
												?>
											<a href="javascript:order_check('staff');" class="btn-point w100-per">임직원 구매</a>	
												<? 	 
												} else {
												?>
											<a href="javascript:chkAuthMemLoc('','pc');" class="btn-point w100-per">임직원 구매</a>	
												<? 
												}
											}?>
											
										</li>
									</ul>
							
								
								<?	
								}else if($_ShopInfo->cooper_yn=="Y"){
								?>
								
									<ul class="clear">
										<li>
											<?if( $_pdata->quantity <= 0 || $_pdata->soldout == 'Y' ) {?>
											<a href="javascript:alert('품절된 상품입니다.');" class="btn-basic w100-per">품절된 상품입니다.</a>
											<?php 
											} else {
												$mem_auth_type	= getAuthType($_ShopInfo->getMemid());
												if($mem_auth_type!='sns') {
												?>
											<a href="javascript:order_check('');" class="btn-basic w100-per">바로구매</a>	
												<? 	 
												} else {
												?>
											<a href="javascript:chkAuthMemLoc('','pc');" class="btn-basic w100-per">바로구매</a>	
												<? 
												}
											}?>
										</li>
										<li>
											<?if( $_pdata->quantity <= 0 || $_pdata->soldout == 'Y' ) {?>
											<a href="javascript:alert('품절된 상품입니다.');" class="btn-point w100-per">품절된 상품입니다.</a>
											<?php 
											} else {
												$mem_auth_type	= getAuthType($_ShopInfo->getMemid());
												if($mem_auth_type!='sns') {
												?>
											<a href="javascript:order_check('cooper');" class="btn-point w100-per">제휴사 구매</a>	
												<? 	 
												} else {
												?>
											<a href="javascript:chkAuthMemLoc('','pc');" class="btn-point w100-per">제휴사 구매</a>	
												<? 
												}
											}?>
											
										</li>
									</ul>
								
								<?
								}
								?>
								
								
							
								
								<ul class="mt-10">
									<!-- 20170823 2015년 상품 매장발송 안되게 체크 장바구니 자체도 안되게 -->
									<!--<li><button class="btn-line" type="button" onclick="product.basketInsert();return false;"  id="cartBtn"><span><i class="icon-cart mr-10" id="car_main"></i>장바구니</button></span></li>-->
									<?if( $_pdata->quantity <= 0 || $_pdata->soldout == 'Y' ) {?>
										<li><button class="btn-line" type="button" onclick="javascript:alert('품절된 상품입니다.');"  id="cartBtn"><span><i class="icon-cart mr-10" id="car_main"></i>장바구니</button></span></li>
									<?} else {?>
										<li><button class="btn-line" type="button" onclick="javascript:order_check('basketI');"  id="cartBtn"><span><i class="icon-cart mr-10" id="car_main"></i>장바구니</button></span></li>
									<?}?>									
									<li>	
										<button class="btn-line " type="button" onclick="like.clickLike('product','main','<?=$_pdata->prodcode?>')" id="wishBtn">
											<span><i id="like_main" class="mr-10" ></i>좋아요 
											<span class="point-color">(<span id="like_cnt_main" class="like-cnt-txt">0</span>)</span>
										</button></span>
									</li>
								</ul>
							</div>
						</section><!-- //.box-price -->
						
						
						<ul class="layer-view-menu">
							<li><button type="button" id="btn-detailPop"><span>상품상세정보</span></button><i class="icon-crosshair"></i></li>
							<li><button type="button" id="btn-deliveryPop"><span>배송반품</span></button><i class="icon-crosshair"></i></li>
						</ul><!-- //.layer-view-menu -->
						<div class="board-share">
							<div class="board-btn">
								<button class="btn-line" type="button" id="btn-reviewList"><span>리뷰<span class="point-color" id="review_count"></span></span></button>
								<button class="btn-line" type="button" id="btn-qnaList"><span>Q&amp;A<span class="point-color" id="qna_count"></span></span></button>
							</div>
							<div class="share">
								<button type="button" type="button"><span><i class="icon-share">상품 공유하기</i></span></button>
								<div class="links">
									<?
									$imgdir = "";
									if(strpos($minimage, "http")=="0"){
										$imgdir = $minimage;
									}else{
										$imgdir = 'http://'.$_SERVER["HTTP_HOST"].'/data/shopimages/product/'.$minimage;
									}
									?>
									<input type="hidden" id="link-label" value="SHINWON MALL">
									<input type="hidden" id="link-title" value="<?=$productname?>">
									<input type="hidden" id="link-image" value="<?=$imgdir?>" data-width='200' data-height='300'>
									<input type="hidden" id="link-url" value="http://<?=$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"]?>">
									<input type="hidden" id="link-img-path"value="<?=$imgdir?>">
									<input type="hidden" id="link-code"value="<?=$_REQUEST[productcode]?>">
									<input type="hidden" id="link-menu"value="product">
									<input type="hidden" id="link-memid" value="">
									
									<a href="javascript:kakaoStory();"><i class="icon-kas">카카오 스토리</i></a>
									<a href="javascript:;" id="facebook-link"><i class="icon-facebook-dark">페이스북</i></a>
									<a href="https://twitter.com/intent/tweet?url=http://<?=$_SERVER[HTTP_HOST].$_SERVER[REQUEST_URI]?>&amp;sort=latest&amp;text=<?=$productname?>" id="twitter-link"><i class="icon-twitter">트위터</i></a>
									<a href="javascript:;" id="band-link"><i class="icon-band">밴드</i></a>
									<a href="javascript:ClipCopy('http://<?=$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"]?>');"><i class="icon-link">링크</i></a>
									
								</div>
								
							</div>
						</div><!-- //.board-share -->
					</div><!-- //.goods-specification -->
				</div><!-- //.goods-info-area -->
		

			<div class="mds-choice" id="mdchoise_div">
				<h3 class="roof-title"><span>MD's CHOICE</span></h3>
				<ul class="goods-list four clear" id="mdchoise">
					
				</ul>
			</div><!-- //.mds-choice -->
			<div class="category-best" id="categorybest_div">
				<h3 class="roof-title"><span>CATEGORY BEST</span></h3>
				<ul class="goods-list four clear" id="categorybest">
					
				</ul>
			</div><!-- //.category-best -->

		</article><!-- //.goods-view-wrap -->

		<!--클릭시이미지-->
		<div class="goodsThumb-zoom inner-align ta-c hide">
			<button type="button" id="thumb-zoomClose"><span><i class="icon-close-small">닫기</i></span></button>
			
			<ul id="pr_content_li">
				
			</ul>
		</div><!-- //.goodsThumb-zoom -->

	</div>
</div><!-- //#contents -->


<!-- 상세 > 상품상세정보 -->
<div class="layer-dimm-wrap goodsDetail-pop">
	<div class="layer-inner">
		<h2 class="layer-title">상품 상세정보</h2>
		<button class="btn-close" type="button"><span>닫기</span></button>
		<div class="layer-content">
			
			<div class="editor-output" style="text-align: left;">
				<ul>
					<li id="content_li"></li>
				</ul>
			</div>
			<div id="jg_content"></div>

		</div><!-- //.layer-content -->
	</div>
</div><!-- //상세 > 상품상세정보 -->

<!-- 상세 > 배송반품 -->
<div class="layer-dimm-wrap goodsDelivery-pop">
	<div class="layer-inner">
		<h2 class="layer-title">배송반품</h2>
		<button class="btn-close" type="button"><span>닫기</span></button>
		<div class="layer-content">
			
			<section class="delivery-info">
				<h3 class="title">배송정보</h3>
				<ul>
					<li><?=$deliinfohtml?></li>
				</ul>
			</section>

		</div><!-- //.layer-content -->
	</div>
</div><!-- //상세 > 배송반품 -->

<!-- 상세 > 매장픽업 -->
<div class="layer-dimm-wrap find-shopPickup">

</div>


<div class="layer-dimm-wrap find-shopToday">
	 <? include ($Dir.FrontDir."productdetail_o2o.php");?>
</div>

<!-- 주문 > 배송지목록 -->
<div class="layer-dimm-wrap popList delivery">
	<div class="layer-inner">
		<h2 class="layer-title">배송지 목록</h2>
		<button class="btn-close" type="button"><span>닫기</span></button>
		<div class="layer-content">

			<ul class="list">
				<li>
					<div class="radio ml-20">
						<input type="radio" name="my_deliveryList" id="deliver_list1">
						<label for="deliver_list1"></label>
					</div>
					<div class="content w300">
						<p class="bold">홍길동</p>
						<p class="txt-toneB">서울 강남구 강남대로 123번지</p>
					</div>
				</li>
				<li>
					<div class="radio ml-20">
						<input type="radio" name="my_deliveryList" id="deliver_list2">
						<label for="deliver_list2"></label>
					</div>
					<div class="content w300">
						<p class="bold">홍길동</p>
						<p class="txt-toneB">서울 강남구 강남대로 123번지</p>
					</div>
				</li>
				<li>
					<div class="radio ml-20">
						<input type="radio" name="my_deliveryList" id="deliver_list3">
						<label for="deliver_list3"></label>
					</div>
					<div class="content w300">
						<p class="bold">홍길동</p>
						<p class="txt-toneB">서울 강남구 강남대로 123번지 서울 강남구 강남대로 123번지 서울 강남구 강남대로 123번지</p>
					</div>
				</li>
			</ul>
			<div class="btnPlace mt-10">
				<button class="btn-line  h-large" type="button"><span>취소</span></button>
				<button class="btn-point h-large" type="button"><span>적용</span></button>
			</div>

		</div><!-- //.layer-content -->
	</div>
</div><!-- //주문 > 배송지목록 -->

<!-- 주문 > 매장안내 -->
<div class="layer-dimm-wrap pop-infoStore">
	<div class="layer-inner">
		<h2 class="layer-title">매장 위치정보</h2>
		<button class="btn-close" type="button"><span>닫기</span></button>
		<div class="layer-content">

			<h3 class="store-title">[VIKI]강남직영점</h3>
			<table class="th-left mt-15">
				<caption>매장 정보</caption>
				<colgroup>
					<col style="width:180px">
					<col style="width:auto">
				</colgroup>
				<tbody>
					<tr>
						<th scope="row"><label>주소</label></th>
						<td>서울 강남구 강남대로 238-11</td>
					</tr>
					<tr>
						<th scope="row"><label>운영시간</label></th>
						<td>평일 09:00 ~ 18:00 (토/일 09:00 ~ 18:00)</td>
					</tr>
					<tr>
						<th scope="row"><label>휴무정보</label></th>
						<td>매주 일요일 / 국경일</td>
					</tr>
					<tr>
						<th scope="row"><label>매장 전화번호</label></th>
						<td>02-5212-2512</td>
					</tr>
				</tbody>
			</table>
			<div class="map-local mt-10">구글지도 위치</div>

		</div><!-- //.layer-content -->
	</div>
</div><!-- //주문 > 매장안내 -->
<form name='orderfrm' id='orderfrm' method='GET' action='<?=$Dir.FrontDir?>order.php' >
<input type='hidden' name='basketidxs' id='basketidxs' value='' >
<input type='hidden' name='staff_order' id='staff_order' value='' >
<input type='hidden' name='cooper_order' id='cooper_order' value='' >
</form>
<!--<script src="/sinwon/web/static/js/ui.js"></script>-->

<script type="text/javascript">
 function mobRfShop(){
	var sh = new EN();

    // [상품상세정보]
  	sh.setData("sc", "24c0f448431b13d11a1b3596ac4eb8b3");
  	sh.setData("userid", "shinwonmall");
  	sh.setData("pcode", "<?=$productcode?>");
   	sh.setData("pnm", encodeURIComponent(encodeURIComponent("<?=$productname?>")));
    sh.setData("img", encodeURIComponent("<?=$minimage?>"));   //전체URL
  	sh.setData("price","<?=number_format( $_pdata->sellprice )?>");
  	sh.setData("cate1", encodeURIComponent(encodeURIComponent("<?=$_pdata->brand?>"))); 
  	sh.setSSL(true);
    sh.sendRfShop();
    
    // 장바구니 버튼 클릭 시 호출 메소드(사용하지 않는 경우 삭제)
  	document.getElementById("cartBtn").onmouseup = sendCart;
    function sendCart() {
      	sh.sendCart();
    }
    
  	// 찜,Wish 버튼 클릭 시 호출 메소드(사용하지 않는 경우 삭제)
  	document.getElementById("wishBtn").onmouseup = sendWish;
    function sendWish() {
        sh.sendWish();
    }
  }

</script>
<script async="true" src="https://cdn.megadata.co.kr/js/enliple_min2.js" onload="mobRfShop()"></script>

<?php 
//include_once("productdetail_layer.php"); //미리보기
?>

