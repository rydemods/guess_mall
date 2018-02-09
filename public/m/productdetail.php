<?php
include_once('outline/header_m.php');

$Htime = date("H");
$vdate = date("YmdHis");


/*-------------------------------
 * REQUEST 
 *-----------------------------*/
$popup=$_REQUEST["popup"]; //popup일 경우 (2016-03-01 김재수 추가)
$mode=$_REQUEST["mode"];
$coupon_code=$_REQUEST["coupon_code"];
$code=$_REQUEST["code"];
$prod_cate_code = $code;
$productcode=$_REQUEST["productcode"];
$link_url   = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
$instaimgpath = $Dir.DataDir."shopimages/instagram/";
$productimgpath = $Dir.DataDir."shopimages/product/";



if(ord($code)==0) {
	$code=substr($productcode,0,12);
}
list($code_a,$code_b,$code_c,$code_d) = sscanf($code,'%3s%3s%3s%3s');
if(strlen($code_a)!=3) $code_a="000";
if(strlen($code_b)!=3) $code_b="000";
if(strlen($code_c)!=3) $code_c="000";
if(strlen($code_d)!=3) $code_d="000";
$likecode=$code_a;
if($code_b!="000") $likecode.=$code_b;
if($code_c!="000") $likecode.=$code_c;
if($code_d!="000") $likecode.=$code_d;

$sort=$_REQUEST["sort"];
$brandcode=$_REQUEST["brandcode"]+0;



/*-------------------------------
 * 상품정보 조회 
 *-----------------------------*/
if( strlen($productcode) > 0 ) {
	
	$_pdata = getProductInfo($productcode);

/*	
	//ERP 상품을 쇼핑몰에 업데이트한다.
	$sql = " 	select productcode from tblproduct where prodcode in (
			select prodcode from tblproduct where productcode ='{$productcode}' )";
	//exdebug($sql);
	$result = pmysql_query($sql,get_db_conn());
	while($row = pmysql_fetch_object($result)){
		
		getUpErpProductUpdate($row->productcode);
		
	}
*/	
shell_exec("wget -O /dev/null 'http://{$_SERVER['HTTP_HOST']}/front/product_update.php?productcode={$productcode}' > /dev/null 2>/dev/null &");

	
} else {
// 	alert_go('해당 상품 정보가 존재하지 않습니다.',"/");
}


	

/* 배송/교환/환불정보 */
$sql = "SELECT deli_info, deli_miniprice FROM tblshopinfo";
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

//상품 쿠폰정보
$_pdata->coupon	= getProductCouponInfo($_REQUEST[productcode]);

//기간할인적용
$_pdata->sellprice = timesale_price($_REQUEST[productcode]);


$sql="select minimage, productname, maximage from tblproduct where productcode='$_REQUEST[productcode]' ";
$result=pmysql_query($sql);
$data=pmysql_fetch_object($result);
$minimage	= $data->minimage;
$maximage	= $data->maximage;
if(strpos($maximage,'http')===FALSE) {
	$maximage = '/data/shopimages/product/'.$maximage;
}

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

<!-- *) 제품상세페이지 분석코드 -->
<!-- NSM Site Analyst Mobile eCommerce (Product_Detail) v2.0 Start -->
<script type='text/javascript'>
var m_pd ="<?=$_pdata->productname?>";
var m_ct ="<?php echo substr($_pdata->productcode,0,12);?>";
var m_amt="<?=$_pdata->sellprice?>";
</script>

<!-- *) 공통 분석스크립트  -->
<!-- 1-script.txt -->

<script type="text/javascript" src="/js/json_adapter/json_adapter.js"></script>
<script type="text/javascript" src="/js/json_adapter/Product.js"></script>
<script type="text/javascript" src="/js/json_adapter/Like.js"></script>
<script type="text/javascript">

var db = new JsonAdapter();
var util = new UtilAdapter();
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
req.device = 'M';

var product = new Product(req);
var like = new Like(req);

//바디 고정,해제
function bodyFix(){
	$('html,body').css('overflow-y','hidden');
}
function bodyStatic(){
	$('html,body').css('overflow-y','auto');
}

