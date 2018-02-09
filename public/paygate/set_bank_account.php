<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

Header("Pragma: no-cache");

$ordercode=$_POST["ordercode"];
$mode=$_POST["mode"];

$sql="SELECT * FROM tblpvirtuallog WHERE ordercode='".$ordercode."' ";
$result=pmysql_query($sql,get_db_conn());
if($row=pmysql_fetch_object($result)) {
	if($row->paymethod!="Q") {
		echo "<html><head><title></title></head><body onload=\"alert('해당 매매보호 결제내역이 존재하지 않습니다.');window.close();\"></body></html>";exit;
	}
	$refund_account=$row->refund_account;
	$refund_name=$row->refund_name;
	$refund_bank_code=$row->refund_bank_code;
} else {
	echo "<html><head><title></title></head><body onload=\"alert('해당 결제내역이 존재하지 않습니다.');window.close();\"></body></html>";exit;
}
pmysql_free_result($result);

if($mode=="update") {
	$refund_account=$_POST["refund_account"];
	$refund_name=$_POST["refund_name"];
	$refund_bank_code=$_POST["refund_bank_code"];
	if(strlen($refund_account)>0 && strlen($refund_name)>0 && strlen($refund_bank_code)>0) {
		$sql = "UPDATE tblpvirtuallog SET ";
		$sql.= "refund_account	= '".$refund_account."', ";
		$sql.= "refund_name		= '".$refund_name."', ";
		$sql.= "refund_bank_code= '".$refund_bank_code."' ";
		$sql.= "WHERE ordercode	='".$ordercode."' ";
		pmysql_query($sql,get_db_conn());
		$onload="<script>alert(\"환불계좌정보 등록이 완료되었습니다.\")</script>";
	}
}
?>
<html>
<head>
<title>환불계좌정보 등록</title>
<meta http-equiv="CONTENT-TYPE" content="text/html;charset=EUC-KR">
<style>
td {font-family:Tahoma;color:666666;font-size:9pt;}

tr {font-family:Tahoma;color:666666;font-size:9pt;}
BODY,TD,SELECT,DIV,form,TEXTAREA,center,option,pre,blockquote {font-family:Tahoma;color:000000;font-size:9pt;}

A:link    {color:333333;text-decoration:none;}

A:visited {color:333333;text-decoration:none;}

A:active  {color:333333;text-decoration:none;}

