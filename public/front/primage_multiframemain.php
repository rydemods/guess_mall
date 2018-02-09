<?php 
$Dir="../";
include_once($Dir."lib/init.php");

$productcode=$_REQUEST["productcode"];
$size=$_REQUEST["size"];
?>
<html>
<head>
<title>상품확대보기</title>
<meta http-equiv="CONTENT-TYPE" content="text/html;charset=EUC-KR">
<meta http-equiv="imagetoolbar" content="no">
<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
<style>
td {font-family:돋음;color:666666;font-size:9pt;}

tr {font-family:돋음;color:666666;font-size:9pt;}
BODY,TD,SELECT,DIV,form,TEXTAREA,center,option,pre,blockquote {font-family:Tahoma;color:000000;font-size:9pt;}

</style>
<SCRIPT LANGUAGE="JavaScript">
<!--
function primage_view(productcode) {
	tmp = "height=350,width=450,toolbar=no,menubar=no,resizable=no,status=no,scrollbars=yes";
	sc="yes";
	url = "<?=$Dir.FrontDir?>primage_multiview.php?productcode="+productcode+"&scroll="+sc;

	window.open(url,"primage_view",tmp);
}

//-->
</SCRIPT>
</head>

<body topmargin=0 leftmargin=0 rightmargin=0 marginheight=0 marginwidth=0 onselectstart="return false" ondragstart="return false" oncontextmenu="return false">

<table border=0 cellpadding=0 cellspacing=0 width="<?=$size?>">
<tr><td align=center width=<?=$size?> height=<?=$size?>><a href="javascript:primage_view('<?=$productcode?>')"><img src="<?=$Dir?>images/common/trans.gif" border=0 alt="클릭하시면 큰 다중이미지를 보실수 있습니다." name=primg width=<?=$size?> height=<?=$size?>></a></td></tr>
<tr><td height=10></td></tr>
<tr>
	<td align=center>
	<A HREF="javascript:primage_view('<?=$productcode?>')"><img src="<?=$Dir?>images/common/btn_zoom.gif" border=0></A>
	</td>
</tr>
<tr><td height=5></td></tr>
</table>
</body>
</html>
