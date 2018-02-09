<?php
/********************************************************************* 
// 파 일 명		: product_select.php 
// 설     명		: 제조사, 원산지, 매입처 목록
// 상세설명	: 관리자 입점관리의 상품등록시 제조사, 원산지, 매입처을 관리한다.
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
	include("access.php");

#---------------------------------------------------------------
# 넘어온 값들을 정리한다.
#---------------------------------------------------------------
	$excel_sql				= $_POST["excel_sql"];
	$excel_sql_orderby	= $_POST["excel_sql_orderby"];
	$productcodes		= $_POST["productcodes"];
	$excel_sql_add		= $productcodes?" AND productcode IN ('".str_replace(",","','", $productcodes)."') ":"";
	$excel_sql				= $excel_sql.$excel_sql_add.$excel_sql_orderby;

	$fields = parse_ini_file("./product_csv_download_conf.ini", true);

	$fp = fopen('php://temp', 'w+');

	$arritem_pro = array();
	$arritem_opt = array();
	foreach ( $fields as $key => $arr ){
		$arrtmp_pro	= array();
		$arrtmp_opt	= array();
		if ( $arr['down'] == 'Y') {
			if ($arr['type'] == 'PD' ) {
				$arrtmp_pro['text']		= $arr['text'];
				$arrtmp_pro['val']		= $key;
				$arritem_pro[]			= $arrtmp_pro;
			} else if ($arr['type'] == 'OPT' ) {
				$arrtmp_opt['text']		= $arr['text'];
				$arrtmp_opt['val']		= $key;
				$arritem_opt[]			= $arrtmp_opt;
			}
		}
	}
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title><?=$pagename[$type]?> 선택하기</title>
<link rel="stylesheet" href="style.css" type="text/css">
<script type="text/javascript" src="lib.js.php"></script>
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
function CheckForm(chkType) {
	if(chkType =='download') {
		//document.form1.target = "HiddenFrame";
		document.form1.submit();
	} else if(chkType =='download_opt') {
		//document.form2.target = "HiddenFrame";
		document.form2.submit();
	}
}
//-->
</SCRIPT>
</head>
<body leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 oncontextmenu="return false" style="overflow-x:hidden;overflow-y:hidden;" ondragstart="return false" onselectstart="return false" oncontextmenu="return false" onLoad="PageResize();">

<TABLE WIDTH="255" BORDER=0 CELLPADDING=0 CELLSPACING=0 style="table-layout:fixed;" id=table_body>
<tr>
	<td>
	<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
	<TR>
		<TD>
		<table cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td><img src="images/newtitle_icon.gif" border="0" width="29" height="31"></td>
			<td width="100%" background="images/member_mailallsend_imgbg.gif">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td><b><font color="white">엑셀 다운로드</b></font></td>
			</tr>
			</table>
			</td>
			<td align="right"><img src="images/member_mailallsend_img2.gif" width="20" height="31" border="0"></td>
		</tr>
		</table>
		</TD>
	</TR>
	<TR>
		<TD height="10"></TD>
	</TR>
	<tr>
		<TD style="padding-left:4pt;padding-right:4pt;" valign="top">
		<table border=0 cellpadding=0 cellspacing=0 width="100%">
		<tr><td height="1" colspan="2" bgcolor="red"></td></tr>
		<tr>
			<td width=40% bgcolor='F5F5F5' background="images/line01.gif" style="background-repeat:repeat-y;background-position:right;padding:9"><b>상품정보</b></td>
			<td align="center"><a href="javascript:CheckForm('download');"><img src=images/btn_exceldown.gif border=0></a></td>
		</tr>
		<tr><td height="1" colspan="2" bgcolor="E7E7E7"></td></tr>
		<tr>
			<td bgcolor='F5F5F5' background="images/line01.gif" style="background-repeat:repeat-y;background-position:right;padding:9"><b>옵션정보</b></td>
			<td align="center"><a href="javascript:CheckForm('download_opt');"><img src=images/btn_exceldown.gif border=0></a></td>
		</tr>
		<tr><td height="1" colspan="2" bgcolor="E7E7E7"></td></tr>
		</table>
		</TD>
	</tr>
	<TR>
		<TD height="10"></TD>
	</TR>
	<TR>
		<TD align=center><a href="javascript:window.close();"><img src="images/btn_close.gif" border="0" hspace="2"></a></TD>
	</TR>
	</table>
	</td>
</tr>
</TABLE>
<form name=form1 action="product_csv_download_indb_v3.php" method=post>
	<input type=hidden name="mode" value="download">
	<input type=hidden name="item_type" value="product">
	<input type=hidden name="excel_sql" value="<?=$excel_sql?>">
<?php
	foreach ($arritem_pro as $key => $val) {
?>
	<input type=hidden name="est[]" value="<?=$val['val']?>">
<?
	}
?>
</form>
<form name=form2 action="product_csv_download_indb_v3.php" method=post>
	<input type=hidden name="mode" value="download_opt">
	<input type=hidden name="item_type" value="product_opt">
	<input type=hidden name="excel_sql" value="<?=$excel_sql?>">
<?php
	foreach ($arritem_opt as $key => $val) {
?>
	<input type=hidden name="est[]" value="<?=$val['val']?>">
<?
	}
?>
</form>

<?=$onload?>
</body>
</html>
