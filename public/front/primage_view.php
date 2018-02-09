<?php 
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

$scroll=$_REQUEST["scroll"];
$image=$_REQUEST["image"];
$productcode=substr($image,0,18);

$multi_img="N";
$sql2 ="SELECT COUNT(*) as cnt FROM tblmultiimages WHERE productcode='{$productcode}' ";
$result2=pmysql_query($sql2,get_db_conn());
$row2=pmysql_fetch_object($result2);
$cnt2=$row2->cnt;
pmysql_free_result($result2);
if($cnt2>0) {
	$multi_img="Y";
}

if($multi_img=="Y") {
	echo "<html></head><body onload=\"location.href='".$Dir.FrontDir."primage_multiview.php?productcode={$productcode}&scroll={$scroll}';\"></body></html>";exit;
}

$imagepath=$Dir.DataDir."shopimages/product/".$image;

if(!file_exists($imagepath)) {
	echo "<script>window.close();</script>"; exit;
}

$size=GetImageSize($imagepath);
$xsize=($size[0]>=800)?"750":$size[0];
$ysize=($size[1]>=600)?"550":$size[1];
$size[0]=$xsize;
$size[1]=$ysize;

$body="";
$sql = "SELECT * FROM tbldesignnewpage WHERE type='primgview' ";
$result=pmysql_query($sql,get_db_conn());
if($row=pmysql_fetch_object($result)) {
	$body=$row->body;
	$body=str_replace("[DIR]",$Dir,$body);
	$auto=$row->leftmenu;
	$tmpsize=$row->filename;
	if(ord($body)) {
		if($auto=="N") $size=explode("",$row->filename);
	}
}
pmysql_free_result($result);

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
var g_fIsSP2 = false;
g_fIsSP2 = (window.navigator.userAgent.indexOf("SV1") != -1);

window.moveTo(10,10);
if (g_fIsSP2) 
	window.resizeTo(<?=($size[0]+50)?>,<?=($size[1]+130)?>);
else
	window.resizeTo(<?=($size[0]+50)?>,<?=($size[1]+120)?>);

function WindowClose(){
	window.close();
}
//-->
</SCRIPT>
</head>

<body topmargin=0 leftmargin=0 rightmargin=0 marginheight=0 marginwidth=0 oncontextmenu="return false;">
<?php 
if(ord($body)) {	//개별디자인
	//$hidden ="<div width={$xsize} height={$ysize} style=\"position:absolute;left=10\">\n";
	//$hidden.="<a href=\"javascript:WindowClose()\"><img src=\"{$Dir}images/common/trans.gif\" width={$xsize} height={$ysize} border=0 alt=\"클릭하시면 창이 닫힙니다\"></a>\n";
	//$hidden.="</div>";

	$pattern=array("[CLOSE]","[IMAGE]","[WIDTH]");
	$replace=array("\"javascript:WindowClose()\"","".$Dir.DataDir."shopimages/product/".$image,$xsize);
	$body=str_replace($pattern,$replace,$body);
	echo $body;
} else {
	echo "<center>";
	echo "<table border=0 cellpadding=0 cellspacing=0 width={$size[0]} height={$size[1]} background=\"".$Dir.DataDir."shopimages/product/{$image}\"><tr><td><a href=\"javascript:WindowClose()\"><img src={$Dir}images/common/trans.gif border=0 width={$size[0]} height={$size[1]} alt=\"클릭하시면 창이 닫힙니다\"></a></td></tr></table>";
	echo "<br><br><a href=\"javascript:WindowClose()\"><img src=\"{$Dir}images/common/imageview_close.gif\" border=0></a>";
}
?>
</body>
</html>
