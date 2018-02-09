<?php
/********************************************************************* 
// 파 일 명		: productlist.php 
// 설     명		: 카테고리 상품 리스트
// 상세설명	: 카테고리별 상품을 리스트로 진열
// 작 성 자		: hspark
// 수 정 자		: 2015.11.03 - 김재수
// 
// 
*********************************************************************/ 
?>
<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/cache_product.php");
include_once($Dir."lib/shopdata.php");
//include_once($Dir."lib/timesale.class.php");

$searchCategory = $_POST['s_search_category'];

include ($Dir.MainDir.$_data->menu_type.".php");

$lnb_flag = 2;
$code=$_REQUEST["code"];
$inCode = $code;
$page_code=$code;
$likestr = $_REQUEST["likestr"];
$bannerImgPath = $Dir.DataDir."shopimages/mainbanner/";
if(ord($code)==0 && $code != "000000000000") {
	Header("Location:/");
	exit;
}
//$timesale=new TIMESALE();

//$selfcodefont_start = "<font class=\"prselfcode\">"; //진열코드 폰트 시작
//$selfcodefont_end = "</font>"; //진열코드 폰트 끝
list($code_a,$code_b,$code_c,$code_d) = sscanf($code,'%3s%3s%3s%3s');
if(strlen($code_a)!=3) $code_a="000";
if(strlen($code_b)!=3) $code_b="000";
if(strlen($code_c)!=3) $code_c="000";
if(strlen($code_d)!=3) $code_d="000";

$code=$code_a.$code_b.$code_c.$code_d;

$likecode=$code_a;
if($code_b!="000") $likecode.=$code_b;
if($code_c!="000") $likecode.=$code_c;
if($code_d!="000") $likecode.=$code_d;

$_cdata="";
$sql = "SELECT * FROM tblproductcode WHERE code_a='{$code_a}' AND code_b='{$code_b}' ";
$sql.= "AND code_c='{$code_c}' AND code_d='{$code_d}' order by cate_sort";
$result=pmysql_query($sql,get_db_conn());
if($row=pmysql_fetch_object($result)) {

	$listname = $row->code_name;
	//접근가능권한그룹 체크
	if($row->group_code=="NO") {

		echo "<html></head><body onload=\"location.href='".$Dir.MainDir."main.php'\"></body></html>";exit;
	}
	if(strlen($_ShopInfo->getMemid())==0) {
		if(ord($row->group_code)) {
			echo "<html></head><body onload=\"location.href='".$Dir.FrontDir."login.php?chUrl=".getUrl()."'\"></body></html>";exit;
		}
	} else {
		if($row->group_code!="ALL" && ord($row->group_code) && $row->group_code!=$_ShopInfo->getMemgroup()) {
			alert_go('해당 카테고리 접근권한이 없습니다.',$Dir.MainDir."main.php");
		}
	}
	$_cdata=$row;
} else {
	echo "<html></head><body onload=\"location.href='".$Dir.MainDir."main.php'\"></body></html>";exit;
}
pmysql_free_result($result);

//$sort = "best";
$sort = "recent";

if($listnum<=0) $listnum=20;
if($brand_idx) list($brand_name)=pmysql_fetch("select brandname from tblproductbrand where bridx='".$brand_idx."'");
?>

<SCRIPT LANGUAGE="JavaScript">
<!--

function GoPage(block,gotopage) {
	document.form2.block.value=block;
	document.form2.gotopage.value=gotopage;
	localStorage.setItem("gotopage",gotopage);
	fnSubmitProductList();

// 	ga ( 'send', 'event', 'paging', gotopage);
	ga ( 'send', 'event', 'paging', 'click', 'procutlist',gotopage);
}


function ChangeSort(val) {
	document.form2.block.value="";
	document.form2.gotopage.value="";
	document.form2.sort.value=val;
	fnSubmitProductList();
}

function ChangeProdView(val) {
	document.form2.block.value="";
	document.form2.gotopage.value="";
	document.form2.listnum.value=val;
	fnSubmitProductList();
}

