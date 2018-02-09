<?php

$exec = "write";
if ( $event_type == "3" && !empty($num) ) {
    // 포토게시물 상세 페이지 인 경우
    $exec = "modify";
}

$arrFileExists = array();

for ( $i = 0; $i < 4; $i++ ) {
    $arrFileExists[$i] = "N";
            
    $varName = "article_filename" . ($i+1);
    if ( $$varName ) { $arrFileExists[$i] = "Y"; }
}                   

?>
	<section class="top_title_wrap">
		<h2 class="page_local">
			<a href="<?=$Dir.MDir?>promotion_detail.php?idx=<?=$idx?>&view_mode=<?=$view_mode?>&view_type=<?=$view_type?>&event_type=<?=$event_type?>" class="prev"></a>
			<span>포토<?=$mode == "modify"?'수정':'등록'?></span>
			<a href="<?=$Dir.MDir?>" class="home"></a>
		</h2>
	</section>

	<div class="mypage_sub">
		<form name=writeForm method='post' action='/board/board_photo.php' enctype='multipart/form-data'>
			<input type=hidden name=mode value='<?=$mode?>'>
			<input type=hidden name=pagetype value='write_photo'>
			<input type=hidden name=exec value='<?=$exec?>'>
			<input type=hidden name=num value='<?=$num?>'>
			<input type=hidden name=board value='photo'>
			<input type=hidden name=s_check value=''>
			<input type=hidden name=search value=''>
			<input type=hidden name=block value=''>
			<input type=hidden name=gotopage value=''>
			<input type=hidden name=pridx value=''>
			<input type=hidden name=pos value=''>
			<input type=hidden name=up_name value='<?=$_ShopInfo->getMemname()?>'>
			<input type=hidden name=up_passwd value='1234'>
			<input type=hidden name=up_email value=''>
			<input type=hidden name=promo_idx value='<?=$idx?>'>
			<input type=hidden name=event_type value='<?=$event_type?>'>
			<input type=hidden name=view_mode value='<?=$view_mode?>'>
			<input type=hidden name=view_type value='<?=$view_type?>'>
			<input type=hidden name=file_exist1 value='<?=$arrFileExists[0]?>'>
			<input type=hidden name=file_exist2 value='<?=$arrFileExists[1]?>'>
			<input type=hidden name=file_exist3 value='<?=$arrFileExists[2]?>'>
			<input type=hidden name=file_exist4 value='<?=$arrFileExists[3]?>'>
			<input type=hidden name=is_mobile value='<?=$isMobile?>'>
		<div class="order_table">
			<table class="my-th-left form_table">
				<colgroup>
					<col style="width:15%;">
					<col style="width:85%;">
				</colgroup>
				<tbody>
					<tr>
						<th>제목</th>
						<td><input type="text" id="up_subject" name="up_subject" value="<?=$article_title?>" placeholder="제목을 입력하세요" title="제목"></td>
					</tr>
					<tr>
						<th>내용</th>
						<td><textarea id="ir1" name="up_memo" placeholder="내용을 입력하세요" title="내용"><?=$article_content?></textarea></td>
					</tr>
				</tbody>
			</table>
		</div><!-- //.order_table -->

		<div class="upload_photo">
			<ul class="upload_photo_list clear">
				<li>
					<label>
						<input type="hidden" name="v_up_filename[]" value="<?=$article_filename1?>" class="vi-image"><input type="file" name="up_filename[]" class="add-image" ids="1" accept="image/*">
						<div class="image_preview" style="display:<?=$article_filename1?'':'none'?>;position:absolute;top:0;left:0;width:100%;height:100%;">
							<img src="<?=$article_filename1?'/data/shopimages/board/photo/'.$article_filename1:'#'?>" style="position:absolute;top:0;left:0;width:100%;height:100%;">
							<a href="javascript:;" class="delete-btn" ids="1">
								<button type="button"></button>
							</a>
						</div>
					</label>
				</li>
				<li>
					<label>
						<input type="hidden" name="v_up_filename[]" value="<?=$article_filename2?>" class="vi-image"><input type="file" name="up_filename[]" class="add-image" ids="2" accept="image/*">
						<div class="image_preview" style="display:<?=$article_filename2?'':'none'?>;position:absolute;top:0;left:0;width:100%;height:100%;">
							<img src="<?=$article_filename2?'/data/shopimages/board/photo/'.$article_filename2:'#'?>" style="position:absolute;top:0;left:0;width:100%;height:100%;">
							<a href="javascript:;" class="delete-btn" ids="2">
								<button type="button"></button>
							</a>
						</div>
					</label>
				</li>
				<li>
					<label>
						<input type="hidden" name="v_up_filename[]" value="<?=$article_filename3?>" class="vi-image"><input type="file" name="up_filename[]" class="add-image" ids="3" accept="image/*">
						<div class="image_preview" style="display:<?=$article_filename3?'':'none'?>;position:absolute;top:0;left:0;width:100%;height:100%;">
							<img src="<?=$article_filename3?'/data/shopimages/board/photo/'.$article_filename3:'#'?>" style="position:absolute;top:0;left:0;width:100%;height:100%;">
							<a href="javascript:;" class="delete-btn" ids="3">
								<button type="button"></button>
							</a>
						</div>
					</label>
				</li>
				<li class='hide'>
					<label>
						<input type="hidden" name="v_up_filename[]" value="<?=$article_filename4?>" class="vi-image"><input type="file" name="up_filename[]" class="add-image" ids="4" accept="image/*">
						<div class="image_preview" style="display:<?=$article_filename4?'':'none'?>;position:absolute;top:0;left:0;width:100%;height:100%;">
							<img src="<?=$article_filename4?'/data/shopimages/board/photo/'.$article_filename4:'#'?>" style="position:absolute;top:0;left:0;width:100%;height:100%;">
							<a href="javascript:;" class="delete-btn" ids="4">
								<button type="button"></button>
							</a>
						</div>
					</label>
				</li>
			</ul><!-- //.upload_photo_list -->
			<ul class="list_notice">
				<li>파일명: 한글,영문,숫자 / 파일용량: 10M이하 /<br> 파일형식: GIF,JPEG</li>
			</ul>
		</div><!-- //.upload_photo -->
	<?
		$btnName = "등록"; 
		if ( $mode == "modify" ) {
			$btnName = "수정";
		}
	?>
	<?php if(strlen($_ShopInfo->getMemid())==0) {?>
		<button type="button" class="btn-point" onClick="javascript:goLogin();"><?=$btnName?>하기</button>
	<?php } else { ?>
		<button type="button" class="btn-point" onClick="javascript:chk_writeForm(document.writeForm);"><?=$btnName?>하기</button>
	<?php } ?>
			<input type="hidden" name="ins4eField[mode]">
			<input type="hidden" name="ins4eField[pagetype]">
			<input type="hidden" name="ins4eField[exec]">
			<input type="hidden" name="ins4eField[num]">
			<input type="hidden" name="ins4eField[board]">
			<input type="hidden" name="ins4eField[s_check]">
			<input type="hidden" name="ins4eField[search]">
			<input type="hidden" name="ins4eField[block]">
			<input type="hidden" name="ins4eField[gotopage]">
			<input type="hidden" name="ins4eField[pridx]">
			<input type="hidden" name="ins4eField[pos]">
			<input type="hidden" name="ins4eField[up_is_secret]">
			<input type="hidden" name="ins4eField[up_name]">
			<input type="hidden" name="ins4eField[up_passwd]">
			<input type="hidden" name="ins4eField[up_email]">
			<input type="hidden" name="ins4eField[up_subject]">
			<input type="hidden" name="ins4eField[up_memo]">
			<input type="hidden" name="ins4eField[up_filename[]]">
			<input type="hidden" name="ins4eField[promo_idx]">
			<input type="hidden" name="ins4eField[event_type]">
			<input type="hidden" name="ins4eField[view_type]">
			<input type="hidden" name="ins4eField[view_mode]">
			<input type="hidden" name="ins4eField[file_exist1]">
			<input type="hidden" name="ins4eField[file_exist2]">
			<input type="hidden" name="ins4eField[file_exist3]">
			<input type="hidden" name="ins4eField[file_exist4]">
			<input type="hidden" name="ins4eField[is_mobile]">
		</form>
		<form name="fileform" method="post">
			<input type="hidden" name="board" value="photo">
			<input type="hidden" name="max_filesize" value="1024000">
			<input type="hidden" name="btype" value="I">
		</form>
	</div><!-- //.mypage-wrap -->

