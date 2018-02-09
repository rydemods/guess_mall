<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/adminlib.php");

$type=$_POST["type"];
$id=$_POST["id"];

if(ord($_ShopInfo->getId())==0 || ord($id)==0){
	echo "<script>window.close();</script>";
	exit;
}

$sql = "SELECT out_reason, out_reason_content FROM tblmemberout WHERE id = '{$id}' ";
list($out_reason, $out_reason_content) = pmysql_fetch($sql);
pmysql_free_result($result);
?>
<html>
<head>
<meta http-equiv='Content-Type' content='text/html;charset=utf-8'>
<title>탈퇴 사유</title>
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
		var oHeight = document.all.table_body.clientHeight + 120;

		window.resizeTo(oWidth,oHeight);
	}
//-->
</SCRIPT>
</head>
<link rel="styleSheet" href="/css/admin.css" type="text/css">
<meta http-equiv='Content-Type' content='text/html;charset=utf-8'>
<title><?=$name?>회원님의 탈퇴 사유</title>
<link rel="stylesheet" href="style.css" type="text/css">

<div class="pop_top_title"><p><?=$name?>회원님의 탈퇴 사유</p></div>
<body leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 oncontextmenu="return false" style="overflow-x:hidden;overflow-y:hidden;" ondragstart="return false" onselectstart="return false" oncontextmenu="return false" onLoad="PageResize();">
	<TABLE WIDTH="500" BORDER=0 CELLPADDING=0 CELLSPACING=0 style="table-layout:fixed;" id=table_body>
		<TR>
			<TD style="padding-top:3pt; padding-bottom:3pt;">
				
				<table cellspacing=0 cellpadding=0 width="100%" border=0>
					<tr>
						<th style="width:100px;font-size:11px;"><span>탈퇴사유</span></th>
						<td class="td_con1" style="padding:10px;">
							<?=$arrMemberOutReason[$out_reason]?>
						</td>		
					</tr>
					<tr>
						<th style="width:100px;font-size:11px;"><span>내용</span></th>
						<td class="td_con1" style="padding:10px;">
							<?=nl2br($out_reason_content)?>
						</td>		
					</tr>
				</table>
			</TD>
		</TR>
		<TR>
			<TD align=center><a href="javascript:window.close()"><img src="images/btn_close.gif" border="0" vspace="10" border=0></a></TD>
		</TR>
	</TABLE>
</body>
</html>
