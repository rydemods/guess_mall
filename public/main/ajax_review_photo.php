<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

$imagepath = $Dir.DataDir."shopimages/review/";

$sql = "SELECT b.minimage, a.id,a.name,a.reserve,a.display,a.subject,a.content,a.date,a.productcode,
			b.productname,b.tinyimage,b.selfcode,b.assembleuse, a.upfile, a.best_type, a.marks, a.type, a.num
			FROM tblproductreview a, tblproduct b, (SELECT c_productcode,c_category FROM tblproductlink WHERE c_maincate = 1 ) c 
			WHERE a.productcode = b.productcode AND a.productcode = c.c_productcode AND a.type = '1' AND b.display = 'Y' AND b.hotdealyn = 'N'
			ORDER BY a.best_type desc, a.date DESC";
$result = pmysql_query($sql);
while ( $row = pmysql_fetch_array($result) ) {
	$photoReviewList[] = $row;
}	
if(count($photoReviewList) > 0){
	foreach( $photoReviewList as $key=>$val ){
		$marks = $val['marks'] * 20;

?>
			<li>
				<a href="javascript:prod_detail('<?=$val['productcode'] ?>');">
					<figure>
						<div class="img"><img src="<?=$imagepath.$val['upfile'] ?>" alt=""></div>
						<figcaption>
							<p class="title"><?=$val['subject'] ?></p>
							<span class="comp-star star-score"><strong style="width:<?=$marks ?>%;"></strong></span>
						</figcaption>
					</figure>
				</a>
			</li>
	<?} ?>	
<?}else{ ?>
	<li class="ta-c none">관련 리뷰가 없습니다.</li>
<?} ?>

