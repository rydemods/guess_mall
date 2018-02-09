<?php
/********************************************************************* 
// 파 일 명		: login.php 
// 설     명		: 입점업체 관리자모드 로그인
// 상세설명	: 입점업체 관리자모드의 로그인 페이지
// 작 성 자		: hspark
// 수 정 자		: 2015.10.23 - 김재수
// 
// 
*********************************************************************/ 
?>
<?php
#---------------------------------------------------------------
# 기본정보 설정파일을 가져온다.
#---------------------------------------------------------------
	$Dir="../";
	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");
	include_once($Dir."lib/venderlib.php");

#---------------------------------------------------------------
# 로그인상태를 확인하기위해 벤더정보를 가져온다. 비로고인 상태이면 NULL
#---------------------------------------------------------------
	$_VenderInfo = new _VenderInfo($_COOKIE[_vinfo]);
	$id      = $_VenderInfo->getVidx();	// 벤더 인덱스값

	if (ord($id) != '') {					// 로그인이 된 경우
?>
<html>
<head>
<meta http-equiv="CONTENT-TYPE" content="text/html; chatset=UTF-8">
<title>쇼핑몰 입점 관리자</title>
</head>
<frameset rows="*,0" border=0>
<frame src="main.php" name=bodyframe noresize scrolling=auto marginwidth=0 marginheight=0>
<frame src="blank.php" name=hiddenframe noresize scrolling=no marginwidth=0 marginheight=0>
</frameset>
</body>
</html>
<?
	} else {									// 로그인이 안된 경우
?>
<html>
<head>
<link rel="stylesheet" href="style.css">
<meta http-equiv="CONTENT-TYPE" content="text/html; charset=UTF-8">
<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
<script language="JavaScript">
function CheckLogin() {
	if (!document.loginform.id.value) {
		alert("아이디를 입력하세요.");
		document.loginform.id.focus();
		return false;
	}
	if (!document.loginform.passwd.value) {
		alert("비밀번호를 입력하세요.");
		document.loginform.passwd.focus();
		return false;
	}
	<?if($_data->ssl_type=="Y" && strlen($_data->ssl_domain)>0 && strlen($_data->ssl_port)>0 && $_data->ssl_pagelist["ADMIN"]=="Y") {?>
	if(typeof document.loginform.ssllogin!="undefined") {
		if(document.loginform.ssllogin.checked) {
			document.loginform.action='https://<?=$_data->ssl_domain?><?=($_data->ssl_port!="443"?":".$_data->ssl_port:"")?>/<?=RootPath.SecureDir?>venderlogin.php';
		}
	}
	<?}?>
}

function sslinfo() {
	window.open("<?=$Dir.FrontDir?>sslinfo.php","sslinfo","width=100,height=100,scrollbars=no");
}

</script>
</head>
<body onload="document.loginform.id.focus()">
<table cellpadding="0" cellspacing="0" width="100%">
<form method=post name=loginform action="<?=$Dir.VenderDir?>loginproc.php" onsubmit="return CheckLogin();">
<?if($_data->ssl_type=="Y" && strlen($_data->ssl_domain)>0 && strlen($_data->ssl_port)>0 && $_data->ssl_pagelist["VLOGN"]=="Y") {?>
<input type=hidden name=shopurl value="<?=$_SERVER['HTTP_HOST']?>">
<?}?>
<tr>
	<td height="100"></td>
</tr>
<tr>
	<td>
	<table align="center" cellpadding="0" cellspacing="0" width="600">
	<tr>
		<td width="665" class="font_size"><img src="<?=$Dir?>images/admin_logo.gif" width="173" height="54" border="0" vspace="5" alt="admin_vender_logo.gif"></td>
	</tr>
	<tr>
		<td width="665">
		<table cellpadding="0" cellspacing="6" bgcolor="#F3F3F3" width="100%">
		<tr>
			<td bgcolor="white" style="padding-top:15pt; padding-right:8pt; padding-bottom:15pt; padding-left:8pt;">
			<table align="center" cellpadding="0" cellspacing="0" width="90%">
			<tr>
				<td><IMG SRC="<?=$Dir?>images/vender_admin_img01.gif" WIDTH=155 HEIGHT=82 ALT=""></td>
				<td width="15"></td>
				<td>&nbsp;</td>
				<td width="272">
				<table cellpadding="1" cellspacing="0" width="100%">
				<tr>
					<td class="skin_font_black1" width="125"><span class="skin_font_red">* </span>운영자/부운영자ID</td>
					<td style="padding-left:4"><input type="text" name="id" value="" size="20" maxlength="20" style="WIDTH: 100%" class="skin_input"></td>
				</tr>
				<tr>
					<td class="skin_font_black1" width="125"><span class="skin_font_red">* </span>비밀번호</td>
					<td style="padding-left:4"><input type="password" name="passwd" value="" size="20" maxlength="50" style="WIDTH: 100%" name=id class="skin_input"></td>
				</tr>
				<tr>
					<td class="skin_font_black1" width="125"></td>
					<td>
					<?if($_data->ssl_type=="Y" && strlen($_data->ssl_domain)>0 && strlen($_data->ssl_port)>0 && $_data->ssl_pagelist["VLOGN"]=="Y") {?>
					<input type=checkbox name=ssllogin value="Y"> <A HREF="javascript:sslinfo()">보안 접속</A>
					<?}?>
					</td>
				</tr>
				</table>
				</td>
				<td width="78" align=right valign=top style="padding-top:7"><input type=image src="<?=$Dir?>images/admin_btn01.gif" style="width:74px;height:48"></td>
			</tr>
			</table>
			</td>
		</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td width="665" style="padding-top:6pt; padding-left:10;" class="font_size">- <? echo $_data->shopname ?>에서 제공한 업체 <span class="font_orange2"><b>ID</b></span>와 <span class="font_orange2"><b>Password</b></span>를 입력하세요.<br>- <b><span class="font_orange2">비밀번호관리에 주의하세요!</span></b></td>
	</tr>
	</table>
    </td>
</tr>
</form>
</table>
</center>
</body>
</html>
<?
	}
?>
