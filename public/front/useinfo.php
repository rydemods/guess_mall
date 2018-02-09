<?php 
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

$sql = "SELECT useinfo FROM tbldesign ";
$result = pmysql_query($sql,get_db_conn());

if($row=pmysql_fetch_object($result)) {
	$useinfo=$row->useinfo;
	if (substr($useinfo,-2,1)=="") {
		$leftmenu = substr($useinfo,-1);
		$useinfo = substr($useinfo,0,-2);
	} else {
		$leftmenu = "Y";	//N:상단 타이틀 이미지 기본 사용, Y:상단 타이틀 이미지 내용속에 포함
	}
	if (strpos(strtolower($useinfo),"table")!=false) $useinfo = "<pre>{$useinfo}</pre>";
	else $useinfo = nl2br($useinfo);
}
pmysql_free_result($result);

?>
<HTML>
<HEAD>
<TITLE><?=$_data->shoptitle?> - 이용안내</TITLE>
<META http-equiv="CONTENT-TYPE" content="text/html; charset=EUC-KR">
<META name="description" content="<?=(ord($_data->shopdescription)?$_data->shopdescription:$_data->shoptitle)?>">
<META name="keywords" content="<?=$_data->shopkeyword?>">
<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
<?php include($Dir."lib/style.php")?>
</HEAD>

<?php  include ($Dir.MainDir.$_data->menu_type.".php") ?>

<body<?=($_data->layoutdata["MOUSEKEY"][0]=="Y"?" oncontextmenu=\"return false;\"":"")?><?=($_data->layoutdata["MOUSEKEY"][1]=="Y"?" ondragstart=\"return false;\" onselectstart=\"return false;\"":"")?> leftmargin="0" marginwidth="0" topmargin="0" marginheight="0"><?=($_data->layoutdata["MOUSEKEY"][2]=="Y"?"<meta http-equiv=\"ImageToolbar\" content=\"No\">":"")?>

<table border="0" cellpadding="0" cellspacing="0" width="100%">
<?php 
if ($leftmenu!="N") {
	echo "<tr>\n";
	if ($_data->title_type=="Y" && file_exists($Dir.DataDir."design/useinfo_title.gif")) {
		echo "<td><img src=\"".$Dir.DataDir."design/useinfo_title.gif\" border=\"0\" alt=\"이용안내\"></td>\n";
	} else {
		echo "<td>\n";
		echo "<TABLE WIDTH=100% BORDER=0 CELLPADDING=0 CELLSPACING=0>\n";
		echo "<TR>\n";
		echo "	<TD><IMG SRC={$Dir}images/{$_data->icon_type}/useinfo_title_head.gif ALT=></TD>\n";
		echo "	<TD width=100% valign=top background={$Dir}images/{$_data->icon_type}/useinfo_title_bg.gif></TD>\n";
		echo "	<TD width=40><IMG SRC={$Dir}images/{$_data->icon_type}/useinfo_title_tail.gif ALT=></TD>\n";
		echo "</TR>\n";
		echo "</TABLE>\n";
		echo "</td>\n";
	}
	echo "</tr>\n";
}
?>
<tr>
	<td align="center">
<?php 
	if(ord($useinfo)) {
		echo $useinfo;
	} else {
		include($Dir.TempletDir."useinfo/useinfo{$_data->design_useinfo}.php");
	}
?>
	</td>
</tr>
</table>
<?php  include ($Dir."lib/bottom.php") ?>
</BODY>
</HTML>
