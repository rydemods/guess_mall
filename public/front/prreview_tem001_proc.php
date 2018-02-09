<?php
//header("Content-Type: text/plain");
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
include_once($Dir."lib/paging_ajax.php");

include_once dirname(__FILE__)."/../lib/product.class.php";
$reviewlist = $_GET['setting_reviewlist'];
$productcode=$_REQUEST["productcode"];
$review_type = $_REQUEST['review_type'];

$qry = "WHERE a.productcode='{$productcode}' ";
if( $review_type == 'all' ) {
	$qry.= " ";
} else if( $review_type == 'poto' ){
	$qry.= " AND a.type = '1' ";
} else if( $review_type == 'text' ){
	$qry.= " AND a.type = '0' ";
}

//if($_data->review_type=="A") $qry.= "AND display='Y' ";
$sql = "SELECT COUNT(*) as t_count, SUM(a.marks) as totmarks FROM tblproductreview a ";
$sql.= $qry;
$result=pmysql_query($sql,get_db_conn());
$row=pmysql_fetch_object($result);
$t_count_review = (int)$row->t_count;
$totmarks = (int)$row->totmarks;
$marks=@ceil($totmarks/$t_count_review);
pmysql_free_result($result);
$paging = new amg_Paging($t_count_review,10,5,'GoPageAjax_Review');
$gotopage = $paging->gotopage;

$product = new PRODUCT();
#### 상품 리뷰 작성여부 ####

# 구매확정이 난 상품 체크
/*
$checkRevie_sql = "SELECT op.ordercode, op.idx FROM tblorderproduct op ";
$checkRevie_sql.= "JOIN tblorderinfo oi ON ( op.ordercode = oi.ordercode AND oi.id = '".$_ShopInfo->getMemid()."' ) ";
$checkRevie_sql.= " WHERE op.productcode = '".$productcode."' AND order_conf = '1' ";
*/

/*상품 구매했는지 여부 체크 원재*/
$isprreview = $product->isProductReview($productcode,$_ShopInfo->memid,$_POST['review_ordercode']);
//list($review_ordercode_cnt) = pmysql_fetch("select count(*) from tblorderproduct where productcode='".$productcode."'");
//list($review_ordercode_cnt) = pmysql_fetch( $checkRevie_sql );

# 리뷰 리스트를 불러온다
//$reviewlist = 'Y';
$sql  = "SELECT a.*, b.productname FROM tblproductreview a LEFT JOIN tblproduct b ON a.productcode = b.productcode ";
$sql .= "{$qry} ORDER BY a.date DESC, a.num DESC ";

