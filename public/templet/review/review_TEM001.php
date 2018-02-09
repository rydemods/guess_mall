<?php

include_once('../lib/paging_ajax.php');
//include_once($Dir."conf/config.point.php");

$sql  = "select * from tblproductcode where code_b = '000' order by cate_sort asc ";
$result = pmysql_query($sql);

$listnum            = 10;
$listnum_comment    = 5;

$bFirst = true;

$firstCateCode = "";
$categoryHtml = "";
$arrTabSearchHtml = array();
$arrTabSubHtml = array();
while ($row = pmysql_fetch_array($result)) {
    $onClass = "";
    $displayOn = "none";
    if ( $bFirst ) {
        $bFirst = false;
        $onClass = "on";
        $displayOn = "block";

        $firstCateCode = $row['code_a'];
    }

    $categoryHtml .= "<li class='{$onClass}' ids='" . $row['code_a'] . "'>" . $row['code_name'] . "</li>";

    // 댓글 리스트 조회
    $sql  = "SELECT ";
    $sql .= "   *, (SELECT brandname FROM tblproductbrand WHERE bridx = tblResult.brand) as brandname, ";
    $sql .= "   (SELECT count(*) FROM tblproductreview_comment where pnum = tblResult.num) as comment_cnt ";
    $sql .= "FROM ( ";
    $sql .= "   SELECT a.*, c.productname, c.sellprice, c.brand, c.consumerprice, c.tinyimage ";
    $sql .= "   FROM tblproductreview a ";
    $sql .= "       LEFT JOIN tblproductlink b ON a.productcode = b.c_productcode ";
    $sql .= "       LEFT JOIN tblproduct c ON a.productcode = c.productcode ";
    $sql .= "   WHERE b.c_maincate = 1 and c_category like '" . $row['code_a'] . "%' ";
    $sql .= ") as tblResult ";
    $sql .= "ORDER BY num desc ";

    $paging = new amg_Paging($sql, 10, $listnum, 'GoPageAjax', $row['code_a']); 

    $gotopage = $paging->gotopage;
    $sql = $paging->getSql($sql);

    $tabSubHtml = '
                <div class="tab-sub" style="display:' . $displayOn . '">
                    <table class="th-top">
                        <caption>리뷰 목록</caption>
                        <colgroup><col style="width:162px"><col style="width:210px"><col style="width:230px"><col style="width:430px"><col style="width:75px"><col style="width:75px"></colgroup>
                        <thead>
                            <tr>
                                <th scope="col" colspan="2">상품정보</th>
                                <th scope="col">별점</th>
                                <th scope="col" colspan="3">내용</th>
                            </tr>
                        </thead>
                        <tbody id="tb_' . $row['code_a'] . '">';
    
    $sub_result = pmysql_query($sql);
    while ($review_row = pmysql_fetch_array($sub_result)) {

        $comment_paging = null;

        // 포토 리뷰인지 체크
        $photoClass = "";
        if ( $review_row['type'] == 1 ) {
            $photoClass = "photo";
        }

        // 리뷰 작성일자 구성
        $reg_date = $review_row['date'];
        $reg_date  = substr($reg_date, 0, 4) . "-" . substr($reg_date, 4, 2) . "-" . substr($reg_date, 6, 2) . " " . substr($reg_date, 8, 2) . ":" . substr($reg_date, 10, 2) . ":" . substr($reg_date, 12, 2);

        // 별점 
        $marks = '';
        for ($i = 0; $i < $review_row['marks']; $i++) {
            //$marks .= '★';
            $marks .= '<img src="/static/img/common/ico_star.png" />';
        }

        $imgUrl = getProductImage($Dir.DataDir.'shopimages/product/',$review_row['tinyimage']);

        $tabSubHtml .= '
            <tr>
                <td><a href="/front/productdetail.php?productcode=' . $review_row['productcode'] . '#tab-product-review"><img src="' . $imgUrl . '" alt="" width="80" height="80"></a></td>
                <td>
                    <div class="price-info-box">
                        <p class="brand-nm">' . $review_row['brandname'] . '</p>
                        <p class="goods-nm">' . $review_row['productname'] . '</p>
                        <p class="price">';

        if ( $review_row['consumerprice'] != "0" ) {
            $tabSubHtml .= '<del>' . number_format($review_row['consumerprice']) . '</del>';
        }

        $tabSubHtml .= number_format($review_row['sellprice']) . '</p>
                    </div>
                </td>
                <td class="star-point">' . $marks . '</td>
                <td class="review-subject" ids="' . $review_row['num'] . '">
                    <a>
                    <p class="' . $photoClass . '">' . $review_row['subject'] . '</p><!-- 포토 리뷰인경어 photo클래스 추가 -->
                    <p><span>ID : ' . setIDEncryp($review_row['id']) . '</span><span>' . $reg_date . '</span></p>
                    </a>
                </td>
                <td>조회 ' . $review_row['hit'] . '</td>
                <td>댓글 ' . $review_row['comment_cnt'] . '</td>
            </tr>';


        // 댓글이 하나 이상인 경우
        $review_comment_list_html = '';
        if ( $review_row['comment_cnt'] >= 1 ) {
            $review_comment_sql  = "SELECT * FROM tblproductreview_comment ";
            $review_comment_sql .= "WHERE pnum = " . $review_row['num'] . " ";
            $review_comment_sql .= "ORDER BY no desc ";

            $comment_paging = new amg_Paging($review_comment_sql, 10, $listnum_comment, 'GoPageAjax2', $review_row['num']); 

            $review_comment_sql = $comment_paging->getSql($review_comment_sql);
            $review_comment_result  = pmysql_query($review_comment_sql);
            
            while ($review_comment_row = pmysql_fetch_array($review_comment_result)) {
                $reg_date = $review_comment_row['regdt'];
//                $reg_date = substr($reg_date, 0, 4) . "-" . substr($reg_date, 4, 2) . "-" . substr($reg_date, 6, 2) . " " . substr($reg_date, 8, 2) . ":" . substr($reg_date, 10, 2) . ":" . substr($reg_date, 12, 2);
                $reg_date = substr($reg_date, 0, 4) . "-" . substr($reg_date, 4, 2) . "-" . substr($reg_date, 6, 2);


                $review_comment_list_html .= '
                    <ul>
                        <li class="data"><span class="id">' . $review_comment_row['id'] . '</span><span class="date">' . $reg_date . '</span></li>
                        <li>' . str_replace("\n", "<br/>", $review_comment_row['content']);

                if ( $review_comment_row['id'] == $_ShopInfo->getMemid() ) {
//                    $review_comment_list_html .= '<button class="btn-delete" onClick="javascript:delete_review_comment(this);" ids="' . $review_comment_row['no'] . '" ids2="' . $review_comment_row['pnum'] . '">X</button>';
                    $review_comment_list_html .= ' <a href="javascript:;" onClick="javascript:delete_review_comment(this);" ids="' . $review_comment_row['no'] . '" ids2="' . $review_comment_row['pnum'] . '"><img src="../static/img/btn/close.png" alt="닫기"></a>';
                }

                $review_comment_list_html .= '
                    </li>
                    </ul>';
            }
        }

        // 업로드 이미지 정보
        $arrUpFile = array();

        if ( !empty($review_row['upfile']) ) { array_push($arrUpFile, $review_row['upfile']); }
        if ( !empty($review_row['upfile2']) ) { array_push($arrUpFile, $review_row['upfile2']); }
        if ( !empty($review_row['upfile3']) ) { array_push($arrUpFile, $review_row['upfile3']); }
        if ( !empty($review_row['upfile4']) ) { array_push($arrUpFile, $review_row['upfile4']); }

        $tabSubHtml .= '
            <tr class="open-content" style="display:none;">
                <td colspan="6">
                    <div class="review-content-open">
                        <div class="REVIEW_CONT">';

        $tabSubHtml .= "<p>" . nl2br($review_row['content']) . "</p>";
        foreach ( $arrUpFile as $key => $val ) {
            $tabSubHtml .= '<img src="/data/shopimages/review/' . $val . '"/>';
        }

        if ( $_ShopInfo->getMemid() == $review_row['id'] ) {
            $tabSubHtml .= '
                <div class="btn-place">
                    <button class="btn-dib-line " type="button" onclick="javascript:send_review_write_page(
                        \'' . $review_row['productcode'] . '\', 
                        \'' . $review_row['productname'] . '\', 
                        \'' . rawurlencode($review_row['subject']) . '\', 
                        \'' . rawurlencode($review_row['content']) . '\', 
                        \'' . $review_row['up_rfile'] . '\', 
                        \'' . $review_row['up_rfile2'] . '\', 
                        \'' . $review_row['up_rfile3'] . '\', 
                        \'' . $review_row['up_rfile4'] . '\', 
                        \'' . $review_row['marks'] . '\', 
                        \'' . $review_row['ordercode'] . '\', 
                        \'' . $review_row['productorder_idx'] . '\', 
                        \'' . $review_row['num'] . '\', 
                        \'' . $row['code_a'] . '\');"><span>수정</span></button>
                    <button class="btn-dib-line " type="button" onclick="javascript:delete_review(\'' . $review_row['num'] . '\');"><span>삭제</span></button>
                </div>';

        }

        $tabSubHtml .= '</div>

						<div class="reply_wrap">
							<div class="reply-reg-box">
                                <form onSubmit="return false;">
                                <input type="hidden" name="pnum" value="' . $review_row['num'] . '" />

								<fieldset>
									<legend>리뷰에 댓글 작성</legend>
									<div class="textarea-cover"><textarea name="review_comment"></textarea></div>';

        if(strlen($_ShopInfo->getMemid())==0) {
            $tabSubHtml .= '            <button class="btn-reg" onClick="javascript:goLogin();">OK</button>';
        } else {
            $tabSubHtml .= '            <button class="btn-reg review-comment-write" type="submit" ids="0">OK</button>';
        }
	
        $tabSubHtml .= '           
								</fieldset>
                                </form>
							</div>
							<div class="reply_comment" id="reply_comment_' . $review_row['num'] . '">' . $review_comment_list_html . '
							</div>
						</div>';

        // 페이징
        $tabSubHtml .= '<div class="list-paginate-wrap">';
        $tabSubHtml .= '<div class="list-paginate" id="paging_' . $review_row['num'] . '">' . $comment_paging->a_prev_page . $comment_paging->print_page . $comment_paging->a_next_page . '</div>';
        $tabSubHtml .= '</div>';
        $tabSubHtml .= '</div><!-- //.review-tab-sub -->';

        $tabSubHtml .= '
                    </div>
                </td>
            </tr>';
    }

    $tabSubHtml .= '
                </tbody>
            </table><!-- //.th-top -->

        <div class="btn-place-view">';

    if(strlen($_ShopInfo->getMemid())==0) {
        $tabSubHtml .= '<button class="btn-dib-function" type="button" onClick="javascript:goLogin();"><span>WRITE</span></button>';
    } else {
        $tabSubHtml .= '<button class="btn-dib-function btn-write-pop" type="button" id="btn_write_popup_' . $row['code_a'] . '" onClick="javascript:document.review_write_form.mode.value=\'write\';"><span>WRITE</span></button>';
    }
    
    $tabSubHtml .= '</div>';

    // 페이징
    $tabSubHtml .= '<div class="list-paginate-wrap">';
    $tabSubHtml .= '<div class="list-paginate" id="paging_' . $row['code_a'] . '">' . $paging->a_prev_page . $paging->print_page . $paging->a_next_page . '</div>';
    $tabSubHtml .= '</div>';
    $tabSubHtml .= '</div><!-- //.review-tab-sub -->';

    array_push($arrTabSubHtml, $tabSubHtml);

    $tabSearchHtml = '
    <div class="half-align REVIEW_TAB_SUB_SEARCH" style="display:' . $displayOn . '">
        <div class="inner">
            
            <div class="select small">
                <span class="ctrl"><span class="arrow"></span></span>
                <button type="button" class="my_value"><sapn>ALL</span></button>
                <ul class="a_list">
                    <li><a href="javascript:;" class="SEARCH_OPTION1" ids="all_review">ALL</a></li>
                    <li><a href="javascript:;" class="SEARCH_OPTION1" ids="photo_review">PHOTO REVIEW</a></li>
                    <li><a href="javascript:;" class="SEARCH_OPTION1" ids="not_photo_review">TEXT REVIEW</a></li>
                </ul>
            </div>
            <div class="select small">
                <span class="ctrl"><span class="arrow"></span></span>
                <button type="button" class="my_value"><span>최근작성순</span></button>
                <ul class="a_list">
                    <li><a href="javascript:;" class="SEARCH_OPTION2" ids="reg_date_desc">최근작성순</a></li>
                    <li><a href="javascript:;" class="SEARCH_OPTION2" ids="comment_cnt_desc">댓글 많은순</a></li>
                </ul>
            </div>

        </div>

        <div class="inner">
            
            <div class="search-box-def">
                <form onSubmit="return false;">
                    <fieldset>
                        <legend>상품검색어 입력</legend>
                        <input type="text" title="검색어 입력자리" id="search_word_' . $row['code_a'] . '" name="search_word" value="">
                        <button type="submit" onClick="frm_submit();">검색하기</button>
                    </fieldset>
                </form>
            </div>

        </div>
    </div><!-- //.half-align -->';

    array_push($arrTabSearchHtml, $tabSearchHtml);
}

