<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

$category_type = $_POST["category_type"];
$type = $_POST["type"];
$pr_link = $Dir.'m/productdetail.php?productcode=';

if($category_type == "best"){
	$category_type = 1;
}else if($category_type == "md"){
	$category_type = 2;
}else if($category_type == "new"){
	$category_type = 3;
}

$imagepath = $Dir.DataDir."shopimages/product/";

$sql = "SELECT a.list_idx,a.sort, c.productcode,c.productname,c.brand,
			c.sellprice ,c.over_minimage, c.quantity,c.display,c.hotdealyn 
			FROM tblproduct_mainitem_list a 
			JOIN tblproduct c ON a.pridx=c.pridx 
			WHERE a.category_type = ".$category_type." AND section = 'B' AND c.display = 'Y'  AND c.hotdealyn = 'N' 
			ORDER BY sort ASC ";
// exdebug($sql);
$result = pmysql_query($sql);
while ( $row = pmysql_fetch_array($result) ) {
	$nowCateList[] = $row;
}	
if(count($nowCateList) > 0){
	foreach( $nowCateList as $key=>$val ){
		if($type == "pc"){
?>

<li>
	<div class="goods-item">
		<div class="thumb-img">
			<a href="javascript:prod_detail('<?=$val['productcode'] ?>');">
				<img src="<?=$imagepath.$val['over_minimage'] ?>" alt="상품 썸네일"></a>
			<div class="layer">
				<div class="btn">
					<button type="button" class="btn-preview">
						<span><i class="icon-cart">장바구니</i></span>
					</button>
					<button type="button">
						<span><i class="icon-like">좋아요</i></span><span>11</span>
					</button>
					<!-- [D] 좋아요 선택시 .on 처리 -->
				</div>
				<div class="opt">
					<span>55</span> <span>66</span> <span>77</span>
				</div>
			</div>
		</div>
		<!-- //.thumb-img -->
		<div class="price-box">
			<div class="brand-nm"><?=brand_name($val['brand'])?></div>
			<div class="goods-nm"><?=$val['productname']?></div>
			<div class="price">\105,800</div>
		</div>
	</div>
	<!-- //.goods-item -->
</li>
		<?}else if($type == "mobile"){?>
			<li><a href="<?=$pr_link.$val['productcode'] ?>"><img src="<?=$imagepath.$val['over_minimage'] ?>" alt="<?=$val['productname']?>" /></a></li>
		<?} ?>	
	<?} ?>	
<?}else{ ?>
	<li class="ta-c none">관련 상품이 없습니다.</li>
<?} ?>