$(document).ready( function() {
	
	//상품DB조회
	getDBProduct('<?=$_pdata->prodcode?>', req.productcode);
	
	//sns 이벤트
	$('#facebook-link').click( snsLinkPop );
	$('#twitter-link').click( snsLinkPop );
	$('#band-link').click( snsLinkPop );


	
	$(document).ready(function(){
		$(".btn-preview").unbind('click');
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
	
	
	
	//mdchoise
	product.mdChoise('M');
	
	product.categorybest('M');
	
});


/* 상품DB조회 */
function getDBProduct(prodcode, productcode){

	this.productcode = productcode;
	
	//상품조회
	var p = product.getProduct(prodcode, productcode,'M');
	//pArr = p.pArr;
	//console.log(p);
	
	//임직원가여부
	var staff_yn = '<?=$_ShopInfo->staff_yn?>';
	var cooper_yn = '<?=$_ShopInfo->cooper_yn?>';
	var sellprice = 0;
	
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
        	sellprice = $.trim(data);
         	
        }
    });
    

    
    var dcrate =Math.round(100-(sellprice/p.pArr[productcode].consumerprice)*100);
    $('#discount_zone').html('<strong>'+Math.round(dcrate)+'</strong>% <img src="/sinwon/m/static/img/icon/icon_darr.png" alt="할인">');
     
    if(dcrate==0){
		$('.discount_pricezone').hide();
	}
	

	//상품별 할인율가져오기
	var ins_per = product.product_discount_rate(p.pArr[productcode].brand, dcrate);
	$('#point_zone').html(util.comma((sellprice * ins_per /100))+'P ('+ins_per+'%)');
	
	//반하트,지크는 o2o제외
	if(p.pArr[productcode].brandcd=='O' || p.pArr[productcode].brandcd=='P' ){
		$('.o2o_select_area').hide();
	}
	
	$('#brand-nm').html(p.pArr[productcode].brandname);
	$('#goods-nm').html(p.pArr[productcode].productname);
	$('#goods-code').html(p.pArr[productcode].prodcode);
	$('#sellprice_zone').html('￦ '+util.comma(p.pArr[productcode].sellprice));
	$('#consumerprice').html('￦ '+util.comma(p.pArr[productcode].consumerprice));
	var discount_rate = 100-(p.pArr[productcode].sellprice *100)/p.pArr[productcode].consumerprice;
	$('#discount_rate').html(discount_rate);
	$('#point').html(p.pArr[productcode].sellprice*(p.pArr[productcode].point/100) +' P ('+p.pArr[productcode].point+'%)');

	//시즌명
	$('#season_eng_name').html(p.season_eng_name);
	
	$('#sellprice_txt').html('￦'+util.comma(p.pArr[productcode].sellprice));
	
	//1-1. 결합상품
	if(p.join_yn =='Y'){
	
		join_type = true;
		
		
		$('#sellpricetxt').html(p.sellprice);
		$('#consumerpricetxt').html(p.consumerprice);
		$('#join_product_area').html(p.joinproduct);
		
		
		$('.o2o_radio').hide();
		$('#join_product_area').show();
		
		
	//1-2. 일반상품		
	}else{
		
	
		$('#opt-zone').html(p.opt_zone);
		$('#product_area').show();
		
		
	}
	
	
	//임직원가
	if(staff_yn=='Y'){
		
		var basic_consumerprice = staff_consumerprice;
		basic_consumerprice = Math.round(basic_consumerprice * ((100-p.pArr[productcode].staff_dc_rate)/100));
		var total_dc_rate = p.pArr[productcode].staff_dc_rate;
		
		
		var row = " <label>임직원가</label>\n<strong class='point-color'>￦ "+util.comma(basic_consumerprice)+"</strong>";
			row += "<span class='discount_pricezone'>";
			row += "	<del>￦"+util.comma(p.pArr[productcode].consumerprice)+"</del>";
			row += "	<span class='tag_discount'><strong>"+Math.round(total_dc_rate)+"</strong>% <img src='/sinwon/m/static/img/icon/icon_darr.png' alt='할인'></span>";
			row += "</span>";
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
			var row = '<label>제휴사가 </label><strong class="point-color"> ￦ '+util.comma(sellprice)+'</strong>';
			$('#price_staff').html(row);
		}else{
			var row = '<label>제휴사가 </label><strong class="point-color"> ￦ '+util.comma(basic_consumerprice)+'</strong>';
				if(total_dc_rate!=0){
					row += "<span class='discount_pricezone'>";
					row += "	<del>￦"+util.comma(p.pArr[productcode].consumerprice)+"</del>";
					row += "	<span class='tag_discount'><strong>"+Math.round(total_dc_rate)+"</strong>% <img src='/sinwon/m/static/img/icon/icon_darr.png' alt='할인'></span>";
					row += "</span>";
				}
			$('#price_staff').html(row);

		}

