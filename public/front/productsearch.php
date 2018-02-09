<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
include_once ($Dir."lib/product.class.php");

$search         = $_REQUEST['sm_search'] ?: $_REQUEST['search'];
$search         = trim($search);    // 앞뒤 빈공간 제거
$searchTitle    = htmlspecialchars($search);  // 화면에 보여줄 검색어
$search         = str_replace("'", "''", $search);  // for query
$listnum		= $_REQUEST['listnum'] ?: "20";
$sort = "recent";	// 2016-10-07 기본값을 recent로 수정

// ================================================================================
// WHERE절에 필요한 내용들
// ================================================================================
$addQuery = array();
$addQuery[] = "a.display = 'Y' ";
$addQuery[] = "a.hotdealyn = 'N' ";

// ================================================================
// 승인대기중인 브랜드에 속한 상품은 리스트에서 제외처리
// ================================================================
$sub_sql = "SELECT b.bridx FROM tblvenderinfo a JOIN tblproductbrand b ON a.vender = b.vender WHERE a.delflag='N' AND a.disabled='1' ";
$sub_result = pmysql_query($sub_sql);

$arrNotAllowedBrandList = array();
while ( $sub_row = pmysql_fetch_object($sub_result) ) {
    array_push($arrNotAllowedBrandList, $sub_row->bridx);
}
pmysql_free_result($sub_result);

if ( count($arrNotAllowedBrandList) >= 1 ) {
    $addQuery[] = "a.brand not in ( " . implode(",", $arrNotAllowedBrandList) . " ) ";
}

// 이게 결과 내 재검색인가보다;;;;
// echo "reSearch [".$_POST['reSearch']."]";
if($_POST['reSearch']){
	$checked['reSearch'] = "checked";
	$researh_qry = "(".str_replace('WHERE ','',str_replace('\\','', htmlspecialchars_decode($_POST['addwhere']) )).")";
// 	echo "researh_qry [".$researh_qry."]";
	$addQuery[] = $researh_qry;
}

if($search){
	$searchWord = strtolower($search);
    $searchWord = str_replace("'", "''", $searchWord);

    // 브랜드 검색
    $subsql = "SELECT bridx FROM tblproductbrand WHERE lower(brandname) like '%{$searchWord}%' OR lower(brandname2) like '%{$searchWord}%' OR lower(brandtag) like '%{$searchWord}%' ";
    $subresult = pmysql_query($subsql);

    $arrSearchBrand = array();
    while ( $subrow = pmysql_fetch_object($subresult) ) {
        if ( $subrow->bridx ) {
            array_push($arrSearchBrand, $subrow->bridx);
        }
    }
    pmysql_free_result($subresult);

	$sword_search = "(
        lower(a.productname) LIKE '%{$searchWord}%'
        OR lower(a.keyword) LIKE '%{$searchWord}%'
        OR lower(a.mdcomment) LIKE '%{$searchWord}%'
        OR lower(a.productcode) LIKE '{$searchWord}%'
        OR lower(a.prodcode) LIKE '{$searchWord}%'";

    if ( count($arrSearchBrand) > 0 ) {
        $sword_search .= " OR a.brand in ( " . implode(",", $arrSearchBrand) . " ) ";
    }

    $sword_search .= ")";

	$addQuery[] = $sword_search;
}

// 검색조건 공통 (productlist.php, productsearch.php)
include 'productlist_common.php';

#검색 리스트
if(count($addQuery) > 0){
	$strDefAddQuery = "WHERE ".implode(" AND ", $addQuery);
	$strAddQuery = $strDefAddQuery;
    // 재검색용 쿼리
    $strAddQuery = htmlspecialchars($strAddQuery);
} else {
	$t_count = 0;
}
?>

<?php include ($Dir.MainDir.$_data->menu_type.".php");?>

<script type="text/javascript">

