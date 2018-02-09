<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

$mem_id         = $_ShopInfo->getMemid();

$search         = $_REQUEST['sm_search'] ?: $_REQUEST['search'];
$search         = trim($search);    // 앞뒤 빈공간 제거

$searchTitle    = htmlspecialchars($search);  // 화면에 보여줄 검색어

$search         = str_replace("'", "''", $search);  // for query

$mem_id         = $_ShopInfo->getMemid();
$listnum        = $_REQUEST['listnum'] ?: "10";

#sort 정리
$s_sort = $_POST[s_sort]?$_POST['s_sort']:"recent";

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
$addQuery[] = "a.display = 'Y' ";
$addQuery[] = "a.hotdealyn = 'N' ";
$addQuery[] = "(  a.mall_type = 0 OR a.mall_type = '".$_ShopInfo->getAffiliateType()."' ) "; // 해당 몰관련 상품만 보여줌 (2015.11.10 - 김재수)

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
if($_REQUEST['reSearch']){
	$checked['reSearch'] = "checked";
	$addQuery[] = "(".str_replace('WHERE ','',str_replace('\\','', htmlspecialchars_decode($_REQUEST['addwhere']) )).")";
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
        OR lower(a.sizecd) LIKE '%{$searchWord}%'
        OR lower(a.productcode) LIKE '{$searchWord}%'
        OR lower(a.prodcode) LIKE '{$searchWord}%'";

 if ( count($arrSearchBrand) > 0 ) {
        $sword_search .= " OR a.brand in ( " . implode(",", $arrSearchBrand) . " ) ";
    }
    $sword_search .= ")";

	$addQuery[] = $sword_search;
}

if(!$search){
	echo "||0";
	exit;
}

#검색 리스트
if(count($addQuery) > 0){
	$loopSearchData = array();

	include 'productlist_common.php';

	$strAddQuery = "WHERE ".implode(" AND ", $addQuery);
	$qry.="AND a.prodcode in (select prodcode from tblproduct where quantity > 0 AND soldout != 'Y' group by prodcode) ";

	$tmp_sort=explode("_",$s_sort);

	$sql = "SELECT a.productcode, a.productname, a.overseas_type, a.buyprice, a.keyword, a.mdcomment, a.sellprice, a.quantity, a.reserve, a.reservetype, a.production, a.option1, a.option2, a.option_quantity, a.mdcomment, a.review_cnt, a.maximage, a.minimage, a.tinyimage, a.over_minimage, a.etctype, a.option_price, a.consumerprice, a.tag, a.selfcode, (a.consumerprice - a.sellprice) as diffprice, a.brand, a.soldout, a.icon, a.color_code, a.sizecd, a.prodcode, a.modifydate, a.pridx, ";
	if($tmp_sort[0]=="best")$sql.=" COALESCE(qty,0) AS qty, ";

	$sql.= " COALESCE((select COUNT( tl.hott_code )AS hott_cnt from tblhott_like tl WHERE tl.section = 'product' AND a.productcode = tl.hott_code),0) AS hott_cnt, li.section, ";
	$sql.= " COALESCE(re.marks,0) AS marks, COALESCE(re.marks_total_cnt,0) AS marks_total_cnt ";
	$sql.= "FROM (select *, case when (buyprice - sellprice) <= 0 then 0 else (buyprice - sellprice) end as saleprice from tblproduct) AS a  ";

	$sql.= "LEFT OUTER JOIN tblproductgroupcode b ON a.productcode=b.productcode ";
	$sql.= "LEFT JOIN (SELECT productcode, sum(marks) as marks,count(productcode) as marks_total_cnt FROM tblproductreview group by productcode) re on a.productcode = re.productcode ";
	$sql.= "LEFT JOIN (SELECT hott_code, section FROM tblhott_like WHERE section = 'product' AND like_id = '".$_ShopInfo->getMemid()."' GROUP BY hott_code, section ) li on a.productcode = li.hott_code  ";
	if($tmp_sort[0]=="best"){
		$sql.= "LEFT JOIN
                    (
                        select op.productcode, sum(op.option_quantity) as qty
                        from tblorderproduct op
                        join	tblproductlink pl on op.productcode = pl.c_productcode 
                        where op.ordercode >= '".date("Ymd",strtotime('-1 month'))."000000' and op.ordercode <= '".date("Ymd")."235959'
                        group by op.productcode
                        order by op.productcode
                    ) bt on a.productcode = bt.productcode
                ";
	}


	$sql.= $strAddQuery." ".$qry;

	if($tmp_sort[0]=="recent"){
		$sort_sql= " ORDER BY modifydate desc, pridx desc ";
		$sortname="신상품";
	}else if($tmp_sort[0]=="best"){
		$sort_sql= " ORDER BY qty desc, pridx desc ";
		$sort_fild=", max(qty) as qty ";
		$sortname="인기순";
	}else if($tmp_sort[0]=="marks"){
		$sort_sql= " ORDER BY COALESCE(marks, 0) desc, pridx desc ";
		$sortname="상품평순";
	}else if($tmp_sort[0]=="like"){
		$sort_sql= " ORDER BY hott_cnt desc, pridx desc ";
		$sortname="좋아요순";
	}else if($tmp_sort[0]=="price"){ 
		$sort_sql= " ORDER BY sellprice ".$tmp_sort[1]." ";
		$sortname="가격순";
	}
	$sql_paging="select prodcode from (".$sql.") tz group by prodcode";

	$paging = new New_Templet_mobile_paging($sql_paging,5,$listnum,'GoPage',true);
	$t_count = $paging->t_count;
	$gotopage = $paging->gotopage;

	$sql = "select  prodcode, min(sellprice) as sellprice, min(consumerprice) as consumerprice, sum(hott_cnt) as hott_cnt, min(minimage) as minimage, min(productcode) as productcode, min(productname) as productname, min(sizecd) as sizecd, min(section) as section, min(icon) as icon, max(modifydate) as modifydate, max(pridx) as pridx, max(marks) as marks, max(brand) as brand, max(tinyimage) as tinyimage ".$sort_fild." from (".$sql.") tz group by prodcode ".$sort_sql;

	$sql = $paging->getSql($sql);
 	//exdebug($sql);

    $list_array = productlist_print( $sql, $type = 'MO_001', array(), $listnum );

    // 재검색용 쿼리
    $strAddQuery = htmlspecialchars($strAddQuery);
} else {
	$t_count = 0;
}

?>
<ul class="goodslist">
<?
foreach ( $list_array as $listKey => $listVal ) {
	echo $listVal;
}
?>
</ul>
<!-- 페이징 -->
<div class="list-paginate mt-10 mb-30">
	<?echo $paging->a_prev_page.$paging->print_page.$paging->a_next_page;?>
</div>
<!-- // 페이징 -->
||<?=$t_count?>||<?=$strAddQuery?>