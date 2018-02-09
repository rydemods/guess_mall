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

    // ==================================================================================
    // 게시물 리스트
    // ==================================================================================

    // 전체 게시물 수
/*
    $sql    = "select count(*) from tblboard_promo where board = 'photo' AND promo_idx = {$idx} ";
    $row    = pmysql_fetch_object(pmysql_query($sql));
    $total_comment_count = $row->count;
*/

    $sql    = "select * from tblboard_promo where board = 'photo' AND promo_idx = '{$idx}' order by num desc ";

    if ( $isMobile ) {
        $listnum = 6;   // 한 페이지에 나오는 댓글수
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
    $list_html = '';
    while ($row = pmysql_fetch_array($result)) {
        $reg_date = date("Y/m/d H:i:s", $row['writetime']);
        $arrRegDate = explode(" ", $reg_date);
        
        if ( $isMobile ) {
            $list_html .= '
					<li>
						<a href="?num=' . $row['num'] . '&' . $_SERVER['QUERY_STRING'] . '">
							<figure>
								<img src="/data/shopimages/board/photo/' . $row['vfilename'] . '" alt="">
								<figcaption>
									<p class="subject">' . $row['title'] . '</p>
									<p class="info">' . $arrRegDate[0] . '  <span class="bar">|</span>  ' . setIDEncryp($row['mem_id']) . '</p>
								</figcaption>
							</figure>
						</a>
					</li>';
        } else {
            $list_html .= '
						<li>
							<a href="?num=' . $row['num'] . '&' . $_SERVER['QUERY_STRING'] . '">
								<figure>
									<div class="img"><img src="/data/shopimages/board/photo/' . $row['vfilename'] . '" alt=""></div>
									<figcaption>
										<p class="list-subject">' . $row['title'] . '</p>
										<p class="list-date">' . $reg_date . '</p>
										<p class="list-name">' . setIDEncryp($row['mem_id']) . '</p>
									</figcaption>
								</figure>
							</a>
						</li>';
        }
    }

    // 이전/다음 링크용
    $view_more_html = GetPromotionViewMore($isMobile);  

    // 수정용
    $article_title      = "";
    $article_content    = "";
    $article_filename1  = "";
    $article_filename2  = "";
    $article_filename3  = "";
    $article_filename4  = "";

	$sns_text	    = "[".$_data->shoptitle."] 이벤트 - ".addslashes($title);
    $sns_thumb_img  = 'http://'.$_SERVER[HTTP_HOST].'/data/shopimages/timesale/'.$thumb_img;

    if ( $isMobile ) {
        if ( empty($mode) ) {
            include($Dir.TempletDir."promotion/mobile/promotion_detail_3_TEM001.php");
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
					<p><?=$title?></p>
					<p><?=$start_date?> ~ <?=$end_date?></p>
					<div class="sns_wrap">
						<ul>
							<li><a href="javascript:sns('facebook','<?=$sns_text?>')" class="facebook">facebook</a></li>
							<li><a href="javascript:sns('twitter','<?=$sns_text?>')" class="twitter">twitter</a></li>
							<li><a href="javascript:sns('band','<?=$sns_text?>')">BAND</a></li>
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

				<section class="photo-list-wrap">
					<p>등록된 포토<em>(<?=number_format($t_count)?>)</em></p>
					<ul class="photo-list">
                        <?=$list_html?>
					</ul><!-- //.reply-list -->
					<div class="list-paginate mt-40">
						<?=$a_div_prev_page.$paging->a_prev_page.$paging->print_page.$paging->a_next_page.$a_div_next_page?>
					</div><!-- //.list-paginate -->
					<? if ($promo_status == 'Y') {?>
					<div class="btn_wrap_list">
                    <?php if(strlen($_ShopInfo->getMemid())==0) {?>
                        <a href="javascript:;" onClick="javascript:goLogin();" class="btn-type1 c1">글쓰기</a>
                    <?php } else { ?>
                        <a href="javascript:;" class="btn-type1 c1 photo-event-write btn-photo-write">글쓰기</a>
                    <?php } ?>
					</div>
					<?}?>
				</section>

				<div class="btn_wrap">
					<a href="javascript:;" id="list_btn" class="btn-type1">목록</a>
				</div>
			</section>
		</main>
	</div>
</div><!-- //#contents -->

<?
    }
?>
