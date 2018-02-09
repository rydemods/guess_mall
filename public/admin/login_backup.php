<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata2.php");

$id      = $_ShopInfo->getId();
$authkey = $_ShopInfo->getAuthkey();

if (ord($id) != 0 && ord($authkey) != 0) {
?>
<html>
<head>
<meta http-equiv="CONTENT-TYPE" content="text/html; chatset=utf-8">
<title>관리자로그인</title>
</head>
<frameset rows="106,*,0" border=0>
<frame src="top.php" name=topframe noresize scrolling=no marginwidth=0 marginheight=0>
<frame src="main.php" name=bodyframe noresize scrolling=auto marginwidth=0 marginheight=0>
<frame src="counter_updateproc.php" name=hiddenframe noresize scrolling=no marginwidth=0 marginheight=0>
</frameset>
</body>
</html>
<?
} else {
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="stylesheet" href="<?=$Dir?>lib/style.css">
<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
<script language="JavaScript">
function CheckLogin() {
	if (!document.loginform.mem_id.value) {
		alert("아이디를 입력하세요.");
		document.loginform.mem_id.focus();
		return false;
	}
	if (!document.loginform.mem_pw.value) {
		alert("패스워드를 입력하세요.");
		document.loginform.mem_pw.focus();
		return false;
	}
	<?php if($_data->ssl_type=="Y" && ord($_data->ssl_domain) && ord($_data->ssl_port) && $_data->ssl_pagelist["ADMIN"]=="Y") {?>
	if(typeof document.loginform.ssllogin!="undefined") {
		if(document.loginform.ssllogin.checked) {
			document.loginform.action='https://<?=$_data->ssl_domain?><?=($_data->ssl_port!="443"?":".$_data->ssl_port:"")?>/<?=RootPath.SecureDir?>adminlogin.php';
		}
	}
	<?php }?>
}

function sslinfo() {
	window.open("<?=$Dir.FrontDir?>sslinfo.php","sslinfo","width=100,height=100,scrollbars=no");
}

</script>
</head>
<body onload="document.loginform.mem_id.focus()">
<table cellpadding="0" cellspacing="0" width="100%">
<form action="loginproc.php" method=post name=loginform onsubmit="return CheckLogin();">
<?php if($_data->ssl_type=="Y" && ord($_data->ssl_domain) && ord($_data->ssl_port) && $_data->ssl_pagelist["ADMIN"]=="Y") {?>
<input type=hidden name=shopurl value="<?=$_SERVER['HTTP_HOST']?>">
<?php }?>
<tr>
	<td height="100"></td>
</tr>
<tr>
	<td>
	<table align="center" cellpadding="0" cellspacing="0" width="600">
	<tr>
		<td style="padding-bottom:5"><img src="<?=$Dir?>images/admin_logo.gif" border="0"></td>
	</tr>
	<tr>
		<td>
		<table cellpadding="0" cellspacing="6" bgcolor="#F3F3F3" width="100%">
		<tr>
			<td bgcolor="#FFFFFF" style="padding:15,8,15,8">
			<table align="center" cellpadding="0" cellspacing="0" width="90%">
			<col width=155></col>
			<col width=15></col>
			<col width=></col>
			<col width=270></col>
			<col width=80></col>
			<tr>
				<td><IMG SRC="<?=$Dir?>images/admin_img01.gif"></td>
				<td></td>
				<td></td>
				<td>
				<table cellpadding="1" cellspacing="0" width="100%">
				<col width=125></col>
				<col width=></col>
				<tr>
					<td>* 운영자/부운영자ID</td>
					<td style="padding-left:4"><input type="text" name="mem_id" value="" size="20" maxlength="20" style="width:100%"></td>
				</tr>
				<tr>
					<td>* 비밀번호</td>
					<td style="padding-left:4"><input type="password" name="mem_pw" value="" size="20" maxlength="20" style="width:100%"></td>
				</tr>
				<tr>
					<td></td>
					<td>
					<?php if($_data->ssl_type=="Y" && ord($_data->ssl_domain) && ord($_data->ssl_port) && $_data->ssl_pagelist["ADMIN"]=="Y") {?>
					<input type=checkbox name=ssllogin value="Y" checked> <A HREF="javascript:sslinfo()">보안 접속</A>
					<?php }?>
					&nbsp;
					</td>
				</tr>
				</table>
				</td>
				<td align=right valign=top style="padding-top:7"><input type=image src="<?=$Dir?>images/admin_btn01.gif"></td>
			</tr>
			</table>
			</td>
		</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td style="padding:6,10">- <?=$_SERVER['HTTP_HOST']?>에서 부여받은 <b>운영자/부운영자</b>의 ID와 Password를 입력하세요.<br><img width=0 height=3><br>- <b>비밀번호관리에 주의하세요!</b></td>
	</tr>
	</table>
    </td>
</tr>
</form>
</table>
</body>
</html>
<?
}
?>