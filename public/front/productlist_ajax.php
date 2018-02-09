<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/cache_product.php");
include_once($Dir."lib/shopdata.php");

$code=$_REQUEST["code"];
$list_type=$_POST["list_type"];

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
	$_cdata=$row;
}
pmysql_free_result($result);

// 품절상품 제외 조건위해 추가..2016-10-10
$soldout = $_REQUEST['soldout'];
$selected[soldout][$soldout]  = 'checked';

$sort=$_REQUEST["sort"];
if(!$sort || $sort == undefined){
	$sort = ($code=="001004002005")?"recent":"best";	// 2016-10-07 기본값을 recent로 수정
}


$listnum=(int)$_REQUEST["listnum"];
$brand_idx=(int)$_REQUEST["brand_idx"];

if($listnum<=0) $listnum=20;

$qry = 'WHERE 1=1 ';

$qry.="AND a.display='Y' AND a.hotdealyn='N' ";
$qry.="AND a.prodcode in (select prodcode from tblproduct where quantity > 0 AND soldout != 'Y' group by prodcode) ";

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
    $qry .= "AND a.brand not in ( " . implode(",", $arrNotAllowedBrandList) . " ) ";
}


/****************************************
 * FILTER : TYPE
 * type 검색조건은 상품리스트에서만 사용 위민트 170203
 ****************************************/
$cateChk = $_REQUEST["cateChk"];
$arrCate = explode(",", $cateChk);
$productlink_cate_qry = "";
if($cateChk){
	foreach($arrCate as $i => $v){
		if($i == 0){
			$productlink_cate_qry.= "AND ( pl.c_category = '".$v."'";
		}else{
			$productlink_cate_qry.= " OR pl.c_category = '".$v."'";
		}
	}
	$productlink_cate_qry.=")";
}

// 검색조건 공통 (productlist.php, productsearch.php)
include 'productlist_common.php';


if($brand_idx)	$qry.= " AND a.brand='".$brand_idx."' ";

if($_data->ETCTYPE["CODEYES"]!="N") {
	$cateList_sql = "SELECT code_a,code_b,code_c,code_d,code_name FROM tblproductcode WHERE code_a='{$code_a}' AND code_b!='000' AND group_code!='NO' ORDER BY cate_sort ASC";
	$cateList_res = pmysql_query($cateList_sql , get_db_conn());
	while($cateList_row = pmysql_fetch_array($cateList_res)){
		$cateList[$cateList_row[code_b]][] = $cateList_row;
	}
	pmysql_free_result($cateList_res);
}

$menu_type2 = "Y";
$chk_layer=0;


