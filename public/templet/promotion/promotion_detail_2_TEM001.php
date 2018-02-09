<?php
    // ==================================================================================
    // 상단 배너
    // ==================================================================================
    $sql    = "SELECT * FROM tblpromo where idx = '{$idx}' ";   
    $result = pmysql_query($sql);
    $row    = pmysql_fetch_object($result);

    $title          = $row->title;
    $start_date     = str_replace("-", ".", $row->start_date);
    $end_date       = str_replace("-", ".", $row->end_date);
    $banner_img     = $row->banner_img;
    $banner_img_m   = $row->banner_img_m;
    $image_type     = $row->image_type;
    $image_type_m   = $row->image_type_m;
    $content        = $row->content;
    $content_m      = $row->content_m;
    $thumb_img      = $row->thumb_img;

    // 이전/다음 링크용
    $view_more_html = GetPromotionViewMore($isMobile);

    // ==================================================================================
    // 댓글 리스트
    // ==================================================================================

    // 전체 댓글 수
    $sql    = "select count(*) from tblboardcomment_promo where board = 'event' and parent = {$idx} ";
    $row    = pmysql_fetch_object(pmysql_query($sql));
    $total_comment_count = $row->count;

    $sql    = "select * from tblboardcomment_promo where board = 'event' and parent = {$idx} order by num desc ";

    if ( $isMobile ) {
        $listnum = 5;   // 한 페이지에 나오는 댓글수
        $paging = new New_Templet_mobile_paging($sql,3,$listnum,'GoPage',true);
    } else {
        $listnum = 8;   // 한 페이지에 나오는 댓글수
        $paging = new New_Templet_paging($sql, 10, $listnum, 'GoPage', true);
    }
    $t_count = $paging->t_count; 
    $gotopage = $paging->gotopage;

    $sql = $paging->getSql($sql);
    $result = pmysql_query($sql);

    $review_html = '';
    while ($row = pmysql_fetch_array($result)) {
        $reg_date = date("Y-m-d H:i:s", $row['writetime']);
        $arrRegDate = explode(" ", $reg_date);
        $comment = str_replace("\n", "<br/>", $row['comment']);

        if ( $isMobile ) {
            $review_html .= '
					<li>
						<p class="reply_info">' . $arrRegDate[0] . '  <span class="bar">|</span>  ' . setIDEncryp($row['c_mem_id']) . ' </p>
						<p class="reply_txt">' . $comment . ' </p>';

            if ( $_ShopInfo->getMemid() == $row['c_mem_id'] && $promo_status == 'Y') {
                $review_html .= '
						<div class="btn_area"><!-- [D] 본인이 쓴 댓글인 경우 삭제버튼 보이기 -->
							<button class="btn-function" type="button" onClick="javascript:delete_comment(\'event\', \'' . $row['num'] . '\');">삭제</button>
						</div>';
            }
        } else {

            if ( $_ShopInfo->getMemid() == $row['c_mem_id'] && $promo_status == 'Y') {
                $review_btn_add = '<button class="btn-line" type="button" onClick="javascript:delete_comment(\'event\', \'' . $row['num'] . '\');"><span>삭제</span></button>';
            }

            $review_html .= '
							<li>
								<div class="reply">
									<div class="btn">
										'.$review_btn_add.'
									</div>
									<p class="name">' . setIDEncryp($row['c_mem_id']) . ' (' . $arrRegDate[0] . ' ' . $arrRegDate[1] . ')</p>
									<div class="comment">
										<p>'.$comment.'</p>
									</div>
								</div><!-- //.reply -->
							</li>';
        }
    }
	$sns_text	    = "[".$_data->shoptitle."] 이벤트 - ".addslashes($title);
    $sns_thumb_img  = 'http://'.$_SERVER[HTTP_HOST].'/data/shopimages/timesale/'.$thumb_img;

    if ( $isMobile ) {
        include($Dir.TempletDir."promotion/mobile/promotion_detail_2_TEM001.php");
    } else {
?>

<div id="contents">
	<div class="inner">
		<main class="event_wrap">
			<section class="event_view reply">
				<div class="subject">
					<p><?=$title?></p>
					<p><?=$start_date?> ~ <?=$end_date?></p>
					<div class="sns_wrap">
						<ul>
							<li><a href="javascript:sns('facebook','<?=$sns_text?>')" class="facebook">facebook</a></li>
							<li><a href="javascript:sns('twitter','<?=$sns_text?>')" class="twitter">twitter</a></li>
							<li><a href="javascript:sns('band','<?=$sns_text?>');">blog</a></li>
						</ul>
					</div>
				</div>
				<div class="event_content">
					<?php if ( $image_type == "E" ) { ?>
					<?=$content?>
					<?php } else { ?>
					<img src="/data/shopimages/timesale/<?=$banner_img?>" alt="">
					<?php } ?>
				</div>
				<section class="reply-list">
					<p>댓글<em>(<?=number_format($total_comment_count)?>)</em></p>
					<div class="reply-inner">					
						<div class="reply-reg-box">
						<?if ($promo_status == 'Y') {?>
							<div class="box">
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
									<!--input type=hidden name='up_name' id="inpt-name" title="작성자 입력자리">
									<input type=hidden name='up_passwd' id="inpt-pwd" title="비밀전호 입력자리">
									<input type="checkbox" id="inpt-check" name='up_is_secret' value='1' --> 
									<fieldset>
										<legend>댓글 입력 창</legend>
										<textarea id="up_comment" name="up_comment" onKeyUp="checkByte(this.form);" onFocus="clearMessage(this.form);"></textarea>
										<button class="btn-reply-reg" type="submit"><span class="btn-type1">입력</span></button>
										<span class="byte hide"><strong id="messagebyte">0</strong>/300</span>
									</fieldset>
								</form>
							</div>
							<div class="text-area">
								<p>* 20자 이상 입력해 주세요.</p>
								<p>* 로그인후 작성하실 수 있습니다.</p>
							</div>
						<?}?>
						</div>
						<ul class="reply-list">
							<?=$review_html?>
						</ul><!-- //.reply-list -->
					</div>
					<div class="list-paginate mt-40">
							<?=$a_div_prev_page.$paging->a_prev_page.$paging->print_page.$paging->a_next_page.$a_div_next_page?>
					</div><!-- //.list-paginate -->
				</section>
				<div class="btn_wrap">
					<a href="javascript:;" id="list_btn" class="btn-type1">목록</a>
				</div>
			</section>
		</main>
	</div>
</div><!-- //#contents -->
<? } ?>

