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

	<!-- 댓글 -->
	<section class="reply_area">
    <form class="reply-reg-form" method=post name=comment_form action="/board/board.php" onSubmit="return chkCommentForm();">
        <input type=hidden name=pagetype value="promotion_comment_result">
        <input type=hidden name=board value="event">
        <input type=hidden name=num value="<?=$idx?>">
        <input type=hidden name=block value="<?=$block?>">
        <input type=hidden name=gotopage value="<?=$gotopage?>">
        <input type=hidden name=search value="<?=$search?>">
        <input type=hidden name=s_check value="<?=$s_check?>">
        <input type=hidden name=event_type value="<?=$event_type?>">
        <input type=hidden name=view_mode value="<?=$view_mode?>">
        <input type=hidden name=view_type value="<?=$view_type?>">
        <input type=hidden name=mode value="up">
        <input type=hidden name=is_mobile value="<?=$isMobile?>" >
        <input type=hidden id="messagebyte" value="0" >
        <!--input type=hidden name='up_name' id="inpt-name" title="작성자 입력자리">
        <input type=hidden name='up_passwd' id="inpt-pwd" title="비밀전호 입력자리">
        <input type="checkbox" id="inpt-check" name='up_is_secret' value='1' -->
		<h4>댓글<span class="count">(<?=number_format($total_comment_count)?>)</span></h4>

    <?
        $placeHolderMsg = "로그인 하셔야 작성이 가능합니다.";
        if ( strlen($_ShopInfo->getMemid()) > 0 ) {
            $placeHolderMsg = "댓글 작성 가능합니다.";
        }
    ?>
		<div class="box_reply_write">
			<textarea placeholder="<?=$placeHolderMsg?>" title="댓글" id="up_comment" name="up_comment" onKeyUp="checkByte(this.form);" onFocus="clearMessage(this.form);"></textarea>
			<button type="submit" class="btn-def">입력</button>
		</div>
		<ul class="reply_list">
			 <!-- <?=$review_html?> --><!-- [D] 댓글 퍼블리싱 확인 위해 임시 주석처리 -->
			 
			<!-- 댓글 (2016-09-13 추가) -->
			<li class="view_reply">
				<p class="info">rwo* (2016-05-05 15:22:23)</p>
				<p class="con">신발 예쁩니다. 단, 양말은 꼭 신고 신으세요. 뒷꿈치 다 나갑니다. 편하고 신발 가볍고 좋아요. 신발 예쁩니다. 뒷꿈치 다 나갑니다. 편하고 신발 가볍고 좋아요.신발 예쁩니다. 단, 양말은 꼭 신고 신으세요. 뒷꿈치 다 나갑니다. </p>
				<div class="clear">
					<div class="btn-feeling">
						<a href="#" class="btn-good-feeling on">15</a><!-- // [D] 버튼 선택시 on클래스 추가 -->
						<a href="#" class="btn-bad-feeling">0</a>
					</div>
					<div class="btn-set">
						<a href="javascript:void(0);" class="btn-function">삭제</a>
					</div>
				</div>
			</li>
			<!-- //댓글 -->

		</ul><!-- //.reply_list -->
    <?php
        if( $paging->pagecount > 0 ){
    ?>
		<div class="list-paginate mt-20">
			<?php echo $paging->a_prev_page.$paging->print_page.$paging->a_next_page; ?>
		</div><!-- //.list-paginate -->
    <?php
        }
    ?>

		<div class="btn-set"><button type="button" class="btn-def" onClick="javascript:location.href='<?=$Dir.MDir?>promotion.php?view_mode=<?=$view_mode?>&view_type=<?=$view_type?>'">이벤트 목록</button></div>
    </form>
	</section><!-- //.reply_area -->
	<!-- //댓글 -->

</article><!-- //.event_detail -->


