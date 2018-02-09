<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

$filename=$_POST["val"];

if(ord($_ShopInfo->getId())==0 || ord($filename)==0){
	echo "<script>window.close();</script>";
	exit;
}

$filepath = $Dir.DataDir."design/".$filename;

if(file_exists($filepath)===false) {
	alert_go('해당 파일이 존재하지 않습니다.','c');
} else {
	$size = getimagesize($filepath);
	$height=$size[1];
	$width=$size[0];
}
if($i=strrpos($filename,"/")) {
	$filename1=substr($filename,0,strrpos($filename,"/")+1);
	$filename2=substr($filename,strrpos($filename,"/")+1);
	$filename=$filename1.urlencode($filename2);
} else {
	$filename=urlencode($filename);
}
?>
<html>
<head>
<meta http-equiv='Content-Type' content='text/html;charset=utf-8'>
<title>이미지보기</title>
<style>td {font-size:9pt; font-family: 굴림;}</style>
<SCRIPT LANGUAGE="JavaScript">
<!--
function PageResize() {
	var oWidth = document.all.table_body.clientWidth + 10;
	var oHeight = document.all.table_body.clientHeight + 75;

	window.resizeTo(oWidth,oHeight);
}
//-->
</SCRIPT>
</head>
<body leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 oncontextmenu="return false" style="overflow-x:hidden;" oncontextmenu="return false" onLoad="PageResize();">

<TABLE BORDER=0 CELLPADDING=0 CELLSPACING=0 id=table_body>
<TR>
	<TD>
	<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td><img src="images/design_img_title.gif" border="0"></td>
		<td width="100%" background="images/member_mailallsend_imgbg.gif">&nbsp;</td>
		<td align=right><img src="images/member_mailallsend_img2.gif" width="20" height="31" border="0"></td>
	</tr>
	</table>
	</TD>
</TR>
<tr>
	<TD style="padding:10pt;">
	<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td align=center>
			<font color="#0066FF">이미지크기 : <?=$width?>x<?=$height?></font>
		</TD>
	</TR>
	</font>                
	<tr>
		<td align=center>
			<a href="javascript:window.close();"><img src="<?=$Dir.DataDir?>design/<?=$filename?>" border=0></a>
		</td>
	</tr>
	</table>
	</TD>
</tr>
<TR>
	<TD align=center>
		<a href="javascript:window.close()"><img src="images/btn_close.gif" border="0" border=0></a>	
	</TD>
</TR>
</TABLE>
</body>
</html>
