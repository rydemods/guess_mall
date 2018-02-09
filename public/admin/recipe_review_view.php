<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/recipe.class.php");

$recipe = new RECIPE();
$data = $recipe->getRecipeCommentDetail($_GET[num]);

if(ord($_ShopInfo->getId())==0){
	echo "<script>alert('정상적인 경로로 접근하시기 바랍니다.');window.close();</script>";
	exit;
}
?>

<html>
<head>
<SCRIPT LANGUAGE="JavaScript">
<!--
document.onkeydown = CheckKeyPress;
document.onkeyup = CheckKeyPress;
function CheckKeyPress() {
	ekey = event.keyCode;

	if(ekey == 38 || ekey == 40 || ekey == 112 || ekey ==17 || ekey == 18 || ekey == 25 || ekey == 122 || ekey == 116) {
		event.keyCode = 0;
		return false;
	}
}

function PageResize() {
	var oWidth = 600;
	var oHeight = document.all.table_body.clientHeight + 120;

	window.resizeTo(oWidth,oHeight);
}

function CheckForm() {
	if (confirm("해당 상품리뷰를 현재 정보로 저장 하시겠습니까?")) {
		document.form1.submit();
	}
}
//-->
</SCRIPT>
</head>
<link rel="styleSheet" href="/css/admin.css" type="text/css">
<link rel="stylesheet" href="style.css" type="text/css">
<title>상품리뷰 수정/답변</title>
<div class="pop_top_title"><p>레시피리뷰 답변</p></div>
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
			<form name="form1" action="recipe_indb.php" method="post">
			<input type="hidden" name="module" value="recipe_contents">
			<input type="hidden" name="mode" value="add_comment_reply">
			<input type="hidden" name="num" value="<?=$data[parent_comment]?>">
			<input type="hidden" name="recipe_no" value="<?=$data[recipe_no]?>">					
			<input type="hidden" name="admin" value="1">					
			<input type="hidden" name="returnUrl" value="c">
			<TR>
				<th width="100"><span>레시피</span></th>
				<TD class="td_con1"><B><?=$data[subject]?></B></TD>
			</TR>
			<TR>
				<th><span>이름</span></th>
				<TD class="td_con1"><B><?=$data[name]?></B></TD>
			</TR>

			<!--TR>
				<th><span>평점</span></th>
				<TD class="td_con1"><SPAN class="font_orange"><B>
				<?php
				for($i=1;$i<=$row->marks;$i++) {
					echo "★";
				}
				?></B></SPAN></TD>
			</TR-->
			<TR>
				<th><span>내용</span></th>
				<TD class="td_con1" style="height:120px;" valign="top"><?=$data[comment]?></TD>
			</TR>
			<TR>
				<th><span>작성자</span></th>
				<TD class="td_con1" valign="top"><input type="text" name="memname" size="10" value="관리자"></TD>
			</TR>
			<TR>
				<th><span>답변</span></th>
				<TD class="td_con1"> 
				<textarea name="comment" style="width:100%;height:120;word-break:break-all;" class="textarea"></textarea>
				</TD>
			</TR>
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
		<td align="center"><a href="javascript:CheckForm();"><img src="images/btn_save.gif" border="0" vspace="5" border=0></a>&nbsp;&nbsp;<a href="javascript:window.close();"><img src="images/btn_close.gif"  border="0" vspace="5" border=0 hspace="2"></a></td>
		<td width="18">&nbsp;</td>
	</tr>
	
	</form>
	</table>
	</TD>
</TR>
</TABLE>
</body>
</html>