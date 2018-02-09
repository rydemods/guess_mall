<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

include_once($Dir."lib/forum.class.php");
$forum = new FORUM('write_form');
$forum_info = $forum->write_form['forum_info'];
$view_detail = $forum->write_form['view'];

$type = $forum->write_form['type'];
$forum_code = $forum->write_form['forum_code'];
$imagepath = $Dir.DataDir."shopimages/forum/";

if(ord($_ShopInfo->getId())==0){
	echo "<script>alert('정상적인 경로로 접근하시기 바랍니다.');window.close();</script>";
	exit;
}
?>

<html>
<head>
<script src="../js/jquery.js"></script>
<SCRIPT LANGUAGE="JavaScript">
<!--

function PageResize() {
	var oWidth = 1000;
	if (document.all.table_body.clientHeight > 600)
	{
		var oHeight = 1000;
	} else {
		var oHeight = document.all.table_body.clientHeight + 120;
	}

	window.resizeTo(oWidth,oHeight);
}

//-->
</SCRIPT>
</head>
<link rel="styleSheet" href="/css/admin.css" type="text/css">
<link rel="stylesheet" href="style.css" type="text/css">
<title>포럼 글 수정</title>
<div class="pop_top_title"><p>포럼 글 수정<!--/답변--></p></div>
<body leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 style="overflow-x:hidden;" onLoad="PageResize();">
<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0 style="table-layout:fixed;" id="table_body">
<TR>
	<TD background="images/member_zipsearch_bg.gif">
	<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td width="18"></td>
		<td></td>
		<td width="18" height=10></td>
	</tr>
	<tr>
		<td width="18">&nbsp;</td>
		<td>
		<table cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td width="100%">
            <div class="table_style01">
			<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>

			<form name="forum_write_form" enctype="multipart/form-data" method=post action="/front/forum_process.php">
			<input type=hidden name="mode" value="write">
			<input type=hidden name="callback" value="admin">
			<input type=hidden name="forum_code" value="<?=$forum_code?>">
			<input type=hidden name="type" value="<?=$type?>">
			<input type=hidden name="forum_index" value="<?=$view_detail->index?>"
		
			<TR>
				<th><span>작성자</span></th>
				<TD class="td_con1"><B><?=$view_detail->id?></B></TD>
			</TR>
			<TR>
				<th><span>날짜</span></th>
				<TD class="td_con1"><B><?=date("Y-m-d h:i",strtotime($view_detail->writetime) )?></B></TD>
			</TR>
		
			<TR>
				<th><span>제목</span></th>
				<TD class="td_con1"><input name="title" value="<?=$view_detail->title?>" style="width:90%;"></TD>
			</TR>
			<TR>
				<th><span>내용</span></th>
				<TD class="td_con1"><textarea name="content" id="ir1" style="width:100%;height:120;" class="textarea"><?=$view_detail->content?></textarea></TD>
			</TR>

			<TR>
				<th><span>썸네일</span></th>
				<TD class="td_con1">
				<?if(is_file($Dir.DataDir.$imagepath.$view_detail->img) ){?>
					<img src='<?=$Dir.DataDir.$imagepath.$view_detail->img?>' style='max-width:320px'/>
				<?}?>
				<input type="hidden" name="v_forum_file" value="<?=$view_detail->img?>">
				<input type="file" name="forum_file[]" id="forum_file">
				</td>
			</TR>
<?php
if( count( $comment_arr ) > 0 ){
?>
			<TR>
				<th><span>댓글</span></th>
				<td>
					<div>
						<table>
							<tr>
								<th style='width: 120px !important;'>ID</th>
								<th width='' >내용</th>
							</tr>

						</table>
					</div>
				</td>
			</TR>
<?php
}
?>
			</TABLE>
			</td>
		</tr>
		</table>
		</td>
		<td width="18">&nbsp;</td>
	</tr>
	<tr>
		<td colspan="3" height="10"></td>
	</tr>
	<tr>
		<td width="18">&nbsp;</td>
		<td align="center"><a id="forum_submit"><img src="images/btn_save.gif" border="0" vspace="5" border=0></a>&nbsp;&nbsp;<a href="javascript:window.close();"><img src="images/btn_close.gif"  border="0" vspace="5" border=0 hspace="2"></a></td>
		<td width="18">&nbsp;</td>
	</tr>
	
	</form>
	</table>
	</TD>
</TR>
</TABLE>

<script type="text/javascript" src="../SE2/js/HuskyEZCreator.js" charset="utf-8"></script>

<script language="javascript">

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

function forum_submit()
{
	var sHTML = oEditors.getById["ir1"].getIR();
	document.forum_write_form.content.value = sHTML;
	document.forum_write_form.submit();
	//window.close();
}

$(document).on("click","#forum_submit",forum_submit);
</script>

</body>
</html>
