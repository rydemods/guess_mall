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

<div class="layer-dimm-wrap pop-photo-write layer-photo-event"> <!-- .layer-class 이부분에 클래스 추가하여 사용합니다. -->
	<div class="dimm-bg"></div>
	<div class="layer-inner w800 photo-event-reg">
		<h3 class="layer-title">HOT<span class="type_txt1">-T</span> 포토등록</h3>
		<button type="button" class="btn-close">창 닫기 버튼</button>
		<div class="layer-content">

            <form name="fileform" method="post">
                <input type="hidden" name="board" value="photo">
                <input type="hidden" name="max_filesize" value="1024000">
                <input type="hidden" name="btype" value="I">
            </form>

            <form name=writeForm method='post' action='/board/board_photo.php' enctype='multipart/form-data'>
                <input type=hidden name=mode value=''>
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

			<table class="th_left">
				<caption>포토등록</caption>
				<colgroup>
					<col style="width:100px">
					<col style="width:auto">
				</colgroup>
				<tbody>
					<tr>
						<th scope="row"><label for="up_subject">제목</label></th>
						<td>
							<input type="text" id="up_subject" name="up_subject" value="<?=$article_title?>" title="제목 입력자리" style="width:100%;">
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="up_memo">내용</label></th>
						<td>
							<textarea name="up_memo" id="ir1" cols="30" rows="10" style="width:100%"><?=$article_content?></textarea>
						</td>
					</tr>
					<tr>
						<th>첨부1.</th>
						<td class="imageAdd">
							<input type="file" id="add-image1" name="up_filename[]" ids="1" accept="image/*">

							<?php if ( $article_filename1 ) { ?>
								<div class="txt-box" id="txt-box1"><?=$article_filename1?><button class="del-img" type="button" ids="1">이미지삭제</button></div>
							<?php } else { ?>
								<div class="txt-box" id="txt-box1"></div>
							<?php } ?>
							<label for="add-image1">찾기</label>
						</td>
					</tr>
					<tr>
						<th>첨부2.</th>
						<td class="imageAdd">
							<input type="file" id="add-image2" name="up_filename[]" ids="2" accept="image/*">
							<?php if ( $article_filename2 ) { ?>
								<div class="txt-box" id="txt-box2"><?=$article_filename2?><button class="del-img" type="button" ids="2">이미지삭제</button></div>
							<?php } else { ?>
								<div class="txt-box" id="txt-box2"></div>
							<?php } ?>
							<label for="add-image2">찾기</label>
						</td>
					</tr>
					<tr>
						<th>첨부3.</th>
						<td class="imageAdd">
							<input type="file" id="add-image3" name="up_filename[]" ids="3" accept="image/*">
							<?php if ( $article_filename3 ) { ?>
								<div class="txt-box" id="txt-box3"><?=$article_filename3?><button class="del-img" type="button" ids="3">이미지삭제</button></div>
							<?php } else { ?>
								<div class="txt-box" id="txt-box3"></div>
							<?php } ?>
							<label for="add-image3">찾기</label>
						</td>
					</tr>
					<tr>
						<th>첨부4.</th>
						<td class="imageAdd">
							<input type="file" id="add-image4" name="up_filename[]" ids="4" accept="image/*">
							<?php if ( $article_filename4 ) { ?>
								<div class="txt-box" id="txt-box4"><?=$article_filename4?><button class="del-img" type="button" ids="4">이미지삭제</button></div>
							<?php } else { ?>
								<div class="txt-box" id="txt-box4"></div>
							<?php } ?>
							<label for="add-image4">찾기</label>
						</td>
					</tr>
				</tbody>
			</table>
			<p class="mt-5">파일명 : 한글,영문,숫자 / 파일용량 : 10M이하 / 첨부기능 파일형식 : GIF,JPG(JPEG)</p>
			<div class="btn_wrap"><a href="javascript:;" class="btn-type1" id="event_photo_submit" onClick="javascript:chk_writeForm(document.writeForm);">등록하기</a></div>

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

            </form>
		</div>
	</div>
</div>

<script type="text/javascript">

    // 업로드한 이미지를 삭제
    $(document).on("click", "button.del-img", function() {
        var idx = $(this).attr("ids");

        $("#add-image" + idx).val("");
        $("#txt-box" + idx).html("");
        $("input[name=file_exist" + idx + "]").val("N");
    });

    // 파일 업로드 이벤트
    $('input[type=file]').bind('change', function (e) {
        var fileName = $(this).val().split('\\').pop();
        var idx = $(this).attr("ids");

        $(this).parent().find(".txt-box").html(fileName + '<button class="del-img" type="button" ids="' + idx + '">이미지삭제</button>');
        $("input[name=file_exist" + idx + "]").val("Y");
    });

</script>

