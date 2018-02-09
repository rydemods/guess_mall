<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

if(strlen($_ShopInfo->getMemid())==0) {
	Header("Location:".$Dir.FrontDir."login.php?chUrl=".getUrl());
	exit;
}
// 전체매장 가져오기
$arrStoreList = array();
$sql  = "SELECT * FROM tblstore WHERE view = '1' ORDER BY sort asc, sno desc ";
$result = pmysql_query($sql);
while ($row = pmysql_fetch_object($result)) {
	$arrStoreList[] = $row;
}
pmysql_free_result($result);

?>
<?php  include ($Dir.MainDir.$_data->menu_type.".php") ?>
<?
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

<div id="contents" class="bg">
	<div class="inner">
		<main class="store_story_wrap write">
			<h3>STORE STORY</h3>
			<h4>스토어 스토리 등록</h4>
			<form name='writeForm' id='writeForm' enctype='multipart/form-data'>
				<input type=hidden name=mode value='<?=$mode?>'>
				<input type=hidden name=sno value='<?=$s_row->sno?>'>
			<section class="store_story_list">
				<table class="th_left">
				<caption></caption>
				<colgroup>
					<col style="width:160px">
					<col style="width:325px">
					<col style="width:160px">
					<col style="width:auto">
				</colgroup>
				<tbody>
					<tr>
						<th scope="row">지점명</th>
						<td colspan="3">
							<div class="my-comp-select">
								<select class="required_value" id="store_code" name="store_code" label="지점선택">
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
						<th scope="row">제목</th>
						<td colspan="3">
							<input type="text" class="required_value" id="title" name="title" value="<?=$s_row->title?>" title="제목 입력자리" label="제목" style="width:100%;">
						</td>
					</tr>
					<tr>
						<th scope="row">이미지첨부</th>
						<td colspan="3" class="imageAdd">
								<input type="hidden" name="v_up_filename[]" class="v_up_filename" value='<?=$s_row->filename?>'>
								<input type=hidden name="file_exist[]" class="file_exist" value='<?=$arrFileExists[0]?>'>
                                <input type="file" id="add-image1" name="up_filename[]" accept="image/*">

                                <?php if ( $s_row->filename ) { ?>
                                    <div class="txt-box" id="txt-box1"><?=$s_row->vfilename?><!--button class="del-img" type="button">이미지삭제</button--></div>
                                <?php } else { ?>
                                    <div class="txt-box" id="txt-box1"></div>
                                <?php } ?>
                                <label for="add-image1">찾기</label>
						</td>
					</tr>
					<tr>
						<th scope="row">내용</th>
						<td colspan="3">
							<textarea wrap=off  id="ir1" class="required_value" id="content" name="content" cols="30" rows="22" label="문의내용" style="width:100%"><?=htmlspecialchars($s_row->content)?></textarea>
						</td>
					</tr>
				</tbody>
				</table>
				<div class="btn_wrap mt-30">
				<?if ($mode == "write") {?>
					<a href="../front/store_story.php" class="btn-type1">취소</a>
					<a href="javascript:;" class="btn-type1 c1 formSubmit">등록</a>
				<?} else if ($mode == "modify") {?>
					<a href="../front/store_story.php" class="btn-type1">목록</a>
					<a href="javascript:;" class="btn-type1 c1 formSubmit">수정</a>
					<a href="javascript:;" class="btn-type1 formDel">삭제</a>
				<?}?>
				</div>
			</section>
			</form>
		</main>
	</div>
</div>
<!-- // [D] 스토어_리스트 퍼블 추가 -->

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

    // 업로드한 이미지를 삭제
    $(document).on("click", "button.del-img", function() {
        $(this).parents(".imageAdd").find("input[name=up_filename[]]").val("");
		$(this).parents(".imageAdd").find(".txt-box").html("");
        $(this).parents(".imageAdd").find(".file_exist").val("N");
        $(this).parents(".imageAdd").find(".v_up_filename").val("");
    });

    // 파일 업로드 이벤트
    $('input[type=file]').bind('change', function (e) {
        var fileName = $(this).val().split('\\').pop();
        $(this).parents(".imageAdd").find(".txt-box").html(fileName + '<!--button class="del-img" type="button">이미지삭제</button-->');
        $(this).parents(".imageAdd").find(".file_exist").val("Y");
        $(this).parents(".imageAdd").find(".v_up_filename").val("");
    });

	$(".formSubmit").click(function(){
		
		var sHTML = oEditors.getById["ir1"].getIR();
		document.writeForm.content.value=sHTML;

		if ($("#writeForm").find("select[name=store_code] option:selected").val() == '') {
			alert("지점명을 입력해 주세요.");
			return;
		}
		if ($("#writeForm").find("input[name=title]").val() == '') {
			alert("제목을 입력해 주세요.");
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
		}

		var fd = new FormData($("#writeForm")[0]);

		$.ajax({
			url : '../front/store_story_proc.php',
            type: "POST",
            data: fd,
            async: false,
            cache: false,
            contentType: false,
            processData: false,
        }).success(function(data){
            if( data === "SUCCESS" ) {
                alert("등록되었습니다.");
				location.href="../front/store_story.php";
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
	});

	$(".formDel").click(function(){
		var sno	= $("#writeForm").find("input[name=sno]").val();
		$.ajax({
			url : 'store_story_proc.php',
            type: "POST",
            data: {
				mode : 'delete', sno : sno
			},
            async: false,
            cache: false,
        }).success(function(data){
            if( data === "SUCCESS" ) {
                alert("삭제되었습니다.");
				location.href="../front/store_story.php";
            } else {
                var arrTmp = data.split("||");
                if ( arrTmp[0] === "FAIL" ) {
                    alert(arrTmp[1]);
                } else {
                    alert("삭제에 실패하였습니다.");
                }
            }
        }).error(function(){
            alert("다시 시도해 주십시오.");
        });
	});

</script>

<?php
include ($Dir."lib/bottom.php")
?>