/*
		var basic_consumerprice = staff_consumerprice;
		basic_consumerprice = Math.round(basic_consumerprice * ((100-p.pArr[productcode].cooper_dc_rate)/100));
		var total_dc_rate = p.pArr[productcode].cooper_dc_rate;
		
		var row = "<label>제휴사가</label>\n<strong class='point-color'>￦ "+util.comma(basic_consumerprice)+"</strong>";
			row += "<span class='discount_pricezone'>";
			row += "	<del>￦"+util.comma(p.pArr[productcode].consumerprice)+"</del>";
			row += "	<span class='tag_discount'><strong>"+Math.round(total_dc_rate)+"</strong>% <img src='/sinwon/m/static/img/icon/icon_darr.png' alt='할인'></span>";
			row += "</span>";
		$('#price_staff').html(row);	
*/

	}
	
	
	//좋아요
	$('.like-cnt-txt').html(p.like_cnt);
	
	//본인좋아요
	

	if(p.like_my_cnt=='0'){
		//$('#like_main').addClass('');
	}else{
		$('#like_main').addClass('on');
	}
	
	
	var imgdir = '';	
	if(p.maximage.indexOf('http')==-1){
		imgdir = '/data/shopimages/product/';
	}
	
	//메인대이미지
	$('#product_maximage').html('<img src="'+imgdir+p.maximage+'" width="414" alt="상품 대표 썸네일">');
	
	//멀티이미지출력
	var rows = "";

	pmiArr = p.pmiArr;

	
	
	for(var i = 0; i < pmiArr.length; i++){
		if(pmiArr[i].productcode==productcode){
			
			var imgdir = '';	
			if(pmiArr[i].primg01.indexOf('http')==-1){
				imgdir = '/data/shopimages/product/';
			}	
			if(pmiArr[i].primg01!='')	rows += '	<li><img src="'+imgdir +pmiArr[i].primg01+'" alt=""></a></li> ';
			if(pmiArr[i].primg02!='')	rows += '	<li><img src="'+imgdir +pmiArr[i].primg02+'" alt=""></a></li> ';
			if(pmiArr[i].primg03!='')	rows += '	<li><img src="'+imgdir +pmiArr[i].primg03+'" alt=""></a></li> ';
			if(pmiArr[i].primg04!='')	rows += '	<li><img src="'+imgdir +pmiArr[i].primg04+'" alt=""></a></li> ';
			if(pmiArr[i].primg05!='')	rows += '	<li><img src="'+imgdir +pmiArr[i].primg05+'" alt=""></a></li> ';
			if(pmiArr[i].primg06!='')	rows += '	<li><img src="'+imgdir +pmiArr[i].primg06+'" alt=""></a></li> ';
			if(pmiArr[i].primg07!='')	rows += '	<li><img src="'+imgdir +pmiArr[i].primg07+'" alt=""></a></li> ';
			if(pmiArr[i].primg08!='')	rows += '	<li><img src="'+imgdir +pmiArr[i].primg08+'" alt=""></a></li> ';
			if(pmiArr[i].primg09!='')	rows += '	<li><img src="'+imgdir +pmiArr[i].primg09+'" alt=""></a></li> ';
			if(pmiArr[i].primg10!='')	rows += '	<li><img src="'+imgdir +pmiArr[i].primg10+'" alt=""></a></li> ';
			
		}
	}

	$('.thumbList-big').html(rows);
	
	//소이미지(tinyimage)
	var imgdir = '';	
	if(p.maximage.indexOf('http')==-1){
		imgdir = '/data/shopimages/product/';
	}
	var rows = "";
	rows += '	<li id="product_maximage"><img src="'+imgdir +p.maximage+'" width="414" alt="상품 대표 썸네일"></li>';
	
	

	
	for(var i = 0; i < pmiArr.length; i++){
		if(pmiArr[i].productcode==productcode){
			
			var imgdir = '';	
			if(pmiArr[i].primg01.indexOf('http')==-1){
				imgdir = '/data/shopimages/product/';
			}	
				
			if(pmiArr[i].primg01!='')	rows += '	<li><img src="'+imgdir +pmiArr[i].primg01+'" alt=""></li> ';
			if(pmiArr[i].primg02!='')	rows += '	<li><img src="'+imgdir +pmiArr[i].primg02+'" alt=""></li> ';
			if(pmiArr[i].primg03!='')	rows += '	<li><img src="'+imgdir +pmiArr[i].primg03+'" alt=""></li> ';
			if(pmiArr[i].primg04!='')	rows += '	<li><img src="'+imgdir +pmiArr[i].primg04+'" alt=""></li> ';
			if(pmiArr[i].primg05!='')	rows += '	<li><img src="'+imgdir +pmiArr[i].primg05+'" alt=""></li> ';
			if(pmiArr[i].primg06!='')	rows += '	<li><img src="'+imgdir +pmiArr[i].primg06+'" alt=""></li> ';
			if(pmiArr[i].primg07!='')	rows += '	<li><img src="'+imgdir +pmiArr[i].primg07+'" alt=""></li> ';
			if(pmiArr[i].primg08!='')	rows += '	<li><img src="'+imgdir +pmiArr[i].primg08+'" alt=""></li> ';
			if(pmiArr[i].primg09!='')	rows += '	<li><img src="'+imgdir +pmiArr[i].primg09+'" alt=""></li> ';
			if(pmiArr[i].primg10!='')	rows += '	<li><img src="'+imgdir +pmiArr[i].primg10+'" alt=""></li> ';


		}
	}

	$('.thumbList-small').html(rows);
	$('#slide').addClass('slide');
	
	
	//color_code추출
	pArr = p.pArr;
	var rows='';
	var rows2='';
	
	for(var i = 0; i < pArr.length; i++){
		var checked='';
		if(pArr[i].productcode==productcode){	
			checked = 'checked';
		}
		rows += '<li><label class="colorchip" style="background-color: '+pArr[i].color_code+'">';
		rows += '<input type="radio" name="add_option[]" value="'+pArr[i].color_code+'"  onclick="getColorcode(\''+pArr[i].productcode+'\', \''+pArr[i].prodcode+'\');" '+checked+'><span></span></label></li>';

	rows2	+= '		<label class="colorchip" style="background-color: '+pArr[i].color_code+'"><input type="radio" name="add_option2[]" value="'+pArr[i].color_code+'"  onclick="getColorcode(\''+pArr[i].productcode+'\', \''+pArr[i].prodcode+'\');" '+checked+'><span></span></label>';
		 						
			
	}
	$('#color_code').html(rows);

	var ccs	= '';
	if (rows2) {
		ccs	+= '<dl>';
		ccs	+= '	<dt class="colorchip_area">';
		ccs	+= rows2;
		ccs	+= '	</dt>';
		ccs	+= '	<dd>GRAY</dd>';
		ccs	+= '</dl>';
	}

	$('#color_code_sel').html(rows2);
	
	
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
		var jb_option = prop_option.split("||");
		var jb_val = prop_val.split("||");
		for(var i = 1; i < jb_option.length; i++){
			jungbogosi += "		<li>";
			jungbogosi += "			<div class=\"type\">"+jb_option[i]+"</div>";
			jungbogosi += "			<div class=\"content\">"+jb_val[i]+"</div>";
			jungbogosi += "		</li>";
		}
		jungbogosi += "		<li>";
		jungbogosi += "			<div class=\"type\">*수선불가 항목</div>";
		jungbogosi += "			<div class=\"content\">소매기장 / 총장 / 밑단 수선등 디자인 변경불가 (리폼불가)</div>";
		jungbogosi += "		</li>";
	}
	$('#jg_content').html(jungbogosi);
	
	
	//상세 > 상세이미지 슬라이드 
	$('.detail_img .slide').bxSlider({
		controls: false
	});
	
		
}

