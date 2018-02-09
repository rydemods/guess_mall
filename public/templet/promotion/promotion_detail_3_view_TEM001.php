<?php
    // ==================================================================================
    // 상단 배너
    // ==================================================================================
    $sql    = "SELECT * FROM tblpromo where idx = '{$idx}' ";
    $result = pmysql_query($sql);
    $row    = pmysql_fetch_object($result);

    $prm_title      = $row->title;
    $prm_start_date = str_replace("-", ".", $row->start_date);
    $prm_end_date   = str_replace("-", ".", $row->end_date);
    $prm_banner_img = $row->banner_img;
    $prm_banner_img_m = $row->banner_img_m;
    $prm_image_type     = $row->image_type;
    $prm_image_type_m   = $row->image_type_m;
    $prm_content        = $row->content;
    $prm_content_m      = $row->content_m;
	pmysql_free_result($result);

    // ==================================================================================
    // 게시물 내용
    // ==================================================================================

    // 전체 게시물 수
    $sql    = "select count(*) from tblboard_promo where board = 'photo' AND promo_idx = {$idx} ";
    $row    = pmysql_fetch_object(pmysql_query($sql));
    $total_comment_count = $row->count;

    $sql    = "select * from tblboard_promo where board = 'photo' AND promo_idx = '{$idx}' AND num = {$num} ";
    $row    = pmysql_fetch_object(pmysql_query($sql));

    $title = $row->title;
    $content = $row->content;
    $filename1 = $row->vfilename;
    $filename2 = $row->vfilename2;
    $filename3 = $row->vfilename3;
    $filename4 = $row->vfilename4;
    $mem_id = $row->mem_id;

    $reg_date = date("Y.m.d H:i:s", $row->writetime);

    if ( $isMobile ) {
        $content_html = '';

        if ( $content ) { $content_html .= '<p>' . nl2br($content) . '</p>'; }
        if ( $filename1 ) { $content_html .= '<img src="/data/shopimages/board/photo/' . $filename1 . '" alt=""><br/>'; }
        if ( $filename2 ) { $content_html .= '<img src="/data/shopimages/board/photo/' . $filename2 . '" alt=""><br/>'; }
        if ( $filename3 ) { $content_html .= '<img src="/data/shopimages/board/photo/' . $filename3 . '" alt=""><br/>'; }
        if ( $filename4 ) { $content_html .= '<img src="/data/shopimages/board/photo/' . $filename4 . '" alt=""><br/>'; }
    } else {
        if ( $content ) { $content_html .= '<p>' . nl2br($content) . '</p>'; }
        if ( $filename1 ) { $content_html .= '<img src="/data/shopimages/board/photo/' . $filename1 . '" alt=""><br/>'; }
        if ( $filename2 ) { $content_html .= '<img src="/data/shopimages/board/photo/' . $filename2 . '" alt=""><br/>'; }
        if ( $filename3 ) { $content_html .= '<img src="/data/shopimages/board/photo/' . $filename3 . '" alt=""><br/>'; }
        if ( $filename4 ) { $content_html .= '<img src="/data/shopimages/board/photo/' . $filename4 . '" alt=""><br/>'; }
    }

    // 이전/다음 링크용
    $view_more_html = GetPhotoEventViewMore();

    // 수정용
    $article_title      = $title;
    $article_content    = $content;
    $article_filename1  = $filename1;
    $article_filename2  = $filename2;
    $article_filename3  = $filename3;
    $article_filename4  = $filename4;

    if ( $isMobile ) {
        if ( empty($mode) ) {
            include($Dir.TempletDir."promotion/mobile/promotion_detail_3_view_TEM001.php");
        } else {
            include($Dir.TempletDir."promotion/mobile/promotion_detail_3_write_TEM001.php");
        }
    } else {
?>

<SCRIPT LANGUAGE="JavaScript" src="/board/chk_form.js.php"></SCRIPT>

<?php include($Dir.TempletDir."promotion/promotion_detail_3_upload_layer_TEM001.php"); ?>

<div id="contents">
	<div class="inner">
		<main class="event_wrap">
			<section class="event_view photo">
				<div class="subject">
					<p><?=$prm_title?></p>
					<p><?=$prm_start_date?> ~ <?=$prm_end_date?></p>
					<div class="sns_wrap">
						<ul>
							<li><a href="javascript:sns('facebook','<?=$sns_text?>')" class="facebook">facebook</a></li>
							<li><a href="javascript:sns('twitter','<?=$sns_text?>')" class="twitter">twitter</a></li>
							<li><a href="javascript:sns('band','<?=$sns_text?>')">BAND</a></li>
						</ul>
					</div>
				</div>

				<div class="event_content">
					<?php if ( $prm_image_type == "E" ) { ?>
					<?=$prm_content?>
					<?php } else { ?>
					<img src="/data/shopimages/timesale/<?=$prm_banner_img?>" alt="">
					<?php } ?>
				</div>

				<section class="photo_view">
					<p>등록된 포토<em>(<?=number_format($total_comment_count)?>)</em></p>
					<div class="subject">
						<p><?=$title?></p>
						<p><?=$reg_date?></p>
					</div>
					<div class="event_content">
						<?=$content_html?>
					</div>
					<div class="btn_wrap_list">
					<? if ($promo_status == 'Y') {?>
                    <?php if(strlen($_ShopInfo->getMemid())==0) {?>
						<a href="javascript:;" class="btn-type1 c1" onClick="javascript:goLogin();">수정</a>
                    <?php } elseif ( $mem_id === $_ShopInfo->getMemid() )  { ?>
						<a href="javascript:;" class="btn-type1 c1 photo-event-write btn-photo-write">수정</a>
                    <?php } ?>
					<?}?>
						<a href="javascript:;" class="btn-type1 c1" id="photo_list_btn">목록</a>
					</div>
				</section>

				<div class="btn_wrap">
					<a href="<?$Dir.FrontDir?>promotion.php?view_mode=<?=$view_mode?>&view_type=<?=$view_type?>" class="btn-type1">목록</a>
				</div>
			</section>
		</main>
	</div>
</div><!-- //#contents -->
<?
    }
?>
