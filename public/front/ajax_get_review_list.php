<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
include_once($Dir."lib/paging_ajax.php");

$cate_code          = $_GET['cate_code'];
$listnum            = $_GET['listnum'];
$page               = $_GET['gotopage'];
$search_opt1        = $_GET['search_opt1'];
$search_opt2        = $_GET['search_opt2'];
$search_word        = $_GET['search_word'];
$listnum_comment    = $_GET['listnum_comment'];

// 댓글 리스트 조회
$sql  = "SELECT ";
$sql .= "   *, (SELECT brandname FROM tblproductbrand WHERE bridx = tblResult.brand) as brandname, ";
$sql .= "   (SELECT count(*) FROM tblproductreview_comment where pnum = tblResult.num) as comment_cnt ";
$sql .= "FROM ( ";
$sql .= "   SELECT a.*, c.productname, c.sellprice, c.brand, c.consumerprice, c.tinyimage ";
$sql .= "   FROM tblproductreview a ";
$sql .= "       LEFT JOIN tblproductlink b ON a.productcode = b.c_productcode ";
$sql .= "       LEFT JOIN tblproduct c ON a.productcode = c.productcode ";
$sql .= "   WHERE b.c_maincate = 1 and c_category like '" . $cate_code . "%' ";

if ( $search_opt1 === "photo_review" ) {
    // 포토리뷰인것만 조회
    $sql .= "   and a.type = '1' ";
} elseif ( $search_opt1 == "not_photo_review" ) {
    // 일반 텍스트형 리뷰인것만 조회
    $sql .= "   and a.type = '0' ";
} else {
    ; // do nothing
}

if ( $search_word !== "" ) {
    $sql .= "   and ( a.subject like '%" . $search_word . "%' or a.content like '%" . $search_word . "%' ) ";
}

$sql .= ") as tblResult ";

if ( $search_opt2 === "reg_date_desc" ) {
    // 최신 등록일 순으로 정렬
    $sql .= "ORDER BY num desc ";
} elseif ( $search_opt2 === "comment_cnt_desc" ) {
    // 댓글이 많은 순으로 정렬
    $sql .= "ORDER BY comment_cnt desc ";
} else {
    // 기본은 최신 등록일 순으로 정렬
    $sql .= "ORDER BY num desc ";
}

$paging = new amg_Paging($sql, 10, $listnum, 'GoPageAjax', $cate_code); 
$gotopage = $paging->gotopage;

$sql = $paging->getSql($sql); 

$htmlResult = '';
$review_result = pmysql_query($sql);
while ($review_row = pmysql_fetch_array($review_result)) {

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

        $htmlResult .= '
            <tr>
                <td><a href="/front/productdetail.php?productcode=' . $review_row['productcode'] . '#tab-product-review"><img src="' . $imgUrl . '" alt="" width="80" height="80"></a></td>
                <td>
                    <div class="price-info-box">
                        <p class="brand-nm">' . $review_row['brandname'] . '</p>
                        <p class="goods-nm">' . $review_row['productname'] . '</p>
                        <p class="price">';

        if ( $review_row['consumerprice'] != "0" ) {
            $htmlResult .= '<del>' . number_format($review_row['consumerprice']) . '</del>';
        }

        $htmlResult .= number_format($review_row['sellprice']) . '</p>
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

        $htmlResult .= '
            <tr class="open-content" style="display:none;">
                <td colspan="6">
                    <div class="review-content-open">
                        <div class="REVIEW_CONT">';

        $htmlResult .= "<p>" . nl2br($review_row['content']) . "</p>";
        foreach ( $arrUpFile as $key => $val ) {
            $htmlResult .= '<img src="/data/shopimages/review/' . $val . '"/>';
        }

        if ( $_ShopInfo->getMemid() == $review_row['id'] ) {
            $htmlResult .= '
                <div class="btn-place">
                    <button class="btn-dib-line " type="button" onclick="javascript:send_review_write_page(
                        \'' . $review_row['productcode'] . '\', 
                        \'' . $review_row['productname'] . '\', 
                        \'' . $review_row['subject'] . '\', 
                        \'' . rawurlencode($review_row['content']) . '\', 
                        \'' . $review_row['up_rfile'] . '\', 
                        \'' . $review_row['up_rfile2'] . '\', 
                        \'' . $review_row['up_rfile3'] . '\', 
                        \'' . $review_row['up_rfile4'] . '\', 
                        \'' . $review_row['marks'] . '\', 
                        \'' . $review_row['ordercode'] . '\', 
                        \'' . $review_row['productorder_idx'] . '\', 
                        \'' . $review_row['num'] . '\', 
                        \'' . $cate_code . '\');"><span>수정</span></button>
                    <button class="btn-dib-line " type="button" onclick="javascript:delete_review(\'' . $review_row['num'] . '\');"><span>삭제</span></button>
                </div>';

        }

        $htmlResult .= '</div>

						<div class="reply_wrap">
							<div class="reply-reg-box">
                                <form onSubmit="return false;">
                                <input type="hidden" name="pnum" value="' . $review_row['num'] . '" />

								<fieldset>
									<legend>리뷰에 댓글 작성</legend>
									<div class="textarea-cover"><textarea name="review_comment"></textarea></div>';

        if(strlen($_ShopInfo->getMemid())==0) {
            $htmlResult .= '            <button class="btn-reg" onClick="javascript:goLogin();">OK</button>';
        } else {
            $htmlResult .= '            <button class="btn-reg review-comment-write" type="submit" ids="0">OK</button>';
        }
	
        $htmlResult .= '           
								</fieldset>
                                </form>
							</div>
							<div class="reply_comment" id="reply_comment_' . $review_row['num'] . '">' . $review_comment_list_html . '
							</div>
						</div>';

        // 페이징
        $htmlResult .= '<div class="list-paginate-wrap">';
        $htmlResult .= '<div class="list-paginate" id="paging_' . $review_row['num'] . '">' . $comment_paging->a_prev_page . $comment_paging->print_page . $comment_paging->a_next_page . '</div>';
        $htmlResult .= '</div>';
        $htmlResult .= '</div><!-- //.review-tab-sub -->';

        $htmlResult .= '
                    </div>
                </td>
            </tr>';

}

$htmlResult .= '
<script type="text/javascript">

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

                    $(obj_comment).val(\'\');
                    GoPageAjax2(0, 0, $(frm).find("[name=\'pnum\']").val());
                } else {
                    alert("댓글 등록이 실패하였습니다.");
                }
            }).error(function () {
                alert("다시 시도해 주세요.");
            });
        });
    });
</script>

';


$htmlResult .= "|||" . $paging->a_prev_page . $paging->print_page . $paging->a_next_page;

echo $htmlResult;
?>
