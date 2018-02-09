<?php include_once('outline/header_m.php'); ?>

<?
include_once($Dir."lib/premiumbrand.class.php");
$pb = new PREMIUMBRAND;
$pb->cube_list();
$pb->section_list('mobile');
$cube_list = $pb->cube_list;
$section_list = $pb->section_list;
$imagepath = $Dir.DataDir."shopimages/mainbanner/";
$imagepath_b = $Dir.DataDir."shopimages/premiumbrand/";
$pb->pb_info();
$pb_info = $pb->pb_info;
?>

<script type="text/javascript">
$(window).ready(function() {
	//프리미엄 브랜드 슬라이더
	var brandSlider = $('.brandpage .slider').bxSlider({
		infiniteLoop: false,
		controls: false
	});

	//var slideNum = $('.brandpage .slider section').length();
	var len = $('.slides').length;
	for(var i=0; i<len; i++){
	   $('.photonav .slides').eq(i)[0].num=i+1;
	   $('.photonav .slides').eq(i).click(function(){
		  if($('.brandpage').find('section').eq(this.num)[0]) brandSlider.goToSlide(this.num);
	   });
	}
});
</script>

<div class="layer-dimm-wrap layer_brand_video">
	<div class="dimm-bg"></div>
	<div class="layer-content">
		<div class="brand_video">
			<iframe id="movie_frame" width="320" height="315" src="" frameborder="0" allowfullscreen=""></iframe>
		</div>
	</div>
</div>

<article class="brandpage">
	<div class="slider clear">

	<?if($pb_info->use_cube =='Y'){?>	
		<section id="cube_section">
			<h2 class="logo">
				<!-- <img src="static/img/common/logo_nike.jpg" alt="로고"> -->
				<img src="<?=$imagepath_b.$pb_info->brand_logo?>" alt="로고"> 
			</h2>
		<?if($cube_list){?>
			<ul class="photonav clear">
			<?foreach($cube_list as $key=>$c_val){?>
				<?if($c_val->type2=='i'){?>
				<li><a class="slide0<?=$key?> slides" href="javascript:;"><img src="<?=$imagepath_b.$c_val->img?>" alt=""></a></li>
				<?}else if($c_val->type2=='m'){?>
				<li><a class="btn_brand_video" href="javascript:;" data-src="<?=$c_val->link?>"><img src="<?=$imagepath_b.$c_val->img?>" alt=""></a></li>
				<?}?>
			<?}?>
				<!--
				<li><a class="slide01 slides" href="javascript:;"><img src="static/img/common/photonav_nike01.jpg" alt=""></a></li>
				<li><a class="slide02 slides" href="javascript:;"><img src="static/img/common/photonav_nike02.jpg" alt=""></a></li>
				<li><a class="slide03 slides" href="javascript:;"><img src="static/img/common/photonav_nike03.jpg" alt=""></a></li>
				<li><a class="slide04 slides" href="javascript:;"><img src="static/img/common/photonav_nike04.jpg" alt=""></a></li>
				<li><a class="btn_brand_video" href="javascript:;"><img src="static/img/common/photonav_nike05.jpg" alt=""></a></li>
				<li><a class="slide05 slides" href="javascript:;"><img src="static/img/common/photonav_nike06.jpg" alt=""></a></li>
				<li><a class="slide06 slides" href="javascript:;"><img src="static/img/common/photonav_nike07.jpg" alt=""></a></li>
				<li><a class="slide07 slides" href="javascript:;"><img src="static/img/common/photonav_nike08.jpg" alt=""></a></li>
				<li><a class="slide08 slides" href="javascript:;"><img src="static/img/common/photonav_nike09.jpg" alt=""></a></li>
				-->
			</ul>
		<?}?>
		</section>
	<?}?>

	<?if($section_list){?>
		<?foreach($section_list as $s_val){?>
		<section class="section_list" data-link="<?=$s_val->link?>">
			<img src="<?=$imagepath_b.$s_val->img_m?>" alt="">
		</section>
		<?}?>
	<?}?>
		<!--
		<section>
			<img src="static/img/common/brand_nike_slide01.jpg" alt="">
		</section>

		<section>
			<img src="static/img/common/brand_nike_slide01.jpg" alt="">
		</section>

		<section>
			<img src="static/img/common/brand_nike_slide01.jpg" alt="">
		</section>
		-->
	</div><!-- //.slider -->

</article><!-- //.brandpage -->

<script>
function view_movie(){
	var src = $(this).data('src');
	var url = "http://www.youtube.com/embed/"+src;
	$("#movie_frame").attr("src",url);
}

function section_link()
{
	var link = $(this).data('link');
	if(link){location.href="/m/"+link;}
}


$(document).on("click",".btn_brand_video",view_movie);

$(document).on("click",".section_list",section_link);

</script>


<? include_once('outline/footer_m.php'); ?>