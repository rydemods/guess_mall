<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
?>

<?php include($Dir.MainDir.$_data->menu_type.".php") ?>

<div id="contents" class="exhibition-slide">

	<div class="breadcrumb studio-top">
		<ul>
			<li><a href="#">HOME</a></li>
			<li><a href="/front/studio.php">STUDIO</a></li>
			<li class="on"><a href="/front/play_the_star_view.php">PLAY THE STAR</a></li>
		</ul>
	</div><!-- //.breadcrumb -->

	<ul class="bxslider" style="background-color:#000;">
		<li style="background:#000 url(../static/img/common/exhibition_01.jpg) 50% 60px no-repeat;"><span class="photo_title">Nepal Swayambhunath, 2010</span></li>
		<li style="background:#000 url(../static/img/common/exhibition_02.jpg) 50% 60px no-repeat;"><span class="photo_title">Tibet, 2011</span></li>
		<li style="background:#000 url(../static/img/common/exhibition_03.jpg) 50% 60px no-repeat;"><span class="photo_title">Tibet, 2011</span></li>
		<li style="background:#000 url(../static/img/common/exhibition_04.jpg) 50% 60px no-repeat;"><span class="photo_title">Tibet, 2011</span></li>
		<li style="background:#000 url(../static/img/common/exhibition_05.jpg) 50% 60px no-repeat;"><span class="photo_title">Pakistan, 2012</span></li>
		<li style="background:#000 url(../static/img/common/exhibition_06.jpg) 50% 60px no-repeat;"><span class="photo_title">Pakistan, 2012</span></li>
		<li style="background:#000 url(../static/img/common/exhibition_07.jpg) 50% 60px no-repeat;"><span class="photo_title">Myanmar, 2012</span></li>
		<li style="background:#000 url(../static/img/common/exhibition_08.jpg) 50% 60px no-repeat;"><span class="photo_title">Myanmar, 2012</span></li>
		<li style="background:#000 url(../static/img/common/exhibition_09.jpg) 50% 60px no-repeat;"><span class="photo_title">Brazil, 2012</span></li>
		<li style="background:#000 url(../static/img/common/exhibition_10.jpg) 50% 60px no-repeat;"><span class="photo_title">Bolivia, 2012</span></li>
		<li style="background:#000 url(../static/img/common/exhibition_11.jpg) 50% 60px no-repeat;"><span class="photo_title">Russia, 2015</span></li>
		<li style="background:#000 url(../static/img/common/exhibition_12.jpg) 50% 60px no-repeat;"><span class="photo_title">Russia, 2015</span></li>
		<li style="background:#000 url(../static/img/common/exhibition_13.jpg) 50% 60px no-repeat;"><span class="photo_title">gyeongnidangil 130, 2015</span></li>
		<li style="background:#000 url(../static/img/common/exhibition_14.jpg) 50% 60px no-repeat;"><span class="photo_title">Moskva, 2015</span></li>
		<li style="background:#000 url(../static/img/common/exhibition_15.jpg) 50% 60px no-repeat;"><span class="photo_title">Russia, 2015</span></li>
		<li style="background:#000 url(../static/img/common/exhibition_16.jpg) 50% 60px no-repeat;"><span class="photo_title">Siberia, 2015</span></li>
		<li style="background:#000 url(../static/img/common/exhibition_17.jpg) 50% 60px no-repeat;"><span class="photo_title">Siberia, 2015</span></li>
	</ul>
</div>

<script>
$(document).ready(function(){
	var old_index = 0;
	
	var photo_slide = $('.exhibition-slide .bxslider').bxSlider({
		mode:'fade',
		pager: false,
		captions: false,
		keyboardEnabled: true,
		infiniteLoop: false
	});

	$('.bx-next, .bx-prev').on( 'click', function( event ){
		sliderNextPage();
	});

	$(document).on( 'keyup', function( event ){
		if( event.keyCode == 39 || event.keyCode == 37 ) sliderNextPage();
	});

	function sliderNextPage(){
		var img_lenght = photo_slide.getSlideCount();
		var img_current_element = photo_slide.getCurrentSlideElement();
		var img_index = $('.bxslider > li').index( img_current_element );

		if( img_index == old_index && img_index == img_lenght - 1 ) {
			location.href='http://test-deco.ajashop.co.kr/front/alexkim_exhibition_outro.php';
		} else if( img_index == old_index && img_index == 0 ) {
			location.href='http://test-deco.ajashop.co.kr/front/alexkim_exhibition_intro.php';
		} else {
			old_index = img_index
		}
	}

});
</script>

<?php  include ($Dir."lib/bottom.php") ?>
<?=$onload?>
</BODY>
</HTML>