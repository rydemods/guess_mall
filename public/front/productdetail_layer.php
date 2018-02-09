<?php
$Dir= $_SERVER[DOCUMENT_ROOT]."/";

/*-------------------------------
 * 공통영역
 *-----------------------------*/
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
include_once dirname(__FILE__)."/../lib/product.class.php";
include_once($Dir."lib/jungbo_code.php"); //정보고시 코드를 가져온다
include_once($Dir."lib/paging_ajax.php");
include_once($Dir."lib/coupon.class.php");

if($_SERVER["PHP_SELF"]=="/front/productdetail_layer.php"){
	include ($Dir.MainDir.$_data->menu_type.".php");	
}

 $_pdata = getProductInfo($productcode);

if($_ShopInfo->cooper_yn == 'Y'){
	// 20170830 나중에 js 수정
	$sql="select b.group_productcode from tblmember a left join tblcompanygroup b on a.company_code=b.group_code where a.cooper_yn='Y' and a.id='{$_ShopInfo->getMemid()}' ";
	$result=pmysql_query($sql);
	$data=pmysql_fetch_object($result);
	$tsale_num	= $data->group_productcode;
	$sale_num	= substr($tsale_num, -1);
}

?>

<!doctype html>
<html lang="ko">
<script type="text/javascript" src="/js/json_adapter/json_adapter.js"></script>
<script type="text/javascript" src="/js/json_adapter/Like.js"></script>
<script type="text/javascript" src="/js/json_adapter/Product.js"></script>
<script type="text/javascript">


var db = new JsonAdapter();
var req = JSON.parse('<?=json_encode($_REQUEST)?>');
var sessid= '<?=$_ShopInfo->getMemid()?>';
var tempkey = '<?=$_ShopInfo->getTempkey()?>';
var vdate = '<?=date("YmdHis");?>';
req.sessid = sessid;
req.tempkey = tempkey;
req.vdate = vdate;
//임직원가여부
req.staff_yn = '<?=$_ShopInfo->staff_yn?>';
req.cooper_yn = '<?=$_ShopInfo->cooper_yn?>';
	
var product = new Product(req);
var like = new Like(req);
var sale_num= '<?=$sale_num?>';

var now_prodcode	= '';
var now_productcode = '';
var productcode = '';


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
			}
		});
	} else {
		alert('발급 가능한 쿠폰이 아닙니다.');
	}
});


$(function(){
	
	if(location.href=='http://test-aja.ajashop.co.kr/front/productdetail_layer.php'){ //테스트용
		
		productLayer('001004005002000037');	
	}
	
	
});


/* 상품상세레이어*/
function productLayer(code){

	$('.goodsPreview').show();
	//product.productLayer(code);
	getDBProduct('<?=$_pdata->prodcode?>', code);
	var coupon ='';
	var data = [];
	
	
	
}




