<?php 
$Dir="../";
?>
<html>
<head>
<title>보안접속</title>
<SCRIPT LANGUAGE="JavaScript">
<!--
function PageResize() {
	var oWidth = document.all.table_body.clientWidth + 10;
	window.resizeTo(oWidth,430);
}
//-->
</script>
</head>
<body topmargin=0 leftmargin=0 rightmargin=0 marginheight=0 marginwidth=0 onLoad="PageResize();">
<table align="center" cellpadding="0" cellspacing="0" width="540" style="table-layout:fixed;" id="table_body">
<tr>
	<td><img src="<?=$Dir?>images/common/security_title.gif" border="0"></td>
</tr>
<tr>
	<td style="padding-left:20px;padding-right:20px;">
	<table align="center" cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td><img src="<?=$Dir?>images/common/security_text01.gif" border="0" vspace="5"></td>
	</tr>
	<tr>
		<td style="padding-left:10pt;font-size:9pt;">쇼핑몰 접속(로그인)시 아이디 및 비밀번호 등 중요데이타 암호화를 통해 안전하게 <br>전송시킬
		수 있습니다. 단, 암호화 및 복호화 처리로 인해서 일반접속보다 속도는 떨어지게<br>
		되지만 보안상 안전함으로 보안접속을 권장합니다.<br><br>
		보안접속으로 접속할 경우 웹 브라우져의 설정에 따라 "보안경고" 창이 뜰 수도 있습니다.<br>
		"보안경고" 창은 암호화가 잘 이루어 지고 있으며 보안이 향상됨을 알려주기 위한 경고창<br>입니다.</td>
	</tr>
	<tr>
		<td><img src="<?=$Dir?>images/common/security_line.gif" border="0" vspace="5"></td>
	</tr>
	<tr>
		<td><img src="<?=$Dir?>images/common/security_text02.gif" border="0" vspace="5"></td>
	</tr>
	<tr>
		<td style="padding-left:10pt;font-size:9pt;">쇼핑몰 접속(로그인)시 일반적인 웹 전송규약인 http를 이용하며 전송시 전송데이타를<br>
		암호화 하지 않음으로 속도면에서는 빠르지만 보안상 문제가 발생할 소지가 있습니다.</td>
	</tr>
	<tr>
		<td><img src="<?=$Dir?>images/common/security_line.gif" border="0" vspace="5"></td>
	</tr>
	<tr>
		<td align="center"><a href="javascript:self.close();"><img src="<?=$Dir?>images/common/bigview_btnclose.gif" border="0"></a></td>
	</tr>
	</table>
	</td>
</tr>
</table>
</body>
</html>
