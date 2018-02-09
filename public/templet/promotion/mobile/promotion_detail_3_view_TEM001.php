<?php include($Dir.TempletDir."promotion/mobile/promotion_navi_TEM001.php"); ?>

<article class="event_detail">
	<header>
		<h3 class="subject"><?=$prm_title?></h3>
		<p class="date"><?=$prm_start_date?> ~ <?=$prm_end_date?></p>
		<a class="btn-sns_share" href="javascript:;"><img src="./static/img/btn/btn_sns_share.png" alt="sns공유하기"></a>
	</header>
    <?php if ( $prm_image_type_m == "E" ) { ?>
   <section class="detail_content">
        <?=$prm_content_m?>
    </section>
    <?php } elseif ( !empty($prm_banner_img_m) ) { ?>
   <section class="detail_content">
        <img src="/data/shopimages/timesale/<?=$prm_banner_img_m?>" alt="">
    </section>
    <?php } else {?>
		<?php if ( $prm_image_type == "E" ) { ?>
	   <section class="detail_content">
			<?=$prm_content?>
		</section>
		<?php } elseif ( !empty($prm_banner_img) ) { ?>
	   <section class="detail_content">
			<img src="/data/shopimages/timesale/<?=$prm_banner_img?>" alt="">
		</section>
		<?php }?>
    <?php }?>

	<!-- 포토 상세 -->
	<section class="event_photo">
		<h4>등록된 포토<span class="count">(<?=number_format($total_comment_count)?>)</span></h4>
		<div class="view_header">
			<p class="subject"><?=$title?></p>
			<p class="period"><?=$reg_date?></p>
		</div>
		<div class="view_content">
			<?=$content_html?>
		<div class="btn-list">
		<?php if(strlen($_ShopInfo->getMemid())==0) {?>
			<button type="button" class="btn-point" onClick="javascript:goLogin();">수정</button>
		<?php } elseif ( $_ShopInfo->getMemid() == $mem_id ) { ?>
			<button type="button" class="btn-point" onClick="javascript:location.href='?<?=$_SERVER['QUERY_STRING']?>&mode=modify';">수정</button>
		<?php } ?>
		<?php if(strlen($_ShopInfo->getMemid())==0) {?>
			<button type="button" class="btn-point" onClick="javascript:goLogin();">삭제</button>
		<?php } elseif ( $_ShopInfo->getMemid() == $mem_id ) { ?>
			<button type="button" class="btn-point" onClick="javascript:delete_photo_event_article('<?=$event_type?>', '<?=$idx?>', '<?=$view_type?>', '<?=$view_mode?>', '<?=$num?>', '<?=$isMobile?>');">삭제</a>
		<?php } ?>
			<button type="button" class="btn-def" id="photo_list_btn">목록</button>
		</div>
		</div>
	</section><!-- //.event_photo -->
	<!-- //포토 상세 -->

</article><!-- //.event_detail -->

<script type="text/javascript">
function delete_photo_event_article(event_type, idx, view_type, view_mode, num, is_mobile) {
    if ( confirm("정말로 삭제하시겠습니까?") ) {
        location.href = "/board/board_photo.php?event_type=<?=$event_type?>&promo_idx=<?=$idx?>&view_type=<?=$view_type?>&view_mode=<?=$view_mode?>&board=photo&pagetype=delete_photo&num=<?=$num?>&mode=delete&is_mobile=<?=$isMobile?>";
    }
}
</script>


