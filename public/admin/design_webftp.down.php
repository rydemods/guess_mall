<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

$type=$_POST["type"];
$dir=$_POST["val"];
$file=$_POST["file"];

$path="";

if(ord($_ShopInfo->getId())==0){
	echo "<script>window.close();</script>";
	exit;
}

$originalpath = $Dir.DataDir."design/";
//$originalpath=str_replace("..","",$originalpath);
$originallength=strlen($originalpath);

$dir=str_replace("..","",$dir); // 상위디렉토리로 이동 금지
$dir=str_replace(" ","",$dir);  // 공백 제거

if(ord($dir)==0)
	$path=$originalpath;
else {
	$dir=str_replace("..","",$dir); // 상위디렉토리로 이동 금지
	$dir=str_replace(" ","",$dir);  // 공백 제거
	$path=$originalpath.$dir."/";
}

if(strlen($path)<strlen($originalpath)) $path=$originalpath;

if($type=="download" && ord($file)) {
	Header("Content-Disposition: attachment; filename=$file");
	Header("Content-Type: application/octet-stream;");
	Header("Pragma: no-cache");
	Header("Expires: 0");
	Header("Content-type: application/octet-stream");

	readfile($path.$file);
	exit;
}

?>
<html>
<head>
<meta http-equiv='Content-Type' content='text/html;charset=utf-8'>
<title>파일다운로드</title>
<style>td {font-size:9pt; font-family: 굴림;}</style>
<SCRIPT LANGUAGE="JavaScript">
<!--
function PageResize() {
	var oWidth = document.all.table_body.clientWidth + 10;
	var oHeight = document.all.table_body.clientHeight + 70;

	window.resizeTo(oWidth,oHeight);
}

function CheckForm(gbn) {
	iscnt=false;

	for(i=0;i<document.form1.file.options.length;i++) {
		if(document.form1.file.options[i].selected) {
			if(document.form1.file.options[i].value.length>0) {
				iscnt=true;
			}
		}
	}
	if(iscnt==false) {
		alert("다운받을 파일을 선택하세요.");
		document.form1.file.focus();
		return;
	}

	document.form1.type.value="download";
	document.form1.submit();
}
//-->
</SCRIPT>
</head>
<body leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 oncontextmenu="return false" style="overflow-x:hidden;overflow-y:hidden;" oncontextmenu="return false" onLoad="PageResize();">
<TABLE WIDTH="350" BORDER=0 CELLPADDING=0 CELLSPACING=0 id=table_body>
<TR>
	<TD>
	<table cellpadding="0" cellspacing="0" width="100%">
	<col width=212></col>
	<col width=></col>
	<col width=20></col>
	<tr>
		<td><img src="images/design_html_title.gif" border="0"></td>
		<td background="images/member_mailallsend_imgbg.gif">&nbsp;</td>
		<td align=right><img src="images/member_mailallsend_img2.gif" border="0"></td>
	</tr>
	</table>
	</TD>
</TR>
<form name=form1 method=post action="<?=$_SERVER['PHP_SELF']?>">
<input type=hidden name=type>
<input type=hidden name=val value="<?=$dir?>">
<tr>
	<TD style="padding:5pt;">
	<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td>
		<select name=file size=20 style="width:100%;font-size:12px;">
<?php
		$temp=getFileList(rtrim($path,'/'));
		@sort($temp);
		for($i=0;$i<count($temp);$i++) {
			$filename=str_replace("*","",$temp[$i]);
			echo "<option value=\"{$filename}\">{$filename}</option>\n";
		}
		if ($i==0) echo "<option value=\"\">파일이 없습니다.";
?>
		</select>
		</td>
	</tr>
	</table>
	</TD>
</tr>
<tr>
	<td style="padding:10">
	<table border=0 cellpadding=2 cellspacing=0 width=100%>
	<tr>
		<td style="color:#6063EA">다운받으실 파일을 선택 후 <FONT COLOR="red">[다운로드]</FONT>를 누르세요.</td>
	</tr>
	</table>
	</td>
</tr>
<tr><td height=5></td></tr>
<TR>
	<TD align=center>
	<input type=button value="다운로드" style="cursor:hand;" onclick="CheckForm()">
	<input type=button value="닫기" style="cursor:hand;" onclick="window.close()">
	</TD>
</TR>
</form>
<tr><td height=10></td></tr>
</table>
</body>
</html>