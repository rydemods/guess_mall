<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

?>

<?php
include ($Dir.MainDir.$_data->menu_type.".php");
$page_code = "benefit";
/* lnb 호출 */
/*
$page_code = "default";
$lnb_flag = 1;
include ($Dir.MainDir."lnb.php"); 
*/

$productcode        = $_POST['productcode'];
$ordercode          = $_POST['ordercode'];
$productorder_idx   = $_POST['productorder_idx'];
$review_num         = $_POST['review_num'];

$review_vote_msg    = "별점을 선택해 주세요.";
if ( $review_num > 0 ) {
    $review_sql  = "SELECT * FROM tblproductreview WHERE num = {$review_num} ";
    $review_row  = pmysql_fetch_object(pmysql_query($review_sql));
    //echo $review_sql;

    $review_vote_msg = "";
    for ( $i = 0; $i < $review_row->marks; $i++ ) {
//        $review_vote_msg .= "★";
        $review_vote_msg .= '<img src="/static/img/common/ico_star.png" />';
    }

/*
    for ( $i = $review_row->marks; $i < 5; $i++ ) {
        $review_vote_msg .= "☆";
    }
*/
}

// 상품 정보
$sql  = "SELECT *, b.brandname ";
$sql .= "FROM tblproduct a LEFT JOIN tblproductbrand b ON a.brand = b.bridx ";
$sql .= "WHERE a.productcode = '" . $productcode . "' ";
$row = pmysql_fetch_object(pmysql_query($sql));

$brandname = $row->brandname;
$prodname = $row->productname;

$product_title = "";
if ( !empty($brandname) ) {
    $product_title .= "[{$brandname}] ";
}
$product_title .= $prodname;

?>
<div id="contents" >
	<div class="containerBody sub-page" >
		
		<div class="breadcrumb">
			<ul>
				<li><a href="/">HOME</a></li>
				<li><a href="mypage.php">MY PAGE</a></li>
				<li class="on"><a>상품리뷰</a></li>
			</ul>
		</div>
		
		<!-- LNB -->
		<div class="left_lnb">
			<? include ($Dir.FrontDir."mypage_TEM01_left.php");?> 
		</div><!-- //LNB -->

		<div class="right_section mypage-content-wrap">

			<div class="my-reivew-write-form">
                <form id="review_write_form" name="review_write_form" method="post" enctype="multipart/form-data" onSubmit="return false;">
				<fieldset>
					<legend>상품 리뷰를 작성하기 위한 입력 테이블 폼</legend>
					<table class="th-left util">
						<caption>상품 리뷰작성 페이지</caption>
						<colgroup><col style="width:110px"><col style="width:auto"></colgroup>
						<tr>
							<th scope="row">구매상품</th>
							<td><?=strip_tags($product_title)?></td>
						</tr>
						<tr>
							<th scope="row">평가</th>
							<td>
								<div class="select small">
									<span class="ctrl"><span class="arrow"></span></span>
									<button type="button" class="my_value"><?=$review_vote_msg?></button>
									<ul class="a_list">
                                        <?php 
                                            for ( $i = 5; $i >= 1; $i-- ) {
                                                echo '<li><a href="javascript:;" class="REVIEW_VOTE" ids="' . $i . '">';
                                                for ( $j = 1; $j <= $i; $j++ ) {
                                                    echo '<img src="/static/img/common/ico_star.png" />';
                                                }
                                                echo '</a></li>';
                                            }
                                        ?>
										<!--li><a href="javascript:;" class="REVIEW_VOTE" ids="5"></a></li>
										<li><a href="javascript:;" class="REVIEW_VOTE" ids="4"></a></li>
										<li><a href="javascript:;" class="REVIEW_VOTE" ids="3"></a></li>
										<li><a href="javascript:;" class="REVIEW_VOTE" ids="2"></a></li>
										<li><a href="javascript:;" class="REVIEW_VOTE" ids="1"></a></li-->
									</ul>
                                    <input type="hidden" id="review_vote" name="review_vote" value="<?=$review_row->marks?>" />
								</div>
							</td>
						</tr>
						<tr>
							<th scope="row"><label for="review-title">제목</label></th>
							<td><input class="input-def w700" type="text" id="review-title" name="review_title" title="리뷰 제목 입력자리" value="<?=$review_row->subject?>"></td>
						</tr>
						<tr>
							<th scope="row">내용</th>
							<td>
								<textarea class="editor-size" id="review-content" name="review_content" cols="30" rows="10" placeholder="내용을 입력해 주세요." title="내용 입력자리"><?=$review_row->content?></textarea>
								<span class="att">※ 배송, 상품문의, 취소, 교환 등의 문의사항은 고객센터를 이용해 주시기 바랍니다. <br>상품평에 작성하시면 답변을 받지 못합니다.</span>
							</td>
						</tr>
						<tr>
							<th scope="row">파일첨부1</th>
							<td class="imageAdd">
                                <input type="file" id="add-image1" name="up_filename[]" accept="image/*">
                                <input type="hidden" name="v_up_filename[]" id="upfile" value="<?=$review_row->upfile?>">
								<div class="txt-box w500"><?=$review_row->up_rfile?></div>
								<label for="add-image1">찾아보기</label>
                                <label id="file_btn1">삭제</label>
							</td>
						</tr>
						<tr>
							<th scope="row">파일첨부2</th>
							<td class="imageAdd">
                                <input type="file" id="add-image2" name="up_filename[]" accept="image/*">
                                <input type="hidden" name="v_up_filename[]" id="upfile2" value="<?=$review_row->upfile2?>">
								<div class="txt-box w500"><?=$review_row->up_rfile2?></div>
								<label for="add-image2">찾아보기</label>
                                <label id="file_btn2">삭제</label>
							</td>
						</tr>
						<tr>
							<th scope="row">파일첨부3</th>
							<td class="imageAdd">
                                <input type="file" id="add-image3" name="up_filename[]" accept="image/*">
                                <input type="hidden" name="v_up_filename[]" id="upfile3" value="<?=$review_row->upfile3?>">
								<div class="txt-box w500"><?=$review_row->up_rfile3?></div>
								<label for="add-image3">찾아보기</label>
                                <label id="file_btn3">삭제</label>
							</td>
						</tr>
						<tr>
							<th scope="row">파일첨부4</th>
							<td class="imageAdd">
                                <input type="file" id="add-image4" name="up_filename[]" accept="image/*">
                                <input type="hidden" name="v_up_filename[]" id="upfile4" value="<?=$review_row->upfile4?>">
								<div class="txt-box w500"><?=$review_row->up_rfile4?></div>
								<label for="add-image4">찾아보기</label>
                                <label id="file_btn4">삭제</label>
								<span>파일명 : 한글,영문,숫자 / 파일용량 : 3MB이하 / 첨부기능 파일형식 : GIF,JPG(JPEG)</span>
							</td>
						</tr>
					</table>
					<div class="btn-place">
						<button class="btn-dib-function" id="btn_review_write"><span>확인</span></button>
						<a class="btn-dib-function line" onClick="javascript:cancel_btn();"><span>취소</span></a>
					</div>
				</fieldset>
                
                    <input type="hidden" name="productcode" id="productcode" value="" />
                    <input type="hidden" name="ordercode" id="ordercode" value="" />
                    <input type="hidden" name="productorder_idx" id="productorder_idx" value="" />
                    <input type="hidden" name="review_num" id="review_num" value="" />
				</form>
			</div>

		</div><!-- //.right_section -->

	</div>