$sql = $paging->getSql($sql);
$result=pmysql_query($sql,get_db_conn());
$j=0;
$reviewList = array();
while($row=pmysql_fetch_object($result)) {
	
	$reviewComment = array();

	$reviewList[$j]['idx'] = $row->num;
	$reviewList[$j]['num'] = $row->num;
	$reviewList[$j]['number'] = ($t_count_review-($setup['list_num'] * ($gotopage-1))-$j);
	$reviewList[$j]['id'] = $row->id;
	$reviewList[$j]['name'] = $row->name;
	$reviewList[$j]['subject'] = $row->subject;
	$reviewList[$j]['productcode'] = $row->productcode;
	$reviewList[$j]['productname'] = $row->productname;
	$reviewList[$j]['ordercode'] = $row->ordercode;
	$reviewList[$j]['productorder_idx'] = $row->productorder_idx;
	$reviewList[$j]['marks'] = $row->quality + 3;
	$reviewList[$j]['hit'] = $row->hit;
	$reviewList[$j]['type'] = $row->type;
	$reviewList[$j]['size'] = $row->size;
	$reviewList[$j]['foot_width'] = $row->foot_width;
	$reviewList[$j]['color'] = $row->color;
	$reviewList[$j]['quality'] = $row->quality;

	#마크 수에 따른 이미지 출력
	switch( $row->marks ){
		case '1' :
// 			$reviewList[$j]['marks_img'] = $Dir.'images/content/ico_star1.gif';
// 			$reviewList[$j]['marks_text'] = '별점1개';
// //			$reviewList[$j]['marks_sp'] = '★☆☆☆☆';
			break;
		case '2' :
// 			$reviewList[$j]['marks_img'] = $Dir.'images/content/ico_star2.gif';
// 			$reviewList[$j]['marks_text'] = '별점2개';
// //			$reviewList[$j]['marks_sp'] = '★★☆☆☆';
			break;
		case '3' :
// 			$reviewList[$j]['marks_img'] = $Dir.'images/content/ico_star3.gif';
// 			$reviewList[$j]['marks_text'] = '별점3개';
// //			$reviewList[$j]['marks_sp'] = '★★★☆☆';
			break;
		case '4' :
// 			$reviewList[$j]['marks_img'] = $Dir.'images/content/ico_star4.gif';
// 			$reviewList[$j]['marks_text'] = '별점4개';
// //			$reviewList[$j]['marks_sp'] = '★★★★☆';
			break;
		case '5' :
// 			$reviewList[$j]['marks_img'] = $Dir.'images/content/ico_star5.gif';
// 			$reviewList[$j]['marks_text'] = '별점5개';
// //			$reviewList[$j]['marks_sp'] = '★★★★★';
			break;
		default :
// 			$reviewList[$j]['marks_img'] = '';
// 			$reviewList[$j]['marks_text'] = '';
// //			$reviewList[$j]['marks_sp'] = '☆☆☆☆☆';
			break;
	}

    // 별표시하기
    $reviewList[$j]['marks_width'] = ($row->quality +3) * 20;
    $reviewList[$j]['marks_sp'] = $row->quality + 3;
	
	$reviewList[$j]['best_type'] = $row->best_type;

	$reviewList[$j]['upfile'] = $row->upfile;       // 첨부파일1
	$reviewList[$j]['upfile2'] = $row->upfile2;     // 첨부파일2
	$reviewList[$j]['upfile3'] = $row->upfile3;     // 첨부파일3
	$reviewList[$j]['upfile4'] = $row->upfile4;     // 첨부파일4
	$reviewList[$j]['upfile5'] = $row->upfile5;     // 첨부파일5

	$reviewList[$j]['up_rfile'] = $row->up_rfile;   // 첨부파일1(실제 업로드한 파일명)
	$reviewList[$j]['up_rfile2'] = $row->up_rfile2; // 첨부파일2(실제 업로드한 파일명)
	$reviewList[$j]['up_rfile3'] = $row->up_rfile3; // 첨부파일3(실제 업로드한 파일명)
	$reviewList[$j]['up_rfile4'] = $row->up_rfile4; // 첨부파일4(실제 업로드한 파일명)
	$reviewList[$j]['up_rfile5'] = $row->up_rfile5; // 첨부파일5(실제 업로드한 파일명)


	//exdebug($reviewList);
	$reviewList[$j]['date'] = substr($row->date,0,4).".".substr($row->date,4,2).".".substr($row->date,6,2);
	$reviewList[$j]['date'].= '&nbsp;'.substr($row->date,8,2).":".substr($row->date,10,2).":".substr($row->date,12,2);
	$reviewList[$j]['content'] = explode("=",$row->content);

	# 코멘트 가져오기
    $listnum_comment = 5;

	$comment_sql  = "SELECT no, id, name, content, regdt, pnum ";
    $comment_sql .= "FROM tblproductreview_comment ";
    $comment_sql .= "WHERE pnum = '".$row->num."' ";
    $comment_sql .= "ORDER BY no desc ";

    $comment_paging = new amg_Paging2($comment_sql, 10, $listnum_comment, 'GoPageAjax2', $row->num);
    $commentgotopage = $comment_paging->gotopage;
    $comment_sql = $comment_paging->getSql($comment_sql);

	$comment_res = pmysql_query( $comment_sql, get_db_conn() );
	while( $comment_row = pmysql_fetch_object( $comment_res ) ){
		$reviewComment[] = $comment_row;
	}
	pmysql_free_result( $comment_res );
	$reviewList[$j]['comment'] = $reviewComment;
	$reviewList[$j]['comment_count'] = $comment_paging->t_count;
	$reviewList[$j]['comment_paging'] = $comment_paging;
	$j++;
}
pmysql_free_result($result);
?>
<script type="text/javascript">
    var listnum_comment = "<?=$listnum_comment?>";

    function goLogin() {
        <?php $url = $Dir.FrontDir."login.php?chUrl="; ?>
        if ( confirm("로그인이 필요합니다.") ) {
            location.href = "<?=$url?>" + encodeURIComponent('<?=$_SERVER['REQUEST_URI']?>');
        }
    }

    function delete_review_comment(obj) {
        var review_comment_num = $(obj).attr("ids");
        var review_num = $(obj).attr("ids2");

        if ( review_comment_num != "" ) {
            if ( confirm("댓글을 삭제하시겠습니까?") ) {
                $.ajax({
                    type        : "GET", 
                    url         : "ajax_delete_review_comment.php", 
                    data        : { review_comment_num : review_comment_num, review_num : review_num }
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

    $(document).ready(function() {
    	var arrSize = [];
    	var arrFootWidth = [];
    	var arrColor = [];
    	var arrReviewQuality = [];
    	//4가지 평점 조회(사이즈, 발볼넓이, 색상, 품질/만족도)
    	$( ".CLS_reviewsize_idx" ).each(function( index) {
        	var idx = $( this ).val();
        	var size = $("#reviewSizeDetail_"+idx).val();
        	var foot_width = $("#reviewFootWidthDetail_"+idx).val();
        	var color = $("#reviewColorDetail_"+idx).val();
        	var review_quality = $("#reviewQualityDetail_"+idx).val();
        	arrSize.push(size);
        	arrFootWidth.push(foot_width);
        	arrColor.push(color);
        	arrReviewQuality.push(review_quality);
        	$('input:radio[name="review_size'+idx+'"][value="'+size+'"]').prop('checked', true);
        	$('input:radio[name="review_foot_width'+idx+'"][value="'+foot_width+'"]').prop('checked', true);
        	$('input:radio[name="review_color'+idx+'"][value="'+color+'"]').prop('checked', true);
        	$('input:radio[name="review_quality'+idx+'"][value="'+review_quality+'"]').prop('checked', true);

    	});

    	//평균 사이즈 평점
		$('input[name=aver_review_size]').each(function() {
		    if( this.value == parseInt(average(arrSize))){
				$(this).prop('checked', true);
		    }
		});
    	//평균 발볼넓이 평점
		$('input[name=aver_review_foot_width]').each(function() {
			 if(this.value == parseInt(average(arrFootWidth))){
		    	$(this).prop('checked', true);
		    }
		});
    	//평균 색상 평점
		$('input[name=aver_review_color]').each(function() {
			 if(this.value == parseInt(average(arrColor))){
		    	$(this).prop('checked', true);
		    }
		});
    	//평균 품질 평점
		$('input[name=aver_review_quality]').each(function() {
			 if(this.value == parseInt(average(arrReviewQuality))){
		    	$(this).prop('checked', true);
		    }
		});

        // 리뷰에 댓글달기
        $(".review-comment-write").on("click", function() {
            var frm = $(this).parent().parent();            // form
            var obj_comment = $(frm).find("textarea");      // textarea
            var review_comment = $(obj_comment).val().trim();
            var pnum = $(this).attr("idx");
        
         	if ( review_comment == "" ) {
                alert("댓글을 입력해 주세요.");
                $(obj_comment).val("").focus();
                return false;
            }
            var fd = new FormData($(frm)[0]);

            $.ajax({
                url: "ajax_insert_review_comment.php",
                type: "POST",
                data: "pnum="+pnum+"&review_comment="+review_comment
            }).success(function(data){
                //console.log(data);
                if ( data === "SUCCESS" ) {
                    alert("댓글이 등록되었습니다.");

                    $(obj_comment).val('');
                    GoPageAjax2(0, 0, pnum);
                } else {
                    alert("댓글 등록이 실패하였습니다.");
                }
            }).error(function () {
                alert("다시 시도해 주세요.");
            });
        });

        // 삭제 추가
//         $('#file_btn1').click(function(){
//             $("#add-image1-txt").text('');
//             $("#upfile").val("");

//             $("#add-image1").replaceWith( $("#add-image1").clone(true) );
//             $("#add-image1").val("");
//         });

//         $('#file_btn2').click(function(){
//             $("#add-image2-txt").text('');
//             $("#upfile2").val("");

//             $("#add-image2").replaceWith( $("#add-image2").clone(true) );
//             $("#add-image2").val("");
//         });

//         $('#file_btn3').click(function(){
//             $("#add-image3-txt").text('');
//             $("#upfile3").val("");

//             $("#add-image3").replaceWith( $("#add-image3").clone(true) );
//             $("#add-image3").val("");
//         });

//         $('#file_btn4').click(function(){
//             $("#add-image4-txt").text('');
//             $("#upfile4").val("");

//             $("#add-image4").replaceWith( $("#add-image4").clone(true) );
//             $("#add-image4").val("");
//         });

// 	     $('#file_btn5').click(function(){
// 	     	$("#add-image5-txt").text('');
// 	     	$("#upfile5").val("");
	
// 	     	$("#add-image5").replaceWith( $("#add-image5").clone(true) );
// 	     	$("#add-image5").val("");
// 	 	});

     });

	//리뷰 paging ajax
	function GoPageAjax_Review(block,gotopage) {
		gBlock = block;
		gGotopage = gotopage;
		var review_type = $('#review_type').val();
		$.ajax({
			type: "GET",
			url: "../front/prreview_tem001_proc.php",
			contentType: "application/x-www-form-urlencoded; charset=UTF-8",
			data: "productcode="+$("input[name='productcode']").val()+"&review_type="+review_type+"&block="+block+"&gotopage="+gotopage+"&setting_reviewlist="+$("#setting_reviewlist").val()
		}).done(function ( data ) {
			$("#boardCommentAjax").html(data);
		});
	}

    function GoPageAjax2(block,commentgotopage,review_num) {
        var params = {
            block : block,
            commentgotopage : commentgotopage,
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

            $("#reply_comment_" + review_num).html(arrData[0]);
            $("#paging_" + review_num).html(arrData[1]);
        });
    }

	//리뷰를 불러옴
	function AjaxReviewType( review_type ){
		$('#review_type').val( review_type );
		$.ajax({
			type: "GET",
			url: "../front/prreview_tem001_proc.php",
			contentType: "application/x-www-form-urlencoded; charset=UTF-8",
			data: "productcode="+$("input[name='productcode']").val()+"&review_type="+review_type+"&setting_reviewlist="+$("#setting_reviewlist").val()
		}).done(function ( data ) {
			$("#boardCommentAjax").html(data);
		});
	}
	//덧글 등록
	$(document).on( 'click', 'button[name="comment_submit"]', function( event ){
		var recomment = $(this).prev().find('textarea').val();
		var pnum = $(this).prev().find('textarea').attr( 'parentNum' );
		var inElement = $(this).parent().parent().parent();

		$.ajax({
			type: "POST",
			url: "../front/ajax_insert_review_comment.php",
			data : { review_comment : recomment, pnum : pnum }
		}).done( function ( data ) {
			if( data == 'SUCCESS' ){
				alert('덧글이 등록되었습니다.');
				inElement.before( '<div class="reply-reg-box">' + recomment + '</div>' );
			} else {
				alert( '덧글 등록이 실패하였습니다.' );
			}
		});
	});

    function send_review_write_page(
        productcode,
        productname,
        subject,
        content,
        up_rfile,
        up_rfile2,
        up_rfile3,
        up_rfile4,
        up_rfile5,
        upfile,
        upfile2,
        upfile3,
        upfile4,
        upfile5,
        marks,
        ordercode,
        productorder_idx,
        review_num,
        cate_code,
		size,
		foot_width,
		color,
		quality) {

        if (typeof review_num == undefined ) {
            review_num = 0;
        }

        var frm = document.reviewForm;
//             $("#order_prod_list_title").html("<span>" + frm.productname.value + "</span>");
        if(upfile != ""){
            var this_photo = "add-photo1";
            var up_file = "upfile";
            var imgpath = "http://<?=$_SERVER["HTTP_HOST"]?>/data/shopimages/review/"+upfile;
            var img = '<p style="background:url('+imgpath +') center no-repeat; background-size:contain"></p>';
            $("#add-photo1").prepend(img);
            $("#add-photo1").prepend('<button type="button" onClick="DeletePhoto(\''+this_photo+'\',\''+up_file+'\');">삭제</button>');
            $("#upfile").val(upfile);
        } 
        if(upfile2 != ""){
            var this_photo = "add-photo2";
            var up_file = "upfile2";
            var imgpath = "http://<?=$_SERVER["HTTP_HOST"]?>/data/shopimages/review/"+upfile2;
            var img = '<p style="background:url('+imgpath +') center no-repeat; background-size:contain"></p>';
            $("#add-photo2").prepend(img);
            $("#add-photo2").prepend('<button type="button" onClick="DeletePhoto(\''+this_photo+'\',\''+up_file+'\');">삭제</button>');
            $("#upfile2").val(upfile2);
        }       
        if(upfile3 != ""){
            var this_photo = "add-photo3";
            var up_file = "upfile3";
            var imgpath = "http://<?=$_SERVER["HTTP_HOST"]?>/data/shopimages/review/"+upfile3;
            var img = '<p style="background:url('+imgpath +') center no-repeat; background-size:contain"></p>';
            $("#add-photo3").prepend(img);
            $("#add-photo3").prepend('<button type="button" onClick="DeletePhoto(\''+this_photo+'\',\''+up_file+'\');">삭제</button>');
            $("#upfile3").val(upfile3);
        } 
        if(upfile4 != ""){
            var this_photo = "add-photo4";
            var up_file = "upfile4";
            var imgpath = "http://<?=$_SERVER["HTTP_HOST"]?>/data/shopimages/review/"+upfile4;
            var img = '<p style="background:url('+imgpath +') center no-repeat; background-size:contain"></p>';
            $("#add-photo4").prepend(img);
            $("#add-photo4").prepend('<button type="button" onClick="DeletePhoto(\''+this_photo+'\',\''+up_file+'\');">삭제</button>');
            $("#upfile4").val(upfile4);
        } 

        if(upfile5 != ""){
            var this_photo = "add-photo5";
            var up_file = "upfile5";
            var imgpath = "http://<?=$_SERVER["HTTP_HOST"]?>/data/shopimages/review/"+upfile5;
            var img = '<p style="background:url('+imgpath +') center no-repeat; background-size:contain"></p>';
            $("#add-photo5").prepend(img);
            $("#add-photo5").prepend('<button type="button" onClick="DeletePhoto(\''+this_photo+'\',\''+up_file+'\');">삭제</button>');
            $("#upfile5").val(upfile5);
        } 
        $("#review_title").val(decodeURIComponent(subject));
        $("#review_content").val(decodeURIComponent(content));

        frm.productcode.value = productcode;
        frm.productname.value = productname;
        //frm.review_vote.value = marks;
        frm.ordercode.value = ordercode;
        frm.productorder_idx.value = productorder_idx;
        frm.review_num.value = review_num;
        frm.mode.value = "modify";

        $('input:radio[name="review_size"][value="'+size+'"]').prop('checked', true);
		$('input:radio[name="review_color"][value="'+color+'"]').prop('checked', true);
		$('input:radio[name="review_foot_width"][value="'+foot_width+'"]').prop('checked', true);
		$('input:radio[name="review_quality"][value="'+quality+'"]').prop('checked', true);
        
        // 리뷰 작성 팝업 띄우기
        //$(".btn-review-layer").trigger("click");
        open_review_write_layer();
    }
    
    function open_review_write_layer() {
        var frm = document.reviewForm;
        if ( frm.mode.value == "modify" ) {
            // 수정인 경우
			$("#submit_type").text("수정");
			$("#review_submit").text("수정");
        } else {
        	$("#submit_type").text("작성");
        	$("#review_submit").text("등록");
            //$("#review_vote_title").html("<span>별점을 선택해 주세요.</span>");
            $("#reviewForm")[0].reset();
            $("#reviewForm > input[name='review_num']").val("0");
        }     

        $('.pop-review-detail').fadeIn();
    }

    function DeletePhoto(this_photo, up_file){

//         	console.log(up_file);
//         console.log(browser().version);
        $("#"+this_photo+"").find('p').remove();
        $("#"+this_photo+"").find('button').remove();
        if (parseInt(browser().version) > 0) {
            // ie 일때 input[type=file] init.
            $("#"+this_photo+"").find('input[type=file]').replaceWith( $("#"+this_photo+"").find('input[type=file]').clone(true) );
            $("#"+up_file+"").val("");
        } else {
            // other browser 일때 input[type=file] init.
            $("#"+this_photo+"").find('input[type=file]').val("");
            $("#"+up_file+"").val("");
        }
    }

    function browser() {
        var s = navigator.userAgent.toLowerCase();
        var match = /(webkit)[ \/](\w.]+)/.exec(s) ||
                /(opera)(?:.*version)?[ \/](\w.]+)/.exec(s) ||
                /(msie) ([\w.]+)/.exec(s) ||
                !/compatible/.test(s) && /(mozilla)(?:.*? rv:([\w.]+))?/.exec(s) || [];
        return { name: match[1] || "", version: match[2] || "0" };
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

<table class="board">
	<caption>리뷰를 작성해 주시면 핫티 온/오프라인 매장에서 사용가능한 포인트를 지급해 드립니다!!</caption>
	<colgroup>
		<col style="width:105px;">
		<col style="width:auto">
		<col style="width:190px;">
	</colgroup>
<?php
	if( count( $reviewList ) > 0 ) {
		foreach( $reviewList as $rKey=>$rVal ) {
			$number = ( $paging->t_count - ( $setup['list_num'] * ( $gotopage - 1 ) ) - $rKey );
?>
	<tbody>
		<tr class="btn-toggle">
			<td><span class="comp-star star-score"><strong style="width:<?=$rVal['marks_width']?>%;">5점만점에 <?=$rVal['marks_sp']?>점</strong></span></td>
			<td class="title" ids="<?=$rVal['idx']?>"><a href="javascript:void(0);"><?=$rVal['subject']?></a></td>
			<td class="name"><?=setIDEncryp($rVal['id'])?> (<?= substr($rVal['date'],0,10) ?>)</td>
		</tr>
		<tr>
			<td colspan="3">
				<div class="content">
                    <div class="cont-box">
                        <p>
                        <?php
                            echo nl2br($rVal['content'][0]) . "<br>";
                            if ( !empty($rVal['upfile']) ) { echo "<br><img src='" . $Dir.DataDir."shopimages/review/" . $rVal['upfile'] . "' />"; }
                            if ( !empty($rVal['upfile2']) ) { echo "<br><img src='" . $Dir.DataDir."shopimages/review/" . $rVal['upfile2'] . "' />"; }
                            if ( !empty($rVal['upfile3']) ) { echo "<br><img src='" . $Dir.DataDir."shopimages/review/" . $rVal['upfile3'] . "' />"; }
                            if ( !empty($rVal['upfile4']) ) { echo "<br><img src='" . $Dir.DataDir."shopimages/review/" . $rVal['upfile4'] . "' />"; }
                            if ( !empty($rVal['upfile5']) ) { echo "<br><img src='" . $Dir.DataDir."shopimages/review/" . $rVal['upfile5'] . "' />"; }
                        ?>
                        </p>
                    </div>

					<!-- [D] 20160909 리뷰평점 변경 -->
					<section class="wrap_select_rating">
						<div class="select_rating">
							<span>사이즈</span>
							<ul>
								<li>
									<div class="radiobox">
										<input type="radio" value="-2" name="review_size<?=$rVal['idx']?>" id="review_size<?=$rVal['idx']?>" >
										<label>작다</label>
									</div>
								</li>
								<li>
									<div class="radiobox">
										<input type="radio" value="-1" name="review_size<?=$rVal['idx']?>" id="review_size<?=$rVal['idx']?>"  >
										<label class="none">조금 작다</label>
									</div>
								</li>
								<li>
									<div class="radiobox">
										<input type="radio" value="0" name="review_size<?=$rVal['idx']?>" id="review_size<?=$rVal['idx']?>" >
										<label>적당함</label>
									</div>
								</li>
								<li>
									<div class="radiobox">
										<input type="radio" value="1" name="review_size<?=$rVal['idx']?>" id="review_size<?=$rVal['idx']?>"  >
										<label class="none">조금 크다</label>
									</div>
								</li>
								<li>
									<div class="radiobox">
										<input type="radio" value="2" name="review_size<?=$rVal['idx']?>" id="review_size<?=$rVal['idx']?>" >
										<label>크다</label>
									</div>
								</li>
							</ul>
						</div>
						<div class="select_rating">
							<span>발볼 넓이</span>
							<ul>
								<li>
									<div class="radiobox">
										<input type="radio" value="-2" name="review_foot_width<?=$rVal['idx']?>">
										<label>작다</label>
									</div>
								</li>
								<li>
									<div class="radiobox">
										<input type="radio" value="-1" name="review_foot_width<?=$rVal['idx']?>">
										<label class="none">조금 작다</label>
									</div>
								</li>
								<li>
									<div class="radiobox">
										<input type="radio" value="0" name="review_foot_width<?=$rVal['idx']?>">
										<label>적당함</label>
									</div>
								</li>
								<li>
									<div class="radiobox">
										<input type="radio" value="1" name="review_foot_width<?=$rVal['idx']?>">
										<label class="none">조금 크다</label>
									</div>
								</li>
								<li>
									<div class="radiobox">
										<input type="radio" value="2" name="review_foot_width<?=$rVal['idx']?>">
										<label>크다</label>
									</div>
								</li>
							</ul>
						</div>
						<div class="select_rating">
							<span>색상</span>
							<ul>
								<li>
									<div class="radiobox">
										<input type="radio" value="-2" name="review_color<?=$rVal['idx']?>">
										<label>어둡다</label>
									</div>
								</li>
								<li>
									<div class="radiobox">
										<input type="radio" value="-1" name="review_color<?=$rVal['idx']?>">
										<label class="none">조금 어둡다</label>
									</div>
								</li>
								<li>
									<div class="radiobox">
										<input type="radio" value="0" name="review_color<?=$rVal['idx']?>">
										<label>화면과 같다</label>
									</div>
								</li>
								<li>
									<div class="radiobox">
										<input type="radio" value="1" name="review_color<?=$rVal['idx']?>">
										<label class="none">조금 밝다</label>
									</div>
								</li>
								<li>
									<div class="radiobox">
										<input type="radio" value="2" name="review_color<?=$rVal['idx']?>">
										<label>밝다</label>
									</div>
								</li>
							</ul>
						</div>
						<div class="select_rating">
							<span>품질/만족도</span>
							<ul>
								<li>
									<div class="radiobox">
										<input type="radio" value="-2" name="review_quality<?=$rVal['idx']?>">
										<label>불만</label>
									</div>
								</li>
								<li>
									<div class="radiobox">
										<input type="radio" value="-1" name="review_quality<?=$rVal['idx']?>">
										<label class="none">조금 불만</label>
									</div>
								</li>
								<li>
									<div class="radiobox">
										<input type="radio" value="0" name="review_quality<?=$rVal['idx']?>">
										<label>보통</label>
									</div>
								</li>
								<li>
									<div class="radiobox">
										<input type="radio" value="1" name="review_quality<?=$rVal['idx']?>">
										<label class="none">조금 만족</label>
									</div>
								</li>
								<li>
									<div class="radiobox">
										<input type="radio" value="2" name="review_quality<?=$rVal['idx']?>">
										<label>만족</label>
									</div>
								</li>
							</ul>
						</div>
					</section>
					<!-- //[D] 20160909 리뷰평점 변경 -->
					<input type="hidden" class="CLS_reviewsize_idx" value="<?=$rVal['idx'] ?>" />
					<input type="hidden" id="reviewSizeDetail_<?=$rVal['idx'] ?>" value="<?=$rVal['size'] ?>" />
					<input type="hidden" id="reviewFootWidthDetail_<?=$rVal['idx'] ?>" value="<?=$rVal['foot_width'] ?>" />
					<input type="hidden" id="reviewColorDetail_<?=$rVal['idx'] ?>" value="<?=$rVal['color'] ?>" />
					<input type="hidden" id="reviewQualityDetail_<?=$rVal['idx'] ?>" value="<?=$rVal['quality'] ?>" />
					<?
                    if ( $_ShopInfo->getMemid() == $rVal['id'] ) {?>
						<div class="buttonset">
							<a href="javascript:;" onclick="javascript:send_review_write_page('<?=$rVal['productcode'] ?>','<?=$rVal['productname'] ?>','<?=rawurlencode($rVal['subject']) ?>'
																								,'<?=rawurlencode($rVal['content'][0])  ?>','<?=$rVal['up_rfile'] ?>','<?=$rVal['up_rfile2'] ?>','<?=$rVal['up_rfile3'] ?>','<?=$rVal['up_rfile4'] ?>'
																								,'<?=$rVal['up_rfile5'] ?>','<?=$rVal['upfile'] ?>','<?=$rVal['upfile2'] ?>','<?=$rVal['upfile3'] ?>','<?=$rVal['upfile4'] ?>','<?=$rVal['upfile5'] ?>'
																								,'<?=$rVal['marks'] ?>','<?=$rVal['ordercode'] ?>','<?=$rVal['productorder_idx'] ?>','<?=$rVal['num'] ?>','<?=$rVal['code_a'] ?>' )"<span>수정</span></a>
							<a href="javascript:;" onclick="javascript:delete_review('<?=$rVal['num'] ?>');">삭제</a>
						</div>
                  <? }?>
				</div>
				<div class="reply_wrap">
					<div class="reply-reg-box">
						<form onsubmit="return false;">
                        <input type="hidden" name="pnum" value="<?=$rVal['idx']?>">
							<legend>리뷰에 댓글 작성</legend>
							<div class="review_comment_form"><textarea name="review_comment"></textarea>
							<?php if(strlen($_ShopInfo->getMemid())==0) { ?>
								<div class="btn_review_write"><a href="javascript:goLogin();">입력</a></div>
							<?php } else { ?>
								<div class="btn_review_write review-comment-write" idx="<?=$rVal['idx']?>"><a href="javascript:;">입력</a></div>
<!-- 								<center><button class="btn-type1 review-comment-write" type="submit">OK</button></center> -->
							<?php } ?>
							</div>
						</form>
					</div>
			    <div class="reply_comment" id="reply_comment_<?=$rVal['idx']?>">
<?php
			if( count( $rVal['comment'] ) > 0 ){
				foreach( $rVal['comment'] as $commentKey=>$commentVal ){?>
				<div class="answer">
					<span class="name"><?=setIDEncryp($commentVal->id)?>(<?=substr($commentVal->regdt,0,4)."-".substr($commentVal->regdt,4,2)."-".substr($commentVal->regdt,6,2) ?>)</span>
					<?if ( $commentVal->id == $_ShopInfo->getMemid() ) { ?>
                    	<div class="btn_delete"> <a class="btn-delete" href="javascript:;" onClick="javascript:delete_review_comment(this);" ids="<?= $commentVal->no?>" ids2="<?=$commentVal->pnum ?>">삭제</a></div>';
                  <? }?>
					<p><?=$commentVal->content ?></p>
					<!-- [D] 호감/비호감 버튼 추가 -->
					<div class="btn-feeling mt-5">
						<a class="btn-good-feeling" href="javascript:select_feeling('<?= $commentVal->no?>','product_review_comment','good','<?=$_ShopInfo->getMemid() ?>');" id="feeling_good_product_review_comment_<?= $commentVal->no?>"><?=totalFeeling($commentVal->no, 'product_review_comment', 'good') ?></a>
						<a class="btn-bad-feeling" href="javascript:select_feeling('<?= $commentVal->no?>','product_review_comment','bad','<?=$_ShopInfo->getMemid() ?>');" id="feeling_bad_product_review_comment_<?= $commentVal->no?>"><?=totalFeeling($commentVal->no, 'product_review_comment', 'bad') ?></a>
					</div>
					<!-- // [D] 호감/비호감 버튼 추가 -->
				</div>
			<?}// comment foreach ?>
		<?}// comment if?>

<?php
        $tmp_comment_paging = $rVal['comment_paging'];

        // 페이징
        echo '<div class="list-paginate-wrap mb-30">';
        echo '<div class="list-paginate" id="paging_' . $rVal['idx'] . '">' . $tmp_comment_paging->a_prev_page . $tmp_comment_paging->print_page . $tmp_comment_paging->a_next_page . '</div>';
        echo '</div>';
        echo '</div><!-- //.review-tab-sub -->';
?>
				</div>
			</td>
		</tr>
	</tbody>
<?php
		} // reviewList foreach
	} else {
?>
	<tbody>
		<tr>
			<td colspan='3'><p class="none">등록된 상품후기가 없습니다.</p></td>
		</tr>
	</tbody>
<?php
	} // reviewList else
?>
</table>

<!-- 페이징 -->
<div class="list-paginate mt-20">
	<?=$paging->a_prev_page.' '.$paging->print_page.' '.$paging->a_next_page?>
</div>
<!-- // 페이징 -->
