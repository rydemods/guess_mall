<?php include_once('outline/header_m.php'); ?>
<?php
// 전체매장 가져오기
$arrStoreList = array();
$sql  = "SELECT * FROM tblstore WHERE view = '1' ORDER BY sort asc, sno desc ";
$result = pmysql_query($sql);
while ($row = pmysql_fetch_object($result)) {
	$arrStoreList[] = $row;
}
pmysql_free_result($result);

$mode	= "write";
$sno		= $_GET['sno'];
if (!empty($sno) ) {
    // 포토게시물 상세 페이지 인 경우
    $mode	= "modify";
    $s_sql  = "SELECT * FROM tblstorestory WHERE sno = {$sno} ";
    $s_row  = pmysql_fetch_object(pmysql_query($s_sql));
}

$arrFileExists = array();

for ( $i = 0; $i < 1; $i++ ) {
    $arrFileExists[$i] = "N";

	if ( $i == 0 ) {
		$varName = "filename";
	} else {
		$varName = "filename" . ($i+1);
	}

    if ( $s_row->$varName ) { $arrFileExists[$i] = "Y"; }
}
?>

<form id="writeForm" name="writeForm" enctype="multipart/form-data">
	<input type=hidden name=mode value='<?=$mode?>'>
	<input type=hidden name=sno value='<?=$s_row->sno?>'>

	<section class="top_title_wrap">
		<h2 class="page_local">
			<a href="javascript:history.back();" class="prev"></a>
			<span>상품리뷰 작성</span>
			<a href="<?=$Dir.MDir?>" class="home"></a>
		</h2>
	</section>

	<div class="mypage_sub">
		<div class="order_table">
			<table class="my-th-left form_table">
				<colgroup>
					<col style="width:35%;">
					<col style="width:65%;">
				</colgroup>
				<tbody>
					<tr>
						<th>지점명</th>
						<td>
							<div class="select-def">
								<select class="SEARCH_SELECT first_category" id="store_code" name="store_code">
									<option value="">지점명</option>
								<?php
								foreach($arrStoreList as $storeKey => $storeVal) {
								?>
									<option value="<?=$storeVal->store_code?>"<?=$s_row->store_code==$storeVal->store_code?' selected':''?>><?=$storeVal->name?></option>
								<?
								}
								?>
								</select>
							</div>
						</td>
					</tr>
					<tr>
						<th>공개여부</th>
						<td>
							<!-- <label><input name="view-type" id="view" value='0' class="radio-def" type="radio" checked><span>공개</span></label>
							<label><input name="view-type" id="no-view" value='1' class="radio-def" type="radio"  ><span>비공개</span></label> -->

							<label class="check_round private">
								<input type="checkbox" class="CLS_view-type" checked="">
							</label>
						</td>
					</tr>
					<tr>
						<th>제목</th>
						<td><input type="text" name="title" id="title" title="제목" value="<?=$s_row->title?>"></td>
					</tr>
					<tr>
						<th colspan=2>내용</th>
					</tr>
					<tr>
						<td colspan=2><textarea wrap=off  id="ir1" name="content" title="내용" style='min-width:180px;'><?=htmlspecialchars($s_row->content)?></textarea></td>
					</tr>
					<tr>
						<th>이미지 첨부</th>
						<td>
							<ul class="upload_photo_list clear">
								<?php
									for ( $loopIdx = 0; $loopIdx < 1; $loopIdx++ ) {
										if ( $loopIdx == 0 ) {
											$up_rFile = $s_row->filename;
										} else {
											$fieldName = "filename" . ($loopIdx+1);
											$up_rFile = $s_row->$fieldName;
										}

										if ($up_rFile) {
											$vi_img_style   = "display:;";
											$vi_img_src = $Dir.DataDir."shopimages/store_story/".$up_rFile;
										} else {
											$vi_img_style   = "display:none;";
											$vi_img_src = "#";
										}
								?>

								<li>
									<label>
										<input type=hidden name="file_exist[]" class="file_exist" value='<?=$arrFileExists[0]?>'>
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
							</ul>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		<?if ($mode == "write") {?>
		<a class="btn-point mt-50" href="javascript:;" id="btn_review_write">등록하기</a>
		<?} else if ($mode == "modify") {?>
		<a class="btn-point mt-50" href="javascript:;" id="btn_review_write">수정완료</a>
		<?}?>

	</div><!-- //.mypage_sub -->
</form>
<!-- // 리뷰작성 -->
<script type="text/javascript" src="../SE2/js/HuskyEZCreator.js" charset="utf-8"></script>
<script type="text/javascript">
	var oEditors = [];

	nhn.husky.EZCreator.createInIFrame({
		oAppRef: oEditors,
		elPlaceHolder: "ir1",
		sSkinURI: "../SE2/SmartEditor2Skin.html",
		htParams : {
			bUseToolbar : true,				// 툴바 사용 여부 (true:사용/ false:사용하지 않음)
			bUseVerticalResizer : true,		// 입력창 크기 조절바 사용 여부 (true:사용/ false:사용하지 않음)
			bUseModeChanger : true,			// 모드 탭(Editor | HTML | TEXT) 사용 여부 (true:사용/ false:사용하지 않음)
			//aAdditionalFontList : aAdditionalFontSet,		// 추가 글꼴 목록
			fOnBeforeUnload : function(){
			}
		},
		fOnAppLoad : function(){
		},
		fCreator: "createSEditor2"
	});

    if (!('url' in window) && ('webkitURL' in window)) {
        window.URL = window.webkitURL;
    }

    $(document).ready(function() {
        $('.add-image').on('change', function( e ) {
            ext = $(this).val().split('.').pop().toLowerCase();
            if($.inArray(ext, ['gif', 'png', 'jpg', 'jpeg']) == -1) {
                resetFormElement($(this));
                window.alert('이미지 파일이 아닙니다! (gif, png, jpg, jpeg 만 업로드 가능)');
            } else {
                blobURL = window.URL.createObjectURL(e.target.files[0]);
                $(this).parents("li").find('.image_preview img').attr('src', blobURL);
                $(this).parents("li").find('.vi-image').val('');
				$(this).parents("li").find(".file_exist").val("Y");
                $(this).parents("li").find('.image_preview').show();
            }
        });

        $('.image_preview a').bind('click', function() {
            if( confirm('삭제 하시겠습니까?') ){
                resetFormElement($(this).parents("li").find('.add-image'));
                $(this).parents("li").find('.vi-image').val('');
				$(this).parents("li").find(".file_exist").val("N");
                $(this).parent().hide();
            }
            return false;
        });
    });

    function resetFormElement(e) {
        e.wrap('<form>').closest('form').get(0).reset();
        e.unwrap();
    }





	$("#btn_review_write").click(function() {
		
		var sHTML = oEditors.getById["ir1"].getIR();
		document.writeForm.content.value=sHTML;

		if ($("#writeForm").find("select[name=store_code] option:selected").val() == '') {
			alert("지점명을 입력해 주세요.");
			return;
		}
		if ($("#writeForm").find("input[name=title]").val() == '') {
			alert("내용을 입력해 주세요.");
			$("#writeForm").find("input[name=title]").focus();
			return;
		}
		if ($("#writeForm").find("input[name=file_exist1]").val() == 'N') {
			alert("이미지를 첨부해 주세요.");
			return;
		}
		if ($("#writeForm").find("textarea[name=content]").val() == '') {
			alert("내용을 입력해 주세요.");
			$("#writeForm").find("textarea[name=content]").focus();
			return;
		} else {

			var fd = new FormData($("#writeForm")[0]);

			$.ajax({
				url : '<?=$Dir.FrontDir?>store_story_proc.php',
				type: "POST",
				data: fd,
				async: false,
				cache: false,
				contentType: false,
				processData: false,
			}).success(function(data){
				if( data === "SUCCESS" ) {
					alert("등록되었습니다.");
					location.href="<?=$Dir.MDir?>store_story.php";
				} else {
					var arrTmp = data.split("||");
					if ( arrTmp[0] === "FAIL" ) {
						alert(arrTmp[1]);
					} else {
						alert("등록에 실패하였습니다.");
					}
				}
			}).error(function(){
				alert("다시 시도해 주십시오.");
			});
		}
	});

</script>

<?php include_once('outline/footer_m.php'); ?>