function getColorcode(productcode, prodcode){
	
	location.href='?productcode='+productcode+'&code='+req.code;
	
	//getDBProduct(prodcode,productcode);
	
}


/* ui_siwon.js 에서 callback */
function addCart(){
	
	product.basketInsert('','M');
	return false;
}

function directOrder(gubun){
	
	var delivery_type = $('[name="delivery_type"]:checked').val();
	var staff_yn = '<?=$_ShopInfo->staff_yn?>';
	var cooper_yn = '<?=$_ShopInfo->cooper_yn?>';

/*		
	if(delivery_type=='1' || delivery_type=='2'){

		if(gubun=='staff'){
			if(staff_yn=='Y'){
				alert('임직원 구매는 O2O 배송을 지원하지 않습니다.');
				return false;
			}
			if(cooper_yn=='Y'){
				alert('협력업체 구매는 O2O 배송을 지원하지 않습니다.');
				return false;	
			}
		}

	}
*/		
	
	if(gubun==''){
			
	}else if(gubun=='staff'){
		
		if(staff_yn=='Y'){
			$('#staff_order').val('Y');	
		}
		if(cooper_yn=='Y'){
			$('#cooper_order').val('Y');
		}
		
	}
/*
	if(gubun=='staff'){
		p_optsize=$(":radio[name='selectSize']:checked").next().text();
		p_quantity=$("#quantity").val();
		$.ajax({
			cache: false,
			type: 'POST',
			url: "../front/productquantity_check.php",
			data : { optsize : p_optsize, quantity : p_quantity, productcode : productcode, page_type : "detail" },
			success: function(data) {
				if( data == "OK" ){
					product.basketInsert('direct');
				} else {
					alert("임직원 구매 가능 수량은 "+data+"개입니다.");
					$('#staff_order').val('Y');	
					return;
				}
			}
		});

	}else{*/
		product.basketInsert('direct');
//	}
		
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
					document.location.href="<?=$Dir.MDir?>login.php?chUrl=<?=getUrl()?>";
				}
			}
		});
	} else {
		alert('발급 가능한 쿠폰이 아닙니다.');
	}
});


