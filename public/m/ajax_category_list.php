<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

$code = $_POST["code"];
$category_type = $_POST["category_type"];
$brand_idx=$_POST["brand_idx"];

if($category_type == "second"){
	//두번째 카테고리 조회
	$sub_sql = "SELECT code_a,code_b,code_c,code_d, code_a||code_b||code_c||code_d as cate_code,code_name,idx,cate_sort FROM tblproductcode
	 					WHERE code_a = '".$code."' AND code_b != '000' AND ( group_code !='NO' AND is_hidden = 'N' ) AND display_list is NULL
	 					ORDER BY code_a, code_b, code_c, code_d, cate_sort ASC";
	
	$sub_result = pmysql_query($sub_sql);
	$arrSecondDepthCate = array();  // 2차 카테고리
	while ( $sub_row = pmysql_fetch_array($sub_result) ) {
		if ( $sub_row['code_c'] == "000" ) {
			// 2차 카테고리
			array_push($arrSecondDepthCate, array($sub_row['cate_sort'], $sub_row['code_a'], $sub_row['code_b'], $sub_row['code_c'], $sub_row['code_d'], $sub_row['code_name']));
		} 
	}
	
	pmysql_free_result($sub_result);
	sort($arrSecondDepthCate);
	$secondHtml = "<option value=''>카테고리 All</option>";
	foreach ( $arrSecondDepthCate as $arrCateInfo ) {
		$firstCateCode = $arrCateInfo[1];
		$secondCateCode = $arrCateInfo[2];
		$secondHtml .= "<option value='".$firstCateCode.$secondCateCode."000000'>".$arrCateInfo[5]."</option>";
	}
	
	echo $secondHtml;
}else if($category_type == "third"){
	//세번째 카테고리 조회
	$firstCode = substr($code,0,3);
	$secondCode = substr($code,3,3);
	$thirdCode = substr($code,6,3);
	if($brand_idx){
	$sub_sql = "select * from (SELECT code_a,code_b,code_c,code_d, code_a||code_b||code_c||code_d as cate_code,code_name,idx,cate_sort FROM tblproductcode 
 					WHERE code_a = '".$firstCode."' AND code_b = '".$secondCode."' AND code_c = '".$thirdCode."' AND code_d != '000' AND ( group_code !='NO' AND is_hidden = 'N' ) AND display_list is NULL
 					ORDER BY code_a, code_b, code_c, code_d, cate_sort ASC)v where cate_code in (select cate_code from tblproductbrand_cate pc where pc.cate_code=cate_code and bridx='".$brand_idx."')  order by cate_sort";
	}else{
	$sub_sql = "SELECT code_a,code_b,code_c,code_d, code_a||code_b||code_c||code_d as cate_code,code_name,idx,cate_sort FROM tblproductcode
 					WHERE code_a = '".$firstCode."' AND code_b = '".$secondCode."' AND code_c = '".$thirdCode."' AND code_d != '000' AND ( group_code !='NO' AND is_hidden = 'N' ) AND display_list is NULL
 					ORDER BY code_a, code_b, code_c, code_d, cate_sort ASC";
	}
	$sub_result = pmysql_query($sub_sql);
	while ( $sub_row = pmysql_fetch_array($sub_result) ) {
		$arrThirdDepthCate[] = $sub_row;
	}
	foreach ($arrThirdDepthCate as $arrCateInfo){
		//$thirdHtml .= "<li><a data-catecode='".$arrCateInfo['cate_code']."'  data-name='".$arrCateInfo ['code_name']."' class='third_category_li'>" . $arrCateInfo ['code_name'] . "</a></li>";
		$thirdHtml .= "<li><a href=\"javascript:third_category_li('".$arrCateInfo['cate_code']."','".$arrCateInfo ['code_name']."')\">" . $arrCateInfo ['code_name'] . "</a></li>";
	}
	echo $thirdHtml;
}else{
	//세번째 카테고리 조회
	$firstCode = substr($code,0,3);
	$secondCode = substr($code,3,3);
	$thirdCode = substr($code,6,3);
	$sub_sql = "SELECT code_a,code_b,code_c,code_d, code_a||code_b||code_c||code_d as cate_code,code_name,idx,cate_sort FROM tblproductcode
 					WHERE code_a = '".$firstCode."' AND code_b = '".$secondCode."' AND code_c = '".$thirdCode."' AND code_d != '000' AND ( group_code !='NO' AND is_hidden = 'N' ) AND display_list is NULL
 					ORDER BY code_a, code_b, code_c, code_d, cate_sort ASC";
		
	$sub_result = pmysql_query($sub_sql);
	while ( $sub_row = pmysql_fetch_array($sub_result) ) {
		$arrThirdDepthCate[] = $sub_row;
	}
	$thirdHtml = "<option value=''>선택하세요</option>";
	foreach ($arrThirdDepthCate as $arrCateInfo){
		$thirdHtml .= "<option value='".$arrCateInfo['cate_code']."'>".$arrCateInfo['code_name']."</option>";
	}
	echo $thirdHtml;
}
?>