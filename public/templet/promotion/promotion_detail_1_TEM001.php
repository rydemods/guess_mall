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
    $winner_list_content    = $row->winner_list_content;

    // ==================================================================================
    // 기획전 리스트
    // ==================================================================================
    $sql  = "SELECT * FROM tblpromotion ";
    $sql .= "WHERE promo_idx = '{$idx}' ";
//    $sql .= "WHERE promo_idx = '{$idx}' AND title <> '' ";
    $sql .= "ORDER BY display_seq asc "; // 노출순서 적용
    $result = pmysql_query($sql);

    $promotion_tab_html             = '';
    $promotion_tab_mobile_html      = '';

    $promotion_tablist_html         = '';
    $promotion_tablist_mobile_html  = '';

    while ($row = pmysql_fetch_array($result)) {
        $tab_name = "promotion-tab-" . $row['seq'];

        if ( !empty($row['title']) ) {
            $promotion_tab_html .= '<li><a href="javascript:;" tab_id="' . $tab_name . '" class="ctLink">' . $row['title'] . '</a></li>';
            $promotion_tab_mobile_html .= '<li><a href="#' . $tab_name . '" onclick="scroll_anchor($(this).attr(\'href\'));return false;">' . $row['title'] . '</a></li>';
        } else {
            $promotion_tab_html .= "";
            $promotion_tab_mobile_html .= "";
        }

        $sub_sql        = "SELECT * FROM tblspecialpromo WHERE special = '" . $row['seq'] . "' ";
        $sub_result     = pmysql_query($sub_sql);
        $sub_row        = pmysql_fetch_object($sub_result);
        $special_list   = $sub_row->special_list;

        $arrProdCode = explode(",", $special_list);
        $productcodes = "'" . implode("','", $arrProdCode) . "'";

        // 프로모션 상품 리스트
        $prod_sql  = "SELECT a.productcode, a.productname, a.sellprice, a.consumerprice, a.brand, a.maximage, a.minimage, a.tinyimage, ";
        $prod_sql .= "a.mdcomment, a.review_cnt, a.icon, a.soldout, a.quantity, a.over_minimage, ";
		$prod_sql .= "COALESCE(re.marks,0) AS marks, COALESCE(re.marks_total_cnt,0) AS marks_total_cnt
			, COALESCE((select COUNT( tl.hott_code )AS hott_cnt from tblhott_like tl WHERE tl.section = 'product' AND a.productcode = tl.hott_code),0) AS hott_cnt, li.section ";
        $prod_sql .= "FROM tblproduct a ";
		
		$prod_sql .= "LEFT JOIN (SELECT productcode, sum(marks) as marks,
									count(productcode) as marks_total_cnt
						FROM tblproductreview group by productcode) re on a.productcode = re.productcode ";
		$prod_sql .= "LEFT JOIN ( SELECT hott_code, section ,COUNT( hott_code )AS hott_cnt FROM tblhott_like WHERE section = 'product' AND like_id = '".$_ShopInfo->getMemid()."' GROUP BY hott_code, section ) li on a.productcode = li.hott_code ";
		$prod_sql .= "WHERE a.display = 'Y' and a.productcode in ( {$productcodes} ) ";

        if ( $isMobile ) {
            $arrProd = productlist_print($prod_sql, "W_018", $arrProdCode, count($arrProdCode));

            if ( !empty($row['title']) ) {
                $promotion_tablist_mobile_html .= '
						<div id="' . $tab_name . '" class="goods-list-item">
							<h4>' . $row['title'] . '</h4>
                            ' . $arrProd[0] . '
						</div><!-- //.goods-list-item -->';
            }
        } else {
            $arrProd = productlist_print($prod_sql, "W_011", $arrProdCode);

            if ( !empty($row['title']) ) {
                $promotion_tablist_html .= '
				<h4 id="' . $tab_name . '" class="category_name">' . $row['title'] . '</h4>
				<div class="brand-style-list">
					<ul class="comp-goods">
						' . $arrProd[0] . '
					</ul>
				</div>';
            }
        }
    }

    // 이전/다음 링크용
    //$view_more_html = GetPromotionViewMore($isMobile);
	$sns_text	    = "[".$_data->shoptitle."] 이벤트 - ".addslashes($title);
    $sns_thumb_img  = 'http://'.$_SERVER[HTTP_HOST].'/data/shopimages/timesale/'.$thumb_img;
?>

<?
    if ( $isMobile ) {
        include($Dir.TempletDir."promotion/mobile/promotion_detail_1_TEM001.php");
    } else {
?>
<div id="contents">
	<div class="inner">
		<main class="event_wrap">
			<section class="event_view product-list">
				<div class="subject">
					<p><?=$title?></p>
					<p><?=$start_date?> ~ <?=$end_date?></p>
					<div class="sns_wrap">
						<ul>
							<li><a href="javascript:sns('facebook','<?=$sns_text?>')" class="instagram">facebook</a></li>
							<li><a href="javascript:sns('twitter','<?=$sns_text?>')" class="twitter">twitter</a></li>
							<li><a href="javascript:sns('band','<?=$sns_text ?>');">blog</a></li>
						</ul>
					</div>
				</div>
				<div class="event_content">
					<?php 
					if ( $image_type == "E" ) { ?>
					<?=$content?>
					<?=$winner_list_content ?>
					<?php } else { ?>
					<img src="/data/shopimages/timesale/<?=$banner_img?>" alt="">
					<?=$winner_list_content ?>
					<?php } ?>
				</div>

                <?php if ( !empty($promotion_tab_html) ) { ?>

				<div class="category_tab">
					<ul class="clear">
                    <?=$promotion_tab_html?>
					</ul>
				</div>
                <?php } ?>

                <?=$promotion_tablist_html?>

				<div class="btn_wrap">
					<a href="javascript:;" id="list_btn" class="btn-type1">목록</a>
				</div>
			</section>
		</main>
	</div>
</div><!-- //#contents -->
<script>
$(document).ready(function(){
	$(".ctLink").click(function() {
		var tab_id	= $(this).attr('tab_id');
		var sc_top	= $('#'+tab_id).offset().top - 135;
		$('html,body').animate({scrollTop:sc_top},100); // 이동
	});

	//좋아요
	$(".btn-like").click(function() {
		var productCode = $(this).attr("ids");
		var likeType = $(this).attr("type");
		var memId = "<?=$_ShopInfo->getMemid()?>";

		
		if(memId != ""){		
			$.ajax({
				type: "POST",
				url: "product_like_proc.php",
				data: "code="+productCode+"&liketype="+likeType+"&section=product&page=product_list",
				dataType:"JSON"
			}).done(function(data){
				$("#like_pcount_"+productCode).html("<strong>좋아요</strong>"+data[0]['hott_cnt']);
				if(data[0]['section'] == "product"){		
					$("#like_"+productCode).attr("class","comp-like btn-like on");
					$("#like_"+productCode).attr("type","on");
				}else{
					$("#like_"+productCode).attr("class","comp-like btn-like");
					$("#like_"+productCode).attr("type","off");
				}			
			});
		}else{
			//로그인 상태가 아닐때 로그인 페이지로 이동
			var url = "../front/login.php?chUrl=/";
			$(location).attr('href',url);
		}		
	});
});
</script>
<? } ?>
