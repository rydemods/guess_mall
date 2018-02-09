<?php include_once('outline/header_m.php'); ?>
<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

$instaimgpath = $Dir.DataDir."shopimages/instagram/";

$mem_id		= $_MShopInfo->getMemid();

$ino				= $_GET["ino"];
$instaSql = "SELECT  i.*, li.section,
						COALESCE((select COUNT( tl.hott_code )AS hott_cnt from tblhott_like tl WHERE tl.section = 'instagram' AND i.idx::varchar = tl.hott_code),0) AS hott_cnt ";
$instaSql .= "FROM tblinstagram i
			LEFT JOIN ( SELECT hott_code, section FROM tblhott_like WHERE section = 'instagram' AND like_id = '".$mem_id."' GROUP BY hott_code, section ) li on i.idx::varchar = li.hott_code
			WHERE i.display = 'Y' ";
$instaSql .= " AND i.idx='{$ino}' ";

//echo $instaSql;

$instaResult	= pmysql_query($instaSql,get_db_conn());
$instaRow = pmysql_fetch_array($instaResult);

$insta_img = getProductImage($instaimgpath,$instaRow['img_m_file']);

$instaRow_content = stripslashes($instaRow['content']);
	
//<img>태그 제거
$instaRow_content	 = preg_replace("/<img[^>]+\>/i", "", $instaRow_content);

// <br>태그 제거
$arrList = array("/<br\/>/", "/<br>/");
$instaRow_content_tmp = trim(preg_replace($arrList, "", $instaRow_content));

if ( !empty($instaRow_content_tmp) ) {
	//$instaRow_content	= str_replace(" ","&nbsp;",nl2br($instaRow_content));
	$instaRow_content	= str_replace("<p>","<div>",$instaRow_content);
	$instaRow_content	= str_replace("</p>","</div>",$instaRow_content);
}

$related_html = "";
if ($instaRow['relation_product']) {
	$related_sql = "SELECT pr.productcode, pr.productname, pr.sellprice, ";
	$related_sql.= "pr.consumerprice, pr.buyprice, pr.brand, pr.maximage, ";
	$related_sql.= "pr.minimage, pr.tinyimage, pr.mdcomment, pr.review_cnt, ";
	$related_sql.= "pr.icon, pr.soldout, pr.quantity, pr.over_minimage, pr.relation_tag FROM tblproduct pr ";
	$related_sql.= "join tblproductlink c on (c.c_maincate = 1 and pr.productcode = c.c_productcode ) ";
	$related_sql.= "WHERE pr.productcode IN ('".str_replace(",", "','", $instaRow['relation_product'])."') ";
	$related_sql.= "AND pr.display = 'Y' ";

	$related_html = productlist_print( $related_sql, 'W_016' );
}
?>

<section class="top_title_wrap">
	<h2 class="page_local">
		<a href="javascript:history.back();" class="prev"></a>
		<span>INSTAGRAM</span>
		<a href="<?=$Dir.FrontDir?>" class="home"></a>
	</h2>
</section>

<div class="store-story-wrap view">
	<div class="cont-img"><img src="<?=$insta_img?>" alt=""></div>
	<div class="name">
		<button class="like_i<?=$instaRow['idx']?> comp-like btn-like<?=$instaRow['section']?' on':''?>" onclick="detailSaveLike('<?=$instaRow['idx']?>','<?=$instaRow['section']?' on':'off'?>','instagram','<?=$mem_id?>','')" title="<?=$instaRow['section']?'선택됨':'선택 안됨'?>"><span  class="like_icount_<?=$instaRow['idx']?>"><strong>좋아요</strong><?=number_format($instaRow['hott_cnt'])?></span></button>
	</div>
	<div class="cont-txt pd-10">
		<?=$instaRow_content?>
	</div>

	<?php
	if( $related_html ) {
	?>
		<!-- 관련 상품 -->
		<section class="goods-detail-related">
			<h3>관련 상품<span class="plus"></span></h3>
			<div class="related_product">
	<?php
				foreach( $related_html as $key=>$related ){
					echo $related;
				} // related foreach
	?>
			</div>
		</section>
		<!-- // 관련 상품 -->
	<?
	}
	?>
</div><!-- //.store-story-wrap -->


<? include_once('outline/footer_m.php'); ?>
