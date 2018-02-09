<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
include_once($Dir."lib/forum.class.php");

$forum = new FORUM('write_form');
$forum_info = $forum->write_form['forum_info'];
$view_detail = $forum->write_form['view'];
$type = $forum->write_form['type'];
$forum_code = $forum->write_form['forum_code'];
?>
<?php  include ($Dir.MainDir.$_data->menu_type.".php") ?>

<div id="contents">
	<div class="inner forum-wrap">
		<main class="board-list-wrap write">
			<h2>FORUM</h2>
			<div class="tit-search">
				<h3><?=$forum_info->code_name?></h3>
			</div>
			<div class="list-wrap">

			<form name="forum_write_form" enctype="multipart/form-data" method=post action="/front/forum_process.php">
			<input type=hidden name="mode" value="write">
			<input type=hidden name="forum_code" value="<?=$forum_code?>">
			<input type=hidden name="type" value="<?=$type?>">
			<input type=hidden name="forum_index" value="<?=$view_detail->index?>">

				<table class="th_left">
					<caption>포럼 작성</caption>
					<colgroup>
						<col style="width:160px">
						<col style="width:auto">
						<col style="width:160px">
						<col style="width:160px">
					</colgroup>
					<tbody>
						<tr>
							<th><label for="">말머리</label></th>
							<td colspan="3">
								<input type="text" class="required_value" id="" name="summary" value="<?=$view_detail->summary?>" title="말머리 입력" label="말머리" style="width:100%;">
							</td>
						</tr>
						<tr>
							<th><label for="">제목</label></th>
							<td colspan="3">
								<input type="text" class="required_value" id="" name="title" value="<?=$view_detail->title?>" title="제목 입력자리" label="제목" style="width:100%;">
							</td>
						</tr>
						<tr>
							<th><label for="">내용</label></th>
							<td colspan="3">
								<textarea class="required_value" id="ir1"  name="content" cols="30" rows="10" label="문의내용" style="width:100%">
									<?=$view_detail->content?>
								</textarea>
							</td>
						</tr>
						<tr>
							<th>썸네일 이미지</th>
							<td colspan="3" class="imageAdd">
								<input type="hidden" name="v_forum_file" value="<?=$view_detail->img?>">
								<input type="file" name="forum_file[]" id="forum_file">
								<div class="txt-box"><?=$view_detail->img?></div> <!-- 파일 업로드시 파일 주소 출력 -->
								<label for="up_filename_forum" id="open_forum_file">찾기</label>
							</td>
						</tr>
						<tr>
							<th>태그</th>
							<td colspan="3" class="tag-add">
								<input type="text" class="required_value" id="" name="hash_tags" value="<?=$view_detail->tag?>" title="태그입력" label="태그입력" style="width:100%;">
								<p class="mt-5">ㆍ태그 여러개의 태그 입력시 쉼표(,)로 구분하여 입력하세요.</p>
							</td>
						</tr>
					</tbody>
				</table>
			</form>

				<div class="btn_wrap ta-c mt-30">
					<a href="javascript:;" class="btn-type1" id="back_list">취소</a>
					<a href="javascript:;" class="btn-type1 c1" id="btnSubmit">등록</a>
				</div>

			</div>
		</main>
	</div>
</div>
<!-- // [D] 스토어_리스트 퍼블 추가 -->

<script type="text/javascript" src="/SE2/js/HuskyEZCreator.js" charset="utf-8"></script>
<script>
$(document).on("click","#open_forum_file",function(){
	$("#forum_file").click();
});

$(document).ready(function(){});
var oEditors = [];
nhn.husky.EZCreator.createInIFrame({
    oAppRef: oEditors,
    elPlaceHolder: "ir1",
    sSkinURI: "../SE2/SmartEditor2Skin.html",
    htParams : {
        bUseToolbar : true,             // 툴바 사용 여부 (true:사용/ false:사용하지 않음)
        bUseVerticalResizer : true,     // 입력창 크기 조절바 사용 여부 (true:사용/ false:사용하지 않음)
        bUseModeChanger : true,         // 모드 탭(Editor | HTML | TEXT) 사용 여부 (true:사용/ false:사용하지 않음)
        //aAdditionalFontList : aAdditionalFontSet,     // 추가 글꼴 목록
        fOnBeforeUnload : function(){
        }
    },
    fOnAppLoad : function(){
    },
    fCreator: "createSEditor2"
        
});

function back_list()
{
	history.go(-1);
}

function write_submit()
{
	var sHTML = oEditors.getById["ir1"].getIR();
	document.forum_write_form.content.value = sHTML;
	document.forum_write_form.submit();
}

$(document).on("click","#btnSubmit",write_submit);

$(document).on("click","#back_list",back_list);

</script>

<?php
include ($Dir."lib/bottom.php")
?>