A:hover  {color:#CC0000;text-decoration:none;}
</style>
<SCRIPT LANGUAGE="JavaScript">
<!--
window.resizeTo(360,240);

function CheckForm() {
	if(document.form1.refund_account.value.length==0) {
		alert("환불수취계좌번호를 입력하세요.");
		document.form1.refund_account.focus();
		return;
	}
	if(document.form1.refund_name.value.length==0) {
		alert("환불수취계좌주명을 입력하세요.");
		document.form1.refund_name.focus();
		return;
	}
	if(document.form1.refund_bank_code.value.length==0) {
		alert("환불수취은행을 선택하세요.");
		document.form1.refund_bank_code.focus();
		return;
	}
	if(confirm("환불계좌 정보를 등록하시겠습니까?")) {
		document.form1.mode.value="update";
		document.form1.submit();
	}
}
//-->
</SCRIPT>
</head>
<body topmargin="0" leftmargin="0" rightmargin="0" marginheight="0" marginwidth="0">
<table border="0" cellpadding="0" cellspacing="0" width="100%">
<form name=form1 method=post action="<?=$_SERVER[PHP_SELF]?>">
<input type=hidden name=mode>
<input type=hidden name=ordercode value="<?=$ordercode?>">
<TR>
	<TD><img src="<?=$Dir?>images/set_bank_account_title.gif" border="0"></TD>
</TR>
<tr>
	<td height="5"></td>
</tR>
<TR>
	<TD style="padding:6pt;">
	<TABLE cellSpacing="0" cellPadding="0" width="100%" border="0">
	<col width="120"></col>
	<col></col>
	<TR>
		<TD height="2" colspan="2" bgcolor="#000000"></TD>
	</TR>
	<TR>
		<TD bgcolor="#F8F8F8" style="padding:5pt;padding-left:10pt;line-height:18px;letter-spacing:-0.5pt;"><img src="<?=$Dir?>images/icon_point2.gif" border="0"><b>환불수취계좌번호</b></TD>
		<TD style="padding:3pt;padding-right:2pt;line-height:18px;BORDER-LEFT:#E3E3E3 1pt solid;"><input type=text name="refund_account" value="<?=$refund_account?>" size="15" style="width:100%;" class="input"></TD>
	</TR>
	<TR>
		<TD height="1" colspan="2" bgcolor="#EDEDED"></TD>
	</TR>
	<TR>
		<TD bgcolor="#F8F8F8" style="padding:5pt;padding-left:10pt;line-height:18px;letter-spacing:-0.5pt;"><img src="<?=$Dir?>images/icon_point2.gif" width="8" height="11" border="0">환불수취계좌주명</TD>
		<TD style="padding:3pt;padding-right:2pt;line-height:18px;BORDER-LEFT:#E3E3E3 1pt solid;"><input type=text name="refund_name" value="<?=$refund_name?>" size=15 style="width:100%;" class="input"></TD>
	</TR>
	<TR>
		<TD height="1" colspan="2" bgcolor="#EDEDED"></TD>
	</TR>
	<TR>
		<TD bgcolor="#F8F8F8" style="padding:5pt;padding-left:10pt;line-height:18px;letter-spacing:-0.5pt;"><img src="<?=$Dir?>images/icon_point2.gif" width="8" height="11" border="0">환불수취은행선택</TD>
		<TD style="padding:3pt;padding-right:2pt;line-height:18px;BORDER-LEFT:#E3E3E3 1pt solid;"><select name="refund_bank_code" class="select">
			<option value="">선택</option>
			<option value="39"<?if($refund_bank_code=="39")echo" selected";?>>경남은행</option>
			<option value="03"<?if($refund_bank_code=="03")echo" selected";?>>기업은행</option>
			<option value="32"<?if($refund_bank_code=="32")echo" selected";?>>부산은행</option>
			<option value="07"<?if($refund_bank_code=="07")echo" selected";?>>수협중앙회</option>
			<option value="48"<?if($refund_bank_code=="48")echo" selected";?>>신협</option>
			<option value="71"<?if($refund_bank_code=="71")echo" selected";?>>우체국</option>
			<option value="23"<?if($refund_bank_code=="23")echo" selected";?>>제일은행</option>
			<option value="06"<?if($refund_bank_code=="06")echo" selected";?>>주택은행</option>
			<option value="81"<?if($refund_bank_code=="81")echo" selected";?>>하나은행</option>
			<option value="34"<?if($refund_bank_code=="34")echo" selected";?>>광주은행</option>
			<option value="11"<?if($refund_bank_code=="11")echo" selected";?>>농협중앙회</option>
			<option value="02"<?if($refund_bank_code=="02")echo" selected";?>>산업은행</option>
			<option value="53"<?if($refund_bank_code=="53")echo" selected";?>>시티은행</option>
			<option value="05"<?if($refund_bank_code=="05")echo" selected";?>>외환은행</option>
			<option value="09"<?if($refund_bank_code=="09")echo" selected";?>>장기신용</option>
			<option value="35"<?if($refund_bank_code=="35")echo" selected";?>>제주은행</option>
			<option value="16"<?if($refund_bank_code=="16")echo" selected";?>>축협중앙회</option>
			<option value="27"<?if($refund_bank_code=="27")echo" selected";?>>한미은행</option>
			<option value="04"<?if($refund_bank_code=="04")echo" selected";?>>국민은행</option>
			<option value="31"<?if($refund_bank_code=="31")echo" selected";?>>대구은행</option>
			<option value="25"<?if($refund_bank_code=="25")echo" selected";?>>서울은행</option>
			<option value="26"<?if($refund_bank_code=="26")echo" selected";?>>신한은행</option>
			<option value="20"<?if($refund_bank_code=="20")echo" selected";?>>우리은행</option>
			<option value="37"<?if($refund_bank_code=="37")echo" selected";?>>전북은행</option>
			<option value="21"<?if($refund_bank_code=="21")echo" selected";?>>조흥은행</option>
			<option value="83"<?if($refund_bank_code=="83")echo" selected";?>>평화은행</option>
		</select></TD>
	</TR>
	<TR>
		<TD height="2" colspan="2" bgcolor="#000000"></TD>
	</TR>
	</TABLE>
	</td>
</TR>
<tr>
	<td align="center"><a href="javascript:CheckForm();"><img src="<?=$Dir?>images/btn_write1a.gif" border="0" vspace="5" hspace="5"><a href="javascript:window.close();"><img src="<?=$Dir?>images/btn_small_close.gif" border="0" vspace="5"></a></td>
</tr>
</form>
</table>

<?=$onload?>

</body>
</html>