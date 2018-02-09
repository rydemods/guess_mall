<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

if(ord($_ShopInfo->getId())==0){
	echo "<script>alert('정상적인 경로로 접근하시기 바랍니다.');window.close();</script>";
	exit;
}

?>

<html>
<head>
<meta http-equiv='Content-Type' content='text/html;charset=utf-8'>
<title>운영자 메모</title>
<link rel="stylesheet" href="style.css" type="text/css">
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
	var oWidth = document.all.table_body.clientWidth + 10;
	var oHeight = document.all.table_body.clientHeight + 100;

	window.resizeTo(oWidth,oHeight);

	document.form1.id.focus();
}

function CheckForm() {
	if(document.form1.id.value.length==0) {
		alert("회원 아이디를 입력하세요.");
		document.form1.id.focus();
		return;
	}
	document.form1.submit();
}
//-->
</SCRIPT>
</head>
<link rel="styleSheet" href="/css/admin.css" type="text/css">
<div class="pop_top_title"><p>운영자 메모하기</p></div>

<body leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 oncontextmenu="return false" style="overflow-x:hidden;overflow-y:hidden;" ondragstart="return false" onselectstart="return false" oncontextmenu="return false" onLoad="PageResize();">
<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0 style="table-layout:fixed;" id=table_body>
<TR>
	<TD style="padding:5pt;">
	<div class="table_style01">
	<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
	<form name=form1 method=post action="member_memopop.php">
	<TR>
		<th class="table_cell" width="49"><span>회원ID</span></th>
		<TD class="td_con1" width="250"><input type=text name=id size=15 class="input" style="width:100%"></TD>
	</TR>
	</TABLE>
	</div>
	</TD>
</TR>
<TR>
	<TD align=center>메모를 등록할 회원ID를 입력하세요!</TD>
</TR>
<TR>
	<TD align=center><a href="javascript:CheckForm();"><img src="images/btn_ok3.gif"  border="0" vspace="2" border=0></a>&nbsp;<a href="javascript:window.close();"><img src="images/btn_close.gif"  border="0" vspace="2" border=0 hspace="2"></a></TD>
</TR>
</form>
</TABLE>
</body>
</html>
