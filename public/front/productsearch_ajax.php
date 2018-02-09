<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/cache_product.php");
include_once($Dir."lib/shopdata.php");

$mem_id         = $_ShopInfo->getMemid();

$search         = $_REQUEST['sm_search_ajax'] ?: $_REQUEST['search'];
$search         = trim($search);    // 앞뒤 빈공간 제거
$list_type=$_POST["list_type"];

$searchTitle    = htmlspecialchars($search);  // 화면에 보여줄 검색어

$search         = str_replace("'", "''", $search);  // for query

$thr			= $_REQUEST['thr'];
$mem_id			= $_ShopInfo->getMemid();
$listnum		= $_REQUEST['listnum'] ?: "20";

$sel_cate_code	= $_REQUEST['sel_cate_code'];
// $brand			= $_REQUEST['brand'];

$sort=$_REQUEST["sort"];
if(!$sort || $sort == undefined){
	$sort = "recent";	// 2016-10-07 기본값을 recent로 수정
}

// 검색어에서 blank제거하고 소문자로 변경
$tmpSearch = trim(strtolower($search));
$packedSearchWord = str_replace(" ", "", $tmpSearch);

if ( !empty($packedSearchWord) && !empty($mem_id) ) {
	$sql  = "INSERT INTO tblmykeyword (id, keyword, packedKeyword, regdate) VALUES ";
	$sql .= "('" . $mem_id . "', '" . $search . "', '" . $packedSearchWord . "', now()) ";
	$result = pmysql_query($sql, get_db_conn());
}

// ================================================================================
// WHERE절에 필요한 내용들
// ================================================================================

$addQuery = array();

if(!$search){
	echo "||0";
	exit;
}
// ================================================================
// 승인대기중인 브랜드에 속한 상품은 리스트에서 제외처리
// ================================================================

// 검색조건 공통 (productlist.php, productsearch.php)
include 'productlist_common.php';

#검색 리스트
if($_POST['addwhere']){
	$strDefAddQuery = $_POST['addwhere'];
	$strAddQuery = $strDefAddQuery;

	$qry.="AND a.prodcode in (select prodcode from tblproduct where quantity > 0 AND soldout != 'Y' group by prodcode) ";


	//$tmp_sort=explode("_",$s_sort);
	$tmp_sort=explode("_",$sort);

	$sql = "SELECT a.productcode, a.productname, a.overseas_type, a.buyprice, a.keyword, a.mdcomment, a.sellprice, a.quantity, a.reserve, a.reservetype, a.production, a.option1, a.option2, a.option_quantity, a.mdcomment, a.review_cnt, a.maximage, a.minimage, a.tinyimage, a.over_minimage, a.etctype, a.option_price, a.consumerprice, a.tag, a.selfcode, (a.consumerprice - a.sellprice) as diffprice, a.brand, a.soldout, a.icon, a.start_no, a.modifydate, a.vcnt, a.pridx, ";
	$sql .= "a.relation_tag, a.prodcode, a.colorcode, a.sizecd, a.brandcd, a.brandcdnm, a.color_code, ";
	$sql .= "COALESCE(re.marks,0) AS marks, COALESCE(re.marks_total_cnt,0) AS marks_total_cnt
			, COALESCE((select COUNT( tl.hott_code )AS hott_cnt from tblhott_like tl WHERE tl.section = 'product' and hott_code!='' AND a.prodcode = tl.hott_code),0) AS hott_cnt, li.section ";

	$sql.= "FROM (select *, case when (buyprice - sellprice) <= 0 then 0 else (buyprice - sellprice) end as saleprice from tblproduct) AS a  ";

	$sql.= "LEFT OUTER JOIN tblproductgroupcode b ON a.productcode=b.productcode ";

	$sql.= "LEFT JOIN (SELECT productcode, sum(marks) as marks,
							count(productcode) as marks_total_cnt
				FROM tblproductreview group by productcode) re on a.productcode = re.productcode ";
	$sql .= "LEFT JOIN ( SELECT hott_code, section ,COUNT( hott_code )AS hott_cnt FROM tblhott_like WHERE section = 'product' AND like_id = '".$_ShopInfo->getMemid()."' and hott_code!='' GROUP BY hott_code, section ) li on a.prodcode = li.hott_code ";

	$def_sql= $sql.$strDefAddQuery." ";
	$sql .= "LEFT JOIN tblproductlink d ON a.productcode = d.c_productcode ";
	$sql.= $strAddQuery." ";
	
	// filter 검색조건 쿼리 추가 위민트 170201
 	$sql.= $qry." ";

	$sort_fild="";
	if($tmp_sort[0]=="recent"){ 		$sort_sql= " ORDER BY modifydate desc, pridx desc ";}
	elseif($tmp_sort[0]=="best"){	$sort_sql= " ORDER BY vcnt desc, pridx desc";}
	elseif($tmp_sort[0]=="marks"){	$sort_sql= " ORDER BY marks desc, pridx desc";}
	elseif($tmp_sort[0]=="like"){	$sort_sql= " ORDER BY hott_cnt desc, pridx desc";}
	elseif($tmp_sort[0]=="price"){ 	$sort_sql= " ORDER BY sellprice ".$tmp_sort[1]." ";}

	//검색시 전체 상품수 및 카테고리별 상품수를 구한다.
	$search_all_count	= getCateProductCnt($def_sql);
	//exdebug($search_all_count);
	$all_t_count	= $search_all_count['all_t_count'];	// 통합 전체 상품수
	$all_c_count	= $search_all_count['all_c_count'];	// 통합 카테고리별 상품수
	
	$sql = "select  prodcode, min(sellprice) as sellprice, min(consumerprice) as consumerprice, sum(hott_cnt) as hott_cnt, min(minimage) as minimage, min(productcode) as productcode, min(productname) as productname, min(sizecd) as sizecd, min(section) as section, min(icon) as icon, max(modifydate) as modifydate, max(pridx) as pridx, max(marks) as marks, max(vcnt) as vcnt, max(brand) as brand, max(tinyimage) as tinyimage ".$sort_fild." from (".$sql.") tz group by prodcode ".$sort_sql;

	$paging = new New_Templet_paging($sql,10,$listnum,'GoPage',true);
	$t_count = $paging->t_count;
	$gotopage = $paging->gotopage;

	$sql = $paging->getSql($sql);

    $list_array = productlist_print( $sql, $type = 'S_001', array(), $listnum );

    // 재검색용 쿼리
    $strAddQuery = htmlspecialchars($strAddQuery);
} else {
	$t_count = 0;
}
?>
<ul class="goods-list <?=$list_type?> clear">
	<?
	//상품리스트
	//외주 하드 코딩 아자샵 솔루션 타입으로 변경 2017-02-16
	foreach( $list_array as $listKey=>$listVal ){
		echo $listVal;
	}
	?>
</ul><!-- //.goods-list -->
<div class="list-paginate">
	<?php
		if( $t_count >= 1 ){
			echo $paging->a_prev_page.$paging->print_page.$paging->a_next_page;
		}
	?>
</div><!-- //.list-paginate -->
||<?=$t_count?>