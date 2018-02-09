<?php
/*********************************************************************
// 파 일 명		: tem_main001.php
// 설     명	: 메인 템플릿
// 상세설명	    : 메인 템플릿
// 작 성 자		: 2015.11.02 - 김재수
// 수 정 자		: 2016.07.28 - 김재수
// 수 정 자		: 2017.01.20 - 위민트
//
*********************************************************************/
?>

<?include ($Dir.MainDir.$_data->menu_type.".php");?>

<?

$instaimgpath = $Dir.DataDir."shopimages/instagram/";
$productimgpath = $Dir.DataDir."shopimages/product/";
$pr_link = $Dir.'front/productdetail.php?productcode=';
?>

<script type="text/javascript" src="../static/js/product.js"></script>
<script type="text/javascript">

</script>


<div id="contents">
	<div class="main-wrap">
		
		<div class="main-visual">
			<?if(count($arrMainImgBanner) > 0){?>
			<ul id="main-visual-slide">
				<?foreach( $arrMainImgBanner as $key=>$val ){
					$bannerImg	= getProductImage($banner_imagepath, $val['banner_img']);
				?>
				<li><a href="<?=$val['banner_link']?>"><img src="<?=$bannerImg?>" alt="<?=$val['banner_title']?>"></a></li>
				<?php }?>
			</ul>
			<?php }?>
		</div><!-- //.main-visual -->

		<div class="inner-align">
			<div class="new-arrivals-wrap with-btn-rolling slideArrow01">
				<h3 class="title"><?=$top_product_row['banner_title']?></h3>
				
				<?
					//상품리스트
					//외주 하드 코딩 아자샵 솔루션 타입으로 변경 2017-02-16
					foreach( $top_product_list_array as $mainTopKey=>$mainTopVal ){
						echo $mainTopVal;
					}
				?>
			</div>
			<div class="main-middle-banner clear">
				<?
				$middletop_cnt = 0;
				foreach($MiddleTopBannerImg as $middletop_banner){
					if(!$middletop_cnt){
						$middletop_class="class='fl-l'";
					}else{
						$middletop_class="class='fl-r'";
					}
					$bannerImg	= getProductImage($banner_imagepath, $middletop_banner['banner_img']);
				?>
					<div <?=$middletop_class?>>
						<a href="<?=$middletop_banner['banner_link']?>">
							<img src="<?=$bannerImg?>" alt=""></a>
					</div>
					
				<?
					$middletop_cnt++;
				}?>
				<!--
				<div class="fl-l">
					<a href="<?=$top_left_banner_img_row['banner_link']?>">
						<img src="<?=$topLeftBannerImg?>" alt=""></a>
				</div>
				<div class="fl-r">
					<a href="<?=$top_right_banner_img_row['banner_link']?>">
						<img src="<?=$topRightBannerImg?>" alt=""></a>
				</div>-->
			</div>
		</div>
		<!-- //.inner-align -->

		<div class="main-best-wrap">
			<h3 class="title"><?=$brand_banner_list[0]['banner_title']?></h3>
			<div id="best-brand-nm">
				<?
				$productbrand_cnt = 0;
				foreach($brand_banner_list as $brand_banner){
				?>
					<a data-slide-index="<?=$productbrand_cnt?>"><?=$brand_banner['banner_up_title']?></a>
				<?
					$productbrand_cnt++;
				}?>
			</div>
			<div class="bg-wrap with-btn-rolling slideArrow02">
				<div id="main-best">
					<?
					foreach($brand_banner_list as $brand_banner){
						$brandBannerImg = getProductImage($banner_imagepath, $brand_banner['banner_img']);
						$brand_banner_no = $brand_banner['no'];
						$brand_banner_product_qry = fnGetBannerProduct($brand_banner_no,"3");

						$brand_banner_product_array = productlist_print( $brand_banner_product_qry, $type = 'M_002', array(), null, null, $code );
						//$brand_banner_product_result = pmysql_query($brand_banner_product_qry);
					?>
					<li>
						<img src="<?=$brandBannerImg?>" alt="">
						<?
							//상품리스트
							//외주 하드 코딩 아자샵 솔루션 타입으로 변경 2017-02-16
							foreach( $brand_banner_product_array as $mainMiddleKey=>$mainMiddleVal ){
								echo $mainMiddleVal;
							}
						?>
						
					</li>
					<?php }?>
				</div>
			</div>
		</div><!-- //.main-best-wrap -->

		<div class="inner-align">
			
			<div class="mt-70 fz-0">
				<a href="<?=$middle_banner_img_row['banner_link']?>"><img src="<?=$middleBannerImg?>" alt=""></a>
			</div>
			
			<div class="main-look">
				<div class="concept">
					<p class="title"><?=$look_banner_row['banner_title']?></p>
					<div class="ment">
						<p><?=$look_banner_row['banner_name']?></p>
					</div>
				</div>
				<div class="pic mt-40">
					<img src="<?=$lookBannerImg?>" alt="">
					<?
						//상품리스트
						//외주 하드 코딩 아자샵 솔루션 타입으로 변경 2017-02-16
						foreach( $look_banner_product_array as $mainBottomKey=>$mainBottomVal ){
							echo $mainBottomVal;
						}
					?>
				</div>
			</div><!-- //.main-look -->
			<ul class="main-bottom-banner mt-80 clear">
				<?php
				// 하단 배너
				while($bottom_banner_img_row = pmysql_fetch_array($bottom_banner_img_result)){
					$bottomBannerImg = getProductImage($banner_imagepath, $bottom_banner_img_row['banner_img']);
				?>
					<li><a href="<?=$bottom_banner_img_row['banner_link']?>"><img src="<?=$bottomBannerImg?>" alt=""></a></li>
				<?php
				}
				?>
			</ul>
		</div><!-- //.inner-align -->

	</div>
</div><!-- //#contents -->




<script type="text/javascript">
var memId = "<?=$_ShopInfo->getMemid()?>";
$(document).ready( function() {

});

</script>
<?php
    // 레이어
    include_once($Dir."lib/eventpopup.php");
    include_once($Dir."lib/eventlayer.php");
//     include_once($Dir."lib/product_preview_popup.php");
//     include_once($Dir."lib/product_layer.php");			// 상품 상세 관련 레이어(리뷰, 쿠폰 등..)추가 위민트 170131
?>

<?php include_once($Dir."lib/bottom.php");?>

<?php include_once($Dir."front/productdetail_layer.php");?>

</body>

</html>
