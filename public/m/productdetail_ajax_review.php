<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once("lib.inc.php");
include_once("shopdata.inc.php");

$productcode = $_REQUEST['productcode'];
$page = $_REQUEST['page'];

//리뷰카운트
$reviewsql = "SELECT * FROM tblproductreview WHERE productcode = '".$productcode."' ORDER BY num desc ";
$reviewres = pmysql_query($reviewsql,get_mdb_conn());
$reviewcount = pmysql_num_rows($reviewres );
//리뷰리스트

$reviewlist_num = 5;
$reviewoffset = ($page-1) * $reviewlist_num ;
$reviewtotalpage = ceil($reviewcount / $reviewlist_num);
$reviewsql = "SELECT * FROM tblproductreview WHERE productcode = '".$productcode."' ORDER BY num desc limit $reviewlist_num offset $reviewoffset ";
$reviewres = pmysql_query($reviewsql,get_mdb_conn());
while($reviewrow = pmysql_fetch_object($reviewres)){
	$reviewloop[] = $reviewrow;
}

list($productname)=pmysql_fetch("SELECT productname FROM tblproduct WHERE productcode = '".$productcode."'");

?>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<ul class="review">
<?foreach($reviewloop as $review){?>
	<?
		$colorStar = 0;
		for($i=0;$i<$review->marks;$i++) {
			$colorStar += 20;
		}
		$noColorStar = "";
		/*for($i=$review->marks;$i<5;$i++) {
			$noColorStar .= "★";
		}*/
		if($review->name){
			$reviewWriter = $review->name;
		}else{
			$reviewWriter = "비회원";
		}
		$reviewDate=substr($review->date,0,4)."-".substr($review->date,4,2)."-".substr($review->date,6,2);
	?>
	<li>
		<a class="title" href="#" title="펼쳐보기">
			<span class="id"><?=$reviewWriter?>(<?=$reviewDate?>)</span>
			<div class="title"><?=$review->subject?></div>
			<div class="starbox"><span style="width:<?=$colorStar?>%"></span></div>
		</a>
		<?$imagepath2=$Dir.DataDir."shopimages/board/reviewbbs/";?>
		<div class="content">
			<p>
				<?if(is_file($imagepath2.$review->upfile)){?>
						<img src="<?=$imagepath2.$review->upfile?>">
					<?}?>
				<?=nl2br($review->content)?>
			</p>
<?php
		if( $_ShopInfo->getMemid()==$rVal['id'] && strlen( $_ShopInfo->getMemid() ) > 0 ){
		//if( isdev() ){
?>
			<p>
				<a href="javascript:review_delete('<?=$review->num?>');" class='btn-dib-function' >삭제</a>
				<a href='../m/mypage_review_write.php?num=<?=$review->num?>' class='btn-dib-function' >수정</a>
			</p>
<?php
		}
?>
		</div>
	</li>
	<?}?>
</ul>

<div class="page_num">
<?php if($page > 1) : ?>
	<a class="btn prev" href="javascript:reviewPage('<?=$page-1?>','<?=$productcode?>');">이전</a>
<?php endif;?>
	<span><?=$page?>/<?=$reviewtotalpage?></span>
<?php if($page != $reviewtotalpage ) : ?>
	<a class="btn next" href="javascript:reviewPage('<?=$page+1?>','<?=$productcode?>');">다음</a>
<?php endif;?>
</div>
