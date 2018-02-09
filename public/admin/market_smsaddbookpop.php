<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

if(ord($_ShopInfo->getId())==0){
	echo "<script>alert('정상적인 경로로 접근하시기 바랍니다.');window.close();</script>";
	exit;
}

$type=$_POST["type"];
$mobile=$_POST["mobile"];

$mode=$_POST["mode"];
$up_name=$_POST["up_name"];
$up_mobile1=$_POST["up_mobile1"];
$up_mobile2=$_POST["up_mobile2"];
$up_mobile3=$_POST["up_mobile3"];
$up_group=$_POST["up_group"];
$up_new_group=$_POST["up_new_group"];
$up_memo=$_POST["up_memo"];

$_sms="";
if($type=="update" && ord($mobile)) {
	$sql = "SELECT * FROM tblsmsaddress WHERE mobile='{$mobile}' ";
	$result=pmysql_query($sql,get_db_conn());
	if($row=pmysql_fetch_object($result)) {
		$_sms=$row;
	} else {
		echo "<script>window.close();</script>";
		exit;
	}
	pmysql_free_result($result);
} else if($mode=="update") {
	$up_mobile=$up_mobile1."-{$up_mobile2}-".$up_mobile3;
	if(ord($up_new_group)) $up_group=$up_new_group;

	$sql = "UPDATE tblsmsaddress SET ";
	$sql.= "name		= '{$up_name}', ";
	$sql.= "mobile		= '{$up_mobile}', ";
	$sql.= "addr_group	= '{$up_group}', ";
	$sql.= "memo		= '{$up_memo}' ";
	$sql.= "WHERE mobile='{$mobile}' ";
	pmysql_query($sql,get_db_conn());
	echo "</head><body onload=\"alert('수정되었습니다.');opener.location.reload();window.close();\"></body></html>";exit;

} else if($mode=="insert") {
	$up_mobile=$up_mobile1."-{$up_mobile2}-".$up_mobile3;
	if(ord($up_new_group)) $up_group=$up_new_group;

	$sql = "INSERT INTO tblsmsaddress(
	name		,
	mobile		,
	addr_group	,
	memo		,
	date) VALUES (
	'{$up_name}', 
	'{$up_mobile}', 
	'{$up_group}', 
	'{$up_memo}', 
	'".date("YmdHis")."')";
	pmysql_query($sql,get_db_conn());
	echo "</head><body onload=\"alert('등록되었습니다.');opener.location.reload();window.close();\"></body></html>";exit;
} else {
	$type="insert";
}

$arrmobile=explode("-",$_sms->mobile);
?>

<html>
<head>
<meta http-equiv='Content-Type' content='text/html;charset=utf-8'>
<title>SMS 주소 등록/수정</title>
<link rel="stylesheet" href="style.css" type="text/css">
<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
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

function CheckForm() {
	if(document.form1.up_name.value.length==0) {
		alert("이름을 입력하세요.");
		document.form1.up_name.focus();
		return;
	}
	if(document.form1.up_mobile1.value.length==0) {
		alert("휴대폰 번호를 입력하세요.");
		document.form1.up_mobile1.focus();
		return;
	}
	if(!IsNumeric(document.form1.up_mobile1.value)) {
		alert("휴대폰 번호는 숫자만 입력하세요.");
		document.form1.up_mobile1.focus();
		return;
	}
	if(document.form1.up_mobile2.value.length==0) {
		alert("휴대폰 번호를 입력하세요.");
		document.form1.up_mobile2.focus();
		return;
	}
	if(!IsNumeric(document.form1.up_mobile2.value)) {
		alert("휴대폰 번호는 숫자만 입력하세요.");
		document.form1.up_mobile2.focus();
		return;
	}
	if(document.form1.up_mobile3.value.length==0) {
		alert("휴대폰 번호를 입력하세요.");
		document.form1.up_mobile3.focus();
		return;
	}
	if(!IsNumeric(document.form1.up_mobile3.value)) {
		alert("휴대폰 번호는 숫자만 입력하세요.");
		document.form1.up_mobile3.focus();
		return;
	}
	if(document.form1.up_group.value.length==0 && document.form1.up_new_group.value.length==0) {
		alert("그룹을 선택하시거나 신규그룹을 입력하세요.");
		document.form1.up_group.focus();
		return;
	}
	document.form1.submit();
}
//-->
</SCRIPT>
</head>
<link rel="styleSheet" href="/css/admin.css" type="text/css">
<meta http-equiv='Content-Type' content='text/html;charset=utf-8'>
<title>SMS 주소 등록/수정</title>
<link rel="stylesheet" href="style.css" type="text/css">

<div class="pop_top_title"><p>SMS 주소 등록/수정</p></div>

<body leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 oncontextmenu="return false" style="overflow-x:hidden;overflow-y:hidden;" oncontextmenu="return false" onLoad="PageResize();">

<TABLE WIDTH="400" BORDER=0 CELLPADDING=0 CELLSPACING=0 style="table-layout:fixed;" id=table_body>
<form name=form1 method=post action="<?=$_SERVER['PHP_SELF']?>">
<input type=hidden name=mode value="<?=$type?>">
<input type=hidden name=mobile value="<?=$mobile?>">
<tr>
	<TD style="padding:10pt;">
	<div class="table_style01">
	<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
	<TR>
		<th><span>이 름</span></th>
		<TD class="td_con1" width="300"><INPUT maxLength=20 value="<?=$_sms->name?>" name=up_name class="input"></TD>
	</TR>
	<TR>
		<th><span>휴대폰 번호</span></th>
		<TD class="td_con1">
		<INPUT onkeyup=strnumkeyup(this) maxLength=3 size=4 value="<?=$arrmobile[0]?>" name=up_mobile1 class="input"> - 
		<INPUT onkeyup=strnumkeyup(this) maxLength=4 size=4 value="<?=$arrmobile[1]?>" name=up_mobile2 class="input"> - 
		<INPUT onkeyup=strnumkeyup(this) maxLength=4 size=4 value="<?=$arrmobile[2]?>" name=up_mobile3 class="input">
		</TD>
	</TR>
	<TR>
		<th><span>그룹선택</span></th>
		<TD class="td_con1">
		기존그룹: 
		<SELECT name=up_group class="input">
		<OPTION value="">그룹을 선택하세요.</OPTION>
<?php
		$sql = "SELECT addr_group FROM tblsmsaddress GROUP BY addr_group ";
		$result=pmysql_query($sql,get_db_conn());
		while($row=pmysql_fetch_object($result)) {
			echo "<option value=\"{$row->addr_group}\"";
			if($_sms->addr_group==$row->addr_group) echo " selected";
			echo ">{$row->addr_group}</option>\n";
		}
		pmysql_free_result($result);
?>
		</SELECT>
		<br>신규생성: <INPUT maxLength=20 name=up_new_group class="input">
		</TD>
	</TR>
	<TR>
		<th><span>기타메모</span></th>
		<TD class="td_con1"><TEXTAREA style="WIDTH: 100%; HEIGHT: 72px" name=up_memo maxlength="100" class="textarea"><?=$_sms->memo?></TEXTAREA></TD>
	</TR>
	</TABLE>
	</div>
	</TD>
</tr>
<TR>
	<TD align="center"><a href="javascript:CheckForm()"><img src="images/btn_ok1.gif" border="0" vspace="0" border=0></a><a href="javascript:window.close();"><img src="images/btn_close.gif" border="0" vspace="0" border=0 hspace="2"></a></TD>
</TR>
</form>
</TABLE>
<?=$onload?>
</body>
</html>
