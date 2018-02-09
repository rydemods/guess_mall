<?php
/********************************************************************* 
// 파 일 명		: productlist_common.php 
// 설     명		: 검색조건 공통 (productlist.php, productsearch.php)
// 상세설명	: productlist.php, productsearch.php 상품 목록 조회시 검색조건 query 
// 작 성 자		: 2016.02.01 - 위민트
// 
// 
*********************************************************************/ 
?>

<?php 

/****************************************
 * FILTER : BRAND
 ****************************************/
//브랜드별 검색

$brand = $_REQUEST ["brand_idx"]?$_REQUEST ["brand_idx"]:$_REQUEST['brand'];
// echo "brand [".$brand."]";
$arrBrand = explode(",", $brand);
// print_r($arrBrand);

if($brand){
	foreach($arrBrand as $i => $v){
		$checked ['brand'] [$v] = "checked";
		if($i == 0){
			$qry.= " AND (a.brand = '".$v."'";
		}else{
			$qry.= " OR a.brand = '".$v."'";
		}
	}
	$qry.=")";
}

/****************************************
 * FILTER : SIZE
 ****************************************/
//사이즈 검색
$param_size = $_REQUEST['size'];
$arrSize = explode(",", $param_size);
//exdebug($brand);

$size_where="";
if($param_size){
	foreach($arrSize as $i => $v){
		foreach($product_size[$v] as $ps => $psv){
			$size_where[]= "a.sizecd LIKE '%".$psv."%'";
		}
	}
	$qry.=" AND (".implode(" OR ",$size_where).")";
}

/****************************************
 * FILTER : COLOR
 ****************************************/
//색상별 검색
$color_name = $_REQUEST['color'];
$arrColor = explode(",", $color_name);
if($color_name){
	foreach($arrColor as $i => $v){
		if($i == 0){
			$qry.= " AND (a.color_code = '".$v."'";
		}else{
			$qry.= " OR a.color_code = '".$v."'";
		}
	}
	$qry.=")";
	//$prod_sql .= " AND a.color_code = '".$color_name."'";
}

/****************************************
 * FILTER : 금액
 ****************************************/
$price_start = $_REQUEST['price_start'];
$price_start = preg_replace("/[^0-9]*/s", "", $price_start);
$price_end = $_REQUEST['price_end'];
$price_end = preg_replace("/[^0-9]*/s", "", $price_end);
//if($price_start>0 && $price_end>0){
if($price_end>0){
	$qry .= "AND (a.sellprice >= ".$price_start." AND a.sellprice <= ".$price_end.")";
}

$view_type = $_REQUEST['view_type'];
?>