// 베스트 리뷰

// 기존
/*
$sql  = "SELECT tblResult.*, ";
$sql .= "(SELECT count(*) FROM tblproductreview_comment where pnum = tblResult.num) as comment_cnt ";
$sql .= "FROM ( ";
$sql .= "   SELECT a.*, b.num, b.marks, b.subject, b.content, b.id, b.hit, b.type ";
$sql .= "   FROM tblproduct a LEFT JOIN tblproductreview b ON a.productcode = b.productcode ";
$sql .= "   WHERE a.display = 'Y' and b.hit is not null ORDER BY b.hit desc limit 4 ";
$sql .= ") as tblResult";
*/

$sql  = "SELECT b.*, (SELECT count(*) FROM tblproductreview_comment where pnum = a.num) as comment_cnt, ";
$sql .= "a.num, a.marks, a.subject, a.content, a.id, a.hit, a.type, a.date, a.upfile, a.upfile2, a.upfile3, a.upfile4 ";
$sql .= "FROM tblproductreview a LEFT JOIN tblproduct b ON a.productcode = b.productcode ";
$sql .= "WHERE a.best_type = 1 ";
$sql .= "ORDER BY num desc ";
$sql .= "LIMIT 4 ";

$result = pmysql_query($sql);

$best_review_html = '';
$best_review_popup_html = '';
while ( $row = pmysql_fetch_array($result) ) {
    
    $comment_paging = null;

    $brand_name = brand_name($row['brand']);
    $comment_cnt = get_review_comment_count($row['num']);

    $marks = '';
    for ($i = 0; $i < $row['marks']; $i++) {
//        $marks .= '★';
        $marks .= '<img src="/static/img/common/ico_star.png" />';
    }

    // 기존꺼
/*
    $best_review_html .= '
            <li>
                <a href="javascript:;" ids="' . $row['num'] . '">
                    <figure>
                        <img src="/data/shopimages/product/' . $row['minimage'] . '" alt="">
                        <figcaption>
                            <span class="star-point">' . $marks . '</span>
                            <span class="subject">' . $row['subject'] . '</span>
                            <span class="content">' . str_replace("&nbsp;", "", $row['content']) . '</span>
                            <span class="id">ID : ' . $row['id'] . '</span>
                            <span class="hit">HIT : ' . number_format($row['hit']) . '</span>
                        </figcaption>
                    </figure>
                </a>
            </li>';
*/

    $thumbImgUrl = getProductImage($Dir.DataDir."shopimages/product/", $row['maximage']);
    $prodLinkUrl = "/front/productdetail.php?productcode=" . $row['productcode'] . "#tab-product-review";

    $photoReviewIcon    = "";
    $v_subject          = strcutMbDot2($row['subject'], 22);
    if ( $row['type'] == "1" ) {
        $photoReviewIcon    = '<img src="/static/img/icon/icon_photo.gif"/>';
        $v_subject          = strcutMbDot2($row['subject'], 16);
    }
    
    $best_review_html .= '
            <li>
                    <figure>
                        <a href="' . $prodLinkUrl . '" >
                        <img src="' . $thumbImgUrl . '" alt="" width="255" height="255"></a>
                        <figcaption>
                            <span class="star-point">' . $marks . '</span>
                            <a href="javascript:;" ids="' . $row['num'] . '">
                            <span class="subject">' . $v_subject . ' ' . $photoReviewIcon . '</span>
                            <span class="content">' . str_replace("&nbsp;", "", $row['content']) . '</span></a>
                            <span class="id">ID : ' . setIDEncryp($row['id']) . '</span>
                            <span class="hit">HIT : ' . number_format($row['hit']) . '</span>
                        </figcaption>
                    </figure>
            </li>
    ';

    // 포토 리뷰인지 체크
    $photoClass = "";
    if ( $row['type'] == 1 ) {
        $photoClass = "photo";
    }

    // 리뷰 작성일자 구성
    $reg_date = $row['date'];
    $reg_date  = substr($reg_date, 0, 4) . "-" . substr($reg_date, 4, 2) . "-" . substr($reg_date, 6, 2) . " " . substr($reg_date, 8, 2) . ":" . substr($reg_date, 10, 2) . ":" . substr($reg_date, 12, 2);

    // 댓글이 하나 이상인 경우
    $review_comment_list_html = '';
    if ( $row['comment_cnt'] >= 1 ) {
        $review_comment_sql  = "SELECT * FROM tblproductreview_comment ";
        $review_comment_sql .= "WHERE pnum = " . $row['num'] . " ";
        $review_comment_sql .= "ORDER BY no desc ";

        $comment_paging = new amg_Paging($review_comment_sql, 10, $listnum_comment, 'GoPageAjax2', $row['num']);    // 베스트 리뷰 페이징

        $review_comment_sql = $comment_paging->getSql($review_comment_sql);

        $review_comment_result  = pmysql_query($review_comment_sql);

        while ($review_comment_row = pmysql_fetch_array($review_comment_result)) {

            $regdt = $review_comment_row['regdt'];
//            $reg_date = substr($reg_date, 0, 4) . "-" . substr($reg_date, 4, 2) . "-" . substr($reg_date, 6, 2) . " " . substr($reg_date, 8, 2) . ":" . substr($reg_date, 10, 2) . ":" . substr($reg_date, 12, 2);
            $regdt = substr($regdt, 0, 4) . "-" . substr($regdt, 4, 2) . "-" . substr($regdt, 6, 2);

            $review_comment_list_html .= '
                <ul>
                    <li class="data"><span class="id">' . $review_comment_row['id'] . '</span><span class="date">' . $regdt . '</span></li>
                    <li>' . str_replace("\n", "<br/>", $review_comment_row['content']);


            if ( $review_comment_row['id'] == $_ShopInfo->getMemid() ) {        
//                $review_comment_list_html .= '<button class="btn-delete" onClick="javascript:delete_review_comment(this);" ids="' . $review_comment_row['no'] . '" ids2="' . $review_comment_row['pnum'] . '">X</button>';
                $review_comment_list_html .= ' <a href="javascript:;" onClick="javascript:delete_review_comment(this);" ids="' . $review_comment_row['no'] . '" ids2="' . $review_comment_row['pnum'] . '"><img src="../static/img/btn/close.png" alt="닫기"></a>';
            }

            $review_comment_list_html .= '</li>
                </ul>';

        }
        pmysql_free_result($review_comment_result);
    }

    $imgUrl = getProductImage($Dir.DataDir.'shopimages/product/',$row['minimage']);

    $best_review_popup_html .= '
	<div class="layer-dimm-wrap best-review-pop-open" style="display:none" id="best_review_pop_open_' . $row['num'] . '">
		<div class="dimm-bg"></div>
		<div class="layer-inner best-review-pop"> <!-- layer-class 부분은 width,height, - margin 값으로 구성되며 클래스명은 자유 -->
			<h3 class="layer-title"></h3>
			<button type="button" class="btn-close">창 닫기 버튼</button>
			<div class="layer-content js-scroll">

				<h5 class="title">BEST REVIEW</h5>

				<table class="th-top">
					<caption>리뷰 목록</caption>
					<colgroup><col style="width:100px"><col style="width:270px"><col style="width:auto"><col style="width:55px"><col style="width:55px"></colgroup>
					<tbody>
						<tr>
							<td><a href="javascript:;"><img class="img-size-mypage" src="' . $imgUrl . '" alt=""></a></td>
							<td>
								<div class="price-info-box">
									<p class="star-point">' . $marks . '</p>
									<p class="brand-nm">' . $brand_name . '</p>
									<p class="goods-nm w200">' . $row['productname'] . '</p>
									<p class="price">';

    if ( $row['consumerprice'] != "0" ) {
        $best_review_popup_html .= '<del>' . number_format($row['consumerprice']) . '</del>';
    }


    $best_review_popup_html .= number_format($row['sellprice']) . '</p>
								</div>
							</td>
							<td class="review-subject pop-td">
								<a href="#">
								<p class="' . $photoClass . '">' . $row['subject'] . '</p><!-- 포토 리뷰인경어 photo클래스 추가 -->
								<p><span>ID : ' . setIDEncryp($row['id']) . '</span><span>' . $reg_date . '</span></p>
								</a>
							</td>
							<td>조회 ' . number_format($row['hit']) . '</td>
							<td>댓글 ' . number_format($comment_cnt) . '</td>
						</tr>
						<tr class="content-view">
							<td colspan="5">
								<div class="review-content-open">';

        // 업로드 이미지 정보
        $arrUpFile = array();

        if ( !empty($row['upfile']) ) { array_push($arrUpFile, $row['upfile']); }
        if ( !empty($row['upfile2']) ) { array_push($arrUpFile, $row['upfile2']); }
        if ( !empty($row['upfile3']) ) { array_push($arrUpFile, $row['upfile3']); }
        if ( !empty($row['upfile4']) ) { array_push($arrUpFile, $row['upfile4']); }

        $best_review_popup_html .= '<div class="REVIEW_CONT">';        

        $best_review_popup_html .= "<p>" . str_replace("\n", "<br/>", $row[content]) . "<p/>";
        foreach ( $arrUpFile as $key => $val ) {
            $best_review_popup_html .= '<img src="/data/shopimages/review/' . $val . '"/>';
        }

        $best_review_popup_html .= '</div>';

        $best_review_popup_html .= '
									<div class="reply_wrap">
										<div class="reply-reg-box">
                                            <form onSubmit="return false;">
                                            <input type="hidden" name="pnum" value="' . $row['num'] . '" />
											<fieldset>
												<legend>리뷰에 댓글 작성</legend>
												<div class="textarea-cover"><textarea name="review_comment"></textarea></div>';

        if(strlen($_ShopInfo->getMemid())==0) {
            $best_review_popup_html .= '            <button class="btn-reg" onClick="javascript:goLogin();">OK</button>';
        } else {
            $best_review_popup_html .= '            <button class="btn-reg review-comment-write" type="submit" ids="1">OK</button>';
        }  
	
        $best_review_popup_html .= '		</fieldset>
                                            </form>
										</div>
										<div class="reply_comment" id="best_reply_comment_' . $row['num'] . '">' . $review_comment_list_html . '
										</div>
									</div>';

        // 페이징
        $best_review_popup_html .= '<div class="list-paginate-wrap">';
        $best_review_popup_html .= '<div class="list-paginate" id="best_paging_' . $row['num'] . '">' . $comment_paging->a_prev_page . $comment_paging->print_page . $comment_paging->a_next_page . '</div>';
        $best_review_popup_html .= '</div>';
        $best_review_popup_html .= '</div><!-- //.review-tab-sub -->';

        $best_review_popup_html .= '
								</div>
							</td>
						</tr>
					</tbody>
				</table><!-- //.th-top -->
				
			</div>
		</div>
	</div>';

}

