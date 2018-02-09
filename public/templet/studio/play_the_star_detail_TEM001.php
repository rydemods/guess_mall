<?php
    $id = $_GET['id'];

    // ============================================================================
    // 이전 다음 게시물 조회
    // ============================================================================
    $sql  = "SELECT prev, next, ";
    $sql .= "   (select title from tblplaythestar where no = tblResult.prev) as prev_title, ";
    $sql .= "   (select title from tblplaythestar where no = tblResult.next) as next_title ";
    $sql .= "FROM ( ";
    $sql .= "   SELECT ";
    $sql .= "       no, ";
    $sql .= "       lag(no) over (ORDER BY no desc ) as prev, ";
    $sql .= "       lead(no) over (ORDER BY no desc ) as next ";
    $sql .= "   FROM tblplaythestar ";
    $sql .= "   WHERE hidden = 1 ";
    $sql .= ") as tblResult ";
    $sql .= "WHERE no = {$id}";

    list($prev_no, $next_no, $prev_title, $next_title) = pmysql_fetch($sql);

    if ( $isMobile ) {
        $view_more_html  = '<div class="btnwrap promo-detail-btn">';
        $view_more_html .= '    <div class="box">';
        if ( $prev_no != "" ) {
            $view_more_html .= '    <a class="btn-def" href="/m/play_the_star_detail.php?id=' . $prev_no . '">이전</a>';
        }
        $view_more_html .= '<a class="btn-def" href="/m/play_the_star_view.php">목록</a>';
        if ( $next_no != "" ) {
            $view_more_html .= '    <a class="btn-def" href="/m/play_the_star_detail.php?id=' . $next_no. '">다음</a>';
        }
        $view_more_html .= '
                    </div>
                </div>';

    } else {
        $view_more_html = '<ul class="view-move">';
        if ( $prev_no != "" ) {
            $view_more_html .= '<li><span>이전글</span><a href="/front/play_the_star_detail.php?id=' . $prev_no . '">' . $prev_title . '</a></li>';
        }
        if ( $next_no != "" ) {
            $view_more_html .= '<li><span>다음글</span><a href="/front/play_the_star_detail.php?id=' . $next_no . '">' . $next_title . '</a></li>';
        }
        $view_more_html .= '</ul>';
    }


    // ============================================================================
    // 게시물 조회
    // ============================================================================
    $sql  = "SELECT * FROM tblplaythestar WHERE hidden = 1 AND no = {$id} ";
    $result = pmysql_query($sql);

    $list_html = '';
    while ($row = pmysql_fetch_array($result)) {
        // 등록일
        $reg_date = $row['regdate'];
        $reg_date = substr($reg_date, 0, 4) . "." . substr($reg_date, 4, 2) . "." . substr($reg_date, 6, 2);
        $thumbImg = getProductImage($Dir.DataDir."/shopimages/playthestar/", $row['img_m']);

		$sns_text	    = "[".$_data->shoptitle."] PLAY THE STAR - ".addslashes($row['title']);
        $sns_thumb_img  = 'http://'.$_SERVER[HTTP_HOST].'/data/shopimages/playthestar/'.$row['img_m'];

        if ( $isMobile ) {
			$pts_content	= $row['content'];
			if ($row['content_m'] != '') $pts_content	= $row['content_m'];
            $list_html .= '
                <article class="promo-detail-content">
                    <div class="promo-title">
                        <h3><strong>' . $row['title'] . '</strong></h3>
                        <button class="btn-share" onclick="popup_open(\'#popup-sns\');return false;"><span class="ir-blind">공유</span></button>
                    </div>
                    <div class="promo-content-inner">' . $pts_content . '</div>
                </article>';
            $list_html .= $view_more_html;

        } else {
            $list_html .= '
                <div class="board-view">
                    <div class="sns-icon02">
                        <a href="javascript:sns(\'kakao\',\''.$row['no'].'\',\''.$sns_text.'\')" class="facebook" id=\'kakaostory-share-button\'>카카오스
토리</a>
                        <a href="javascript:sns(\'facebook\',\''.$row['no'].'\',\''.$sns_text.'\')" class="instagram">페이스북</a>
                        <a href="javascript:sns(\'twitter\',\''.$row['no'].'\',\''.$sns_text.'\')" class="twitter">트위터</a>
                    </div>
                    <p class="title">' . $row['title'] . ' <span class="date">' . $reg_date . '</span></p>
                    <div class="view-content">' . $row['content'] . '</div>
                </div>';
        }
    }

    if ( $isMobile ) {
        include ($Dir.TempletDir."studio/mobile/play_the_star_detail_TEM001.php");
    } else {

?>

<div id="contents">
        <div class="containerBody sub-page">

            <? include ($Dir.TempletDir."studio/navi_TEM001.php"); ?>
			<div class="board_list_tap">
            	<ul>
                	<li class="on"><a href="/front/play_the_star_view.php?view_mode=thumb">갤러리형</a></li>
                    <li><a href="/front/play_the_star_view.php?view_mode=blog">리스트형</a></li>
                </ul>
            </div>
            <div class="board-default-wrap">
                <?=$list_html?>
            </div><!-- //.promotion-wrap -->

            <?=$view_more_html?>

            <div class="ta-c mt-30">
                <button class="btn-dib-function" type="button" id="list_btn" onclick="javascript:location.href='/front/play_the_star_view.php?view_mode=thumb';"><span>LIST</span></button>
            </div>
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

<?php if ( $isMobile ) { ?>

$(document).ready(function() {
    Kakao.init('f98b9dc26d0fb025add7216b13cc2496');

    Kakao.Link.createTalkLinkButton({
      container: '#kakao-link-btn',
      label: '<?=$sns_text?>',
      image: {
        src: '<?=$sns_thumb_img?>',
        width: '300',
        height: '200'
      },
      webButton: {
        text: '<?=$_data->shoptitle?>',
        url: 'http://<?=$_SERVER[HTTP_HOST].$_SERVER[REQUEST_URI]?>' // 앱 설정의 웹 플랫폼에 등록한 도메인의 URL이어야 합니다.
      }
    });
});

function sns(select, text){

    var Link_url = "http://<?=$_SERVER[HTTP_HOST].$_SERVER[REQUEST_URI]?>";

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

<?php } else { ?>

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

<?php } ?>

</script>


