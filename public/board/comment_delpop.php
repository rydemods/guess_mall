<?php
if(basename($_SERVER['SCRIPT_NAME'])===basename(__FILE__)) {
	header("HTTP/1.0 404 Not Found");
	exit;
}

include ("head.php");

$num=$_POST["num"];
$c_num=$_POST["c_num"];
$mode=$_POST["mode"];
$up_passwd=$_POST["up_passwd"];


if ($setup[use_comment] != "Y") {
	$errmsg="해당 게시판은 댓글 기능을 지원하지 않습니다.";
	alert_go($errmsg,'c');
	echo "<html><head><title></title></head><body onload=\"alert('{$errmsg}');window.close();\"></body></html>";exit;
}

if ($member[grant_comment]!="Y") {
	$errmsg="이용권한이 없습니다.";
	alert_go($errmsg,'c');
}

$qry  = "SELECT * FROM tblboardcomment WHERE board='{$board}' AND parent='{$num}' AND num='".$c_num."' ";
$result1 = pmysql_query($qry,get_db_conn());
$ok_result = pmysql_num_rows($result1);

if ((!$ok_result) || ($ok_result == -1)) {
	$errmsg="삭제할 댓글이 없습니다.\\n\\n다시 확인하시기 바랍니다.";
	alert_go($errmsg,'c');
} else {
	$row1 = pmysql_fetch_array($result1);
}

if ($_POST["mode"] == "delete") {
	if($member[admin]!="SU") {
		if (strlen($_POST["up_passwd"])==0) {
			$errmsg="잘못된 경로로 접근하셨습니다.";
			alert_go($errmsg,'c');
		}

		if (($row1[passwd]!=$_POST["up_passwd"]) && ($setup[passwd]!=$_POST["up_passwd"])) {
			$errmsg="비밀번호가 일치하지 않습니다.\\n\\n다시 확인 하십시오.";
			alert_go($errmsg,'c');
		}
	}
	$del_sql = "DELETE FROM tblboardcomment WHERE board='{$board}' AND parent='{$num}' AND num = '".$_POST["c_num"]."'";
	$delete = pmysql_query($del_sql,get_db_conn());

	if ($delete) {
		@pmysql_query("UPDATE tblboard SET total_comment = total_comment - 1 WHERE board='{$board}' AND num='{$num}'",get_db_conn());
	}
	echo "
	<script>
		try {
			opener.location.reload();
		} catch (e) {}
		window.close();
	</script>
	";
	exit;
	
}

?>
<html>
<head>
<meta http-equiv=Content-type content=text/html; charset=euc-kr>
<title>비밀번호 확인</title>
<style>
td	{font-family:"굴림,돋움";color:#4B4B4B;font-size:12px;line-height:17px;}
BODY,DIV,form,TEXTAREA,center,option,pre,blockquote {font-family:Tahoma;color:000000;font-size:9pt;}

A:link    {color:#635C5A;text-decoration:none;}
A:visited {color:#545454;text-decoration:none;}
A:active  {color:#5A595A;text-decoration:none;}
A:hover  {color:#545454;text-decoration:underline;}
.input{font-size:12px;BORDER-RIGHT: #DCDCDC 1px solid; BORDER-TOP: #C7C1C1 1px solid; BORDER-LEFT: #C7C1C1 1px solid; BORDER-BOTTOM: #DCDCDC 1px solid; HEIGHT: 18px; BACKGROUND-COLOR: #ffffff;padding-top:2pt; padding-bottom:1pt; height:19px}
.select{color:#444444;font-size:12px;}
.textarea {border:solid 1;border-color:#e3e3e3;font-family:돋음;font-size:9pt;color:333333;overflow:auto; background-color:transparent}
</style>
<Script language="javascript">
function PageResize() {
	var oWidth = document.all.table_body.clientWidth + 10;
	var oHeight = document.all.table_body.clientHeight + 55;

	window.resizeTo(oWidth,oHeight);
}

function delconfirm() {
	try {
		var pass = document.pwForm.up_passwd.value;

		if (pass == "") {
			alert("비밀번호를 입력해주세요.");
			document.pwForm.up_passwd.focus();
			return;
		}
	} catch (e) {}

	if (!confirm("덧글을 삭제 하시겠습니까?")) {
		return;
	}
	document.pwForm.submit();
}

</script>
</head>
<body leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 oncontextmenu="return false" style="overflow-x:hidden;overflow-y:hidden;" oncontextmenu="return false" onLoad="PageResize();">

<table width="330"  border="0" cellpadding="0" cellspacing="0" id=table_body>
<form method=post action="<?=$PHP_SELF?>" name=pwForm>
<input type=hidden name=pagetype value="comment_delpop">
<input type=hidden name=board value=<?=$board?>>
<input type=hidden name=num value=<?=$num?>>
<input type=hidden name=c_num value=<?=$c_num?>>
<input type=hidden name=mode value="delete">
<tr>
	<td><IMG SRC="<?=$Dir.BoardDir?>images/message_title.gif" border="0"></td>
	<td width="100%" background="<?=$Dir.BoardDir?>images/message_title1bg.gif"><IMG SRC="<?=$Dir.BoardDir?>images/message_title1.gif" border="0"></td>
	<td><IMG SRC="<?=$Dir.BoardDir?>images/message_title2.gif" border="0"></td>
</tr>
<tr>
<td background="<?=$Dir.BoardDir?>images/message_bg.gif"></td>
<td width="100%">
	<table width="100%"  border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td height="1" bgcolor="#cfcfcf"></td>
	</tr>
	<tr>
		<td align="center" bgcolor="#F6F6F6" style="padding:5px;padding-top:10px;padding-bottom:10px;">댓글 입력시 등록한 비밀번호를 입력하세요.</td>
	</tr>
	<tr>
		<td height="1" bgcolor="#cfcfcf"></td>
	</tr>
	<tr>
	<td align="center" height="40">
	<?if($member[admin]=="SU") {?>
	정말 삭제하시겠습니까?
	<?}else{?>
	비밀번호 <input type="password" name="up_passwd" style="width:160px" class="input">
	<?}?>
	</td>
	</tr>
	</table>
</td>
<td background="<?=$Dir.BoardDir?>images/message_bg1.gif"></td>
</tr>
<tr>
	<td><IMG SRC="<?=$Dir.BoardDir?>images/message_down.gif" border="0"></td>
	<td background="<?=$Dir.BoardDir?>images/message_down1.gif"></td>
	<td><IMG SRC="<?=$Dir.BoardDir?>images/message_down2.gif" border="0"></td>
</tr>
<tr>
	<td colspan="3" align="center" style="padding-top:5px;"><img src="<?=$Dir.BoardDir?>images/board_btn01.gif" border="0" style="CURSOR:hand" onclick="delconfirm()"><img src="<?=$Dir.BoardDir?>images/board_btn02.gif" border="0" hspace="5" style="CURSOR:hand" onClick="history.go(-1);"></td>
</tr>
</form>
</table>

</body>
</html>
