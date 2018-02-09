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
<meta http-equiv="CONTENT-TYPE" content="text/html; chatset=UTF-8">
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
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<link href="webdoc/css/style.css" rel="stylesheet">
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

<body onload="document.loginform.mem_id.focus()" style="background:#000;">

<form action="loginproc.php" method=post name=loginform onsubmit="return CheckLogin();">
<?php if($_data->ssl_type=="Y" && ord($_data->ssl_domain) && ord($_data->ssl_port) && $_data->ssl_pagelist["ADMIN"]=="Y") {?>
<input type=hidden name=shopurl value="<?=$_SERVER['HTTP_HOST']?>">
<?php }?>

<div class="admin_login_wrap">
	<div class="admin_login">
		<h1 class="logo">AJASHOP</h1>
		<p>관리자 페이지 입니다.<br>관리자모드는 관리자 아이디를 이용한 접속만 가능합니다.</p>
		<!--<h1>ADMIN LOGIN</h1>-->
			<div class="table_s02" style="padding-top:30px;">
				<table cellpadding=0 cellspacing=0 border=0  class="field">
					<colgroup><col width="" /><col width="200" /><col width="100" /></colgroup>
					<tr>
						<td class="none"><input name="mem_id" type="text" class="inp" value='' placeholder="관리자 아이디" style="width:310px;"></td>
					</tr>
					<tr>
						<td><input name="mem_pw" type="password" class="inp" value='' placeholder="비밀번호" style="width:310px;"></td>
					</tr>
					<tr>
						<td><input type="submit" value="" class="btn_login" /></td>
					</tr>
				</table>
			</div>
		</div>
	</div>

</form>

</body>
</html>
<?
}
?>