<script type="text/javascript">

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
                $(this).parents("li").find('.image_preview').show();				
				$("input[name=file_exist" + $(this).attr("ids") + "]").val("Y");
            }
        });

        $('.image_preview a').bind('click', function() {
            if( confirm('삭제 하시겠습니까?') ){
                resetFormElement($(this).parents("li").find('.add-image'));
                $(this).parents("li").find('.vi-image').val('');
                $(this).parent().hide();
				$("input[name=file_exist" + $(this).attr("ids") + "]").val("N");
            }
            return false;
        });
    });
	
    function resetFormElement(e) {
        e.wrap('<form>').closest('form').get(0).reset();
        e.unwrap();
    }

    $(document).ready(function() {
        nhn.husky.EZCreator.createInIFrame({
            oAppRef: oEditors,
            elPlaceHolder: "ir1",
            sSkinURI: "../SE2/SmartEditor2Skin.html",
            htParams : {
                bUseToolbar : true,             // 툴바 사용 여부 (true:사용/ false:사용하지 않음)
                bUseVerticalResizer : false,     // 입력창 크기 조절바 사용 여부 (true:사용/ false:사용하지 않음)
                bUseModeChanger : true,         // 모드 탭(Editor | HTML | TEXT) 사용 여부 (true:사용/ false:사용하지 않음)
                //aAdditionalFontList : aAdditionalFontSet,     // 추가 글꼴 목록
                fOnBeforeUnload : function(){
                }
            },
            fOnAppLoad : function(){
            },
            fCreator: "createSEditor2"
        });
    });

</script>


