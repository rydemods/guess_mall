<?php
    // ==================================================================================
    // 상단 배너
    // ==================================================================================
    $sql    = "SELECT * FROM tblpromo where idx = '{$idx}' ";   
    $result = pmysql_query($sql);
    $row    = pmysql_fetch_object($result);

    $title                  = $row->title;
    $winner_list_content    = $row->winner_list_content;
    $start_date             = str_replace("-", ".", $row->start_date);
    $end_date               = str_replace("-", ".", $row->end_date);

    // 이전/다음 링크용
    $view_more_html = GetPromotionViewMore($isMobile);

if ( $isMobile ) { ?>

<?php include($Dir.TempletDir."promotion/mobile/promotion_navi_TEM001.php"); ?>

<!-- 프로모션 내용 -->
<article class="promo-detail-content">
    <div class="promo-title">
        <h3><strong><?=$title?></strong><span class="date"><?=$start_date?>~<?=$end_date?></span></h3>
        <!--button class="btn-share" onclick="popup_open('#popup-sns');return false;"><span class="ir-blind">공유</span></button-->
    </div>

    <div class="promo-content-inner">
        <?=$winner_list_content?>
    </div>
</article>
<!-- // 프로모션 내용 -->

<?=$view_more_html?>

<?php 
} else {
?>

<div id="contents">
    <div class="containerBody sub-page">
        
        <div class="breadcrumb">
            <ul>
                <li><a href="/">HOME</a></li>
                <li class="on"><a href="/front/promotion.php">PROMOTION</a></li>
            </ul>
        </div><!-- //.breadcrumb -->

        <div class="promotion-wrap">

            <div class="board-view">
                <p class="title"><?=$title?> <span class="date"><?=$start_date?>~<?=$end_date?></span></p>
                <div class="view-content">
                    <?=$winner_list_content?>
                </div>
            
            <?=$view_more_html?>

            <div class="btn-place-view"><button class="btn-dib-function" type="button" id="list_btn"><span>LIST</span></button></div>

        </div><!-- //.promotion-wrap -->

    </div><!-- //공통 container -->
</div><!-- //contents -->

<?php } ?>
