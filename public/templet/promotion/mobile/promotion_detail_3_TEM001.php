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

	<!-- 포토 리스트 -->
	<section class="event_photo">
		<h4>등록된 포토<span class="count">(<?=number_format($t_count)?>)</span></h4>
		<ul class="event_photolist clear">
			<?=$list_html?>
		</ul><!-- //.event_photolist -->
    <?php
        if( $paging->pagecount > 0 ){
    ?>
		<div class="list-paginate">
			<?php echo $paging->a_prev_page.$paging->print_page.$paging->a_next_page; ?>
		</div><!-- //.list-paginate -->
    <?php
        }
    ?>
        <div class="btn-set">
			<?php if(strlen($_ShopInfo->getMemid())==0) {?>
			<button type="button" class="btn-point" onClick="javascript:goLogin();">글쓰기</button>
			<?} else { ?>
			<button type="button" class="btn-point" onClick="javascript:location.href='?<?=$_SERVER['QUERY_STRING']?>&mode=write';">글쓰기</button>
			<?} ?>
			<button type="button" class="btn-def list" onClick="javascript:location.href='<?=$Dir.MDir?>promotion.php?view_mode=<?=$view_mode?>&view_type=<?=$view_type?>'">이벤트 목록</button>
		</div>
	</section><!-- //.event_photo -->
	<!-- //포토 리스트 -->

</article><!-- //.event_detail -->