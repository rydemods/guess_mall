<?php 

/*********************************************************************
 // 파 일 명		: sql_tem_top001.php
 // 설     명		: 상단 header query
 // 상세설명		: 브랜드, 카테고리 정보 조회
 // 작 성 자		: 2017.01.20 - 위민트
 // 수 정 자		:
 //
 *********************************************************************/
$banner_imagepath = $Dir.DataDir."shopimages/mainbanner/";
$prod_imagepath = $Dir.DataDir."shopimages/product/";

/*********************************************************************
 * 브랜드 캐시여부확인
 *********************************************************************/
//브랜드 캐시를 따라갈페이지 이외에 페이지는 세션 초기화
$brand_session_page = array("brand_main","openguide","brand_qna","productlist","productdetail","lookbook_list","ecatalog_list", "brand_store","lookbook_view");

$page_name=reset(explode(".php",end(explode("/",$_SERVER["PHP_SELF"]))));

$page_in_check=in_array($page_name,$brand_session_page);
if($_GET[bridx] && $page_in_check){
	brand_session($_GET[bridx]);
}else{
	if(!$page_in_check) brand_session("","1");
}

$brand_idx=$_SESSION[brand_session_no];

/*********************************************************************
 * 배너
 *********************************************************************/
// 배너 조회
function fnGetBanner($banner_no, $limit_cnt){
	
	if($limit_cnt == null)		$limit_cnt = 1;
	
	$sql  = "SELECT * FROM tblmainbannerimg "; 
	$sql .= "WHERE banner_hidden = 1 AND banner_no = '".$banner_no."' AND banner_type = 0 ";
	$sql.= "AND banner_start_date < to_char( now() + interval '1 hour', 'YYYYMMDDHH24' )";
	$sql.= "AND banner_end_date > to_char( now() + interval '1 hour', 'YYYYMMDDHH24' )";
	$sql .= "ORDER BY banner_sort asc, no desc limit ".$limit_cnt;
// 	echo "fnGetBanner sql [".$sql."]";
	$result = pmysql_query($sql);
	return $result;
}

// 카테고리별 배너
function fnGetCategoryBanner($banner_category){
	$sql  = "select * from tblmainbannerimg ";
	$sql .= "where 1=1 ";
	$sql .= "and banner_hidden = 1 ";
	$sql .= "and banner_no = 113 ";
	$sql .= "and banner_category = '".$banner_category."' ";
	$sql.= "AND banner_start_date < to_char( now() + interval '1 hour', 'YYYYMMDDHH24' )";
	$sql.= "AND banner_end_date > to_char( now() + interval '1 hour', 'YYYYMMDDHH24' )";
	$sql .= "ORDER BY banner_sort limit 1";
	$result = pmysql_query($sql);
	$row = pmysql_fetch_array($result);
	return $row;
}

// 배너별 상품 조회
function fnGetBannerProduct($tblmainbannerimg_no, $limit=""){
	global $_ShopInfo;
	$sql  = "SELECT tc.*, COALESCE((select COUNT( tl.hott_code )AS hott_cnt from tblhott_like tl WHERE tl.section = 'product' AND tc.prodcode = tl.hott_code),0) AS hott_cnt ";
	$sql .= "FROM (SELECT ";
	$sql .= "ta.tblmainbannerimg_no, ";
	$sql .= "tb.*, li.section ";
	$sql .= "FROM ";
	$sql .= "tblmainbannerimg_product ta ";
	$sql .= "LEFT OUTER JOIN tblproduct tb ON ta.productcode = tb.productcode ";
	$sql .= "LEFT JOIN ( SELECT hott_code, section FROM tblhott_like WHERE section = 'product' AND like_id = '".$_ShopInfo->getMemid()."' and hott_code != '' GROUP BY hott_code, section ) li ON tb.prodcode = li.hott_code ";
	$sql .= "WHERE ";
	$sql .= "tblmainbannerimg_no = '".$tblmainbannerimg_no."' ";
	$sql .= "and tb.productcode is not null) tc ";
	if($limit){
		$sql .= " limit ".$limit;
	}
// 	echo "fnGetBannerProduct [".$sql."]";
	
	return $sql;
}

// 최상단
$topTitle_banner_img_result = fnGetBanner(119, 1);
$topTitle_banner_img_row = pmysql_fetch_array($topTitle_banner_img_result);
$topTitleBannerImg	= getProductImage($banner_imagepath, $topTitle_banner_img_row['banner_img']);

// 상단 롤링 배너
$top_banner_img_result = fnGetBanner(77, 7);
while ( $row = pmysql_fetch_array($top_banner_img_result) ) {
	$arrMainImgBanner[] = $row;
}