?>

	<!-- 리뷰작성팝업 -->
	<div class="layer-dimm-wrap review-write">
		<div class="dimm-bg"></div>
		<div class="layer-inner review-write-reg"> <!-- layer-class 부분은 width,height, - margin 값으로 구성되며 클래스명은 자유 -->
			<h3 class="layer-title"></h3>
			<button type="button" class="btn-close">창 닫기 버튼</button>
			<div class="layer-content">

				<h5 class="title">REVIEW</h5>

                <form id="review_write_form" name="review_write_form" method="post" enctype="multipart/form-data">

				<table class="view-bbs-write" width="100%" summary="포토이벤트 등록">
					<caption>리뷰 등록</caption>
					<colgroup><col style="width:80px"><col style="width:auto"></colgroup>
					<tbody>
						<tr class="order-find">
							<th><label for="order-goods">주문상품</label></th>
							<td>
								<div class="select small" style="z-index:30;">
									<span class="ctrl"><span class="arrow"></span></span>
									<button type="button" class="my_value" id="order_prod_list_title">&nbsp;</button>
									<ul class="a_list" id="order_prod_list"></ul>
								</div>
							</td>
						</tr>
						<tr>
							<th>별점</th>
							<td>
								<div class="select small">
									<span class="ctrl"><span class="arrow"></span></span>
									<button type="button" class="my_value" id="review_vote_title"><span>별점을 선택해 주세요.</span></button>
									<ul class="a_list">
										<li><a href="javascript:;" class="REVIEW_VOTE" ids="5"><img src="../../static/img/common/ico_star5.png" alt="5점"/></a></li>
										<li><a href="javascript:;" class="REVIEW_VOTE" ids="4"><img src="../../static/img/common/ico_star4.png" alt="4점"/></a></li>
										<li><a href="javascript:;" class="REVIEW_VOTE" ids="3"><img src="../../static/img/common/ico_star3.png" alt="3점"/></a></li>
										<li><a href="javascript:;" class="REVIEW_VOTE" ids="2"><img src="../../static/img/common/ico_star2.png" alt="2점"/></a></li>
										<li><a href="javascript:;" class="REVIEW_VOTE" ids="1"><img src="../../static/img/common/ico_star1.png" alt="1점"/></a></li>
									</ul>
                                    <input type="hidden" id="review_vote" name="review_vote" value="" />
								</div>
							</td>
						</tr>
						<tr>
							<th><label for="review-title">제목</label></th>
							<td><input type="text" id="review-title" name="review_title"></td>
						</tr>
						<tr>
							<th><label for="review-content">내용</label></th>
							<td>
								<textarea id="review-content" name="review_content" cols="30" rows="10" placeholder="내용을 입력해 주세요." title="내용 입력자리"></textarea>
								<span>※ 배송,상품문의, 취소, 교환등의 문의사항은 고객센터를 이용해 주시기 바랍니다.상품평에 작성하시면 답변을 받지 못합니다. </span>
							</td>
						</tr>
						<tr>
							<th><label for="add-image1">이미지</label></th>
							<td class="imageAdd">
								<input type="file" id="add-image1" name="up_filename[]" accept="image/*">
                                <input type="hidden" id="file_exist" name="file_exist" value="N" />
								<div class="txt-box" id="add-image1-txt">&nbsp;</div>
								<label for="add-image1">찾아보기</label>
							</td>
						</tr>
						<tr>
							<th><label for="add-image2">이미지</label></th>
							<td class="imageAdd">
								<input type="file" id="add-image2" name="up_filename[]" accept="image/*">
                                <input type="hidden" id="file_exist" name="file_exist" value="N" />
								<div class="txt-box" id="add-image2-txt">&nbsp;</div>
								<label for="add-image2">찾아보기</label>
							</td>
						</tr>
						<tr>
							<th><label for="add-image3">이미지</label></th>
							<td class="imageAdd">
								<input type="file" id="add-image3" name="up_filename[]" accept="image/*">
                                <input type="hidden" id="file_exist" name="file_exist" value="N" />
								<div class="txt-box" id="add-image3-txt">&nbsp;</div>
								<label for="add-image3">찾아보기</label>
							</td>
						</tr>
						<tr>
							<th><label for="add-image4">이미지</label></th>
							<td class="imageAdd">
								<input type="file" id="add-image4" name="up_filename[]" accept="image/*">
                                <input type="hidden" id="file_exist" name="file_exist" value="N" />
								<div class="txt-box" id="add-image4-txt">&nbsp;</div>
								<label for="add-image4">찾아보기</label>
								<span>파일명 : 한글,영문,숫자 / 파일용량 : 3M이하 / 첨부기능 파일형식 : GIF,JPG(JPEG)</span>
							</td>
						</tr>
					</tbody>
				</table>

                    <input type="hidden" name="productcode" id="productcode" value="" />
                    <input type="hidden" name="productname" id="productname" value="" />
                    <input type="hidden" name="ordercode" id="ordercode" value="" />
                    <input type="hidden" name="productorder_idx" id="productorder_idx" value="" />
                    <input type="hidden" name="review_num" id="review_num" value="0" />
                    <input type="hidden" name="mode" id="mode" value="" />

                </form>

				<div class="btn-place"><button class="btn-dib-function" type="button" id="btn_review_write"><span>WRITE</span></button></div>
			</div>
		</div>
	</div><!-- //리뷰작성팝업 -->

	<!-- 베스트 리뷰 팝업창 -->
    <?=$best_review_popup_html?>
    <!-- //베스트 리뷰 팝업창 -->

	<div id="contents">
		<div class="containerBody sub-page">

			<div class="review-total-wrap">

				<div class="breadcrumb">
					<ul>
						<li><a href="#">HOME</a></li>
						<li class="on"><a href="/front/review.php">REVIEW</a></li>
					</ul>
				</div><!-- //.breadcrumb -->