/* 레이어상품상세 */
function getDBProduct(prodcode, productcode){
	
	var data = db.getDBFunc({sp_name: 'product_erp_productcode', sp_param : productcode});
	var prodcode = data.data[0].prodcode;
	
	var p = product.getProduct(prodcode,productcode);

	if (p == 'SOLDOUT'){
		return;
	} else {
		$("#colorChoice_"+now_prodcode+"_"+now_productcode).parents('label').removeClass('active');
		$("#colorChoice_"+prodcode+"_"+productcode).parents('label').addClass('active');
		now_prodcode	= prodcode;
		now_productcode = productcode;
	}
	//console.log(p);
	var pArr = p.pArr[productcode];
	var sellprice = 0;
	var consumerprice = pArr.consumerprice;
	
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
        	p.sellprice = $.trim(data);
        	pArr.sellprice = $.trim(data);
        	sellprice = $.trim(data);
         	p.pArr[productcode].sellprice =$.trim(data); 
        }
    });
    
    
	
	var dcrate = 100-(sellprice /consumerprice) * 100;
	//$('#discount_zone').html('<span>'+dcrate+'</span>% <i class="icon-dc-arrow">할인</i>');
	//$('#sellprice_txt').html('￦'+util.comma(p.pArr[productcode].sellprice));
	

	if(dcrate==0){
		$('.discount_pricezone').hide();
	}
	
	//상품별 할인율가져오기
	var ins_per = product.product_discount_rate(pArr.brand, dcrate);
	$('#layer_point').html(comma(( sellprice * ins_per /100))+'P ('+ins_per+'%)');
	
	
	

	//브랜드명
	$('#brand_nm').html(pArr.brandname);
	$('#goods_nm').html(pArr.productname)
	//$('#goods_code').html('('+pArr.prodcode+')');
	
	//가격
	$('#layer_price').html('<strong>￦'+product.comma(sellprice)+'</strong>');
	$('#layer_delprice').html('<del>￦'+product.comma(pArr.consumerprice)+'</del>');
	$('#sellprice').val(pArr.sellprice);
	$("#sellprice_txt").text("￦"+product.comma(pArr.sellprice));
	
	product.pridx = pArr.pridx;
	

	var sellrate = 100- (pArr.sellprice/pArr.consumerprice) * 100;
	$('#layer_sellrate').html(Math.round(sellrate)); //가격표기
	
	//var reservepoint = pArr.sellprice * (pArr.reserve/100);
	//$('#layer_point').html(product.comma(reservepoint)+' P ('+pArr.reserve+'%)');
	
	$('#season_eng_name').html(p.season_eng_name); //시즌명
	
	//$('#deli_miniprice').html(p.deli_miniprice);
	
	$('#layer_detailview').html('<a href="/front/productdetail.php?productcode='+productcode+'&code='+req.code+'" class="btn-line mt-10 w100-per">상세보기</a>'); //상세보기버튼
	
	//색상
	var rows = '';
	for(var i = 0; i < p.pArr.length; i++){
		var checked= '';
		if(p.pArr[i].color_code==pArr.color_code){
			checked= 'active';	
		}
		
		rows += '<label class="chip-'+p.pArr[i].color_name.toLowerCase()+' '+checked+'" style="background-color: '+p.pArr[i].color_rgb+';"><input type="radio" onclick="getDBProduct(\''+prodcode+'\', \''+p.pArr[i].productcode+'\');" name="color_choice" value="'+p.pArr[i].color_name+'" ><span>'+p.pArr[i].color_name+'</span></label>';
	}
	$('#goods-colorChoice').html(rows);
	
	
	//1-1. 결합상품
	if(p.join_yn =='Y'){
	
		join_type = true;
		
		
		$('#sellpricetxt').html(p.sellprice);
		$('#consumerpricetxt').html(p.consumerprice);
		//$('#join_product_area').html(p.joinproduct);
		
		
		$('.o2o_radio').hide();
		$('#join_product_area').show();
		
		
	//1-2. 일반상품		
	}else{ 
	
		

		$('#sellpricetxt').html(comma(p.pArr[productcode].sellprice));
		$('#opt_zone').html(p.opt_zone);
		
		$('#product_area').show();
		
		
		

		
	}
	
	
	//임직원가
	if(product.staff_yn=='Y'){
		
		var basic_consumerprice = staff_consumerprice;
		basic_consumerprice = basic_consumerprice * ((100-p.pArr[productcode].staff_dc_rate)/100);
		var total_dc_rate = p.pArr[productcode].cooper_dc_rate;
		
		var row = "<label>임직원가</label><strong class='point-color'>￦"+comma(basic_consumerprice)+"</strong>";
			if(total_dc_rate!=0){
			row += '<span >';
			row += '<del>\<span>￦'+comma(p.pArr[productcode].consumerprice)+'</span></del>';
			row += '<div class="discount" id="discount_zone"><span>'+Math.floor(total_dc_rate)+'</span>% <i class="icon-dc-arrow">할인</i></div>';
			row += '</span>';
			}
		$('#price_staff').html(row);
		
	}

		//제휴사가
	if(product.cooper_yn=='Y'){

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
			var row = '<label>제휴사가 </label><strong class="point-color"> \\'+comma(sellprice)+'</strong>';
			$('#price_staff').html(row);
		}else{
			var row = '<label>제휴사가 </label><strong class="point-color"> \\'+comma(basic_consumerprice)+'</strong>';
				if(total_dc_rate!=0){
				row += '<span >';
				row += '<del>\<span>￦'+comma(p.pArr[productcode].consumerprice)+'</span></del>';
				row += '<div class="discount" id="discount_zone"><span>'+Math.round(total_dc_rate)+'</span>% <i class="icon-dc-arrow">할인</i></div>';
				row += '</span>';
				}
			$('#price_staff').html(row);

		}
	}


	//수량
	var rows='<input type="text" value="'+product.quantity+'" id="quantity" name="add_quantity[]" readonly><button class="plus" onclick="product.setQntPlus();"></button><button class="minus"  onclick="product.setQntMinus();"></button>';
	$('#quantity_zone').html(rows);
	
	//좋아요layer
	$('#layer_likezone').html('<button class="btn-line" type="button"><span><i id="like_main" class="icon-like mr-10" onclick="like.clickLike(\'product\',\'main\', \''+prodcode+'\')"></i>좋아요 <span class="point-color">(<span id="like_cnt_main" class="like-cnt-txt">0</span>)</span></span></button>');
	
	//좋아요
	$('.like-cnt-txt').html(p.like_cnt);
	
	//본인좋아요
	if(p.like_my_cnt=='0'){
		$('#like_main').addClass('icon-like');
	}else{
		$('#like_main').addClass('icon-dark-like');
	}
	
	//바로구매
	$('#layer_direct').html('<button class="btn-point w100-per" type="button" onclick="product.order_check();"><span>바로구매</span></button>');
	
	//장바구니
	$('#layer_basket').html('<button class="btn-line" type="button" onclick="product.basketInsert();return false;"><span><i class="icon-cart mr-10" ></i>장바구니</span></button>');		
	
	
	//이미지url경로체크
	var imgdir = '';	
	if(p.minimage.indexOf('http')==-1){
		imgdir = '/data/shopimages/product/';
	}
	
	//메인대이미지
	$('#product_maximage').html('<img src="'+imgdir+p.minimage+'" alt="상품 대표 썸네일">');
	
	//멀티이미지출력
	var rows = '<li><img src="'+imgdir +p.minimage+'" alt=""></a></li>';

	pmiArr = p.pmiArr;

	for(var i = 0; i < pmiArr.length; i++){
		if(pmiArr[i].productcode==productcode){	
			
			var imgdir2 = '';	
			if(pmiArr[i].primg01.indexOf('http')==-1){
				imgdir2 = '/data/shopimages/product/';
			}
			
			if(pmiArr[i].primg01!='')	rows += '	<li><img src="'+imgdir2 + pmiArr[i].primg01+'" alt=""></a></li> ';
			if(pmiArr[i].primg02!='')	rows += '	<li><img src="'+imgdir2 + pmiArr[i].primg02+'" alt=""></a></li> ';
			if(pmiArr[i].primg03!='')	rows += '	<li><img src="'+imgdir2 + pmiArr[i].primg03+'" alt=""></a></li> ';
			if(pmiArr[i].primg04!='')	rows += '	<li><img src="'+imgdir2 + pmiArr[i].primg04+'" alt=""></a></li> ';
			//if(pmiArr[i].primg05!='')	rows += '	<li><img src="'+imgdir2 + pmiArr[i].primg05+'" alt=""></a></li> ';
			//if(pmiArr[i].primg06!='')	rows += '	<li><img src="'+imgdir2 + pmiArr[i].primg06+'" alt=""></a></li> ';
			//if(pmiArr[i].primg07!='')	rows += '	<li><img src="'+imgdir2 + pmiArr[i].primg07+'" alt=""></a></li> ';
			//if(pmiArr[i].primg08!='')	rows += '	<li><img src="'+imgdir2 + pmiArr[i].primg08+'" alt=""></a></li> ';
			//if(pmiArr[i].primg09!='')	rows += '	<li><img src="'+imgdir2 + pmiArr[i].primg09+'" alt=""></a></li> ';
			//if(pmiArr[i].primg10!='')	rows += '	<li><img src="'+imgdir2 + pmiArr[i].primg10+'" alt=""></a></li> ';
			
		}
	}

	$('.thumbList-big').html(rows);
	
	//소이미지(tinyimage)
	var rows = "";
	rows += '	<li id="product_maximage"><a data-slide-index="0"><img src="'+imgdir +p.minimage+'" alt="상품 대표 썸네일"></a></li>';
	
	for(var i = 0; i < pmiArr.length; i++){
		if(pmiArr[i].productcode==productcode){
			
			var imgdir = '';	
			if(pmiArr[i].primg01.indexOf('http')==-1){
				imgdir = '/data/shopimages/product/';
			}
				
			if(pmiArr[i].primg01!='')	rows += '	<li><a data-slide-index="1"><img src="'+imgdir + pmiArr[i].primg01+'" alt=""></a></li> ';
			if(pmiArr[i].primg02!='')	rows += '	<li><a data-slide-index="2"><img src="'+imgdir + pmiArr[i].primg02+'" alt=""></a></li> ';
			if(pmiArr[i].primg03!='')	rows += '	<li><a data-slide-index="3"><img src="'+imgdir + pmiArr[i].primg03+'" alt=""></a></li> ';
			if(pmiArr[i].primg04!='')	rows += '	<li><a data-slide-index="4"><img src="'+imgdir + pmiArr[i].primg04+'" alt=""></a></li> ';
			//if(pmiArr[i].primg05!='')	rows += '	<li><a data-slide-index="5"><img src="'+imgdir + pmiArr[i].primg05+'" alt=""></a></li> ';
			//if(pmiArr[i].primg06!='')	rows += '	<li><a data-slide-index="6"><img src="'+imgdir + pmiArr[i].primg06+'" alt=""></a></li> ';
			//if(pmiArr[i].primg07!='')	rows += '	<li><a data-slide-index="7"><img src="'+imgdir + pmiArr[i].primg07+'" alt=""></a></li> ';
			//if(pmiArr[i].primg08!='')	rows += '	<li><a data-slide-index="8"><img src="'+imgdir + pmiArr[i].primg08+'" alt=""></a></li> ';
			//if(pmiArr[i].primg09!='')	rows += '	<li><a data-slide-index="9"><img src="'+imgdir + pmiArr[i].primg09+'" alt=""></a></li> ';
			//if(pmiArr[i].primg10!='')	rows += '	<li><a data-slide-index="10"><img src="'+imgdir + pmiArr[i].primg10+'" alt=""></a></li> ';
			
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
		
		
}
function order_check(gubun){

        var delivery_type = $('[name="delivery_type"]:checked').val();


        if(delivery_type=='1' || delivery_type=='3'){
/*
                if(gubun=='staff'){
                        alert('임직원 구매는 O2O 배송을 지원하지 않습니다.');
                        return false;
                }
                if(gubun=='cooper'){
                        alert('협력업체 구매는 O2O 배송을 지원하지 않습니다.');
                        return false;
                }
*/
        }



        if(gubun==''){

        }else if(gubun=='staff'){
                $('#staff_order').val('Y');
        }else if(gubun=='cooper'){
                $('#cooper_order').val('Y');
        }
/*
        if(gubun=='staff'){
                p_optsize=$(":radio[name='optSize']:checked").val();
                p_quantity=$("#quantity").val();
                $.ajax({
                        cache: false,
                        type: 'POST',
                        url: "../front/productquantity_check.php",
                        data : { optsize : p_optsize, quantity : p_quantity, productcode : now_productcode, page_type : "detail" },
                        success: function(data) {
                                if( data == "OK" ){
                                        product.basketInsert('direct');
                                } else {
                                        alert("임직원 구매 가능 수량은 "+data+"개입니다.");
                                        return;
                                }
                        }
                });

        }else{*/
                product.basketInsert('direct');
//        }

}

</script>

<!-- 상품 미리보기 -->
<div class="layer-dimm-wrap goodsPreview">
	<div class="layer-inner">
		<h2 class="layer-title hidden">상품미리보기</h2>
		<button class="btn-close" type="button"><span>닫기</span></button>
		<div class="layer-content">
			
			<div class="goods-info-area clear">
				<div class="thumb-box">
					<div class="big-thumb">
						<ul class="thumbList-big">
							
						</ul>
					</div>
					<ul class="thumbList-small clear">
						
					</ul>
				</div><!-- //.thumb-box -->
				<div class="specification">
					<section class="box-intro">
						<h2>브랜드,상품명,금액,간략소개</h2>
						<p class="brand-nm" id="brand_nm"></p>
						<p class="goods-nm" id="goods_nm"></p>
						<p class="goods-code" id="goods_code"></p>
						<div class="price">
							<label>판매가 </label>
							<span id="layer_price"></span>
							
							<span class="discount_pricezone">
								<span id="layer_delprice"></span>
								<div class="discount" id="discount_zone"><span id="layer_sellrate">0</span>% <i class="icon-dc-arrow">할인</i></div>
							</span>
						</div>
						<div class="summarize-ment">
							
						</div>
						
						<div class="price staff" id="price_staff">
								
						</div>
					</section><!-- //.box-intro -->
					<section class="box-summary">
						<h2>상품의 포인트, 할인정보, 배송비 정보</h2>
						<ul class="goods-summaryList">
							<li>
								<label>포인트 적립</label>
								<div id="layer_point"></div>
							</li>
							<?if(count($_pdata->coupon)!=0){?>
							<li>
								<label>할인정보</label>
								<div class="coupon-down">
									<div class="btn-line"><span>쿠폰 다운로드<i class="icon-download"></i></span></div>
									<ul class="list" >
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
									<p class="delivery-ment"><span id="deli_miniprice"><?=number_format($_pdata->deli_miniprice)?></span>원 이상 무료배송 </p>
									<div class="question-btn ml-5">
										<i class="icon-question">무료배송기준 설명</i>
										<div class="comment">
											<dl>
												<dt>배송비 안내</dt>
												<dd><strong>택배수령:</strong> <?=number_format($_pdata->deli_miniprice)?>원 이상 결제시 무료배송</dd>
												<dd><strong>당일수령:</strong> 거리별 추가 배송비 발생</dd>
												<dd><strong>매장픽업:</strong> 배송비 발생하지 않음</dd>
											</dl>
										</div>
									</div>
								</div>
							</li>
						</ul>
					</section><!-- //.box-summary -->
					
					<div class="set-goods" id="join_product_area" >
						
					</div>
						
					<section class="box-opt">
						<h2>상품의 색상,사이즈,수량</h2>
						<div class="goods-colorChoice2" id="goods-colorChoice"><!-- [D] 상세페이지 로딩시 해당 색상은 input 태그 checked 필수 -->
						
						</div>
						<div class="opt-size-wrap">
							<div class="opt-size mt-10" id="opt_zone">
								
							</div>
							<a href="#" class="btn-size-guide">사이즈 가이드</a>
						</div>
						<div class="quantity mt-10" id="quantity_zone">
							
						</div>
						<input type="hidden" id="quantity_max">
						<input type="hidden" name="sellprice" id="sellprice" value="" />
						
						<form name='orderfrm' id='orderfrm' method='GET' action='/front/order.php' >
							<input type='hidden' name='basketidxs' id='basketidxs' value='' >
							<input type='hidden' name='staff_order' id='staff_order' value='' >
						</form>
					</section><!-- //.box-opt -->
					<section class="box-price">
						<h2>총 금액확인, 구매버튼, 장바구니버튼, 좋아요버튼</h2>
						<div class="total clear"><span>총 합계</span><strong id="sellprice_txt">\0</strong></div>
						<div class="buy-btn clear">
							
							<?
								if(($_ShopInfo->staff_yn=="N" and $_ShopInfo->cooper_yn=="N") or $_ShopInfo->staff_yn=="" ){
								
								?>
							
									
									<?if(1==2 &&  $_pdata->quantity <= 0 || $_pdata->soldout == 'Y' ) {?>
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
											<?if(1==2 && $_pdata->quantity <= 0 || $_pdata->soldout == 'Y' ) {?>
											<a href="javascript:alert('품절된 상품입니다..');" class="btn-basic w100-per">품절된 상품입니다.</a>
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
											<?if(1==2 && $_pdata->quantity <= 0 || $_pdata->soldout == 'Y' ) {?>
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
											<?if(1==2 && $_pdata->quantity <= 0 || $_pdata->soldout == 'Y' ) {?>
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
											<?if(1==2 && $_pdata->quantity <= 0 || $_pdata->soldout == 'Y' ) {?>
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
									
								<li><button class="btn-line" type="button" onclick="product.basketInsert();return false;"><span><i class="icon-cart mr-10"></i>장바구니</button></span></li>
								<li>	
									<button class="btn-line " type="button" onclick="like.clickLike('product','main','<?=$_pdata->prodcode?>')">
										<span><i id="like_main" class="mr-10" ></i>좋아요 
										<span class="point-color">(<span id="like_cnt_main" class="like-cnt-txt">0</span>)</span>
									</button></span>
								</li>
							</ul>

							<div id="layer_detailview"></div>
						</div>
					</section><!-- //.box-price -->
				</div><!-- //.goods-specification -->
			</div><!-- //.goods-info-area -->

		</div><!-- //.layer-content -->
	</div>
</div><!-- //상품 미리보기 -->

