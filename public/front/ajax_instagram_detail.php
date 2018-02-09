<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

$idx = $_POST["idx"];

$delivery_addr = array(); // 배송지 정보

$sql = "SELECT  i.*, li.section,
							 	COALESCE((select COUNT( tl.hott_code )AS hott_cnt from tblhott_like tl WHERE tl.section = 'instagram' AND i.idx::varchar = tl.hott_code),0) AS hott_cnt
							FROM tblinstagram i
							LEFT JOIN ( SELECT hott_code, section FROM tblhott_like WHERE section = 'instagram' AND like_id = '".$_ShopInfo->getMemid()."' GROUP BY hott_code, section ) li on i.idx::varchar = li.hott_code
							WHERE i.display = 'Y' "; 
$sql .= "AND i.idx = '{$idx}' ";
$result = pmysql_query( $sql, get_db_conn() );

//이전 정보
$prev_sql =  "SELECT  i.* FROM tblinstagram i WHERE i.display = 'Y' ";
$prev_sql .= "AND i.idx > '{$idx}' ORDER BY regdt ASC limit 1";
$prev_result = pmysql_query( $prev_sql, get_db_conn() );
$prev_row = pmysql_fetch_object( $prev_result );


//다음 정보
$next_sql =  "SELECT  i.* FROM tblinstagram i WHERE i.display = 'Y' ";
$next_sql .= "AND i.idx < '{$idx}' ORDER BY regdt DESC limit 1";
$next_result = pmysql_query( $next_sql, get_db_conn() );
$next_row = pmysql_fetch_object( $next_result );

$prod_image = "";
$prod_brand = "";
$prod_name = "";
while( $row = pmysql_fetch_object( $result ) ){
	$arrProductCode = explode(",",$row->relation_product);
	
	//상품코드로 상품정보 조회
	foreach ($arrProductCode as $key =>$val){
		$prod_sql = "SELECT productcode, productname, brand, minimage FROM tblproduct WHERE display = 'Y' ";
		$prod_sql .= "AND productcode = '{$val}' ";
		$prod_result = pmysql_query( $prod_sql, get_db_conn() );
		
		while( $prod_row = pmysql_fetch_object( $prod_result ) ){
			$prod_name .= $prod_row->productname.",";
			$prod_brand .= brand_name($prod_row->brand).",";
			$prod_image .= $prod_row->minimage.",";
		
		}
	}
	
	// HTML 이미지 제거 및 치환 (2016.11.02 - peter.Kim)
	$instaRow_content = stripslashes($row->content);
	
	//<img>태그 제거
	$instaRow_content	 = preg_replace("/<img[^>]+\>/i", "", $instaRow_content);

	// <br>태그 제거
	$arrList = array("/<br\/>/", "/<br>/");
	$instaRow_content_tmp = trim(preg_replace($arrList, "", $instaRow_content));

	if ( !empty($instaRow_content_tmp) ) {
			$instaRow_content	= str_replace("<p>","<div>",$instaRow_content);
			$instaRow_content	= str_replace("</p>","</div>",$instaRow_content);
	}
	
	$instagram_info[] = array( 
            'idx'      => $row->idx, 
			'title'      => $row->title,
            'content'      => $instaRow_content,
			'link_url'      => $row->link_url,
			'link_m_url'      => $row->link_m_url,
			'productcode'      => $row->productcode,
			'img_file'      => $row->img_file,
			'img_rfile'      => $row->img_rfile,
			'img_m_file'      => $row->img_m_file,
			'access'      => $row->access,
			'regdt'      => $row->regdt,
			'hash_tags'      => $row->hash_tags,
			'hott_cnt'  => $row->hott_cnt,
			'section'  => $row->section,
			'relation_product'  => $row->relation_product,
			'productname'  => substr( $prod_name, 0, (strlen($prod_name)-1) ),
			'brandname' => substr( $prod_brand, 0, (strlen($prod_brand)-1) ),
			'brandimage' => substr( $prod_image, 0, (strlen($prod_image)-1) ),
			'pre_idx'  => $prev_row->idx ? : "",
			'next_idx'  => $next_row->idx ? : "",
	);
}

echo json_encode( $instagram_info );

?>