function list_cut(type){
	document.form2.list_type.value=type;
	fnSubmitProductList();
}


// 장바구니 
function basket(productcode){
	var frm = document.frmBasket;
	frm.code.value = productcode.substr( 0, 12 );
	frm.productcode.value = productcode;

	$.ajax({
		type: "POST",
		url: "../front/confirm_basket_proc.php",
		data: $('#productbasket'+productcode).serialize(),
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

			$.ajax({
				type: "POST",
				url: "../front/confirm_basket.php",
				data: $('#productbasket'+productcode).serialize(),
				async: false
			}).done(function ( msg ) {
				if(confirm(msg)){
					location.replace("basket.php");
				}else{
					return false;
				}
			});
		}
	});
}

function list_ajax(){
	$.ajax({
		cache: false,
		type: 'POST',
		url: 'productlist_ajax.php',
		data: $("#form2").serialize(),
                timeout: 100000,
		success: function(data) {
			var arrTmp = data.split("||");
			$(".goods-list-ajax").html(arrTmp[0]);
			$(".total-ea").html("<strong>"+arrTmp[1]+"</strong> items");
			window.scrollTo(0,0);	// 페이지 상단이동
		}
	});

}

//-->
</SCRIPT>
<script type="text/javascript" src="../static/js/product.js"></script>

<?php
	include($Dir.TempletDir."product/list_{$_cdata->list_type}.php");
?>


<form name=frmBasket method=post id='ID_frmBasket'>
	<input type=hidden name=option1 value='1'>
	<input type=hidden name=quantity value='1'>
	<input type=hidden name=code value="">
	<input type=hidden name=productcode value="">
	<input type=hidden name=ordertype>
	<input type=hidden name=opts>
</form>

<form name=form2 id="form2" method=get action="<?=$_SERVER['PHP_SELF']?>" class="formProdList">
	<input type=hidden 		name=code 								value="<?=$code?>">
	<input type=hidden 		name=listnum 							value="<?=$listnum?>">
	<input type=hidden 		name=sort 								value="<?=$sort?>">
	<input type=hidden 		name=block 								value="<?=$block?>">
	<input type=hidden 		name=gotopage 							value="<?=$gotopage?>">
	<input type="hidden" 	name="brand" id="brand" 				value="">
	<input type="hidden" 	name="cateChk" id="cateChk"			 	value="<?=$cateChk?>" />
	<input type="hidden" 	name="color" id="color" 				value="">
	<input type="hidden" 	name="size" id="size" 					value="">
	<input type="hidden" 	name="soldout" id="soldout" 			value="<?=$soldout?>">
	<input type="hidden" 	name="price_start" id="price_start" 	value="" >
	<input type="hidden" 	name="price_end" id="price_end" 		value="" >
	<input type="hidden" 	name="view_type" id="view_type" 		value="<?=$view_type?>" >
	<input type="hidden" 	name="list_type" id="list_type" 		value="four" >
	<input type="hidden" 	name="brand_idx" id="brand_idx" 		value="<?=$brand_idx?>" >
</form>


<script>
$(document).ready(function(){
	var code	= "<?=$code?>";
	if(code=="001004002005"){ // code=="001004002005" 2017-09-11수정
		$("#sortlist option:eq(1)").prop("selected", true);
		$("#form2").find("input[name=sort]").val('recent');
	} else {
		$("#sortlist option:eq(0)").prop("selected", true);
	}
	//list_ajax();
	var gotopage = localStorage.getItem("gotopage");
	var detailpage = localStorage.getItem("detailpage");
	if(detailpage == 'Y'){
		localStorage.setItem("detailpage",'N');
		GoPage('',gotopage);
		return;
	}
	localStorage.setItem("detailpage",'N');
	GoPage('',1);	
});
</script>

<div id="create_openwin" style="display:none"></div>

<?php  include ($Dir."lib/bottom.php") ?>
</BODY>
</HTML>
<?php  if($HTML_CACHE_EVENT=="OK") ob_end_flush(); ?>