if($_cdata->islist=="Y"){

	// list count 위민트 170203
	$sql = "SELECT a.productcode AS dis, * FROM tblproduct AS a ";
	if($cateChk){
		$sql.= "JOIN ( SELECT c_productcode FROM tblproductlink pl WHERE 1=1 ";
		// 카테고리 멀티 처리 위민트 170203
		if($productlink_cate_qry){
			$sql.= $productlink_cate_qry." ";
		}
		$sql.= "GROUP BY c_productcode ) AS link ";
	}else{
		$sql.= "JOIN ( SELECT c_productcode FROM tblproductlink WHERE c_category LIKE '".$likecode."%' GROUP BY c_productcode ) AS link ";
	}
	$sql.= "on( a.productcode=link.c_productcode ) ";

	$sql.= $qry." ";

	if(strlen($not_qry)>0) {
		$sql.= $not_qry." ";
	}
	
	//$listnum
	
	$sql="select prodcode from (".$sql.") tz group by prodcode";
	$paging = new New_Templet_paging($sql,10,$listnum,'GoPage',true);
	$t_count = $paging->t_count;
	$gotopage = $paging->gotopage;
	
	//번호, 사진, 상품명, 제조사, 가격
	$tmp_sort=explode("_",$sort);
	if($tmp_sort[0]=="reserve") {
		$addsortsql=",CASE WHEN a.reservetype='N' THEN CAST(a.reserve AS FLOAT)*1 ELSE CAST(a.reserve AS FLOAT)*a.sellprice*0.01 END AS reservesort ";
	}
	
	$sql = "SELECT a.productcode, a.productname, a.overseas_type, a.buyprice, a.keyword, a.mdcomment, a.sellprice, a.quantity, a.reserve, a.reservetype, a.production, a.option1, a.option2, a.option_quantity, a.review_cnt, a.regdate ,a.modifydate, a.pridx, a.season,";
	if($_cdata->sort=="date2") $sql.="CASE WHEN a.quantity<=0 THEN '11111111111111' ELSE a.date END as date, ";
	$sql.= "a.maximage, a.minimage,a.tinyimage, a.over_minimage, a.etctype, a.option_price, a.consumerprice, a.tag, a.selfcode, a.brand, a.icon, a.soldout, a.prodcode, a.colorcode, a.sizecd 
			,COALESCE(re.marks,0) AS marks, COALESCE(re.marks_total_cnt,0) AS marks_total_cnt
			,COALESCE((select COUNT( tl.hott_code )AS hott_cnt from tblhott_like tl WHERE tl.section = 'product' and hott_code!='' AND a.prodcode = tl.hott_code),0) AS hott_cnt, li.section, a.color_code ";
	if($tmp_sort[0]=="best")$sql.=",COALESCE(qty,0) AS qty ";
	$sql.= $addsortsql;
	
	$sql.= "FROM (select *, case when (consumerprice - sellprice) <= 0 then 0 else (consumerprice - sellprice) end as saleprice from tblproduct) AS a  ";

	if($cateChk){
		$sql.= "JOIN ( SELECT c_productcode FROM tblproductlink pl WHERE 1=1 ";
		// 카테고리 멀티 처리 위민트 170203
		if($productlink_cate_qry){
			$sql.= $productlink_cate_qry." ";
		}
		$sql.= "GROUP BY c_productcode ) AS link ";
	}else{
		$sql.= "JOIN ( SELECT c_productcode FROM tblproductlink WHERE c_category LIKE '".$likecode."%' GROUP BY c_productcode ) AS link ";
	}
	
	$sql.= "on( a.productcode=link.c_productcode ) ";
	$sql.= "LEFT JOIN (SELECT productcode, sum(quality+3) as marks,
								count(productcode) as marks_total_cnt
					FROM tblproductreview group by productcode) re on a.productcode = re.productcode ";
	$sql .= "LEFT JOIN ( SELECT hott_code, section ,COUNT( hott_code )AS hott_cnt FROM tblhott_like WHERE section = 'product' AND like_id = '".$_ShopInfo->getMemid()."' and hott_code!='' GROUP BY hott_code, section ) li on a.prodcode = li.hott_code ";

    // 베스트로 정렬일 경우 최근 한달 판매수량 기준으로 정렬하기 위해 추가.2016-06-02 jhjeong
    if($tmp_sort[0]=="best"){
	        $sql.= "left join 
                    (
                        select op.productcode, sum(op.option_quantity) as qty
                        from tblorderproduct op 
                        join	tblproductlink pl on op.productcode = pl.c_productcode ";
	        // 카테고리 멀티 처리 위민트 170203
			if($cateChk){
				if($productlink_cate_qry){
					$sql.= $productlink_cate_qry." ";
				}
			}else{
				$sql.= "and pl.c_category LIKE '".$likecode."%' ";
			}
			$sql.= "where op.ordercode >= '".date("Ymd",strtotime('-1 month'))."000000' and op.ordercode <= '".date("Ymd")."235959' 
                        group by op.productcode 
                        order by op.productcode 
                    ) bt on a.productcode = bt.productcode 
                ";
    }
    

	$sql.= $qry." ";
	if(strlen($not_qry)>0) {
		$sql.= $not_qry." ";
	}

	$sort_fild="";
//	if($tmp_sort[0]=="recent"){ 		$sort_sql= " ORDER BY modifydate desc, pridx desc ";}
	if($tmp_sort[0]=="recent"){ 		$sort_sql= " ORDER BY regdate desc, pridx desc ";}
	 
	elseif($tmp_sort[0]=="best"){	$sort_sql= " ORDER BY qty desc, pridx desc "; $sort_fild=", max(qty) as qty ";}
	elseif($tmp_sort[0]=="marks"){	$sort_sql= " ORDER BY marks desc, pridx desc";}
	elseif($tmp_sort[0]=="like"){	$sort_sql= " ORDER BY hott_cnt desc, pridx desc";}
	elseif($tmp_sort[0]=="price"){ 	$sort_sql= " ORDER BY sellprice ".$tmp_sort[1]." ";}

	$sql = "select  prodcode, min(sellprice) as sellprice, min(consumerprice) as consumerprice, sum(hott_cnt) as hott_cnt, min(minimage) as minimage, min(productcode) as productcode, min(productname) as productname, min(sizecd) as sizecd, min(section) as section, min(icon) as icon, max(regdate ) as regdate ,max(modifydate) as modifydate, max(pridx) as pridx, max(marks) as marks, max(brand) as brand, max(season) as season, max(tinyimage) as tinyimage ".$sort_fild." from (".$sql.") tz group by prodcode ".$sort_sql;

	if($_SERVER[REMOTE_ADDR]=="218.234.32.103"){
 		//exdebug($sql);
	}

	$sql = $paging->getSql($sql);

	$total_cnt = $paging->t_count;

	$list_array = productlist_print( $sql, $type = 'S_001', array(), null, null, $code );

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
		if( $total_cnt >= 1 ){
			echo $paging->a_prev_page.$paging->print_page.$paging->a_next_page;
		}
	?>
</div><!-- //.list-paginate -->
||<?=$total_cnt?>