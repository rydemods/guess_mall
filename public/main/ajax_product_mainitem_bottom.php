<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

$category_type = 0;
$type = $_POST["type"];
$pr_link = $Dir.'m/productdetail.php?productcode=';

$imagepath = $Dir.DataDir."shopimages/product/";

$sql = "SELECT a.list_idx,a.sort, c.productcode,c.productname,c.brand,
			c.sellprice ,c.tinyimage,c.maximage,c.minimage,c.quantity,c.display, c.consumerprice,c.hotdealyn,li.section,
			TRUNC(5.00 * re.marks / (re.marks_total_cnt * 5),1) as marks_ever_cnt,
			COALESCE((select COUNT( tl.hott_code )AS hott_cnt from tblhott_like tl WHERE tl.section = 'product' AND c.productcode = tl.hott_code),0) AS hott_cnt,
			COALESCE(re.marks_total_cnt,0) as marks_total_cnt
			FROM tblproduct_mainitem_list a
			JOIN tblproduct c ON a.pridx=c.pridx
			LEFT JOIN (SELECT productcode,
			sum(marks) as marks,
			count(productcode) as marks_total_cnt
			FROM tblproductreview group by productcode) re on c.productcode = re.productcode
			LEFT JOIN ( SELECT hott_code, section ,COUNT( hott_code )AS hott_cnt FROM tblhott_like WHERE section = 'product' AND like_id = '".$_ShopInfo->getMemid()."' GROUP BY hott_code, section ) li on c.productcode = li.hott_code
			WHERE a.category_type = ".$category_type." AND a.section = 'C' AND c.display = 'Y' AND c.hotdealyn = 'N'
			ORDER BY sort ASC ";
$result = pmysql_query($sql);
// exdebug($sql);
while ( $row = pmysql_fetch_array($result) ) {
	$nowCateList[] = $row;
}
if(count($nowCateList) > 0){
	foreach( $nowCateList as $key=>$val ){
		$marks = substr($val['marks_ever_cnt'],0,1)  * 20;
		if($type == "pc"){
?>
			<li>
				<figure>
					<a href="javascript:prod_detail('<?=$val['productcode'] ?>');"><img src="<?=$imagepath.$val['minimage'] ?>" alt=""></a>
					<figcaption>
						<a href="javascript:prod_detail('<?=$val['productcode'] ?>')">
							<strong class="brand"><?=brand_name($val['brand'])?></strong>
							<p class="title"><?=$val['productname'] ?></p>
							 <?if($val['consumerprice'] != $val['sellprice']){ ?>
                             <span class="price"><del><?=number_format($val['consumerprice']) ?></del>  <strong><?=number_format($val['sellprice']) ?></strong></span>
                             <?}else{ ?>
                             <span class="price"><strong><?=number_format($val['consumerprice']) ?></strong></span>
                             <?} ?>
							<div class="star">
								<span class="comp-star star-score"><strong style="width:<?=$marks?>%;"></strong></span>(<?=$val['marks_total_cnt']?>)
								<?if($val['section']){ ?>
								<button class="comp-like btn-like like_p<?=$val['productcode']?> on" onclick="detailSaveLike('<?=$val['productcode']?>','on','product','<?=$_ShopInfo->getMemid()?>','<?=$val['brand'] ?>')" id="likehott_<?=$val['productcode']?>" title="선택됨"><span class="like_pcount_<?=$val['productcode']?>"><strong>좋아요</strong><?=$val['hott_cnt'] ?></span></button>
								<?}else{ ?>
								<button class="comp-like btn-like like_p<?=$val['productcode']?>" onclick="detailSaveLike('<?=$val['productcode']?>','off','product','<?=$_ShopInfo->getMemid()?>','<?=$val['brand'] ?>')" id="likehott_<?=$val['productcode']?>" title="선택 안됨"><span class="like_pcount_<?=$val['productcode']?>"><strong>좋아요</strong><?=$val['hott_cnt'] ?></span></button>
								<?} ?>
							</div>
						</a>

					</figcaption>
				</figure>
			</li>
		<?}else if($type == "mobile"){ ?>
			<li class="grid-item">
				<figure>
					<a href="<?=$pr_link.$val['productcode'] ?>"><img src="<?=$imagepath.$val['minimage'] ?>" alt=""></a>
					<figcaption>
						<a href="<?=$pr_link.$val['productcode'] ?>">
							<p class="title">
								<strong class="brand">[<?=brand_name($val['brand'])?>]</strong>
								<span class="name"><?=$val['productname'] ?></span>
							</p>
							 <?if($val['consumerprice'] != $val['sellprice']){ ?>
							 <span class="price"><del><?=number_format($val['consumerprice']) ?></del>  <strong><?=number_format($val['sellprice']) ?></strong></span>
							 <?}else{ ?>
							 <span class="price"><strong><?=number_format($val['consumerprice']) ?></strong></span>
							 <?} ?>
							 <div class="star"><span class="comp-star star-score"><strong style="width:<?=$marks?>%;"></strong></span>(<?=$val['marks_total_cnt']?>)</div>
						 </a>
						 <?if($val['section']){ ?>
						<button class="comp-like btn-like like_p<?=$val['productcode']?> on" onclick="detailSaveLike('<?=$val['productcode']?>','on','product','<?=$_ShopInfo->getMemid()?>','<?=$val['brand'] ?>')" id="likehott_<?=$val['productcode']?>" title="선택됨"><span class="like_pcount_<?=$val['productcode']?>"><strong>좋아요</strong><?=$val['hott_cnt'] ?></span></button>
						<?}else{ ?>
						<button class="comp-like btn-like like_p<?=$val['productcode']?>" onclick="detailSaveLike('<?=$val['productcode']?>','off','product','<?=$_ShopInfo->getMemid()?>','<?=$val['brand'] ?>')" id="likehott_<?=$val['productcode']?>" title="선택 안됨"><span class="like_pcount_<?=$val['productcode']?>"><strong>좋아요</strong><?=$val['hott_cnt'] ?></span></button>
						<?} ?>
					</figcaption>
				</figure>
			</li>
		<?} ?>
	<?} ?>
<?}else{ ?>
	<li class="ta-c none">관련 상품이 없습니다.</li>
<?} ?>

