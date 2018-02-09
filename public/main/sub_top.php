<?php 
/*	lnb type
	1 : main
	2 : list
	3 : view

 */

?>
<script type="text/javascript">
function goSearch(){
	document.formForSearch.submit();	
}
</script>

<form name=formForSearch action="../front/productsearch.php" method=get>
<?
switch($subTop_flag){
	case 1 : 
?>

<!-- 메인롤링&서치 -->
<div class="main_visual_wrap">
	<div class="search_position" style="z-index: 100">
		<p class="ment"><br />해외 소프트웨어의 경쟁력있는 구매<br />어떤 소프트웨어라도 TOOLFARM 에서는 가능합니다</p>
		<a href="javascript:vode(0);" data-target="prev" class="btn_left slider_btn" title="이전"></a>
		<a href="javascript:vode(0);" data-target="next" class="btn_right slider_btn" title="다음"></a>
		<p class="searchbox_wrap">
			<a href="javascript:goSearch();" class="btn_find"></a>
			<input type="text" name="search" id="search" onclick="this.value='';" value=""/>
		</p>
	</div>
	<div class="searchSlide">
	<ul class="rolling_img">
		<!--<li style="background:url(../img/common/main_visual_rolling01.jpg) center no-repeat;"></li>-->
<?php
	foreach($mainBanner[maintop_rolling] as $k=>$v){
?>
		<li style="background:url(<?=$Dir.$v[banner_img]?>) center no-repeat;width:1920px;"></li>
<?	}?>
	</ul>
	</div>
</div><!-- //.main_visual_wrap -->
<script type="text/javascript">
$(document).ready(function (){
	var searchSlide = $("div.searchSlide").sudoSlider({
		effect: "slide",
		speed:1000,
		continuous:true,
		slideCount:1,
		prevNext:false,
		moveCount:1,
		customLink:'div.search_position a.slider_btn',
		autoWidth:false,
		auto:false,
		animationZIndex:0
	});
});
</script>
<? break;

	case 2 :
?>						
<!-- 서브비주얼&서치 -->
<div class="sub_top_visual">
	<div class="sub_top_visual_wrap">
		<p class="ment">TOOLFARM에서는<br />완벽한 가상공간을 생동감 넘치게 재현할 수 있다.</p>
		<p class="searchbox_wrap">
			<a href="javascript:goSearch();" class="btn_find"></a>
			<input type="text" name="search" id="search" onclick="this.value='';" value=""/>
		</p>
	</div>
</div><!-- //.main_visual_wrap -->
<?	break; 

	case 3 : 

?>
<!-- 서브비주얼&서치 -->
<div class="sub_top_visual view_page">
	<div class="sub_top_visual_wrap">
		<p class="searchbox_wrap">
			<a href="javascript:goSearch();" class="btn_find"></a>
			<input type="text" name="search" id="search" onclick="this.value='';" value=""/>
		</p>
	</div>
</div><!-- //.main_visual_wrap -->
<?	break;
} ?>	

</form>