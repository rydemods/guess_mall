<?php 
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

if($_data->ETCTYPE["BRANDMAP"]!="Y") {
	alert_go('현재 페이지는 미사용 중 입니다.',-1);
}

$brandpagemark = "Y"; // 브랜드 전용 페이지
$searchValue=$_REQUEST["searchValue"];
?>
<HTML>
<HEAD>
<TITLE><?=$_data->shoptitle?> - 브랜드맵</TITLE>
<META http-equiv="CONTENT-TYPE" content="text/html; charset=EUC-KR">
<META name="description" content="<?=(ord($_data->shopdescription)?$_data->shopdescription:$_data->shoptitle)?>">
<META name="keywords" content="<?=$_data->shopkeyword?>">
<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
<?php include($Dir."lib/style.php")?>
</HEAD>

<?php  include ($Dir.MainDir.$_data->menu_type.".php") ?>

<body<?=($_data->layoutdata["MOUSEKEY"][0]=="Y"?" oncontextmenu=\"return false;\"":"")?><?=($_data->layoutdata["MOUSEKEY"][1]=="Y"?" ondragstart=\"return false;\" onselectstart=\"return false;\"":"")?> leftmargin="0" marginwidth="0" topmargin="0" marginheight="0"><?=($_data->layoutdata["MOUSEKEY"][2]=="Y"?"<meta http-equiv=\"ImageToolbar\" content=\"No\">":"")?>

<?
$lnb_flag = 2;
include ($Dir.MainDir."lnb.php");
?>

<table border="0" cellpadding="0" cellspacing="0" width="100%">
<?php 
$leftmenu="Y";
if($_data->design_bmap=="U") {
	$sql="SELECT body,leftmenu FROM tbldesignnewpage WHERE type='bmap'";
	$result=pmysql_query($sql,get_db_conn());
	if($row=pmysql_fetch_object($result)) {
		$body=$row->body;
		$body=str_replace("[DIR]",$Dir,$body);
		$leftmenu=$row->leftmenu;
		$newdesign="Y";
	}
	pmysql_free_result($result);
}

if ($leftmenu!="N") {
	echo "<tr>\n";
	if ($_data->title_type=="Y" && file_exists($Dir.DataDir."design/brandmap_title.gif")) {
		echo "<td><img src=\"".$Dir.DataDir."design/brandmap_title.gif\" border=\"0\" alt=\"브랜드맵\"></td>\n";
	} else {
		echo "<td>\n";
		echo "<TABLE WIDTH=100% BORDER=0 CELLPADDING=0 CELLSPACING=0>\n";
		echo "<TR>\n";
		echo "	<TD><IMG SRC={$Dir}images/{$_data->icon_type}/brandmap_title_head.gif ALT=></TD>\n";
		echo "	<TD width=100% valign=top background={$Dir}images/{$_data->icon_type}/brandmap_title_bg.gif></TD>\n";
		echo "	<TD width=40><IMG SRC={$Dir}images/{$_data->icon_type}/brandmap_title_tail.gif ALT=></TD>\n";
		echo "</TR>\n";
		echo "</TABLE>\n";
		echo "</td>\n";
	}
	echo "</tr>\n";
}

echo "<tr>\n";
echo "	<td align=\"center\">\n";
include ($Dir.TempletDir."brandmap/brandmap{$_data->design_bmap}.php");
echo "	</td>\n";
echo "</tr>\n";
?>
	</td>
</tr>
</table>
<?php  include ($Dir."lib/bottom.php") ?>
</BODY>
</HTML>