// 상단 배너 롤링 좌측
$top_left_banner_img_result = fnGetBanner(85, 2);
while ( $row = pmysql_fetch_array($top_left_banner_img_result) ) {
	$MiddleTopBannerImg[] = $row;
}
/*
$top_left_banner_img_result = fnGetBanner(85, 2);
$top_left_banner_img_row = pmysql_fetch_array($top_left_banner_img_result);
$topLeftBannerImg	= getProductImage($banner_imagepath, $top_left_banner_img_row['banner_img']);

// 상단 배너 롤링 우측
$top_right_banner_img_result = fnGetBanner(99, 1);
$top_right_banner_img_row = pmysql_fetch_array($top_right_banner_img_result);
$topRightBannerImg	= getProductImage($banner_imagepath, $top_right_banner_img_row['banner_img']);
*/
// 상단 상품
$top_product_result = fnGetBanner(78, 1);
$top_product_row = pmysql_fetch_array($top_product_result);
$top_product_no = $top_product_row['no'];
// echo "top_product_no [".$top_product_no."]";
$top_product_list_qry = fnGetBannerProduct($top_product_no);
$top_product_list_array = productlist_print( $top_product_list_qry, $type = 'M_001', array(), null, null, $code );
$top_product_list_result = pmysql_query($top_product_list_qry);
$top_product_list_mo_array = productlist_print( $top_product_list_qry, $type = 'MO_001', array(), null, null, $code );


// 중단
$middle_banner_img_result = fnGetBanner(120, 1);
$middle_banner_img_row = pmysql_fetch_array($middle_banner_img_result);
$middleBannerImg	= getProductImage($banner_imagepath, $middle_banner_img_row['banner_img']);
$middleBannerImg_m	= getProductImage($banner_imagepath, $middle_banner_img_row['banner_img_m']);

// 브랜드 
$brand_banner_result = fnGetBanner(87, 7);
while ( $row = pmysql_fetch_array($brand_banner_result) ) {
	$brand_banner_list[] = $row;
}

// Look 배너
$look_banner_result = fnGetBanner(88, 1);
$look_banner_row = pmysql_fetch_array($look_banner_result);
$lookBannerImg	= getProductImage($banner_imagepath, $look_banner_row['banner_img']);
// Look 상품
$look_banner_no = $look_banner_row['no']; 
$look_banner_product_qry = fnGetBannerProduct($look_banner_no, "3");
$look_banner_product_array = productlist_print( $look_banner_product_qry, $type = 'M_003', array(), null, null, $code );
$look_banner_product_result = pmysql_query($look_banner_product_qry);

$banner_count=pmysql_num_rows($look_banner_product_result);	// 메인페이지 랭킹 2~3개 노출 용 파라미터
// $look_banner_product_mo_array = productlist_print( $look_banner_product_qry, $type = 'MO_003', array(), null, null, $code );
if ($banner_count == 2){
	$look_banner_product_mo_array = productlist_print( $look_banner_product_qry, $type = 'MO_003', array(), null, null, $code );
} else if ($banner_count == 3) {
	$look_banner_product_mo_array = productlist_print( $look_banner_product_qry, $type = 'MO_004', array(), null, null, $code );
}

// 하단 배너
$bottom_banner_img_result = fnGetBanner(118, 3);

/*********************************************************************
 * 브랜드
 *********************************************************************/
// 상품브랜드 정보
$sql_tblproductbrand_list  = "SELECT bridx, brandname, logo_img, brandname2, brandtag FROM tblproductbrand ";
$sql_tblproductbrand_list .= "WHERE 1=1 ";
$sql_tblproductbrand_list .= "AND display_yn = 1 ";
$sql_tblproductbrand_list .= "order by brand_sort asc ";
//$sql_tblproductbrand_list .= "LIMIT 6";
// echo $sql_tblproductbrand_list;
$brand_result = pmysql_query($sql_tblproductbrand_list,get_db_conn());
while ( $row = pmysql_fetch_array($brand_result) ) {
	$brand_list[] = $row;
}
if($brand_idx){
list($brand_logo)=pmysql_fetch("select logo_img from tblproductbrand where bridx='".$brand_idx."'");
}

/*********************************************************************
 * 카테고리
 *********************************************************************/
$sql_shop_menu_list  = "SELECT code_a, code_b, code_c, code_d, code_a||code_b||code_c||code_d as cate_code, code_name, idx FROM tblproductcode ";
$sql_shop_menu_list .= "WHERE 1=1 ";
$sql_shop_menu_list .= "and type = 'L' ";
$sql_shop_menu_list .= "and is_hidden = 'N' ";
$sql_shop_menu_list .= "ORDER BY cate_sort";
$shop_menu_result = pmysql_query($sql_shop_menu_list,get_db_conn());
while ( $row = pmysql_fetch_array($shop_menu_result) ) {
	$shop_menu_list[] = $row;
}

