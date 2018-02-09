<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/venderlib.php");
include("access.php");

//상품QNA 게시판 존재여부 확인 및 설정정보 확인
$prqnaboard=getEtcfield($_venderdata->etcfield,"PRQNA");
if(ord($prqnaboard)>0) {
	$sql = "SELECT * FROM tblboardadmin WHERE board='".$prqnaboard."' ";
	$result=pmysql_query($sql,get_db_conn());
	$qnasetup=pmysql_fetch_object($result);
	pmysql_free_result($result);

	$qnasetup->btype=$qnasetup->board_skin[0];
	$qnasetup->max_filesize=$qnasetup->max_filesize*(1024*100);
	if($qnasetup->use_hidden=="Y") $qnasetup=NULL;
}

if(strlen($qnasetup->board)<=0) {
	alert_go("쇼핑몰 Q&A게시판 오픈이 안되었습니다.\\n\\n쇼핑몰에 문의하시기 바랍니다.",'c');
}

$exec=$_POST["exec"];
$num=$_POST["num"];

if (($exec != "delete") && ($exec != "modify"))	{
	$errmsg="잘못된 경로로 접근하셨습니다.";
	alert_go($errmsg,'c');
}

$qry = "WHERE a.board='".$qnasetup->board."' ";
$qry.= "AND a.pridx=b.pridx AND b.vender='".$_VenderInfo->getVidx()."' ";

$sql = "SELECT a.*, b.productcode,b.productname,b.tinyimage,b.sellprice ";
$sql.= "FROM tblboard a, tblproduct b ".$qry." ";
$sql.= "AND a.num='".$num."' ";
$result=pmysql_query($sql,get_db_conn());
if(!$qnadata=pmysql_fetch_object($result)) {
	alert_go('해당 게시글이 존재하지 않습니다.','c');
}
pmysql_free_result($result);

$mode='';
if ($exec == "delete") {
	$html_url = "order_qnadelete.php";
	$mode="delete";
} else if ($exec == "modify") {
	$html_url = "order_qnawriteopen.php";
}
if($error=="1") $error_meaage="<FONT COLOR=\"red\">※ 비밀번호 입력이 잘못되었습니다.</FONT><br><br>";

?>
<html>
<head>
<title>관리자 페이지</title>
<meta http-equiv="Content-Type" content="text/html; charset=EUC-KR">
<link rel="stylesheet" href="style.css">
<SCRIPT LANGUAGE="JavaScript">
<!--
function check_submit() {
	if(!pwForm.up_passwd.value) {
		alert("비밀번호를 입력하여 주세요");
		pwForm.up_passwd.focus();
		return;
	}
	pwForm.submit();
}
//-->
</SCRIPT>
</head>
<body marginwidth=0 marginheight=0 leftmargin=0 topmargin=0 onload="window.resizeTo(420,200);document.pwForm.up_passwd.focus();">
<center>

<form method=post action="<?=$html_url?>" onsubmit="return check_submit();" name=pwForm>
<input type=hidden name=mode value=<?=$mode?>>
<input type=hidden name=exec value=<?=$exec?>>
<input type=hidden name=num value=<?=$num?>>

<div align=center>
<table align="center" cellpadding="0" cellspacing="0" width="350">
<tr>
	<td height="10" colspan="3"></td>
</tr>
<tr>
	<td><IMG SRC="images/message_title.gif" border="0"></td>
	<td background="images/message_title1bg.gif"><IMG SRC="images/message_title1.gif" border="0"></td>
	<td><IMG SRC="images/message_title2.gif" border="0"></td>
</tr>
<tr>
	<td background="images/message_bg.gif"></td>
	<td width="100%">
	<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td align="center" height="40">비밀번호 : <INPUT type=password name="up_passwd" value="" size="20" maxLength="20" style="width:160px;"></td>
	</tr>
	</table>
	</td>
	<td background="images/message_bg1.gif"></td>
</tr>
<tr>
	<td><IMG SRC="images/message_down.gif" border="0"></td>
	<td background="images/message_down1.gif"></td>
	<td><IMG SRC="images/message_down2.gif" border="0"></td>
</tr>
<tr>
	<td colspan="3" align="center" style="padding-top:10px;">
	<img src="images/btn_confirm03.gif" border="0" style="cursor:hand;" onClick="check_submit()">
	<img src="images/btn_cancel05.gif" border="0" style="CURSOR:hand" onClick="window.close()">
	</td>
</tr>
</table>

</form>
</center>
</body>
</html>