function openLayer(){

	$('#layer_review_list').show();
}

</script>


<!-- 상품상세 - 리뷰 -->

<?
 include($Dir.FrontDir."prreview_tem001.php"); ?>
<!-- // 상품상세 - 리뷰 -->

<!-- 상품상세 - Q&A -->
<? include($Dir.FrontDir."prqna_tem001.php"); ?>
<!-- // 상품상세 - Q&A -->


<!--  
<div id="page">
-->
<!-- 내용 -->
<main id="content" class="subpage">
	

	<section class="detailpage page_local">

		<h2 class="page_title">
			<a href="javascript:history.back();" class="prev">이전페이지</a>
			<span>상세페이지</span>
		</h2>
	
		<div class="detail_view">
			<div class="colorchip_area">
				<ul id="color_code">
					
					
				</ul>
			</div>
			<div class="detail_img with-btn-rolling">
				<ul id="slide" class="thumbList-small">
					
				</ul>
			</div>
			<!-- 20170711 모바일 상품상세 페이지 좌우로 이동됨  -->
			<!--  
			<div class="detail_img with-btn-rolling" style="width:414px;height:418px">
				<ul id="slide" class="thumbList-small">
					<li id="product_maximage"><img src="<?=$maximage?>" width="414" alt="상품 대표 썸네일"></li>
				</ul>
			</div>
			-->
		</div><!-- //.detail_view -->
		<div class="goods_info">
			<p class="brand" id="brand-nm"><!--BESTI BELLI--></p>
			<p class="name" id="goods-nm"><!--솔리드 심플 벨티트 자켓--><span class="code" id="goods-code"><!--(toefoe16561)--></span></p>
			<input type="hidden" name="sellprice" id="sellprice" value="<?=$_pdata->sellprice?>" />
			<p class="price">
				<label>판매가</label>
				<span>
					<strong id="sellprice_zone"><!--￦ 105,800--></strong>
					
					<span class="discount_pricezone">
						<del id="consumerprice"><!--￦ 105,800--></del>
						<span class="tag_discount" id="discount_zone"></span>
					</span>
				</span>
			</p>
			<p class="price" id="price_staff">
				
			</p>
			<!-- <p class="text">깔끔한 디자인의 원피스입니다.<br>두툼한 소재로 만들어 초가을까지 입으실 수 있습니다.<br>178cm 마네킹이 66사이즈를 착용하였습니다.</p> -->
			<ul class="etc">
				<li>
					<label>포인트 적립</label>
					<div id="point_zone"></div>
				</li>
				
				<?if(count($_pdata->coupon)!=0){?>
				<li>
					<label>할인정보</label>
					<span>
						<div class="wrap_bubble">
							<div class="btn_bubble"><button type="button" class="btn_coupon_down">쿠폰 다운로드</button></div>
							<div class="pop_bubble">
								<div class="inner">
									<button type="button" class="btn_pop_close">닫기</button>
									<div class="container">
										<p class="tit_pop">쿠폰 다운로드</p>
										<ul class="list_coupon">
										<?php
										foreach( $_pdata->coupon as $pcKey=>$pcVal ){?>
											<li>
												<label><?=$pcVal->coupon_name?></label>
												<button type="button" class="btn_coupon_down CLS_coupon_download" data-coupon='<?=$pcVal->coupon_code?>'>쿠폰 다운로드</button>
											</li>
										<?php }?>
										</ul>
									</div>
								</div>
							</div>
						</div>
					</span>
				</li>
				<?}?>
				<li>
					<label>시즌정보</label>
					<span id="season_eng_name"><!--2016SS--></span>
				</li>
				<li>
					<label>배송비</label>
					<span>
						<?=number_format($_data_detail->deli_miniprice)?>원 이상 무료배송
						<div class="wrap_bubble shipping_fee">
							<div class="btn_bubble"><button type="button" class="btn_help">?</button></div>
							<div class="pop_bubble">
								<div class="inner">
									<button type="button" class="btn_pop_close">닫기</button>
									<div class="container">
										<p class="tit_pop">배송비 안내</p>
										<table class="tbl_txt">
											<colgroup>
												<col style="width:44px;">
												<col style="width:auto;">
											</colgroup>
											<tbody>
												<tr>
													<th>택배발송:</th>
													<td><?=number_format($_data_detail->deli_miniprice)?>원 이상 결제시 무료배송</td>
												</tr>
												<tr>
													<th>당일수령:</th>
													<td>거리별 추가 배송비 발생</td>
												</tr>
												<tr>
													<th>매장픽업:</th>
													<td>배송비 발생하지 않음</td>
												</tr>
											</tbody>
										</table>
									</div>
								</div>
							</div>
						</div><!-- //.wrap_bubble -->
					</span>
				</li>
			</ul>
		</div><!-- //.goods_info -->

		<div class="goods_info_pop">
			<ul class="menu_list">
				<li><a href="javascript:;" class="btn_goods_detail">상품상세정보</a></li>
				<li><a href="javascript:;" class="btn_goods_delivery">배송반품</a></li>
			</ul>
			<div class="menu_btn">
				<a href="javascript:;" class="btn_review_list btn-line" onclick="openLayer();">리뷰<span class="point-color" id="review_count"></span></a>
				<a href="javascript:;" class="btn_qna_list btn-line">Q&A<span class="point-color" id="qna_count"></span></a>
				
				<div class="wrap_bubble layer_sns_share">
					<div class="btn_bubble"><button type="button" class="btn_sns_share">sns 공유</button></div>
					<div class="pop_bubble">
						<div class="inner">
							<button type="button" class="btn_pop_close">닫기</button>
							<div class="icon_container">
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
								
								<a href="javascript:kakaoStory();"><img src="/sinwon/m/static/img/icon/icon_sns_kas.png" alt=""></a>
								<a href="javascript:;" id="facebook-link"><img src="/sinwon/m/static/img/icon/icon_sns_face.png" alt=""></a>
								<a href="https://twitter.com/intent/tweet?url=http://<?=$_SERVER[HTTP_HOST].$_SERVER[REQUEST_URI]?>&amp;sort=latest&amp;text=<?=$productname?>" id="twitter-link"><img src="/sinwon/m/static/img/icon/icon_sns_twit.png" alt=""></a>
								<a href="javascript:;" id="band-link"><img src="/sinwon/m/static/img/icon/icon_sns_band.png" alt=""></a>
								<a href="javascript:ClipCopy('http://<?=$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"]?>');"><img src="/sinwon/m/static/img/icon/icon_sns_link.png" alt=""></a>
							</div>
						</div>
					</div>
				</div><!-- //.wrap_bubble -->

			</div>
		</div><!-- //.goods_info_pop -->

		<div class="recommend_list" data-ui="TabMenu">
			<div class="tab-menu clear">
				<a data-content="menu" class="active" title="선택됨">MD’s CHOICE</a>
				<a data-content="menu">CATEGORY BEST</a>
			</div>

			<!-- MD’s CHOICE -->
			<div class="tab-content active" data-content="content">
				<div class="nowrap_list">
					<ul class="goodslist" id="mdchoise">
					<!--<li>
							<a href="#">
								<figure>
									<div class="img"><img src="/sinwon/m/static/img/test/@goodslist_01.jpg" alt="상품 이미지"></div>
									<figcaption>
										<p class="name">솔리드 심플 벨티트 자켓 </p>
										<p class="price">￦ 105,800</p>
									</figcaption>
								</figure>
							</a>
						</li>
					-->
					</ul>
				</div>
			</div>
			<!-- //MD’s CHOICE -->

			<!-- CATEGORY BEST -->
			<div class="tab-content" data-content="content">
				<div class="nowrap_list">
					<ul class="goodslist" id="categorybest">
						<!--
						<li>
							<a href="#">
								<figure>
									<div class="img"><img src="/sinwon/m/static/img/test/@goodslist_13.jpg" alt="상품 이미지"></div>
									<figcaption>
										<p class="name">솔리드 심플 벨티트 자켓</p>
										<p class="price">￦ 105,800</p>
									</figcaption>
								</figure>
							</a>
						</li>
						-->
					</ul>
				</div>
			</div>
			<!-- //CATEGORY BEST -->
		</div><!-- //.recommend_list -->

		<div class="goods_order" >
			<div class="bg"></div>
			<div class="contents">
				<div class="option_open">
					<div class="select_shipping" data-ui="TabMenu">
						<button type="button" class="btn_close">닫기</button>
						<div class="wrap_tabmenu">
							<ul class="tab-menu clear">
								<li data-content="menu" class="active" title="선택됨"><label><input type="radio" class="radio_def" id="deliver_type0" name="delivery_type" data-type="0" value="0" checked> 택배발송</label></li>
								<li data-content="menu" class="o2o_select_area"><label><input type="radio" class="radio_def" id="deliver_type1" name="delivery_type" data-type="1" value="1"> 매장픽업</label></li>
								<li data-content="menu" class="o2o_select_area"><label><input type="radio" class="radio_def" id="deliver_type2" name="delivery_type" data-type="3" value="3"> 당일수령</label></li>
							</ul>
							<div class="wrap_bubble shipping_info">
								<div class="btn_bubble"><button type="button" class="btn_help">?</button></div>
								<div class="pop_bubble">
									<div class="inner">
										<button type="button" class="btn_pop_close">닫기</button>
										<div class="container">
											<p class="tit_pop">배송방법 안내</p>
											<table class="tbl_txt">
												<colgroup>
													<col style="width:44px;">
													<col style="width:auto;">
												</colgroup>
												<tbody>
													<tr>
														<th>택배발송:</th>
														<td>택배로 발송하는 기본 배송 서비스</td>
													</tr>
													<tr>
														<th>당일수령:</th>
														<td>당일수령이 가능한 라이더 배송 서비스</td>
													</tr>
													<tr>
														<th>매장픽업:</th>
														<td>원하는 날짜, 원하는 매장에서 상품을 받아가는 맞춤형 배송 서비스</td>
													</tr>
												</tbody>
											</table>
										</div>
									</div>
								</div>
							</div><!-- //.wrap_bubble -->
						</div>
						<!-- 택배발송 -->
						<div class="tab-content active" data-content="content">
							<!-- [D] 내용없음 -->
						</div>
						<!-- //택배발송 -->
						
						<div class="tab-content" data-content="content">
							<dl>
								<dt><span id="mapSelectStoreName1"></span></dt>
								<dd><a href="javascript:;" class="btn_select_store01 btn-basic" onclick="getShopO2O(1, '1', 'M')">매장선택</a></dd>
							</dl>
						</div>
				
			
						<div class="tab-content" data-content="content">
							<dl>
								<dt><span id="mapSelectStoreName2">당일수령은 서울지역만 가능합니다.</span></dt>
								<dd><a href="javascript:;" class="btn_select_store01 btn-basic" onclick="getShopO2O(1, '1', 'M')">매장선택</a></dd>
							</dl>
						</div>
			
						
						<input type="hidden" name="mapSelectStore" id="mapSelectStore">
						
						
						
					</div><!-- //.select_shipping -->

					<div class="select_option">
						<div class="select_c" id="color_code_sel">
							<dl>
								<dt class="colorchip_area">
									<label class="colorchip chip-darkGrey"><input type="radio" name="selectColor" value="dark_grey" checked><span></span></label>
									<label class="colorchip chip-beige light-color"><input type="radio" name="selectColor" value="beige"><span></span></label>
								</dt>
								<dd>GRAY</dd>
							</dl>
						</div>
						<div class="select_s">
							<dl>
								<dd class="size_select" id="opt-zone">
									
								</dd>
							</dl>
						</div>
						<div class="ea_area">
							<div class="ea-select">
								<input type="text" value="1" id="quantity" name="add_quantity[]" readonly="">
								<button class="plus" onclick="product.setQntPlus();"></button>
								<button class="minus" onclick="product.setQntMinus();"></button>
							</div>
							<input type="hidden" id="quantity_max">
							
						</div>
					</div><!-- //.select_option -->

					<div class="total_price">
						<dl>
							<dt>총 합계</dt>
							<dd id="sellprice_txt"></dd>
						</dl>
					</div><!-- //.total_price -->
				</div><!-- //.option_open -->

				<!-- [D] 임직원가 수정(2017-04-24) -->
				<div class="btnset staff"><!-- [D] 임직원 구매인 경우 .staff 클래스 추가 -->
					<ul class="clear">
						<li>
							<a href="javascript:SA_PRODUCT(document.getElementById('quantity').value);//return false;" class="btn_addcart" id="cartBtn">
								<span class="icon_cart_black"></span>장바구니
							</a>
						</li>
						<li >
							<a href="javascript:;" id="like_main wishBtn" class="btn_like" onclick="like.clickLikeM('product','main','<?=$_pdata->prodcode?>')">
								<span class="icon_like"></span>좋아요 <span class="point-color">
									(<span id="like_cnt_main" class="like-cnt-txt">0</span>)
								</span>
							</a>
						</li>
						<li class="btn_ordernow"><a href="#" onMouseDown="SA_PRODUCT(document.getElementById('quantity').value);">바로구매</a></li>
					</ul>
					
					<?if($_ShopInfo->staff_yn=='Y'){?>
					<a href="javascript:;" class="staff_buy">임직원 구매</a>
					<?}?>
					<?if($_ShopInfo->cooper_yn=='Y'){?>
					<a href="javascript:;" class="staff_buy">제휴사가 구매</a>
					<?}?>
				</div><!-- //.btnset -->
				<!-- //[D] 임직원가 수정(2017-04-24) -->
			</div>
		</div><!-- //.goods_order -->

	</section><!-- //.detailpage -->
	
	
	<!-- 상품상세정보 팝업 -->
	<section class="pop_layer layer_goods_detail">
		<div class="inner">
			<h3 class="title">상품상세정보<button type="button" class="btn_close">닫기</button></h3>
			<div id="content_li">
				<!--<img src="/sinwon/m/static/img/test/@goods_detail.jpg" alt="상품상세정보">-->	
			</div>
			<ul class="goods_information" id="jg_content">
			<!---
				<li>
					<div class="type">제품소재</div>
					<div class="content">[겉감]폴리에스터100%/[안감]폴리에스터100%</div>
				</li>
			-->	
			</ul>
		</div>
	</section>
	<!-- //상품상세정보 팝업 -->
	
	<!-- 배송반품 팝업 -->
	<section class="pop_layer layer_goods_delivery">
		<div class="inner">
			<h3 class="title">배송반품<button type="button" class="btn_close">닫기</button></h3>
			<div class="info_txt">
				<ul>
					<li><?=$deliinfohtml?></li>
				</ul>
			</div>
		</div>
	</section>
	<!-- //배송반품 팝업 -->
	
	
	<!-- 당일수령 팝업 -->
	<section class="pop_layer layer_select_store01">
		 
		<? include ($Dir.FrontDir."productdetail_o2o.php");?>
	</section>
	<!-- //당일수령 팝업 -->




</main>
<!-- //내용 -->
<form name='orderfrm' id='orderfrm' method='GET' action='order.php' >
<input type='hidden' name='basketidxs' id='basketidxs' value='' >
<input type='hidden' name='staff_order' id='staff_order' value='' >
<input type='hidden' name='cooper_order' id='cooper_order' value='' >
</form>

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

<?
include_once("outline/footer_m.php");
?>

