<?php
include_once('outline/header_m.php');
$page_cate = 'BRAND';

$bridx = $_GET['bridx'];
$imagepath = $Dir.DataDir."shopimages/mainbanner/";

// 리스트 목록
$list_sql = "SELECT bno,brand_bridx,brand_name,story_content,concept_content,brand_link,top_banner_img_pc,top_banner_img_mobile,banner_sort,gnb_banner_img_pc,gnb_banner_img_mobile,view_type,view_number
 				FROM tblmainbrand WHERE brand_status = 0 AND view_type = 1 AND brand_bridx = ".$bridx;

$result = pmysql_query( $sql, get_db_conn() );
while( $row = pmysql_fetch_array( $result ) ){
	$data_brand[] = $row;
}

$list_result = pmysql_query($list_sql,get_db_conn());
if($row=pmysql_fetch_object($list_result)){
	$temp_bridx = $row->bridx;
	$temp_brandname = $row->brandname;
	$temp_vender = $row->vender;
	$temp_sotry_content = $row->story_content;
	$temp_concept_centent = $row->concept_content;
	$temp_brand_link = $row->brand_link;

	$temp_top_banner_img_mobile = getProductImage($imagepath, $row->top_banner_img_mobile );
	$temp_gnb_banner_img_mobile = getProductImage($imagepath, $row->gnb_banner_img_mobile );

	if($row->top_banner_img_mobile == ''){
		$temp_top_banner_img_mobile = $temp_top_banner_img_pc."?v".date("His");
	} else if ($row->top_banner_img_pc != ''){
		$temp_top_banner_img_mobile = $imagepath.$row->top_banner_img_mobile;
	}

	if($row->gnb_banner_img_mobile == ''){
		$temp_gnb_banner_img_mobile = $temp_gnb_banner_img_pc."?v".date("His");
	} else if ($row->gnb_banner_img_mobile != ''){
		$temp_gnb_banner_img_mobile = $imagepath.$row->gnb_banner_img_mobile;
	}

} else {
	if($temp_sotry_content == "" || $temp_sotry_content == null){
		$temp_sotry_content = "내용 준비중 입니다";
	}
	if($temp_concept_centent == "" || $temp_concept_centent == null){
		$temp_concept_centent = "내용 준비중 입니다";
	}
	$temp_top_banner_img_mobile = $temp_top_banner_img_mobile."?v".date("His");
	$temp_gnb_banner_img_mobile = $temp_gnb_banner_img_mobile."?v".date("His");
}

$bProductSql = "SELECT a.productcode,b.productname,b.sellprice,b.tinyimage ";
$bProductSql.= "FROM tblmainbrand_product a ";
$bProductSql.= "JOIN tblproduct b ON a.productcode=b.productcode ";
$bProductSql.= "WHERE a.tblmainbrand_bno = ".$row->bno;
$bProductResult = pmysql_query($bProductSql,get_db_conn());
while($bProductRow = pmysql_fetch_array($bProductResult)){
	$thisBannerProduct[] = $bProductRow;
}
pmysql_free_result( $bProductResult );

$temp_sql = "SELECT * FROM tblproductbrand WHERE bridx = ".$bridx;
$temp_result = pmysql_query($temp_sql,get_db_conn());

?>

<!-- 내용 -->
<main id="content" class="subpage fullh">
	<section class="page_local">
		<h2 class="page_title">
			<a href="javascript:history.back();" class="prev">이전페이지</a>
		<?php 
			while($temp_row = pmysql_fetch_object($temp_result)) {
				echo "<span>".$temp_row->brandname."</span>";
			}
		?>
		</h2>
		<div class="breadcrumb">
			<?php include_once('brand_menu.php'); ?>
		</div>
	</section><!-- //.page_local -->

	<section class="brand_intro">
		<?php 
			if($bridx == "307"){
		?>
				<div class="vh-main" style="float:left;width:100%;height:auto;text-align:center;padding-bottom:350px;">
					<img src="./fromsw/images/main/m_vh_main.jpg" alt="VanHart Main Image" />
				</div>
		<?php
			} else {
		?>
				<div class="img"><img src="<?=$temp_top_banner_img_mobile ?>" alt="브랜드 이미지"></div>
		<?php
			}
		?>
		<!-- <a href="brand_shop.php" class="btn_shop">SHOP</a> -->		<!-- 추후 전체이미지에 샵 링크 연동 일단 숨김 -->
		<div class="box_txt">
			<dl>
				<dt>STORY</dt>
				<dd><?=$temp_sotry_content ?> </dd>
			</dl>
			<dl>
				<dt>CONCEPT</dt>
				<dd><?=$temp_concept_centent ?></dd>
			</dl>
			<dl>
				<dt>NEW PRODUCT</dt>
				<?foreach($thisBannerProduct as $bannerProductKey=>$bannerProduct){?>	
					<!-- 
					<dd><a href="productdetail.php?productcode=<?=$bannerProduct[productcode] ?>&code="><?=$bannerProduct[productname]?></a>&nbsp;&nbsp;</dd>
					 -->
					<dd><a href="productdetail.php?productcode=<?=$bannerProduct[productcode] ?>&code="><?=$bannerProduct[productname]?></a>&nbsp;&nbsp;</dd>
				<?}?>
			</dl>
		</div>
	</section>

</main>
<!-- //내용 -->

<?php
include_once('outline/footer_m.php');
?>