// 1차 카테고리 (여성, 남성...)
$sql_dept1_list  = "SELECT code_a, code_b, code_c, code_d, code_a||code_b||code_c||code_d as cate_code, code_name, idx FROM tblproductcode ";
$sql_dept1_list .= "WHERE 1=1 ";
$sql_dept1_list .= "and code_a = '001' ";
$sql_dept1_list .= "and code_c = '000' ";
$sql_dept1_list .= "and type = 'LM' ";
$sql_dept1_list .= "and is_hidden = 'N' ";
$sql_dept1_list .= "ORDER BY cate_sort";

// 2차 카테고리
$sql_dept2_list  = "SELECT code_a, code_b, code_c, code_d, code_a||code_b||code_c||code_d as cate_code, code_name, idx FROM tblproductcode ";
$sql_dept2_list .= "WHERE 1=1 ";
$sql_dept2_list .= "and code_a = '001' ";
$sql_dept2_list .= "and code_b = '[param_dept_b]' ";
$sql_dept2_list .= "and code_c != '000' ";
$sql_dept2_list .= "and code_d = '000' ";
$sql_dept2_list .= "and is_hidden = 'N' ";
$sql_dept2_list .= "and display_list is NULL ";
$sql_dept2_list .= "ORDER BY cate_sort ";

// 3차 카테고리
$sql_dept3_list  = "SELECT code_a, code_b, code_c, code_d, code_a||code_b||code_c||code_d as cate_code, code_name, idx FROM tblproductcode ";
$sql_dept3_list .= "WHERE 1=1 ";
$sql_dept3_list .= "and code_a = '001' ";
$sql_dept3_list .= "and code_b = '[param_dept_b]' ";
$sql_dept3_list .= "and code_c = '[param_dept_c]' ";
$sql_dept3_list .= "and type = 'LMX' ";
$sql_dept3_list .= "and is_hidden = 'N' ";
$sql_dept3_list .= "and display_list is NULL ";
$sql_dept3_list .= "ORDER BY cate_sort ";

// 1차 카테고리 (아울렛)
$sql_dept1_out_list  = "SELECT code_a, code_b, code_c, code_d, code_a||code_b||code_c||code_d as cate_code, code_name, idx FROM tblproductcode ";
$sql_dept1_out_list .= "WHERE 1=1 ";
$sql_dept1_out_list .= "and code_a = '002' ";
$sql_dept1_out_list .= "and code_c = '000' ";
$sql_dept1_out_list .= "and type = 'LM' ";
$sql_dept1_out_list .= "and is_hidden = 'N' ";
$sql_dept1_out_list .= "ORDER BY cate_sort";

// 2차 카테고리
$sql_dept2_out_list  = "SELECT code_a, code_b, code_c, code_d, code_a||code_b||code_c||code_d as cate_code, code_name, idx FROM tblproductcode ";
$sql_dept2_out_list .= "WHERE 1=1 ";
$sql_dept2_out_list .= "and code_a = '002' ";
//$sql_dept2_out_list .= "and code_b = '[param_dept_out_b]' ";
$sql_dept2_out_list .= "and code_b = '[param_dept_b]' ";
$sql_dept2_out_list .= "and code_c != '000' ";
$sql_dept2_out_list .= "and code_d = '000' ";
$sql_dept2_out_list .= "and is_hidden = 'N' ";
$sql_dept2_out_list .= "and display_list is NULL ";
$sql_dept2_out_list .= "ORDER BY cate_sort ";

// 3차 카테고리
$sql_dept3_out_list  = "SELECT code_a, code_b, code_c, code_d, code_a||code_b||code_c||code_d as cate_code, code_name, idx FROM tblproductcode ";
$sql_dept3_out_list .= "WHERE 1=1 ";
$sql_dept3_out_list .= "and code_a = '002' ";
//$sql_dept3_out_list .= "and code_b = '[param_dept_out_b]' ";
//$sql_dept3_out_list .= "and code_c = '[param_dept_out_c]' ";
$sql_dept3_out_list .= "and code_b = '[param_dept_b]' ";
$sql_dept3_out_list .= "and code_c = '[param_dept_c]' ";
$sql_dept3_out_list .= "and type = 'LMX' ";
$sql_dept3_out_list .= "and is_hidden = 'N' ";
$sql_dept3_out_list .= "and display_list is NULL ";
$sql_dept3_out_list .= "ORDER BY cate_sort ";



/*********************************************************************
 * 검색어
 *********************************************************************/
$sql_mykeyword  = "SELECT * FROM tblmykeyword ";
$sql_mykeyword .= "WHERE id = '" . $_ShopInfo->getMemid() . "' ";
$sql_mykeyword .= "ORDER BY regdate desc ";
$sql_mykeyword .= "LIMIT 9";

?>