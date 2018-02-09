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

$cateChk = substr($_REQUEST["cateChk"],0,strlen($_REQUEST["cateChk"])-1);

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

$thisCate = getDecoCodeLoc( $code );

?>
<?php
	//메뉴 및 리스트를 불러온다.
    if ( strlen($code_a) == 3 && $code_b == "000" ) {
        // 대카테고리
        include($Dir.TempletDir."product/main_list_{$_cdata->list_type}.php");
    } else {
        include($Dir.TempletDir."product/list_{$_cdata->list_type}.php");
    }
?>
<form name=frmBasket method=post id='ID_frmBasket'>
	<input type=hidden name=option1 value='1'>
	<input type=hidden name=quantity value='1'>
	<input type=hidden name=code value="">
	<input type=hidden name=productcode value="">
	<input type=hidden name=ordertype>
	<input type=hidden name=opts>
</form>
<form name=form2 method=get action="<?=$_SERVER['PHP_SELF']?>">
<input type=hidden name=code value="<?=$code?>">
<input type=hidden name=listnum value="<?=$listnum?>">
<input type=hidden name=block value="<?=$block?>">
<input type=hidden name=gotopage value="<?=$gotopage?>">
<input type=hidden name=item_cate value="<?=$item_cate?>">
<input type=hidden name=brand value="<?=$brand?>">
<input type="hidden" name="likestr" value="<?=$likestr?>">
<input type="hidden" name="cateChk" />
<input type="hidden" name="brandChk" />
</form>
<script type="text/javascript">
var brandCode = [];
var productCode = $("input[name='code']").val();
var category = $("input[name='category']").val();

$(document).ready(function() {
	$(".btn-submit").click(function() {
		var sortval = $("#sortlist option:selected").val();
		if(brandCode != ""){
			//배열에 code가 있는 경우 삭제
			var codeSize = brandCode.length;
			brandCode.splice(0,codeSize);
		}			
		$("input[name=brandchk]:checked").each(function() {
			brandCode.push($(this).attr("ids"));
		});
		if(typeof category == "undefined"){
			$.ajax({
				type: "POST",
				url: "ajax_product_list.php",
				data: "code="+productCode+"&brandcode="+brandCode+"&catetype=main&sort="+sortval,
				dataType:"html"
			}).done(function(html){
				$(".goods-list-item").html(html);
			});
		}else{
			$.ajax({
				type: "POST",
				url: "ajax_product_list.php",
				data: "code="+productCode+"&brandcode="+brandCode+"&catetype=list&category="+category+"&sort="+sortval,
				dataType:"html"
			}).done(function(html){
				$(".goods-list-item").html(html);
			});		
		}	
	});

	$(".btn-like").click(function() {
		var productCode = $(this).attr("ids");
		var likeType = $(this).attr("type");
		var memId = "<?=$_ShopInfo->getMemid()?>";

		//
		if(memId != ""){		
			$.ajax({
				type: "POST",
				url: "product_like_proc.php",
				data: "code="+productCode+"&liketype="+likeType+"&section=product",
				dataType:"JSON"
			}).done(function(data){
				$("#like_count_"+productCode).html("<strong>좋아요</strong>"+data[0]['hott_cnt']);
				if(data[0]['section'] == "product"){
					$("#like_"+productCode).attr("class","comp-like btn-like on");
					$("#like_"+productCode).attr("type","on");
				}else{
					$("#like_"+productCode).attr("class","comp-like btn-like");
					$("#like_"+productCode).attr("type","off");
				}			
			});
		}else{
			//로그인 상태가 아닐때 로그인 페이지로 이동
			var url = "../front/login.php?chUrl=/";
			$(location).attr('href',url);
		}		
	});
});

//상품정렬순 조회
function sortList(sort){
	if(brandCode != ""){
		//배열에 code가 있는 경우 삭제
		var codeSize = brandCode.length;
		brandCode.splice(0,codeSize);
	}			
	$("input[name=brandchk]:checked").each(function() {
		brandCode.push($(this).attr("ids"));
	});

	if(typeof category == "undefined"){
		$.ajax({
			type: "POST",
			url: "ajax_product_list.php",
			data: "code="+productCode+"&brandcode="+brandCode+"&catetype=main&sort="+sort,
			dataType:"html"
		}).done(function(html){
			$(".goods-list-item").html(html);
			$(".comp-goods .color-thumb").each(function(i) {
				
				var $ui = $(this);
				var $list = $ui.find("ul");
				var listTotal = $list.children().length;
				var viewNum = 3;
				var isControl = (listTotal > viewNum) ? true : false;
				
				if (!$list.closest(".bx-viewport")[0]) $list.bxSlider({ slideWidth:62, maxSlides:viewNum, pager:false, controls:isControl });
				
			});
			
		});
	}else{
		$.ajax({
			type: "POST",
			url: "ajax_product_list.php",
			data: "code="+productCode+"&brandcode="+brandCode+"&catetype=list&category="+category+"&sort="+sort,
			dataType:"html"
		}).done(function(html){
			$(".goods-list-item").html(html);
			$(".comp-goods .color-thumb").each(function(i) {
				
				var $ui = $(this);
				var $list = $ui.find("ul");
				var listTotal = $list.children().length;
				var viewNum = 3;
				var isControl = (listTotal > viewNum) ? true : false;
				
				if (!$list.closest(".bx-viewport")[0]) $list.bxSlider({ slideWidth:62, maxSlides:viewNum, pager:false, controls:isControl });
				
			});
		});
	}		
}

</script>
<?
	if($biz[bizNumber]){
?>
<script type="text/javascript">
	_TRK_PI = "PLV";
</script>
<?
	}
?>


<div id="create_openwin" style="display:none"></div>

<?php  include ($Dir."lib/bottom.php") ?>
</BODY>
</HTML>
<?php  if($HTML_CACHE_EVENT=="OK") ob_end_flush(); ?>
