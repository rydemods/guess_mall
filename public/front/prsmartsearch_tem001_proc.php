<?php 
header("Content-Type: text/plain");
header("Content-Type: text/html; charset=euc-kr");
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
foreach($_POST as $k => $v){
	if(is_array($v)){
		$_POST[$k] = array_filter($v);
	}
}
#sleep('4');
$searchCategory = $_POST['smart_search_category'];
$searchBrand = $_POST['smart_search_brand'];
$searchMinPrice = $_POST['smart_search_min_price'];
$searchMaxPrice = $_POST['smart_search_max_price'];
$searchColor = $_POST['smart_search_color'];

$searchStr = array();
$addQuery = array();
if(count($searchCategory) > 0){
	$searchStr[] = "Category";
	$addQuery[] = "b.c_category LIKE '".max($searchCategory)."%'";
}
if(count($searchBrand) > 0){
	$searchStr[] = "Brand";
	$addQuery[] = "a.brand IN ('".implode("', '", $searchBrand)."')";
}

$searchStr[] = "Price";
$addQuery[] = "a.sellprice between '".$searchMinPrice."' and '".$searchMaxPrice."'";

if($searchColor){
	$searchStr[] = "Color";
	$arrSearchColor = explode(",", $searchColor);
	$arrSearchColorParts = array();
	foreach($arrSearchColor as $v){
		if(!strlen($v)) continue;
		$arrSearchColorParts[] = "a.color_code like '%".$v."%'";
	}
	$addQuery[] = "(".implode(" or ", $arrSearchColorParts).")";
}

$loopSearchData = array();
if($addQuery) $strAddQuery = "WHERE ".implode(" AND ", $addQuery);

$sql = "
			SELECT 
				productcode, 
				productname, 
				sellprice, 
				tinyimage
			FROM 
				tblproduct a 
				LEFT JOIN tblproductlink b on a.productcode = b.c_productcode 
			".$strAddQuery."
			GROUP BY 
				productcode, productname, sellprice, tinyimage
			ORDER BY 
				productcode
			LIMIT 50 OFFSET 0";
$result=pmysql_query($sql, get_db_conn());
while($row=pmysql_fetch_object($result)) {
	$loopSearchData[] = $row;
}

?>
<dt><span class="goods"><?=implode(", ", $searchStr)?></span> 검색결과 <span class="num"><?=number_format(count($loopSearchData))?></span>건</dt>

<?
	foreach($loopSearchData as $v){
		if (strlen($v->tinyimage)>0 && file_exists($Dir.DataDir."shopimages/product/".$v->tinyimage)) {
			$imgsrc = $Dir.DataDir."shopimages/product/".urlencode($v->tinyimage);
		}else{
			$imgsrc = $Dir."images/no_img.gif";
		}
?>
		<dd>
			<ul class="find_result_list">
				<li class="goods_pic"><a href="../front/productdetail.php?productcode=<?=$v->productcode?>"><img src="<?=$imgsrc?>" alt="" width = '63'/></a></li>
				<li class="goods_nm">
					<p class="nm"><a href="../front/productdetail.php?productcode=<?=$v->productcode?>"><?=$v->productname?></a></p>
					<p class="price"><?=number_format($v->sellprice)?>원</p>
				</li>
			</ul>
		</dd>
<?
	}
?>
<!--
<?if(count($loopSearchData) > 0){?>
<?}else{?>
	<dd>
		<ul class="find_result_list">
			<li class="goods_nm">
				<p class="nm"><b>검색 조건을 입력해주세요.</b></p>
			</li>
		</ul>
	</dd>
<?}?>
-->