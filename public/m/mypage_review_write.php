<?php include_once('outline/header_m.php'); ?>
<?php
$productcode        = $_POST['productcode'];
$ordercode          = $_POST['ordercode'];
$productorder_idx   = $_POST['productorder_idx'];
$review_num         = $_POST['review_num'];

$order_date	= substr($ordercode,0,4)."-".substr($ordercode,4,2)."-".substr($ordercode,6,2);

$review_row = null;
if ( $review_num > 0 ) {
    $review_sql  = "SELECT * FROM tblproductreview WHERE num = {$review_num} ";
    $review_row  = pmysql_fetch_object(pmysql_query($review_sql));
	$order_date	= substr($review_row->ordercode,0,4)."-".substr($review_row->ordercode,4,2)."-".substr($review_row->ordercode,6,2);
	$review_color = $review_row->color;
	$review_foot_width = $review_row->foot_width;
	$review_size = $review_row->size;
	$review_quality = $review_row->quality;
}

// 상품 정보
$sql  = "SELECT a.*, b.brandname ";
$sql .= "FROM tblproduct a LEFT JOIN tblproductbrand b ON a.brand = b.bridx ";
$sql .= "WHERE a.productcode = '" . $productcode . "' ";
// exdebug($sql);
$row = pmysql_fetch_object(pmysql_query($sql));

$brandname = $row->brandname;
$prodname = $row->productname;
$prodsellprice = $row->sellprice;

$img_path = $Dir.'data/shopimages/product/';
$prodimg	= getProductImage($img_path, $row->minimage);

$product_title = "";
if ( !empty($brandname) ) {
    $product_title .= "[{$brandname}] ";
}
$product_title .= $prodname;


?>
<!-- 리뷰작성 -->
<form id="review_write_form" name="review_write_form" method="post" enctype="multipart/form-data" onSubmit="return false;">

	<section class="top_title_wrap">
		<h2 class="page_local">
			<a href="javascript:history.back();" class="prev"></a>
			<span>상품리뷰 작성</span>
			<a href="<?$Dir.MDir?>" class="home"></a>
		</h2>
	</section>

	<div class="mypage_sub">

		<div class="my-cancel-detail">
			<p class="att-title">
				<label for="ord-num">주문날짜 : <?=$order_date?></label>
			</p>
		</div>

		<div class="box_mylist">
			<div class="content">
				<a href="javascript:;">
					<figure class="mypage_goods">
						<div class="img"><img src="<?=$prodimg?>" alt=""></div>
						<figcaption>
							<p class="brand">[<?=$brandname?>]</p>
							<p class="name"><?=$prodname?></p>
							<p class="price"><span class="point-color"><?=number_format($prodsellprice)?>원</span></p>
						</figcaption>
					</figure>
				</a>
			</div>
		</div><!-- //.box_mylist -->
