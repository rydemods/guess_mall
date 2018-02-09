<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

$id=$_POST["id"];

if(ord($_ShopInfo->getId())==0 || ord($id)==0){
	echo "<script>window.close();</script>";
	exit;
}

$member_addform="";
$sql = "SELECT member_addform FROM tblshopinfo ";
$result=pmysql_query($sql,get_db_conn());
if($row=pmysql_fetch_object($result)) {
	$member_addform=$row->member_addform;
} else {
	echo "<script>window.close();</script>";
	exit;
}
pmysql_free_result($result);

$sql = "SELECT joinip,ip,logindate,logincnt,group_code,member_out,etcdata FROM tblmember WHERE id='{$id}' ";
$result=pmysql_query($sql,get_db_conn());
if($row=pmysql_fetch_object($result)) {
	if($row->member_out=="Y") {
		echo "<script>window.close();</script>";
		exit;
	}
	$_mem=$row;
} else {
	echo "<script>window.close();</script>";
	exit;
}
pmysql_free_result($result);
?>

<html>
<head>
<meta http-equiv='Content-Type' content='text/html;charset=utf-8'>
<title>쇼핑몰 회원 기타정보</title>
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
	var oHeight = document.all.table_body.clientHeight + 75;

	window.resizeTo(oWidth,oHeight);
}
//-->
</SCRIPT>
</head>
<link rel="styleSheet" href="/css/admin.css" type="text/css">
<div class="pop_top_title"><p><?=$id?> 회원님의 기타정보</p></div>
<body leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 style="overflow-x:hidden;" onLoad="PageResize();">
<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0 style="table-layout:fixed;" id="table_body">
<TR>
	<TD>
	<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td width="5">&nbsp;</td>
		<td width="100%" height="25">
        <div class="table_style01">
		<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
<?php
		$etc=explode("=",$_mem->etcdata);
		if(ord($member_addform)) {	//회원 추가입력정보 관련
			$fieldarray=explode("=",$member_addform);
			$num=sizeof($fieldarray)/3;
			for($i=0;$i<$num;$i++) {
				echo "		<TR>\n";
				echo "			<TD class=\"td_con_blue1\" style=\"padding-left:13px;\"><img src=\"images/icon_point2.gif\" width=\"8\" height=\"11\" border=\"0\"><b>".$fieldarray[$i*3]."</b></td>\n";
				echo "			<TD class=\"td_con_blue\">{$etc[$i]}&nbsp;</td>\n";
				echo "		</tr>\n";
				echo "		<tr>\n";
				echo "			<TD colspan=\"2\" background=\"images/table_con_line.gif\"></TD>\n";
				echo "		</tr>\n";
			}
		}
		if(ord($_mem->group_code)) {	//회원등급 관련
			$sql = "SELECT group_name FROM tblmembergroup WHERE group_code='{$_mem->group_code}' ";
			$result=pmysql_query($sql,get_db_conn());
			if($row=pmysql_fetch_object($result)) {
				echo "		<TR>\n";
				echo "			<TD class=\"td_con_red1\" style=\"padding-left:13px;\"><img src=\"images/icon_point2.gif\" width=\"8\" height=\"11\" border=\"0\"><b>회원등급</b></td>\n";
				echo "			<TD class=\"td_con_red\">{$row->group_name}</td>\n";
				echo "		</tr>\n";
				echo "		<tr>\n";
				echo "			<TD colspan=\"2\" background=\"images/table_con_line.gif\"></TD>\n";
				echo "		</tr>\n";
			}
			pmysql_free_result($result);
		}
?>
		<TR>
			<th><span>가입IP</span></th>
			<TD class="td_con1"><p><?=$_mem->joinip?></p></TD>
		</TR>
		<TR>
			<th><span>마지막 로그인 IP</span></th>
			<TD class="td_con1"><p><?=$_mem->ip?></p></TD>
		</TR>			
		<TR>
			<th><span>마지막 로그인 시간</span></th>
			<TD class="td_con1"><p><?=(strlen($_mem->logindate)==14?substr($_mem->logindate,0,4)."/".substr($_mem->logindate,4,2)."/".substr($_mem->logindate,6,2)." ".substr($_mem->logindate,8,2).":".substr($_mem->logindate,10,2).":".substr($_mem->logindate,12,2):"-")?></p></TD>
		</TR>								
		<TR>
			<th><span>로그인 횟수</span></th>
			<TD class="td_con1"><p><?=$_mem->logincnt?>회</p></TD>
		</TR>
		</TABLE>
        </div>
		</td>
		<td width="5">&nbsp;</td>
	</tr>
	<tr>
		<td width="20">&nbsp;</td>
		<td align="center"><a href="javascript:window.close()"><img src="images/btn_close.gif" width="36" height="18" border="0" vspace="5" border=0></a></td>
		<td width="18">&nbsp;</td>
	</tr>
	</table>
	</TD>
</TR>
</TABLE>
