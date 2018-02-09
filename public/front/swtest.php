<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/cache_product.php");
include_once($Dir."lib/shopdata.php");

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
	
	$temp_top_banner_img_pc = getProductImage($imagepath, $row->top_banner_img_pc );
	$temp_gnb_banner_img_pc = getProductImage($imagepath, $row->gnb_banner_img_pc );
	
	if($row->top_banner_img_pc == ''){
		$temp_top_banner_img_pc = $temp_top_banner_img_pc."?v".date("His");
	} else if ($row->top_banner_img_pc != ''){
		$temp_top_banner_img_pc = $imagepath.$row->top_banner_img_pc;
	}
	
	if($row->gnb_banner_img_pc == ''){
		$temp_gnb_banner_img_pc = $temp_gnb_banner_img_pc."?v".date("His");
	} else if ($row->gnb_banner_img_pc != ''){
		$temp_gnb_banner_img_pc = $imagepath.$row->gnb_banner_img_pc;
	}
	
} else {
	if($temp_sotry_content == "" || $temp_sotry_content == null){
		$temp_sotry_content = "내용 준비중 입니다";
	}
	if($temp_concept_centent == "" || $temp_concept_centent == null){
		$temp_concept_centent = "내용 준비중 입니다";
	}
	$temp_top_banner_img_pc = $temp_top_banner_img_pc."?v".date("His");
	$temp_gnb_banner_img_pc = $temp_gnb_banner_img_pc."?v".date("His");
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

// echo $Dir.MainDir.$_data->menu_type.".php";
// exit();
include ($Dir.MainDir.$_data->menu_type.".php");?>

<div id="contents">
	<div class="brand-page">

		<article class="brand-about">
			<h2>SI 브랜드 소개</h2>
			<?php 
				if($bridx == "307"){
			?>
					<div class="vh-main" style="float:left;width:100%;height:auto;text-align:center;padding-bottom:200px;">
						<img src="./fromsw/images/main/vh_main.jpg" alt="VanHart Main Image" />
					</div>
			<?php
				} else {
			?>
					<div class="visual" style="background:url(<?=$temp_top_banner_img_pc ?>) 50% 0 no-repeat"></div>
			<?php
				}
			?>
			
			
			<div class="introduce clear">
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
						<tr align="center">
							<td style='border:0px'>
								<a name="pro_upChange" style="cursor: hand;">
									<img src="images/btn_plus.gif" border="0" style="margin-bottom: 3px;" />
								</a>
								<br>
								<a name="pro_downChange" style="cursor: hand;">
									<img src="images/btn_minus.gif" border="0" style="margin-top: 3px;" />
								</a>
							</td>
							<td style='border:0px'>
								<img style="width: 40px; height:40px;" src="<?=$Dir.DataDir."shopimages/product/".$bannerProduct['tinyimage']?>" border="1"/>
                                <img style="width: 40px; height:40px;" src="<?=getProductImage($Dir.DataDir.'shopimages/product/', $bannerProduct['tinyimage'] );?>" border="1"/>
								<input type='hidden' name='relationProduct[]' value='<?=$bannerProduct[productcode]?>'>
							</td>
							<td style='border:0px' align="left"><?=$bannerProduct[productname]?>&nbsp;&nbsp;<img src="images/icon_del1.gif" onclick="javascript:T_relationPrDel('<?=$bannerProduct[productcode]?>','relationProduct');" border="0" style="cursor: hand;vertical-align:middle;" />
							</td>
						</tr>
						 -->
					<!-- 
					<dd><a href="productdetail.php?productcode=<?=$bannerProduct[productcode] ?>&code="><?=$bannerProduct[productname]?></a>&nbsp;&nbsp;</dd>
					 -->
					<dd><a href="productdetail.php?productcode=<?=$bannerProduct[productcode] ?>&code="><?=$bannerProduct[productname]?></a>&nbsp;&nbsp;</dd>
					<?}?>
				</dl>
			</div>
		</article>

	</div>
</div><!-- //#contents -->

<?php  include ($Dir."lib/bottom.php") ?>