<?php
// ===================================================================
// 상단 배너 
// ===================================================================
$sql  = "SELECT * FROM tblmainbannerimg ";
$sql .= "WHERE banner_no = 103 and banner_hidden = 1 ";
$sql .= "ORDER BY banner_sort asc LIMIT 1";
$result = pmysql_query($sql);

$topBannerImg       = "";
$topBannerLink      = "";
$topBannerTarget    = "";
while ($row = pmysql_fetch_array($result)) {
    $topBannerImg       = $row['banner_img'];
    $topBannerLink      = $row['banner_link'];
    $topBannerTarget    = $row['banner_target'];
}

if ( !empty($topBannerImg) ) {
        echo '<div class="best-review-banner">';
        if ( !empty($topBannerLink) ) {
            echo '<a href="' . $topBannerLink . '" target="' . $topBannerTarget . '">';
        }
        echo '  <img src="/data/shopimages/mainbanner/<?=$topBannerImg?>" alt="리뷰페이지 배너 영역">';
        if ( !empty($topBannerLink) ) {
            echo '</a>';
        }
        echo '</div>';
}
?>

				<span class="roof"></span>
				<h3 class="title">BEST REVIEW</h3>

				<div class="best-review">
					<ul>
                        <?=$best_review_html?>
					</ul>
				</div><!-- //.best-review -->

				<div class="category-tab-wrap">
					<div class="category-underline"></div>
					<ul class="category-tab">
                        <?=$categoryHtml?>
					</ul>
				</div><!-- //.category-tab-wrap -->

                <?php 
                    for ($i = 0; $i < count($arrTabSubHtml); $i++) { 
                        echo $arrTabSearchHtml[$i];
                        echo $arrTabSubHtml[$i];
                    } 
                ?>

				<dl class="attention">
					<dt>유의사항</dt>
					<dd>구매확정 후 90일 이내 리뷰를 작성하시면 마일리지가 적립됩니다.(일반리뷰 <?=number_format($pointSet['textr']['point'])?>M , 포토리뷰 <?=number_format($pointSet['photo']['point'])?>M)</dd>
					<dd>구매후기에 적합하지 않은 내용은 고객동의없이 비공개 처리 될 수 있으며, 지급 마일리지는 차감됩니다.</dd>
					<dd>취소/반품/교환의 경우 작성하신 후기 및 적립된 마일리지는 자동삭제, 차감됩니다.</dd>
				</dl>

			</div><!-- //.promotion-wrap -->

		</div><!-- //.containerBody -->
	</div><!-- //contents -->