function GoPage(block,gotopage) {
	document.formSearchHidden.block.value=block;
	document.formSearchHidden.gotopage.value=gotopage;
	localStorage.setItem("gotopage",gotopage);
	fnSubmitProductList();
	//document.formSearch.submit();
}

function ChangeSort(sort){
	var frm = document.formSearchHidden;
	frm.block.value = '';
	frm.gotopage.value = '';
	frm.sort.value = sort;
	fnSubmitProductList();
//	frm.submit();
}
function ChangeProdView(val){
	var frm = document.formSearchHidden;
	frm.block.value = '';
	frm.gotopage.value = '';
	frm.listnum.value = val;
	fnSubmitProductList();
	//frm.submit();
}
function list_cut(type){
	document.formSearchHidden.list_type.value=type;
	fnSubmitProductList();
}
function list_ajax(){
	$.ajax({
		cache: false,
		type: 'POST',
		url: 'productsearch_ajax.php',
		data: $("#formSearchHidden").serialize(),
		success: function(data) {
			var arrTmp = data.split("||");

			$(".goods-list-ajax").html(arrTmp[0]);
			$(".total-ea").html("<strong>"+arrTmp[1]+"</strong> items");
			$(".top_total").html("총 "+arrTmp[1]+"개");
			window.scrollTo(0,0);	// 페이지 상단이동
		}
	});
}
function GoSearch(){

	var frm = document.formSearch;
	var frmH = document.formSearchHidden;

	if ( $("#sm_search").val().trim() === "" ) {
		alert("검색어를 입력해주세요.");
		$("#sm_search").val("").focus();
		return false;
	}
	
	$("#sm_search_ajax").val($("#sm_search").val());
	
	if($("input:checkbox[id='re-search']").is(":checked") !== false) {
		$("#reSearch_ajax").val($("#reSearch").val());
	}
	
	/*
	if($("input:checkbox[id='re-search']").is(":checked") == false) {
		frm.brand.value = '';
		frm.sel_cate_code.value = '';
	}
	frm.block.value = '';
	frm.gotopage.value = '';
	*/
	frm.submit();
}

</script>
<script type="text/javascript" src="../static/js/product.js"></script>
<?
//echo "dd";
//echo $_data->design_search;
//echo $qqqq_sql;
include($Dir.TempletDir."search/search{$_data->design_search}.php");
?>

<form name="formSearchHidden" id="formSearchHidden" method="POST" class="mt-20 formProdList">
	<input type=hidden 		name=block 								value="<?=$block?>">
	<input type=hidden 		name=gotopage 							value="<?=$gotopage?>">
	<input type=hidden 		name=listnum 							value="<?=$listnum?>">
	<input type=hidden 		name=sort 								value="<?=$sort?>">
	<input type=hidden 		name=addwhere 							value = "<?=$strAddQuery?>">
	<input type=hidden 		name=brand id="brand"					value = "">
	<input type="hidden" 	name="color" id="color" 				value="">
	<input type="hidden" 	name="size" id="size" 					value="">
	<input type="hidden" 	name="price_start" id="price_start" 	value="" >
	<input type="hidden" 	name="price_end" id="price_end" 		value="" >
	<input type="hidden" 	name="view_type" id="view_type" 		value="<?=$view_type?>" >
	<input type="hidden" 	name="list_type" id="list_type" 		value="four" >
	<input type="hidden" 	name="sm_search_ajax" id="sm_search_ajax" 	value="<?=$searchTitle?>" >
	<input type="hidden" 	name="reSearch_ajax" id="reSearch_ajax" 	value="<?=$_POST['reSearch']?>" >
</form>

<script>

$(document).ready(function(){
// 	list_ajax();
	var skey="<?=$searchWord?>"; //마케팅 스크립트2017-09-12
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
//카테고리 관련 javascript end
</script>


<?php
include ($Dir."lib/bottom.php")
?>
</BODY>
</HTML>