<!--
		<div class="review_starvote">
			<h3>만족도</h3>
			<div class="select_star">
				<span class="bg_select">★ ★ ★ ★ ★</span>
				<select id="review_vote" name="review_vote">
					<option value="5">★ ★ ★ ★ ★</option>
					<option value="4">★ ★ ★ ★</option>
					<option value="5">★ ★ ★</option>
					<option value="2">★ ★</option>
					<option value="1">★</option>
				</select>
			</div>
		</div> //.review_starvote -->

		 <section class="wrap_select_rating">
			<div class="select_rating">
				<label>사이즈</label>
				<ul>
					<li>
						<span>작다</span>
						<input type="radio" value="-2" name="review_size">
					</li>
					<li>
						<span></span>
						<input type="radio" value="-1" name="review_size" >
					</li>
					<li>
						<span>적당함</span>
						<input type="radio" value="0" name="review_size">
					</li>
					<li>
						<span></span>
						<input type="radio" value="1" name="review_size" >
					</li>
					<li>
						<span>크다</span>
						<input type="radio" value="2" name="review_size" >
					</li>
				</ul>
			</div>
			<div class="select_rating">
				<label>발볼 넓이</label>
				<ul>
					<li>
						<span>작다</span>
						<input type="radio" value="-2" name="review_foot_width" >
					</li>
					<li>
						<span></span>
						<input type="radio" value="-1" name="review_foot_width" >
					</li>
					<li>
						<span>적당함</span>
						<input type="radio" value="0" name="review_foot_width" >
					</li>
					<li>
						<span></span>
						<input type="radio" value="1" name="review_foot_width" >
					</li>
					<li>
						<span>크다</span>
						<input type="radio" value="2" name="review_foot_width" >
					</li>
				</ul>
			</div>
			<div class="select_rating">
				<label>색상</label>
				<ul>
					<li>
						<span>어둡다</span>
						<input type="radio" value="-2" name="review_color" >
					</li>
					<li>
						<span></span>
						<input type="radio" value="-1" name="review_color" >
					</li>
					<li>
						<span>화면과 같다</span>
						<input type="radio" value="0" name="review_color" >
					</li>
					<li>
						<span></span>
						<input type="radio" value="1" name="review_color" >
					</li>
					<li>
						<span>밝다</span>
						<input type="radio" value="2" name="review_color" >
					</li>
				</ul>
			</div>
			<div class="select_rating">
				<label>품질/만족도</label>
				<ul>
					<li>
						<span>불만</span>
						<input type="radio" value="-2" name="review_quality" >
					</li>
					<li>
						<span></span>
						<input type="radio" value="-1" name="review_quality" >
					</li>
					<li>
						<span>보통</span>
						<input type="radio" value="0" name="review_quality" >
					</li>
					<li>
						<span></span>
						<input type="radio" value="1" name="review_quality" >
					</li>
					<li>
						<span>만족</span>
						<input type="radio" value="2" name="review_quality" >
					</li>
				</ul>
			</div>
		</section><!-- //.wrap_select_rating --><!-- [D] 2차개발분 -->

		<div class="order_table">
			<table class="my-th-left form_table">
				<colgroup>
					<col style="width:15%;">
					<col style="width:85%;">
				</colgroup>
				<tbody>
					<tr>
						<th>제목</th>
						<td><input type="text" id="review-title" name="inp_writer" title="제목" value="<?=$review_row->subject?>"></td>
					</tr>
					<tr>
						<th>내용</th>
						<td><textarea id="review-content" name="inp_content" title="내용"><?=$review_row->content?></textarea></td>
					</tr>
				</tbody>
			</table>

			<ul class="list_notice">
				<li>배송,상품문의, 취소, 교환 등의 문의사항은 1:1문의 또는 상담전화를 이용해 주시기 바랍니다.</li>
			</ul>
		</div><!-- //.order_table -->

		<div class="upload_photo">
			<h3>사진등록</h3>
			<ul class="upload_photo_list clear">
				<?php
					for ( $loopIdx = 0; $loopIdx < 3; $loopIdx++ ) {
						if ( $loopIdx == 0 ) {
							$up_rFile = $review_row->upfile;
						} else {
							$fieldName = "upfile" . ($loopIdx+1);
							$up_rFile = $review_row->$fieldName;
						}

						if ($up_rFile) {
							$vi_img_style   = "display:;";
							$vi_img_src = $Dir.DataDir."shopimages/review/".$up_rFile;
						} else {
							$vi_img_style   = "display:none;";
							$vi_img_src = "#";
						}
				?>

				<li>
					<label>
						<input type="hidden" name="v_up_filename[<?=$loopIdx?>]" value="<?=$up_rFile?>" class="vi-image"><input type="file" name="up_filename[<?=$loopIdx?>]" class="add-image">
						<div class="image_preview" style='<?=$vi_img_style?>position:absolute;top:0;left:0;width:100%;height:100%;'>
							<img src="<?=$vi_img_src?>" style='position:absolute;top:0;left:0;width:100%;height:100%;'>
							<a href="#" class="delete-btn">
								<button type="button"></button>
							</a>
						</div>
					</label>
				</li>

				<?php
					}
				?>
			</ul><!-- //.upload_photo_list -->
			<ul class="list_notice">
				<li>파일명: 한글,영문,숫자 / 파일용량: 3M이하 / 파일형식: GIF,JPEG</li>
			</ul>
		</div><!-- //.upload_photo -->

		<a class="btn-point" href="javascript:;" id="btn_review_write">저장</a>

	</div><!-- //.mypage_sub -->

    <input type="hidden" name="productcode" id="productcode" value="" />
    <input type="hidden" name="ordercode" id="ordercode" value="" />
    <input type="hidden" name="op_idx" id="productorder_idx" value="" />
    <input type="hidden" name="review_num" id="review_num" value="" />
    <input type="hidden" name="color" id="color" value="<?=$review_color ? $review_color : '0' ?>" />
    <input type="hidden" name="size" id="size" value="<?=$review_size ? $review_size : '0' ?>" />
    <input type="hidden" name="foot_width" id="foot_width" value="<?=$review_foot_width ? $review_foot_width : '0' ?>" />
    <input type="hidden" name="quality" id="quality" value="<?=$review_quality ? $quality : '0' ?>" />
	<input type="hidden" name="review_vote" id="review_vote" />
