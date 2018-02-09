<?php
include_once('./outline/header_m.php')
?>

<link rel="stylesheet" href="./static/css/jquery.bxslider.css">
<script src="./static/js/jquery.bxslider.min.js"></script>



	<!-- 내용 -->
	<main id="content">
		
		<div class="sub-title">
			<h2>PLAY THE STAR</h2>
			<a class="btn-prev" href="javascript:history.go(-1);"><img src="./static/img/btn/btn_page_prev.png" alt="이전 페이지"></a>
		</div>

		<div class="alexKim-gallery-wrap">
			<ul class="img-list">
				<li style="background:url(./static/img/alexkim/alexkim_01.jpg) 50% 25px no-repeat; background-size:contain;"><span class="title">NEPAL SWAYAMBBHUNATH, 2010</span></li>
				<li style="background:url(./static/img/alexkim/alexkim_02.jpg) 50% 25px no-repeat; background-size:contain;"><span class="title">TEBET, 2011</span></li>
				<li style="background:url(./static/img/alexkim/alexkim_03.jpg) 50% 25px no-repeat; background-size:contain;"><span class="title">TEBET, 2011</span></li>
				<!-- <li style="background:url(./static/img/alexkim/alexkim_04.jpg) 50% 25px no-repeat; background-size:contain;"><span class="title">TEBET, 2011</span></li>
				<li style="background:url(./static/img/alexkim/alexkim_05.jpg) 50% 25px no-repeat; background-size:contain;"><span class="title">PAKISTAN, 2012</span></li>
				<li style="background:url(./static/img/alexkim/alexkim_06.jpg) 50% 25px no-repeat; background-size:contain;"><span class="title">PAKISTAN, 2012</span></li>
				<li style="background:url(./static/img/alexkim/alexkim_07.jpg) 50% 25px no-repeat; background-size:contain;"><span class="title">MYANMAR, 2012</span></li>
				<li style="background:url(./static/img/alexkim/alexkim_08.jpg) 50% 25px no-repeat; background-size:contain;"><span class="title">MYANMAR, 2012</span></li>
				<li style="background:url(./static/img/alexkim/alexkim_09.jpg) 50% 25px no-repeat; background-size:contain;"><span class="title">BRAZIL, 2012</span></li>
				<li style="background:url(./static/img/alexkim/alexkim_10.jpg) 50% 25px no-repeat; background-size:contain;"><span class="title">BRAZIL, 2012</span></li>
				<li style="background:url(./static/img/alexkim/alexkim_11.jpg) 50% 25px no-repeat; background-size:contain;"><span class="title">RUSSIA, 2015</span></li>
				<li style="background:url(./static/img/alexkim/alexkim_12.jpg) 50% 25px no-repeat; background-size:contain;"><span class="title">RUSSIA, 2015</span></li>
				<li style="background:url(./static/img/alexkim/alexkim_13.jpg) 50% 25px no-repeat; background-size:contain;"><span class="title">GYEONGNIDANGIL 130, 2015</span></li>
				<li style="background:url(./static/img/alexkim/alexkim_14.jpg) 50% 25px no-repeat; background-size:contain;"><span class="title">MOSKVA, 2015</span></li>
				<li style="background:url(./static/img/alexkim/alexkim_15.jpg) 50% 25px no-repeat; background-size:contain;"><span class="title">RUSSIA, 2015</span></li>
				<li style="background:url(./static/img/alexkim/alexkim_16.jpg) 50% 25px no-repeat; background-size:contain;"><span class="title">SIBERIA, 2015</span></li>
				<li style="background:url(./static/img/alexkim/alexkim_17.jpg) 50% 25px no-repeat; background-size:contain;"><span class="title">SIBERIA, 2015</span></li> -->
				<!-- <li><img src="./static/img/alexkim/alexkim_01.jpg" title="NEPAL SWAYAMBBHUNATH, 2010"></li>
				<li><img src="./static/img/alexkim/alexkim_02.jpg" title="TEBET, 2011"></li>
				<li><img src="./static/img/alexkim/alexkim_03.jpg" title="TEBET, 2011"></li>
				<li><img src="./static/img/alexkim/alexkim_04.jpg" title="TEBET, 2011"></li>
				<li><img src="./static/img/alexkim/alexkim_05.jpg" title="PAKISTAN, 2012"></li>
				<li><img src="./static/img/alexkim/alexkim_06.jpg" title="PAKISTAN, 2012"></li>
				<li><img src="./static/img/alexkim/alexkim_07.jpg" title="MYANMAR, 2012"></li>
				<li><img src="./static/img/alexkim/alexkim_08.jpg" title="MYANMAR, 2012"></li>
				<li><img src="./static/img/alexkim/alexkim_09.jpg" title="BRAZIL, 2012"></li>
				<li><img src="./static/img/alexkim/alexkim_10.jpg" title="BRAZIL, 2012"></li>
				<li><img src="./static/img/alexkim/alexkim_11.jpg" title="RUSSIA, 2015"></li>
				<li><img src="./static/img/alexkim/alexkim_12.jpg" title="RUSSIA, 2015"></li>
				<li><img src="./static/img/alexkim/alexkim_13.jpg" title="GYEONGNIDANGIL 130, 2015"></li>
				<li><img src="./static/img/alexkim/alexkim_14.jpg" title="MOSKVA, 2015"></li>
				<li><img src="./static/img/alexkim/alexkim_15.jpg" title="RUSSIA, 2015"></li>
				<li><img src="./static/img/alexkim/alexkim_16.jpg" title="SIBERIA, 2015"></li>
				<li><img src="./static/img/alexkim/alexkim_17.jpg" title="SIBERIA, 2015"></li> -->
			</ul>
		</div>

	</main>
	<!-- // 내용 -->
<script type="text/javascript">
$(document).ready(function(){
	var slide_index = 0;
	var slide_length = 0;
	var action_type = false;
	
	var photo_slide = $('.img-list').bxSlider({
		touchEnabled: false,
		pager: false,
		captions: true,
		infiniteLoop: false,
		onSliderLoad : function( currentIndex ){
			slide_length = $('.img-list > li ').length;
			//sliderOnCarousel();
		},
		onSlidePrev : function( $slideElement, oldIndex, newIndex ){
			slide_index = newIndex;
			action_type = true;
		},
		onSlideNext : function( $slideElement, oldIndex, newIndex ){
			slide_index = newIndex;
			action_type = true;
		},
		onSlideAfter : function( $slideElement, oldIndex, newIndex ){
			action_type = false;
			//sliderOnCarousel();
		}
	});
	
	$('.bx-next, .bx-prev').on( 'click', function( event ){
		if( $(this).hasClass('bx-next') && slide_index == slide_length - 1 && action_type === false ) {
			sliderNextPage( 'bx-next' );
		} else if( $(this).hasClass('bx-prev') && slide_index == 0 && action_type === false  ) {
			sliderNextPage( 'bx-prev' );
		}
	});
/*
	photo_slide.on( 'touchend ', function( event ){
		sliderOnCarousel();
	});

	function sliderOnCarousel(){
		$('.bx-next, .bx-prev').stop();
		$('.bx-next, .bx-prev').fadeIn().animate({opacity: '+=0'}, 3000).fadeOut();
	}
*/
	function sliderNextPage( button ){
		if( button == 'bx-next' ){
			location.href='/m/gallery_alexKim_outro.php';
		} else if( button == 'bx-prev' ){
			location.href='/m/gallery_alexKim_intro.php';
		}
	}
});
</script>
<?php
include_once('./outline/footer_m.php')
?>