<script type="text/javascript">
    var listnum             = '<?=$listnum?>';
    var listnum_comment     = '<?=$listnum_comment?>';
    var arrSearchOpt1       = new Array();              // 포토리뷰 / 텍스트리뷰
    var arrSearchOpt2       = new Array();              // 최근작성순 / 댓글많은순
    var arrSearchWord       = new Array();              // 검색어
    var current_cate_code   = "<?=$firstCateCode?>";

    // 좌상단 옵션1 변경
    $(".SEARCH_OPTION1").on("click", function() {
        arrSearchOpt1[current_cate_code] = $(this).attr("ids");
        GoPageAjax(0, 0, current_cate_code);
    });

    // 좌상단 옵션2 변경
    $(".SEARCH_OPTION2").on("click", function() {
        arrSearchOpt2[current_cate_code] = $(this).attr("ids");
        GoPageAjax(0, 0, current_cate_code);
    });

    // 탭을 클릭할때마다 현재 카테고리를 변경
    $(".category-tab > li").on("click", function() {
        current_cate_code = $(this).attr("ids");
    });

    $(".REVIEW_VOTE").on("click", function() {
        $("#review_vote").val($(this).attr("ids"));
    });

    // 리뷰작성 후 "WRITE"버튼을 클릭한 경우
    $("#btn_review_write").on("click", function() {
        var productcode         = $("#productcode").val().trim();
        var ordercode           = $("#ordercode").val().trim();
        var productorder_idx    = $("#productorder_idx").val().trim();

        var review_title        = $("#review-title").val().trim();
        var review_content      = $("#review-content").val().trim();
        var review_vote         = $("#review_vote").val();

        var chk_result = chkReviewContentLength($("#review-content")[0]);

        if ( chk_result === false ) {
            $("#review-content").focus();
        } else if ( productcode == "" ) {
            alert("주문상품을 선택해 주세요.");
        } else if ( review_title == "" ) {
            alert("제목을 입력해 주세요.");
            $("#review-title").val('').focus();
        } else if ( review_content == "" ) {
            alert("내용을 입력해 주세요.");
            $("#review-content").val('').focus();
        } else if ( review_vote == "" ) {
            alert("별점을 선택해 주세요.");
        } else {

            var fd = new FormData($("#review_write_form")[0]);  
            
            $.ajax({
                url: "ajax_insert_review.php",
                type: "POST",
                data: fd, 
                async: false,
                cache: false,
                contentType: false,
                processData: false,
            }).success(function(data){
                if ( data === "SUCCESS" ) {
                    alert("리뷰가 등록되었습니다.");

                    // 레이어를 닫고
                    $(".review-write").hide();

                    // 현재 리스트 refresh
                    GoPageAjax(0, 0, current_cate_code);
                } else {
                    var arrTmp = data.split("||");
                    if ( arrTmp[0] === "FAIL" ) {
                        alert(arrTmp[1]);
                    } else {
                        alert("리뷰가 등록이 실패하였습니다.");
                    }
                }
            }).error(function () {
                alert("다시 시도해 주세요.");
            });
        }
    });

    function goLogin() {
        <?php $url = $Dir.FrontDir."login.php?chUrl="; ?>
        if ( confirm("로그인이 필요합니다.") ) {
            location.href = "<?=$url?>" + encodeURIComponent('<?=$_SERVER['REQUEST_URI']?>');
        }
    }

    // 파일 업로드 이벤트 
    $('input[type=file]').bind('change', function (e) {
        var fileName = $(this).val().split('\\').pop();

        $(this).parent().find(".txt-box").html(fileName);
        $("#file_exist").val("Y");
    });

    // 검색어 입력시
    function frm_submit() {
        var search_word = $("#search_word_" + current_cate_code).val().trim();
        if ( search_word == "" ) { 
            alert("검색어를 입력해 주세요.");
            $("#search_word_" + current_cate_code).val("").focus();
            return false;
        }

        arrSearchWord[current_cate_code] = search_word;
        GoPageAjax(0, 0, current_cate_code);
    }


    $(document).ready(function() {

        // 리뷰에 댓글달기
        $(".review-comment-write").on("click", function() {

            var frm = $(this).parent().parent();            // form
            var obj_comment = $(frm).find("textarea");      // textarea

            var review_comment = $(obj_comment).val().trim();

            if ( review_comment == "" ) {
                alert("댓글을 입력해 주세요.");
                $(obj_comment).val("").focus();
                return false;
            }

            var fd = new FormData($(frm)[0]);  
            
            $.ajax({
                url: "ajax_insert_review_comment.php",
                type: "POST",
                data: fd, 
                async: false,
                cache: false,
                contentType: false,
                processData: false,
            }).success(function(data){
                if ( data === "SUCCESS" ) {
                    alert("댓글이 등록되었습니다.");

                    $(obj_comment).val('');
                    GoPageAjax2(0, 0, $(frm).find("[name='pnum']").val());
                } else {
                    alert("댓글 등록이 실패하였습니다.");
                }
            }).error(function () {
                alert("다시 시도해 주세요.");
            });
        });
    });

    function GoPageAjax(block,gotopage,cate_code) {
        var params = {
            block : block,
            gotopage : gotopage,
            listnum : listnum,
            listnum_comment : listnum_comment,
            cate_code : cate_code,
            search_word : arrSearchWord[current_cate_code],
            search_opt1 : arrSearchOpt1[current_cate_code],
            search_opt2 : arrSearchOpt2[current_cate_code]
        };

        $.ajax({
            type        : "GET", 
            url         : "ajax_get_review_list.php", 
            contentType : "application/x-www-form-urlencoded; charset=UTF-8",
            data        : params
        }).done(function ( data ) {
            var arrData = data.split("|||");

            $("#tb_" + cate_code).html(arrData[0]);
            $("#paging_" + cate_code).html(arrData[1]);

            resetReviewView();
        });
    }

    function GoPageAjax2(block,gotopage,review_num) {
        var params = {
            block : block,
            gotopage : gotopage,
            listnum : listnum_comment,
            review_num : review_num
        };

        $.ajax({
            type        : "GET", 
            url         : "ajax_get_review_comment_list.php", 
            contentType : "application/x-www-form-urlencoded; charset=UTF-8",
            data        : params
        }).done(function ( data ) {
            var arrData = data.split("|||");

            $("#best_reply_comment_" + review_num).html(arrData[0]);
            $("#best_paging_" + review_num).html(arrData[1]);
            $("#reply_comment_" + review_num).html(arrData[0]);
            $("#paging_" + review_num).html(arrData[1]);
        });
    }

    $(".btn-write-pop").on("click", function() {
        var frm = document.review_write_form;

        if ( frm.mode.value == "modify" ) {
            // 수정인 경우
            $("#order_prod_list_title").html("<span>" + frm.productname.value + "</span>");
            $("#review_vote_title").html("<span><img src=\"../../static/img/common/ico_star" + frm.review_vote.value + ".png\"></span>");
        } else {

            $("#order_prod_list_title").html("<span>상품을 선택해 주세요.</span>");
            $("#review_vote_title").html("<span>별점을 선택해 주세요.</span>");

            $("#review_write_form")[0].reset();
            $("#review_write_form > input").val("");
            $("#review_write_form > input[name='review_num']").val("0");

            $.ajax({
                type        : "GET", 
                url         : "ajax_get_order_product_list.php", 
                contentType : "application/x-www-form-urlencoded; charset=UTF-8"
            }).done(function ( data ) {
                var arrData = data.split("||");
                var arrItem;
                var productname = "";

                var li_html = "";
                for ( i = 0; i < arrData.length; i++ ) {
                    if ( arrData[i] === "" ) { continue; }

                    arrItem = arrData[i].split("^^");
                    productname = arrItem[3];

                    li_html += '<li><a href="javascript:;" onClick="javascript:review_order_prod_select(\'' + arrData[i] + '\');">' + productname + '</a></li>';
                }

                $("#order_prod_list").html(li_html);
            });
        }

    });


    function delete_review_comment(obj) {
        var review_comment_num = $(obj).attr("ids");
        var review_num = $(obj).attr("ids2");

        if ( review_comment_num != "" ) {
            if ( confirm("댓글을 삭제하시겠습니까?") ) {
                $.ajax({
                    type        : "GET", 
                    url         : "ajax_delete_review_comment.php", 
                    data        : { review_comment_num : review_comment_num }
                }).done(function ( result ) {
                    if ( result == "SUCCESS" ) {
                        alert("댓글이 삭제되었습니다.");

                        GoPageAjax2(0, 0, review_num);
                    } else {
                        alert("댓글이 삭제가 실패했습니다.");
                    }
                });
            }
        }
    }

    // 상품 선택시
    function review_order_prod_select(val) {
        var arrItem = val.split("^^");

        var ordercode = arrItem[0];
        var productcode = arrItem[1];
        var productorder_idx = arrItem[2];

        $("#ordercode").val(ordercode);                 // 주문 코드
        $("#productcode").val(productcode);             // 상품 코드
        $("#productorder_idx").val(productorder_idx);   // 주문 상품 idx
    }

    function send_review_write_page(
        productcode, 
        productname, 
        subject,
        content,
        up_rfile,
        up_rfile2,
        up_rfile3,
        up_rfile4,
        marks, 
        ordercode, 
        productorder_idx, 
        review_num, 
        cate_code) {

        if ( review_num == undefined ) {
            review_num = 0;
        }

        var frm = document.review_write_form;

        $("#order_prod_list_title").html("<span>" + frm.productname.value + "</span>");
        $("#review_vote_title").html("<span><img src=\"../../static/img/common/ico_star" + frm.review_vote.value + ".png\"></span>");
        $("#review-title").val(decodeURIComponent(subject));
        $("#review-content").val(decodeURIComponent(content));
        $("#add-image1-txt").text(up_rfile);
        $("#add-image2-txt").text(up_rfile2);
        $("#add-image3-txt").text(up_rfile3);
        $("#add-image4-txt").text(up_rfile4);

        frm.productcode.value = productcode;
        frm.productname.value = productname;
        frm.review_vote.value = marks;
        frm.ordercode.value = ordercode;
        frm.productorder_idx.value = productorder_idx;
        frm.review_num.value = review_num;
        frm.mode.value = "modify";

        // 리뷰 작성 팝업 띄우기
        $("#btn_write_popup_" + cate_code).trigger("click");
    }

    function delete_review(review_num) {
        if ( confirm("삭제하시겠습니까?") ) {
            $.ajax({
                type        : "GET", 
                url         : "ajax_delete_review.php", 
                contentType : "application/x-www-form-urlencoded; charset=UTF-8",
                data        : { review_num : review_num }
            }).done(function ( data ) {
                if ( data === "SUCCESS" ) {
                    alert("리뷰가 삭제되었습니다.");
                    location.reload();
                }
            });
        }
    }



</script>