</form>
<!-- // 리뷰작성 -->

<script type="text/javascript">
	var averEval = [];

    if (!('url' in window) && ('webkitURL' in window)) {
        window.URL = window.webkitURL;
    }

    $(document).ready(function() {
		$('input:radio[name="review_size"][value="<?=$review_size ?>"]').prop('checked', true);
		$('input:radio[name="review_color"][value="<?=$review_color ?>"]').prop('checked', true);
		$('input:radio[name="review_foot_width"][value="<?=$review_foot_width ?>"]').prop('checked', true);
		$('input:radio[name="review_quality"][value="<?=$review_quality ?>"]').prop('checked', true);

        $('.add-image').on('change', function( e ) {
            ext = $(this).val().split('.').pop().toLowerCase();
            if($.inArray(ext, ['gif', 'png', 'jpg', 'jpeg']) == -1) {
                resetFormElement($(this));
                window.alert('이미지 파일이 아닙니다! (gif, png, jpg, jpeg 만 업로드 가능)');
            } else {
                blobURL = window.URL.createObjectURL(e.target.files[0]);
                $(this).parents("li").find('.image_preview img').attr('src', blobURL);
                $(this).parents("li").find('.vi-image').val('');
                $(this).parents("li").find('.image_preview').show();
            }
        });

        $('.image_preview a').bind('click', function() {
            if( confirm('삭제 하시겠습니까?') ){
                resetFormElement($(this).parents("li").find('.add-image'));
                $(this).parents("li").find('.vi-image').val('');
                $(this).parent().hide();
            }
            return false;
        });

        // 리뷰작성 후 "WRITE"버튼을 클릭한 경우
        $("#btn_review_write").on("click", function() {
            var review_title    = $("#review-title").val().trim();
            var review_content  = $("#review-content").val().trim();
 //           var review_vote     = $("select[name=review_vote] option:selected").val();
            var size = $(':radio[name="review_size"]:checked').val();
            var color = $(':radio[name="review_color"]:checked').val();
            var foot_width = $(':radio[name="review_foot_width"]:checked').val();
            var quality = $(':radio[name="review_quality"]:checked').val();

    		averEval.push(size);
    		averEval.push(color);
    		averEval.push(foot_width);
    		averEval.push(quality);
    		$("#review_vote").val(parseInt(average(averEval)));
            
            if ( chkReviewContentLength($("#review-content")[0]) === false ) {
                $("#review-content").focus();
            } else if ( review_title == "" ) {
                alert("제목을 입력해 주세요.");
                $("#review-title").val('').focus();
            } else if ( review_content == "" ) {
                alert("내용을 입력해 주세요.");
                $("#review-content").val('').focus();
            } else if ( size == "" ) {
            	alert("사이즈를 선택해 주세요");
            } else if ( color == "" ) {   
            	alert("색상을 선택해 주세요");
            } else if ( foot_width == "" ) {      
            	alert("발볼 넓이를 선택해 주세요");
            } else if ( quality == "" ) {           
            	alert("품질/만족도를 선택해 주세요");
            } else {
                $("#productcode").val("<?=$productcode?>");             // 상품 코드
                $("#ordercode").val("<?=$ordercode?>");                 // 주문 코드
                $("#productorder_idx").val("<?=$productorder_idx?>");   // 주문 상품 idx
                $("#review_num").val("<?=$review_num?>");               // 리뷰 num
				$("#size").val(size);
				$("#foot_width").val(foot_width);
				$("#color").val(color);
				$("#quality").val(quality);
                
                var fd = new FormData($("#review_write_form")[0]);

                $.ajax({
                    url: "/front/ajax_insert_review_v2.php",
                    type: "POST",
                    data: fd,
                    async: false,
                    cache: false,
                    contentType: false,
                    processData: false,
                }).success(function(data){
                    if ( data === "SUCCESS" ) {
                        alert("리뷰가 등록되었습니다.");
                        if("<?=$review_num?>" == ""){
                        	location.href = "/m/mypage_review.php";
                        }else{
                        	location.href = "/m/mypage_review.php?tebmenu=on";
                        }       
                       	
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

        
    });


    function resetFormElement(e) {
        e.wrap('<form>').closest('form').get(0).reset();
        e.unwrap();
    }

  //배열 평균 구하기
    function average(array) {
      var sum = 0;
      for (var i = 0; i < array.length; i++)
      	sum += parseInt( array[i], 10 );
      return sum / array.length;
    }
</script>

<?php include_once('outline/footer_m.php'); ?>

