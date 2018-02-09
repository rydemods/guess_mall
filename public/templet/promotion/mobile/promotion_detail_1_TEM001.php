<?php include($Dir.TempletDir."promotion/mobile/promotion_navi_TEM001.php"); ?>

<article class="event_detail">
	<header>
		<h3 class="subject"><?=$title?></h3>
		<p class="date"><?=$start_date?> ~ <?=$end_date?></p>
		<a class="btn-sns_share" href="javascript:;"><img src="./static/img/btn/btn_sns_share.png" alt="sns공유하기"></a>
	</header>
    <?php if ( $image_type_m == "E" ) { ?>
   <section class="detail_content">
        <?=$content_m?>
    </section>
    <?php } elseif ( !empty($banner_img_m) ) { ?>
   <section class="detail_content">
        <img src="/data/shopimages/timesale/<?=$banner_img_m?>" alt="">
    </section>
    <?php } else {?>
		<?php if ( $image_type == "E" ) { ?>
	   <section class="detail_content">
			<?=$content?>
		</section>
		<?php } elseif ( !empty($banner_img) ) { ?>
	   <section class="detail_content">
			<img src="/data/shopimages/timesale/<?=$banner_img?>" alt="">
		</section>
		<?php }?>
    <?php }?>

	<!-- 상품리스트 -->
	<section class="event_goodslist">
		<ul class="tabmenu clear">
			<?=$promotion_tab_mobile_html?>
		</ul><!-- //.tabmenu -->
		 <?=$promotion_tablist_mobile_html?>
		 <div class="btn-set">
			<button type="button" class="btn-def" onClick="javascript:location.href='<?=$Dir.MDir?>promotion.php?view_mode=<?=$view_mode?>&view_type=<?=$view_type?>'">이벤트 목록</button>
		 </div>
	</section><!-- //.event_goodslist -->
	<!-- //상품리스트 -->

</article><!-- //.event_detail -->

<script type="text/javascript">
	function scroll_anchor(_target, _y) {

		if (arguments.length == 0) return;

		var $target = $(_target);
		var scrolltop = (_y == undefined) ? $target[0].offsetTop : _y;
		//TweenMax.to($("#page"), 0.3, { scrollTop:scrolltop, ease:Sine.easeOut });;
		//$(window).scrollTop(scrolltop);
		$('html, body').animate({
			scrollTop: scrolltop - 20
		}, 500);
	}
</script>