</div>

<script type="text/javascript">

    $(document).ready(function() {

        $(".REVIEW_VOTE").on("click", function() {
            $("#review_vote").val($(this).attr("ids"));
        });

        // 파일 업로드 이벤트 
        $('input[type=file]').bind('change', function (e) {
            var fileName = $(this).val().split('\\').pop();
            $(this).parent().find("div").html(fileName);
        });

        // 리뷰작성 후 "WRITE"버튼을 클릭한 경우
        $("#btn_review_write").on("click", function() {
            var review_title    = $("#review-title").val().trim();
            var review_content  = $("#review-content").val().trim();
            var review_vote     = $("#review_vote").val();

            if ( chkReviewContentLength($("#review-content")[0]) === false ) {
                $("#review-content").focus();
            } else if ( review_title == "" ) {
                alert("제목을 입력해 주세요.");
                $("#review-title").val('').focus();
            } else if ( review_content == "" ) {
                alert("내용을 입력해 주세요.");
                $("#review-content").val('').focus();
            } else if ( review_vote == "" ) {
                alert("별점을 선택해 주세요.");
            } else {
                $("#productcode").val("<?=$productcode?>");             // 상품 코드
                $("#ordercode").val("<?=$ordercode?>");                 // 주문 코드
                $("#productorder_idx").val("<?=$productorder_idx?>");   // 주문 상품 idx
                $("#review_num").val("<?=$review_num?>");               // 리뷰 num

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
                        location.href = "/front/mypage_review.php";
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
        
        $('#file_btn1').click(function(){
            $(this).parent().parent().find("div").html('');
            $("#upfile").val("");

            $("#add-image1").replaceWith( $("#add-image1").clone(true) );
            $("#add-image1").val("");
        });

        $('#file_btn2').click(function(){
            $(this).parent().parent().find("div").html('');
            $("#upfile2").val("");

            $("#add-image2").replaceWith( $("#add-image2").clone(true) );
            $("#add-image2").val("");
        });

        $('#file_btn3').click(function(){
            $(this).parent().parent().find("div").html('');
            $("#upfile3").val("");

            $("#add-image3").replaceWith( $("#add-image3").clone(true) );
            $("#add-image3").val("");
        });

        $('#file_btn4').click(function(){
            $(this).parent().parent().find("div").html('');
            $("#upfile4").val("");

            $("#add-image4").replaceWith( $("#add-image4").clone(true) );
            $("#add-image4").val("");
        });
        
    });

    function cancel_btn() {
        if ( confirm("정말 취소하시겠습니까?") ) {
            history.go(-1);
        }
    }
</script>

<?php
include ($Dir."lib/bottom.php") 
?>

