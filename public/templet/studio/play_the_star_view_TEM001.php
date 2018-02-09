<?php
    $view_mode = $_GET['view_mode'] ?: 'thumb';

    // ============================================================================
    // 게시물 조회
    // ============================================================================
    $special_no = 16;

    if ( $view_mode == "blog" ) {
        $sql  = "SELECT * FROM tblplaythestar WHERE hidden = 1 ";
        if ( isset($special_no) && !empty($special_no) ) {
            $sql .= "AND no <> {$special_no} ";
        }
        $sql .= " ORDER BY no desc ";
    } else {
        $sql  = "SELECT * FROM tblplaythestar WHERE hidden = 1 ORDER BY no desc ";
    }

    if ( $isMobile ) {
        $listnum = 5;
        $paging = new New_Templet_mobile_paging($sql, 5, $listnum, 'GoPage', true);
    } else {
        $listnum = 8;
        $paging = new New_Templet_paging($sql, 10, $listnum, 'GoPage', true);
    }
    $t_count = $paging->t_count;
    $gotopage = $paging->gotopage;

    $sql    = $paging->getSql($sql);
    $result = pmysql_query($sql);

    $list_html = '';
    while ($row = pmysql_fetch_array($result)) {
        // 등록일
        $reg_date = $row['regdate'];
        $reg_date = substr($reg_date, 0, 4) . "." . substr($reg_date, 4, 2) . "." . substr($reg_date, 6, 2);
        $thumbImg = getProductImage($Dir.DataDir."/shopimages/playthestar/", $row['img_m']);

		$sns_text	= "[".$_data->shoptitle."] PLAY THE STAR - ".addslashes($row['title']);

        if ( isset($special_no) && !empty($special_no) && $special_no == $row['no'] ) {
            if ( $isMobile ) {
                $linkUrl = "http://cash-stores.com/m/gallery/gallery_alexKim_intro.php";
            } else {
                $linkUrl = "http://cash-stores.com/front/alexkim_exhibition_intro.php";
            }
        } else {
            if ( $isMobile ) {
                $linkUrl = "/m/play_the_star_detail.php?id=" . $row['no'];
            } else {
                $linkUrl = "/front/play_the_star_detail.php?id=" . $row['no'];
            }
        }

        if ( $isMobile ) {
            $list_html .= '
                <li>
                    <a class="btn-detail" href="' . $linkUrl . '">
                        <figure>
                            <img src="' . $thumbImg . '" alt="">
                            <figcaption>' . $row['title'] . '</figcaption>
                        </figure>
                    </a>
                </li>
            ';
        } else {
            if ( $view_mode == "thumb" ) {
                $thumbImg = getProductImage($Dir.DataDir."/shopimages/playthestar/", $row['img']);

                $list_html .= '
                    <li>
                        <a href="' . $linkUrl . '">
                            <figure>
                                <img src="' . $thumbImg . '" alt="" width="366" height="247">
                                <figcaption>' . $row['title'] . '</figcaption>
                            </figure>
                        </a>
                    </li>';
            } else {

            $list_html .= '
                    <div class="board-view">
                        <p class="title">' . $row['title'] . ' <span class="date">' . $reg_date . '</span></p>
                        <div class="view-content">' . $row['content'] . '</div>
						<div class="sns-icon02">
							<a href="javascript:sns(\'kakao\',\''.$row['no'].'\',\''.$sns_text.'\')" class="facebook" id=\'kakaostory-share-button\'>카카오스토리</a>
							<a href="javascript:sns(\'facebook\',\''.$row['no'].'\',\''.$sns_text.'\')" class="instagram">페이스북</a>
							<a href="javascript:sns(\'twitter\',\''.$row['no'].'\',\''.$sns_text.'\')" class="twitter">트위터</a>
						</div>
                    </div>';
            }
        }
    }

    if ( $isMobile ) {
        include ($Dir.TempletDir."studio/mobile/play_the_star_view_TEM001.php");
    } else {

        if ( $view_mode == "thumb" ) {
            // 썸네일 리스트 형태
            include ($Dir.TempletDir."studio/play_the_star_view_thumb_TEM001.php");
        } else {
            // blog 형태
?>

<div id="contents">
        <div class="containerBody sub-page">

            <? include ($Dir.TempletDir."studio/navi_TEM001.php"); ?>
			<div class="board_list_tap">
            	<ul>
                	<li><a href="/front/play_the_star_view.php?view_mode=thumb">갤러리형</a></li>
                    <li class="on"><a href="/front/play_the_star_view.php?view_mode=blog">리스트형</a></li>
                </ul>
            </div>
            <div class="board-default-wrap">

                <?=$list_html?>

                <div class="list-paginate-wrap">
                    <div class="list-paginate">
                    <?=$a_div_prev_page.$paging->a_prev_page.$paging->print_page.$paging->a_next_page.$a_div_next_page?>
                    </div>
                </div>

            </div><!-- //.promotion-wrap -->

        </div><!-- //공통 container -->
    </div><!-- //contents -->

<?php } ?>

<form name=form2 method=get action="<?=$_SERVER['PHP_SELF']?>" >
    <input type=hidden name=idx value="<?=$idx?>">
    <input type=hidden name=listnum value="<?=$listnum?>">
    <input type=hidden name=block value="<?=$block?>">
    <input type=hidden name=gotopage value="<?=$gotopage?>">
</form>

<script src="//developers.kakao.com/sdk/js/kakao.min.js"></script>
<script type="text/javascript">
function chk_from() {
    if ( $("#keyword").val().trim() == "" ) {
        // 검색어가 없는 경우
        alert("검색어를 입력해 주세요.");
        $("#search_word").val("").focus();
        return false;
    }

    document.form2.block.value = "";
    document.form2.gotopage.value = "";
}

function GoPage(block,gotopage) {
    document.form2.block.value=block;
    document.form2.gotopage.value=gotopage;
    document.form2.submit();
}

// CASH 키로 교체해야함 - 임시키
Kakao.init('f98b9dc26d0fb025add7216b13cc2496');

function sns(select, pts_no, text){
	
	var Link_url = "http://<?=$_SERVER[HTTP_HOST]?>" + "/front/playthestar_sns/"+pts_no+".html";
	
	if(select =='facebook'){//페이스북
		var sns_url = "http://www.facebook.com/sharer.php?u="+encodeURIComponent(Link_url);
	}
	if(select =='twitter'){//트위터
		var sns_url = "http://twitter.com/intent/tweet?text="+encodeURIComponent(text)+"&url="+ Link_url + "&img" ;
	}
	if( select == 'kakao' ){

		Kakao.Story.share({
          url: Link_url,
          text: text
        });
		
	} else {
		var popup= window.open(sns_url,"_snsPopupWindow", "width=500, height=500");
		popup.focus();
	}
	
}

</script>

<?php } ?>

